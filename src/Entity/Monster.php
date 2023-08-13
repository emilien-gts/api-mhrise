<?php

namespace App\Entity;

use App\Model\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Monster
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING)]
    public ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    public ?Referential $type = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    public ?bool $isLarge = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    public ?int $dangerLevel = null;

    /**
     * @var ArrayCollection<int, Referential>
     */
    #[ORM\ManyToMany(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinTable(name: 'monster_elements')]
    public Collection $elements;

    /**
     * @var ArrayCollection<int, Referential>
     */
    #[ORM\ManyToMany(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinTable(name: 'monster_ailments')]
    public Collection $ailments;

    /**
     * @var ArrayCollection<int, Referential>
     */
    #[ORM\ManyToMany(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinTable(name: 'monster_weakness')]
    public Collection $weakness;

    #[ORM\ManyToOne(targetEntity: Monster::class, inversedBy: 'subSpecies')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    public ?Monster $mainSpecie = null;

    /**
     * @var ArrayCollection<int, Monster>
     */
    #[ORM\OneToMany(mappedBy: 'mainSpecie', targetEntity: Monster::class, cascade: ['PERSIST'])]
    public Collection $subSpecies;

    public function __construct()
    {
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
}
