<script>
var handleHideBoard = function(event, id) {
        YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/addExcludeBoard').'/'?>"+id, {success: function(o){},failure: function(o){}},null);
        this.hide();
};
var handleHideUser = function(event, id) {
        YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/addExcludeUser').'/'?>"+id, {success: function(o){},failure: function(o){}},null);
        this.hide();
};
var handleHideTopic = function(event, id) {
        YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/addExcludeTopic')?>", {success: function(o){},failure: function(o){}},'topicId='+id);
        this.hide();
};
var handleDeleteTopic = function(event, id) {
        YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('topics/delete').'/'?>"+id, {success: function(o){},failure: function(o){}},null);
        var topic = document.getElementById('topic-'+id);
		topic.style.display = 'none';
		//this.hide();
};
var handleBanTopic = function(event, id) {
        YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('boards/ban')?>", {success: function(o){},failure: function(o){}},'tid='+id);
};
var handleNo = function() {
        this.hide();
};

function containerHandler(e) {
	var elTarget = YAHOO.util.Event.getTarget(e);
	while (elTarget != null && elTarget.id != "topic-container") {
		if(elTarget.nodeName.toUpperCase() == "DIV") {
            if (elTarget.id.substring(0,8) == "exclude-"){
                YAHOO.util.Event.preventDefault(e);

                var ids = elTarget.id.substring(8);
                var i = ids.indexOf('-', 0);
                var bid = ids.substr(0, i);
                ids = ids.substr(i+1, ids.length-i-1);
				i = ids.indexOf('-', 0);
                var uid = ids.substr(0, i);
				var tid = ids.substr(i+1, ids.length-i-1);
                // Instantiate the Dialog
                var simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                 { width: "300px",
                   fixedcenter: true,
                   visible: false,
                   draggable: false,
                   close: true,
                   text: '<span style="color:black">Do you want to Hide all posts from this <b>Board</b> or from this <b>User</b>?</span>',
                   icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                   constraintoviewport: true,
                   buttons: [ { text:"Hide Board", handler:{ fn: handleHideBoard, obj: bid } , isDefault:true },
                                { text:"Hide User", handler:{ fn: handleHideUser, obj: uid } , isDefault:true },
								<?php if($isAdmin) { ?>
								{ text:"Hide Topic from Pubic", handler:{ fn: handleHideTopic, obj: tid } , isDefault:true },
								<?php } ?>
                                          { text:"Cancel",  handler:handleNo } ]
                 } );
                simpledialog1.setHeader("Are you sure?");
                elTarget.id = "xx";
                elTarget.innerHTML = '';
                // Render the Dialog
                simpledialog1.render(elTarget);
                simpledialog1.show();
                break;
            }
			else if (elTarget.id.substring(0,7) == "delete-"){
                YAHOO.util.Event.preventDefault(e);

                var id = elTarget.id.substring(7);
                // Instantiate the Dialog
                var simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                 { width: "300px",
                   fixedcenter: true,
                   visible: false,
                   draggable: false,
                   close: true,
                   text: '<span style="color:black">Are you sure you want to delete this entire topic?</span>',
                   icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                   constraintoviewport: true,
                   buttons: [ { text:"DELETE", handler:{ fn: handleDeleteTopic, obj: id } },
                                          { text:"Cancel",  handler:handleNo, isDefault:true } ]
                 } );
                simpledialog1.setHeader("Are you sure?");
                elTarget.id = "xx";
                elTarget.innerHTML = '';
                // Render the Dialog
                simpledialog1.render(elTarget);
                simpledialog1.show();
                break;
            }
			else if (elTarget.id.substring(0,4) == "ban-"){
                YAHOO.util.Event.preventDefault(e);
                var id = elTarget.id.substring(4);
                // Instantiate the Dialog
                var simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1",
                 { width: "300px",
                   fixedcenter: true,
                   visible: false,
                   draggable: false,
                   close: true,
                   text: '<span style="color:black">You want to <b>ban</b> this user from this board?</span>',
                   icon: YAHOO.widget.SimpleDialog.ICON_HELP,
                   constraintoviewport: true,
                   buttons: [ { text:"BAN", handler:{ fn: handleBanTopic, obj: id } },
                                          { text:"Cancel",  handler:handleNo, isDefault:true } ]
                 } );
                simpledialog1.setHeader("Are you sure?");
                elTarget.id = "xx";
                elTarget.innerHTML = '';
                // Render the Dialog
                simpledialog1.render(elTarget);
                simpledialog1.show();
                break;
            }
			else if (elTarget.id.substring(0,5) == "edit-"){
                YAHOO.util.Event.preventDefault(e);
				var id = elTarget.id.substring(5);
				window.location="<?php echo url::site('topics/edit') ?>/"+id;
                break;
            }
			else {
                elTarget = elTarget.parentNode;
            }
        }
        else {
            elTarget = elTarget.parentNode;
        }
    }
}

</script>


