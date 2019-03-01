[:link: Introduction à la sérialisation avec JMSSerializer - cours OCR](https://openclassrooms.com/fr/courses/4087036-construisez-une-api-rest-avec-symfony/4301996-introduction-a-la-serialisation-avec-jmsserializer)

# Introduction à la sérialisation avec JMSSerializer

Le sérialiseur natif de Symfony est disponible depuis les toutes premières versions du framework. Cependant, les fonctionnalités supportées par celui-ci étaient assez basique.

JMSSerializer est un bundle qui a ainsi été développé pour la gestion de la sérialisation dans Symfony. Il permet d’intégrer la librairie JMSSerializer et est très largement utilisé dans le cadre du développement d’une API avec Symfony.

## 1. Installation de JMSSerializer

Installation de Symfony 3.4 : `composer create-project symfony/framework-standard-edition blog-api "3.4.*"`

Installation de JMSSerializer : `composer require jms/serializer-bundle`

Une fois l'installation terminée, il faut déclarer JMSSerializer dans la classe `AppKernel` (dans le dossier `app` du projet) :

```php
<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // …
            new JMS\SerializerBundle\JMSSerializerBundle(),
        ];
        // …
    }
    // …
}
```

## 2. La ressource, le cœur de la sérialisation

On crée une entité `Article` (classe `Article.php` dans le dossier `src/AppBundle/Entity`).

<details>
<summary>code de la classe Article.php</summary>
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
class Article
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
</details>

Création de la base de données et du schéma : `$ bin/console doctrine:database:create && bin/console doctrine:schema:create`

:warning: On veillera à configurer l'accès à la base de données dans le fichier `app/config/parameters.yml`.

## 3. Premiers pas : la sérialisation et la désérialisation

La librairie `JMSSerializer` propose de nombreuses fonctionnalités :
* linéariser (sérialisation) un graph d'objets (un objet peut en contenir d'autres, ce que l'on appelle un "graph d'objets") en chaîne de caractères (JSON, XML) ;
* délinéariser (désérialisation) une chaîne de caractères pour obtenir un graph d'objets ;
* la linéarisation est configurable en YAML, XML ou en annotations ;
* l'intégration de JMSSerializer est native avec FOSRestBundle.

### 3.1 La sérialisation

La **sérialisation** est un processus permettant de convertir des données (une instance d’une classe, un tableau, etc.) en un format prédéfini. Pour le cas d'une API, la sérialisation est le mécanisme par lequel les objets PHP seront transformés en un format textuel (JSON, XML, etc.).

**Cas pratique** : envoi en JSON des infos d'un article donné

**Contraintes REST à respecter :**

* l'URI doit être unique pour chaque ressource (ici, ce sera `/articles/{id}`) ;
* le contenu de la réponse doit être auto-décrit, il faut donc indiquer dans la réponse quel type de données est envoyé (JSON).

On récupère un objet article en base de données, on le transforme en format linéarisé (JSON) pour l'envoyer à un client (cas typique de la présentation de données, celle d'un article ici).

Concrètement, on crée une méthode `showAction()` associée à une route en `GET`.

<details>
<summary>code de la méthode <code>showAction</code></summary>
<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticleController extends Controller
{
    /**
     * @Route("/articles/{id}", name="article_show", methods={"GET"})
     */
    public function showAction(Article $article)
    {
        // on serialise l'objet $article
        // $article est hydraté automatiquement grâce au ParamConverter de Doctrine
        $data = $this->get('jms_serializer')->serialize($article, 'json');

        // on retourne une réponse
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json'); 

        return $response;
    }
}
</details>

<br />

**Explications du code de la méthode `showAction` :**

:small_blue_diamond: On récupère le paramètre d'url (en l'occurrence, l'id de l'article à renvoyer en JSON) grâce au ParamConverter de Doctrine. Doctrine comprend que l'id passé dans l'url est la clé primaire d'un objet de l'entité `Article` qu'il va automatiquement chercher en base via la méthode `find()`. Si une telle entrée est trouvée en base, Doctrine hydrate alors un objet `Article` dans la variable `$article` :

```
/**
 * @Route("/articles/{id}", name="article_show")
 */
public function showAction(Article $article, methods={"GET"})
{
    // si l'on ping en GET l'URI `/articles/1`, la variable $article va contenir un objet hydraté avec les valeurs des champs de l'article d'id 1 en base
    dump($article);
    // ..
}
```

