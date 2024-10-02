<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241002132233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add expiration date to message and change the size of encryption key';
    }

    public function up(Schema $schema): void
    {
        $defaultExpirationDate = (new \DateTimeImmutable())->modify('+30 days')->format('Y-m-d H:i:s');

        $this->addSql('ALTER TABLE message ADD expiration_date DATETIME NOT NULL DEFAULT :defaultExpirationDate COMMENT \'(DC2Type:datetime_immutable)\'', [
            'defaultExpirationDate' => $defaultExpirationDate,
        ]);
        $this->addSql('ALTER TABLE recipient CHANGE encryption_key encryption_key VARCHAR(45) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE message DROP expiration_date');
        $this->addSql('ALTER TABLE recipient CHANGE encryption_key encryption_key VARCHAR(255) NOT NULL');
    }
}
