<?php

namespace App\Entity\Decoration;

use App\Model\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['name'])]
class Decoration
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    public ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'decoration', targetEntity: DecorationMaterial::class, cascade: ['ALL'])]
    public Collection $materials;

    public function __construct()
    {
        $this->materials = new ArrayCollection();
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
}