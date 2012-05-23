<script>
var subCallback =
{
  success: function(o){},
  failure: function(o){}
};
function ebAddRequest(bId){
	YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/addExcludeBoard').'/'?>"+bId, subCallback,null);
}
function ebDelRequest(bId){
	YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/delExcludeBoard').'/'?>"+bId, subCallback,null);
}
function euAddRequest(id){
	YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/addExcludeUser').'/'?>"+id, subCallback,null);
}
function euDelRequest(id){
	YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/delExcludeUser').'/'?>"+id, subCallback,null);
}
</script>

<div>

<?php if($loggedin){ ?>
<div style="width:100%;">
<h2 style="margin-bottom:0px;padding-bottom:2px;">Your Subscriptions</h2>
<i>Topics in your subscriptions will appear on your homepage and can be added to lists.</i><br />
<div style="float:left;clear:right;width:25%;">
<h2 style="margin-bottom:0px;padding-bottom:2px;">General</h2><?php echo html::anchor('boards/browse/', '[Find more message boards]'); ?><ul>
<?php foreach($subs as $board){?>
<li>
<?php echo html::anchor('topics/?boardid='.$board->id, '<span class="title">'.$board->title.'</span>'); ?>
 : <span class="description"><?php echo $board->description ?></a></span>
</li>
<?php } /*endforeach*/  ?>
</ul>

<?php if(sizeof($hiddenboards) > 0){?>
<h2 style="margin-bottom:0px;padding-bottom:2px;">Hidden Boards</h2>
<form>
<ul>
<?php foreach($hiddenboards as $board){?>
<li>
<?php echo html::anchor('topics/?boardid='.$board->id, '<span class="title">'.$board->title.'</span>'); ?>
 <input type="button" value="UNHIDE" onClick="ebDelRequest(<?php echo $board->id;?>);this.style.display='none';">
</li>
<?php } /*endforeach*/  ?>
</ul>
<?php } ?>

</div>
<div style="float:left;clear:right;width:25%;"><h2 style="margin-bottom:0px;padding-bottom:2px;">Games</h2><?php echo html::anchor('boards/browse/games', '[Find more Games]'); ?><ul>
<?php foreach($gsubs as $board){?>
<li>
<?php echo html::anchor('topics/?boardid='.$board->id, '<span class="title">'.$board->title.'</span>'); ?>
 : <span class="description"><?php echo $board->description ?></a></span>
</li>
<?php } /*endforeach*/  ?>
</ul>

<?php if(sizeof($hiddengboards) > 0){?>
<h2 style="margin-bottom:0px;padding-bottom:2px;">Hidden Boards</h2>
<form>
<ul>
<?php foreach($hiddengboards as $board){?>
<li>
<?php echo html::anchor('topics/?boardid='.$board->id, '<span class="title">'.$board->title.'</span>'); ?>
 <input type="button" value="UNHIDE" onClick="ebDelRequest(<?php echo $board->id;?>);this.style.display='none';">
</li>
<?php } /*endforeach*/  ?>
</ul>
<?php } ?>

</div>
<div style="float:left;clear:right;width:25%;"><h2 style="margin-bottom:0px;padding-bottom:2px;">Users</h2><?php echo html::anchor('boards/browse/users', '[Find more Users]'); ?><ul>
<?php foreach($usubs as $board){?>
<li>
<?php echo html::anchor('topics/?boardid='.$board->id, '<span class="title">'.$board->title.'</span>'); ?>
 : <span class="description"><?php echo $board->description ?></a></span>
</li>
<?php } /*endforeach*/  ?>
</ul>

<?php if(sizeof($hiddenuboards) > 0){?>
<h2 style="margin-bottom:0px;padding-bottom:2px;">Hidden Boards</h2>
<form>
<ul>
<?php foreach($hiddenuboards as $board){?>
<li>
<?php echo html::anchor('topics/?boardid='.$board->id, '<span class="title">'.$board->title.'</span>'); ?>
 <input type="button" value="UNHIDE" onClick="ebDelRequest(<?php echo $board->id;?>);this.style.display='none';">
</li>
<?php } /*endforeach*/  ?>
</ul>
<?php }
if(sizeof($hiddenusers) > 0){?>
<h2 style="margin-bottom:0px;padding-bottom:2px;">Hidden Users</h2>
<form>
<ul>
<?php foreach($hiddenusers as $usr){?>
<li>
<?php echo html::anchor('topics/?boardid='.$usr->board_id, '<span class="title">'.$usr->title.'</span>'); ?>
 <input type="button" value="UNHIDE" onClick="euDelRequest(<?php echo $usr->id;?>);this.style.display='none';">
</li>
<?php } /*endforeach*/  ?>
</ul>
<?php } ?>

</div>
<div style="float:left;clear:right;width:25%;"><h2 style="margin-bottom:0px;padding-bottom:2px;">Lists</h2><?php echo html::anchor('lists', '[Edit your lists]'); ?><ul>
<?php foreach($lists as $list){?>
<li>
<?php echo html::anchor('topics/?listid='.$list->id, '<span class="title">'.$list->title.'</span>'); ?>
</li>
<?php } /*endforeach*/  ?>
</ul></div>
</div>

<?php } else { ?>
<h2>Message Board Directory</h2>
<?php echo html::anchor('boards/browse/', 'Browse General Message Boards'); ?>
<br /><?php echo html::anchor('boards/browse/games', 'Browse Games'); ?>
<br /><?php echo html::anchor('boards/browse/users', 'Browse Users'); ?>
<br/>
<?php } ?>
</div><br /><br />
<div style="clear:left;">
<hr />
<?php if($loggedin){echo html::anchor('boards/create', '[Create a new board]');} ?> -
<?php if($loggedin){echo html::anchor('games/create', '[Add a new Game]');} ?>
</div>
