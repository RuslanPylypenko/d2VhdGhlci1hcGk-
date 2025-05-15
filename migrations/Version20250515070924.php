<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250515070924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add confirm token';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
        ALTER TABLE subscription
            ADD confirm_token_token VARCHAR(22) DEFAULT NULL,
            ADD confirm_token_expired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
    SQL);

        $this->addSql(<<<'SQL'
        COMMENT ON COLUMN subscription.confirm_token_expired_at
            IS '(DC2Type:datetime_immutable)'
    SQL);
    }
}
