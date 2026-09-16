# WP Books

Plugin WordPress permettant de récupérer et d'afficher une sélection de livres depuis l'API publique [Gutendex](https://gutendex.com/).

## Installation

### Prérequis

* WordPress 6.0+
* PHP 8.1+

### Installation du plugin

1. Cloner le repository dans le dossier `wp-content/plugins/` de votre installation WordPress :

```bash
git clone https://github.com/QVan27/WP-Books wp-books
```

2. Depuis l'administration WordPress, aller dans **Extensions**.

3. Activer le plugin **WP Books**.

Aucune installation WordPress complète n'est fournie avec le repository. Les fichiers du cœur de WordPress, les dépendances et les fichiers contenant des informations sensibles ne sont pas versionnés.

## Utilisation

### Shortcode

Ajouter le shortcode suivant dans une page :

```text
[books_list]
```

### Bloc Gutenberg

Depuis l'éditeur Gutenberg, rechercher le bloc **Books list** dans l'inserteur et l'ajouter à la page.

### Administration

Une page **WP Books** est disponible dans le menu d'administration WordPress (Réglages).

Elle permet de :

* consulter la date de dernière récupération des données ;
* actualiser manuellement les données ;
* supprimer le cache.
* configuration des options du plugin comme le titre et l'introduction.

## Choix techniques

### Récupération des données

L'API Gutendex est interrogée à l'aide de `wp_remote_get()`, la fonction HTTP native de WordPress.

Les 30 premiers livres sont récupérés. La réponse est vérifiée avant d'être utilisée :

* erreur de connexion ;
* code HTTP ;
* réponse JSON invalide ;
* données attendues absentes.

Les données provenant de l'API sont ensuite normalisées avant d'être transmises au renderer.

### Cache

Les résultats sont stockés dans un transient WordPress.

Cela permet d'éviter une requête vers l'API à chaque affichage tout en utilisant le système de cache natif de WordPress.

Le cache peut être actualisé ou supprimé manuellement depuis l'administration.

### Architecture

Le plugin sépare les principales responsabilités :

* `WP_Books_API` : communication avec Gutendex et normalisation des données ;
* `WP_Books_Cache` : gestion du cache ;
* `WP_Books_Renderer` : préparation et rendu des livres ;
* `WP_Books_Block` : enregistrement du bloc Gutenberg ;
* `WP_Books_Admin` : gestion de l'administration.

Le shortcode et le bloc Gutenberg utilisent le même renderer afin d'éviter de dupliquer la logique de rendu.

### Front-end

Le rendu initial est effectué côté serveur.

La recherche par titre, le filtre par langue, la pagination et le changement de vue grille/liste sont ensuite gérés côté JavaScript à partir des livres déjà chargés.

Cela évite de multiplier les requêtes vers l'API pour chaque interaction.

### WordPress

Le plugin utilise autant que possible les APIs natives de WordPress :

* HTTP API pour les requêtes externes ;
* Transients API pour le cache ;
* Shortcode API ;
* Block API ;
* APIs d'administration ;
* fonctions natives de nettoyage et d'échappement.

Aucune modification du thème n'est nécessaire.

### Sécurité

Les actions disponibles dans l'administration sont protégées par des vérifications de capacités et des nonces.

Les données externes sont nettoyées et les valeurs affichées sont échappées selon leur contexte.

## Limitations

* Seuls les 30 premiers livres retournés par Gutendex sont récupérés.
* La recherche et le filtre s'appliquent uniquement à ces 30 livres.
* La pagination est donc limitée aux données récupérées.
* La durée du cache est actuellement définie dans le code et n'est pas configurable depuis l'administration.
* Les recherches et filtres sont effectués côté client et ne déclenchent pas de nouvelle requête vers Gutendex.
* Le chargement de nouveaux résultats de manière asynchrone n'est pas implémenté.
* Le plugin dépend de la disponibilité de l'API Gutendex.

## Tests

Le plugin a été testé sur une installation WordPress avec les thèmes :

* Twenty Twenty-Five ;
* Twenty Twenty-Four ;
* Ainsi que mon thème personnalisé.

Les principales fonctionnalités testées sont :

* récupération des livres depuis Gutendex ;
* gestion du cache ;
* gestion des erreurs API ;
* affichage des données manquantes ;
* shortcode ;
* bloc Gutenberg ;
* recherche par titre ;
* filtre par langue ;
* pagination ;
* changement de vue grille/liste ;
* responsive ;
* actions d'administration.

## Temps passé

Environ **3 heures** de développement, intégration et tests.
