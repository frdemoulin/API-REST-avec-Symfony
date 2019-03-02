# La désérialisation

[:link: La désérialisation - cours OCR](https://openclassrooms.com/fr/courses/4087036-construisez-une-api-rest-avec-symfony/4322086-la-deserialisation)

## 1. Travailler avec le body converter

**Démarche pratique :** un client fait appel à l'API pour créer un article. Le client doit envoyer toutes les informations nécessaires à la création de l'objet Article.

Le fichier `app/config/config.yml` doit contenir la configuration suivante :

```yml
# app/config/config.yml

sensio_framework_extra:
    request: { converters: true }
    
fos_rest:
    body_converter:
        enabled: true
```

Grâce au ParamConverter spécial provenant du FOSRestBundle, la conversion de JSON (ou XML) vers un objet PHP est faite automatiquement.

On crée la méthode de création d'un article.

<details>
<summary><b>Code de la méthode <code>createAction</code></b></summary>
<p>

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ArticleController extends Controller
{
    /**
     * @Rest\Post("/articles")
     * @Rest\View
     * @ParamConverter("article", converter="fos_rest.request_body")
     */
    public function createAction(Article $article)
    {
        dump($article); die;
    }
}
```
</p>
</details>

<br />

## 2. Utiliser le composant Form pour manipuler les informations postées

## 3. Valider les paramètres envoyés par l'utilisateur en GET et POST