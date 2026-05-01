<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260501141005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE aimer (utilisateur_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_C2D0C6E8FB88E14F (utilisateur_id), INDEX IDX_C2D0C6E8F347EFB (produit_id), PRIMARY KEY (utilisateur_id, produit_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE aimer ADD CONSTRAINT FK_C2D0C6E8FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE aimer ADD CONSTRAINT FK_C2D0C6E8F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_produit DROP FOREIGN KEY `FK_53AE1BB5F347EFB`');
        $this->addSql('ALTER TABLE utilisateur_produit DROP FOREIGN KEY `FK_53AE1BB5FB88E14F`');
        $this->addSql('DROP TABLE utilisateur_produit');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur_produit (utilisateur_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_53AE1BB5FB88E14F (utilisateur_id), INDEX IDX_53AE1BB5F347EFB (produit_id), PRIMARY KEY (utilisateur_id, produit_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE utilisateur_produit ADD CONSTRAINT `FK_53AE1BB5F347EFB` FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_produit ADD CONSTRAINT `FK_53AE1BB5FB88E14F` FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE aimer DROP FOREIGN KEY FK_C2D0C6E8FB88E14F');
        $this->addSql('ALTER TABLE aimer DROP FOREIGN KEY FK_C2D0C6E8F347EFB');
        $this->addSql('DROP TABLE aimer');
    }
}
