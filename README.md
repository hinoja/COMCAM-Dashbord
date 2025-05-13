<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="logo.jpg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions">
    <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
</p>

# 🌳 Proposition Technique pour la Modernisation de l'Application de Contrôle des Transactions Forestières

## 📝 Résumé du Projet

Cette **application Laravel 11** vise à moderniser la gestion des transactions forestières. Le projet a pour objectifs :

- ✅ Numériser et optimiser la gestion des transactions forestières  
- 📊 Faciliter la prise de décision grâce à des outils de reporting visuels  
- 📜 Assurer le respect de la réglementation forestière  
- 🌲 Améliorer le suivi et la gestion des titres forestiers  
- 🧾 Optimiser la production des rapports annuels  
- 🔍 Renforcer la traçabilité des activités forestières  

### ⚙️ Technologies et Outils

Le projet exploite les fonctionnalités modernes de Laravel :

- Laravel 11
- Eloquent ORM
- Laravel Excel (maatwebsite/excel)
- Middleware & Validation avancée
- Authentification Laravel Breeze ou Jetstream
- Tâches planifiées et files d’attente
- Notifications & gestion des erreurs
- Visualisation des données (Charts.js, Laravel Charts)

---

## 🗺️ I. Contexte

Le projet s’inscrit dans une volonté de moderniser la gestion quotidienne des titres forestiers.  
La **Délégation des Eaux et Forêts**, via le service COMCAM, a identifié le besoin :

- d'une solution numérique plus performante,
- et d'une analyse statistique facilitée.

L’application existante nécessite une refonte complète. La nouvelle version intégrera l’automatisation de la vérification des **dépassements de volumes autorisés**, pour améliorer le suivi et la conformité réglementaire.

---

## 🎯 II. Objectifs

- **Digitalisation accrue** : Gestion numérique complète des transactions
- **Aide à la décision** : Tableaux de bord et visualisations analytiques
- **Conformité réglementaire** : Processus simplifiés
- **Traçabilité des données** : Meilleur archivage et auditabilité
- **Production automatisée** : Rapports périodiques

---

## 👥 III. Acteurs

| Acteur           | Rôle                                                                 |
|------------------|----------------------------------------------------------------------|
| **Administrateur** | Gère la plateforme (utilisateurs, configuration)                    |
| **Utilisateurs internes** | Membres autorisés à enregistrer et gérer les transactions        |

---

## 🧩 IV. Modules Fonctionnels

| #   | Module / Sous-module                                    | Acteur(s)           |
|-----|----------------------------------------------------------|---------------------|
| M001 | **Gestion des Comptes**                                 |                     |
|      | M001-SM001 : Ajout d’un utilisateur                     | Administrateur      |
|      | M001-SM002 : Mise à jour de compte                      | Utilisateur         |
|      | M001-SM003 : Suppression de compte                      | Administrateur      |
|      | M001-SM004 : Réinitialisation de mot de passe           | *                   |
|      | M001-SM005 : Blocage de compte                          | Administrateur      |
| M002 | **Authentification**                                    | *                   |
|      | M002-SM001 : Connexion                                  |                     |
|      | M002-SM002 : Déconnexion                                |                     |
| M003 | **Gestion des Titres**                                  |                     |
|      | M003-SM001 : Filtrer un titre                           | *                   |
|      | M003-SM002 : Enregistrement d’un nouveau titre          | Utilisateur         |
|      | M003-SM003 : Mise à jour des informations               |                     |
|      | M003-SM004 : Suppression d’un titre                     |                     |
| M004 | **Gestion des Opérateurs**                              | Utilisateur         |
|      | M004-SM001 : Enregistrement de sociétés forestières     |                     |
|      | M004-SM002 : Mise à jour des informations               |                     |
|      | M004-SM003 : Suppression des sociétés                   |                     |
| M005 | **Gestion des Transactions**                            | Utilisateur         |
|      | M005-SM001 : Enregistrement des transactions journalières |                   |
|      | M005-SM002 : Mise à jour des transactions               |                     |
|      | M005-SM003 : Détection des dépassements                 |                     |
|      | M005-SM004 : Suppression d’une transaction              |                     |
| M006 | **Gestion des Essences**                                |                     |
|      | M006-SM001 : Ajout des essences                         | Utilisateur         |
|      | M006-SM002 : Modification des essences                  |                     |
|      | M006-SM003 : Suppression des essences                   |                     |
| M009 | **Tableau de bord Dynamique** (graphiques dynamiques)   | Utilisateur         |
| M010 | **Synthèse périodique**                                 | Utilisateur         |

> 🟰 * : Tous les acteurs

---

## ⏳ V. Durée Estimative

La durée estimée pour la réalisation du projet est de **1 à 2 mois** après lancement.  
📅 Objectif de mise en production : **Début Mars**, selon les contraintes du client.

---

## 📦 VI. Livrables

- 🔗 Lien officiel du site web fonctionnel  
- 💻 Code source documenté et commenté  
- 📘 Cahier de recettes (tests de validation)  
- 🧑‍🏫 Séances de formation et prise en main  

---

> 🔒 Ce projet joue un rôle clé dans la digitalisation du secteur forestier et dans le renforcement de la conformité et de la traçabilité.

