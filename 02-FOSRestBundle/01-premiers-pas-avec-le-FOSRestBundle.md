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
## 3. Configuration
