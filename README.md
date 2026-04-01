# 🎓 Projet-Web2-AstridMaximilienGabriel

Site web officiel du département informatique de l'EFREI, réalisé dans le cadre d'un projet de première année.

---

## 📋 Description

Ce projet consiste en la conception et le développement d'un site web multi-pages dédié au département informatique de l'EFREI. Il présente l'école, ses formations et ses équipes, et intègre un **système de réservation de créneaux** permettant aux étudiants de prendre rendez-vous avec des professeurs.

---

## 👥 Équipe

| Nom | Rôle |
|---|---|
| Maximilien | Développeur |
| Astrid | Développeur |
| Gabriel | Développeur |

---

## 🛠️ Technologies utilisées

- **HTML** — Structure des pages
- **CSS** — Mise en forme et design
- **JavaScript** — Interactions côté client
- **PHP** — Logique serveur et gestion des sessions
- **MySQL** — Base de données

---

## 📁 Pages du site

| Page | Description |
|---|---|
| **Accueil** | Page principale de présentation du département |
| **À propos** | Informations générales sur l'EFREI |
| **Équipes** | Présentation des professeurs et du personnel |
| **Formations** | Détail des formations proposées |
| **Témoignages** | Retours d'expérience d'étudiants |
| **Contact** | Formulaire pour contacter l'EFREI |
| **Calendrier** | Réservation de créneaux avec un professeur (accessible depuis la page Équipes) |

---

## ⚙️ Installation et lancement en local

### Prérequis

- [XAMPP](https://www.apachefriends.org/) / [WAMP](https://www.wampserver.com/) / [MAMP](https://www.mamp.info/) installé sur votre machine
- Un navigateur web

### Étapes

1. **Cloner ou copier le projet** dans le dossier racine de votre serveur local :
   - XAMPP → `C:/xampp/htdocs/`
   - WAMP → `C:/wamp64/www/`
   - MAMP → `/Applications/MAMP/htdocs/`

2. **Démarrer Apache et MySQL** depuis le panneau de contrôle de XAMPP/WAMP/MAMP.

3. **Importer la base de données** :
   - Ouvrir [phpMyAdmin](http://localhost/phpmyadmin)
   - Créer une base de données nommée `init_db`
   - Importer le fichier SQL fourni dans le projet (`init_db.sql`)

4. **Accéder au site** depuis votre navigateur :
   ```
   http://localhost/Projet-de-site-Web-B1-IN/
   ```

---

## 🗄️ Base de données

- **Nom :** `init_db`
- **Système :** MySQL
- La base de données gère les sessions utilisateurs ainsi que les créneaux de réservation avec les professeurs.

---

## ✨ Fonctionnalités principales

- Navigation entre les pages via des liens HTML
- Système de **sessions PHP** pour la gestion des utilisateurs connectés
- **Réservation de créneaux** : en cliquant sur un professeur depuis la page Équipes, l'utilisateur accède à un calendrier interactif pour choisir un créneau de rendez-vous

---

*Projet réalisé dans le cadre de la formation B1 Informatique — EFREI*
