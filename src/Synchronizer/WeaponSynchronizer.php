<?php

namespace App\Synchronizer;

use App\Entity\Item;
use App\Entity\Skill;
use App\Entity\Weapon\Weapon;
use App\Entity\Weapon\WeaponMaterial;
use App\Entity\Weapon\WeaponRampageSkill;
use App\Enum\WeaponMaterialTypeEnum;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;

class WeaponSynchronizer extends AbstractSynchronizer
{
    public const JSON_NAME = 'weapons.json';

    /** @var array<string, Skill> */
    public array $_skills = [];

    /** @var array<string, Item> */
    public array $_items = [];

    /**
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function sync(): void
    {
        $this->syncWeaponsTypes();
        $this->syncWeapons();

        $this->saveAndClose();
    }

    /**
     * @throws IOException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    private function syncWeapons(): void
    {
        $this->openJson(self::JSON_NAME, 'data');
        $depth = $this->reader->depth();
        $this->reader->read();

        do {
            /** @var array $data */
            $data = $this->reader->value();
            $this->syncWeapon($data);
        } while ($this->reader->next() && $this->reader->depth() > $depth);
    }

    private function syncWeaponsTypes(): void
    {
        $types = ['Great Sword', 'Sword & Shield', 'Dual Blades', 'Long Sword', 'Hammer', 'Hunting Horn', 'Lance', 'Gunlance', 'Switch Axe', 'Charge Blade', 'Insect Glaive', 'Bow', 'Heavy Bowgun', 'Light Bowgun'];
        foreach ($types as $value => $libelle) {
            $type = $this->referentialFactory->createWeaponType($libelle, $value);
            $this->em->persist($type);
        }

        $this->em->flush();
    }

    private function syncWeapon(array $data): void
    {
        $w = new Weapon();
        $w->name = $data['name'];
        $w->type = isset($data['weapon_type']) ? $this->referentialRepository->findOneWeaponTypeByValue((int) $data['weapon_type']) : null;
        $w->description = $data['description'] ?? null;
        $w->rarity = isset($data['rareType']) ? (int) $data['rareType'] : null;
        $w->baseValue = isset($data['baseVal']) ? (int) $data['baseVal'] : null;
        $w->buyValue = isset($data['buyVal']) ? (int) $data['buyVal'] : null;
        $w->attackValue = isset($data['atk']) ? (int) $data['atk'] : null;
        $w->criticalRate = isset($data['criticalRate']) ? (int) $data['criticalRate'] : null;
        $w->defenseBonus = isset($data['defBonus']) ? (int) $data['defBonus'] : null;

        if (\array_key_exists('rampage_skills', $data)) {
            $this->syncRampageSkills($data['rampage_skills'], $w);
        }

        if (\array_key_exists('forging_materials', $data)) {
            $this->syncMaterials($data['forging_materials'], $w, WeaponMaterialTypeEnum::FORGING_MATERIAL);
        }

        if (\array_key_exists('upgrade_materials', $data)) {
            $this->syncMaterials($data['upgrade_materials'], $w, WeaponMaterialTypeEnum::UPGRADE_MATERIAL);
        }

        $this->em->persist($w);
    }

    private function syncRampageSkills(array $rampageSkills, Weapon $w): void
    {
        foreach ($rampageSkills as $rampageSkill) {
            $s = $this->findSkill($rampageSkill['skill']['name']);
            if (null === $s) {
                continue;
            }

            $rs = new WeaponRampageSkill();
            $rs->nbSlots = isset($rampageSkill['rampage_slots']) ? (int) $rampageSkill['rampage_slots'] : null;
            $rs->description = $rampageSkill['description'];

            $rs->skill = $s;
            $w->addRampageSkill($rs);
        }
    }

    private function syncMaterials(array $materials, Weapon $w, WeaponMaterialTypeEnum $type): void
    {
        foreach ($materials as $material) {
            $i = $this->findItem($material['item']['name']);
            if (null === $i) {
                continue;
            }

            $m = new WeaponMaterial();
            $m->amount = isset($material['amount']) ? (int) \str_replace('x', '', $material['amount']) : null;
            $m->type = $type;

            $m->item = $i;
            $w->addMaterial($m);
        }
    }

    private function findSkill(string $skillName): ?Skill
    {
        $skill = $this->_skills[$skillName] ?? null;
        $skill = $skill ?? $this->em->getRepository(Skill::class)->findOneBy(['name' => $skillName]);
        if (!isset($this->_skills[$skillName]) && $skill) {
            $this->_skills[$skillName] = $skill;
        }

        return $skill;
    }

    private function findItem(string $itemName): ?Item
    {
        $item = $this->_items[$itemName] ?? null;
        $item = $item ?? $this->em->getRepository(Item::class)->findOneBy(['name' => $itemName]);
        if (!isset($this->_items[$itemName]) && $item) {
            $this->_items[$itemName] = $item;
        }

        return $item;
    }
}