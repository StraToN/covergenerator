<?
  $check="";
  if (isset($_GET['c']))
    $check = stripslashes($_GET['c']);
  else
  {
    header('location: index.php');
    die();
  }

  require_once('config.inc.php');
  require_once('includes/rating.inc.php');

  $db = mysql_pconnect(DB_HOST, DB_USER, DB_PASS)
    or die('Erreur MySQL');
  mysql_select_db(DB_NAME, $db)
    or die('Erreur MySQL');

  $sql = "SELECT visuel.*, ratings.total_votes, ratings.total_value
    FROM visuel
    LEFT JOIN ratings on visuel.md5=ratings.id
    WHERE visuel.`md5`='".addslashes($check)."'";
  $result = mysql_query($sql, $db)
    or die('Erreur MySQL');
  if (mysql_num_rows($result)==1)
  {
    $visuel = mysql_fetch_array($result);
    // seuls les admins voient les couverture non modérées
    if ($visuel['accepted']=='1' || in_array($_SERVER["REMOTE_ADDR"], $admin_ips))
    {
      $model = $visuel['model'];
      $voted = false;
      $sql = "SELECT id FROM
        ratings_ip
        WHERE id='".addslashes($check)."'
        AND ip='".$_SERVER["REMOTE_ADDR"]."'";
      if (mysql_num_rows(mysql_query($sql, $db))==1)
        $voted = true;
    }
    else
    {
      header('location: index.php');
      die();
    }
  }
  else
  {
    header('location: index.php');
    die();
  }

  if (isset($_GET['s']))
  {
    $sql = "UPDATE visuel
      SET warnings=warnings+1
      WHERE `md5`='".addslashes($check)."'";
    mysql_query($sql, $db)
      or die('Erreur MySQL');
  }

  require_once('header.inc.php');
?>
<h2>Visualisation d'une couverture parodique</h2>
<img src="images/full/<? echo $check; ?>.jpg" class="blackborder"/>
<?
  // suppression
  if (in_array($_SERVER["REMOTE_ADDR"], $admin_ips))
  {
?>
      <br/><a href="index.php?del=<? echo $check; ?>">Supprimer</a><br/>
      <a href="admin.php?ip=<? echo $visuel['ip']; ?>">Voir toutes les contributions de <? echo $visuel['ip']; ?></a><br/>
      <br/>
<?
  }
  echo rating_bar($visuel['total_value'], $visuel['total_votes'], $voted, $check);
?>
<br/>
<a href="create2.php?m=<? echo $model; ?>">Créer une autre couverture sur ce modèle</a><br/>
<br/>
<br/>
<a href="view.php?c=<? echo urlencode($check); ?>&amp;s=1">Signaler cette image à l'administrateur</a> (pas drôle, contenu offensant, incitation à la haine, etc.)
<?
  require_once('footer.inc.php');
?>
