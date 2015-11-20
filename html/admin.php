<?
  require_once('config.inc.php');
  require_once('includes/generator.inc.php');

  $db = mysql_pconnect(DB_HOST, DB_USER, DB_PASS)
    or die('Erreur MySQL');
  mysql_select_db(DB_NAME, $db)
    or die('Erreur MySQL');

  if (in_array($_SERVER["REMOTE_ADDR"], $admin_ips))
  {
    require_once('header.inc.php');
    echo "<h2>Administration</h2>";

    $page = 1;
    if (isset($_GET['p']))
      $page = $_GET['p'];

    $filter = 3;
    if (isset($_GET['f']))
      $filter = $_GET['f'];

    $model = '';
    if (isset($_GET['m']))
      $model = $_GET['m'];

    $searcha = '';
    if (isset($_GET['sa']))
        $searcha = stripslashes($_GET['sa']);

    if (isset($_GET['del']))
    {
      if (isset($_GET['mod']))
      {
        foreach($_GET['del'] as $md5)
        {
          $sql = "UPDATE visuel
            SET accepted=1,
            date_accepted=now()
            WHERE md5='".$md5."'";
          mysql_query($sql, $db);
        }
      }
      elseif (isset($_GET['sup']))
      {
        foreach($_GET['del'] as $md5)
          delete_image($md5);
      }
    }

    if (isset($_GET['ip']))
    {
      echo "<h3>Contributions a modérer de ".$_GET['ip']."</h3>";
      $sql_count = "SELECT count(*) as total FROM visuel
        where ip='".$_GET['ip']."'
        AND candidate=1 AND accepted=0
        AND NOW() > DATE_ADD(visuel.`date`, INTERVAL 10 MINUTE)";
      $sql = "SELECT *, DATE_FORMAT(visuel.`date`, '%d/%m/%Y %H:%i') as friendly_date FROM visuel
        where ip='".$_GET['ip']."'
        AND candidate=1 AND accepted=0
        AND NOW() > DATE_ADD(visuel.`date`, INTERVAL 10 MINUTE)";
    }
    elseif ($model!='')
    {
      echo "<h3>File de modération</h3>";
      $sql_count = "SELECT count(*) as total FROM visuel
        WHERE model='".addslashes($model)."'
        AND candidate=1 AND accepted=0
        AND NOW() > DATE_ADD(visuel.`date`, INTERVAL 10 MINUTE)";
      $sql = "SELECT *, DATE_FORMAT(visuel.`date`, '%d/%m/%Y %H:%i') as friendly_date FROM visuel
        WHERE model='".addslashes($model)."'
        AND candidate=1 AND accepted=0
        AND NOW() > DATE_ADD(visuel.`date`, INTERVAL 10 MINUTE)
        LIMIT ".($page-1)*NB_PER_PAGE_ADMIN.",".NB_PER_PAGE_ADMIN;

    }
    else
    {
      switch ($filter)
      {
        case '1':
          echo "<h3>Derniers ajouts à la galerie</h3>";
          $sql_count = "SELECT count(*) as total FROM visuel
            WHERE candidate=1 AND accepted=1";
          $sql = "SELECT *, DATE_FORMAT(visuel.`date`, '%d/%m/%Y %H:%i') as friendly_date, ratings.total_value, ratings.total_votes
            FROM visuel
            LEFT JOIN ratings ON ratings.id = visuel.md5
            WHERE candidate=1 AND accepted=1
            ORDER BY `date` DESC
            LIMIT ".($page-1)*NB_PER_PAGE_ADMIN.",".NB_PER_PAGE_ADMIN;
          break;
        case '2':
          echo "<h3>Images signalées</h3>";
          $sql_count = "SELECT count(*) as total
            FROM visuel
            WHERE warnings > 0";
          $sql = "SELECT *, DATE_FORMAT(visuel.`date`, '%d/%m/%Y %H:%i') as friendly_date, ratings.total_value, ratings.total_votes
            FROM visuel
            LEFT JOIN ratings ON ratings.id = visuel.md5
            WHERE warnings > 0
            ORDER by warnings DESC
            LIMIT ".($page-1)*NB_PER_PAGE_ADMIN.",".NB_PER_PAGE_ADMIN;
          break;
        case '3':
          echo "<h3>File de modération</h3>";
          if ($searcha!='')
          {
            $sql_count = "SELECT count(*) as total FROM visuel
              WHERE candidate=1 AND accepted=0
              AND title LIKE '%".addslashes($searcha)."%'
              AND NOW() > DATE_ADD(visuel.`date`, INTERVAL 10 MINUTE)";
            $sql = "SELECT visuel.*, DATE_FORMAT(visuel.`date`, '%d/%m/%Y %H:%i') as friendly_date, ratings.total_value, ratings.total_votes 
              FROM visuel
              LEFT JOIN ratings ON ratings.id = visuel.md5
              WHERE visuel.candidate=1 AND visuel.accepted=0
              AND NOW() > DATE_ADD(visuel.`date`, INTERVAL 10 MINUTE)
              AND visuel.title LIKE '%".addslashes($searcha)."%'
              ORDER BY visuel.`date`
              LIMIT ".($page-1)*NB_PER_PAGE_ADMIN.",".NB_PER_PAGE_ADMIN;
          }
          else
          {
            $sql_count = "SELECT count(*) as total FROM visuel
              WHERE candidate=1 AND accepted=0
              AND NOW() > DATE_ADD(visuel.`date`, INTERVAL 10 MINUTE)";
            $sql = "SELECT visuel.*, DATE_FORMAT(visuel.`date`, '%d/%m/%Y %H:%i') as friendly_date, ratings.total_value, ratings.total_votes
              FROM visuel
              LEFT JOIN ratings ON ratings.id = visuel.md5
              WHERE visuel.candidate=1 AND visuel.accepted=0
              AND NOW() > DATE_ADD(visuel.`date`, INTERVAL 10 MINUTE)
              ORDER BY visuel.`date`
              LIMIT ".($page-1)*NB_PER_PAGE_ADMIN.",".NB_PER_PAGE_ADMIN;
          }
      }
    }
    $results = mysql_query($sql, $db);
?>
  <a href="admin.php?f=3">En attente de modération</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?f=1">Derniers ajouts à la galerie</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.php?f=2">Images signalées</a><br/><br/>
  <form action="admin.php" method="get" name="biglist">
  <input type="submit" value="Supprimer les couverture cochées" name="sup"/>
<?
  if ($filter==3)
  {
?>
  <input type="submit" value="Ajouter à la galerie" name="mod"/><br/>
  <br/>Recherche : <input type="text" value="<? echo $searcha; ?>" name="sa"/>
  <input type="submit" name="gosearch" value="Go"/><br/>
<?
  }
?>
  <br/><A href="#" onClick="check(); return false;">[ Tout cocher / décocher ]</A>
  <script type="text/javascript">
    <!--
    var checked = 1;  
    function check()
    { 
      for (var i=0;i<document.biglist.elements.length;i++)
      {
        var e = document.biglist.elements[i];
        if(e.type == "checkbox")
        {
          e.checked = checked;
        }
      }
      checked = !checked;
    }
    -->
  </script>
  <table style="border: 1px solid grey">
    <tr>
      <th>&nbsp;</th>
      <th>Date</th>
      <th>Texte</th>
      <th>IP</th>
      <th>Avert.</th>
      <th>Note</th>
      <th>Visuel</th>
      <th>Actions</th>
    </tr>
<?
    while ($visuel = mysql_fetch_array($results))
    {
?>
    <tr style="border: 1px solid grey">
      <td><input type="checkbox" name="del[]" value="<? echo $visuel['md5']; ?>"/></td>
      <td style="padding-right: 20px" nowrap><? echo $visuel['friendly_date']; ?></td>
      <td style="padding-right: 20px"><? echo $visuel['title']; ?></td>
      <td style="padding-right: 20px"><a href="admin.php?f=<? echo $filter; ?>&amp;ip=<? echo $visuel['ip']; ?>"><? echo $visuel['ip']; ?></a></td>
      <td style="padding-right: 20px"><? echo $visuel['warnings']; ?></td>
      <td style="padding-right: 20px" nowrap><? if ($visuel['total_votes']==0) echo '0'; else echo round($visuel['total_value']/$visuel['total_votes'],1); ?> (<? if ($visuel['total_votes']=='') echo '0'; else echo $visuel['total_votes']; ?>)</td>
      <td style="padding-right: 20px"><a href="admin.php?f=<? echo $filter; ?>&amp;m=<? echo $visuel['model']; ?>"><? echo $pictures[$visuel['model']]['picture']; ?></a></td>
      <td>
        <a href="view.php?c=<? echo $visuel['md5']; ?>" onmouseover="document.getElementById('<? echo $visuel['md5']; ?>').style.visibility='visible'; document.getElementById('<? echo $visuel['md5']; ?>').src='images/thumbs/<? echo $visuel['md5']; ?>-thumb.jpg';" onmouseout="document.getElementById('<? echo $visuel['md5']; ?>').style.visibility='hidden'">Voir</a>
        <img id="<? echo $visuel['md5']; ?>" src="" style="float: right; position: absolute; visibility: hidden;"/>
      </td>
    </tr>
<?
    }
?>
  </table>
  <A href="#" onClick="check(); return false;">[ Tout cocher / décocher ]</A><br/>
  <input type="hidden" name="f" value="<? echo $filter; ?>"/>
  <input type="hidden" name="p" value="<? echo $page; ?>"/>
  <input type="hidden" name="m" value="<? echo $model; ?>"/>
  <input type="submit" value="Supprimer les couverture cochées" name="sup"/>
<?
  if ($filter==3)
  {
?>
  <input type="submit" value="Ajouter à la galerie" name="mod"/>
<?
  }
?>
  </form>
<?
  $result_count = mysql_query($sql_count, $db);
  $count = mysql_fetch_array($result_count);
  $nb_total = $count['total'];
?>
<div id="pager">
<?
  if ($nb_total/NB_PER_PAGE_ADMIN > 1)
  {
?>
<strong>Pages : </strong>
<?
    for ($a=1;$a<=ceil($nb_total/NB_PER_PAGE_ADMIN);$a++)
    {
      if ($page!=$a)
      {
?>
  <a href="admin.php?f=<? echo $filter; ?>&amp;p=<? echo $a; ?>&amp;m=<? echo $model; ?>"><? echo $a; ?></a>
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
  }
?>
