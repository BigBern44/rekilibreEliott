<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220125103916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_dicussion (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE discussion ADD categorie_dicussion_id INT NOT NULL');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FBC03817 FOREIGN KEY (categorie_dicussion_id) REFERENCES categorie_dicussion (id)');
        $this->addSql('CREATE INDEX IDX_C0B9F90FBC03817 ON discussion (categorie_dicussion_id)');
        $this->addSql('ALTER TABLE registration CHANGE membership_check membership_check TINYINT(1) NOT NULL, CHANGE activities_single_check activities_single_check TINYINT(1) NOT NULL, CHANGE activities_multi_checks activities_multi_checks TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FBC03817');
        $this->addSql('DROP TABLE categorie_dicussion');
        $this->addSql('DROP INDEX IDX_C0B9F90FBC03817 ON discussion');
        $this->addSql('ALTER TABLE discussion DROP categorie_dicussion_id');
        $this->addSql('ALTER TABLE registration CHANGE membership_check membership_check TINYINT(1) DEFAULT NULL, CHANGE activities_single_check activities_single_check TINYINT(1) DEFAULT NULL, CHANGE activities_multi_checks activities_multi_checks TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
