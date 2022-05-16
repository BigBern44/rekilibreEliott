<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220125143513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE discussion_categorie_discussion (discussion_id INT NOT NULL, categorie_discussion_id INT NOT NULL, INDEX IDX_96595A2A1ADED311 (discussion_id), INDEX IDX_96595A2A1911325B (categorie_discussion_id), PRIMARY KEY(discussion_id, categorie_discussion_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE discussion_categorie_discussion ADD CONSTRAINT FK_96595A2A1ADED311 FOREIGN KEY (discussion_id) REFERENCES discussion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discussion_categorie_discussion ADD CONSTRAINT FK_96595A2A1911325B FOREIGN KEY (categorie_discussion_id) REFERENCES categorie_discussion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discussion DROP categorie_discussion_id');
        $this->addSql('ALTER TABLE registration CHANGE membership_check membership_check TINYINT(1) NOT NULL, CHANGE activities_single_check activities_single_check TINYINT(1) NOT NULL, CHANGE activities_multi_checks activities_multi_checks TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE discussion_categorie_discussion');
        $this->addSql('ALTER TABLE discussion ADD categorie_discussion_id INT NOT NULL');
        $this->addSql('ALTER TABLE registration CHANGE membership_check membership_check TINYINT(1) DEFAULT NULL, CHANGE activities_single_check activities_single_check TINYINT(1) DEFAULT NULL, CHANGE activities_multi_checks activities_multi_checks TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
