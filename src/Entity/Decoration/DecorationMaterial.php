<?php

namespace App\Entity\Decoration;

use App\Entity\Item;
use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['decoration', 'item'])]
class DecorationMaterial
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Decoration::class, cascade: ['PERSIST'], inversedBy: 'materials')]
    public ?Decoration $decoration = null;

    #[ORM\ManyToOne(targetEntity: Item::class, cascade: ['PERSIST'], inversedBy: 'decorations')]
    public ?Item $material = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    public ?int $amount = null;
}
