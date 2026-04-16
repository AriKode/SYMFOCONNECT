<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260416120124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, content LONGTEXT NOT NULL, is_read TINYINT NOT NULL, created_at DATETIME NOT NULL, recipient_id INT NOT NULL, INDEX IDX_BF5476CAE92F8F78 (recipient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE post_likes (post_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_DED1C2924B89032C (post_id), INDEX IDX_DED1C292A76ED395 (user_id), PRIMARY KEY (post_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_follows (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_136E94793AD8644E (user_source), INDEX IDX_136E9479233D34C1 (user_target), PRIMARY KEY (user_source, user_target)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE92F8F78 FOREIGN KEY (recipient_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE post_likes ADD CONSTRAINT FK_DED1C2924B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_likes ADD CONSTRAINT FK_DED1C292A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_follows ADD CONSTRAINT FK_136E94793AD8644E FOREIGN KEY (user_source) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_follows ADD CONSTRAINT FK_136E9479233D34C1 FOREIGN KEY (user_target) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAE92F8F78');
        $this->addSql('ALTER TABLE post_likes DROP FOREIGN KEY FK_DED1C2924B89032C');
        $this->addSql('ALTER TABLE post_likes DROP FOREIGN KEY FK_DED1C292A76ED395');
        $this->addSql('ALTER TABLE user_follows DROP FOREIGN KEY FK_136E94793AD8644E');
        $this->addSql('ALTER TABLE user_follows DROP FOREIGN KEY FK_136E9479233D34C1');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE post_likes');
        $this->addSql('DROP TABLE user_follows');
        $this->addSql('ALTER TABLE `user` DROP roles');
    }
}
