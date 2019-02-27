[Une architecture, pas un protocole - cours OCR](https://openclassrooms.com/fr/courses/4087036-construisez-une-api-rest-avec-symfony/4280556-une-architecture-pas-un-protocole)

# Une architecture, pas un protocole

## 1. Architecture

**REST** est une architecture et non un protocole. Créé en 2000, REST est l'acronyme de **Re**presentational **S**tate **T**ransfer. C'est un moyen de présenter et manipuler des ressources.

Une **API** (**A**pplication **P**rogramming **I**nterface) est une application à laquelle on peut faire effectuer des actions via le protocole HTTP (récupérer des données concernant des utilisateurs, ajouter des produits, supprimer l'auteur d'un article, rattacher un artiste à un spectacle, etc). Ce qui ressemble fortement à ce qu'on peut retrouver dans un site web habitueln d'où la notion d'architecture (hormis le fait qu'il ne s'agit pas d'afficher de page web HTML).

Une API fixe une structure d'application.

**En pratique :** on crée une liste d'actions possibles avec l'application (récupérer une liste d'utilisateurs, en ajouter, en supprimer...) et une manière d'effectuer ces actions grâce à HTTP. Cette manière de faire doit respecter les contraintes de REST.

## 2. Les six contraintes REST

### Contrainte n° 1 : Client / Server (Client / Serveur)

Cette contrainte impose qu'il y ait une séparation des responsabilités entre le client et le serveur. En simplifiant les choses, le client effectue une requête HTTP, le serveur reçoit la demande et doit renvoyer une réponse. Le protocole HTTP est exactement pensé ainsi ! Cette contrainte fait également mention du fait que le serveur doit être en mesure de gérer des requêtes provenant de plusieurs clients à la fois.

**Illustration :**

![illustration requête client / serveur API](https://user.oc-static.com/upload/2017/01/30/14857889239877_contrainte-1-REST.001.png)

### Contrainte n° 2 : Stateless (sans état)

Une API REST doit être sans état : d'une requête à une autre, l'API ne doit pas garder en mémoire ce qu'il s'est passé à la requête précédente. Il faut donc oublier le principe de session côté serveur, c'est au client d'avoir une session.

:bulb: C'est particulièrement utile lorsqu'il faut qu'un utilisateur soit authentifié pour obtenir une information. En reprenant l'illustration ci-dessus, l'authentification doit se passer sur le frontend. A chaque requête, on communique à l'API les informations de l'utilisateur authentifié. L'API ne doit en aucun cas garder un historique des requêtes précédentes.

:warning: Cela ne veut pas dire qu'il ne peut y avoir d'enregistrement en base de données. Simplement, une API ne garde pas d'historique d'une requête à l'autre. L'API n'est là que pour répondre aux requêtes quand elles lui arrivent sans a priori.

### Contrainte n° 3 : Cacheable (cachable)

Pour limiter les temps de chargement, une API fait appel au cache HTTP : il s'agit de réduire au minimum le temps de génération d'une réponse HTTP. On fait en sorte qu'une même réponse HTTP ne soit pas générée deux fois. La première fois, tous les calculs et traitements sont faits (ce qui peut prendre beaucoup de temps), puis cette réponse est "enregistrée" pour pouvoir être resservie les fois prochaines et ainsi limiter le temps de traitement à chaque requête.

### Contrainte n° 4 : Layered system (système à plusieurs couches)

Lorsqu'un client émet une requête à une API, il ne doit pas savoir ce qui se passe pour obtenir une réponse. Le client ne se soucie pas de comment l'API renvoie une réponse.

### Contrainte n° 5 : Uniform interface (interface uniforme)

Une **ressource** est un élément que l'on manipule en fonction du besoin que l'on en a avec une API.

**Exemple :** avec l'API d'Instagram, il est possible de manipuler des utilisateurs (récupérer des informations, en mettre à jour, etc), de manipuler des images également. Les images et les utilisateurs sont des ressources.

La contrainte n° 5 est orientée ressources. Chaque ressource doit :
* posséder un identifiant unique ;
* doit avoir une représentation ;
* doit être auto-décrite.

#### Une ressource doit posséder un identifiant unique

Identifiant tel id, slug, uuid () ou tout autre attribut. En base de données ou ailleurs. On accède à une ressource en plaçant son identifiant dans l'URI.

**Exemple :**  pour accéder à un utilisateur, l'URI pourrait être  `/users/1`  (1 étant l'id de l'utilisateur)

:warning: Chaque URI doit donc correspondre à une ressource unique. Dans l'exemple prcédent, il doit être impossible d'accéder à un autre utilisateur que celui qui a l'id 1 lorsque l'on indique l'URI `/users/1`.

:warning: La méthode HTTP joue un rôle primordial : en effet, il y a une différence fondamentale entre utiliser la méthode HTTP  GET et la méthode HTTP POST.

Il est absolument interdit d'effectuer un changement d'état de la ressource (ajouter un utilisateur par exemple) grâce à une requête faite avec la méthode HTTP GET. Pour effectuer cette d'action, il faut utiliser la méthode HTTP POST.

Il est donc très important de comprendre qu'il y a une différence entre les requêtes suivantes :

 GET `/users`  - Récupération d'une liste d'utilisateurs.

 POST `/users`  - Ajout d'un nouvel utilisateur.

Même URI, mais méthode HTTP différente ! :)

#### Une ressource doit avoir une représentation

Il faut choisir une manière de formater / afficher la réponse et s'y tenir.

**Exemple :** dans l'exemple d'un utilisateur, l'API offre la possibilité de consulter les informations d'un utilisateur que l'on peut formater en JSON. Ainsi, si l'utilisateur de l'API effectue une requête GET sur l'url http://domain.name/users/ad70e3ea-e793-11e6-bf01-fe55135034f3, voici le contenu de la réponse :

```
{
    "uuid" : "ad70e3ea-e793-11e6-bf01-fe55135034f3",
    "fullname" : "Sarah Khalil",
    "job" : "Auteur"
}
```

#### Une ressource doit être auto-décrite

Il s'agit d'indiquer le format de la réponse (JSON, XML) dans le header de la requête HTTP en y ajoutant le `Content-Type`.

**Exemple :** dans le cas où la réponse est en JSON, le header serait `Content-Type: application/json`.

## Contrainte n° 6 (facultative) : Code on demand (du code sur demande)

Il s'agit de demander au serveur, donc à l'API, un morceau de code pour que celui-ci soit exécuté par le client. On s'assure que le code que l'on s'apprête à exécuter n'est pas malicieux.

## 3. Une architecture basée sur un protocole que l'on connaît bien : HTTP

**Rappels concernant HTTP :** protocole d'échange entre deux machines. Une API est une application capable de recevoir une requête HTTP et de rendre une réponse HTTP.

### 3.1. Requête HTTP

Une requête HTTP émane d'un client (tout logiciel dans la capacité de forger une requête).

**Structure type d'une requête HTTP :**

```
METHODE URL VERSION<crlf>
EN-TETE : Valeur<crlf>
.
.
.
EN-TETE : Valeur<crlf>
Ligne vide<crlf>
CORPS DE LA REQUETE
```

Une requête est constituée des éléments suivants :

1. La première ligne (request line) doit contenir :
* la méthode HTTP (GET, POST, PUT, PATCH, DELETE, OPTIONS, CONNECT, HEAD ou TRACE)
* l'URI, c'est-à-dire ce qu'il y a après le nom de domaine (exemple : `/users/1`)
* la version du protocole (exemple : HTTP/1.1)

2. Les en-têtes (headers), un en-tête par ligne, chaque ligne finie par le caractère spécial "retour à la ligne" (CRLF)

3. Le contenu de la requête (body), doit être séparé de deux caractères spéciaux "retour à la ligne" (CRLF CRLF) - optionnel

**Exemple de requête HTTP :**
```
POST /users HTTP/1.1
User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.95 Safari/537.36
Content-Type: application/x-www-form-urlencoded
Content-Length: 28

name=Sarah Khalil&job=auteur
```

### 3.2. Méthodes HTTP

Dans le cadre d'une API RESTFul, les méthodes GET, POST, PUT, DELETE sont celles généralement utilisées.

:arrow_forward: **GET :** utilisée pour récupérer des informations en rapport avec l'URI ; il ne faut en aucun cas modifier ces données au cours de cette requête. Cette méthode est dite safe (sécuritaire), puisqu'elle n'affecte pas les données du serveur. Elle est aussi dite idempotente, c'est-à-dire qu'une requête faite en GET doit toujours faire la même chose (comme renvoyer une liste d'utilisateurs à chaque fois que la requête est faite - d'une requête à l'autre, on ne renverra pas des produits si le client s'attend à une liste d'utilisateurs !).

