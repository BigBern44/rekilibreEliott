<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220117164717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F69CCBE9A');
        $this->addSql('DROP INDEX IDX_C0B9F90F69CCBE9A ON discussion');
        $this->addSql('ALTER TABLE discussion CHANGE author_id_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C0B9F90FF675F31B ON discussion (author_id)');
        $this->addSql('ALTER TABLE registration CHANGE membership_check membership_check TINYINT(1) NOT NULL, CHANGE activities_single_check activities_single_check TINYINT(1) NOT NULL, CHANGE activities_multi_checks activities_multi_checks TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FF675F31B');
        $this->addSql('DROP INDEX IDX_C0B9F90FF675F31B ON discussion');
        $this->addSql('ALTER TABLE discussion CHANGE author_id author_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F69CCBE9A FOREIGN KEY (author_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_C0B9F90F69CCBE9A ON discussion (author_id_id)');
        $this->addSql('ALTER TABLE registration CHANGE membership_check membership_check TINYINT(1) DEFAULT NULL, CHANGE activities_single_check activities_single_check TINYINT(1) DEFAULT NULL, CHANGE activities_multi_checks activities_multi_checks TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
