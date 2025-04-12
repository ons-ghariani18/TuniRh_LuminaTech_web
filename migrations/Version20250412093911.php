<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250412093911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681ED5B3B0F1');
        $this->addSql('DROP INDEX IDX_B26681ED5B3B0F1 ON evenement');
        $this->addSql('ALTER TABLE evenement ADD sponsor_id INT NOT NULL');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E12F7FB51 FOREIGN KEY (sponsor_id) REFERENCES sponsor (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B26681E12F7FB51 ON evenement (sponsor_id)');
        $this->addSql('ALTER TABLE sponsor MODIFY id_sp INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON sponsor');
        $this->addSql('ALTER TABLE sponsor DROP type, DROP telephone, DROP email, DROP image, CHANGE id_sp id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE sponsor ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E12F7FB51');
        $this->addSql('DROP INDEX IDX_B26681E12F7FB51 ON evenement');
        $this->addSql('ALTER TABLE evenement DROP sponsor_id');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681ED5B3B0F1 FOREIGN KEY (id_sp) REFERENCES sponsor (id_sp) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B26681ED5B3B0F1 ON evenement (id_sp)');
        $this->addSql('ALTER TABLE sponsor MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON sponsor');
        $this->addSql('ALTER TABLE sponsor ADD type VARCHAR(100) NOT NULL, ADD telephone VARCHAR(15) NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD image VARCHAR(255) DEFAULT NULL, CHANGE id id_sp INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE sponsor ADD PRIMARY KEY (id_sp)');
    }
}
