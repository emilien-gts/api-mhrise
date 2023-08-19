<?php

namespace App\Synchronizer;

use App\Entity\Item;
use App\Entity\Monster;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;

class ItemSynchronizer extends AbstractSynchronizer
{
    public const JSON_NAME = 'items.json';
    public const BATCH_SIZE = 2000;
    public const MONSTER_TYPES = ['wyvern', 'bird wyvern', 'herbivore', 'fanged beast'];

    /** @var array<string, Monster> */
    public array $_monsters = [];

    /**
     * @throws InvalidArgumentException
     * @throws IOException
     * @throws Exception
     */
    public function sync(): void
    {
        $this->helper->cleanEntity(Item::class);
        $this->openJson(self::JSON_NAME, 'data');

        $this->initMonsters();
        $this->syncItemsType();
        $this->syncItems();

        $this->saveAndClose();
    }

    private function initMonsters(): void
    {
        $monsters = $this->em->getRepository(Monster::class)->findAll();
        foreach ($monsters as $monster) {
            $this->_monsters[$monster->name] = $monster;
        }
    }

    private function syncItemsType(): void
    {
        $types = [0 => 'Consumable', 1 => 'Utility', 2 => 'Item', 3 => 'Scrap', 4 => 'Ammo', 5 => 'Coating', 7 => 'Gastronome', 8 => 'Egg', 11 => 'Collection'];
        foreach ($types as $value => $libelle) {
            $itemType = $this->referentialFactory->createItemType($libelle, $value);
            $this->em->persist($itemType);
        }

        $this->em->flush();
    }

    /**
     * @throws IOException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    private function syncItems(): void
    {
        $depth = $this->reader->depth();
        $this->reader->read();

        $i = 0;
        do {
            /** @var array $data */
            $data = $this->reader->value();
            $this->syncItem($data);

            if (0 !== $i && ($i % self::BATCH_SIZE) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
            ++$i;
        } while ($this->reader->next() && $this->reader->depth() > $depth);
    }

    private function syncItem(array $data): void
    {
        $i = new Item($data['name']);
        $i->description = $data['description'] ?? null;
        $i->type = isset($data['type']) ? $this->referentialRepository->findOneItemTypeByValue((int) $data['type']) : null;
        $i->isSupply = SynchronizerUtils::array_value_as_bool($data, 'supply');
        $i->buyPrice = SynchronizerUtils::array_value_as_int($data, 'buyPrice');
        $i->sellPrice = SynchronizerUtils::array_value_as_int($data, 'sellPrice');

        $this->attachMonsters($i);

        $this->em->persist($i);
    }

    private function attachMonsters(Item $i): void
    {
        $this->attachMonstersByName($i);
        $this->attachMonstersByType($i);
    }

    private function attachMonstersByName(Item $i): void
    {
        $search = \trim(\strtolower(\sprintf('%s %s', $i->name ?? '', $i->description ?? '')));
        $filteredMonsters = \array_filter($this->_monsters, function ($name) use ($search) {
            return \str_contains($search, \strtolower($name));
        }, \ARRAY_FILTER_USE_KEY);

        foreach ($filteredMonsters as $monster) {
            $i->addLinkMonster($monster);
        }
    }

    private function attachMonstersByType(Item $i): void
    {
        $search = \trim(\strtolower($i->name ?? ''));

        foreach (self::MONSTER_TYPES as $type) {
            $filteredMonsters = \array_filter($this->_monsters, function ($monster) use ($type, $search) {
                $monsterType = \strtolower($monster->type?->libelle ?? '');

                return $type === $monsterType && \str_contains($search, $type);
            }, \ARRAY_FILTER_USE_BOTH);

            foreach ($filteredMonsters as $monster) {
                $i->addLinkMonster($monster);
            }
        }
    }
}
