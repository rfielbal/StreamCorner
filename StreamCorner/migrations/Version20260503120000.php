<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260503120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Use User as the single customer entity.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76FB88E14F');
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F0816FB88E14F');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DFB88E14F');
        $this->addSql('ALTER TABLE noter DROP FOREIGN KEY FK_761C961AFB88E14F');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2FB88E14F');
        $this->addSql('ALTER TABLE aimer DROP FOREIGN KEY FK_C2D0C6E8FB88E14F');

        $this->addSql('DROP INDEX UNIQ_880E0D76FB88E14F ON admin');
        $this->addSql('DROP INDEX IDX_C35F0816FB88E14F ON adresse');
        $this->addSql('DROP INDEX IDX_6EEAA67DFB88E14F ON commande');
        $this->addSql('DROP INDEX UNIQ_761C961AFB88E14F ON noter');
        $this->addSql('DROP INDEX UNIQ_24CC0DF2FB88E14F ON panier');
        $this->addSql('DROP INDEX IDX_C2D0C6E8FB88E14F ON aimer');

        $this->addSql('ALTER TABLE admin CHANGE utilisateur_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE adresse CHANGE utilisateur_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande CHANGE utilisateur_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE noter CHANGE utilisateur_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE panier CHANGE utilisateur_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE aimer CHANGE utilisateur_id user_id INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (user_id, produit_id)');

        $this->addSql('ALTER TABLE user ADD prenom VARCHAR(100) DEFAULT NULL, ADD nom VARCHAR(100) DEFAULT NULL, CHANGE roles roles JSON NOT NULL');

        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE noter ADD CONSTRAINT FK_761C961AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE aimer ADD CONSTRAINT FK_C2D0C6E8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76A76ED395 ON admin (user_id)');
        $this->addSql('CREATE INDEX IDX_C35F0816A76ED395 ON adresse (user_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_761C961AA76ED395 ON noter (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_24CC0DF2A76ED395 ON panier (user_id)');
        $this->addSql('CREATE INDEX IDX_C2D0C6E8A76ED395 ON aimer (user_id)');

        $this->addSql('DROP TABLE utilisateur');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom_u VARCHAR(100) NOT NULL, prenom_u VARCHAR(100) NOT NULL, mdp_u VARCHAR(255) NOT NULL, email_u VARCHAR(180) NOT NULL, role_u VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql("INSERT INTO utilisateur (id, nom_u, prenom_u, mdp_u, email_u, role_u) SELECT id, COALESCE(nom, 'Client'), COALESCE(prenom, 'StreamCorner'), password, email, 'ROLE_USER' FROM user");

        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76A76ED395');
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F0816A76ED395');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE noter DROP FOREIGN KEY FK_761C961AA76ED395');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2A76ED395');
        $this->addSql('ALTER TABLE aimer DROP FOREIGN KEY FK_C2D0C6E8A76ED395');

        $this->addSql('DROP INDEX UNIQ_880E0D76A76ED395 ON admin');
        $this->addSql('DROP INDEX IDX_C35F0816A76ED395 ON adresse');
        $this->addSql('DROP INDEX IDX_6EEAA67DA76ED395 ON commande');
        $this->addSql('DROP INDEX UNIQ_761C961AA76ED395 ON noter');
        $this->addSql('DROP INDEX UNIQ_24CC0DF2A76ED395 ON panier');
        $this->addSql('DROP INDEX IDX_C2D0C6E8A76ED395 ON aimer');

        $this->addSql('ALTER TABLE admin CHANGE user_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE adresse CHANGE user_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande CHANGE user_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE noter CHANGE user_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE panier CHANGE user_id utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE aimer CHANGE user_id utilisateur_id INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (utilisateur_id, produit_id)');

        $this->addSql('ALTER TABLE user DROP prenom, DROP nom, CHANGE roles roles LONGTEXT NOT NULL');

        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE noter ADD CONSTRAINT FK_761C961AFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE aimer ADD CONSTRAINT FK_C2D0C6E8FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76FB88E14F ON admin (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_C35F0816FB88E14F ON adresse (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DFB88E14F ON commande (utilisateur_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_761C961AFB88E14F ON noter (utilisateur_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_24CC0DF2FB88E14F ON panier (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_C2D0C6E8FB88E14F ON aimer (utilisateur_id)');
    }
}
