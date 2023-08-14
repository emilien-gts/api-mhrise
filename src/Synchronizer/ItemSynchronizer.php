<?php

namespace App\Synchronizer;

use App\Entity\Item;
use App\Entity\Monster;

class ItemSynchronizer extends AbstractSynchronizer
{
    public const JSON_NAME = 'items.json';
    public const BATCH_SIZE = 2000;

    public array $_monsters = [];

    public function sync(): void
    {
        $this->helper->cleanEntity(Item::class);
        $this->initMonsters();

        $this->syncItemsType();
        $this->syncItems();

        $this->em->flush();
        $this->em->clear();
        $this->reader->close();
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
        $types = [0 => 'Health', 1 => 'Utility', 2 => 'Trap', 3 => 'Scrap', 4 => 'Ammo', 5 => 'Coating', 7 => 'Gastronome', 8 => 'Egg', 11 => 'Collection'];
        foreach ($types as $value => $libelle) {
            $itemType = $this->referentialFactory->createItemType($libelle, $value);
            $this->em->persist($itemType);
        }

        $this->em->flush();
    }

    private function syncItems(): void
    {
        $this->openJson(self::JSON_NAME, 'data');
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
        $i = new Item();
        $i->name = $data['name'];
        $i->description = $data['description'] ?? null;
        $i->type = isset($data['type']) ? $this->referentialRepository->findOneItemTypeByValue((int) $data['type']) : null;
        $i->isSupply = isset($data['supply']) ? (bool) $data['supply'] : null;
        $i->buyPrice = isset($data['buyPrice']) ? (int) $data['buyPrice'] : null;
        $i->sellPrice = isset($data['sellPrice']) ? (int) $data['sellPrice'] : null;

        $this->attachMonsters($i);

        $this->em->persist($i);
    }

    private function attachMonsters(Item $i): void
    {
        $this->attachMonstersByName($i);
    }

    private function attachMonstersByName(Item $i): void
    {
        $filteredMonsters = \array_filter($this->_monsters, function ($name) use ($i) {
            if ($i->name && $i->description) {
                return \strpos($i->name, $name) || \strpos($i->description, $name);
            } elseif ($i->name && !$i->description) {
                return \strpos($i->name, $name);
            } elseif (!$i->name && $i->description) {
                return \strpos($i->description, $name);
            } else {
                return false;
            }
        }, \ARRAY_FILTER_USE_KEY);

        if (!empty($filteredMonsters)) {
            foreach ($filteredMonsters as $monster) {
                $i->linkMonsters->add($monster);
            }
        }
    }
}
