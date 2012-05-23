<h2>Edit game <?php echo $game->title ?></h2>

<form method="post" action="<?php echo url::site('games/submit') ?>">
	<input type="hidden" name="gameid" value="<?php echo $game->id ?>">
<table>
<tr><td align="right">Game Name:</td><td><input id="sbox" type="text" name="title"  value="<?php echo $game->title ?>"></td></tr>
<tr><td align="right"><a target="_blank" href="http://www.metacritic.com/games/">Metacritic</a> url:</td><td><input type="text" name="mc_url"  value="<?php echo $game->mc_url ?>"></td></tr>
<tr><td align="right">GameRankings url:</td><td><input type="text" name="gr_url" value="<?php echo $game->gr_url ?>"></td></tr>
<tr><td align="right">GameSpot url:</td><td><input type="text" name="gs_url" value="<?php echo $game->gs_url ?>"></td></tr>
<tr><td align="right">GameTrailers url:</td><td><input type="text" name="gt_url" value="<?php echo $game->gt_url ?>"></td></tr>
<tr><td align="right">GiantBomb url:</td><td><input type="text" name="gb_url" value="<?php echo $game->gb_url ?>"></td></tr>
<tr><td align="right">slug:</td><td><input type="text" name="slug" value="<?php echo $game->slug ?>"></td></tr>
<tr><td align="right">slugalt1:</td><td><input type="text" name="slugalt1" value="<?php echo $game->slugalt1 ?>"></td></tr>
<tr><td align="right">slugalt2:</td><td><input type="text" name="slugalt2" value="<?php echo $game->slugalt2 ?>"></td></tr>
<tr><td colspan="2"><input type="submit" value="Save"></td></tr>
</table>
</form>