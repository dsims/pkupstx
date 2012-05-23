<br />
<table class="classic" width="100%">
	<tr><th width="80%">OFFICIAL</th><th>Last Post</th></tr>
<?php foreach($asubs as $board){?>
	<tr><td>
		<?php echo html::anchor('topics/board/'.$board->id.'/'.slug::format($board->title), '<span class="forumlink">'.$board->title.'</span>'); ?>
		<br />
		<span class="description"><?php echo $board->description ?></span>
	</td><td><?php echo $board->date_submit_format ?></td></tr>
	<?php } /*endforeach*/  ?>
</table>
<table class="classic" width="100%">
	<tr><th width="80%"><?php echo html::anchor('boards/browse/', 'GENERAL'); ?></th><th>Last Post</th></tr>
<?php foreach($subs as $board){?>
	<tr><td>
		<?php echo html::anchor('topics/board/'.$board->id.'/'.slug::format($board->title), '<span class="forumlink">'.$board->title.'</span>'); ?>
		<br />
		<span class="description"><?php echo $board->description ?></span>
	</td><td><?php echo $board->date_submit_format ?></td></tr>
	<?php } /*endforeach*/  ?>
	<tr><td colspan="2"><?php echo html::anchor('boards/browse/', '<span class="forumlink">>> See more boards</span>'); ?></td></tr>
</table>
<table class="classic" width="100%">
	<tr><th width="80%"><?php echo html::anchor('boards/browse/games', 'GAMES'); ?></th><th>Last Post</th></tr>
<?php foreach($gsubs as $board){?>
	<tr><td>
		<?php echo html::anchor('topics/board/'.$board->id.'/'.slug::format($board->title), '<span class="forumlink">'.$board->title.'</span>'); ?>
		<br />
		<span class="description"><?php echo $board->description ?></span>
	</td><td><?php echo $board->date_submit_format ?></td></tr>
<?php } /*endforeach*/  ?>
	<tr><td colspan="2"><?php echo html::anchor('boards/browse/games', '<span class="forumlink">>> See more games</span>'); ?></td></tr>
</table>
<table class="classic" width="100%">
	<tr><th width="80%"><?php echo html::anchor('boards/browse/users', 'USERS'); ?></th><th>Last Post</th></tr>
<?php foreach($usubs as $board){?>
	<tr><td>
		<?php echo html::anchor('topics/board/'.$board->id.'/'.slug::format($board->title), '<span class="forumlink">'.$board->title.'</span>'); ?>
		<br />
		<span class="description"><?php echo $board->description ?></span>
	</td><td><?php echo $board->date_submit_format ?></td></tr>
<?php } /*endforeach*/  ?>
	<tr><td colspan="2"><?php echo html::anchor('boards/browse/users', '<span class="forumlink">>> See more users</span>'); ?></td></tr>
</table>
<table class="classic" width="100%">
	<tr><th width="80%"><b>LISTS</b> <?php echo html::anchor('lists', '>> Edit your lists'); ?></th><th>Last Post</th></tr>
<?php foreach($lists as $list){?>
	<tr><td>
	<?php echo html::anchor('topics/?listid='.$list->id, '<span class="title">'.$list->title.'</span>'); ?>
	</td><td></td></tr>
<?php } /*endforeach*/  ?>

</table>

<br /><br />
<div style="clear:left;">
<hr />
<?php if($loggedin){echo html::anchor('boards/create', '[Create a new board]');} ?> -
<?php if($loggedin){echo html::anchor('games/create', '[Add a new Game]');} ?>
</div>
