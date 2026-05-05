<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260505100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add multi-image gallery support for products.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE produit_image (id INT AUTO_INCREMENT NOT NULL, produit_id INT NOT NULL, filename VARCHAR(255) NOT NULL, alt VARCHAR(180) DEFAULT NULL, position INT NOT NULL, INDEX IDX_5D291BB3F347EFB (produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit_image ADD CONSTRAINT FK_5D291BB3F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit_image DROP FOREIGN KEY FK_5D291BB3F347EFB');
        $this->addSql('DROP TABLE produit_image');
    }
}
