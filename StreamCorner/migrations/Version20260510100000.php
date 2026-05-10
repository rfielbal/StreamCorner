<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260510100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a 1 to 5 star rating to product reviews.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE noter ADD note INT DEFAULT 5 NOT NULL');
        $this->addSql('ALTER TABLE noter ALTER note DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE noter DROP note');
    }
}
