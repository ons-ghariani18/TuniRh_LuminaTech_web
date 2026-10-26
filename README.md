# 👥 TuniRh – Plateforme de gestion des ressources humaines

> Application **web (Symfony)** et application **desktop (JavaFX)** partageant la **même base de données MySQL**.
> Projet réalisé par l'équipe **LuminaTech** – Esprit,

![Symfony](https://img.shields.io/badge/Symfony-Web-000000?logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8-777BB4?logo=php&logoColor=white)
![Java](https://img.shields.io/badge/Java-JavaFX-ED8B00?logo=openjdk&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)

---

## 📌 Présentation

TuniRh est une solution de gestion des ressources humaines composée de deux applications :

- une **application desktop** développée en **Java / JavaFX** ;
- une **plateforme web** développée en **Symfony**, obtenue par **reverse engineering** de la base de données de l'application desktop, afin que les deux applications utilisent **la même base MySQL**.

Ce dépôt contient la **partie web (Symfony)**.
Dépôt de la partie desktop : [lien à ajouter]

## 🧩 Modules

| # | Module | Contenu |
|---|---|---|
| 1 | **Utilisateurs & réclamations** | Comptes, rôles et permissions, réclamations |
| 2 | **Offres & candidatures** | Publication d'offres, suivi et évaluation des candidatures |
| 3 | **Événements & sponsors** | Calendrier des événements, participants, sponsors |
| 4 | **Formations & réservations** | Catalogue de formations, réservations, suivi des participations |
| 5 | **Reporting & analytics** | Tableaux de bord, statistiques, export de données |

## 🔌 Intégrations

- **Google Calendar API** (synchronisation des événements)
- [Autres API : à compléter]
- Bundles Symfony externes : [liste à compléter, ex. KnpPaginator, DomPDF, EasyAdmin…]

## 🛠️ Stack technique

| Partie | Technologies |
|---|---|
| Web | Symfony, PHP 8, Twig, Doctrine ORM, Composer |
| Desktop | Java, JavaFX |
| Base de données | MySQL |
| Conception | Diagrammes de classes et de séquence (UML) |
| Méthode | Agile / Scrum (organisation en sprints) |
| Outils | Git / GitHub |

## ⚙️ Installation (partie web)

### Prérequis
- PHP 8.1+ et Composer
- Symfony CLI
- MySQL 5.7+
- Node.js (pour les assets)

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/ons-ghariani18/TuniRh_LuminaTech_web.git
cd TuniRh_LuminaTech_web

# 2. Installer les dépendances
composer install

# 3. Configurer la base de données dans un fichier .env.local
#    DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/tunirh"

# 4. Créer / importer la base (la même que l'application desktop)
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. Lancer le serveur
symfony server:start
```

L'application est accessible sur `http://localhost:8000`.

> ⚠️ Ne publiez jamais de vrais identifiants ni de clés d'API dans le dépôt. Placez-les dans `.env.local` (ignoré par Git).

## 📋 Méthodologie

- Organisation du travail en **sprints** (backlog, planification, revues)
- Conception avec des **diagrammes de classes** et **diagrammes de séquence**
- Reverse engineering de la base MySQL pour générer les entités Symfony



## 📄 Licence

Projet académique – ESPRIT. Licence MIT.
