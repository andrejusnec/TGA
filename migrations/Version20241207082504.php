<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241207082504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33C31A529C');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33C31A529C FOREIGN KEY (project_group_id) REFERENCES project_group (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33C31A529C');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33C31A529C FOREIGN KEY (project_group_id) REFERENCES project_group (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
