Cover Generator

PREAMBULE
---------

Les sources de ce générateur sont fournies en l'état, sans garantie, et sans
plus de documentation que ce fichier, et les commentaires directement inclus
dans le code source. Il est donc nécessaire d'avoir des connaissances correctes
en PHP pour mettre en place ce générateur.

L'auteur renonce à tous ses droits sur ce code source, ils sont reversés au
domaine public. Vous pouvez donc en faire ce que bon vous chante, y compris
l'imprimer sur du papier A4 et en faire des cocottes en papier que vous pourrez
ensuite vendre à la sortie du métro. Attention cependant : gaspiller du papier,
c'est mal.

Le générateur contient sans doute des bugs, et certaines fonctions sont codées
à la va-vite. L'auteur décline donc toute responsabilité en cas de problème.


PRE-REQUIS
----------
- Linux
- Apache
- PHP (avec notamment les extensions MySQL et GD)
- MYSQL
- Beaucoup de café


INSTALLATION
------------

1 - CREATION DE LA BASE DE DONNEES

  Vous devez tout d'abord créer une base de données MySQL et un utilisateur
  ayant tous les droits sur cette base. Vous pouvez ensuite créer les tables
  nécessaires en chargeant le script fourni (sql/generator.sql).

2 - CREATION DES PAGES WEB

  Il suffit de copier l'intégralité des fichiers du répertoire "html" a la
  racine de votre serveur Web.


CONFIGURATION DU SITE
---------------------

La majorité du paramétrage du site s'effectue dans le fichier config.inc.php.
Toutes les options ne sont pas décrites dans cette documentation, mais elles
le sont dans le fichier lui-même (sous forme de commentaires PHP).

1 - AJOUTER UN MODELE DE COUVERTURE

  Pour proposer un nouveau modèle de couverture à vos visiteurs, vous devez
  d'abord créer une couverture sans titre, ainsi qu'une miniature de cette
  couverture (les deux au format JPEG).
  
  Une largeur maximale de 500 pixels est recommandée pour le modèle taille
  réelle. Pour la miniature, il est préférable de définir choisir une largeur
  fixe pour que tous les modèles puisse s'afficher correctement sous forme de
  mosaïque. 150 pixels de large est un choix optimal.
  
  Placez les deux images dans le dossier images/genuine.

  Vous devez ensuite paramétrer ce modèle de couverture dans le fichier
  config.inc.php. Les différents paramètres sont indiqués dans le fichier.
  Il s'agit d'indiquer le nom des deux images, les coordonnéesdu rectangle dans
  lequel le titre doit d'inscrire, la fonte a utiliser, et la couleur du titre.

  L'exemple fourni devrait vous aider à comprendre comment cela fonctionne.

2 - PAGE D'INFORMATIONS

  Pour modifier la page "A propos du site", vous devez directement éditer le
  fichier about.php.

3 - PAGE D'ADMINISTRATION

  Si vous avez entré votre IP dans la listes des IP d'administrateurs du fichier
  config.inc.php, vous verrez déjà un lien "supprimer" au dessous de chaque
  couverture de la galerie.

  Par ailleurs, vous aurez accès à la page admin.php qui vous permet de traiter
  les couvertures en attente de modération, et d'effectuer des recherches selon
  divers critères.
  

CONFIGURATION SYSTEME
---------------------

Pour que le générateur fonctionne de manière optimale, plusieurs paramétrages
système sont nécessaires.

1 - DROITS D'ACCES

  Apache doit pouvoir écrire dans deux répertoires spécifiques pour créer
  les couvertures. Il s'agit de :

  - images/full
  - images/thumbs

  Le premier contient les couvertures générées en grand format, le second au
  format "miniatures".

2 - TACHES AUTOMATISEES

  La suppression automatique des couvertures non candidates a la galerie
  s'effectue par l'appel régulier à un script PHP : cleanup.php.

  Vous pouvez bien évidemment le faire à la main, en consultant régulièrement
  la page cleanup.php de votre site, mais il est plus simple de l'automatiser
  en créant un cronjob (cet exemple necessite l'installation de curl) :

  */5 * * * *     /usr/bin/curl http://www.votresite.com/cleanup.php

3 - INTERDICTION DES HOTLINKS

  Si vous ne souhaitez pas que les couvertures générées puissent être
  directement liées dans d'autres pages que celles de votre site, vous
  pouvez décommenter les lignes des fichiers :

  images/full/.htaccess
  images/thumbs/.htaccess

  N'oubliez pas d'adapter la troisème ligne à l'url de votre site.


CONTACT
-------

Vous pouvez contacter l'auteur à l'adresse : deelight@logeek.com
