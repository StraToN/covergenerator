<?
  ini_set('display_errors', 'On');
  require_once('config.inc.php');
  function generate($model, $chaine)
  {
    global $pictures;

    if (!isset($pictures[$model]))
      return false;
    if ($chaine=='')
      return false;

    $db = mysql_pconnect(DB_HOST, DB_USER, DB_PASS)
      or die('Erreur MySQL');
    mysql_select_db(DB_NAME, $db)
      or die('Erreur MySQL');

    $sql = "SELECT *
      FROM visuel
      WHERE `md5`='".addslashes(md5($chaine.$model))."'";
    $result = mysql_query($sql, $db)
      or die('Erreur MySQL');
    if (mysql_num_rows($result)==1)
      return md5($chaine.$model);

    $ip = $_SERVER["REMOTE_ADDR"];
    $sql = "INSERT INTO visuel
      (`md5`, `model`, `title`, `date`, `ip`, `warnings`, `candidate`, `accepted`)
      VALUES (
        '".addslashes(md5($chaine.$model))."',
        '".addslashes($model)."',
        '".addslashes($chaine)."',
        now(),
        '".addslashes($ip)."',
        0,
        0,
        0);";
    mysql_query($sql, $db)
      or die('Erreur MySQL');

    $cover_pic = $pictures[$model]['picture'];
    $x_min_optimal = $pictures[$model]['x_min'];
    $x_max_optimal = $pictures[$model]['x_max'];
    $y_min_optimal = $pictures[$model]['y_min'];
    $y_max_optimal = $pictures[$model]['y_max'];
    $color = $pictures[$model]['color'];
    $font = dirname(__FILE__).'/../'.$pictures[$model]['font'];
  
    $largeur_max = $x_max_optimal - $x_min_optimal;
    $hauteur_max = $y_max_optimal - $y_min_optimal;
  
    $optimal_size_found = false;
    $current_size = 75;
    while (!$optimal_size_found)
    {
      $textbox = imagettfbbox($current_size, 0, $font, $chaine);
      $largeur = $textbox[4] - $textbox[6];
      $hauteur = $textbox[1] - $textbox[7];
      //$hauteur = 0 - $textbox[7] - $textbox[1];
      if (($largeur < $largeur_max) && ($hauteur < $hauteur_max))
        $optimal_size_found = true;
      else
        $current_size--;
    }
   
    $hauteur_compensee = 0 - $textbox[7] - $textbox[1];
    $optimal_y = $y_max_optimal - round(($hauteur_max-$hauteur_compensee) / 2) ;
    $optimal_x = $x_max_optimal - $largeur;

    $im = imagecreatefromjpeg('images/genuine/'.$cover_pic);
    $text_color = imagecolorallocate($im, $color[0], $color[1], $color[2]);
    imagettftext($im, $current_size, 0, $optimal_x, $optimal_y, $text_color, $font, $chaine);
    imagejpeg($im, 'images/full/'.md5($chaine.$model).'.jpg', 90);

    // thumbnail
    list($width, $height) = getimagesize('images/full/'.md5($chaine.$model).'.jpg');
    $new_width = 150;
    $new_height = round((150/$width)*$height);

    // Redimensionnement
    $image_p = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($image_p, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    imagejpeg($image_p, 'images/thumbs/'.md5($chaine.$model).'-thumb.jpg', 70);
  
    imagedestroy($im);
    imagedestroy($image_p);

    return md5($chaine.$model);
  }

  function delete_image($md5)
  {
    if ($md5=='')
      return false;

    $db = mysql_pconnect(DB_HOST, DB_USER, DB_PASS)
      or die('Erreur MySQL');
    mysql_select_db(DB_NAME, $db)
      or die('Erreur MySQL');

    $sql = "DELETE FROM ratings
      WHERE id='".$md5."'";
    mysql_query($sql, $db)
      or die('Erreur MySQL');
    $sql = "DELETE FROM visuel
      WHERE md5='".$md5."'";
    mysql_query($sql, $db)
      or die('Erreur MySQL');
    @unlink(dirname(__FILE__).'/../images/thumbs/'.$md5.'-thumb.jpg');
    @unlink(dirname(__FILE__).'/../images/full/'.$md5.'.jpg');
  }
?>
