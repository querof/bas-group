<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241002112257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add encryptionKey';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE recipient ADD encryption_key VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE recipient RENAME INDEX identifier_unique TO UNIQ_6804FB49772E836A');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE recipient DROP encryption_key');
        $this->addSql('ALTER TABLE recipient RENAME INDEX uniq_6804fb49772e836a TO identifier_unique');
    }
}
