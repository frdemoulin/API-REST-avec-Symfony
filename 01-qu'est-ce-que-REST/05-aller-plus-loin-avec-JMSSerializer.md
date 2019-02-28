[:link: Aller plus loin avec JMSSserializer - cours OCR](https://openclassrooms.com/fr/courses/4087036-construisez-une-api-rest-avec-symfony/4302366-allez-plus-loin-avec-jmsserializer) - [:link: annotations avec JMSSerializer - doc JMSSerializer](http://jmsyst.com/libs/serializer/master/reference/annotations)

# Aller plus loin avec JMSSerializer

On aborde ici la personnalisation / configuration de JMSSerializer.

## 1. La configuration

### Politique d'exclusion

La politique d'exclusion permet de ne pas sérialiser un champ donné (ils le sont tous par défaut).

**Approche :** pour retirer un champ, il faut tout exclure, puis n'exposer que les éléments que l'on souhaite retrouver. C'est dans les annotations que cela se passe !

**Code de l'entité Article avec exclusion du champ id :**

```
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table()
 *
 * @Serializer\ExclusionPolicy("ALL")
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
     *
     * @Serializer\Expose
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     *
     * @Serializer\Expose
     */
    private $content;

    //…
}
```

**Explications :**

:small_blue_diamond: On exclut tous les champs de la classe `Article` via l'annotation `@Serializer\ExclusionPolicy("All")` sur la classe elle-même.

:small_blue_diamond: On ajoute l'annotation `@Serializer\Expose` à tous les champs à sérialiser (donc tous sauf id).

### Groupe de sérialisation

On peut offrir des vues différentes à une ressource à l'aide des groupes de sérialisation. Pour cela, on ajoute l'annotation `@Groups` sur les champs à rattacher à un ou plusieurs groupes.

**Exemple :** afficher tous les champs des ressources avec la méthode `listAction` mais ne pas afficher l'id avec la méthode `showAction`.

Pour cela, on crée deux groupes `detail` et `list`.

**Code de l'entité Article avec création des groupes `detail` et `list` :**

```
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

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
     *
     * @Serializer\Groups({"list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Serializer\Groups({"detail", "list"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     *
     * @Serializer\Groups({"detail", "list"})
     */
    private $content;
 
    //…   
}
```

:warning: La politique d'exclusion et les groupes de sérialisation ne font pas bon ménage (il est nécessaire de supprimer les annotations `@Serializer\ExclusionPolicy("ALL")` et  `@Serializer\expose` pour ne jouer qu'avec les groupes de sérialisation).

**Explications :** on a créé deux groupes de sérialisation :
* `detail` contenant les champs `title` et `content` ;
* `list` contenant en plus l'id.

Pour indiquer au serializer de ne sérialiser que les champs du groupe `detail`, il faut modifier le contrôleur `ArticleController` de la sorte :

```
<?php

namespace AppBundle\Controller;

//…
use JMS\Serializer\SerializationContext;

class ArticleController extends Controller
{
    /**
     * @Route("/articles/{id}", name="article_show")
     */
    public function showArticleAction(Article $article)
    {
        $data = $this->get('jms_serializer')->serialize($article, 'json', SerializationContext::create()->setGroups(array('detail')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    // …
}
```

**Explications :** c'est en troisième paramètre de la méthode `serialize` que l'on indique à quel(s) groupe(s) on souhaite que le serializer fasse appel :

```
$data = $this->get('jms_serializer')->serialize($article, 'json', SerializationContext::create()->setGroups(array('detail')));
```

On modifie de façon anlagoue la méthode `listAction` afin qu'elle ne sérialise id, title et content :

```
<?php

namespace AppBundle\Controller;

// …

class ArticleController extends Controller
{
    // …

    /**
     * @Route("/articles", name="article_create", methods={"GET"})
     */
    public function listArticleAction()
    {
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findAll();

        $data = $this->get('jms_serializer')->serialize($articles, 'json', SerializationContext::create()->setGroups(array('list')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}

```

## 2. Aller au-delà de la configuration

@TODO

### Travailler avec les events

### Serializer Handler

### La méthode `serialize`

### La méthode `deserialize`

