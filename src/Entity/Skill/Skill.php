<?php

namespace App\Entity\Skill;

use App\Entity\Decoration\DecorationSkill;
use App\Model\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['name'])]
class Skill
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    public ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'skill', targetEntity: SkillVariant::class, cascade: ['ALL'])]
    public Collection $variants;

    #[ORM\OneToMany(mappedBy: 'skill', targetEntity: DecorationSkill::class)]
    public Collection $decorations;

    public function __construct()
    {
        $this->variants = new ArrayCollection();
        $this->decorations = new ArrayCollection();
    }

    public function addDecoration(DecorationSkill $decoration): void
    {
        if (!$this->decorations->contains($decoration)) {
            $this->decorations->add($decoration);
            $decoration->skill = $this;
        }
    }

    public function removeDecoration(DecorationSkill $decoration): void
    {
        if ($this->decorations->contains($decoration)) {
            $this->decorations->removeElement($decoration);
            $decoration->skill = null;
        }
    }

    public function addVariant(SkillVariant $variant): void
    {
        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->skill = $this;
        }
    }

    public function removeVariant(SkillVariant $variant): void
    {
        if ($this->variants->contains($variant)) {
            $this->variants->removeElement($variant);
            $variant->skill = null;
        }
    }
}
