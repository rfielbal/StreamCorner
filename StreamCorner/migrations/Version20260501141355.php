<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260501141355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AJOUTER_PANIER_PRODUIT ON ajouter (panier_id, produit_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PARVENIR_COMMANDE_PRODUIT ON parvenir (commande_id, produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_AJOUTER_PANIER_PRODUIT ON ajouter');
        $this->addSql('DROP INDEX UNIQ_PARVENIR_COMMANDE_PRODUIT ON parvenir');
    }
}
