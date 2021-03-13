<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210303174538 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agences ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE agences ADD CONSTRAINT FK_B46015DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B46015DDA76ED395 ON agences (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649236A171E');
        $this->addSql('DROP INDEX IDX_8D93D649236A171E ON user');
        $this->addSql('ALTER TABLE user DROP agance_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agences DROP FOREIGN KEY FK_B46015DDA76ED395');
        $this->addSql('DROP INDEX IDX_B46015DDA76ED395 ON agences');
        $this->addSql('ALTER TABLE agences DROP user_id');
        $this->addSql('ALTER TABLE user ADD agance_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649236A171E FOREIGN KEY (agance_id) REFERENCES agences (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D649236A171E ON user (agance_id)');
    }
}
