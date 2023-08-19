<?php

namespace App\Command;

use App\Synchronizer\DecorationSynchronizer;
use App\Synchronizer\ItemSynchronizer;
use App\Synchronizer\MonsterSynchronizer;
use App\Synchronizer\QuestSynchronizer;
use App\Synchronizer\SkillSynchronizer;
use App\Synchronizer\WeaponSynchronizer;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: self::COMMAND_NAME, description: 'Synchronize data from JSON\'s files.')]
class SynchronizeCommand extends Command
{
    public const COMMAND_NAME = 'app:synchronize';

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly MonsterSynchronizer $monsterSynchronizer,
        private readonly QuestSynchronizer $questSynchronizer,
        private readonly ItemSynchronizer $itemSynchronizer,
        private readonly SkillSynchronizer $skillSynchronizer,
        private readonly DecorationSynchronizer $decorationSynchronizer,
        private readonly WeaponSynchronizer $weaponSynchronizer
    ) {
        parent::__construct(self::COMMAND_NAME);
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->sync();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());

            return 0;
        }

        return 1;
    }

    /**
     * @throws IOException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    private function sync(): void
    {
        $this->logger->notice('>>> Import monsters');
        $this->monsterSynchronizer->sync();

        $this->logger->notice('>>> Import quests');
        $this->questSynchronizer->sync();

        $this->logger->notice('>>> Import items');
        $this->itemSynchronizer->sync();

        $this->logger->notice('>>> Import skills');
        $this->skillSynchronizer->sync();

        $this->logger->notice('>>> Import decorations');
        $this->decorationSynchronizer->sync();

        $this->logger->notice('>>> Import weapons');
        $this->weaponSynchronizer->sync();
    }
}
