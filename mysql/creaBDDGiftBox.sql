DROP DATABASE IF EXISTS `MyGiftBox`;
CREATE DATABASE MyGiftBox CHARACTER SET utf8;

USE MyGiftBox;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `membre` (
  `idMembre` int(11) NOT NULL AUTO_INCREMENT,
  `mailMembre` text NOT NULL,
  `passwordMembre` text NOT NULL,
  `nomMembre` text NOT NULL,
  `prenomMembre` text NOT NULL,
  `role` int(1) NOT NULL,
  PRIMARY KEY(`idMembre`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

CREATE TABLE IF NOT EXISTS `categorie` (
  `idCategorie` int(11) NOT NULL AUTO_INCREMENT,
  `nomCategorie` text NOT NULL,
  PRIMARY KEY(`idCategorie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

CREATE TABLE IF NOT EXISTS `prestation` (
  `idPrestation` int(11) NOT NULL AUTO_INCREMENT,
  `nomPrestation` text NOT NULL,
  `descr` text NOT NULL,
  `img` text NOT NULL,
  `prix` decimal(5,2) NOT NULL,
  `activation` boolean NOT NULL,
  `idCategorie` int(11) NOT NULL,
  PRIMARY KEY(`idPrestation`),
  FOREIGN KEY(`idCategorie`) REFERENCES `categorie`(`idCategorie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;


CREATE TABLE IF NOT EXISTS `coffret` (
  `idCoffret` int(11) NOT NULL AUTO_INCREMENT,
  `idMembre` int(11) NOT NULL,
  `nomCoffret` text NOT NULL,
  `messageCoffret` text NOT NULL,
  `dateOuvertureCoffret` date NOT NULL,
  `estCree` boolean NOT NULL,
  `estPaye` boolean NOT NULL,
  `estTransmis`boolean NOT NULL,
  `estOuvert` boolean NOT NULL,
  `hasContenuCoffret` boolean NOT NULL,
  `msgRemerciement` text NOT NULL,
  `tokenCoffret` text NOT NULL,
  `tokenCagnotte` text NOT NULL,
  PRIMARY KEY(`idCoffret`),
  FOREIGN KEY(`idMembre`) REFERENCES `membre`(`idMembre`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `contenuCoffret` (
  `idCoffret` int(11) NOT NULL,
  `idPrestation` int(11) NOT NULL,
  `quantite` int(5) NOT NULL,
  PRIMARY KEY(`idCoffret`, `idPrestation`),
  FOREIGN KEY(`idCoffret`) REFERENCES `coffret`(`idCoffret`),
  FOREIGN KEY(`idPrestation`) REFERENCES `prestation`(`idPrestation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `membre` (`idMembre`, `mailMembre`, `passwordMembre`, `nomMembre`, `prenomMembre`, `role`) VALUES
(1, 'visiteur@visiteur.fr', '$2y$10$DYcbBMwkrenrO.kzVtDbPewt7NPCF00MiYerOq8kTKglo5BdEkn1G', 'Visiteur', 'Visiteur', 0),
(2, 'leo@leo.fr', '$2y$10$Ij67Jjn8QqL0oLa2hhgaW..hwa17.3dGnrAkNEskGZ7kBPY89PuSK', 'Galassi', 'Léo', 1),
(3, 'svz@svz.fr', '$2y$10$Kj/pz.UrzATTWZZAOCoyXeHvWVeI4Fq8Ho52SJn/N3xmvZt1Xje2C', 'Rimet', 'Quentin', 1),
(4, 'maeva@maeva.fr', '$2y$10$NV98dGIssJQeUp4HX9AZ0e0B/bmY.KdPJQxQo7GjpnmeL91bki/Ye', 'Butaye', 'Maeva', 1),
(5, 'camille@camille.fr', '$2y$10$whsnOjvjLwr31I6TlxakzOJ/iHC4Tntp/DokW8y4PxolE75/dvKmm', 'Schwarz', 'Camille', 1);

INSERT INTO `categorie` (`idCategorie`, `nomCategorie`) VALUES
(1, 'Attention'),
(2, 'Activité'),
(3, 'Restauration'),
(4, 'Hébergement');

INSERT INTO `prestation` (`idPrestation`, `nomPrestation`, `descr`, `img`, `prix`, `activation`, `idCategorie`) VALUES
(1, 'Champagne', 'Bouteille de champagne + flutes + jeux à gratter', 'champagne.jpg', '20.00', 1, 1),
(2, 'Musique', 'Partitions de piano à 4 mains', 'musique.jpg', '25.00', 1, 1),
(3, 'Exposition', 'Visite guidée de l’exposition ‘REGARDER’ à la galerie Poirel','poirelregarder.jpg', '14.00', 1, 2),
(4, 'Goûter', 'Goûter au FIFNL', 'gouter.jpg', '20.00', 1, 3),
(5, 'Projection', 'Projection courts-métrages au FIFNL', 'film.jpg', '10.00', 1, 2),
(6, 'Bouquet', 'Bouquet de roses et Mots de Marion Renaud', 'rose.jpg', '16.00', 1, 1),
(7, 'Diner Stanislas', 'Diner à La Table du Bon Roi Stanislas (Apéritif /Entrée / Plat / Vin / Dessert / Café / Digestif)', 'bonroi.jpg', '60.00', 1, 3),
(8, 'Origami', 'Baguettes magiques en Origami en buvant un thé', 'origami.jpg', '12.00', 1, 3),
(9, 'Livres', 'Livre bricolage avec petits-enfants + Roman', 'bricolage.jpg', '24.00', 1, 1),
(10, 'Diner  Grand Rue ', 'Diner au Grand’Ru(e) (Apéritif / Entrée / Plat / Vin / Dessert / Café)', 'grandrue.jpg', '59.00', 1, 3),
(11, 'Visite guidée', 'Visite guidée personnalisée de Saint-Epvre jusqu’à Stanislas', 'place.jpg', '11.00', 1, 2),
(12, 'Bijoux', 'Bijoux de manteau + Sous-verre pochette de disque + Lait après-soleil', 'bijoux.jpg', '29.00', 1, 1),
(13, 'Opéra', 'Concert commenté à l’Opéra', 'opera.jpg', '15.00', 1, 2),
(14, 'Thé Hotel de la reine', 'Thé de debriefing au bar de l’Hotel de la reine', 'hotelreine.gif', '5.00', 1, 3),
(15, 'Jeu connaissance', 'Jeu pour faire connaissance', 'connaissance.jpg', '6.00', 1, 2),
(16, 'Diner', 'Diner (Apéritif / Plat / Vin / Dessert / Café)', 'diner.jpg', '40.00', 1, 3),
(17, 'Cadeaux individuels', 'Cadeaux individuels sur le thème de la soirée', 'cadeaux.jpg', '13.00', 1, 1),
(18, 'Animation', 'Activité animée par un intervenant extérieur', 'animateur.jpg', '9.00', 1, 2),
(19, 'Jeu contacts', 'Jeu pour échange de contacts', 'contact.png', '5.00', 1, 2),
(20, 'Cocktail', 'Cocktail de fin de soirée', 'cocktail.jpg', '12.00', 1, 3),
(21, 'Star Wars', 'Star Wars - Le Réveil de la Force. Séance cinéma 3D', 'starwars.jpg', '12.00', 1, 2),
(22, 'Concert', 'Un concert à Nancy', 'concert.jpg', '17.00', 1, 2),
(23, 'Appart Hotel', 'Appart’hôtel Coeur de Ville, en plein centre-ville', 'apparthotel.jpg', '56.00', 1, 4),
(24, 'Hôtel d''Haussonville', 'Hôtel d''Haussonville, au coeur de la Vieille ville à deux pas de la place Stanislas', 'hotel_haussonville_logo.jpg', '169.00', 1, 4),
(25, 'Boite de nuit', 'Discothèque, Boîte tendance avec des soirées à thème & DJ invités', 'boitedenuit.jpg', '32.00', 1, 2),
(26, 'Planètes Laser', 'Laser game : Gilet électronique et pistolet laser comme matériel, vous voilà équipé.', 'laser.jpg', '15.00', 1, 2),
(27, 'Fort Aventure', 'Découvrez Fort Aventure à Bainville-sur-Madon, un site Accropierre unique en Lorraine ! Des Parcours Acrobatiques pour petits et grands, Jeu Mission Aventure, Crypte de Crapahute, Tyrolienne, Saut à l''élastique inversé, Toboggan géant... et bien plus encore.', 'fort.jpg', '25.00', 1, 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;