# Student Link 

**Student Link** est une application web dynamique et mobile-first (PWA) conçue pour connecter les futurs étudiants d'un même établissement avant la rentrée scolaire. Elle permet aux utilisateurs de créer un profil, de rechercher des camarades partageant les mêmes centres d'intérêt ou la même école, et de gérer les paramètres de leur compte.

---

## Fonctionnalités Clés (Cahier des Charges)

- **Authentification Sécurisée** : Inscription et connexion par email et mot de passe.
- **Dashboard Dynamique** : Affichage automatique des 10 derniers inscrits issus du *même établissement* que l'étudiant connecté.
- **Moteur de Recherche Global** : Filtrage des étudiants par Nom, Prénom, Établissement ou Centres d'intérêt (`#tags`).
- **Gestion du Profil** : Mise à jour de l'âge et des passions de l'utilisateur.
- **Espace Réglages & RGPD** : Modification du mot de passe et possibilité de désactiver son compte (*Soft Delete*).
- **Interface Mobile-First** : Design responsive adapté aux smartphones (Grille fluide, menu de navigation bas).

---

## Stack Technique & Architecture

L'application a été développée sans framework ("from scratch") afin de maîtriser les fondamentaux du développement web moderne :

### Architecture Logicielle
- **Design Pattern : MVC (Modèle-Vue-Contrôleur)**
  - `src/Model/` : Gestion des entités et des interactions avec la base de données (PDO).
  - `src/Controller/` : Logique métier, interception des formulaires et sécurité.
  - `src/View/` : Interfaces utilisateurs (HTML5 / CSS3 / JavaScript).

### Technologies Utilisées
- **Backend** : PHP 8 (Programmation Orientée Objet) et sessions natives pour le maintien de l'état d'authentification.
- **Base de données** : MySQL via l'interface d'abstraction **PDO**.
- **Frontend** : HTML5, CSS3 (Flexbox/Grid, Media Queries) et JavaScript moderne (POO / Classes indépendantes pour la validation de formulaires).

---

## Sécurité Implémentée

1. **Injections SQL** : Neutralisées à 100% via l'utilisation systématique de **requêtes préparées** (`PDO::prepare`) avec des marqueurs nommés.
2. **Hachage des Mots de Passe** : Utilisation de l'algorithme robuste de salage et hachage automatique **`PASSWORD_BCRYPT`** via la fonction native `password_hash()`. Les mots de passe ne transitent et ne sont stockés qu'en clair lors de la saisie initiale.
3. **Failles XSS (Cross-Site Scripting)** : Échappement systématique de toutes les données dynamiques affichées au client à l'aide de la fonction **`htmlspecialchars()`**.
4. **Gestion des Erreurs** : Utilisation de blocs `try / catch` et du mode d'exception de PDO (`PDO::ERRMODE_EXCEPTION`) pour éviter la divulgation d'informations sensibles (chemins système, structure de table) en cas de panne de la base.
5. **Conformité RGPD** : Implémentation d'un **Soft Delete** pour la désactivation de compte (mise à jour du statut en base de données plutôt qu'une suppression brute), assurant l'intégrité référentielle tout en masquant l'utilisateur.


