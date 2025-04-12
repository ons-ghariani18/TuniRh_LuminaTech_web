<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250411140709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Create messenger_messages table
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        // Handle foreign key if it exists
        $this->addSql('SET @fkExists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = "FK_4DA239A76ED395" AND TABLE_NAME = "reservations")');
        $this->addSql('SET @sqlDropFK = IF(@fkExists = 1, "ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239A76ED395", "SELECT \'Foreign key constraint does not exist\' AS message")');
        $this->addSql('PREPARE dropFKStmt FROM @sqlDropFK');
        $this->addSql('EXECUTE dropFKStmt');
        $this->addSql('DEALLOCATE PREPARE dropFKStmt');
        
        // Remove index if it exists
        $this->addSql('SET @idxExists = (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = "reservations" AND INDEX_NAME = "FK_4DA239A76ED395")');
        $this->addSql('SET @sqlDropIdx = IF(@idxExists = 1, "DROP INDEX FK_4DA239A76ED395 ON reservations", "SELECT \'Index does not exist\' AS message")');
        $this->addSql('PREPARE dropIdxStmt FROM @sqlDropIdx');
        $this->addSql('EXECUTE dropIdxStmt');
        $this->addSql('DEALLOCATE PREPARE dropIdxStmt');
        
        // For MariaDB, let's handle the roles conversion differently
        // First, create a temporary column for JSON data
        $this->addSql('ALTER TABLE users ADD roles_json LONGTEXT NULL');
        
        // Update with proper JSON array format for existing roles
        $this->addSql('UPDATE users SET roles_json = CONCAT(\'["ROLE_\', UPPER(roles), \'"]\') WHERE roles != ""');
        $this->addSql('UPDATE users SET roles_json = \'["ROLE_USER"]\' WHERE roles = "" OR roles IS NULL');
        
        // Modify the users table
        $this->addSql('ALTER TABLE users DROP roles');
        $this->addSql('ALTER TABLE users CHANGE roles_json roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE users CHANGE id_user id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE users DROP tel, DROP address, CHANGE email email VARCHAR(180) NOT NULL, CHANGE account_type account_type VARCHAR(50) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        
        // Then modify the reservations table and add the constraint
        $this->addSql('ALTER TABLE reservations CHANGE id_user user_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_4DA239A76ED395 ON reservations (user_id)');
    }
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239A76ED395');
        $this->addSql('DROP INDEX IDX_4DA239A76ED395 ON reservations');
        $this->addSql('ALTER TABLE reservations CHANGE user_id id_user INT NOT NULL');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239A76ED395 FOREIGN KEY (id_user) REFERENCES users (id_user)');
        $this->addSql('CREATE INDEX FK_4DA239A76ED395 ON reservations (id_user)');
        $this->addSql('ALTER TABLE users MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON users');
        $this->addSql('DROP INDEX `PRIMARY` ON users');
        $this->addSql('ALTER TABLE users ADD tel VARCHAR(15) NOT NULL, ADD address VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE roles roles VARCHAR(50) NOT NULL, CHANGE account_type account_type VARCHAR(50) DEFAULT NULL COMMENT \'Type de compte utilisateur\', CHANGE id id_user INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE users ADD PRIMARY KEY (id_user)');
    }
}
