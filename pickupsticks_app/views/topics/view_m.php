<div id="myMarkedUpContainer"><div id="dialog"></div>
<div id="topic" class="listtopic" style="border:none;">
<?php if(strlen($topic->avatar)) { ?>
<div class="avatar"><?php echo html::anchor('profile/'.$topic->username, '<img src="'.url::base().'content/images/avatars/create/'.$topic->avatar.'_50.gif">');?></div>
<?php } ?>
<div class="title" style="padding-top:5px;padding-bottom:5px;"><?php echo $topic->title ?></div>
<div style="padding-bottom:10px"><div class="date">

Posted by <?php echo html::anchor('profile/'.$topic->username, '<span style="color:black;">'.$topic->user.'</span>') ?> <abbr class="timeago" title="<?php echo $topic->date_added_iso?>"><?php echo $topic->date_added_format ?></abbr> in <?php echo html::anchor('topics/board/'.$topic->board_id.'/'.slug::format($topic->board), '<span style="color:black;">'.$topic->board.'</span>');?>
<?php if(isset($user)  && $user->id > 0 && ($board->owner_id == $user->id || $topic->user_id == $user->id || $isAdmin)) { ?>
	<?php if($topic->user_id == $user->id || $isAdmin) { ?>
 - <div style="display:inline;" id="edit-<?php echo $topic->id ?>"><a class="edit_link" href="<?php echo url::site('topics/edit/'.$topic->id) ?>">Edit</a></div>
 - <div style="display:inline;" id="edit-<?php echo $topic->id ?>"><a class="delete_link" href="<?php echo url::site('topics/delete/'.$topic->id) ?>">Delete</a></div>
	<?php }
}?>
</div></div>

<div class="topicarea">
<?php if(sizeof($topic->urlsdisplay) > 0) { ?>
<div class="url-embed"><?php echo $topic->urlsdisplay ?></div>
<?php } ?>
<div class="body"><?php echo $topic->body?></div>
<br />Replies:<br /><br/>

<div id="commentarea" class="commentarea">
<?php $lastcomment = 0;$lastcommentid=0;
foreach($comments as $comment){ if($comment->date_submit > $lastcomment){
	$lastcomment=$comment->date_submit;$lastcommentid = $comment->id;}?>

<div class="commentrow" id="comment-<?php echo $comment->id?>">
		<?php if(strlen($comment->user_avatar)){?><?php echo html::anchor('profile/'.$comment->username, '<img src="'.url::base().'content/images/avatars/create/'.$comment->user_avatar.'.gif">');?> <?php } else { ?>&bull;<?php } ?>
		<?php echo $comment->comment ?>
		<div class="date" style="display:block">Reply by <span style="display:inline"><?php echo $comment->user ?> <abbr class="timeago" title="<?php echo $comment->date_added_iso?>"><?php echo $comment->date_added_format ?></abbr>
		</div>
</div>
<?php } /*endforeach*/ ?>
<div id="comments"></div>
</div>

<?php if($topic->locked == 1) { echo 'Topic is Locked'; } else if(isset($user)){ ?>
<div style="padding-left:15px">
<br />
<form id="newpostform" name="newpostform" method="POST" action="#" onsubmit="return false;">
			<br />Reply: <textarea name="value" id="tvalue"></textarea>
			<input type="hidden" name="topicid" id="topicid" value="<?php echo $topic->id ?>"><br />
			<input id="newpostbutton" type="submit" value="SUBMIT">
</form>

<script>

$(function() {
  $("#newpostbutton").click(function() {

	var tvalue = $("#tvalue").val();
	var topicid = $("input#topicid").val();
	var dataString = 'value=' + tvalue +'&topicid='+topicid;

	$.ajax({
	type: "POST",
	url: "<?php echo url::site('comments/submit') ?>",
	data: dataString,
	success: function() {
		window.location.reload();
	}
	});
	return false;
  });
});
</script>


</div>
<?php } ?>
</div>
</div>
</div>