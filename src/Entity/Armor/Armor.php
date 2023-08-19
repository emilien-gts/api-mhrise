<?php

namespace App\Entity\Armor;

use App\Enum\ArmorPartEnum;
use App\Model\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['name'])]
class Armor
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING)]
    public ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    public ?int $armorSetId = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    public ?int $rarity = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    public ?int $value = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    public ?int $buyValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    public ?int $defenseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    public ?int $fireDefenseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    public ?int $waterDefenseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    public ?int $iceDefenseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    public ?int $thunderDefenseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    public ?int $dragonDefenseValue = null;

    #[ORM\Column(type: Types::STRING, nullable: false, enumType: ArmorPartEnum::class)]
    public ?ArmorPartEnum $part = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    public ?int $rank = null;

    #[ORM\OneToMany(mappedBy: 'armor', targetEntity: ArmorSkill::class, cascade: ['ALL'])]
    public Collection $skills;

    #[ORM\OneToMany(mappedBy: 'armor', targetEntity: ArmorMaterial::class, cascade: ['ALL'])]
    public Collection $materials;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->materials = new ArrayCollection();
    }

    public function addSkill(ArmorSkill $skill): void
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
            $skill->armor = $this;
        }
    }

    public function removeSkill(ArmorSkill $skill): void
    {
        if ($this->skills->contains($skill)) {
            $this->skills->removeElement($skill);
            $skill->armor = null;
        }
    }

    public function addMaterial(ArmorMaterial $armorMaterial): void
    {
        if (!$this->materials->contains($armorMaterial)) {
            $this->materials->add($armorMaterial);
            $armorMaterial->armor = $this;
        }
    }

    public function removeMaterial(ArmorMaterial $armorMaterial): void
    {
        if ($this->materials->contains($armorMaterial)) {
            $this->materials->removeElement($armorMaterial);
            $armorMaterial->armor = null;
        }
    }
}
