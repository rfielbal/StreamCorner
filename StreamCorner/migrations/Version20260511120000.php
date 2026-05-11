<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260511120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename the Video product category to Diffusion.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE categorie SET nom_categorie = 'Diffusion' WHERE nom_categorie IN ('Video', 'video', 'Vidéo', 'vidéo')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE categorie SET nom_categorie = 'Video' WHERE nom_categorie = 'Diffusion'");
    }
}
