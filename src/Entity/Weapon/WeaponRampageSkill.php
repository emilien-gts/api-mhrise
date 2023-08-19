<?php

namespace App\Entity\Weapon;

use App\Entity\Skill;
use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['skill', 'nbSlots', 'description'])]
class WeaponRampageSkill
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Weapon::class, cascade: ['PERSIST'], inversedBy: 'rampageSkills')]
    public ?Weapon $weapon = null;

    #[ORM\ManyToOne(targetEntity: Skill::class)]
    public ?Skill $skill = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    public ?int $nbSlots = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;
}
