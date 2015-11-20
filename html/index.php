<?
  require_once('config.inc.php');
  require_once('includes/generator.inc.php');
  require_once('includes/rating.inc.php');
  require_once('header.inc.php');

  $filter = '3';
  if (isset($_GET['f']))
    if (is_numeric($_GET['f']))
      if ($_GET['f'] >= 0 && $_GET['f'] < 5)
        $filter = $_GET['f'];

  $page = 1;
  if (isset($_GET['p']))
    if (is_numeric($_GET['p']))
      if ($_GET['p'] > 0)
        $page = $_GET['p'];

  if ($filter==0 && trim($search)=='')
    $filter = 3;

  $db = mysql_pconnect(DB_HOST, DB_USER, DB_PASS)
    or die('Erreur MySQL');
  mysql_select_db(DB_NAME, $db)
    or die('Erreur MySQL');

  if (isset($_GET['del']))
    if (in_array($_SERVER["REMOTE_ADDR"], $admin_ips))
      delete_image($_GET['del']);
?>

<?
  if (trim(ANNONCE)!='')
  {
?>
<p class="annonce"><strong>Info : </strong> <? echo ANNONCE; ?></p>
<?
  }
?>


<h2>Voir les couvertures parodiques</h2>
<form action="index.php" method="get">
  <select name="f" onChange="form.submit();">
    <option value="3" <? if ($filter=='3') echo "selected"; ?>>Les mieux notées</option>
    <option value="1" <? if ($filter=='1') echo "selected"; ?>>Les plus récentes</option>
    <option value="4" <? if ($filter=='4') echo "selected"; ?>>10 piochées au hasard</option>
  </select>
  <input type="submit" value="Go"/>
</form>
<br/>
<table>
<tr>
<?
  switch ($filter)
  {
    case '0':
      // recherche;
      $sql_count = "SELECT count(md5) as total
        FROM visuel
        WHERE (MATCH (visuel.title) AGAINST ('".addslashes($search)."')
        OR visuel.title LIKE '%".addslashes($search)."%')
        AND visuel.candidate=1
        AND visuel.accepted=1";
      $sql = "SELECT visuel.*, DATE_FORMAT(visuel.`date`, '%d/%m/%Y %H:%i') as friendly_date, ratings.total_value, ratings.total_votes
        FROM visuel
        LEFT JOIN ratings ON visuel.md5 = ratings.id
        WHERE (MATCH (visuel.title) AGAINST ('".addslashes($search)."')
        OR visuel.title LIKE '%".addslashes($search)."%')
        AND visuel.candidate=1
        AND visuel.accepted=1
        ORDER BY visuel.title
        LIMIT ".($page-1)*NB_PER_PAGE.",".NB_PER_PAGE;
      break;
    case '3':
      // Les mieux notées
      $sql_count = "SELECT count(md5) as total
        FROM visuel
        LEFT JOIN ratings ON visuel.md5 = ratings.id
        WHERE ratings.total_votes > 14
        AND visuel.candidate=1
        AND visuel.accepted=1
        AND (ratings.total_value/ratings.total_votes) >= 3";
      $sql = "SELECT visuel.*, DATE_FORMAT(visuel.`date`, '%d/%m/%Y %H:%i') as friendly_date, ratings.total_value, ratings.total_votes
        FROM visuel
        LEFT JOIN ratings ON visuel.md5 = ratings.id
        WHERE ratings.total_votes > 14
        AND visuel.candidate=1
        AND visuel.accepted=1
        AND (ratings.total_value/ratings.total_votes) >= 3
        ORDER BY (ratings.total_value/ratings.total_votes) DESC, ratings.total_votes DESC
        LIMIT ".($page-1)*NB_PER_PAGE.",".NB_PER_PAGE;
      break;
    case '4':
      // 10 au hasard
      $sql_count = "SELECT 10";
      $sql = "SELECT visuel.*, DATE_FORMAT(visuel.`date`, '%d/%m/%Y %H:%i') as friendly_date, ratings.total_value, ratings.total_votes
        FROM visuel
        LEFT JOIN ratings ON visuel.md5 = ratings.id
        WHERE visuel.candidate=1
        AND visuel.accepted=1
        ORDER BY RAND()
        LIMIT 0,10";
      break;
    default:
      // Les plus récentes
      $sql_count = "SELECT count(md5) as total
        FROM visuel
        WHERE visuel.candidate=1
        AND visuel.accepted=1";
      $sql = "SELECT visuel.*, DATE_FORMAT(`date`, '%d/%m/%Y %H:%i') as friendly_date, ratings.total_value, ratings.total_votes
        FROM visuel
        LEFT JOIN ratings ON visuel.md5 = ratings.id
        WHERE visuel.candidate=1
        AND visuel.accepted=1
        ORDER BY `date_accepted` DESC
        LIMIT ".($page-1)*NB_PER_PAGE.",".NB_PER_PAGE;
  }
  $result = mysql_query($sql, $db)
    or die('Erreur MySQL');
  $result_count = mysql_query($sql_count, $db)
    or die('Erreur MySQL');
  $count = mysql_fetch_array($result_count);
  $nb_total = $count['total'];

  $array_visuels = array();
  while ($visuel = mysql_fetch_array($result))
  {
    $array_visuels[$visuel['md5']] = array(
      'total_votes' => $visuel['total_votes'],
      'total_value' => $visuel['total_value'],
      'voted' => false,
    );
  }

  // recuperation des votes du visiteur
  if (count($array_visuels) > 0)
  {
    $sql_in = '';
    foreach (array_keys($array_visuels) as $md5)
    {
      if ($sql_in!='')
        $sql_in .= ",";
      $sql_in .= "'".$md5."'";
    }
    $sql = "SELECT id
      FROM ratings_ip
      WHERE ip='".$_SERVER["REMOTE_ADDR"]."'
      AND id IN (".$sql_in.")";
    $result_votes = mysql_query($sql, $db)
      or die('Erreur MySQL');
    while ($vote = mysql_fetch_array($result_votes))
    {
      $array_visuels[$vote['id']]['voted'] = true;
    }
  }

  // affichage de la galerie
  $count = 0;
  while (list($md5, $attr) = each($array_visuels))
  {
    if ($count%5==0 && $count!=0)
    {
?>
  </tr>
  <tr>
<?
    }
?>
    <td>
      <a href="view.php?c=<? echo $md5; ?>"><img src="images/thumbs/<? echo $md5; ?>-thumb.jpg" class="blackborder"/></a><br/>
<?
    echo rating_bar($attr['total_value'], $attr['total_votes'], $attr['voted'], $md5);
  
    // suppression
    if (in_array($_SERVER["REMOTE_ADDR"], $admin_ips))
    {
?>
      <a href="index.php?del=<? echo $md5; ?>">Supprimer</a><br/>
<?
    }
?>
    </td>
<?
    $count++;
  }
?>
</tr>
</table>
<div id="pager">
<?
  if ($nb_total/NB_PER_PAGE > 1)
  {
?>
<strong>Pages : </strong>
<?
    for ($a=1;$a<=ceil($nb_total/NB_PER_PAGE);$a++)
    {
      if ($page!=$a)
      {
?>
  <a href="index.php?f=<? echo $filter; ?>&amp;p=<? echo $a; ?>&amp;s=<? echo $search; ?>"><? echo $a; ?></a>
<?
      }
      else
      {
?>
  <strong><? echo $a; ?></strong>
<?
      }
    }
  }
?>
</div>
<?
  require_once('footer.inc.php');
?>
