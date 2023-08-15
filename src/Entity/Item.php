<?php

namespace App\Entity;

use App\Model\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Item
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING)]
    public ?string $name = null;

    #[ORM\ManyToOne(targetEntity: Referential::class, cascade: ['PERSIST'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    public ?Referential $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    public ?bool $isSupply = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $buyPrice = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $sellPrice = null;

    /**
     * @var ArrayCollection<int, Monster>
     */
    #[ORM\ManyToMany(targetEntity: Monster::class)]
    #[ORM\JoinTable(name: 'item_link_monster')]
    public Collection $linkMonsters;

    public function __construct()
    {
        $this->linkMonsters = new ArrayCollection();
    }

    public function addLinkMonster(Monster $monster): void
    {
        if (!$this->linkMonsters->contains($monster)) {
            $this->linkMonsters->add($monster);
        }
    }
}
