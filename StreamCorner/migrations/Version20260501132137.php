<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260501132137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, email_a VARCHAR(180) NOT NULL, mdp_a VARCHAR(255) NOT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_880E0D76FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, ville VARCHAR(100) NOT NULL, rue VARCHAR(255) NOT NULL, cp VARCHAR(10) NOT NULL, pays VARCHAR(100) NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_C35F0816FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ajouter (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, prix_ht NUMERIC(10, 2) NOT NULL, panier_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_AB384B5FF77D927C (panier_id), INDEX IDX_AB384B5FF347EFB (produit_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom_categorie VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, date_commande DATETIME NOT NULL, total_ht_co NUMERIC(10, 2) NOT NULL, total_taxe NUMERIC(10, 2) NOT NULL, total NUMERIC(10, 2) NOT NULL, utilisateur_id INT NOT NULL, adresse_id INT NOT NULL, INDEX IDX_6EEAA67DFB88E14F (utilisateur_id), INDEX IDX_6EEAA67D4DE7DC5C (adresse_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE noter (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT NOT NULL, date_message DATETIME NOT NULL, traitement VARCHAR(50) NOT NULL, utilisateur_id INT NOT NULL, produit_id INT NOT NULL, UNIQUE INDEX UNIQ_761C961AFB88E14F (utilisateur_id), INDEX IDX_761C961AF347EFB (produit_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, tototal_ht_pa NUMERIC(10, 2) NOT NULL, utilisateur_id INT NOT NULL, UNIQUE INDEX UNIQ_24CC0DF2FB88E14F (utilisateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE parvenir (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, prix_ht NUMERIC(10, 2) NOT NULL, commande_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_904E91E882EA2E54 (commande_id), INDEX IDX_904E91E8F347EFB (produit_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(150) NOT NULL, prix_unit_ht NUMERIC(10, 2) NOT NULL, image VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, stock INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_29A5EC27BCF5E72D (categorie_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE sav (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT NOT NULL, date_message DATETIME NOT NULL, traitement VARCHAR(50) NOT NULL, commande_id INT NOT NULL, INDEX IDX_6C7681F482EA2E54 (commande_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom_u VARCHAR(100) NOT NULL, prenom_u VARCHAR(100) NOT NULL, mdp_u VARCHAR(100) NOT NULL, email_u VARCHAR(180) NOT NULL, role_u VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur_produit (utilisateur_id INT NOT NULL, produit_id INT NOT NULL, INDEX IDX_53AE1BB5FB88E14F (utilisateur_id), INDEX IDX_53AE1BB5F347EFB (produit_id), PRIMARY KEY (utilisateur_id, produit_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE ajouter ADD CONSTRAINT FK_AB384B5FF77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE ajouter ADD CONSTRAINT FK_AB384B5FF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D4DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id)');
        $this->addSql('ALTER TABLE noter ADD CONSTRAINT FK_761C961AFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE noter ADD CONSTRAINT FK_761C961AF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE parvenir ADD CONSTRAINT FK_904E91E882EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE parvenir ADD CONSTRAINT FK_904E91E8F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE sav ADD CONSTRAINT FK_6C7681F482EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE utilisateur_produit ADD CONSTRAINT FK_53AE1BB5FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_produit ADD CONSTRAINT FK_53AE1BB5F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76FB88E14F');
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F0816FB88E14F');
        $this->addSql('ALTER TABLE ajouter DROP FOREIGN KEY FK_AB384B5FF77D927C');
        $this->addSql('ALTER TABLE ajouter DROP FOREIGN KEY FK_AB384B5FF347EFB');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFB88E14F');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D4DE7DC5C');
        $this->addSql('ALTER TABLE noter DROP FOREIGN KEY FK_761C961AFB88E14F');
        $this->addSql('ALTER TABLE noter DROP FOREIGN KEY FK_761C961AF347EFB');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2FB88E14F');
        $this->addSql('ALTER TABLE parvenir DROP FOREIGN KEY FK_904E91E882EA2E54');
        $this->addSql('ALTER TABLE parvenir DROP FOREIGN KEY FK_904E91E8F347EFB');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27BCF5E72D');
        $this->addSql('ALTER TABLE sav DROP FOREIGN KEY FK_6C7681F482EA2E54');
        $this->addSql('ALTER TABLE utilisateur_produit DROP FOREIGN KEY FK_53AE1BB5FB88E14F');
        $this->addSql('ALTER TABLE utilisateur_produit DROP FOREIGN KEY FK_53AE1BB5F347EFB');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('DROP TABLE ajouter');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE noter');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE parvenir');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE sav');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateur_produit');
    }
}
