<?
  // configuration de la base de données
  define("DB_USER", "utilisateur_mysql");
  define("DB_HOST", "localhost");
  define("DB_NAME", "nom_de_la_base");
  define("DB_PASS", "mod_de_passe_mysql");

  // titre du site
  define('SITE_TITLE', 'Cover generator');

  // termes bannis pour la génération d'images
  // (pas la peine de mettre toutes les combinaisons majuscules/minuscule
  // par contre il faut gerer les cas avec/sans accents)
  $banned_words = array('pipi', 'caca');

  // IP des administrateurs (accès à la page d'administration et à la suppression)
  // Attention : si d'autres personnes se connectent depuis ces IP, ils auront
  // aussi accès aux pages d'administration.
  $admin_ips = array('127.0.0.1', '127.0.0.2');

  // nombre d'images par page
  define("NB_PER_PAGE", 10);

  // nombre d'entrees par page (admin)
  define("NB_PER_PAGE_ADMIN", 100);

  // suppression automatique des images candidates à la galerie (et non acceptées)
  // au bout d'un certain nombre d'heures. Si 0 : les images sont conservées.
  define('HOURS_CANDIDATES_KEPT', 0);

  // nombre d'heures minimum entre chaque candidature, par IP
  // mettre 0 pour n'imposer aucune limitation
  define('SUBMIT_DELAY', 0);

  // autorise la participation à la galerie
  define('ALLOW_GALLERY', true);

  // annonce visible sur toutes les pages du site (pensez as escaper les apostrophes)
  define('ANNONCE', 'C\'est parti !');

  // credits en bas de page
  define('CREDITS', '<strong>Credits </strong>- Personne ! Niark Niark !');

  // couvertures vierges :
  //  picture : image taille réelle
  //  thumb : image miniature
  //  x_min, y_min : coordonnées du coin supérieur gauche de la zone "vierge"
  //  x_max, y_max : coordonnées du coin inférieur droit de la zone "vierge"
  //  color : couleur du texte
  //  font : chemin vers la fonte TTF à utiliser
  //
  // Ne pas oublier d'incrémenter l'index de tableau pour chaque couverture

$pictures = array(
    1 => array(
      'picture' => 'exemple.jpg',
      'thumb'  => 'exemple-miniature.jpg',
      'x_min' => 7,
      'x_max' => 371,
      'y_min' => 8,
      'y_max' => 77,
      'font' => 'includes/liftarn.ttf',
      'color' => array(148, 29, 61)),
    );
?>
