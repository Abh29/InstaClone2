<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325210243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE country_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE post_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE country (id INT NOT NULL, name VARCHAR(255) NOT NULL, flag VARCHAR(255) DEFAULT NULL, language VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE post (id INT NOT NULL, country_id INT DEFAULT NULL, profile_id INT NOT NULL, caption VARCHAR(255) NOT NULL, picture VARCHAR(255) NOT NULL, thumbnail VARCHAR(255) NOT NULL, visibility INT NOT NULL, city VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF92F3E70 ON post (country_id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DCCFA12B8 ON post (profile_id)');
        $this->addSql('COMMENT ON COLUMN post.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE post_profile (post_id INT NOT NULL, profile_id INT NOT NULL, PRIMARY KEY(post_id, profile_id))');
        $this->addSql('CREATE INDEX IDX_BCBF1D3C4B89032C ON post_profile (post_id)');
        $this->addSql('CREATE INDEX IDX_BCBF1D3CCCFA12B8 ON post_profile (profile_id)');
        $this->addSql('CREATE TABLE profile (id INT NOT NULL, app_user_id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, birthdate DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8157AA0F4A3353D8 ON profile (app_user_id)');
        $this->addSql('CREATE TABLE profile_profile (profile_source INT NOT NULL, profile_target INT NOT NULL, PRIMARY KEY(profile_source, profile_target))');
        $this->addSql('CREATE INDEX IDX_52E9749337A01814 ON profile_profile (profile_source)');
        $this->addSql('CREATE INDEX IDX_52E974932E45489B ON profile_profile (profile_target)');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, user_name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64924A232CF ON "user" (user_name)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post_profile ADD CONSTRAINT FK_BCBF1D3C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post_profile ADD CONSTRAINT FK_BCBF1D3CCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F4A3353D8 FOREIGN KEY (app_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE profile_profile ADD CONSTRAINT FK_52E9749337A01814 FOREIGN KEY (profile_source) REFERENCES profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE profile_profile ADD CONSTRAINT FK_52E974932E45489B FOREIGN KEY (profile_target) REFERENCES profile (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE country_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE post_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE profile_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DF92F3E70');
        $this->addSql('ALTER TABLE post DROP CONSTRAINT FK_5A8A6C8DCCFA12B8');
        $this->addSql('ALTER TABLE post_profile DROP CONSTRAINT FK_BCBF1D3C4B89032C');
        $this->addSql('ALTER TABLE post_profile DROP CONSTRAINT FK_BCBF1D3CCCFA12B8');
        $this->addSql('ALTER TABLE profile DROP CONSTRAINT FK_8157AA0F4A3353D8');
        $this->addSql('ALTER TABLE profile_profile DROP CONSTRAINT FK_52E9749337A01814');
        $this->addSql('ALTER TABLE profile_profile DROP CONSTRAINT FK_52E974932E45489B');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_profile');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE profile_profile');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
