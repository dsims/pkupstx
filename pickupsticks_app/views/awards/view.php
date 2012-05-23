<h1><?php echo $award->title ?> Achievement</h1>
<?php echo nl2br($award->description) ?>
<div class="rightlist" style="color:white;text-align:center;">
	<div class="listheader">Awarded To</div>
</div>
<?php foreach($users as $user){
	?> <span class="awardtype<?php echo $user->type?>">●</span> <?php echo $user->title.' <br />';
}
?>