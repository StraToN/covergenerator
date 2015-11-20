<?
  $search = "";
  if (isset($_GET['s']))
    $search = stripslashes($_GET['s']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
  <title><? echo SITE_TITLE; ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <script type="text/javascript" language="javascript" src="includes/js/behavior.js"></script>
  <script type="text/javascript" language="javascript" src="includes/js/rating.js"></script>
  <link rel="stylesheet" type="text/css" href="rating.css" /> 
  <link type="text/css" rel="stylesheet" href="style.css"/>
</head>
<body>
<div id="page">
<div id="header">
<h1><a href="index.php"><? echo SITE_TITLE; ?></a></h1>
<div id="search">
  <form action="index.php" method="get">
  <input type="text" name="s" value="<? echo $search; ?>"/>
  <input type="submit" value="Rechercher"/>
  <input type="hidden" name="f" value="0"/>
  </form>
</div>
<div id="menu">
  <ul>
    <li><a href="index.php">Accueil</a></li>
    <li><a href="create.php">Créer une couverture</a></li>
    <li><a href="about.php">A propos du site</a></li>
  </ul>
</div>
</div>
