<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250821080050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create notification email table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE notification_email (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, uuid VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL --(DC2Type:datetime_immutable)
        , updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL --(DC2Type:datetime_immutable)
        , status VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, recipients CLOB NOT NULL --(DC2Type:json)
        )');
        $this->addSql('CREATE INDEX idx_uuid ON notification_email (uuid)');
        $this->addSql('CREATE INDEX idx_status ON notification_email (status)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE notification_email');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