<?php if ($board->id > 0){?>
<div style="float:right;text-align:right;">
	<?php if (isset($user) && (($board->owner_id == $user->id) || $isAdmin)){?>
		<?php echo html::anchor('boards/manage/'.$board->id, "Manage");?>
	<?php } ?>
	Subscribers: <?php echo $board->subscribers ?><br/>
<?php if (isset($user) && $user->id > 0){ ?>
<script>
var subCallback =
{
  success: function(o){},
  failure: function(o){}
};
function subAddRequest(){
	YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/add').'/'.$board->id; ?>", subCallback,null);
}
function subDelRequest(){
	YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/del').'/'.$board->id; ?>", subCallback,null);
}
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
<div id='edit_post_div' style='display:none;'>
	<div class="hd">Edit Post</div>
	<div class="bd">Loading...</div>
</div>
<form id="subform">
<?php if (!$hasboard){ ?>
<input type="button" value="SUBSCRIBE" onClick="subAddRequest();this.style.display='none';">
<?php } else { ?>
<input type="button" value="UNSUBSCRIBE" onClick="subDelRequest();this.style.display='none';">
<?php } ?>
<?php if (!$boardexcluded){ ?>
<input type="button" value="HIDE BOARD" onClick="ebAddRequest(<?php echo $board->id;?>);this.style.display='none';">
<?php } else { ?>
<input type="button" value="UNHIDE BOARD" onClick="ebDelRequest(<?php echo $board->id;?>);this.style.display='none';">
<?php } ?>
<?php if ($board->type == 'u')
    {
        if(!$userexcluded){ ?>
<input type="button" value="HIDE USER" onClick="euAddRequest(<?php echo $board->owner_id;?>);this.style.display='none';">
<?php   } else { ?>
<input type="button" value="UNHIDE USER" onClick="euDelRequest(<?php echo $board->owner_id;?>);this.style.display='none';">
<?php   }
    }
?>
</form>

<?php }//end if user ?>
</div>
<?php } //end if board ?>
<div style="padding-bottom:10px;margin-top:10px;">
<h1 style="display:inline;">
<?php if ($board->id > 0){
	if($board->type == 'u'){
		if(isset($boarduser))
			echo html::anchor('users/'.$boarduser->id, '<img style="vertical-align:middle" src="'.url::base().'content/images/avatars/create/'.$boarduser->avatar.'_50.gif">');
		echo " $board->title's board";
	}
	else{echo "Message Board: $board->title"; }
}
elseif($type == 'dm'){
	echo "Direct Messages";
}
elseif ($list->id > 0){
    echo "List: $list->title";
}
elseif($all==0){echo "Subscribed Topics | ".$typename;}
else{echo "ALL Topics ".$typename;}
?> <span style="font-size:.6em;color:darkgrey">older than <?php echo $oldtimeformat ?></span>
</h1>
</div>
<?php
if($all == 0 && $board->id == 0 && (!isset($user) || $user->id == 0)){
    echo "If you were logged-in, this \"Home\" page would show topics from boards you have subscribed to.  Try viewing \"All\" topics instead.";
}
else{

?>
</div>
<div id="topic-container">
<?php $topicIds = '0'; $lasttopictime = 0; $oldtopictime = 0;
foreach($topics as $topic){ $topicIds .= ','.$topic->id; $oldtopictime = $topic->date_submit; if($topic->date_submit > $lasttopictime){$lasttopictime=$topic->date_submit;}?>
<div id="topic-<?php echo $topic->id ?>" class="listtopic"">
<?php if(strlen($topic->avatar)) { ?>
<div class="avatar"><?php echo html::anchor('users/'.$topic->user_id, '<img src="'.url::base().'content/images/avatars/create/'.$topic->avatar.'_50.gif">');?></div>
<?php } ?>
<div class="date">
Posted by <?php echo html::anchor('users/'.$topic->user_id, '<span style="color:black;">'.$topic->user.'</span>') ?> <?php echo '<span style="color:black;">'.$topic->date_added_format.'</span>' ?>
<?php if(($topic->boardtype != 'u' || $topic->board_id != $topic->user_board_id) && $board->id == 0) { ?>
 in <?php echo html::anchor('topics/board/'.$topic->board_id.'/'.$topic->boardslug, '<span style="color:black;">'.$topic->board.'</span>');?>
<?php } ?>
<?php if(isset($user)  && $user->id > 0 && $all){ ?>
<div style="display:inline;" id="exclude-<?php echo $topic->board_id ?>-<?php echo $topic->user_id ?>-<?php echo $topic->id ?>"> - <a href="#">Hide</a></div>
<?php }  ?>
<?php if(isset($user)  && $user->id > 0 && ($board->owner_id == $user->id || $topic->user_id == $user->id || $isAdmin)) { ?>
 - <div style="display:inline;" id="delete-<?php echo $topic->id ?>"><a href="#">Delete</a></div>
	<?php if($topic->user_id == $user->id || $isAdmin) { ?>
 - <div style="display:inline;" id="edit-<?php echo $topic->id ?>"><a class="edit_link" href="#">Edit</a></div>
	<?php } ?>
	<?php if(($board->owner_id == $user->id && $topic->user_id != $board->owner_id) || $isAdmin) { ?>
	 - <div style="display:inline;" id="ban-<?php echo $topic->id ?>"><a href="#">Ban</a></div>
	<?php } ?>
<?php } ?>

</div>
<div class="title"><?php echo html::anchor('topics/'.$topic->id.'/'.$topic->slug, ''.$topic->title.'') ?></div>
<div class="topicarea">
<?php if(strlen($topic->urlsdisplay) > 0) { ?>
<div class="url-embed"><?php echo $topic->urlsdisplay ?></div>
<?php } if($topic->comments > 0) { ?>
<div id="commentarea">
<div class="viewcomments"><?php echo html::anchor('topics/'.$topic->id.'/'.$topic->slug, $topic->comments.' Replies') ?></div>
</div>
<?php } ?>
</div>
</div>
<br />
<?php } /*endforeach*/ ?>
</div>
<?php if(sizeof($topics)==20) { ?>
<div class="listtopic" style="text-align:center;">
<a href="<?php $tempget = $_GET; unset($tempget['archive']);unset($tempget['boardid']); echo URL::site(url::current()).'?archive='.$oldtopictime.'&'.http_build_query($tempget, '&'); ?>">VIEW OLDER &darr;</a>
</div>
<?php } ?>
<script>
YAHOO.util.Event.on("topic-container", "click", containerHandler);
</script>
<?php }//end if loggedin ?>
<div>