<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250515195552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add index to city';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE INDEX city ON subscription (city)
        SQL);
    }
}
