<?
  require_once('config.inc.php');
  require_once('header.inc.php');
?>
<h2>Création d'une couverture</h2>
<h3>Etape 1/2 : Choisir un visuel</h3>
<table>
  <tr>
<?
  while (list($key, $value) = each($pictures))
  {
    if (($key-1)%5==0 && $key!=1)
    {
?>
  </tr>
  <tr>
<?
    }
?>
    <td style="text-align: center;">
      <a href="create2.php?m=<? echo $key; ?>"><img src="images/genuine/<? echo $value['thumb']; ?>" class="blackborder"/></a><br/>
      <a href="images/genuine/<? echo $value['picture']; ?>" target="_blank">Voir taille réelle</a>
    </td>
<?
  }
?>
  </tr>
</table>
<?
  require_once('footer.inc.php');
?>
