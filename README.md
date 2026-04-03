# Projet-Web2-AstridMaximilienGabriel

Site web officiel du département informatique de l'EFREI, réalisé dans le cadre d'un projet de première année.

---

## Description

Ce projet consiste en la conception et le développement d'un site web multi-pages dédié au département informatique de l'EFREI. Il présente l'école, ses formations et ses équipes, et intègre un **système de réservation de créneaux** permettant aux étudiants de prendre rendez-vous avec des professeurs.

---

## Équipe

| Nom | Rôle |
|---|---|
| Maximilien | Développeur — header/footer PHP, CSS, carrousel des professeurs |
| Astrid | Développeur — design, passage HTML en PHP, CSS, page Accueil |
| Gabriel | Développeur — partie SQL, JavaScript du calendrier, pages HTML |

---

## Technologies utilisées

- **HTML** — Structure des pages
- **CSS** — Mise en forme et design
- **JavaScript** — Interactions côté client (calendrier, carrousel)
- **PHP** — Logique serveur et gestion des sessions
- **MySQL** — Base de données (réservations et comptes utilisateurs)

---

## Pages du site

| Page | Fichier | Description |
|---|---|---|
| **Accueil** | `Accueil.php` | Page principale de présentation du département |
| **À propos** | `Apropos.php` | Présentation des membres de l'équipe projet |
| **Équipes** | `Equipes.php` | Présentation des professeurs et du personnel |
| **Formations** | `Formations.php` | Détail des formations proposées |
| **Témoignages** | `Temoignages.php` | Retours d'expérience d'étudiants |
| **Contact** | `Contact.php` | Formulaire pour contacter l'EFREI |
| **Calendrier** | `Calendrier1.php` | Réservation de créneaux avec un professeur (accessible depuis Équipes) |
| **Mes réservations** | `MesReservations.php` | Récapitulatif des réservations selon le rôle connecté |
| **Connexion** | `connexion.php` | Authentification par pseudo et mot de passe |

---

## Installation et lancement en local

### Prérequis

- [XAMPP](https://www.apachefriends.org/) / [WAMP](https://www.wampserver.com/) / [MAMP](https://www.mamp.info/) installé sur votre machine
- Un navigateur web

### Étapes

1. **Cloner ou copier le projet** dans le dossier racine de votre serveur local :
   - XAMPP → `C:/xampp/htdocs/`
   - WAMP → `C:/wamp64/www/`
   - MAMP → `/Applications/MAMP/htdocs/`

2. **Démarrer Apache et MySQL** depuis le panneau de contrôle de XAMPP/WAMP/MAMP.

3. **Initialiser la base de données** (voir section dédiée ci-dessous).

4. **Accéder au site** depuis votre navigateur :
   ```
   http://localhost/Projet-de-site-Web-B1-IN/PHP/Accueil.php
   ```

---

## Initialisation de la base de données

Le fichier `init_db.sql` contient tout ce qu'il faut pour créer la base de données, les tables et un compte de test. Il suffit de l'exécuter **une seule fois**.

### Via phpMyAdmin (recommandé)

1. Ouvrir phpMyAdmin dans votre navigateur :
   - XAMPP / WAMP → `http://localhost/phpmyadmin`
   - MAMP → `http://localhost:8888/phpMyAdmin/`

2. Cliquer sur l'onglet **"SQL"** dans la barre du haut (depuis l'accueil, sans sélectionner de base).

3. Copier-coller tout le contenu du fichier `init_db.sql` dans la zone de texte.

4. Cliquer sur **"Exécuter"**.

La base `efrei_rdv` est créée automatiquement avec les tables `membres` et `reservations`.

### Via la ligne de commande

```bash
mysql -u root -p < init_db.sql
```

---

## Base de données

- **Nom :** `efrei_rdv`
- **Système :** MySQL

### Table `membres` — comptes utilisateurs

| Colonne | Type | Description |
|---|---|---|
| `id` | INT | Identifiant auto-incrémenté |
| `pseudo` | VARCHAR(50) | Nom d'utilisateur unique |
| `mdp` | VARCHAR(255) | Mot de passe |
| `role` | ENUM | `etudiant` ou `prof` |
| `created_at` | DATETIME | Date de création du compte |

### Table `reservations` — créneaux réservés

| Colonne | Type | Description |
|---|---|---|
| `id` | INT | Identifiant auto-incrémenté |
| `id_etudiant` | VARCHAR(20) | Pseudo de l'étudiant connecté |
| `professeur` | VARCHAR(100) | Nom du professeur |
| `creneau` | VARCHAR(50) | Heure du créneau (ex: `09:00`) |
| `date_rdv` | DATE | Date du rendez-vous |
| `created_at` | TIMESTAMP | Date d'enregistrement |

### Compte de test inclus dans `init_db.sql`

| Pseudo | Mot de passe | Rôle |
|---|---|---|
| `testuser` | `monmotdepasse` | étudiant |

Pour créer des comptes professeurs, décommenter les lignes correspondantes dans `init_db.sql` avant de l'exécuter.

---

## Fonctionnalités principales

- Navigation entre les pages via un header commun (`header.php`)
- Système de **sessions PHP** pour la gestion des utilisateurs connectés
- **Réservation de créneaux** : en cliquant sur un professeur depuis la page Équipes, l'utilisateur accède à un calendrier interactif pour choisir un créneau. La réservation est enregistrée automatiquement sous le pseudo connecté, sans saisie manuelle.
- **Page "Mes réservations"** : affichage personnalisé selon le rôle — un étudiant voit ses rendez-vous, un professeur voit ses heures de permanence.
- **Carrousel** de présentation des professeurs sur la page Équipes

---

## Repartir de zéro (reset complet)

1. Dans phpMyAdmin, sélectionner la base `efrei_rdv` dans le panneau gauche
2. Onglet **"Opérations"** → **"Supprimer la base de données"** → confirmer
3. Ré-exécuter `init_db.sql` comme décrit dans la section d'initialisation ci-dessus

---

*Projet réalisé dans le cadre de la formation B1 Informatique — EFREI*
