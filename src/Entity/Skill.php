<?php

namespace App\Entity;

use App\Entity\Decoration\DecorationSkill;
use App\Model\IdTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ['name'])]
class Skill
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, unique: true)]
    public ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $lv2 = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $lv3 = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $lv4 = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $lv5 = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $lv6 = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $lv7 = null;

    #[ORM\OneToMany(mappedBy: 'skill', targetEntity: DecorationSkill::class)]
    public Collection $decorations;

    public function __construct()
    {
        $this->decorations = new ArrayCollection();
    }

    public function addDecoration(DecorationSkill $decoration): void
    {
        if (!$this->decorations->contains($decoration)) {
            $this->decorations->add($decoration);
            $decoration->skill = $this;
        }
    }

    public function removeDecoration(DecorationSkill $decoration): void
    {
        if ($this->decorations->contains($decoration)) {
            $this->decorations->removeElement($decoration);
            $decoration->skill = null;
        }
    }
}
