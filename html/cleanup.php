<?
  require_once('config.inc.php');
  require_once('includes/generator.inc.php');

  $db = mysql_pconnect(DB_HOST, DB_USER, DB_PASS)
    or die('Erreur MySQL');
  mysql_select_db(DB_NAME, $db)
    or die('Erreur MySQL');

  // purge de la file de modération
  if (HOURS_CANDIDATES_KEPT != 0)
  {
    $sql = "SELECT md5
      FROM visuel
      WHERE candidate=1
      AND accepted=0
      AND NOW() > DATE_ADD(`date`, INTERVAL ".HOURS_CANDIDATES_KEPT." HOUR)";
    $results = mysql_query($sql)
      or die('Erreur MySQL');
    while ($visuel = mysql_fetch_array($results))
      delete_image($visuel['md5']);
  }

  // purge des images non candidates
  $sql = "SELECT md5
    FROM visuel
    WHERE candidate=0
    AND accepted=0
    AND NOW() > DATE_ADD(`date`, INTERVAL 10 MINUTE)";
  $results = mysql_query($sql)
    or die('Erreur MySQL');
  while ($visuel = mysql_fetch_array($results))
    delete_image($visuel['md5']);
?>
