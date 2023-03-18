<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230318190000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_8d93d649e7927c74');
        $this->addSql('ALTER TABLE "user" ALTER email DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER phone SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649444F97DD ON "user" (phone)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_8D93D649444F97DD');
        $this->addSql('ALTER TABLE "user" ALTER email SET NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER phone DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');
    }
}
