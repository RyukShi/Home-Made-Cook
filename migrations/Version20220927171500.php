<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220927171500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe ADD image_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE recipe ADD image_file VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE recipe ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE recipe DROP image_name');
        $this->addSql('ALTER TABLE recipe DROP image_file');
        $this->addSql('ALTER TABLE recipe DROP updated_at');
    }
}