:arrow_forward: **POST :** utilisée pour créer une ressource. Les informations (texte, fichier...) pour créer la ressource sont envoyées dans le contenu de la requête. Cette méthode n'est ni safe, ni idempotente.

:arrow_forward: **PUT :** utilisée pour remplacer les informations d'une resource avec ce qui est envoyé dans le contenu de la requête. Cette méthode n'est ni safe, ni idempotente.

:arrow_forward: **PATCH :** utilisée pour modifier une ressource. La différence avec une requête avec la méthode PUT est que l'action à effectuer sur la ressource est indiquée dans le contenu de la requête.

:bulb: La différence fondamentale entre les méthodes PUT et  PATCH est la manière dont les modifications sont demandées. Pour une requête PUT, l'ensemble des données sont fournies, il suffit simplement de récupérer la nouvelle version de la ressource pour la persister par exemple. Pour une requête PATCH, l'action est fournie parce qu'il peut y avoir différentes manières de mettre à jour une ressource.

**Exemple :** on souhaite rattacher un utilisateur à une organisation, dans le contenu de la requête, il sera indiqué qu'il s'agit d'un rattachement à une organisation en plus des informations à mettre à jour

:arrow_forward: **DELETE :** utilisée pour supprimer une ou plusieurs ressources. Les ressources à supprimer sont indiquées dans l'URI.

