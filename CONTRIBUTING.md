
# Contribution Guide

Le wokflow git doit être utilisé lors de la contribution à des projets open source sur GitHub, comme indiqué dans ce document. Il nécessite une connaissance basique de git (commit, branche, etc.) en utilisant la ligne de commande.

## Comment contribuer au projet ?

- Forkez le répertoire Github du projet
- Suivez les [instructions](https://github.com/Ammar-Khaoula/ToDoList_Projet8?tab=readme-ov-file#installation) pour installer le projet
- Créer une nouvelle branche git:
```bash
  git checkout -b new-branch
``` 

- Push it on your fork git 
```bash
  push origin new-branch
``` 
- Ouvrir une pull request sur le répertoire Github du projet

## Règles de qualité

- Suivre les normes de codage du projet, basées sur les lignes directrices PSR (PHP Standards Recommendations), pour assurer la cohérence du code
- Utilisez des outils de validation de code tels que [PHPStan](https://phpstan.org/user-guide/getting-started) et [SymfonyInsight](https://insight.symfony.com/projects/92f258af-ae4e-475e-b6ac-43a9a38f48ac) pour identifier les erreurs et problèmes potentiels
- Favorisez l'utilisation de noms significatifs pour les variables
- Assurez-vous que toutes les fonctionnalités et corrections sont couvertes par des tests unitaires et fonctionnels 
- Maintenez un historique de commits propre et significatif pour faciliter la traçabilité
