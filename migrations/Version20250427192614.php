<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427192614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id SERIAL NOT NULL, restaurant_id INT NOT NULL, guest_number SMALLINT NOT NULL, order_date DATE NOT NULL, order_hour TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, allergy VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E00CEDDEB1E7706E ON booking (restaurant_id)');
        $this->addSql('COMMENT ON COLUMN booking.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN booking.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, title VARCHAR(64) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN category.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN category.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE category_menu (category_id INT NOT NULL, menu_id INT NOT NULL, PRIMARY KEY(category_id, menu_id))');
        $this->addSql('CREATE INDEX IDX_F69E40D412469DE2 ON category_menu (category_id)');
        $this->addSql('CREATE INDEX IDX_F69E40D4CCD7E912 ON category_menu (menu_id)');
        $this->addSql('CREATE TABLE food (id SERIAL NOT NULL, title VARCHAR(64) NOT NULL, description TEXT NOT NULL, price SMALLINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN food.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN food.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE food_category (food_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(food_id, category_id))');
        $this->addSql('CREATE INDEX IDX_2E013E83BA8E87C4 ON food_category (food_id)');
        $this->addSql('CREATE INDEX IDX_2E013E8312469DE2 ON food_category (category_id)');
        $this->addSql('CREATE TABLE menu (id SERIAL NOT NULL, restaurant_id INT NOT NULL, title VARCHAR(64) NOT NULL, description TEXT NOT NULL, price SMALLINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7D053A93B1E7706E ON menu (restaurant_id)');
        $this->addSql('COMMENT ON COLUMN menu.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN menu.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE picture (id SERIAL NOT NULL, restaurant_id INT NOT NULL, title VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_16DB4F89B1E7706E ON picture (restaurant_id)');
        $this->addSql('COMMENT ON COLUMN picture.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN picture.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE restaurant (id SERIAL NOT NULL, name VARCHAR(32) NOT NULL, description TEXT NOT NULL, am_opening_time JSON NOT NULL, pm_opening_time JSON NOT NULL, max_guest SMALLINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN restaurant.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN restaurant.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, api_token VARCHAR(255) NOT NULL, first_name VARCHAR(32) NOT NULL, last_name VARCHAR(64) NOT NULL, guest_number SMALLINT DEFAULT NULL, allergy VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_menu ADD CONSTRAINT FK_F69E40D412469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE category_menu ADD CONSTRAINT FK_F69E40D4CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E83BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE food_category ADD CONSTRAINT FK_2E013E8312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDEB1E7706E');
        $this->addSql('ALTER TABLE category_menu DROP CONSTRAINT FK_F69E40D412469DE2');
        $this->addSql('ALTER TABLE category_menu DROP CONSTRAINT FK_F69E40D4CCD7E912');
        $this->addSql('ALTER TABLE food_category DROP CONSTRAINT FK_2E013E83BA8E87C4');
        $this->addSql('ALTER TABLE food_category DROP CONSTRAINT FK_2E013E8312469DE2');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT FK_7D053A93B1E7706E');
        $this->addSql('ALTER TABLE picture DROP CONSTRAINT FK_16DB4F89B1E7706E');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_menu');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE food_category');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP TABLE "user"');
    }
}
