<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Enum\ReferentialTypeEnum;
use App\Model\IdTrait;
use App\Validator\Constraint\ReferentialTypeConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
    normalizationContext: ['groups' => 'api:monster:read'],
    denormalizationContext: ['groups' => 'api:monster:write'],
    validationContext: ['groups' => 'api:monster']
)]
#[UniqueEntity(fields: ['name'], groups: ['Default', 'api:monster'])]
class Monster
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Assert\NotBlank(groups: ['Default', 'api:monster'])]
    #[Groups(groups: ['api:monster:read', 'api:monster:write'])]
    public string $name;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[ReferentialTypeConstraint(match: ReferentialTypeEnum::MONSTER_TYPE, groups: ['Default', 'api:monster'])]
    #[Groups(groups: ['api:monster:read', 'api:monster:write'])]
    public ?Referential $type = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    #[Groups(groups: ['api:monster:read', 'api:monster:write'])]
    public ?bool $isLarge = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(groups: ['api:monster:read', 'api:monster:write'])]
    public ?string $description = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(groups: ['api:monster:read', 'api:monster:write'])]
    public ?int $dangerLevel = null;

    /**
     * @var ArrayCollection<int, Referential>
     */
    #[ORM\ManyToMany(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinTable(name: 'monster_elements')]
    #[Groups(groups: ['api:monster:read', 'api:monster:write'])]
    #[ReferentialTypeConstraint(match: ReferentialTypeEnum::ELEMENT, groups: ['Default', 'api:monster'])]
    public Collection $elements;

    /**
     * @var ArrayCollection<int, Referential>
     */
    #[ORM\ManyToMany(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinTable(name: 'monster_ailments')]
    #[Groups(groups: ['api:monster:read', 'api:monster:write'])]
    #[ReferentialTypeConstraint(match: ReferentialTypeEnum::AILMENT, groups: ['Default', 'api:monster'])]
    public Collection $ailments;

    /**
     * @var ArrayCollection<int, Referential>
     */
    #[ORM\ManyToMany(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinTable(name: 'monster_weakness')]
    #[Groups(groups: ['api:monster:read', 'api:monster:write'])]
    #[ReferentialTypeConstraint(match: ReferentialTypeEnum::ELEMENT, groups: ['Default', 'api:monster'])]
    public Collection $weakness;

    #[ORM\ManyToOne(targetEntity: Monster::class, inversedBy: 'subSpecies')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups(groups: ['api:monster:read', 'api:monster:write'])]
    #[ApiProperty(readableLink: false, writableLink: true)]
    public ?Monster $mainSpecie = null;

    /**
     * @var ArrayCollection<int, Monster>
     */
    #[ORM\OneToMany(mappedBy: 'mainSpecie', targetEntity: Monster::class, cascade: ['PERSIST'])]
    #[Groups(groups: ['api:monster:read', 'api:monster:write'])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    public Collection $subSpecies;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->elements = new ArrayCollection();
        $this->ailments = new ArrayCollection();
        $this->weakness = new ArrayCollection();
        $this->subSpecies = new ArrayCollection();
    }

    public function addSubSpecie(Monster $monster): void
    {
        if (!$this->subSpecies->contains($monster)) {
            $this->subSpecies->add($monster);
            $monster->mainSpecie = $this;
        }
    }

    public function removeSubSpecie(Monster $monster): void
    {
        if ($this->subSpecies->contains($monster)) {
            $this->subSpecies->removeElement($monster);
            $monster->mainSpecie = null;
        }
    }

    public function setElements(array $elements): self
    {
        $this->elements = new ArrayCollection($elements);

        return $this;
    }

    public function setAilments(array $ailments): self
    {
        $this->ailments = new ArrayCollection($ailments);

        return $this;
    }

    public function setWeakness(array $weakness): self
    {
        $this->weakness = new ArrayCollection($weakness);

        return $this;
    }

    public function setSubSpecies(array $subSpecies): self
    {
        $this->subSpecies = new ArrayCollection($subSpecies);

        return $this;
    }
}
