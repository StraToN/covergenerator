<?
function rating_bar($total_value, $total_votes, $voted, $id)
{
  $avg_note = 0;
  $nb_votes = 0;
  if (is_numeric($total_votes) && $total_votes!=0)
  {
    $avg_note = $total_value/$total_votes;
    $nb_votes = $total_votes;
  }

  $current_width = round(15*$avg_note);

  $class = 'ratingblock';
  if($voted)
    $class = 'ratingblock voted';
  $rater = '';
  $rater .= ' <div id="unit_long'.$id.'" class="'.$class.'">';
  $rater .= '   <ul id="unit_ul'.$id.'" class="unit-rating" style="width: 75px;">';
  $rater .= '     <li class="current-rating" style="width: '.$current_width.'px;">Currently '.round($avg_note,2).'/5</li>';
  for ($ncount = 1; $ncount <= 5; $ncount++)
  {
    if(!$voted)
    {
      $querystring = 'j='.$ncount.'&amp;q='.$id.'&amp;r=1&amp;tn='.$total_value.'&amp;tv='.$total_votes;
      $rater .= '<li><a href="vote.php?'.$querystring.'" title="'.$ncount.' / 5" class="r'.$ncount.'-unit rater" rel="nofollow">'.$ncount.'</a></li>';
    }
  }
  $rater .= '  </ul>';
  $rater .= '  ('.$nb_votes.' votes)';
  $rater .= ' </div>';
  return $rater;
}
?>
