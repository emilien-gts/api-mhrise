<?php

namespace App\Entity\Weapon;

use App\Entity\Item;
use App\Enum\WeaponMaterialTypeEnum;
use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class WeaponMaterial
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Weapon::class, cascade: ['PERSIST'], inversedBy: 'materials')]
    public ?Weapon $weapon = null;

    #[ORM\ManyToOne(targetEntity: Item::class)]
    public ?Item $item = null;

    #[ORM\Column(type: Types::STRING, nullable: false, enumType: WeaponMaterialTypeEnum::class)]
    public ?WeaponMaterialTypeEnum $type = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    public ?int $amount = null;
}
