USE HelpdeskGesicBDD;

INSERT INTO `client` (`id_client`, `raison_social`, `adresse`, `ville`, `code_postal`, `num_tel`, `email`, `positionX`, `positionY`, `date_creation`, `top_actif`) VALUES
(1, 'Urcoopa', '36 Avenue Du Grand Piton, Cambaie', 'Saint-Paul', '97460', '0262453710', 'urcoopa@urcoopa.fr', NULL, NULL, '2026-06-09 22:29:33', 1),
(2, 'Proval', '6 Rue Claude Chappe, ZI de Cambaie', 'Le Port', '97420', '0262453700', 'contact@proval.re', NULL, NULL, '2026-06-09 22:29:33', 1),
(3, 'Couvée d''or', '36 Rue Montaigne, Les Trois Mares', 'Le Tampon', '97430', '0262395574', 'sa.couvee.d.or@wanadoo.fr', NULL, NULL, '2026-06-09 22:29:33', 1),
(4, 'Nutrima Production', 'ZAC 2000, 8 Rue Claude Chappe', 'Le Port', '97420', '0262453705', 'contact@nutrima.fr', NULL, NULL, '2026-06-09 22:29:33', 1),
(5, 'Petfood Run', 'ZI de Cambaie, CS 70003', 'Saint-Paul', '97460', '0262453707', 'petfoodrun@petfoodrun.fr', NULL, NULL, '2026-06-09 22:29:33', 1),
(6, 'Gésic', '21b Rue des Baies Roses, Cambaie', 'Saint-Paul', '97419', '0262457328', 'contact@gesic.fr', NULL, NULL, '2026-06-09 22:29:33', 1),
(7, 'PLR', 'Zone Industrielle n°1', 'Le Port', '97420', '0262551234', 'contact@plr-transport.re', NULL, NULL, '2026-06-09 22:29:33', 1),
(8, 'Cogedal', 'Zone Industrielle n°2, BP 188', 'Saint-Pierre', '97410', '0262961656', 'contact@cogedal.re', NULL, NULL, '2026-06-09 22:29:33', 1);

INSERT INTO `impact` (`id_impact`, `niveau`, `libelle`, `prompt`, `note`) VALUES
(1, 'I1', 'Majeur', 'Toute l''entreprise ou service(s) critiques', 4),
(2, 'I2', 'Important', 'Un département entier', 3),
(3, 'I3', 'Modéré', 'Une équipe ou plusieurs Utilisateurs', 2),
(4, 'I4', 'Mineur', 'Un seul Utilisateur', 1);

INSERT INTO `urgence` (`id_urgence`, `niveau`, `libelle`, `prompt`, `note`) VALUES
(1, 'U1', 'Critique métier', 'Production arrêtée, risque financier important', 17),
(2, 'U2', 'Immédiat', 'Fonction essentiel bloquée', 4),
(3, 'U3', 'À traiter rapidement', 'Fonction secondaire indisponible', 3),
(4, 'U4', 'À traiter prochainement', 'Demande de confort ou d''amélioration', 2),
(5, 'U5', 'Peut attendre', 'Non urgente, à planifier', 1);

INSERT INTO `utilisateur` (`nom`, `prenom`, `email`, `role`, `mot_de_passe`, `date_creation`, `top_actif`, `num_tel`, `id_client`) VALUES
('Tang', 'Eric', 'eric.tang@gesic.fr', 'ROLE_DEVELOPPEUR', '$2y$13$JAjJVpHbjpmF2Gthq3yVhOZaxl12I.GVYxT1pBHOb/w/SVYOtgoSS', '2026-06-09 19:17:57', 1, '0689556655', 6),
('Erick', 'Grondin', 'admin@admin.fr', 'ROLE_DEVELOPPEUR', '$2y$13$JAjJVpHbjpmF2Gthq3yVhOZaxl12I.GVYxT1pBHOb/w/SVYOtgoSS', '2026-06-18 07:41:29', 0, '0699332211', 6),
('Jean', 'dupont', 'jd@jd.fr', 'ROLE_CLIENT', '$2y$13$xyZqzPnhDzcqID3Klb9lzO0YA45Q3kUKv1GzT4Op0XYFA6H9gmzEm', '2026-06-18 09:48:08', 1, '0265998877', 8),
('jean', 'blond', 'jean@client.fr', 'ROLE_CLIENT', '$2y$13$o3RLDkXaL9dlQ0sZ1SPvGuufmekJnhra7lNEUEJEiokI0GA0g908y', '2026-06-24 05:35:58', 1, '0699332211', 2),
('Fontaine', 'Arnauld', 'arnauld.fontaine@gesic.fr', 'ROLE_DEVELOPPEUR', '$2y$13$XhBUGy7bOhELrIjZK6N7CuCFXKqghEQtt.6vP.p/AgipSgKMuN2j2', '2026-06-26 11:31:27', 1, '0666998877', 6);