:arrow_forward: **OPTIONS :** utilisée pour obtenir la liste des actions possibles pour une ressource donnée (suppression, ajout…).

:arrow_forward: **CONNECT :** utilisée pour établir une première connexion avec le serveur pour une URI donnée.

:arrow_forward: **HEAD :** même principe que pour la méthode GET, mais seules les entêtes devront être renvoyées en réponse.

:arrow_forward: **TRACE :** utilisée pour connaître le chemin parcouru par la requête à travers plusieurs serveurs. En réponse, une entêteviasera présente pour décrire tous les serveurs par lesquels la requête est passée.

### 3.3. Réponse HTTP

Une réponse HTTP émane d'un serveur (tout logiciel dans la capacité de forger une réponse HTTP).

**Structure type d'une réponse HTTP :**

```
VERSION-HTTP CODE EXPLICATION<crlf>
EN-TETE : Valeur<crlf>
.
.
.
EN-TETE : Valeur<crlf>
Ligne vide<crlf>
CORPS DE LA REPONSE
```

Une réponse est constituée des éléments suivants :

1. La première ligne (status line) doit contenir :
* la version du protocole utilisée
* le code status
* l'équivalent textuel du code status

2. Les en-têtes (headers), un en-tête par ligne, chaque ligne finie par le caractère spécial "retour à la ligne" (CRLF)

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

### Le code status

[liste complète des codes status - wikipedia](https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP)

Il existe cinq catégories de **code status** à fournir dans la première ligne de la réponse HTTP :

| Catégorie | Description |
|-|-|
| 1xx : les informations | Une réponse doit contenir ce type de code status lorsqu'il s'agit d'informer le client de l'état de la demande. C'est utile pour indiquer que, par exemple, la requête a bien été reçue et que le traitement vient de commencer, dans le cas de traitements asynchrones par exemple. |
| 2xx : les succès | Tout s'est bien passé sur le serveur. |
| 3xx : les redirections | Une redirection est sur le point d'être effectuée. Petite exception avec le code 304 qui signifie que le contenu n'a pas changé, dans un contexte de cache. |
| 4xx : les erreurs client | La requête contient une erreur et ne peut pas être traitée. |
| 5xx : les erreurs serveur | Le serveur vient de rencontrer un problème empêchant le traitement de la requête |

## 4. Modèle de maturité de Richardson

