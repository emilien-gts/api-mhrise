<?php

namespace App\Synchronizer;

use App\Entity\Armor\Armor;
use App\Entity\Armor\ArmorMaterial;
use App\Entity\Armor\ArmorSkill;
use App\Enum\ArmorPartEnum;
use App\Enum\MaterialTypeEnum;
use App\Synchronizer\Model\FindItemTrait;
use App\Synchronizer\Model\FindSkillTrait;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;

class ArmorSynchronizer extends AbstractSynchronizer
{
    use FindSkillTrait;
    use FindItemTrait;

    public const JSON_NAME = 'armors.json';

    /**
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function sync(): void
    {
        $this->helper->cleanEntity(ArmorMaterial::class);
        $this->helper->cleanEntity(ArmorSkill::class);
        $this->helper->cleanEntity(Armor::class);

        $this->openJson(self::JSON_NAME, 'data');
        $depth = $this->reader->depth();
        $this->reader->read();

        do {
            /** @var array $data */
            $data = $this->reader->value();
            $this->syncArmor($data);
        } while ($this->reader->next() && $this->reader->depth() > $depth);

        $this->saveAndClose();
    }

    private function syncArmor(array $data): void
    {
        $a = new Armor($data['name']);
        $a->description = $data['description'] ?? null;
        $a->armorSetId = SynchronizerUtils::array_value_as_int($data, 'series');
        $a->rarity = SynchronizerUtils::array_value_as_int($data, 'rare');
        $a->value = SynchronizerUtils::array_value_as_int($data, 'value');
        $a->value = SynchronizerUtils::array_value_as_int($data, 'value');
        $a->buyValue = SynchronizerUtils::array_value_as_int($data, 'buyValue');
        $a->defenseValue = SynchronizerUtils::array_value_as_int($data, 'defVal');
        $a->fireDefenseValue = SynchronizerUtils::array_value_as_int($data, 'fireRegVal');
        $a->waterDefenseValue = SynchronizerUtils::array_value_as_int($data, 'waterRegVal');
        $a->iceDefenseValue = SynchronizerUtils::array_value_as_int($data, 'iceRegVal');
        $a->thunderDefenseValue = SynchronizerUtils::array_value_as_int($data, 'thunderRegVal');
        $a->dragonDefenseValue = SynchronizerUtils::array_value_as_int($data, 'dragonRegVal');
        $a->part = isset($data['part']) ? ArmorPartEnum::from($data['part']) : null;
        $a->rank = $data['rank'] ?? null;

        if (\array_key_exists('skills', $data)) {
            $this->syncSkills($data['skills'], $a);
        }

        if (\array_key_exists('forging_materials', $data)) {
            $this->syncMaterials($data['forging_materials'], $a, MaterialTypeEnum::FORGING_MATERIAL);
        }

        $this->em->persist($a);
    }

    private function syncSkills(array $skills, Armor $a): void
    {
        foreach ($skills as $skill) {
            $s = $this->findSkill($skill['skill']['name']);
            if (null === $s) {
                continue;
            }

            $as = new ArmorSkill();
            $as->description = $skill['description'];

            $as->skill = $s;
            $a->addSkill($as);
        }
    }

    private function syncMaterials(array $materials, Armor $a, MaterialTypeEnum $type): void
    {
        foreach ($materials as $material) {
            $i = $this->findItem($material['item']['name']);
            if (null === $i) {
                continue;
            }

            $m = new ArmorMaterial();
            $m->amount = isset($material['amount']) ? (int) \str_replace('x', '', $material['amount']) : null;
            $m->type = $type;

            $m->item = $i;
            $a->addMaterial($m);
        }
    }
}
