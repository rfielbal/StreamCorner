<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260503195100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Store user roles as text on MariaDB.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT '(DC2Type:json)'");
    }
}
