<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260605092333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE impact (id_impact INT AUTO_INCREMENT NOT NULL, niveau VARCHAR(10) NOT NULL, libelle VARCHAR(100) NOT NULL, prompt LONGTEXT NOT NULL, note SMALLINT NOT NULL, UNIQUE INDEX UNIQ_C409C0074BDFF36B (niveau), PRIMARY KEY (id_impact)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE urgence (id_urgence INT AUTO_INCREMENT NOT NULL, niveau VARCHAR(10) NOT NULL, libelle VARCHAR(100) NOT NULL, prompt LONGTEXT NOT NULL, note SMALLINT NOT NULL, UNIQUE INDEX UNIQ_737D6BCD4BDFF36B (niveau), PRIMARY KEY (id_urgence)) DEFAULT CHARACTER SET utf8mb4');
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
        $this->addSql('DROP INDEX IDX_97A0ADA3267C6CFD ON ticket');
        $this->addSql('ALTER TABLE ticket ADD priorite_calculee SMALLINT NOT NULL, ADD id_impact INT NOT NULL, ADD id_urgence INT NOT NULL, DROP note_priorite, DROP niveau_impact, DROP id_priorite');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA350EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA390E462FD FOREIGN KEY (id_assigne) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3DD688AE0 FOREIGN KEY (id_destinataire) REFERENCES utilisateur (id_user)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3E23E75EA FOREIGN KEY (id_impact) REFERENCES impact (id_impact)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3ADE0C2BD FOREIGN KEY (id_urgence) REFERENCES urgence (id_urgence)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3C3534552 FOREIGN KEY (id_statut) REFERENCES statut_ticket (id_statut)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3F31B0F1C FOREIGN KEY (id_logiciel_client) REFERENCES logiciel_client (id_client_logiciel)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3C9486A13 FOREIGN KEY (id_categorie) REFERENCES categorie_ticket (id_categorie)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3E23E75EA ON ticket (id_impact)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3ADE0C2BD ON ticket (id_urgence)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3E173B1B8 FOREIGN KEY (id_client) REFERENCES client (id_client)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE impact');
        $this->addSql('DROP TABLE urgence');
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
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3E23E75EA');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3ADE0C2BD');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3C3534552');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3F31B0F1C');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3C9486A13');
        $this->addSql('DROP INDEX IDX_97A0ADA3E23E75EA ON ticket');
        $this->addSql('DROP INDEX IDX_97A0ADA3ADE0C2BD ON ticket');
        $this->addSql('ALTER TABLE ticket ADD note_priorite INT NOT NULL, ADD niveau_impact VARCHAR(255) NOT NULL, ADD id_priorite INT NOT NULL, DROP priorite_calculee, DROP id_impact, DROP id_urgence');
        $this->addSql('CREATE INDEX IDX_97A0ADA3267C6CFD ON ticket (id_priorite)');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3E173B1B8');
    }
}
