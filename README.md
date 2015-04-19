TaT (Trends Analysis Twitter)
=========================

Ce projet a été effectuée dans le cadre du projet de 3ème année à l'INSA de Rennes.
L'objectif est de développer une interface permettant d'analyser en temps réel les tendances de Twitter.
Vous pouvez retrouver la [page de présentation](http://trends-analysis-twitter.tk/) du projet si vous voulez plus d'informations ainsi que des démonstrations.

Pré-requis
----------

Le système est conçu et testé sous Ubuntu Server 14.10 mais il doit être compatible avec des versions antèrieures d'Ubuntu ou toute autre version de Linux.
Il est possible de le tester sous Windows avec [Wamp](http://www.wampserver.com/) par exemple. Il faut noter que certaines fonctions ne sont pas disponibles (telle que la planification de campagnes).

Versions minimales :

- MySQL 5.5
```bash
sudo apt-get install
```

- Apache 2.4
```bash
sudo apt-get install
```

- PhP 5.0
```bash
sudo apt-get install
```

- Java 1.7
```bash
sudo apt-get install
```

TaT se repose sur cron pour la planification de campagnes.

Il se peut que TaT fonctionne correctement sur de sversion antèrieures, cependant nosu garantissons le bon fonctionnement de TaT sous les versions mentionnées plus haut.

Installation
------------

Avant toute chose, il faut s'assurer que tous les pré-requis sont installés.
Il suffit d'installer le projet dans un dossier en s'assurant que l'utilisateur php (en général www-data) possède tous les droits nécessaires à l'exécution et la création de fichiers.

Il faut remplir le fichier credentials.php. Si vous ne possédez pas de 
