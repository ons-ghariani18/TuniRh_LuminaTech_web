<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416002754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY fk_formation');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY reclamation_ibfk_1');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY reponse_ibfk_1');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('ALTER TABLE candidatures DROP FOREIGN KEY candidatures_ibfk_1');
        $this->addSql('ALTER TABLE candidatures CHANGE id_candidature id_candidature INT NOT NULL, CHANGE id_offre id_offre INT DEFAULT NULL, CHANGE nom_candidat nom_candidat VARCHAR(100) NOT NULL, CHANGE email email VARCHAR(100) NOT NULL, CHANGE cv cv LONGTEXT NOT NULL, CHANGE lettre_motivation lettre_motivation LONGTEXT NOT NULL, CHANGE statut statu VARCHAR(50) NOT NULL');
        $this->addSql('DROP INDEX idoffre ON candidatures');
        $this->addSql('CREATE INDEX IDX_DE57CF664103C75F ON candidatures (id_offre)');
        $this->addSql('ALTER TABLE candidatures ADD CONSTRAINT candidatures_ibfk_1 FOREIGN KEY (id_offre) REFERENCES offresemploi (id_offre) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY evenement_ibfk_1');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY evenement_ibfk_1');
        $this->addSql('ALTER TABLE evenement ADD image VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(100) NOT NULL, CHANGE lieu lieu VARCHAR(255) NOT NULL, CHANGE budget budget DOUBLE PRECISION NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE google_calendar_event_id google_calendar_event_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681ED5B3B0F1 FOREIGN KEY (id_sp) REFERENCES sponsor (id_sp) ON DELETE CASCADE');
        $this->addSql('DROP INDEX id_sp ON evenement');
        $this->addSql('CREATE INDEX IDX_B26681ED5B3B0F1 ON evenement (id_sp)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT evenement_ibfk_1 FOREIGN KEY (id_sp) REFERENCES sponsor (id_sp) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE formation ADD max_participants INT NOT NULL, DROP cost, CHANGE description description LONGTEXT DEFAULT NULL, CHANGE start_date start_date DATETIME NOT NULL, CHANGE end_date end_date DATETIME NOT NULL, CHANGE name title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE offresemploi CHANGE id_offre id_offre INT NOT NULL, CHANGE description description LONGTEXT NOT NULL, CHANGE competences_requises competences_requises LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY reservations_ibfk_1');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY reservations_ibfk_2');
        $this->addSql('DROP INDEX user_id ON reservations');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY reservations_ibfk_1');
        $this->addSql('ALTER TABLE reservations ADD reservation_date DATETIME NOT NULL, ADD status VARCHAR(20) NOT NULL, DROP date_inscription, DROP statut, CHANGE employe_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA2395200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('CREATE INDEX IDX_4DA239A76ED395 ON reservations (user_id)');
        $this->addSql('DROP INDEX formation_id ON reservations');
        $this->addSql('CREATE INDEX IDX_4DA2395200282E ON reservations (formation_id)');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT reservations_ibfk_1 FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX email ON sponsor');
        $this->addSql('ALTER TABLE sponsor CHANGE type type VARCHAR(100) NOT NULL, CHANGE telephone telephone VARCHAR(15) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE users MODIFY id_user INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON users');
        $this->addSql('ALTER TABLE users ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', ADD account_type VARCHAR(50) DEFAULT NULL, DROP nom, DROP prenom, DROP tel, DROP address, DROP role, CHANGE email email VARCHAR(180) NOT NULL, CHANGE id_user id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE users ADD PRIMARY KEY (id)');
        $this->addSql('DROP INDEX email ON users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, formation_id INT NOT NULL, questionText TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, option1 VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, option2 VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, option3 VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, correctOptionIndex INT NOT NULL, INDEX fk_formation (formation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reclamation (idReclamation INT AUTO_INCREMENT NOT NULL, idUser INT NOT NULL, sujet VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, dateReclamation DATE NOT NULL, statut VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX idUser (idUser), PRIMARY KEY(idReclamation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reponse (idReponse INT AUTO_INCREMENT NOT NULL, idReclamation INT NOT NULL, message TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, dateReponse DATE NOT NULL, INDEX idReclamation (idReclamation), PRIMARY KEY(idReponse)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT fk_formation FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT reclamation_ibfk_1 FOREIGN KEY (idUser) REFERENCES users (id_user) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT reponse_ibfk_1 FOREIGN KEY (idReclamation) REFERENCES reclamation (idReclamation) ON DELETE CASCADE');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE candidatures DROP FOREIGN KEY FK_DE57CF664103C75F');
        $this->addSql('ALTER TABLE candidatures CHANGE id_candidature id_candidature INT AUTO_INCREMENT NOT NULL, CHANGE id_offre id_offre INT NOT NULL, CHANGE nom_candidat nom_candidat VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE cv cv TEXT NOT NULL, CHANGE lettre_motivation lettre_motivation TEXT NOT NULL, CHANGE statu statut VARCHAR(50) NOT NULL');
        $this->addSql('DROP INDEX idx_de57cf664103c75f ON candidatures');
        $this->addSql('CREATE INDEX idOffre ON candidatures (id_offre)');
        $this->addSql('ALTER TABLE candidatures ADD CONSTRAINT FK_DE57CF664103C75F FOREIGN KEY (id_offre) REFERENCES offresemploi (id_offre) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681ED5B3B0F1');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681ED5B3B0F1');
        $this->addSql('ALTER TABLE evenement DROP image, CHANGE type type VARCHAR(100) DEFAULT NULL, CHANGE lieu lieu VARCHAR(255) DEFAULT NULL, CHANGE budget budget NUMERIC(15, 2) DEFAULT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE google_calendar_event_id google_calendar_event_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT evenement_ibfk_1 FOREIGN KEY (id_sp) REFERENCES sponsor (id_sp) ON DELETE SET NULL');
        $this->addSql('DROP INDEX idx_b26681ed5b3b0f1 ON evenement');
        $this->addSql('CREATE INDEX id_sp ON evenement (id_sp)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681ED5B3B0F1 FOREIGN KEY (id_sp) REFERENCES sponsor (id_sp) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation ADD cost NUMERIC(10, 2) DEFAULT \'0.00\' NOT NULL, DROP max_participants, CHANGE description description TEXT DEFAULT NULL, CHANGE start_date start_date DATE NOT NULL, CHANGE end_date end_date DATE NOT NULL, CHANGE title name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE offresemploi CHANGE id_offre id_offre INT AUTO_INCREMENT NOT NULL, CHANGE description description TEXT NOT NULL, CHANGE competences_requises competences_requises TEXT NOT NULL');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA239A76ED395');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2395200282E');
        $this->addSql('DROP INDEX IDX_4DA239A76ED395 ON reservations');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY FK_4DA2395200282E');
        $this->addSql('ALTER TABLE reservations ADD date_inscription DATE DEFAULT CURRENT_DATE, ADD statut VARCHAR(50) DEFAULT \'en attente\' NOT NULL, DROP reservation_date, DROP status, CHANGE user_id employe_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT reservations_ibfk_1 FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT reservations_ibfk_2 FOREIGN KEY (employe_id) REFERENCES users (id_user) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX user_id ON reservations (employe_id)');
        $this->addSql('DROP INDEX idx_4da2395200282e ON reservations');
        $this->addSql('CREATE INDEX formation_id ON reservations (formation_id)');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA2395200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE sponsor CHANGE type type VARCHAR(100) DEFAULT NULL, CHANGE telephone telephone VARCHAR(20) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX email ON sponsor (email)');
        $this->addSql('ALTER TABLE users MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON users');
        $this->addSql('ALTER TABLE users ADD nom VARCHAR(100) NOT NULL, ADD prenom VARCHAR(100) NOT NULL, ADD tel VARCHAR(20) NOT NULL, ADD address TEXT DEFAULT NULL, ADD role VARCHAR(100) NOT NULL, DROP roles, DROP account_type, CHANGE email email VARCHAR(255) NOT NULL, CHANGE id id_user INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE users ADD PRIMARY KEY (id_user)');
        $this->addSql('DROP INDEX uniq_1483a5e9e7927c74 ON users');
        $this->addSql('CREATE UNIQUE INDEX email ON users (email)');
    }
}
