<?php

namespace App\Entity;

use App\Enum\ReferentialTypeEnum;
use App\Model\IdTrait;
use App\Repository\ReferentialRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ReferentialRepository::class)]
#[UniqueEntity(fields: ['type', 'name'])]
class Referential
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, enumType: ReferentialTypeEnum::class)]
    public ReferentialTypeEnum $type;

    #[ORM\Column(type: Types::STRING)]
    public string $libelle;

    public function __construct(ReferentialTypeEnum $type, string $libelle)
    {
        $this->type = $type;
        $this->libelle = $libelle;
    }
}
