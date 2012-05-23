<script>
var subCallback =
{
  success: function(o){},
  failure: function(o){}
};
function ebDelRequest(bId){
	YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/delExcludeBoard').'/'?>"+bId, subCallback,null);
}
function euDelRequest(id){
	YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/delExcludeUser').'/'?>"+id, subCallback,null);
}
</script>

<h2><?php echo $area; if($type != 'g') echo ' Message Board' ?> Directory</h2>

<form action="<?php echo url::site('boards/search') ?>" method="GET">
<input type="search" name="terms">
<input type="hidden" name="type" value="<?php echo $type ?>">
</form>

<!--<div  style="float:left;clear:right;width:50%;" id="actboards">-->
<?php $width = ($loggedin || $type=='g' ? '33%' : '50%'); ?>
<table width="100%">
<tr valign="top">
	<th style="text-align:left;" width="<?php echo $width ?>"><h2>Recent Activity</h2></th>
	<th style="text-align:left;" width="<?php echo $width ?>"><h2>Newest</h2></th>
	<?php if($type == 'g'){ ?>
	<th style="text-align:left;" width="<?php echo $width ?>"><h2>Most Wanted</h2></th>
	<?php } else if($loggedin){ ?>
	<th style="text-align:left;" width="<?php echo $width ?>"><h2>Subscribed</h2></th>
	<?php } ?>
</tr>
<tr valign="top"><td>
<?php foreach($actboards as $board){?>
<div>
<?php
$link = ($type != 'g') ? 'topics/board/'.$board->id.'/'.slug::format($board->title) : 'games/'.$board->owner_id.'/'.slug::format($board->title);
echo html::anchor($link, '<span class="forumlink">'.$board->title.'</span>', array('title'=>$board->description));
?>
</div>
<br />
<?php } /*endforeach*/ ?>
</td>
<td>
<?php foreach($newboards as $board){?>
<div>
<?php
$link = ($type != 'g') ? 'topics/board/'.$board->id.'/'.slug::format($board->title) : 'games/'.$board->owner_id.'/'.slug::format($board->title);
echo html::anchor($link, '<span class="forumlink">'.$board->title.'</span>', array('title'=>$board->description)); ?>
</div>
<br />
<?php } /*endforeach*/ ?>
</td>
<?php if($type == 'g'){ ?>
<td>
	<?php if(sizeof($wanted) == 0){echo 'none';} foreach($wanted as $board){?>
	<div>
	<?php
	$link = ($type != 'g') ? 'topics/board/'.$board->id.'/'.slug::format($board->title) : 'games/'.$board->owner_id.'/'.slug::format($board->title);
	echo html::anchor($link, '<span class="forumlink">'.$board->title.'</span>', array('title'=>$board->description)); ?>
	</div><br />
	<?php } /*endforeach*/ ?>
<?php } if($loggedin){ ?>
	<?php if($type == 'g') {?>
		<h2>Subscribed</h2>
	<?php } else echo '<td>'; ?>
<?php if(sizeof($subs) == 0){echo 'none';} foreach($subs as $board){?>
<div>
<?php echo html::anchor('topics/board/'.$board->id.'/'.slug::format($board->title), '<span class="forumlink">'.$board->title.'</span>', array('title'=>$board->description)); ?>
</div>
<br />
<?php } /*endforeach*/ ?>
<h2>Hidden</h2>
<?php if(sizeof($hiddenboards) == 0){echo 'none';} foreach($hiddenboards as $board){?>
<div>
<?php echo html::anchor('topics/board/'.$board->id.'/'.slug::format($board->title), '<span class="forumlink">'.$board->title.'</span>', array('title'=>$board->description)); ?>
	<input type="button" value="X" onClick="ebDelRequest(<?php echo $board->id;?>);this.style.display='none';">
</div>
<br />
<?php } /*endforeach*/ ?>
<?php if($type == 'u') { ?>
<h2>Hidden Users</h2>
<?php if(sizeof($hiddenusers) == 0){echo 'none';} foreach($hiddenusers as $usr){?>
	<div>
<?php echo html::anchor('topics/board/'.$board->id.'/'.slug::format($board->title), '<span class="title">'.$usr->title.'</span>'); ?>
 <input type="button" value="X" onClick="euDelRequest(<?php echo $usr->id;?>);this.style.display='none';">
<?php } /*endforeach*/  ?>
	</div>
<?php }//type ?>
</td>
<?php }//loggedin ?>

</tr>
</table>