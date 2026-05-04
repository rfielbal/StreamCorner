<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260504120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow one product review per user and product.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE noter DROP FOREIGN KEY FK_761C961AA76ED395');
        $this->addSql('DROP INDEX UNIQ_761C961AA76ED395 ON noter');
        $this->addSql('ALTER TABLE noter DROP traitement');
        $this->addSql('CREATE INDEX IDX_761C961AA76ED395 ON noter (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_NOTER_USER_PRODUIT ON noter (user_id, produit_id)');
        $this->addSql('ALTER TABLE noter ADD CONSTRAINT FK_761C961AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE noter DROP FOREIGN KEY FK_761C961AA76ED395');
        $this->addSql('DROP INDEX UNIQ_NOTER_USER_PRODUIT ON noter');
        $this->addSql('DROP INDEX IDX_761C961AA76ED395 ON noter');
        $this->addSql('ALTER TABLE noter ADD traitement VARCHAR(50) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_761C961AA76ED395 ON noter (user_id)');
        $this->addSql('ALTER TABLE noter ADD CONSTRAINT FK_761C961AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }
}
