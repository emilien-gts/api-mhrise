<?php

namespace App\Entity\Armor;

use App\Entity\Skill\Skill;
use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['skill', 'nbSlots', 'description'])]
class ArmorSkill
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Armor::class, cascade: ['PERSIST'], inversedBy: 'skills')]
    public ?Armor $armor = null;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    public ?Skill $skill = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;
}
