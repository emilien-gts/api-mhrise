<?php

namespace App\Entity;

use App\Model\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Quest
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING)]
    public ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    public ?Referential $client = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    public ?Referential $map = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    public ?bool $isKey = null;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    public ?Referential $type = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    public ?int $difficulty = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $objective = null;

    #[ORM\ManyToMany(targetEntity: Monster::class)]
    #[ORM\JoinTable(name: 'quest_target')]
    public Collection $targets;

    public function __construct()
    {
        $this->targets = new ArrayCollection();
    }

    public function addTarget(Monster $monster): void
    {
        if (!$this->targets->contains($monster)) {
            $this->targets->add($monster);
        }
    }
}
