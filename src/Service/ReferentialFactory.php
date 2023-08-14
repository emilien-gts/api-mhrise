<?php

namespace App\Service;

use App\Entity\Referential;
use App\Enum\ReferentialTypeEnum;

class ReferentialFactory
{
    public function createMonsterType(string $libelle): Referential
    {
        return new Referential(ReferentialTypeEnum::MONSTER_TYPE, $libelle);
    }

    public function createElement(string $libelle): Referential
    {
        return new Referential(ReferentialTypeEnum::ELEMENT, $libelle);
    }

    public function createAilment(string $libelle): Referential
    {
        return new Referential(ReferentialTypeEnum::AILMENT, $libelle);
    }

    public function createQuestClient(string $libelle): Referential
    {
        return new Referential(ReferentialTypeEnum::QUEST_CLIENT, $libelle);
    }

    public function createMap(string $libelle): Referential
    {
        return new Referential(ReferentialTypeEnum::MAP, $libelle);
    }

    public function createQuestType(string $libelle): Referential
    {
        return new Referential(ReferentialTypeEnum::QUEST_TYPE, $libelle);
    }

    public function createItemType(string $libelle, ?int $value): Referential
    {
        $referential = new Referential(ReferentialTypeEnum::ITEM_TYPE, $libelle);
        $referential->value = $value;

        return $referential;
    }
}
