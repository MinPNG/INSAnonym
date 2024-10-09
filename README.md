# INS'Anonym

INS'Anonym est une platforme d'anonymisation compétitive

![logo-min](https://user-images.githubusercontent.com/23292338/94370662-8a291a80-00f1-11eb-9e1c-b453dddcedc1.png)

## Description

Le but de ce projet est de réaliser une plateforme permettant d'héberger une compétition d'anonymisation.

Le plateforme permet à l'organisateur de déposer un fichier initial non-anonymisé (ex: données GPS, bornes GSM/Wifi).
Puis les équipes téléchargent le fichier déposé sur la plateforme, elles l'anonymisent avec leurs algorithmes, et déposent les données anonymisées.
La plateforme attribue un score pour l'utilité, et la résistance aux attaques basiques des données anonymisés.

Les équipes peuvent ensuite essayer de désanonymiser les données des autres équipes, ce qui leur donne un score d'attaque en fonction de la quantité de données désanonymisés.
Les équipes reçoivent aussi un score de défense en fonction de leur résistance aux attaques des autres équipes.

L'équipe gagnante est celle qui a obtenu le meilleur score total.

## Guide d'utilisateur

Le *guide d'utilisateur* est disponible via le lien ci-dessous:
https://docs.google.com/document/d/e/2PACX-1vRsWLIR94CK-C_xYdUkOQA_OuL-BKQkW3dqUfbo4XIX4VpXYH961H7YQROf3r3LbQ/pub

Le *guide d'administrateur* est disponible ici:
https://docs.google.com/document/d/e/2PACX-1vTvj19vg31a2kr45CTuwh2zVkcGcL67x5zYU7dfgGz_bPJn5c07RVlLb4zPI5pyWg/pub

## Fichiers d'exemple

Exemple d'un fichier d'attaque qui peut être envoyé sur la plateforme:
https://docs.google.com/document/d/e/2PACX-1vTiYIYGasdf3aawHHPyTH6KZHBj61xEXNqjbtqfLiHS8xWhMI_CjjmYF4vLD65beo_r1O9NT_KsTlzg/pub

## Utilitaires pour le projet

Nous avons développé une librairie python capable d'aider à la création d'algorithmes d'anonymisation et d'attaques: [INSAnonym-utils](https://github.com/danymat/INSAnonym-utils)

## Avancement du projet

![casNominalParticipant (6)](https://user-images.githubusercontent.com/59082879/98948379-a2fd5c00-24f6-11eb-9199-79240e7311d3.png)

![casNominalOrganisateur (3)](https://user-images.githubusercontent.com/59082879/99880113-a63cca00-2c11-11eb-9d92-1786f5731c84.png)

## Installation

L'installation de la plateforme de compétition peut se faire manuellement en installant les différents services utilisés par INSAnonym. C'est-à-dire un serveur web (Apache2 ou Nginx), php7 et un serveur SQL. *La méthode d'installation manuelle n'est pas recommandée.*
Afin de faciliter l'installation, seule la méthode via image Docker pré-configuré est détaillée ici.

##### Via Docker:
- Pré-condition: Installez Docker / Docker-Compose:

https://docs.docker.com/engine/install/ et https://docs.docker.com/compose/install/

- Clonez le repertoire Git :
```shell
$ git clone https://github.com/machneit/INSAnonym.git
$ cd INSAnonym
```
- Exécution de l'instance Docker (Compile et lance la platforme INSAnonym):

```shell
$ docker-compose up -d --build
```

*Les ports réseaux sont configurable dans le fichier **docker-compose.yml**.*
*Exemple: [port1]:[port2], le port2 sera redirigé vers le port1 sur la machine réel.*

- Pour arrêter la platforme:
-
```shell
$ docker-compose down
```

En tant qu'administrateur **(admin:@dminDARC2021)**, l'accès à la base de donnée est disponible à partir de l'url: https://localhost/admin/database

- Si besoin, il est possible lancer une console dans l'instance docker (modification des scripts d'utilité par exemple):
```shell
$ docker-compose ps
$ docker exec -it [name] /bin/bash (Avec [name] remplacé pour le nom de l'instance Docker)
```
BN : /!\ attention actuellement il y a un soucis avec le répertoire des logs de rabbitmq : les droits ne sont pas bons. Il faut donc créer un autre répertoire et faire la modif dans le fichier docker-compose.yml

Il vous faut maintenant vous connecter à RabbitMQ afin de pouvoir gérer la file d'attente des uploads.
L'utilisateur par défaut est **guest:guest**.

1. Créez un utilisateur administrateur, attribuez lui les droits.
2. supprimez guest.
3. Reconnectez-vous avec le nouvel utilisateur, et créez un utilisateur nommé *python_server* avec le mot de passe *guest*, attribuez-lui les droits.
4. Créer une nouvelle Queue nommée python.
```
**L'installation est terminée !
La plateforme de compétition est disponible à l'adresse: https://localhost:443/**

## Architecture technique

Schéma du modèle MVC utilisé
![Modèle Vue Controlleur](https://user-images.githubusercontent.com/59082879/95556892-4ea51f00-0a14-11eb-9415-46c38e4760d8.png)


## Auteurs
* **Thomas LAVIGNE**
* **Arnaud FEVRIER**
* **Alexandre GIARD**
* **Jean-Baptiste LABAT**
* **Antoine MOUTONNET**
* **Daniel MATHIOT**
