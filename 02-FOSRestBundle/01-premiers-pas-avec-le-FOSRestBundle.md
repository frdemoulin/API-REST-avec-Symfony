[:link: Premiers pas avec le FOSRestBundle - cours OCR](https://openclassrooms.com/fr/courses/4087036-construisez-une-api-rest-avec-symfony/4320271-premiers-pas-avec-le-fosrestbundle) - [:link: FOSRestBundle et Symfony à la rescousse - zestedesavoir](https://zestedesavoir.com/tutoriels/1280/creez-une-api-rest-avec-symfony-3/developpement-de-lapi-rest/fosrestbundle-et-symfony-a-la-rescousse/)

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

En se plaçant dans le dossier du projet, on installe FOSRestBundle avec composer via la commande : `composer require friendsofsymfony/rest-bundle`.

On déclare ensuite le bundle dans la classe `app/AppKernel.php` :

<details>
<summary><b>Code de <code>AppKernel.php</code></b></summary>
<p>

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
            new FOS\RestBundle\FOSRestBundle(),
        ];

        //…

        return $bundles;
    }

    //…
}
```
</p>
</details>
<br/>

## 3. Configuration

### 3.1. Configuration de base

La configuration du FOSRestBundle se fait dans le fichier `app/config/config.yml` à la clé `fos_rest` :

```yml
#app/config/config.yml

fos_rest:
    body_converter:
        enabled: true
    view:
        formats: { json: true, xml: false, rss: false }
    serializer:
        serialize_null: true
```

**Explications :**

:small_blue_diamond: `fos_rest.body_converter.enabled` : activation de la désérialisation automatique du contenu de la requête. Concrètement, on sera en mesure de recevoir un objet déjà désérialisé en paramètre d'une action, à condition d'ajouter une annotation particulière (vue plus tard) ;

:small_blue_diamond: `fos_rest.view.formats` : indication des formats à gérer pour la sérialisation (on se limite ici au JSON) ;

:small_blue_diamond: `fos_rest.serializer.serialize_null` : par défaut, un champ `null` dans un objet est ignoré lors de la sérialisation. On ne veut pas de ce fonctionnement ici.

:warning: Il faut désactiver l'annotation `@View` du SensioFrameworkExtraBundle pour éviter les collisions avec l'annotation `@View` du FOSRestBundle :

```yml
# app/config/config.yml

sensio_framework_extra:
    view: { annotations: false }
```

### 3.2. Configuration du serializer

On indique le type de serializer à utiliser.

**Option 1 :** utiliser le composant Serializer de Symfony

```yml
# app/config/config.yml
framework:
    # ...
    serializer:
        enabled: true
```

**Option 2 :** utiliser le JMSSerializerBundle

Si l'on souhaite utiliser le bundle JMSSerializerBundle, il suffit d'installer le bundle et le déclarer dans la classe `AppKernel`. Le bundle déclare un alias sur le service serializer de manière à ce que le service `jms_serializer` soit appelé.

**Option 3 :** utiliser un tout autre serializer

Il suffit d'indiquer le service à utiliser dans la configuration du bundle FOSRestBundle :

```yml
# app/config/config.yml

fos_rest:
    service:
        serializer: your_serializer.service
```

:warning: Il est nécessaire de configurer un serializer sous peine d'avoir l'erreur suivante : 

```
[InvalidArgumentException] Neither a service called "jms_serializer.serializer" nor "serializer" is available and no serializer is explicitly configured.
You must either enable the JMSSerializerBundle, enable the FrameworkBundle serializer or configure a custom serializer.
```

### Afficher toute la configuration du FOSRestBundle

On peut consulter toute la configuration du bundle en tapant la commande : `php bin/console config:dump-reference fos_rest`
