<?php

namespace App\Synchronizer;

use App\Entity\Skill\Skill;
use App\Entity\Skill\SkillVariant;
use App\Utils;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;

class SkillSynchronizer extends AbstractSynchronizer
{
    public const JSON_NAME = 'skills.json';

    /**
     * @throws IOException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function sync(): void
    {
        $this->helper->cleanEntity(Skill::class);
        $this->openJson(self::JSON_NAME, 'data');

        $depth = $this->reader->depth();
        $this->reader->read();

        do {
            /** @var array $data */
            $data = $this->reader->value();
            $this->syncSkill($data);
        } while ($this->reader->next() && $this->reader->depth() > $depth);

        $this->saveAndClose();
    }

    private function syncSkill(array $data): void
    {
        $s = new Skill();
        $s->name = $data['name'];
        $s->description = $data['description'] ?? null;

        for ($i = 1; $i <= 7; ++$i) {
            $key = \sprintf('lv%d', $i);
            if (!isset($data[$key])) {
                continue;
            }

            $name = \sprintf('%s %s', $data['name'], Utils::convert_to_roman($i));
            $sv = new SkillVariant($name);
            $sv->effect = $data[$key];

            $s->addVariant($sv);
        }

        $this->em->persist($s);
    }
}
