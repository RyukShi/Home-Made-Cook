<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220929142147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C9C055445');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C9C055445 FOREIGN KEY (cooker_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recipe DROP CONSTRAINT FK_DA88B1379C055445');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B1379C055445 FOREIGN KEY (cooker_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE recipe DROP CONSTRAINT fk_da88b1379c055445');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT fk_da88b1379c055445 FOREIGN KEY (cooker_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT fk_9474526c9c055445');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526c9c055445 FOREIGN KEY (cooker_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
