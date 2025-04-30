<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250416AddIdReclamRepToReponse extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajouter la colonne id_reclam_rep à la table reponse';
    }

    public function up(Schema $schema): void
    {
        // Vérifier si la colonne existe déjà
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');
        
        // Ajouter la colonne id_reclam_rep si elle n'existe pas
        $this->addSql('ALTER TABLE reponse ADD COLUMN IF NOT EXISTS id_reclam_rep INT NOT NULL');
        
        // Ajouter la contrainte de clé étrangère
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_reponse_reclamation 
                      FOREIGN KEY (id_reclam_rep) REFERENCES reclamation(id_reclamation) ON DELETE CASCADE');
        
        // Créer un index pour la colonne
        $this->addSql('CREATE INDEX IDX_5FB6DEC7_reclam_rep ON reponse (id_reclam_rep)');
        
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(Schema $schema): void
    {
        // Supprimer la contrainte de clé étrangère
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY IF EXISTS FK_reponse_reclamation');
        
        // Supprimer l'index
        $this->addSql('DROP INDEX IF EXISTS IDX_5FB6DEC7_reclam_rep ON reponse');
        
        // Supprimer la colonne
        $this->addSql('ALTER TABLE reponse DROP COLUMN IF EXISTS id_reclam_rep');
    }
}