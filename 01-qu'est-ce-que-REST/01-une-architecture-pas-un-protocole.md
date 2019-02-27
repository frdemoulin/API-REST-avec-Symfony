[lien OCR](https://openclassrooms.com/fr/courses/4087036-construisez-une-api-rest-avec-symfony/4280556-une-architecture-pas-un-protocole)

# 1. Architecture

REST est une architecture et non un protocole. Créé en 2000, REST est l'acronyme de **Re**presentational **S**tate **T**ransfer. C'est un moyen de présenter et manipuler des ressources.

Une API (**A**pplication **P**rogramming **I**nterface) est une application à laquelle on peut faire effectuer des actions via le protocole HTTP (récupérer des données concernant des utilisateurs, ajouter des produits, supprimer l'auteur d'un article, rattacher un artiste à un spectacle, etc). Ce qui ressemble fortement à ce qu'on peut retrouver dans un site web habitueln d'où la notion d'architecture (hormis le fait qu'il ne s'agit pas d'afficher de page web HTML).

Une API fixe une structure d'application.

**En pratique :** on crée une liste d'actions possibles avec l'application (récupérer une liste d'utilisateurs, en ajouter, en supprimer...) et une manière d'effectuer ces actions grâce à HTTP. Cette manière de faire doit respecter les contraintes de REST.

# 2. Les six contraintes REST

## Contrainte n° 1 : Client / Server (Client / Serveur)

Cette contrainte impose qu'il y ait une séparation des responsabilités entre le client et le serveur. En simplifiant les choses, le client effectue une requête HTTP, le serveur reçoit la demande et doit renvoyer une réponse. Le protocole HTTP est exactement pensé ainsi ! Cette contrainte fait également mention du fait que le serveur doit être en mesure de gérer des requêtes provenant de plusieurs clients à la fois.

**Illustration :**

![illustration requête client / serveur API](https://user.oc-static.com/upload/2017/01/30/14857889239877_contrainte-1-REST.001.png)

## Contrainte n° 2 : Stateless (sans état)

Une API REST doit être sans état : d'une requête à une autre, l'API ne doit pas garder en mémoire ce qu'il s'est passé à la requête précédente. Il faut donc oublier le principe de session côté serveur, c'est au client d'avoir une session.

:bulb: C'est particulièrement utile lorsqu'il faut qu'un utilisateur soit authentifié pour obtenir une information. En reprenant l'illustration ci-dessus, l'authentification doit se passer sur le frontend. A chaque requête, on communique à l'API les informations de l'utilisateur authentifié. L'API ne doit en aucun cas garder un historique des requêtes précédentes.

:warning: Cela ne veut pas dire qu'il ne peut y avoir d'enregistrement en base de données. Simplement, une API ne garde pas d'historique d'une requête à l'autre. L'API n'est là que pour répondre aux requêtes quand elles lui arrivent sans a priori.

## Contrainte n° 3 : Cacheable (cachable)

Pour limiter les temps de chargement, une API fait appel au cache HTTP : il s'agit de réduire au minimum le temps de génération d'une réponse HTTP. On fait en sorte qu'une même réponse HTTP ne soit pas générée deux fois. La première fois, tous les calculs et traitements sont faits (ce qui peut prendre beaucoup de temps), puis cette réponse est "enregistrée" pour pouvoir être resservie les fois prochaines et ainsi limiter le temps de traitement à chaque requête.

## Contrainte n° 4 : Layered system (système à plusieurs couches)

Lorsqu'un client émet une requête à une API, il ne doit pas savoir ce qui se passe pour obtenir une réponse. Le client ne se soucie pas de comment l'API renvoie une réponse.

## Contrainte n° 5 : Uniform interface (interface uniforme)

Une **ressource** est un élément que l'on manipule en fonction du besoin que l'on en a avec une API.

**Exemple :** avec l'API d'Instagram, il est possible de manipuler des utilisateurs (récupérer des informations, en mettre à jour, etc), de manipuler des images également. Les images et les utilisateurs sont des ressources.

La contrainte n° 5 est orientée ressources. Chaque ressource doit :
* posséder un identifiant unique ;
* doit avoir une représentation ;
* doit être auto-décrite.

### Une ressource doit posséder un identifiant unique

Identifiant tel id, slug, uuid () ou tout autre attribut. En base de données ou ailleurs. On accède à une ressource en plaçant son identifiant dans l'URI.

**Exemple :**  pour accéder à un utilisateur, l'URI pourrait être  `/users/1`  (1 étant l'id de l'utilisateur)

:warning: Chaque URI doit donc correspondre à une ressource unique. Dans l'exemple prcédent, il doit être impossible d'accéder à un autre utilisateur que celui qui a l'id 1 lorsque l'on indique l'URI `/users/1`.

:warning: La méthode HTTP joue un rôle primordial : en effet, il y a une différence fondamentale entre utiliser la méthode HTTP  GET et la méthode HTTP POST.

Il est absolument interdit d'effectuer un changement d'état de la ressource (ajouter un utilisateur par exemple) grâce à une requête faite avec la méthode HTTP GET. Pour effectuer cette d'action, il faut utiliser la méthode HTTP POST.

Il est donc très important de comprendre qu'il y a une différence entre les requêtes suivantes :

 GET `/users`  - Récupération d'une liste d'utilisateurs.

 POST `/users`  - Ajout d'un nouvel utilisateur.

Même URI, mais méthode HTTP différente ! :)

### Une ressource doit avoir une représentation

Il faut choisir une manière de formater / afficher la réponse et s'y tenir.

**Exemple :** dans l'exemple d'un utilisateur, l'API offre la possibilité de consulter les informations d'un utilisateur que l'on peut formater en JSON. Ainsi, si l'utilisateur de l'API effectue une requête GET sur l'url http://domain.name/users/ad70e3ea-e793-11e6-bf01-fe55135034f3, voici le contenu de la réponse :

```
{
    "uuid" : "ad70e3ea-e793-11e6-bf01-fe55135034f3",
    "fullname" : "Sarah Khalil",
    "job" : "Auteur"
}
```

### Une ressource doit être auto-décrite

Il s'agit d'indiquer le format de la réponse (JSON, XML) dans le header de la requête HTTP en y ajoutant le `Content-Type`.

**Exemple :** dans le cas où la réponse est en JSON, le header serait `Content-Type: application/json`.

## Contrainte n° 6 (facultative) : Code on demand (du code sur demande)

Il s'agit de demander au serveur, donc à l'API, un morceau de code pour que celui-ci soit exécuté par le client. On s'assure que le code que l'on s'apprête à exécuter n'est pas malicieux.

# 3. Une architecture basée sur un protocole que l'on connaît bien : HTTP

Rappels concernant HTTP : protocole d'échange entre deux machines. Une API n'est rien d'autre qu'une application capable de recevoir une requête HTTP et rendre une réponse HTTP

## 3.1. Requête HTTP

Une requête HTTP émane d'un client (tout logiciel dans la capacité de forger une requête). Une requête est constituée des éléments suivants :

```
Ligne de commande (méthode HTTP, URI, version de protocole)
En-tête de requête
<nouvelle ligne>
Corps de requête
```

1. La première ligne (request line) doit contenir :
* la méthode HTTP (GET, POST, PUT, PATCH, DELETE, OPTIONS,  CONNECT, HEAD ou TRACE)
* l'URI, c'est-à-dire ce qu'il y a après le nom de domaine (exemple : `/users/1`)
* la version du protocole (exemple : HTTP/1.1)

2. Les entêtes (headers), un entête par ligne, chaque ligne finie par le caractère spécial "retour à la ligne" (CRLF)

3. Le contenu de la requête (body), doit être séparé de deux caractères spéciaux "retour à la ligne" (CRLF CRLF) - optionnel

**Exemple de requête HTTP :**
```
POST /users HTTP/1.1
User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36
Content-Type: application/x-www-form-urlencoded
Content-Length: 28

name=Sarah Khalil&job=auteur
```

## 3.2. Méthodes HTTP

Dans le cadre d'une API RESTFul, les méthodes GET, POST,  PUT, DELETE sont celles généralement utilisées.

**GET :** utilisée pour récupérer des informations en rapport avec l'URI ; il ne faut en aucun cas modifier ces données au cours de cette requête. Cette méthode est dite safe (sécuritaire), puisqu'elle n'affecte pas les données du serveur. Elle est aussi dite idempotente, c'est-à-dire qu'une requête faite en GET doit toujours faire la même chose (comme renvoyer une liste d'utilisateurs à chaque fois que la requête est faite - d'une requête à l'autre, on ne renverra pas des produits si le client s'attend à une liste d'utilisateurs !).

