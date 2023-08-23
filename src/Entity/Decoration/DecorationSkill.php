<?php

namespace App\Entity\Decoration;

use App\Entity\Skill\Skill;
use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class DecorationSkill
{
    use IdTrait;

    #[ORM\ManyToOne(targetEntity: Decoration::class, cascade: ['PERSIST'], inversedBy: 'skills')]
    #[Groups(groups: ['api:skill:read', 'api:skill:write'])]
    public ?Decoration $decoration = null;

    #[ORM\ManyToOne(targetEntity: Skill::class, cascade: ['PERSIST'], inversedBy: 'decorations')]
    #[Groups(groups: ['api:decoration:read', 'api:decoration:write'])]
    public ?Skill $skill = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(groups: ['api:decoration:read', 'api:decoration:write', 'api:skill:read', 'api:skill:write'])]
    public ?string $description = null;
}
