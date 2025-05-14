<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250514133918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added subscription table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE subscription (id SERIAL NOT NULL, city VARCHAR(255) NOT NULL, frequency VARCHAR(10) NOT NULL, confirmed BOOLEAN NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
    }
}
