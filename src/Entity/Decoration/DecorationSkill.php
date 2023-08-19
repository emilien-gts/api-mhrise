<?php

namespace App\Entity\Decoration;

use App\Entity\Skill\Skill;
use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
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
