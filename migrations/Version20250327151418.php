<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250327151048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Check if the foreign key exists before dropping it
        $exists = $this->connection->fetchOne('SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE CONSTRAINT_NAME = ? AND TABLE_NAME = ?', ['FK_5FB6DEC78E3D9B7C', 'reponse']);
        if ($exists) {
            $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC78E3D9B7C');
        }

        $this->addSql('DROP INDEX fk_5fb6dec78e3d9b7c ON reponse');
        $this->addSql('CREATE INDEX IDX_5FB6DEC78E3D9B7C ON reponse (idReclamation)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC78E3D9B7C FOREIGN KEY (idReclamation) REFERENCES reclamation (id_reclamation) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');

        // Check if the foreign key exists before dropping it
        $exists = $this->connection->fetchOne('SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE CONSTRAINT_NAME = ? AND TABLE_NAME = ?', ['FK_5FB6DEC78E3D9B7C', 'reponse']);
        if ($exists) {
            $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC78E3D9B7C');
        }

        $this->addSql('DROP INDEX idx_5fb6dec78e3d9b7c ON reponse');
        $this->addSql('CREATE INDEX FK_5FB6DEC78E3D9B7C ON reponse (idReclamation)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC78E3D9B7C FOREIGN KEY (idReclamation) REFERENCES reclamation (idReclamation) ON DELETE CASCADE');
    }
}
