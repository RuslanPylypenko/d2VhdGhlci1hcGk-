<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250515172358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add unique indexes, add unsubscribe_token';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
        ALTER TABLE subscription
            ADD COLUMN subscribed BOOLEAN NOT NULL,
            ADD COLUMN unsubscribe_token_token VARCHAR(22),
            ADD CONSTRAINT UNIQ_A3C664D3E7927C74 UNIQUE (email),
            ADD CONSTRAINT UNIQ_A3C664D3F464BC96 UNIQUE (confirm_token_token),
            ADD CONSTRAINT UNIQ_A3C664D36E7ACDB UNIQUE (unsubscribe_token_token)
    SQL);
    }
}
