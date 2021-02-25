<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210225085528 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agences ADD user_id INT DEFAULT NULL, ADD user_creat_id INT DEFAULT NULL, ADD compte_id INT DEFAULT NULL, ADD num_agence INT NOT NULL, ADD adresse_agence VARCHAR(255) NOT NULL, ADD statut TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE agences ADD CONSTRAINT FK_B46015DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE agences ADD CONSTRAINT FK_B46015DD210AB6CE FOREIGN KEY (user_creat_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE agences ADD CONSTRAINT FK_B46015DDF2C56620 FOREIGN KEY (compte_id) REFERENCES comptes (id)');
        $this->addSql('CREATE INDEX IDX_B46015DDA76ED395 ON agences (user_id)');
        $this->addSql('CREATE INDEX IDX_B46015DD210AB6CE ON agences (user_creat_id)');
        $this->addSql('CREATE INDEX IDX_B46015DDF2C56620 ON agences (compte_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agences DROP FOREIGN KEY FK_B46015DDA76ED395');
        $this->addSql('ALTER TABLE agences DROP FOREIGN KEY FK_B46015DD210AB6CE');
        $this->addSql('ALTER TABLE agences DROP FOREIGN KEY FK_B46015DDF2C56620');
        $this->addSql('DROP INDEX IDX_B46015DDA76ED395 ON agences');
        $this->addSql('DROP INDEX IDX_B46015DD210AB6CE ON agences');
        $this->addSql('DROP INDEX IDX_B46015DDF2C56620 ON agences');
        $this->addSql('ALTER TABLE agences DROP user_id, DROP user_creat_id, DROP compte_id, DROP num_agence, DROP adresse_agence, DROP statut');
    }
}
