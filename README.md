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
- 📥📤 Permettre l'import et l'export des données via des fichiers Excel, y compris pour les opérations de maintenance et de sauvegarde  

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

## 🚦 Démarrage du Projet : Gestion des Données Initiales
Pour démarrer à partir d'une base de données existante, il est **impératif de préparer un fichier Excel** contenant la liste des sociétés forestières.  
> **Étapes recommandées :**  
> 1. **Charger le fichier Excel** avec les sociétés existantes.  
> 2. **Vérifier et compléter la liste des sociétés** avant d'importer les titres et transactions.

> ℹ️ **Remarque :**  
> La liste des essences est automatiquement chargée lors de l'installation de l'application, car elle est incluse dans les seeders.

Si aucune base existante n'est disponible, il est **toujours nécessaire de charger la liste des sociétés**.  
> Cette liste doit être **mise à jour régulièrement** pour garantir la cohérence des données lors de l'enregistrement des titres et transactions.

### 📥 Liens de téléchargement des ressources nécessaires
src="logo.jpg"
- [Télécharger le fichier Excel des sociétés forestières](<a href="Fichiers Requis/societes.xlsx" target="_blank">)
 

> Veuillez utiliser ces fichiers comme modèles pour l'importation initiale des données.  
> Assurez-vous de les compléter et de les valider avant toute opération d'import.

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
|      | M001-SM006 : Gestion des rôles et permissions           | Administrateur      |
| M002 | **Authentification**                                    | *                   |
|      | M002-SM001 : Connexion                                  |                     |
|      | M002-SM002 : Déconnexion                                |                     |
|      | M002-SM003 : Gestion de la sécurité (2FA, logs)         |                     |
| M003 | **Gestion des Titres**                                  |                     |
|      | M003-SM001 : Filtrer un titre                           | *                   |
|      | M003-SM002 : Enregistrement d’un nouveau titre          | Utilisateur         |
|      | M003-SM003 : Mise à jour des informations               |                     |
|      | M003-SM004 : Suppression d’un titre                     |                     |
|      | M003-SM005 : Importation des titres via Excel           | Administrateur      |
| M004 | **Gestion des Opérateurs (Sociétés)**                   | Utilisateur         |
|      | M004-SM001 : Enregistrement de sociétés forestières     |                     |
|      | M004-SM002 : Mise à jour des informations               |                     |
|      | M004-SM003 : Suppression des sociétés                   |                     |
|      | M004-SM004 : Importation des sociétés via Excel         | Administrateur      |
| M005 | **Gestion des Transactions**                            | Utilisateur         |
|      | M005-SM001 : Enregistrement des transactions journalières |                   |
|      | M005-SM002 : Mise à jour des transactions               |                     |
|      | M005-SM003 : Détection des dépassements                 |                     |
|      | M005-SM004 : Suppression d’une transaction              |                     |
|      | M005-SM005 : Importation des transactions via Excel     | Administrateur      |
|      | M005-SM006 : Exportation des transactions               | Utilisateur         |
| M006 | **Gestion des Essences**                                |                     |
|      | M006-SM001 : Ajout des essences                         | Utilisateur         |
|      | M006-SM002 : Modification des essences                  |                     |
|      | M006-SM003 : Suppression des essences                   |                     |
|      | M006-SM004 : Importation des essences via Excel         | Administrateur      |
| M007 | **Gestion des Paramètres Généraux**                     | Administrateur      |
|      | M007-SM001 : Mise à jour des paramètres système         |                     |
| M008 | **Gestion des Notifications**                           | Utilisateur         |
|      | M008-SM001 : Alertes automatiques (dépassements, erreurs) |                   |
| M009 | **Tableau de bord Dynamique** (graphiques dynamiques)   | Utilisateur         |
| M010 | **Synthèse périodique**                                 | Utilisateur         |
| M011 | **Audit et Historique**                                 | Administrateur      |
|      | M011-SM001 : Suivi des actions et logs                  |                     |
| M012 | **Maintenance des Données**                             | Administrateur      |
|      | M012-SM001 : Importation et exportation des données via fichiers Excel pour la maintenance et la sauvegarde | |

> 🟰 * : Tous les acteurs

---

## ⏳ V. Durée Estimative

La durée estimée pour la réalisation du projet est de **1 à 3 mois** après lancement avec des interruptions
📅 Objectif de mise en production : **Début Mars**, selon les contraintes du client mais cela s'est fait en  **Juin**

---

## 📦 VI. Livrables

- 🔗 Lien officiel du site web fonctionnel :  
    [https://www.camerooncomcam-bois.com/](https://www.camerooncomcam-bois.com/)
- 💻 Code source documenté et commenté  
- 📘 Cahier de recettes (tests de validation)  
- 🧑‍🏫 Séances de formation et prise en main  

---

> 🔒 Ce projet joue un rôle clé dans la digitalisation du secteur forestier et dans le renforcement de la conformité et de la traçabilité.

