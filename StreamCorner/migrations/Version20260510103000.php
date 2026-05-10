<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260510103000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add sender email to public contact messages.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE contact ADD email VARCHAR(180) NOT NULL DEFAULT ''");
        $this->addSql('ALTER TABLE contact ALTER email DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact DROP email');
    }
}
