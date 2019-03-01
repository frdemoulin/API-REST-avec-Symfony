[:link: La sérialisation avec le composant Serializer de Symfony - cours OCR](https://openclassrooms.com/fr/courses/4087036-construisez-une-api-rest-avec-symfony/4302521-la-serialisation-avec-le-composant-serializer-de-symfony)

# La sérialisation avec le composant Serializer de Symfony

FOSRestBundle est utile pour organiser l'ensemble d'une application Symfony exposant une API REST. Il facilite l'intégration de JMSSerializer. Il est cependant possible d'utiliser le composant Serializer par défaut de Symfony

## 1. Travailler avec le composant Serializer de Symfony

### 1.1. Activer le Serializer de Symfony

Pour pouvoir utiliser le Serializer, on doit l'activer en configuration. Pour cela, on ajoute la configuration suivante dans le fichier `app/config/config.yml` :

```
framework:
    serializer:
        enabled: true
```

### 1.2. Sérialisation

On se propose de créer une nouvelle entité `Author` comportant des attributs id, fullname, biography et articles.

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
 *
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
     * @ORM\Column
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

On met à jour l'entité `Article` en conséquence afin d'y associer l'entité `Author` avec une relation ManyToOne.

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
 *
 */
class Article
{
    //…

    /**
     * @ORM\ManyToOne(targetEntity="Author", cascade={"all"}, fetch="EAGER")
     */
    private $author;

    // …

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

La commande `php bin/console doctrine:schema:update --dump-sql` permet de visualiser les requêtes SQL afin de mettre à jour la base de données. On met réellement le schéma de la base de données à jour avec la commande `php bin/console doctrine:schema:update --force`.

On crée alors le contrôleur en charge de la présentation d'un auteur.

<details>
<summary><b>Code de <code>AuthorController</code></b></summary>
<p>

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Author;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends Controller
{
    /**
     * @Route("/authors/{id}", name="author_show")
     */
    public function showAction()
    {
        // on récupère l'article d'id 1
        $article = $this->getDoctrine()->getRepository('AppBundle:Article')->findOneById(1);

        // pas d'auteur en base, donc on en crée l'un que l'on attache ensuite à l'article d'id 1
        $author = new Author();
        $author->setFullname('Sarah Khalil');
        $author->setBiography('Ma super biographie.');
        $author->getArticles()->add($article);

        $data = $this->get('serializer')->serialize($author, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
```
</p>
</details>
<br />

La méthode `serialize` du service `Serializer` prend deux paramètres :
* argument 1 : les données à sérialiser (ici, l'objet `$author`) ;
* argument 2 : le format dans lequel sérialiser (ici, JSON).

```php
$data =  $this->get('serializer')->serialize($author, 'json');
```

### 1.3. Désérialisation

On se propose de créer une méthode de création d'un nouvel auteur.

<details>
<summary><b>Code de la méthode <code>createAction</code></b></summary>
<p>

```php
/**
     * @Route("/authors", name="author_create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        // on récupère les datas reçues en POST
        $data = $request->getContent();

        // on désérialise les datas en hydratant un objet de l'entité Author
        $author = $this->get('serializer')->deserialize($data, 'AppBundle\Entity\Author', 'json');

        // on récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        // on persist puis on flush le nouvel auteur
        $em->persist($author);
        $em->flush();

        $response = new Response('', Response::HTTP_CREATED);

        return $response;
    }
```
</p>
</details>
<br />

La méthode `deserialize` du service `Serializer` prend trois paramètres :
* argument 1 : les données à désérialiser (ici, l'objet `$data`) ;
* argument 2 : le namespace de l'entité à hydrater (ici, celui de l'entité `Author`) ;
* argument 3 : le format dans lequel sérialiser (ici, JSON).

```php
$author = $this->get('serializer')->deserialize($data, 'AppBundle\Entity\Author', 'json');
```

## 2. Pour aller plus loin

Il est possible d'étendre le Serializer de Symfony via deux éléments centraux :

* le **normalizer**, une classe qui est en charge de la transformation d'un objet en un tableau ;
* l'**encoder**, une classe qui est en charge de la transformation de la donnée normalisée (tableau) en une chaîne de caractères (json/xml).

Le composant de base gère déjà tout ce qu'il faut pour le JSON et l'XML. Si l'on souhaite manipuler d'autres formats, les normalizers et les encoders sont faits pour cela.

[:link: utilisation du Serializer - doc Symfony](https://symfony.com/doc/current/serializer.html) - [:link: documentation du Serializer - doc Symfony](https://symfony.com/doc/current/components/serializer.html) - [:link: le composant Serializer - présentation SymfonyLive Paris 2017](https://speakerdeck.com/lyrixx/symfony-live-2017-serializer)