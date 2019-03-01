[:link: Premiers pas avec le FOSRestBundle - cours OCR](https://openclassrooms.com/fr/courses/4087036-construisez-une-api-rest-avec-symfony/4320271-premiers-pas-avec-le-fosrestbundle)

# Premiers pas avec le FOSRestBundle

## 1. Qu'est-ce que FOSRestBundle ?

FOSRestBundle est un bundle permettant de répondre à des problèmes courants durant le développement d'API REST. Par exemple :

* Désérialiser le contenu de la requête automatiquement (de JSON ou XML vers objet)
* Gérer la négociation de contenu : il s'agit de lire les entêtes (headers) d'une requête pour en déterminer la réponse adéquate
* Désactiver la protection CSRF pour les formulaires : c'est très pratique dans la mesure où ce genre de vérification doit se faire côté client (pas de session côté serveur !)
* Fournir un certain nombre d'annotations pour les controllers afin faciliter les choses
* Faciliter l'intégration du bundle `JMSSerializerBundle` pour la sérialisation : on peut donc bénéficier de toutes les fonctionnalités de JMSSerializer vues précédemment

**Démarche à suivre :** générer un projet Symfony full stack neuf dans le but de créer une API REST de gestion d'articles de blog.

## 2. Installation



## 3. Configuration