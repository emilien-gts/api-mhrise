<?php

namespace App\Synchronizer;

use Doctrine\ORM\EntityManagerInterface;

class SynchronizerHelper
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function cleanEntity(string $classname): void
    {
        $this->em->createQuery(\sprintf('DELETE FROM %s e', $classname))->execute();
    }
}
