<?php

namespace App\Entity\Weapon;

use App\Entity\Skill\SkillVariant;
use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[UniqueEntity(fields: ['skill', 'nbSlots', 'description'])]
class WeaponRampageSkill
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Weapon::class, cascade: ['PERSIST'], inversedBy: 'rampageSkills')]
    public ?Weapon $weapon = null;

    #[ORM\ManyToOne(targetEntity: SkillVariant::class)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?SkillVariant $skill = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?int $nbSlots = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?string $description = null;
}
