<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Decoration\DecorationMaterial;
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
    normalizationContext: ['groups' => 'api:item:read'],
    denormalizationContext: ['groups' => 'api:item:write'],
    validationContext: ['groups' => 'api:item']
)]
#[UniqueEntity(fields: ['name'])]
class Item
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Groups(groups: ['api:item:read', 'api:item:write', 'api:decoration:read', 'api:decoration:write', 'api:weapon:read', 'api:weapon:write', 'api:armor:read', 'api:armor:write'])]
    public string $name;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[ReferentialTypeConstraint(match: ReferentialTypeEnum::ITEM_TYPE, groups: ['Default', 'api:monster'])]
    #[Groups(groups: ['api:item:read', 'api:item:write', 'api:decoration:read', 'api:decoration:write', 'api:weapon:read', 'api:weapon:write', 'api:armor:read', 'api:armor:write'])]
    public ?Referential $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(groups: ['api:item:read', 'api:item:write', 'api:decoration:read', 'api:decoration:write', 'api:weapon:read', 'api:weapon:write', 'api:armor:read', 'api:armor:write'])]
    public ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    #[Groups(groups: ['api:item:read', 'api:item:write', 'api:decoration:read', 'api:decoration:write', 'api:weapon:read', 'api:weapon:write', 'api:armor:read', 'api:armor:write'])]
    public ?bool $isSupply = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(groups: ['api:item:read', 'api:item:write', 'api:decoration:read', 'api:decoration:write', 'api:weapon:read', 'api:weapon:write', 'api:armor:read', 'api:armor:write'])]
    public ?int $buyPrice = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(groups: ['api:item:read', 'api:item:write', 'api:decoration:read', 'api:decoration:write', 'api:weapon:read', 'api:weapon:write', 'api:armor:read', 'api:armor:write'])]
    public ?int $sellPrice = null;

    /**
     * @var ArrayCollection<int, Monster>
     */
    #[ORM\ManyToMany(targetEntity: Monster::class)]
    #[ORM\JoinTable(name: 'item_link_monster')]
    #[Groups(groups: ['api:item:read', 'api:item:write'])]
    public Collection $linkMonsters;

    #[ORM\OneToMany(mappedBy: 'material', targetEntity: DecorationMaterial::class)]
    #[Groups(groups: ['api:item:read', 'api:item:write'])]
    public Collection $decorations;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->linkMonsters = new ArrayCollection();
    }

    public function addLinkMonster(Monster $monster): void
    {
        if (!$this->linkMonsters->contains($monster)) {
            $this->linkMonsters->add($monster);
        }
    }

    public function addDecoration(DecorationMaterial $decoration): void
    {
        if (!$this->decorations->contains($decoration)) {
            $this->decorations->add($decoration);
            $decoration->material = $this;
        }
    }

    public function removeDecoration(DecorationMaterial $decoration): void
    {
        if ($this->decorations->contains($decoration)) {
            $this->decorations->removeElement($decoration);
            $decoration->material = null;
        }
    }
}
