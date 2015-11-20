<?
  require_once('config.inc.php');

  $return = false;
  if (isset($_GET['r']))
    $return = true;

  // vote
  $vote = '';
  if (isset($_GET['j']))
    if (is_numeric($_GET['j']))
      if ($_GET['j']>=0 && $_GET['j']<=5)
        $vote = round($_GET['j']);
  if ($vote=='')
  {
    header('location: index.php');
    die();
  }

  // identifiant
  $md5 = '';
  if (isset($_GET['q']))
    $md5 = $_GET['q'];

  // filtre
  $filter = '';
  if (isset($_GET['f']))
    $filter = $_GET['f'];

  // search
  $search = '';
  if (isset($_GET['s']))
    $search = stripslashes($_GET['s']);

  // page
  $page = '';
  if (isset($_GET['p']))
    $page = $_GET['p'];

  // connexion base
  $db = mysql_pconnect(DB_HOST, DB_USER, DB_PASS)
    or die('Erreur MySQL');
  mysql_select_db(DB_NAME, $db)
    or die('Erreur MySQL');

  // verification du md5
  $sql = "SELECT md5
    FROM visuel
    WHERE md5='".addslashes($md5)."'";
  if (mysql_numrows(mysql_query($sql))!=1)
  {
    header('location: index.php');
    die();
  }
  else
  {
    // a deja voté ?
    $sql = "SELECT id
      FROM ratings_ip
      WHERE ip='".$_SERVER["REMOTE_ADDR"]."'
      AND id='".addslashes($md5)."'";
    if (mysql_numrows(mysql_query($sql, $db))==0)
    {
      $sql = "INSERT INTO ratings_ip
        (id, ip)
        VALUES ('".addslashes($md5)."', '".$_SERVER["REMOTE_ADDR"]."')";
      mysql_query($sql, $db)
        or die('Erreur MySQL');
      $sql = "SELECT id
        FROM ratings
        WHERE id='".addslashes($md5)."'";
      if (mysql_numrows(mysql_query($sql, $db))==0)
      {
        $sql = "INSERT INTO ratings
          (id, total_votes, total_value)
          VALUES ('".addslashes($md5)."', 1, '".addslashes($vote)."')";
        mysql_query($sql, $db)
          or die('Erreur MySQL');
      }
      else
      {
        $sql = "UPDATE ratings
          SET total_votes = (total_votes + 1), total_value = (total_value + '".addslashes($vote)."')
          WHERE id='".addslashes($md5)."'";
        mysql_query($sql, $db)
          or die('Erreur MySQL');
      }
    }
    else
      die();
  }

  if ($return)
  {
    header('location: '.$_SERVER['HTTP_REFERER']);
    die();
  }
  else
  {
    $totalnotes = 0;
    if (isset($_GET['tn']))
      if (is_numeric($_GET['tn']))
        if ($_GET['tn']>=0)
          $totalnotes = $_GET['tn'];
    $totalnotes += $vote;

    $totalvotes = 0;
    if (isset($_GET['tv']))
      if (is_numeric($_GET['tv']))
        if ($_GET['tv']>=0)
          $totalvotes = $_GET['tv'];
    $totalvotes++;

    require_once('includes/rating.inc.php');
    $new_back = array(rating_bar($totalnotes, $totalvotes, true, $md5));
    $allnewback = join("\n", $new_back);

    //name of the div id to be updated | the html that needs to be changed
    $output = "unit_long$md5|$allnewback";
    echo $output;   
  }
?>
