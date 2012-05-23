<h1>MERGE GAMES</h1>

	<form method="post" action="<?php echo url::site('games/mergedo') ?>">
		<select name="game1Id">
<?php foreach($games as $game){ ?>
<option value="<?php echo $game->id ?>" <?php if($game->id == $gameid) { echo 'selected'; } ?>> <?php echo $game->title ?> - <?php echo $game->slug ?> (<?php echo $game->id ?>)</option>
<?php } /*endforeach*/ ?>
		</select>

		<select name="game2Id">
<?php foreach($games as $game){ ?>
<option value="<?php echo $game->id ?>" <?php if($game->id == $gameid) { echo 'selected'; } ?>> <?php echo $game->title ?> - <?php echo $game->slug ?> (<?php echo $game->id ?>)</option>
<?php } /*endforeach*/ ?>
		</select>
		<input type="submit">
	</form>

<h1>DELETE GAME</h1>

	<form method="post" action="<?php echo url::site('games/delete') ?>">
		<select name="gameId">
<?php foreach($games as $game){ ?>
<option value="<?php echo $game->id ?>" <?php if($game->id == $gameid) { echo 'selected'; } ?>> <?php echo $game->title ?> - <?php echo $game->slug ?> (<?php echo $game->id ?>)</option>
<?php } /*endforeach*/ ?>
		</select>
		<input type="submit">
	</form>