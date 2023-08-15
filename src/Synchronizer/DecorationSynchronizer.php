<?php

namespace App\Synchronizer;

use App\Entity\Decoration\Decoration;
use App\Entity\Decoration\DecorationMaterial;
use App\Entity\Item;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;

class DecorationSynchronizer extends AbstractSynchronizer
{
    public const JSON_NAME = 'decorations.json';

    /** @var array<string, Item> */
    public array $_items = [];

    /**
     * @throws IOException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function sync(): void
    {
        $this->helper->cleanEntity(Decoration::class);
        $this->openJson(self::JSON_NAME, 'data');

        $depth = $this->reader->depth();
        $this->reader->read();

        do {
            /** @var array $data */
            $data = $this->reader->value();
            $this->syncDecoration($data);
        } while ($this->reader->next() && $this->reader->depth() > $depth);

        $this->saveAndClose();
    }

    private function syncDecoration(array $data): void
    {
        $d = new Decoration();
        $d->name = $data['name'];
        $d->description = $data['description'] ?? null;

        if (isset($data['materials'])) {
            $this->syncDecorationMaterials($data['materials'], $d);
        }

        $this->em->persist($d);
    }

    private function syncDecorationMaterials(array $materials, Decoration $d): void
    {
        foreach ($materials as $material) {
            $i = $this->findDecorationMaterial($material['item']['name']);
            if (null === $i) {
                continue;
            }

            $di = new DecorationMaterial();
            $di->amount = isset($material['amount']) ? (int) \str_replace('x', '', $material['amount']) : null;

            $d->addMaterial($di);
            $i->addDecoration($di);
        }
    }

    private function findDecorationMaterial(string $materialName): ?Item
    {
        $item = $this->_items[$materialName] ?? null;
        $item = $item ?? $this->em->getRepository(Item::class)->findOneBy(['name' => $materialName]);
        if (!isset($this->_items[$materialName]) && $item) {
            $this->_items[$materialName] = $item;
        }

        return $item;
    }
}
