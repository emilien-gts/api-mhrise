<?php

namespace App\Entity\Skill;

use App\Model\IdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SkillVariant
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    public string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $effect = null;

    #[ORM\ManyToOne(targetEntity: Skill::class, inversedBy: 'variants')]
    public ?Skill $skill = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
