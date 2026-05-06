# Blog - Application Symfony

Application de blog développée avec Symfony 8, permettant la gestion d'articles, de catégories et d'utilisateurs avec un système de rôles.

## Fonctionnalités

- **Articles** : création, modification, suppression, recherche par titre, filtrage par catégorie, pagination
- **Catégories** : gestion avec code couleur
- **Authentification** : inscription, connexion, déconnexion
- **Gestion des rôles** : Utilisateur, Éditeur, Administrateur
- **Contrôle d'accès** : seul l'auteur (ou un admin) peut modifier/supprimer ses articles

## Stack technique

| Couche | Technologie |
|--------|------------|
| Backend | PHP 8.4 / Symfony 8.0 |
| ORM | Doctrine ORM 3.6 |
| Base de données | MySQL 8.0 |
| Frontend | Twig / Bootstrap 5.3 |
| JavaScript | Stimulus 3 / Turbo 7 |

## Prérequis

- PHP >= 8.4
- Composer
- MySQL 8.0
- Symfony CLI (recommandé)

## Installation

1. **Cloner le dépôt**
   ```bash
   git clone <url-du-repo>
   cd blog
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer la base de données**

   Copier `.env` en `.env.local` et renseigner la connexion :
   ```env
   DATABASE_URL="mysql://user:password@127.0.0.1:3306/blog?serverVersion=8.0.32&charset=utf8mb4"
   ```

4. **Créer la base de données et lancer les migrations**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Installer les assets**
   ```bash
   php bin/console importmap:install
   ```

6. **Lancer le serveur**
   ```bash
   symfony server:start
   ```
   Puis ouvrir [http://localhost:8000](http://localhost:8000)

## Utilisation

### Rôles disponibles

| Rôle | Accès |
|------|-------|
| `ROLE_USER` | Créer et gérer ses propres articles |
| `ROLE_EDITOR` | Idem + accès éditeur |
| `ROLE_ADMIN` | Accès complet + gestion des utilisateurs (`/admin/user`) |

### Créer un compte administrateur

Après inscription, accéder à `/admin/user` avec un compte admin pour attribuer les rôles.

## Structure du projet

```
src/
├── Controller/       # PostController, CategoryController, UserController, SecurityController
├── Entity/           # Post, Category, User
├── Form/             # PostType, CategoryType, RegistrationFormType
└── Repository/       # PostRepository (recherche + pagination)

templates/
├── post/             # index, form, delete_form
├── category/         # index, new
├── user/             # index (admin)
├── security/         # login
├── layouts/          # composants réutilisables (flash messages)
└── base.html.twig    # layout principal
```
