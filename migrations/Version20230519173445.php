<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230519173445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, name_conversation VARCHAR(50) NOT NULL, color_conversation VARCHAR(50) NOT NULL, url_avatar_conversation VARCHAR(100) NOT NULL, type_conversation VARCHAR(50) NOT NULL, status_conversation TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customization (id INT AUTO_INCREMENT NOT NULL, conversation_customization_id INT NOT NULL, user_customization_id INT NOT NULL, message_color_customization VARCHAR(50) NOT NULL, user_nickname_customization VARCHAR(50) NOT NULL, user_status_customization TINYINT(1) NOT NULL, is_admin_customization TINYINT(1) NOT NULL, INDEX IDX_AB0369C669550C9C (conversation_customization_id), INDEX IDX_AB0369C6A340ECE0 (user_customization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, conversation_message_id INT NOT NULL, user_message_id INT NOT NULL, content_message LONGTEXT NOT NULL, hour_date_message DATETIME NOT NULL, status_message TINYINT(1) NOT NULL, INDEX IDX_B6BD307F42664F2B (conversation_message_id), INDEX IDX_B6BD307FF41DD5C5 (user_message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE public_keys (id INT AUTO_INCREMENT NOT NULL, conversation_public_keys_id INT NOT NULL, user_public_keys_id INT NOT NULL, key_publickeys LONGTEXT NOT NULL, INDEX IDX_CFD3063D401AF524 (conversation_public_keys_id), UNIQUE INDEX UNIQ_CFD3063D68F7488D (user_public_keys_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name_user VARCHAR(50) NOT NULL, last_name_user VARCHAR(50) NOT NULL, nickname_user VARCHAR(50) NOT NULL, birthday_user DATE NOT NULL, password_user VARCHAR(100) NOT NULL, avatar_url_user VARCHAR(100) NOT NULL, status_user TINYINT(1) NOT NULL, font_size_user VARCHAR(50) NOT NULL, public_key_user LONGTEXT NOT NULL, private_key_user LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_conversation (user_id INT NOT NULL, conversation_id INT NOT NULL, INDEX IDX_A425AEBA76ED395 (user_id), INDEX IDX_A425AEB9AC0396 (conversation_id), PRIMARY KEY(user_id, conversation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customization ADD CONSTRAINT FK_AB0369C669550C9C FOREIGN KEY (conversation_customization_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE customization ADD CONSTRAINT FK_AB0369C6A340ECE0 FOREIGN KEY (user_customization_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F42664F2B FOREIGN KEY (conversation_message_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF41DD5C5 FOREIGN KEY (user_message_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE public_keys ADD CONSTRAINT FK_CFD3063D401AF524 FOREIGN KEY (conversation_public_keys_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE public_keys ADD CONSTRAINT FK_CFD3063D68F7488D FOREIGN KEY (user_public_keys_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_conversation ADD CONSTRAINT FK_A425AEBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_conversation ADD CONSTRAINT FK_A425AEB9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customization DROP FOREIGN KEY FK_AB0369C669550C9C');
        $this->addSql('ALTER TABLE customization DROP FOREIGN KEY FK_AB0369C6A340ECE0');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F42664F2B');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF41DD5C5');
        $this->addSql('ALTER TABLE public_keys DROP FOREIGN KEY FK_CFD3063D401AF524');
        $this->addSql('ALTER TABLE public_keys DROP FOREIGN KEY FK_CFD3063D68F7488D');
        $this->addSql('ALTER TABLE user_conversation DROP FOREIGN KEY FK_A425AEBA76ED395');
        $this->addSql('ALTER TABLE user_conversation DROP FOREIGN KEY FK_A425AEB9AC0396');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80233D34C1');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE customization');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE public_keys');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_conversation');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
