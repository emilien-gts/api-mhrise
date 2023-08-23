<?php

namespace App\Entity;

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
    normalizationContext: ['groups' => 'api:quest:read'],
    denormalizationContext: ['groups' => 'api:quest:write'],
    validationContext: ['groups' => 'api:quest']
)]
#[UniqueEntity(fields: ['name', 'client'])]
class Quest
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(groups: ['api:quest:read', 'api:quest:write'])]
    public string $name;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[ReferentialTypeConstraint(match: ReferentialTypeEnum::QUEST_CLIENT, groups: ['Default', 'api:quest'])]
    #[Groups(groups: ['api:quest:read', 'api:quest:write'])]
    public ?Referential $client = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(groups: ['api:quest:read', 'api:quest:write'])]
    public ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[ReferentialTypeConstraint(match: ReferentialTypeEnum::MAP, groups: ['Default', 'api:quest'])]
    #[Groups(groups: ['api:quest:read', 'api:quest:write'])]
    public ?Referential $map = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    #[Groups(groups: ['api:quest:read', 'api:quest:write'])]
    public ?bool $isKey = null;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[ReferentialTypeConstraint(match: ReferentialTypeEnum::QUEST_TYPE, groups: ['Default', 'api:quest'])]
    #[Groups(groups: ['api:quest:read', 'api:quest:write'])]
    public ?Referential $type = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Groups(groups: ['api:quest:read', 'api:quest:write'])]
    public ?int $difficulty = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(groups: ['api:quest:read', 'api:quest:write'])]
    public ?string $objective = null;

    #[ORM\ManyToMany(targetEntity: Monster::class)]
    #[ORM\JoinTable(name: 'quest_target')]
    #[Groups(groups: ['api:quest:read', 'api:quest:write'])]
    public Collection $targets;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->targets = new ArrayCollection();
    }

    public function addTarget(Monster $monster): void
    {
        if (!$this->targets->contains($monster)) {
            $this->targets->add($monster);
        }
    }

    public function removeTarget(Monster $monster): void
    {
        if ($this->targets->contains($monster)) {
            $this->targets->removeElement($monster);
        }
    }
}
