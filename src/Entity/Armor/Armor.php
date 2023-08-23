<?php

namespace App\Entity\Armor;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Enum\ArmorPartEnum;
use App\Model\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
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
    normalizationContext: ['groups' => 'api:armor:read'],
    denormalizationContext: ['groups' => 'api:armor:write'],
    validationContext: ['groups' => 'api:armor']
)]
class Armor
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?string $description = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?int $armorSetId = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?int $rarity = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?int $value = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?int $buyValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?int $defenseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?int $fireDefenseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?int $waterDefenseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?int $iceDefenseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?int $thunderDefenseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?int $dragonDefenseValue = null;

    #[ORM\Column(type: Types::STRING, nullable: false, enumType: ArmorPartEnum::class)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?ArmorPartEnum $part = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false)]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public ?int $rank = null;

    #[ORM\OneToMany(mappedBy: 'armor', targetEntity: ArmorSkill::class, cascade: ['ALL'])]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public Collection $skills;

    #[ORM\OneToMany(mappedBy: 'armor', targetEntity: ArmorMaterial::class, cascade: ['ALL'])]
    #[Groups(groups: ['api:armor:read', 'api:armor:write'])]
    public Collection $materials;

    public function __construct(string $name)
    {
        $this->name = $name;

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
