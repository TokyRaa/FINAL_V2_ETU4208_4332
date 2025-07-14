CREATE TABLE membre (
  id_membre INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  date_naissance DATE NOT NULL,
  genre ENUM('M','F','Autre') NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  ville VARCHAR(100),
  mdp VARCHAR(255) NOT NULL,
  image_profil VARCHAR(255)
);

CREATE TABLE categorie_objet (
  id_categorie INT AUTO_INCREMENT PRIMARY KEY,
  nom_categorie VARCHAR(100) NOT NULL
);

CREATE TABLE objet (
  id_objet INT AUTO_INCREMENT PRIMARY KEY,
  nom_objet VARCHAR(150) NOT NULL,
  id_categorie INT NOT NULL,
  id_membre INT NOT NULL,
  FOREIGN KEY (id_categorie) REFERENCES categorie_objet(id_categorie),
  FOREIGN KEY (id_membre)    REFERENCES membre(id_membre)
);

CREATE TABLE images_objet (
  id_image INT AUTO_INCREMENT PRIMARY KEY,
  id_objet INT NOT NULL,
  nom_image VARCHAR(255) NOT NULL,
  FOREIGN KEY (id_objet) REFERENCES objet(id_objet)
);

CREATE TABLE emprunt (
  id_emprunt INT AUTO_INCREMENT PRIMARY KEY,
  id_objet   INT NOT NULL,
  id_membre  INT NOT NULL,
  date_emprunt DATE NOT NULL,
  date_retour  DATE,
  FOREIGN KEY (id_objet)  REFERENCES objet(id_objet),
  FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);

CREATE TABLE emprunt (
  id_emprunt INT AUTO_INCREMENT PRIMARY KEY,
  id_objet INT,
  id_membre INT,
  date_emprunt DATETIME DEFAULT CURRENT_TIMESTAMP,
  date_retour DATETIME,
  FOREIGN KEY (id_objet) REFERENCES objet(id_objet),
  FOREIGN KEY (id_membre) REFERENCES membre(id_membre)
);

INSERT INTO membre (nom, date_naissance, genre, email, ville, mdp, image_profil) VALUES
('Alice Dupont','1990-05-12','F','alice@example.com','Paris', SHA2('passAlice',256), 'alice.jpg'),
('Bob Martin','1985-11-03','M','bob@example.com','Lyon',   SHA2('passBob',256),   'bob.png'),
('Chloé Nguyen','1992-07-21','F','chloe@example.com','Toulouse',SHA2('passChloe',256),'chloe.jpg'),
('David Rossi','1988-02-14','M','david@example.com','Marseille',SHA2('passDavid',256),'david.png');

INSERT INTO categorie_objet (nom_categorie) VALUES
('Esthétique'),('Bricolage'),('Mécanique'),('Cuisine');

INSERT INTO images_objet (id_objet, nom_image) VALUES
(1,'maquillage1.jpg'),
(1,'maquillage2.jpg'),
(2,'lampe1.png'),
... ;


INSERT INTO objet (nom_objet, id_categorie, id_membre) VALUES
('Trousse de maquillage', 1, 1),
('Palette de fards à paupières', 1, 1),
('Miroir de poche', 1, 1),
('Pince à épiler', 1, 1),
('Lampe de poche', 2, 1),
('Perceuse sans fil', 2, 1),
('Tournevis multi-embouts',2, 1),
('Clé à molette', 3, 1),
('Crics hydraulique',3, 1),
('Robot de cuisine', 4, 1),
('Vernis à ongles',1, 2),
('Sèche-cheveux', 1, 2),
('Fer à lisser', 1, 2),
('Niveau à bulle',2, 2),
('Scie sauteuse', 2, 2),
('Tournevis électrique',2, 2),
('Clé dynamométrique',3, 2),
('Compresseur d’air', 3, 2),
('Mixeur plongeant', 4, 2),
('Balance de cuisine',4, 2),
('Crayon à sourcils',1, 3),
('Fond de teint', 1, 3),
('Brosse à cheveux', 1, 3),
('Marteau', 2, 3),
('Pistolet à colle', 2, 3),
('Cutter', 2, 3),
('Clé anglaise', 3, 3),
('Pompe à vélo', 3, 3),
('Grille-pain', 4, 3),
('Cocotte-minute',4, 3),
('Rouge à lèvres', 1, 4),
('Crème hydratante', 1, 4),
('Lisseur de cheveux', 1, 4),
('Perceuse à percussion', 2, 4),
('Pince à sertir', 2, 4),
('Clé à pipe', 3, 4),
('Pneu de secours', 3, 4),
('Friteuse sans huile', 4, 4),
('Robot pâtissier', 4, 4),
('Casserole inox', 4, 4);

INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour) VALUES
( 3,  2, '2025-06-10', '2025-06-15'),  
( 5,  1, '2025-07-01', NULL),         
(11,  4, '2025-05-20', '2025-05-25'), 
(18,  3, '2025-04-15', '2025-04-20'),  
(23,  1, '2025-06-30', NULL),          
(29,  2, '2025-07-05', '2025-07-08'), 
(33,  3, '2025-06-01', '2025-06-03'),  
(37,  4, '2025-06-25', NULL),          
(40,  2, '2025-05-10', '2025-05-12'),  
(14,  3, '2025-07-10', NULL)           
;

ALTER TABLE objet ADD abime TINYINT(1) DEFAULT 0;