**POST :** utilisée pour créer une ressource. Les informations (texte, fichier...) pour créer la ressource sont envoyées dans le contenu de la requête. Cette méthode n'est ni safe, ni idempotente.

**PUT :** utilisée pour remplacer les informations d'une resource avec ce qui est envoyé dans le contenu de la requête. Cette méthode n'est ni safe, ni idempotente.

**PATCH :** utilisée pour modifier une ressource. La différence avec une requête avec la méthode PUT est que l'action à effectuer sur la ressource est indiquée dans le contenu de la requête.

:bulb: La différence fondamentale entre les méthodes PUT et  PATCH est la manière dont les modifications sont demandées. Pour une requête PUT, l'ensemble des données sont fournies, il suffit simplement de récupérer la nouvelle version de la ressource pour la persister par exemple. Pour une requête PATCH, l'action est fournie parce qu'il peut y avoir différentes manières de mettre à jour une ressource.

**Exemple :** on souhaite rattacher un utilisateur à une organisation, dans le contenu de la requête, il sera indiqué qu'il s'agit d'un rattachement à une organisation en plus des informations à mettre à jour

**DELETE :** utilisée pour supprimer une ou plusieurs ressources. Les ressources à supprimer sont indiquées dans l'URI.

**OPTIONS :** utilisée pour obtenir la liste des actions possibles pour une ressource donnée (suppression, ajout…).

**CONNECT :** utilisée pour établir une première connexion avec le serveur pour une URI donnée.

**HEAD :** même principe que pour la méthode GET, mais seules les entêtes devront être renvoyées en réponse.

**TRACE :** utilisée pour connaître le chemin parcouru par la requête à travers plusieurs serveurs. En réponse, une entêteviasera présente pour décrire tous les serveurs par lesquels la requête est passée.

## 3.3. Réponse HTTP

Une réponse HTTP émane d'un serveur (tout logiciel dans la capacité de forger une réponse HTTP). Une réponse est constituée des éléments suivants :

1. La première ligne (status line) doit contenir :
* la version du protocole utilisée
* le code status
* l'équivalent textuel du code status

2. Les entêtes (headers), un entête par ligne, chaque ligne finie par le caractère spécial "retour à la ligne" (CRLF)

3. Le contenu de la réponse (body), doit être séparé de deux caractères spéciaux "retour à la ligne (CRLFCRLF) - optionnel.

**Exemple de réponse HTTP :**

```
HTTP/1.1 200 OK
Date:Tue, 31 Jan 2017 13:18:38 GMT
Content-Type: application/json

{
    "current status" : "Everything is ok!"
}
```

## Le code status

Il existe cinq catégories de code status à fournir dans la première ligne de la réponse HTTP :



# 4. Modèle de maturité de Richardson