<?php

namespace App\Entity\Weapon;

use App\Entity\Referential;
use App\Model\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['name'])]
class Weapon
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    public string $name;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    public ?Referential $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    public ?int $rarity = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $baseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $buyValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $attackValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $criticalRate = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $defenseBonus = null;

    #[ORM\OneToMany(mappedBy: 'weapon', targetEntity: WeaponRampageSkill::class, cascade: ['ALL'])]
    public Collection $rampageSkills;

    #[ORM\OneToMany(mappedBy: 'weapon', targetEntity: WeaponMaterial::class, cascade: ['ALL'])]
    public Collection $materials;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->rampageSkills = new ArrayCollection();
        $this->materials = new ArrayCollection();
    }

    public function addRampageSkill(WeaponRampageSkill $rampageSkill): void
    {
        if (!$this->rampageSkills->contains($rampageSkill)) {
            $this->rampageSkills->add($rampageSkill);
            $rampageSkill->weapon = $this;
        }
    }

    public function removeRampageSkill(WeaponRampageSkill $rampageSkill): void
    {
        if ($this->rampageSkills->contains($rampageSkill)) {
            $this->rampageSkills->removeElement($rampageSkill);
            $rampageSkill->weapon = null;
        }
    }

    public function addMaterial(WeaponMaterial $weaponMaterial): void
    {
        if (!$this->materials->contains($weaponMaterial)) {
            $this->materials->add($weaponMaterial);
            $weaponMaterial->weapon = $this;
        }
    }

    public function removeMaterial(WeaponMaterial $weaponMaterial): void
    {
        if ($this->materials->contains($weaponMaterial)) {
            $this->materials->removeElement($weaponMaterial);
            $weaponMaterial->weapon = null;
        }
    }
}
