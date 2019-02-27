[Le rôle de Symfony - cours OCR](https://openclassrooms.com/fr/courses/4087036-construisez-une-api-rest-avec-symfony/4280576-le-role-de-symfony)

# Le rôle de Symfony

L'architecture REST repose sur le protocole HTTP. Cela tombe bien, Symfony est un framework dit HTTP. C'est justement ce dont on a besoin pour le fondement d'une API REST !

Même si Symfony est une framework full stack, on commence par se concentrer sur deux composants fondamentaux sur lesquels tous les développements d'une API vont reposer : HTTPFoundation et HTTPKernel.

## 1. Représentation de la requête et de la réponse HTTP : le composant HttpFoundation

### Requête HTTP

Symfony offre une représentation objet de la requête HTTP avec la classe `Symfony\Component\HttpFoundation\Request`. On manipule la requête HTTP (simple texte à la base) sous la forme d'un objet `request`. Pour cela, on peut typehinter l'un des paramètres d'une méthode de contrôleur avec le type `Symfony\Component\HttpFoundation\Request`.

```
<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class DefaultController
{
    public function myAction(Request $request)
    {
        // Nous avons accès à la requête courante dans la variable $request.
    }
}
```

:bulb: Il est également possible d'accéder à la requête grâce au service `request_stack` en procédant de la sorte : `$this->get('request_stack')->getCurrentRequest()`.

Pas nécessaire dans un contrôleur ! L'utilisation de ce service est utile lorsqu'il existe plusieurs requests en cours (après un forward d'une action à une autre par exemple. Ce service est également utile pour accéder à la requête depuis un autre service par exemple.

### Réponse HTTP

Symfony offre une représentation objet pour la réponse HTTP avec la classe `Symfony\Component\HttpFoundation\Response`. Il faut toujours renvoyer une réponse. Il existe plusieurs manières de le faire depuis un contrôleur.

#### 1. Instancier la classe `Response` par soi-même

```
<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function myAction()
    {
        return new Response('Le contenu de ma réponse');
    }
}
```

:bulb: Il existe d'autres classes dérivant de la classe `Response` :
* `Symfony\Component\HttpFoundation\BinaryFileResponse` ;
* `Symfony\Component\HttpFoundation\JsonResponse` ;
* `Symfony\Component\HttpFoundation\StreamedResponse`.

#### 2. Utiliser la méthode `render()` du contrôleur provenant du `FrameworkBundle`

```
<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function myAction()
    {
        return $this->render('default/myTemplate.html.twig');
    }
}
```

### 3. Utiliser l'annotation spéciale `@Template` (non recommandé)

```
<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController
{
    /**
     * @Template("default/myTemplace.html.twig")
     */
    public function myAction()
    {
        return array();
    }
}
```

:bulb: Consulter les classes [`Request`](https://api.symfony.com/2.7/Symfony/Component/HttpFoundation/Request.html) et [`Response`](https://api.symfony.com/2.7/Symfony/Component/HttpFoundation/Response.html) de Symfony afin d'y découvrir leurs attributs, constantes et méthodes afin de travailler avec ces objets.

## 2. De la requête à la réponse HTTP : le composant HttpKernel

[composant HttpKernel - doc Symfony](https://symfony.com/doc/current/components/http_kernel.html)

Le composant HttpKernel est en charge de la conversion de la requête en réponse HTTP via le composant EventDispatcher.

![composant HttpKernel](https://user.oc-static.com/upload/2017/01/19/14848376128975_Capture%20d%E2%80%99e%CC%81cran%202017-01-19%20a%CC%80%2015.52.28.png)

Les évènements sur lesquels on peuit s'appuyer pour travailler avec la requête et la réponse sont recensés ici : [Les évènements Symfony… et les nôtres](https://openclassrooms.com/courses/developpez-votre-site-web-avec-le-framework-symfony/le-gestionnaire-d-evenements-de-symfony#/id/r-3625174).

Il est ainsi possible à tout moment de créer un event listener (ou un event subscriber, c'est selon) pour récupérer, modifier la requête et/ou créer une réponse.

