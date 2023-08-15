<?php

namespace App\Command;

use App\Synchronizer\ItemSynchronizer;
use App\Synchronizer\MonsterSynchronizer;
use App\Synchronizer\QuestSynchronizer;
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
        private readonly ItemSynchronizer $itemSynchronizer
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
            $this->logger->notice('>>> Import monsters');
            $this->monsterSynchronizer->sync();

            $this->logger->notice('>>> Import quests');
            $this->questSynchronizer->sync();

            $this->logger->notice('>>> Import items');
            $this->itemSynchronizer->sync();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());

            return 0;
        }

        return 1;
    }
}
