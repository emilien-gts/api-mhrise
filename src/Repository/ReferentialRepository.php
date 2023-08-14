<?php

namespace App\Repository;

use App\Entity\Referential;
use App\Enum\ReferentialTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Referential|null find($id, $lockMode = null, $lockVersion = null)
 * @method Referential|null findOneBy(array $criteria, array $orderBy = null)
 * @method Referential[]    findAll()
 * @method Referential[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReferentialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Referential::class);
    }

    public function findMonsterType(string $libelle): ?Referential
    {
        return $this->findOneByTypeAndLibelle(ReferentialTypeEnum::MONSTER_TYPE, $libelle);
    }

    public function findElement(string $libelle): ?Referential
    {
        return $this->findOneByTypeAndLibelle(ReferentialTypeEnum::ELEMENT, $libelle);
    }

    public function findWeakness(string $libelle): ?Referential
    {
        return $this->findElement($libelle);
    }

    public function findQuestClient(string $libelle): ?Referential
    {
        return $this->findOneByTypeAndLibelle(ReferentialTypeEnum::QUEST_CLIENT, $libelle);
    }

    public function findMap(string $libelle): ?Referential
    {
        return $this->findOneByTypeAndLibelle(ReferentialTypeEnum::MAP, $libelle);
    }

    public function findQuestType(string $libelle): ?Referential
    {
        return $this->findOneByTypeAndLibelle(ReferentialTypeEnum::QUEST_TYPE, $libelle);
    }

    public function findAilment(string $libelle): ?Referential
    {
        return $this->findOneByTypeAndLibelle(ReferentialTypeEnum::AILMENT, $libelle);
    }

    private function findOneByTypeAndLibelle(ReferentialTypeEnum $type, string $libelle): ?Referential
    {
        return $this->findOneBy([
           'type' => $type,
           'libelle' => $libelle,
        ]);
    }
}
