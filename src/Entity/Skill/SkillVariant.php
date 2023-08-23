<?php

namespace App\Entity\Skill;

use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[UniqueEntity(fields: ['name'])]
class SkillVariant
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Groups(groups: ['api:skill:read', 'api:skill:write'])]
    public string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(groups: ['api:skill:read', 'api:skill:write'])]
    public ?string $effect = null;

    #[ORM\ManyToOne(targetEntity: Skill::class, inversedBy: 'variants')]
    public ?Skill $skill = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
