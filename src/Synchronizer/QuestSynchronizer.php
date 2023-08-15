<?php

namespace App\Synchronizer;

use App\Entity\Monster;
use App\Entity\Quest;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;

class QuestSynchronizer extends AbstractSynchronizer
{
    public const JSON_NAME = 'quests.json';

    /**
     * @throws InvalidArgumentException
     * @throws IOException
     * @throws Exception
     */
    public function sync(): void
    {
        $this->helper->cleanEntity(Quest::class);

        $this->syncQuests();
        $this->saveAndclose();
    }

    /**
     * @throws IOException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    private function syncQuests(): void
    {
        $this->openJson(self::JSON_NAME, 'quests');
        $depth = $this->reader->depth();
        $this->reader->read();

        do {
            /** @var array $data */
            $data = $this->reader->value();
            if ($this->supportsQuest($data)) {
                $this->syncQuest($data);
            }
        } while ($this->reader->next() && $this->reader->depth() > $depth);
    }

    private function supportsQuest(array $data): bool
    {
        return isset($data['game']) && self::MHRISE_GAME_NAME === $data['game'];
    }

    private function syncQuest(array $data): void
    {
        $q = new Quest();
        $q->name = $data['name'];
        $q->description = $data['description'] ?? null;
        $q->isKey = $data['isKey'] ?? null;
        $q->objective = $data['objective'] ?? null;
        $q->difficulty = isset($data['difficulty']) ? (int) $data['difficulty'] : null;

        $this->syncReferentialItem($data['client'] ?? [], $q, 'findQuestClient', 'createQuestClient');
        $this->syncReferentialItem($data['map'] ?? [], $q, 'findMap', 'createMap');
        $this->syncReferentialItem($data['questType'] ?? [], $q, 'findQuestType', 'createQuestType');

        if (isset($data['targets'])) {
            $this->syncTargets($data['targets'], $q);
        }

        $this->em->persist($q);
    }

    private function syncTargets(array $targets, Quest $q): void
    {
        foreach ($targets as $target) {
            if ($m = $this->em->getRepository(Monster::class)->findOneBy(['name' => $target])) {
                $q->addTarget($m);
            }
        }
    }
}
