<?php

namespace App\Synchronizer\Model;

use App\Entity\Item;

trait FindItemTrait
{
    /** @var array<string, Item> */
    public array $_items = [];

    private function findItem(string $itemName): ?Item
    {
        $item = $this->_items[$itemName] ?? null;
        $item = $item ?? $this->em->getRepository(Item::class)->findOneBy(['name' => $itemName]);
        if (!isset($this->_items[$itemName]) && $item) {
            $this->_items[$itemName] = $item;
        }

        return $item;
    }
}
