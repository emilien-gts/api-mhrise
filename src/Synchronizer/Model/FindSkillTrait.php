<?php

namespace App\Synchronizer\Model;

use App\Entity\Skill\Skill;
use App\Entity\Skill\SkillVariant;

trait FindSkillTrait
{
    /** @var array<string, Skill> */
    public array $_skills = [];

    /** @var array<string, SkillVariant> */
    public array $_skillsVariants = [];

    private function findSkill(string $skillName): ?Skill
    {
        $skill = $this->_skills[$skillName] ?? null;
        $skill = $skill ?? $this->em->getRepository(Skill::class)->findOneBy(['name' => $skillName]);
        if (!isset($this->_skills[$skillName]) && $skill) {
            $this->_skills[$skillName] = $skill;
        }

        return $skill;
    }

    private function findSkillVariant(string $skillVariantName): ?SkillVariant
    {
        $skillVariant = $this->_skillsVariants[$skillVariantName] ?? null;
        $skillVariant = $skillVariant ?? $this->em->getRepository(SkillVariant::class)->findOneBy(['name' => $skillVariantName]);
        if (!isset($this->_skillsVariants[$skillVariantName]) && $skillVariant) {
            $this->_skillsVariants[$skillVariantName] = $skillVariant;
        }

        return $skillVariant;
    }
}
