<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\ReferentialTypeEnum;
use App\Model\IdTrait;
use App\Repository\ReferentialRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReferentialRepository::class)]
#[UniqueEntity(fields: ['type', 'name'])]
#[ApiResource(
    operations: [new GetCollection(), new Get()],
    normalizationContext: ['groups' => 'api:referential:read'],
)]
class Referential
{
    use IdTrait;

    #[ORM\Column(type: Types::STRING, enumType: ReferentialTypeEnum::class)]
    #[Groups(groups: ['api:referential:read'])]
    public ReferentialTypeEnum $type;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(groups: ['api:referential:read'])]
    public string $libelle;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(groups: ['api:referential:read'])]
    public ?int $value = null;

    public function __construct(ReferentialTypeEnum $type, string $libelle)
    {
        $this->type = $type;
        $this->libelle = $libelle;
    }
}
