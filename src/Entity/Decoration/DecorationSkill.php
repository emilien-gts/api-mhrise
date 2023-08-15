<?php

namespace App\Entity\Decoration;

use App\Entity\Skill;
use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['decoration', 'skill'])]
class DecorationSkill
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Decoration::class, cascade: ['PERSIST'], inversedBy: 'skills')]
    public ?Decoration $decoration = null;

    #[ORM\ManyToOne(targetEntity: Skill::class, cascade: ['PERSIST'], inversedBy: 'decorations')]
    public ?Skill $skill = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;
}
