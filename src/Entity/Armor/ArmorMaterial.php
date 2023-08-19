<?php

namespace App\Entity\Armor;

use App\Entity\Item;
use App\Enum\MaterialTypeEnum;
use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ArmorMaterial
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Armor::class, cascade: ['PERSIST'], inversedBy: 'materials')]
    public ?Armor $armor = null;

    #[ORM\ManyToOne(targetEntity: Item::class)]
    public ?Item $item = null;

    #[ORM\Column(type: Types::STRING, nullable: false, enumType: MaterialTypeEnum::class)]
    public ?MaterialTypeEnum $type = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    public ?int $amount = null;
}
