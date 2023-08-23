<?php

namespace App\Entity\Skill;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Decoration\DecorationSkill;
use App\Model\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(),
        new Get(),
        new Put(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: ['groups' => 'api:skill:read'],
    denormalizationContext: ['groups' => 'api:skill:write'],
    validationContext: ['groups' => 'api:skill']
)]
#[UniqueEntity(fields: ['name'])]
class Skill
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Groups(groups: ['api:skill:read', 'api:skill:write'])]
    public ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(groups: ['api:skill:read', 'api:skill:write'])]
    public ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'skill', targetEntity: SkillVariant::class, cascade: ['ALL'])]
    #[Groups(groups: ['api:skill:read', 'api:skill:write'])]
    public Collection $variants;

    #[ORM\OneToMany(mappedBy: 'skill', targetEntity: DecorationSkill::class)]
    #[Groups(groups: ['api:skill:read', 'api:skill:write'])]
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
