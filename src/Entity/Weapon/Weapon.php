<?php

namespace App\Entity\Weapon;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Referential;
use App\Enum\ReferentialTypeEnum;
use App\Model\IdTrait;
use App\Validator\Constraint\ReferentialTypeConstraint;
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
    normalizationContext: ['groups' => 'api:weapon:read'],
    denormalizationContext: ['groups' => 'api:weapon:write'],
    validationContext: ['groups' => 'api:weapon']
)]
#[UniqueEntity(fields: ['name'])]
class Weapon
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public string $name;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[ReferentialTypeConstraint(match: ReferentialTypeEnum::WEAPON_TYPE, groups: ['Default', 'api:weapon'])]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?Referential $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?string $description = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?int $rarity = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?int $baseValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?int $buyValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?int $attackValue = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?int $criticalRate = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public ?int $defenseBonus = null;

    #[ORM\OneToMany(mappedBy: 'weapon', targetEntity: WeaponRampageSkill::class, cascade: ['ALL'])]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
    public Collection $rampageSkills;

    #[ORM\OneToMany(mappedBy: 'weapon', targetEntity: WeaponMaterial::class, cascade: ['ALL'])]
    #[Groups(groups: ['api:weapon:read', 'api:weapon:write'])]
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
