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

  $db = mysql_pconnect(DB_HOST, DB_USER, DB_PASS)
    or die('Erreur MySQL');
  mysql_select_db(DB_NAME, $db)
    or die('Erreur MySQL');
  $sql = "SELECT *
    FROM visuel
    WHERE `md5`='".addslashes($check)."'";
  $result = mysql_query($sql, $db)
    or die('Erreur MySQL');
  if (mysql_num_rows($result)==1)
  {
    $visuel = mysql_fetch_array($result);
    $model = $visuel['model'];
    $title = $visuel['title'];
  }
  else
  {
    header('location: index.php');
    die();
  }

  // verification du délai entre les candidatures

  $time_to_wait = '';
  if (SUBMIT_DELAY!=0)
  {
    $sql = "SELECT date_submit, TIME_FORMAT(SEC_TO_TIME(UNIX_TIMESTAMP(DATE_ADD(date_submit, INTERVAL ".SUBMIT_DELAY." HOUR))-UNIX_TIMESTAMP(NOW())), '%k heure(s) %i minutes(s) et %s seconde(s)') as time_ok
      FROM last_submit
      WHERE ip='".addslashes($_SERVER['REMOTE_ADDR'])."'
      AND DATE_ADD(date_submit, INTERVAL ".SUBMIT_DELAY." HOUR) > NOW()";
    $result = mysql_query($sql, $db)
      or die('Erreur MySQL');
    if ($last_submit = mysql_fetch_array($result))
    {
      // un resultat -> impossible de soumettre
      $time_to_wait = $last_submit['time_ok'];
    }
  }

  $candidate=false;
  if (ALLOW_GALLERY && $time_to_wait=='')
  {
    if (isset($_GET['candidate']))
    {
      if ($visuel['ip']==$_SERVER['REMOTE_ADDR'])
      {
        $sql = "SELECT *
          FROM history
          WHERE LOWER(title)='".addslashes(strtolower($title))."'
          AND model='".addslashes($model)."'";
        $result = mysql_query($sql, $db)
          or die('Erreur MySQL');
        if (mysql_num_rows($result)==1)
        {
          // déjà proposé : on fait rien
        }
        else
        {
          $sql = "UPDATE visuel
            SET candidate=1
            WHERE `md5`='".addslashes($check)."'";
          mysql_query($sql, $db)
            or die('Erreur MySQL');
          $sql = "INSERT INTO history
            (title, model, date_created)
            VALUES ('".addslashes(strtolower($title))."',
              '".addslashes($model)."',
              now())";
          mysql_query($sql, $db)
            or die('Erreur MySQL');
        }
        // on stocke la derniere candidature
        $sql = "DELETE FROM last_submit
          WHERE ip='".addslashes($_SERVER['REMOTE_ADDR'])."'";
        mysql_query($sql, $db)
          or die('Erreur MySQL');
        $sql = "INSERT INTO last_submit
          (ip, date_submit)
          VALUES('".addslashes($_SERVER['REMOTE_ADDR'])."', NOW())";
        mysql_query($sql, $db)
          or die('Erreur MySQL');
        $candidate=true;
      }
      else
      {
        header('location: index.php');
        die();
      }
    }
  }

  require_once('header.inc.php');

  if (!$candidate)
  {
?>
<h2>Couverture créée !</h2>
<p><strong>Attention : </strong>cette image est stockée <strong>temporairement</strong> sur
le serveur et n'est pas visible dans la galerie.<br/>Pour la diffuser à vos amis, veuillez
l'enregistrer sur votre ordinateur (clic droit > enregistrer l'image sous...).<br/>
<br/>

<img src="images/full/<? echo $check; ?>.jpg" class="blackborder"/>
<br/>
<h2>Participation à la galerie</h2>
<?
    if (ALLOW_GALLERY)
    {
      if (SUBMIT_DELAY!=0)
      {
        if ($time_to_wait=='')
        {
?>
<strong>Attention : </strong> vous ne pouvez proposer qu'<strong>une image toutes les <? echo SUBMIT_DELAY; ?> heures</strong> !<br/>
Si vous êtes sûr que votre couverture :
<ul>
<li>n'est pas déjà passée (cf. le moteur de recherche)</li>
<li>est drôle (cf. Jean Roucas)</li>
<li>n'est pas bourrée de fautes d'orthographe (cf. Bescherelle)</li>
<li>est lisible (cf. un ophtalmo)</li>
</ul>
... Alors elle mérite peut-être de figurer dans la galerie du site !<br/>
<br/>
Si c'est le cas, veuillez <a href="create3.php?c=<? echo $check; ?>&amp;candidate=1">cliquer ici</a>.<br/>
Votre image sera ensuite proposée aux modérateurs, qui décideront ou non de l'ajouter à la galerie.
<?
        }
        else
        {
?>
Désolé, vous ne pouvez proposer qu'une image toutes les <? echo SUBMIT_DELAY; ?> heures.<br/>
Veuillez patienter encore <? echo $time_to_wait; ?> avant d'effectuer une nouvelle proposition. Merci !
<?
        }
      }
      else
      {
?>
Si vous pensez que cette image mérite de figurer dans la galerie du site, veuillez
<a href="create3.php?c=<? echo $check; ?>&amp;candidate=1">cliquer ici</a>.<br/>Votre image sera ensuite proposée aux modérateurs,
qui décideront ou non de l'ajouter à la galerie.
<?
      }
    }
    else
    {
?>
Les candidatures à la galerie du site sont actuellement fermées.
<?
    }
  }
  elseif (ALLOW_GALLERY && $time_to_wait=='')
  {
?>
<h2>Couverture enregistrée !</h2>
Votre création a été soumise aux modérateurs. Elle apparaîtra dans la galerie si elle est acceptée. Merci de votre
contribution !
<?
  }
?>
<br/>
<br/>
<a href="create2.php?m=<? echo $model; ?>">Créer une autre couverture sur ce modèle</a><br/>
<?
  require_once('footer.inc.php');
?>
