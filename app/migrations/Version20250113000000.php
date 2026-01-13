<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250113000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create todos table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE todos (
            id INT AUTO_INCREMENT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            completed TINYINT(1) NOT NULL DEFAULT 0,
            priority SMALLINT NOT NULL DEFAULT 2,
            due_date DATE DEFAULT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            completed_at DATETIME DEFAULT NULL,
            INDEX idx_completed (completed),
            INDEX idx_priority (priority),
            INDEX idx_created_at (created_at),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE todos');
    }
}
