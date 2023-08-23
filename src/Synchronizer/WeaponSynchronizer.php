<?php

namespace App\Synchronizer;

use App\Entity\Weapon\Weapon;
use App\Entity\Weapon\WeaponMaterial;
use App\Entity\Weapon\WeaponRampageSkill;
use App\Enum\MaterialTypeEnum;
use App\Synchronizer\Model\FindItemTrait;
use App\Synchronizer\Model\FindSkillTrait;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;

class WeaponSynchronizer extends AbstractSynchronizer
{
    use FindItemTrait;
    use FindSkillTrait;

    public const JSON_NAME = 'weapons.json';
    public const WEAPONS_TYPES = ['Great Sword', 'Sword & Shield', 'Dual Blades', 'Long Sword', 'Hammer', 'Hunting Horn', 'Lance', 'Gunlance', 'Switch Axe', 'Charge Blade', 'Insect Glaive', 'Bow', 'Heavy Bowgun', 'Light Bowgun'];

    /**
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function sync(): void
    {
        $this->helper->cleanEntity(WeaponMaterial::class);
        $this->helper->cleanEntity(WeaponRampageSkill::class);
        $this->helper->cleanEntity(Weapon::class);
        $this->openJson(self::JSON_NAME, 'data');

        $this->syncWeaponsTypes();
        $this->syncWeapons();

        $this->saveAndClose();
    }

    private function syncWeaponsTypes(): void
    {
        foreach (self::WEAPONS_TYPES as $value => $libelle) {
            $type = $this->referentialFactory->createWeaponType($libelle, $value);
            $this->em->persist($type);
        }

        $this->em->flush();
    }

    /**
     * @throws IOException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    private function syncWeapons(): void
    {
        $depth = $this->reader->depth();
        $this->reader->read();

        do {
            /** @var array $data */
            $data = $this->reader->value();
            $this->syncWeapon($data);
        } while ($this->reader->next() && $this->reader->depth() > $depth);
    }

    private function syncWeapon(array $data): void
    {
        $w = new Weapon($data['name']);
        $w->type = isset($data['weapon_type']) ? $this->referentialRepository->findOneWeaponTypeByValue((int) $data['weapon_type']) : null;
        $w->description = $data['description'] ?? null;
        $w->rarity = SynchronizerUtils::array_value_as_int($data, 'rare');
        $w->baseValue = SynchronizerUtils::array_value_as_int($data, 'baseVal');
        $w->buyValue = SynchronizerUtils::array_value_as_int($data, 'buyVal');
        $w->attackValue = SynchronizerUtils::array_value_as_int($data, 'atk');
        $w->criticalRate = SynchronizerUtils::array_value_as_int($data, 'criticalRate');
        $w->defenseBonus = SynchronizerUtils::array_value_as_int($data, 'defBonus');

        if (\array_key_exists('rampage_skills', $data)) {
            $this->syncRampageSkills($data['rampage_skills'], $w);
        }

        if (\array_key_exists('forging_materials', $data)) {
            $this->syncMaterials($data['forging_materials'], $w, MaterialTypeEnum::FORGING_MATERIAL);
        }

        if (\array_key_exists('upgrade_materials', $data)) {
            $this->syncMaterials($data['upgrade_materials'], $w, MaterialTypeEnum::UPGRADE_MATERIAL);
        }

        $this->em->persist($w);
    }

    private function syncRampageSkills(array $rampageSkills, Weapon $w): void
    {
        foreach ($rampageSkills as $rampageSkill) {
            $s = $this->findSkillVariant($rampageSkill['skill']['name']);
            if (null === $s) {
                continue;
            }

            $rs = new WeaponRampageSkill();
            $rs->nbSlots = SynchronizerUtils::array_value_as_int($rampageSkills, 'rampage_slots');
            $rs->description = $rampageSkill['description'] ?? null;

            $rs->skill = $s;
            $w->addRampageSkill($rs);
        }
    }

    private function syncMaterials(array $materials, Weapon $w, MaterialTypeEnum $type): void
    {
        foreach ($materials as $material) {
            $i = $this->findItem($material['item']['name']);
            if (null === $i) {
                continue;
            }

            $m = new WeaponMaterial();
            $m->amount = SynchronizerUtils::array_amount_value_as_int($material);
            $m->type = $type;

            $m->item = $i;
            $w->addMaterial($m);
        }
    }
}
