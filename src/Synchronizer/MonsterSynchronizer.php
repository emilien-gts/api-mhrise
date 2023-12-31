<?php

namespace App\Synchronizer;

use App\Entity\Monster;
use App\Repository\ReferentialRepository;
use App\Service\ReferentialFactory;
use Doctrine\ORM\EntityManagerInterface;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;
use pcrov\JsonReader\JsonReader;
use Symfony\Component\HttpKernel\KernelInterface;

class MonsterSynchronizer extends AbstractSynchronizer
{
    public const JSON_NAME = 'monsters.json';

    private array $_monsters = [];
    private array $_subSpecies = [];

    public function __construct(
        JsonReader $reader,
        EntityManagerInterface $em,
        KernelInterface $kernel,
        SynchronizerHelper $helper,
        ReferentialRepository $referentialRepository,
        ReferentialFactory $referentialFactory
    ) {
        parent::__construct($reader, $em, $helper, $referentialRepository, $referentialFactory, $kernel);
    }

    /**
     * @throws IOException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function sync(): void
    {
        $this->helper->cleanEntity(Monster::class);
        $this->openJson(self::JSON_NAME, 'monsters');

        $this->syncMainSpecies();
        $this->syncSubSpecies();

        $this->saveAndclose();
    }

    /**
     * @throws Exception
     */
    private function syncMainSpecies(): void
    {
        $depth = $this->reader->depth();
        $this->reader->read();

        do {
            /** @var array $data */
            $data = $this->reader->value();
            if ($this->supportsMonster($data)) {
                $this->syncMonster($data);
            }
        } while ($this->reader->next() && $this->reader->depth() > $depth);
    }

    private function supportsMonster(array $data): bool
    {
        return isset($data['name']) && !empty($this->getRiseData($data));
    }

    private function getRiseData(array $data): array
    {
        $mhRiseData = \array_values(\array_filter($data['games'] ?? [], function (array $data) {
            return isset($data['game']) && self::MHRISE_GAME_NAME === $data['game'];
        }));

        return $mhRiseData[0] ?? [];
    }

    private function syncMonster(array $data): void
    {
        $mhRiseData = $this->getRiseData($data);

        $m = new Monster($data['name']);
        $m->isLarge = $data['isLarge'] ?? null;
        $m->description = SynchronizerUtils::array_value_as_string($mhRiseData, 'description');
        $m->dangerLevel = SynchronizerUtils::array_value_as_int($mhRiseData, 'danger');

        $this->syncReferentialItem($data['type'], $m, 'findMonsterType', 'createMonsterType');
        $this->syncReferentialList($data['elements'] ?? [], $m, 'findElement', 'createElement');
        $this->syncReferentialList($data['ailments'] ?? [], $m, 'findAilment', 'createAilment');
        $this->syncReferentialList($data['weakness'] ?? [], $m, 'findWeakness', 'createElement');

        if (isset($data['subSpecies'])) {
            foreach ($data['subSpecies'] as $subSpecie) {
                $this->_subSpecies[$subSpecie] = $m;
            }
        }

        $this->_monsters[$m->name] = $m;
        $this->em->persist($m);
    }

    private function syncSubSpecies(): void
    {
        foreach ($this->_subSpecies as $subSpecie => $mainSpecie) {
            if (isset($this->_monsters[$subSpecie])) {
                $mainSpecie->addSubSpecie($this->_monsters[$subSpecie]);
            }
        }
    }
}
