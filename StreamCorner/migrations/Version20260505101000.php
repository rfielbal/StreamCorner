<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260505101000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align product image index name with Doctrine metadata.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit_image DROP FOREIGN KEY FK_5D291BB3F347EFB');
        $this->addSql('DROP INDEX IDX_5D291BB3F347EFB ON produit_image');
        $this->addSql('CREATE INDEX IDX_F5A163CBF347EFB ON produit_image (produit_id)');
        $this->addSql('ALTER TABLE produit_image ADD CONSTRAINT FK_5D291BB3F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit_image DROP FOREIGN KEY FK_5D291BB3F347EFB');
        $this->addSql('DROP INDEX IDX_F5A163CBF347EFB ON produit_image');
        $this->addSql('CREATE INDEX IDX_5D291BB3F347EFB ON produit_image (produit_id)');
        $this->addSql('ALTER TABLE produit_image ADD CONSTRAINT FK_5D291BB3F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
    }
}
