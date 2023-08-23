<?php

namespace App\Entity\Decoration;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
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
    normalizationContext: ['groups' => 'api:decoration:read'],
    denormalizationContext: ['groups' => 'api:decoration:write'],
    validationContext: ['groups' => 'api:decoration']
)]
#[UniqueEntity(fields: ['name'])]
class Decoration
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Groups(groups: ['api:decoration:read', 'api:decoration:write', 'api:item:read', 'api:item:write'])]
    public ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(groups: ['api:decoration:read', 'api:decoration:write', 'api:item:read', 'api:item:write'])]
    public ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'decoration', targetEntity: DecorationMaterial::class, cascade: ['ALL'])]
    #[Groups(groups: ['api:decoration:read', 'api:decoration:write'])]
    public Collection $materials;

    #[ORM\OneToMany(mappedBy: 'decoration', targetEntity: DecorationSkill::class, cascade: ['ALL'])]
    #[Groups(groups: ['api:decoration:read', 'api:decoration:write'])]
    public Collection $skills;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->materials = new ArrayCollection();
        $this->skills = new ArrayCollection();
    }

    public function addMaterial(DecorationMaterial $material): void
    {
        if (!$this->materials->contains($material)) {
            $this->materials->add($material);
            $material->decoration = $this;
        }
    }

    public function removeMaterial(DecorationMaterial $material): void
    {
        if ($this->materials->contains($material)) {
            $this->materials->removeElement($material);
            $material->decoration = null;
        }
    }

    public function addSkill(DecorationSkill $skill): void
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
            $skill->decoration = $this;
        }
    }

    public function removeSkill(DecorationSkill $skill): void
    {
        if ($this->skills->contains($skill)) {
            $this->skills->removeElement($skill);
            $skill->decoration = null;
        }
    }
}
