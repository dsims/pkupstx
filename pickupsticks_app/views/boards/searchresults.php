<h2>Search Results</h2>

<form action="<?php echo url::site('boards/search') ?>" method="GET">
<input type="search" name="terms" value="<?php echo $terms ?>">
<input type="hidden" name="type" value="<?php echo $type ?>">
</form>
<br />
<div id="boards">
<?php foreach($boards as $board){?>
<div id="board-<?php echo $board->id ?>">
<?php echo html::anchor('topics/board/'.$board->id.'/'.slug::format($board->title), '<span class="title">'.$board->title.'</span>'); ?>
 <?php if(strlen($board->description)){ ?> : <span class="description"><?php echo $board->description ?></span> <?php } ?>
</div>
<br />
<?php } /*endforeach*/ ?>
</div>