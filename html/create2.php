<?
  require_once('config.inc.php');

  $model = '';
  if (isset($_GET['m']))
    if (isset($pictures[$_GET['m']]))
      $model = $_GET['m'];
  if ($model=='')
  {
    header('location: index.php');
    die();
  }

  $titre = '';
  if (isset($_GET['t']))
    $titre = stripslashes($_GET['t']);

  if ($titre!='')
  {
    // filtrage des mots bannis ($banned_words);
    foreach ($banned_words as $banned_word)
    {
      if (stristr($titre, $banned_word))
      {
        header('location: index.php');
        die();
      }
    }

    require_once('includes/generator.inc.php');
    $md5 = generate($model, $titre);
    if ($md5)
    {
      header('location: create3.php?c='.urlencode($md5));
      die();
    }
    else
    {
      header('location: index.php');
      die();
    }
  }

  require_once('header.inc.php');
?>

<h2>Création d'une couverture</h2>

<h3>Etape 1/2 : Choisir une couverture</h3>
Couverture choisie (<a href="create.php">changer</a>) :<br/>
<br/>
<a href="images/genuine/<? echo $pictures[$model]['picture']; ?>" target="_blank"><img src="images/genuine/<? echo $pictures[$model]['thumb']; ?>" class="blackborder"/></a><br/>

<h3>Etape 2/2 : Choisir un titre</h3>
<form action="create2.php" method="get" name="step2">
<strong>Choisir un titre :</strong><br/>
<input type="text" name="t" value="<? echo $titre; ?>" size="30"/><br/>
<input type="submit" value="Créer ma couverture"/>
<input type="hidden" name="m" value="<? echo $model; ?>"/>
</form>
<script type="text/javascript">document.step2.t.focus();</script>
<?
  require_once('footer.inc.php');
?>
