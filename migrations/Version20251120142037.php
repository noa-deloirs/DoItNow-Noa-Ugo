<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120142037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache_archive DROP FOREIGN KEY FK_1FAA6329D2235D39');
        $this->addSql('ALTER TABLE tache_archive CHANGE tache_id tache_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tache_archive ADD CONSTRAINT FK_1FAA6329D2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache_archive DROP FOREIGN KEY FK_1FAA6329D2235D39');
        $this->addSql('ALTER TABLE tache_archive CHANGE tache_id tache_id INT NOT NULL');
        $this->addSql('ALTER TABLE tache_archive ADD CONSTRAINT FK_1FAA6329D2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id)');
    }
}
