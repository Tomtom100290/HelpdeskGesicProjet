<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260603092459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_ticket (id_categorie INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(150) NOT NULL, valeur_bloquant INT NOT NULL, PRIMARY KEY (id_categorie)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE client (id_client INT AUTO_INCREMENT NOT NULL, raison_social VARCHAR(150) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, ville VARCHAR(100) DEFAULT NULL, code_postal VARCHAR(10) DEFAULT NULL, num_tel VARCHAR(20) DEFAULT NULL, email VARCHAR(150) DEFAULT NULL, positionX NUMERIC(10, 7) DEFAULT NULL, positionY NUMERIC(10, 7) DEFAULT NULL, date_creation DATETIME NOT NULL, top_actif TINYINT NOT NULL, PRIMARY KEY (id_client)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE compte_rendu (id_compte_rendu INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, temps_traitement_minutes INT DEFAULT NULL, date_redaction DATETIME NOT NULL, id_ticket INT NOT NULL, id_utilisateur INT NOT NULL, UNIQUE INDEX UNIQ_D39E69D2B197184E (id_ticket), INDEX IDX_D39E69D250EAE44 (id_utilisateur), PRIMARY KEY (id_compte_rendu)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE histo_statut_ticket (id_historique INT AUTO_INCREMENT NOT NULL, date_changement DATETIME NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, id_ticket INT NOT NULL, id_statut_avt INT NOT NULL, id_statut_ap INT NOT NULL, id_utilisateur INT NOT NULL, INDEX IDX_D3E64762B197184E (id_ticket), INDEX IDX_D3E64762519C1F35 (id_statut_avt), INDEX IDX_D3E64762A416DA56 (id_statut_ap), INDEX IDX_D3E6476250EAE44 (id_utilisateur), PRIMARY KEY (id_historique)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE logiciel (id_logiciel INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, fk_type_logiciel VARCHAR(80) DEFAULT NULL, coeff_criticite NUMERIC(3, 1) NOT NULL, top_actif TINYINT NOT NULL, PRIMARY KEY (id_logiciel)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE logiciel_client (id_client_logiciel INT AUTO_INCREMENT NOT NULL, date_installation DATE DEFAULT NULL, version_logiciel VARCHAR(20) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, date_creation DATETIME NOT NULL, id_client INT NOT NULL, id_logiciel INT NOT NULL, INDEX IDX_1A45CC94E173B1B8 (id_client), INDEX IDX_1A45CC947C8B8A09 (id_logiciel), PRIMARY KEY (id_client_logiciel)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id_message INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, top_actif TINYINT NOT NULL, date_envoi DATETIME NOT NULL, id_ticket INT NOT NULL, id_utilisateur INT NOT NULL, id_message_parent INT DEFAULT NULL, INDEX IDX_B6BD307FB197184E (id_ticket), INDEX IDX_B6BD307F50EAE44 (id_utilisateur), INDEX IDX_B6BD307F37CF4046 (id_message_parent), PRIMARY KEY (id_message)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE priorite (id_priorite INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, niveau_criticite INT NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id_priorite)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE statut_ticket (id_statut INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, couleur_lib VARCHAR(7) NOT NULL, top_actif TINYINT NOT NULL, PRIMARY KEY (id_statut)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tache (id_tache INT AUTO_INCREMENT NOT NULL, date_creation DATETIME NOT NULL, libelle VARCHAR(200) NOT NULL, description LONGTEXT DEFAULT NULL, date_realisation DATETIME DEFAULT NULL, statut VARCHAR(255) NOT NULL, id_ticket INT DEFAULT NULL, id_utilisateur_assign INT NOT NULL, id_utilisateur_creat INT NOT NULL, INDEX IDX_93872075B197184E (id_ticket), INDEX IDX_9387207590AD2185 (id_utilisateur_assign), INDEX IDX_938720758BE64456 (id_utilisateur_creat), PRIMARY KEY (id_tache)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ticket (id_ticket INT AUTO_INCREMENT NOT NULL, titre VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, note_priorite INT NOT NULL, niveau_impact VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, date_cloture DATETIME DEFAULT NULL, id_utilisateur INT NOT NULL, id_assigne INT DEFAULT NULL, id_destinataire INT DEFAULT NULL, id_priorite INT NOT NULL, id_statut INT NOT NULL, id_logiciel_client INT NOT NULL, id_categorie INT NOT NULL, INDEX IDX_97A0ADA350EAE44 (id_utilisateur), INDEX IDX_97A0ADA390E462FD (id_assigne), INDEX IDX_97A0ADA3DD688AE0 (id_destinataire), INDEX IDX_97A0ADA3267C6CFD (id_priorite), INDEX IDX_97A0ADA3C3534552 (id_statut), INDEX IDX_97A0ADA3F31B0F1C (id_logiciel_client), INDEX IDX_97A0ADA3C9486A13 (id_categorie), PRIMARY KEY (id_ticket)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (id_user INT AUTO_INCREMENT NOT NULL, nom VARCHAR(80) NOT NULL, prenom VARCHAR(80) NOT NULL, email VARCHAR(150) NOT NULL, entreprise VARCHAR(150) DEFAULT NULL, role VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, top_actif TINYINT NOT NULL, num_tel VARCHAR(20) DEFAULT NULL, id_client INT DEFAULT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), INDEX IDX_1D1C63B3E173B1B8 (id_client), PRIMARY KEY (id_user)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE compte_rendu ADD CONSTRAINT FK_D39E69D2B197184E FOREIGN KEY (id_ticket) REFERENCES ticket (id_ticket)');
        $this->addSql('ALTER TABLE compte_rendu ADD CONSTRAINT FK_D39E69D250EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE histo_statut_ticket ADD CONSTRAINT FK_D3E64762B197184E FOREIGN KEY (id_ticket) REFERENCES ticket (id_ticket)');
        $this->addSql('ALTER TABLE histo_statut_ticket ADD CONSTRAINT FK_D3E64762519C1F35 FOREIGN KEY (id_statut_avt) REFERENCES statut_ticket (id_statut)');
        $this->addSql('ALTER TABLE histo_statut_ticket ADD CONSTRAINT FK_D3E64762A416DA56 FOREIGN KEY (id_statut_ap) REFERENCES statut_ticket (id_statut)');
        $this->addSql('ALTER TABLE histo_statut_ticket ADD CONSTRAINT FK_D3E6476250EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE logiciel_client ADD CONSTRAINT FK_1A45CC94E173B1B8 FOREIGN KEY (id_client) REFERENCES client (id_client)');
        $this->addSql('ALTER TABLE logiciel_client ADD CONSTRAINT FK_1A45CC947C8B8A09 FOREIGN KEY (id_logiciel) REFERENCES logiciel (id_logiciel)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FB197184E FOREIGN KEY (id_ticket) REFERENCES ticket (id_ticket)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F37CF4046 FOREIGN KEY (id_message_parent) REFERENCES message (id_message)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075B197184E FOREIGN KEY (id_ticket) REFERENCES ticket (id_ticket)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_9387207590AD2185 FOREIGN KEY (id_utilisateur_assign) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_938720758BE64456 FOREIGN KEY (id_utilisateur_creat) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA350EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA390E462FD FOREIGN KEY (id_assigne) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3DD688AE0 FOREIGN KEY (id_destinataire) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3267C6CFD FOREIGN KEY (id_priorite) REFERENCES priorite (id_priorite)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3C3534552 FOREIGN KEY (id_statut) REFERENCES statut_ticket (id_statut)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3F31B0F1C FOREIGN KEY (id_logiciel_client) REFERENCES logiciel_client (id_client_logiciel)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3C9486A13 FOREIGN KEY (id_categorie) REFERENCES categorie_ticket (id_categorie)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3E173B1B8 FOREIGN KEY (id_client) REFERENCES client (id_client)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte_rendu DROP FOREIGN KEY FK_D39E69D2B197184E');
        $this->addSql('ALTER TABLE compte_rendu DROP FOREIGN KEY FK_D39E69D250EAE44');
        $this->addSql('ALTER TABLE histo_statut_ticket DROP FOREIGN KEY FK_D3E64762B197184E');
        $this->addSql('ALTER TABLE histo_statut_ticket DROP FOREIGN KEY FK_D3E64762519C1F35');
        $this->addSql('ALTER TABLE histo_statut_ticket DROP FOREIGN KEY FK_D3E64762A416DA56');
        $this->addSql('ALTER TABLE histo_statut_ticket DROP FOREIGN KEY FK_D3E6476250EAE44');
        $this->addSql('ALTER TABLE logiciel_client DROP FOREIGN KEY FK_1A45CC94E173B1B8');
        $this->addSql('ALTER TABLE logiciel_client DROP FOREIGN KEY FK_1A45CC947C8B8A09');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FB197184E');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F50EAE44');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F37CF4046');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075B197184E');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_9387207590AD2185');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_938720758BE64456');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA350EAE44');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA390E462FD');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3DD688AE0');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3267C6CFD');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3C3534552');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3F31B0F1C');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3C9486A13');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3E173B1B8');
        $this->addSql('DROP TABLE categorie_ticket');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE compte_rendu');
        $this->addSql('DROP TABLE histo_statut_ticket');
        $this->addSql('DROP TABLE logiciel');
        $this->addSql('DROP TABLE logiciel_client');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE priorite');
        $this->addSql('DROP TABLE statut_ticket');
        $this->addSql('DROP TABLE tache');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
