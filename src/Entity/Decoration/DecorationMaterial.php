<?php

namespace App\Entity\Decoration;

use App\Entity\Item;
use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[UniqueEntity(fields: ['decoration', 'item'])]
class DecorationMaterial
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Decoration::class, cascade: ['PERSIST'], inversedBy: 'materials')]
    #[Groups(groups: ['api:item:read', 'api:item:write'])]
    public ?Decoration $decoration = null;

    #[ORM\ManyToOne(targetEntity: Item::class, cascade: ['PERSIST'], inversedBy: 'decorations')]
    #[Groups(groups: ['api:decoration:read', 'api:decoration:write'])]
    public ?Item $material = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    #[Groups(groups: ['api:decoration:read', 'api:decoration:write', 'api:item:read', 'api:item:write'])]
    public ?int $amount = null;
}
