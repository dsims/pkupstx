<?php if($loggedin) { ?>
<form action="<?php echo url::site('games/submitSettings') ?>" method="post">
	<input type="hidden" name="game_id" value="<?php echo $game->id ?>">
	<input type="hidden" name="type" value="<?php echo $type ?>">
	<div data-role="controlgroup" id="gameown" style="float:right;">
		I own it <input type="checkbox" name="own" value="1" <?php if($uowned == 1) echo 'checked' ?> />
		I want it <input type="checkbox" name="want" value="2" <?php if($uowned == 2) echo 'checked' ?> />
		<input type="submit" value="SUBMIT" />
	</div>
	
</form>
<script>
	var checkboxes = $('#gameown input[type="checkbox"]');
	checkboxes.click(function(){
	  var self = this;
	  checkboxes.each(function(){
		if(this!=self) this.checked = ''
	  })
	})
</script>
<?php } ?>
<h1><?php echo sizeof($users) ?> user<?php if(sizeof($users) != 1) {echo 's';}?> <?php echo $type ?> <?php echo html::anchor('games/'.$game->id.'/'.slug::format($game->title), $game->title); ?></h1>
<div style="float:right"></div>
<table class="listing">
<?php if(sizeof($users) == 0){echo "NONE";}foreach($users as $user){ ?>
<tr>
<td>
<?php 
	if($user->rating == 2)
		html::anchor('topics/'.$user->topic_id, '<img src="http://i387.photobucket.com/albums/oo313/Chief_forum_Smilies/badshroom.gif">');
	if($user->rating == 1)
		html::anchor('topics/'.$user->topic_id, '<img src="http://i181.photobucket.com/albums/x235/judgeSpear/PIPs/spr_mushroom.gif">');
	if($user->rating == 3)
		html::anchor('topics/'.$user->topic_id, '<img src="http://digibutter.nerr.biz/content/images/star.gif">');
 ?> 
<?php echo html::anchor('profile/'.$user->username, $user->title) ?></td>
</tr>
<?php } /*endforeach*/ ?>
</table>
