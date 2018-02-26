<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170428030234 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `job` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
          `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
          `time_deleted` datetime DEFAULT NULL,
          `deleted` tinyint(1) NOT NULL DEFAULT \'0\',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;');
        $this->addSql('CREATE TABLE permissions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organisations (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission_role (role_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_6A711CAD60322AC (role_id), INDEX IDX_6A711CAFED90CCA (permission_id), PRIMARY KEY(role_id, permission_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organisation_user (user_id INT NOT NULL, organisation_id INT NOT NULL, INDEX IDX_CFD7D651A76ED395 (user_id), INDEX IDX_CFD7D6519E6B1585 (organisation_id), PRIMARY KEY(user_id, organisation_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_user (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_332CA4DDA76ED395 (user_id), INDEX IDX_332CA4DDD60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission_user (user_id INT NOT NULL, permission_id INT NOT NULL, INDEX IDX_DC5D4DE9A76ED395 (user_id), INDEX IDX_DC5D4DE9FED90CCA (permission_id), PRIMARY KEY(user_id, permission_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE permission_role ADD CONSTRAINT FK_6A711CAD60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE permission_role ADD CONSTRAINT FK_6A711CAFED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organisation_user ADD CONSTRAINT FK_CFD7D651A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organisation_user ADD CONSTRAINT FK_CFD7D6519E6B1585 FOREIGN KEY (organisation_id) REFERENCES organisations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDD60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE permission_user ADD CONSTRAINT FK_DC5D4DE9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE permission_user ADD CONSTRAINT FK_DC5D4DE9FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users ADD address VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE permission_role DROP FOREIGN KEY FK_6A711CAFED90CCA');
        $this->addSql('ALTER TABLE permission_user DROP FOREIGN KEY FK_DC5D4DE9FED90CCA');
        $this->addSql('ALTER TABLE organisation_user DROP FOREIGN KEY FK_CFD7D6519E6B1585');
        $this->addSql('ALTER TABLE permission_role DROP FOREIGN KEY FK_6A711CAD60322AC');
        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDD60322AC');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE organisations');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE permission_role');
        $this->addSql('DROP TABLE organisation_user');
        $this->addSql('DROP TABLE role_user');
        $this->addSql('DROP TABLE permission_user');
        $this->addSql('ALTER TABLE users DROP address');
    }
}
