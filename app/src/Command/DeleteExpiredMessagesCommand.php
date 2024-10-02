<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:delete-expired-messages',
    description: 'Deletes messages that have expired.',
)]
class DeleteExpiredMessagesCommand extends Command
{
    private MessageRepository $messageRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(MessageRepository $messageRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->messageRepository = $messageRepository;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $expiredMessages = $this->messageRepository->findExpiredMessages();

        foreach ($expiredMessages as $message) {
            $this->entityManager->remove($message);
        }

        $this->entityManager->flush();

        $output->writeln('Expired messages deleted.');

        return Command::SUCCESS;
    }
}