![capture du 2019-02-28 14-12-43](https://user-images.githubusercontent.com/1475600/53568855-58418200-3b63-11e9-96af-38917d145f9c.png)

:small_blue_diamond: L'appel au service `jms_serializer` se fait via la commande `get('jms_serializer')` sur laquelle on appelle la méthode `serialize`. Cette méthode prend deux arguments :
  * argument 1 : l'objet à sérialiser ;
  * argument 2 : le format dans lequel on souhaite sérialiser l'objet (ici, du JSON).

```
$data = $this->get('jms_serializer')->serialize($article, 'json');
```

:small_blue_diamond: On construit l'objet `Response` avec les données sérialisées et on finit par indiquer qu'il s'agit de JSON grâce à l'entête `Content-Type` :

```
$response = new Response($data);
$response->headers->set('Content-Type', 'application/json');

return $response;
```

:bulb: Pour récupérer des paramètres d'URL en GET ou en POST dans l'objet `$request` :

[:link: Symfony Request Object - doc Symfony](https://symfony.com/doc/current/introduction/http_fundamentals.html#symfony-request-object)

```
// retrieve GET variables
$request->query->get('foo');
// retrieve POST variables
$request->request->get('bar', 'default value if bar does not exist');
// possible également
$request->get('id');
```

### 3.2 La désérialisation

La **désérialisation** est le processus qui consiste à recevoir du JSON (ou tout autre format textuel) pour le transformer en objet.

**Cas pratique** : création d'un nouvel article. Pour cela, on crée une nouvelle méthode `createAction()` dans le contrôleur `ArticleController`. Cette méthode est en `POST`, car il s'agit d'une création de ressources (un nouvel article).

**Démarche :** désérialiser le JSON reçu, en obtenir un objet `Article`, puis le persister en base de données

<details>
<summary>code de la méthode <code>createAction</code></summary>
<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ArticleController extends Controller
{
    /**
     * @Route("/articles", name="article_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        // on récupère les datas reçues en POST
        $data = $request->getContent();

        // on désérialise les datas en hydratant un objet de l'entité Article
        $article = $this->get('jms_serializer')->deserialize($data, 'AppBundle\Entity\Article', 'json');

        // on récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        // on persist puis on flush le nouvel article
        $em->persist($article);
        $em->flush();

        // on retourne une réponse
        return new Response('', Response::HTTP_CREATED);
    }
}
</details>

<br />

**Explications du code de la méthode `createAction` :**

:small_blue_diamond: On récupère le JSON envoyé dans le body de la requête pour pouvoir le désérialiser. Pour cela, on fait à nouveau appel au service ` jms_serializer` avec la méthode `deserialize`. Cette méthode prend trois arguments :
* argument 1 : les données (une string) au format JSON ;
* argument 2 : le namespace de la classe de l'objet à obtenir (ici il s'agit de   `AppBundle\Entity\Article`) ;
* argument 3 : le format de données reçues (ici, il s'agit de JSON).

```
$article = $this->get('jms_serializer')->deserialize($data, 'AppBundle\Entity\Article', 'json');
```

:small_blue_diamond: On récupère l'EntityManager afin de persist puis flush le nouvel article

```
$em = $this->getDoctrine()->getManager();
$em->persist($article);
$em->flush();
```

:small_blue_diamond: On retourne une réponse. Si la ressource a été créée avec succès, on renvoie un [status code 201](https://developer.mozilla.org/fr/docs/Web/HTTP/Status/201) via la constante `HTTP_CREATED`.

```
return new Response('', Response::HTTP_CREATED);
```

[:link: code de la classe Response - GitHub Symfony](https://github.com/symfony/http-foundation/blob/master/Response.php)

### 3.3 Exercice : afficher la liste de tous les articles

Pour cela, on crée une méthode `listAction()` associée à une route en `GET`.

<details>
<summary>code de la méthode <code>listAction</code></summary>
<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticleController extends Controller
{
    // ..

    /**
     * @Route("/articles", name="article_list", methods={"GET"})
     */
    public function listAction()
    {
        // on récupère tous les articles en base à partir du repository de l'entité Article
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findAll();

        // on sérialise les datas à partir des objets de l'entité Article
        $data = $this->get('jms_serializer')->serialize($articles, 'json');

        // on retourne une réponse
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
</details>

<br />

**Explications du code de la méthode `listAction` :**

On récupère en base l'ensemble des articles grâce à Doctrine (méthode `findAll()` appliquée au `Repository` de l'entité `Article`), puis on sérialise la collection d'articles avec le service `jms_serializer`, avant de retourner une réponse.