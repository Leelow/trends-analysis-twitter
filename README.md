TaT (Trends Analysis Twitter)
=========================

Ce projet a été effectuée dans le cadre du projet de 3ème année à l'INSA de Rennes.
L'objectif est de développer une interface permettant d'analyser en temps réel les tendances de Twitter.
Vous pouvez retrouver la [page de présentation](http://trends-analysis-twitter.tk/) du projet si vous voulez plus d'informations ainsi que des [démonstrations](http://trends-analysis-twitter.tk/panel/).

Pré-requis
----------

Le système est conçu et testé sous Ubuntu Server 14.10 mais il doit être compatible avec des versions antèrieures d'Ubuntu ou toute autre version de Linux.
Il est possible de le tester sous Windows avec [Wamp](http://www.wampserver.com/) par exemple. Il faut noter que certaines fonctions ne sont pas disponibles (telle que la planification de campagnes).

Avant toute chose, assurez-vous que votre système est à jour :
```bash
sudo apt-get upgrade
sudo apt-get update
```


Versions minimales :

- PhP 5.0+ (extension PDO requise)
```bash
sudo apt-get install php5
```

- Apache 2.4+
```bash
sudo apt-get install apache2
```

- MySQL 5.5+
```bash
sudo apt-get install mysql-server libapache2-mod-auth-mysql php5-mysql
```

- Java 1.7
```bash
sudo apt-get install openjdk-7-jre
```

TaT se repose sur cron pour la planification de campagnes.

Il se peut que TaT fonctionne correctement sur de sversion antèrieures, cependant nosu garantissons le bon fonctionnement de TaT sous les versions mentionnées plus haut.

Installation
------------

Avant toute chose, il faut s'assurer que tous les pré-requis sont installés.
Il suffit d'installer le projet dans un dossier en s'assurant que l'utilisateur php (en général www-data) possède tous les droits nécessaires à l'exécution et la création de fichiers dans ce repertoire.

Étape 1
-------

TaT utilise l'API [OAuth](https://dev.twitter.com/oauth) et [Streaming](https://dev.twitter.com/streaming/overview) de Twitter et nécessite donc des clés d'API. Vous pouvez en obtenir facilement en suivant les consignes données [ici](https://themepacific.com/how-to-generate-api-key-consumer-token-access-key-for-twitter-oauth/994/).

Étape 2
-------

Il faut ensuite configurer le fichier credentials.php qui contient l'ensemble des identifiants/clés de l'application :

Il suffit de ré-utiliser les clés obtenues à l'étape 1 :
```php
  define('TWITTER_CONSUMER_KEY',    '');
  define('TWITTER_CONSUMER_SECRET', '');
	
  define('OAUTH_TOKEN',  '');
  define('OAUTH_SECRET', '');
```

D'indiquer les identifiants de la base de données :
```php
  define('BDD_HOST',     '');
  define('BDD_LOGIN',    '');
  define('BDD_PASSWORD', '');
```

Puis de changer les identifiants de l'interface web si vous le souhaitez :
```php
  define('PANEL_LOGIN',    'admin');
  define('PANEL_PASSWORD', 'admin');
```

Étape 3
-------

La dernière étape consiste à exécuter le script d'installation qui initialise la base de données :
```bash
  sudo php scripts/installation.php
```

Utilisation
-----------

Si tout s'est bien déroulé, vous pouvez facilement accéder à l'interface web et utiliser TaT !

Bibliothèques utilisées
-----------------------

- [Algortihme TF-IDF](https://github.com/valent141/trends-analysis-twitter-tfidf)
- [Algorithme Newman-Girvan](https://github.com/ElyKar/Trends-Analysis)
- [Phirehose](https://github.com/fennb/phirehose)
- [Codebird](https://github.com/jublonet/codebird-php)