[Niveaux de respects du modèle](https://martinfowler.com/articles/richardsonMaturityModel.html)

Le modèle de maturité de Richardson donne un moyen d'évaluer une API à partir d'une échelle de 4 niveaux (de 0 à 3). Plus on monte dans les niveaux, plus l'API est considérée RESTFul (= pleinement REST). Plus l'API adhère aux contraintes REST, plus elle est RESTFul (va dans le sens des bonnes pratiques).

**Niveaux de respects du modèle de maturité de Richardson :**

![Niveaux de respects du modèle de maturité de Richardson](https://user.oc-static.com/upload/2017/01/19/14848360980357_overview.png)

### Level 0 : the swamp of POX (un marécage de bon vieux XML)

Une API ne respectant que le niveau 0 n'est pas une API REST (c'est plus API old school type SOAP).

Il s'agit de :
* n'utiliser qu'un seul point d'entrée pour communiquer avec l'API, c'est-à-dire qu'une seule URI, comme par exemple `/api` ;
* n'utiliser qu'une seule méthode HTTP pour effectuer ses demandes à l'API, avec `POST`.

:warning: Ces deux règles ne sont pas à suivre selon les contraintes de REST énoncées :
* en effet, chaque ressource devrait avoir son point d'entrée. Si par exemple une API gère des utilisateurs et des produits, il devrait y avoir au moins deux URI différentes pour récupérer ces listes : `/users` et `/products`.
* l'utilisation de la méthode HTTP `POST` pour toutes les actions à effectuer sur une API n'est pas une bonne pratique : la méthode `POST` n'est réservée qu'à la création de ressource. Si l'on souhaite récupérer une liste d'utilisateurs, il faut utiliser la méthode `GET` pour la requête.

### Level 1 : les ressources

Le niveau 1 concerne les ressources et demande dans un premier temps que chaque ressource puisse être distinguée séparément (la contrainte n° 5 stipule que les URIs doivent correspondre à la ressource que le client de votre API souhaite manipuler).

**Exemple d'URIs pour le CRUD d'articles :**

* pour la création d'articles : `/articles/create` ;
* pour la lecture d'articles : `/articles` (liste des articles) et `/articles/{identifiant-unique}` (un seul article) ;
* pour la mise à jour : `/articles/{identifiant-unique}/update` ;
* pour la suppression : `/articles/{identifiant-unique}/delete`.

### Level 2 : HTTP verbs (méthodes HTTP)

Ce niveau concerne l'utilisation des méthodes HTTP en fonction de l'action à effectuer sur une API. Il s'agit également de choisir avec soin le code status de la réponse HTTP.

**Exemple de méthodes HTTP pour le CRUD d'articles :**
* création : POST `/articles` ;
* lecture : GET `/articles` ou GET `/articles/{identifiant-unique}` ;
* mise à jour : PUT `/articles/{identifiant-unique}` ;
* suppression : DELETE `/articles/{identifiant-unique}`.

:bulb: **Pratique courante :** garder le nom des ressources au pluriel

**Code status les plus courants :**
* **200 OK** : tout s'est bien passé ;
* **201 Created** : la création de la ressource s'est bien passée (en général le contenu de la nouvelle ressource est aussi renvoyée dans la réponse, mais ce n'est pas obligatoire - on ajoute aussi un header Locationavec l'URL de la nouvelle ressource) ;
* **204 No content** : même principe que pour la 201, sauf que cette fois-ci, le contenu de la ressource nouvellement créée ou modifiée n'est pas renvoyée en réponse ;
* **304 Not modified** : le contenu n'a pas été modifié depuis la dernière fois qu'elle a été mise en cache ;
* **400 Bad request** : la demande n'a pas pu être traitée correctement ;
* **401 Unauthorized** : l'authentification a échoué ;
* **403 Forbidden** : l'accès à cette ressource n'est pas autorisé ;
* **404 Not found** : la ressource n'existe pas ;
* **405 Method not allowed** : la méthode HTTP utilisée n'est pas traitable par l'API ;
* **406 Not acceptable** : le serveur n'est pas en mesure de répondre aux attentes des entêtes  Accept . En clair, le client demande un format (XML par exemple) et l'API n'est pas prévue pour générer du XML ;
* **500 Server error** : le serveur a rencontré un problème.

:warning: Attention avec le code retour 400 : bon nombre d'APIs se contentent de ne renvoyer qu'une réponse avec ce code status sans même une explication, Penser à expliquer ce qu'il s'est passé (pourquoi la requête n'est pas correcte) afin de faciliter un mécanisme de retry côté client par exemple.

### Level 3 : hypermedia controls (the glory of REST!)

Le niveau 3 est simplement l'idée de rendre une API auto découvrable en imitant les liens hypertextes dans une page web classique. Concrètement, on doit indiquer au client de l'API ce qu'il est possible de faire à partir d'une ressource.

**Exemple dans le cas de la gestion d'articles :** si le client demande un article, non seulement il obtiendra les informations de l'article en question (titre, contenu, etc), mais aussi la liste des liens (URL) pour effectuer d'autres actions sur cette ressource, comme la mettre à jour ou un article associé par exemple.

**Exemple de contenu de réponse offrant une auto découverte de l'API :**

```
{
    "id" : 1,
    "title" : "Le titre de l'article",
    "content" : "<p> Le contenu de l'article.</p>",
    "links" : {
        "update" : "http://domain.name/article/1",
        "associated" : "http://domain.name/article/16"
    }
}
```