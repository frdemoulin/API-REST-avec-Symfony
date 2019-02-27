[Les outils indispensables à tout bon développeur d'API - cours OCR](https://openclassrooms.com/fr/courses/4087036-construisez-une-api-rest-avec-symfony/4285681-les-outils-indispensables-a-tout-bon-developpeur-dapi)

# Les outils indispensables à tout bon développeur d'API

Besoin d'interroger une API de toutes sortes de manières, en faisant varier les headers et contenus de la requête en particulier. Pour se faire, deux outils : le premier est graphique et très facile à prendre en main, Postman ! Le second est à utiliser en ligne de commande, Curl.

## 1. Postman

[Postman - site officiel](https://www.getpostman.com/)

Interface graphique permettant d'appeler / tester une API. Il permet de :
* faire une requête HTTP avec la méthode idoine ;
* y ajouter des paramètres en POST par exemple ;
* simuler le fonctionnement d'un formulaire ;
* ajouter un ou plusieurs headers à la requête ;
* etc.

## 2. cURL

cURL est l'abréviation de _client URL request library_ : « bibliothèque de requêtes aux URL pour les clients ». L'utilisation de Curl se fait via la ligne de commande et est destinée à récupérer le contenu d'une ressource accessible par un réseau informatique. Il peut ainsi être utilisé en tant que client REST.

**Exemple de requête POST avec cURL :**

```
$ curl 127.0.0.1\
-H "Accept: application/json"\
-X POST\
-u myuser:pass\
--data '{"message":"hello"}'
```

Le premier argument `127.0.0.1` est l'host que l'on cherche à contacter. Les options sont :
* `-H` correspond aux headers que nous souhaitons ajouter à la requête ;
* `-X` correspond à la méthode HTTP que nous souhaitons utiliser ;
* `-u` correspond aux informations que nous souhaitons faire passer pour une authentification HTTP ;
* `--data` correspond au contenu (body) de la requête.

Il existe d'autres options accessibles via la commande `curl --help`.