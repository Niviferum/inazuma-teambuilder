# Inazuma Eleven Team Builder

Application Laravel de gestion d'équipes Inazuma Eleven avec système de formation tactique.

## Fonctionnalités

### Gestion des joueurs
- Base de données de 5407 joueurs importés depuis un fichier CSV
- Affichage en grille avec pagination (20 joueurs par page)
- Filtres par position (GK/DF/MF/FW), élément (Feu/Vent/Montagne/Forêt) et recherche textuelle
- Fiche détaillée de chaque joueur avec :
  - Informations générales (nom, surnom, position, élément, équipe d'origine)
  - Graphique radar des 7 statistiques (kick, control, technique, intelligence, pressure, physical, agility)
  - Liste des 4 techniques spéciales
  - Backgrounds dynamiques selon l'élément du joueur

### Joueurs custom
- Création de joueurs personnalisés avec upload d'image
- Limitation à 680 points de statistiques totales
- Privés : visibles uniquement par leur créateur
- Édition et suppression réservées au créateur

### Gestion des équipes
- Création d'équipes avec nom et description
- Roster limité à 11 joueurs sur le terrain
- Vérification automatique de la présence d'un gardien de but

### Système de formation tactique
- 3 formations disponibles : 4-3-3, 4-4-2, 3-5-2
- Terrain visuel avec placement des joueurs
- Assignation par clic sur les emplacements
- Sauvegarde de la formation par équipe
- Seuls les joueurs sur le terrain (non banc) peuvent être assignés

### Système d'authentification
- Inscription/Connexion via Laravel Breeze
- Chaque utilisateur gère ses propres équipes
- Les joueurs custom sont privés à leur créateur

## Prérequis

- PHP 8.4+
- Composer
- Node.js et npm
- SQLite

## Installation

### 1. Cloner le projet
```bash
git clone <repository-url>
cd inazuma-team-builder
```

### 2. Installer les dépendances
```bash
composer install
npm install
```

### 3. Configuration de l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

Créer la base de données SQLite :
```bash
touch database/database.sqlite
```

### 4. Migrations et import des données
```bash
php artisan migrate
php artisan db:seed
```

Le seeder importe automatiquement les 5407 joueurs depuis `database/seeders/players.csv`.

### 5. Créer le lien de stockage pour les images
```bash
php artisan storage:link
```

### 6. Compiler les assets
```bash
npm run dev
```

Dans un terminal séparé, lancer le serveur :
```bash
php artisan serve
```

L'application est accessible sur `http://127.0.0.1:8000`

## Utilisation

### Page d'accueil
- Affiche tous les joueurs disponibles (base de données + joueurs custom publics)
- Utilisez les filtres pour rechercher par position, élément ou nom
- Cliquez sur un joueur pour voir sa fiche détaillée

### Créer un compte
1. Cliquez sur "Register" dans le header
2. Remplissez le formulaire d'inscription
3. Vous êtes automatiquement connecté

### Créer un joueur custom
1. Connectez-vous
2. Allez sur "Players" puis "Create Player"
3. Remplissez le formulaire (nom, position, élément, statistiques)
4. Les statistiques totales ne doivent pas dépasser 680 points
5. Uploadez une image (optionnel)
6. Le joueur est créé et visible uniquement par vous

### Créer une équipe
1. Connectez-vous
2. Allez sur "My Teams"
3. Cliquez sur "Create Team"
4. Donnez un nom et une description
5. L'équipe est créée

### Ajouter des joueurs à une équipe
1. Allez sur "My Teams" et sélectionnez une équipe
2. Les joueurs sont ajoutés au banc par défaut (maximum 5)
3. Utilisez la barre de recherche pour trouver des joueurs
4. Cliquez sur "Add" pour ajouter un joueur

### Gérer le terrain et le banc
1. Sur la page de votre équipe, vous aurez une section pitch (une autre section bench viendra par la suite du développement)
2. Utilisez les boutons pour déplacer les joueurs 
3. Bouton rouge "Remove" : retirer complètement le joueur de l'équipe

### Définir une formation tactique
1. Sur la page de votre équipe, cliquez sur "Formation"
2. Sélectionnez une formation (4-3-3, 4-4-2, 3-5-2)
3. Cliquez sur un emplacement vide sur le terrain
4. Sélectionnez un joueur parmi ceux sur le terrain
5. Le joueur apparaît à sa position
6. Cliquez sur "Save Formation" pour enregistrer

### Éditer/Supprimer
- Joueurs custom : seul le créateur peut éditer/supprimer
- Équipes : seul le propriétaire peut éditer/supprimer
- Boutons disponibles sur les pages de détail

## Structure technique

### Technologies
- Laravel 12.49.0
- PHP 8.4.1
- SQLite
- Tailwind CSS (via Vite)
- Chart.js (pour les graphiques radar)

### Base de données
- `users` : utilisateurs
- `players` : tous les joueurs (base + custom)
- `teams` : équipes des utilisateurs
- `player_team` : table pivot avec colonnes :
  - `quantity` : toujours à 1 (héritage ancien système)
  - `formation_position` : position 1-11 sur le terrain
  - `formation` : nom de la formation (4-3-3, 4-4-2, 3-5-2)

### Fichiers CSS personnalisés
- `resources/css/inazuma.css` : styles custom de l'application
- Backgrounds animés selon l'élément (Feu, Vent, Forêt, Montagne)
- Coins coupés sur les cartes
- Animations fadeIn sur les éléments

### Routes principales
- `/` : page d'accueil (liste des joueurs)
- `/players` : liste des joueurs
- `/players/{id}` : détail d'un joueur
- `/players/create` : créer un joueur custom (auth requis)
- `/teams` : mes équipes (auth requis)
- `/teams/{id}` : détail équipe avec roster (auth requis)
- `/teams/{id}/formation` : gestion formation (auth requis)

## Données de test

Un compte de test peut être créé via l'inscription standard. Les 5407 joueurs de la base sont disponibles pour tous les utilisateurs.

## Problèmes connus

- Le système de quantity dans player_team est un héritage et est toujours fixé à 1
- Les joueurs doivent d'abord être sur le terrain pour être assignés à une formation

## Développement

Pour le développement avec hot-reload :
```bash
npm run dev
php artisan serve
```
