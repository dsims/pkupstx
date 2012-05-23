<div class="topicslisttop">
<div class="toptitle" style="<?php if($board->id > 0){ echo "height:60px;"; } ?>">
<h1 style="display:inline;">
<?php if ($board->id > 0){
	if($board->type == 'u'){
		if(isset($boarduser))
			echo html::anchor('profile/'.$boarduser->username, '<img style="vertical-align:middle" src="'.url::base().'content/images/avatars/create/'.$boarduser->avatar.'_50.gif">');
		echo " $board->title's board";
	}
	else{echo $board->title." Posts"; }
}
elseif($type == 'dm'){
	echo "Direct Messages";
}
elseif ($list->id > 0){
    echo $list->title;
}
elseif($all==0){echo "Subscribed Topics";}// | ".$typename;}
else{echo $typename." Posts";}
if($isArchive){ echo ' <span style="font-size:.6em;color:#A9A9A9">older than '.$oldtimeformat.'</span>'; }
if($board->privacy == 2){ echo ' <span style="font-size:.6em;color:#A9A9A9">(private)</span>'; }
if($board->privacy == 3){ echo ' <span style="font-size:.6em;color:#A9A9A9">(hushed)</span>'; }
?>
</h1>
</div>
	<div style="float:right">
<?php
if(isset($user) && $user->id > 0)
{
	if($board->id > 0)
		echo '<a type="button" href="'.url::site('topics/post/'.$board->id).'">Post</a>';
	else
		echo '<a type="button" href="'.url::site('topics/startnew/'.$board->id).'">Post</a>';	
}
if($board->type == 'g')
	{
		echo html::anchor('games/'.$board->owner_id.'/'.slug::format($board->title), "View product details");
	}
?>
	</div>
</div>
<?php
if($all == 0 && $board->id == 0 && (!isset($user) || $user->id == 0)){
    echo "oops?";
}
else{
?>
</div> <!-- end full body -->

<div data-role="controlgroup" data-type="horizontal">
	<?php $i=0;foreach($tabs as $tab=>$url){$i++; if($tab=='Board'){continue;}?>
	<a type="button" href="<?php echo url::site($url)?>"><?php echo $tab ?></a>
	<?php } ?>
</div>

<ul data-role="listview">
<?php $topicIds = '0'; $lasttopictime = 0; $oldtopictime = 0;
foreach($topics as $topic){ $topicIds .= ','.$topic->id; $oldtopictime = $topic->date_submit; if($topic->date_submit > $lasttopictime){$lasttopictime=$topic->date_submit;}?>
<li>
<div id="topic-<?php echo $topic->id ?>" class="listtopic">

<div class="embed-mini" style="float:right;<?php if(strlen($topic->urlsdisplaymini)) { echo 'padding-left:10px;width:100px;'; }?>">
	
<?php if(strlen($topic->urlsdisplaymini)) { ?>
	<div class="url-embed-mini">
	<?php echo $topic->urlsdisplaymini; ?>
	</div>
<?php }?>

</div>

<div class="title" style="margin-left:15px;">
<?php echo html::anchor('topics/'.$topic->id.'/'.$topic->slug, ''.$topic->title) ?>
</div>

<div class="topicinfo">
<div class="date">
	Posted by <?php echo html::anchor('profile/'.$topic->username, '<span style="color:black;">'.$topic->user.'</span>') ?>
 <abbr class="timeago" title="<?php echo $topic->date_added_iso?>"><?php echo $topic->date_added_format ?></abbr>
<?php if(($topic->boardtype != 'u' || $topic->board_id != $topic->user_board_id) && $board->id == 0) { ?>
 to <?php echo html::anchor('topics/board/'.$topic->board_id.'/'.$topic->boardslug, '<span style="color:black;">'.$topic->board.'</span>');?>
<?php } ?>
</div>
<?php if(strlen($topic->urldomain)){ ?>
	<div class="urldomain"><a href="<?php echo $topic->urldomainlink ?>" target="_blank" style="color:black;" title="<?php echo $topic->urldomainlink ?>"><?php echo $topic->urldomain ?></a></div>
<?php } ?>
</div>

<div class="commentarea" id="comments-<?php echo $topic->id ?>">
    <?php if($topic->comments == 1) { ?>
	<a href="<?php echo url::site('topics/'.$topic->id.'/'.$topic->slug) ?>"><?php echo $topic->comments ?></a>
	<?php } if($topic->comments >= 1) { ?>
    <?php if(strlen($topic->comment_first_avatar)){?><?php echo html::anchor('profile/'.$topic->comment_first_username, '<img alt="'.$topic->comment_first_user.'" title="'.$topic->comment_first_user.'" src="'.url::base().'content/images/avatars/create/'.$topic->comment_first_avatar.'.gif">');?> <?php } else { ?>>><?php } ?> <?php echo $topic->comment_first ?>
    <?php } if($topic->comments >= 2) { ?>
	<a href="<?php echo url::site('topics/'.$topic->id.'/'.$topic->slug) ?>"><?php echo $topic->comments ?></a>
	<?php if(strlen($topic->comment_last_avatar)){?><?php echo html::anchor('profile/'.$topic->comment_last_username, '<img alt="'.$topic->comment_last_user.'" title="'.$topic->comment_last_user.'" src="'.url::base().'content/images/avatars/create/'.$topic->comment_last_avatar.'.gif">');?> <?php } else { ?>>><?php } ?></div> <?php echo $topic->comment_last ?>
	<?php } ?>
</div>
</div>
</li>
<?php } /*endforeach*/ ?>

<script>lastTopicTime = <?php echo $lasttopictime ?>;</script>

<?php if(sizeof($topics)==30) { ?>
<li>
<div class="listtopic" style="text-align:center;">
<a href="<?php $tempget = $_GET; unset($tempget['archive']);unset($tempget['boardid']); echo URL::site(url::current()).'?archive='.$oldtopictime.'&'.http_build_query($tempget, '&'); ?>">VIEW OLDER &darr;</a>
</div>
</li>
<?php } ?>
</ul>
</div>
<? } ?>
<div><!-- start for template -->
