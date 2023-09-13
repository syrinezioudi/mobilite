<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230912193350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calcul_score (id INT AUTO_INCREMENT NOT NULL, id_student_id INT DEFAULT NULL, score DOUBLE PRECISION DEFAULT NULL, UNIQUE INDEX UNIQ_BAB3ABFB6E1ECFCD (id_student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coach (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(12) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre (id INT AUTO_INCREMENT NOT NULL, coach_id INT DEFAULT NULL, titre VARCHAR(20) NOT NULL, description VARCHAR(255) NOT NULL, date DATE NOT NULL, img LONGTEXT NOT NULL, lieu VARCHAR(30) NOT NULL, pos1 DOUBLE PRECISION NOT NULL, pos2 DOUBLE PRECISION NOT NULL, INDEX IDX_AF86866F3C105691 (coach_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, offre_id INT NOT NULL, user_id INT NOT NULL, date_participation DATE NOT NULL, rbac VARCHAR(20) NOT NULL, r1 VARCHAR(20) NOT NULL, r2 VARCHAR(20) NOT NULL, r3 VARCHAR(20) NOT NULL, r4 VARCHAR(20) NOT NULL, rl1 VARCHAR(20) NOT NULL, rl2 VARCHAR(20) NOT NULL, rl3 VARCHAR(20) NOT NULL, niveau_f VARCHAR(20) NOT NULL, niveau_a VARCHAR(20) NOT NULL, nomp VARCHAR(20) NOT NULL, prenom VARCHAR(20) NOT NULL, email VARCHAR(20) NOT NULL, INDEX IDX_D79F6B114CC8505A (offre_id), INDEX IDX_D79F6B11A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE syrine (id INT AUTO_INCREMENT NOT NULL, nomprenom VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(20) NOT NULL, prenom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calcul_score ADD CONSTRAINT FK_BAB3ABFB6E1ECFCD FOREIGN KEY (id_student_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866F3C105691 FOREIGN KEY (coach_id) REFERENCES coach (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B114CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calcul_score DROP FOREIGN KEY FK_BAB3ABFB6E1ECFCD');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866F3C105691');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B114CC8505A');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11A76ED395');
        $this->addSql('DROP TABLE calcul_score');
        $this->addSql('DROP TABLE coach');
        $this->addSql('DROP TABLE offre');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE syrine');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
