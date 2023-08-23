<?php

namespace App\Entity\Weapon;

use App\Entity\Item;
use App\Enum\MaterialTypeEnum;
use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class WeaponMaterial
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Weapon::class, cascade: ['PERSIST'], inversedBy: 'materials')]
    public ?Weapon $weapon = null;

    #[ORM\ManyToOne(targetEntity: Item::class)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?Item $item = null;

    #[ORM\Column(type: Types::STRING, nullable: false, enumType: MaterialTypeEnum::class)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?MaterialTypeEnum $type = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?int $amount = null;
}
