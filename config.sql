
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS taches_db;
USE taches_db;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  mot_de_passe VARCHAR(255) NOT NULL
);

-- Table des tâches
CREATE TABLE IF NOT EXISTS taches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT NOT NULL,
  titre VARCHAR(255) NOT NULL,
  description TEXT,
  date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);
