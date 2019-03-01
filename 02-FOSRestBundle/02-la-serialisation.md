[:link: La sérialisation - cours OCR](https://openclassrooms.com/fr/courses/4087036-construisez-une-api-rest-avec-symfony/4320271-premiers-pas-avec-le-fosrestbundle)

# La sérialisation

## 1. Les ressources que l'API va manipuler

On crée deux entités `Article` et `Author`. On définit une relation ManyToOne entre ces deux entités :
* un article est attaché à un unique (One) auteur ;
* un auteur peut être associé à un ou plusieurs (Many) articles.

`Article` 1,1 <-> 1,n `Author`

`Article` est l'owner de la relation.

Ainsi, dans l'entité `Article`, on fera figurer :

```php
/**
     * @ORM\ManyToOne(targetEntity="Author", cascade={"all"}, fetch="EAGER")
     */
    private $author;

    // ..

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(Author $author)
    {
        $this->author = $author;
    }
```

Et dans l'entité `Author` :

```php
/**
 * @ORM\OneToMany(targetEntity="Article", mappedBy="author", cascade={"persist"})
*/

    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getArticles()
    {
        return $this->articles;
    }
```

<details>
<summary><b>Code de l'entité <code>Article</code></b></summary>
<p>

```php
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

    /**
     * @ORM\ManyToOne(targetEntity="Author", cascade={"all"}, fetch="EAGER")
     */
    private $author;

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

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(Author $author)
    {
        $this->author = $author;
    }
}
```
</p>
</details>

<br />

<details>
<summary><b>Code de l'entité <code>Author</code></b></summary>
<p>

```php
<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
class Author
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $fullname;

    /**
     * @ORM\Column(type="text")
     */
    private $biography;

    /**
     * @ORM\OneToMany(targetEntity="Article", mappedBy="author", cascade={"persist"})
     */
    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    public function getBiography()
    {
        return $this->biography;
    }

    public function setBiography($biography)
    {
        $this->biography = $biography;
    }

    public function getArticles()
    {
        return $this->articles;
    }
}
```
</p>
</details>

<br />

## 2. La gestion des routes

[:link: Routing - doc Symfony FOSRestBundle](https://symfony.com/doc/current/bundles/FOSRestBundle/5-automatic-route-generation_single-restful-controller.html)

**Rappel des contraintes dans la gestion des routes avec une API :**
* une ressource doit avoir un identifiant unique ;
* chaque point d'entrée doit correspondre à un ou plusieurs verbes HTTP convenablement choisis.

Le FOSRestBundle propose des annotations pour gérer ces deux contraintes un peu plus rapidement que l'annotation `@Route` habituelle :
* @Prefix("/api")
* @Head("/articles")
* @Get("/articles/{id}")
* @Post("/articles")
* @Put("/articles/{id}")
* @Patch("/articles/{id}")
* @Delete("/articles/{id}")
* @Link("/articles/{id}/authors")
* @Unlink("/articles/{id}/authors")
* @Options("/authors")

:bulb: Dans le cas d'un point d'entrée accessible avec deux méthodes HTTP différentes, il suffit d'ajouter les annotations comme suit :

```php
<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;

class DefaultController
{
    /**
     * @Rest\Put("/articles/{id}")
     * @Rest\Post("/articles/{id}")
     */

    public function randomAction()
    {
    }  
}
```

On peut également gérer les requirements (contraintes sur les paramètres d'URL) de la sorte :

```php
<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;

class ArticleController
{
    /**
     * @Get(
     *     path = "/articles/{id}",
     *     name = "app_article_show",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function showAction()
    {
    }
}
```

## 3. Sérialiser une ressource

### 3.1. Principe

Avec le FOSRestBundle, on peut sérialiser une ressource sans faire appel au service `serializer` en ajoutant la configuration :

```yml
# app/config/config.yml
format_listener:
    rules:
        - { path: '^/', priorities: ['json'], fallback_format: 'json' }
```

**Explications :** pour toutes les routes commençant par `/`, les objets retournés par les actions devront être sérialisés en JSON en priorité. 

Il est possible de donner un ordre pour la sérialisation pour l'option  `priorities` : en fonction de ce qui sera demandé en requête via l'en-tête  `Content-Type`, FOSRestBundle sera en mesure de savoir dans quel format sérialiser la réponse. Si dans la requête le format n'est pas indiqué, c'est ce qui est indiqué dans l'option `fallback_format` qui prendra le relais.

On ajoute ensuite l'annotation `@View` à la méthode d'affichage d'un article :

```php
<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

class ArticleController
{
    /**
     * @Get(
     *     path = "/articles/{id}",
     *     name = "app_article_show",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     */
    public function showAction(Article $article)
    {
        return $article;
    }
}
```

Étant donné que cette action ne renvoit pas de réponse, on demande, grâce à la configuration suivante, que le FOSRestBundle fasse appel à un listener (ViewResponseListener en l'occurrence, à l’instar de Symfony via l’annotation Template du SensioFrameworkExtraBundle) en charge de récupérer l'objet retourné et qu'il effectue la sérialisation pour nous. On renvoie ainsi juste une instance de View et on laisse le bundle appeler le gestionnaire de vue lui-même :

```yml
# app/config/config.yml
fos_rest:
    view:
        #…
        view_response_listener: true
    # …
```

### 3.2. Options avec l'annotation `@View`

:small_blue_diamond: L'option `StatusCode` indique quel code status renvoyer pour la réponse.

:small_blue_diamond: L'option `serializerGroups` est un tableau prenant en paramètre les groupes de sérialisation à utiliser pour la sérialisation de l'objet retourné par l'action.

```php
<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;

class DefaultController
{
    /**
     * @View(
     *     statusCode = 201,
     *     serializerGroups = {"POST_CREATE"}
     * )
     */
    public function realExampleAction()
    {
        // …
        return $objectToBeSerialized;
    }   
}
```

### 3.3. Étendre la classe FOSRestController

On a vu comment utiliser le serializer sans avoir à appeler le service nous-mêmes. Il y a aussi l'option où, dans l'action, on instancie la réponse à la main. Et enfin, il reste une option : retourner une vue.

La classe `FOS\RestBundle\Controller\FOSRestController` est une classe abstraite utilisant un trait (`FOS\RestBundle\Controller\ControllerTrait`) contenant des méthodes permettant de manipuler une vue.

## 4. Négociation de contenu

Le FOSRestBundle est capable d'adapter la sérialisation des données pour rendre une réponse qui soit en adéquation avec ce qui est demandé dans la requête. Tout cela est possible grâce à la négociation de contenu.

### Qu'est-ce que la négociation de contenu ?

Une application doit être en mesure de pouvoir fournir plusieurs versions d'une ressource / document sur une même URI. De ce fait, le client spécifie la version qu'il souhaite recevoir grâce aux en-têtes de la requête HTTP :

```http
Accept: text/html; q=1.0, text/*; q=0.8
Accept-Language: fr;q=1.0, en-gb;q=0.8, en;q=0.7
Accept-Charset: iso-8859-5, unicode-1-1;q=0.8
User-Agent: CERN-LineMode/2.15 libwww/2.17b3
Vary: Accept 
```

**Explications :**

:small_blue_diamond: l'en-tête `Accept: text/html; q=1.0, text/*; q=0.8`  indique qu'en priorité le client souhaite obtenir du html, si ce n'est pas possible, tout ce qui est possible au format texte ;

:small_blue_diamond: l'en-tête `Accept-Language: fr;q=1.0, en-gb;q=0.8, en;q=0.7` indique que le client souhaite obtenir du contenu en français en priorité, si ce n'est pas possible, en anglais de Grande-Bretagne (en-gb) et enfin, en anglais si les deux premières possibilités ne peuvent être honorées ;

:small_blue_diamond: l'en-tête `Accept-Charset: iso-8859-5, unicode-1-1;q=0.8` indique dans quel ordre on souhaite obtenir l'encoding des caractères de la réponse. Tout d'abord iso-8859-5, puis unicode-1-1.

:small_blue_diamond: Le caractère `q` pour chacun des formats demandés correspond à la pondération. Plus elle est importante, plus le client le souhaite en priorité. Si aucune valeur n'est donnée à une demande, elle est au plus fort par défaut, c'est-à-dire 1.0.

Tout le travail de négociation de contenu est là : savoir jongler avec toutes les demandes dans une même requête et savoir ce que l'application (l'API) est capable de fournir afin qu'elle puisse générer une réponse qui soit en adéquation la plus logique avec ce qui est demandé.

Ainsi, si, en tant que client, on souhaite obtenir du JSON, il faut simplement ajouter l'en-tête `Accept : application/json` avec une grande priorité.

:bulb: Il existe une librairie utilisée par FOSRestBundle et capable de gérer les demandes faites en requête : [Negotiation](https://github.com/willdurand/Negotiation).