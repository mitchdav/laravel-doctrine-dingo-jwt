<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170621013435 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE albums (id INT AUTO_INCREMENT NOT NULL, artist_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, release_date DATETIME DEFAULT NULL, INDEX IDX_F4E2474FB7970CF8 (artist_id), INDEX name_idx (name), INDEX releaseDate_idx (release_date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artists (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, UNIQUE INDEX name_unq (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE AlbumToUser (user_id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_4B4684D8A76ED395 (user_id), INDEX IDX_4B4684D81137ABCF (album_id), PRIMARY KEY(user_id, album_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE albums ADD CONSTRAINT FK_F4E2474FB7970CF8 FOREIGN KEY (artist_id) REFERENCES artists (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE AlbumToUser ADD CONSTRAINT FK_4B4684D8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE AlbumToUser ADD CONSTRAINT FK_4B4684D81137ABCF FOREIGN KEY (album_id) REFERENCES albums (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE permissions CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE organisations CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE roles CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE job job VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE AlbumToUser DROP FOREIGN KEY FK_4B4684D81137ABCF');
        $this->addSql('ALTER TABLE albums DROP FOREIGN KEY FK_F4E2474FB7970CF8');
        $this->addSql('DROP TABLE albums');
        $this->addSql('DROP TABLE artists');
        $this->addSql('DROP TABLE AlbumToUser');
        $this->addSql('ALTER TABLE organisations CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE permissions CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE roles CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE users CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE address address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE job job VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
