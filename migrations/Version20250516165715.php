<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250516165715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table subscriptions, and messenger_messages';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
        CREATE TABLE subscription (
            id SERIAL PRIMARY KEY,
            city VARCHAR(255)      NOT NULL,
            frequency VARCHAR(10)  NOT NULL,
            confirmed BOOLEAN      NOT NULL,
            subscribed BOOLEAN     NOT NULL,
            email VARCHAR(255)     NOT NULL,
            confirm_token VARCHAR(22),
            unsubscribe_token VARCHAR(22),
            CONSTRAINT uq_subscription_email             UNIQUE(email),
            CONSTRAINT uq_subscription_confirm_token     UNIQUE(confirm_token),
            CONSTRAINT uq_subscription_unsubscribe_token UNIQUE(unsubscribe_token)
        );
    SQL);

        $this->addSql(<<<'SQL'
        CREATE INDEX idx_subscription_city ON subscription(city);
    SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (
                id BIGSERIAL NOT NULL, 
                body TEXT NOT NULL, 
                headers TEXT NOT NULL, 
                queue_name VARCHAR(190) NOT NULL, 
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id)
                                            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.available_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
                BEGIN
                    PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql(<<<'SQL'
            DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();
        SQL);
    }
}
