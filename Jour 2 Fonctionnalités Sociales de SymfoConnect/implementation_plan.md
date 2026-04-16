# Plan d'implémentation - Jour 2 : Fonctionnalités Sociales

Ce document détaille les étapes techniques pour accomplir les objectifs du Jour 2 de SymfoConnect.

## 1. Sécurité et Authentification
- [ ] **Mise à jour de l'entité User** : Implémenter `UserInterface` et `PasswordAuthenticatedUserInterface`. Ajouter le champ `roles`.
- [ ] **Inscription** : Créer un `RegistrationController` avec un formulaire (`RegistrationFormType`) validant l'unicité de l'email et du username, et hachant le mot de passe.
- [ ] **Connexion / Déconnexion** : Configurer `security.yaml` et créer un `LoginAuthenticator` via `make:auth`.
- [ ] **Interface** : Modifier le layout (`base.html.twig`) pour afficher le username et un bouton de déconnexion si l'utilisateur est connecté.

## 2. Entités Sociales (Relations ManyToMany)
- [ ] **Système de Follow** : Relation ManyToMany auto-référencée sur `User` (`following` / `followers`).
- [ ] **Système de Like** : Relation ManyToMany entre `Post` et `User`.
- [ ] **Notifications** : Créer l'entité `Notification` (recipient, type, content, isRead, createdAt).

## 3. Logique Métier
- [ ] **Création de Post sécurisée** : Restreindre `/post/nouveau` aux utilisateurs connectés et assigner automatiquement `app.user` comme auteur.
- [ ] **Actions Follow/Like** : Créer les routes et méthodes de controller pour `follow/{username}`, `unfollow/{username}`, `like/{id}` et `unlike/{id}`.
- [ ] **Fil d'actualité (`/feed`)** : Requête DQL/QueryBuilder pour récupérer les posts des personnes suivies par l'utilisateur connecté.

## 4. Autorisations et Voter
- [ ] **PostVoter** : Implémenter un Voter pour vérifier que seul l'auteur peut supprimer son post.
- [ ] **Suppression** : Ajouter la logique de suppression dans le controller en utilisant `isGranted('DELETE', post)`.

## 5. Bonus & UX
- [ ] **Flash Messages** : Notifications visuelles lors des actions (follow, like, erreur).
- [ ] **Notification Follow** : Créer une entrée en base de données `Notification` lorsqu'un utilisateur en suit un autre.
- [ ] **Design Premium** : Améliorer les boutons et compteurs sur les profils et les posts.
