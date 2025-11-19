<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119144700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tache_archive (id INT AUTO_INCREMENT NOT NULL, tache_id INT NOT NULL, user_id INT NOT NULL, categorie_id INT NOT NULL, statut_id INT NOT NULL, titre VARCHAR(50) NOT NULL, description VARCHAR(255) NOT NULL, date_echeance DATE NOT NULL, priotite VARCHAR(50) NOT NULL, date_archivage DATE NOT NULL, INDEX IDX_1FAA6329D2235D39 (tache_id), INDEX IDX_1FAA6329A76ED395 (user_id), INDEX IDX_1FAA6329BCF5E72D (categorie_id), INDEX IDX_1FAA6329F6203804 (statut_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tache_archive ADD CONSTRAINT FK_1FAA6329D2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id)');
        $this->addSql('ALTER TABLE tache_archive ADD CONSTRAINT FK_1FAA6329A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tache_archive ADD CONSTRAINT FK_1FAA6329BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('ALTER TABLE tache_archive ADD CONSTRAINT FK_1FAA6329F6203804 FOREIGN KEY (statut_id) REFERENCES statut (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tache_archive DROP FOREIGN KEY FK_1FAA6329D2235D39');
        $this->addSql('ALTER TABLE tache_archive DROP FOREIGN KEY FK_1FAA6329A76ED395');
        $this->addSql('ALTER TABLE tache_archive DROP FOREIGN KEY FK_1FAA6329BCF5E72D');
        $this->addSql('ALTER TABLE tache_archive DROP FOREIGN KEY FK_1FAA6329F6203804');
        $this->addSql('DROP TABLE tache_archive');
    }
}
