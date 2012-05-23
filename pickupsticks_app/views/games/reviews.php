<?php 
	if($isLoggedIn) { echo html::anchor('topics/review/'.$game->id, '<div style="float:right">REVIEW</div>');  }
?>

<h1 style="display:inline;line-height:50px;"><?php echo $game->title ?></h1>

<div class="rightlist" style="color:white;text-align:center;">
	<div class="listheader">Reviews</div>
</div>
<?php
foreach($reviews as $topic){ ?>

<div class="listtopicview">

<?php if(strlen($topic->avatar)) { ?>
<div class="avatarsmall"><?php echo html::anchor('profile/'.$topic->username, '<img src="'.url::base().'content/images/avatars/create/'.$topic->avatar.'.gif">');?></div>
<?php } ?>
	<?php if(strlen($topic->urlsdisplaymini) > 0) { ?>
<div class="url-embed-mini" style="float:right;"><?php echo $topic->urlsdisplaymini ?></div>
<?php } ?>

<div class="title" style="margin-left:15px;">
<?php echo html::anchor('topics/'.$topic->id.'/'.$topic->slug, ''.$topic->title) ?><?php if(strlen($topic->titlebody)){echo '<span class="titlebody"> - '.$topic->titlebody.'</span>';} ?>
</div>

<div class="topicinfo">
<div class="date">
<?php echo $topic->urltypes ?> posted by
<?php echo html::anchor('profile/'.$topic->username, '<span style="color:black;">'.$topic->user.'</span>') ?> <abbr class="timeago" title="<?php echo $topic->date_added_iso?>"><?php echo $topic->date_added_format ?></abbr>
 <!--in <?php echo html::anchor('topics/board/'.$topic->board_id.'/'.$topic->boardslug, '<span style="color:black;">'.$topic->board.'</span>');?>-->
</div>
</div>


<?php if($topic->comments > 0) { ?>
<div id="commentview-<?php echo $topic->id ?>" class="replieslink"><?php echo html::anchor('topics/'.$topic->id.'/'.$topic->slug, $topic->comments.' Replies') ?></div>
<?php } ?>
</div>


<?php } ?>
