<script>
var subCallback =
{
  success: function(o){},
  failure: function(o){}
};
function delBan(uId){
	YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('boards/delBan').'/'?>", subCallback,"boardid=<?php echo $board->id ?>&userid="+uId);
}
function delSub(uId){
	YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/delSub')?>", subCallback,"boardid=<?php echo $board->id ?>&userid="+uId);
}
function allow(uId){
	YAHOO.util.Connect.asyncRequest('POST', "<?php echo url::site('subscriptions/allow')?>", subCallback,"boardid=<?php echo $board->id ?>&userid="+uId);
}
</script>

<div>
<h2 style="margin-bottom:0px;padding-bottom:2px;">Manage Board: <?php echo $board->title ?></h2>
<?php if($board->privacy != 3 || $isAdmin) { ?>
<form method="POST" action="<?php echo url::site('boards/edit')?>">
	<h2 style="margin-bottom:0px;padding-bottom:2px;">Privacy Setting:</h2>
	<input type="radio" name="privacy" value="0" <?php if($board->privacy == 0) { echo "checked"; } ?> ><b>Public</b> : Anyone can see, comment, and post new topics.<br/>
	<input type="radio" name="privacy" value="1" <?php if($board->privacy == 1) { echo "checked"; } ?> ><b>Protected</b> : Anyone can see and comment.<br/>
	<input type="radio" name="privacy" value="2" <?php if($board->privacy == 2) { echo "checked"; } ?> ><b>Private</b> : Only approved members can see & post.<br/>
	<?php if($isAdmin) {?>
	<input type="radio" name="privacy" value="3" <?php if($board->privacy == 3) { echo "checked"; } ?> ><b>Hushed</b> : Only subscribed members can see in "all" view.<br/>
	<?php } ?>
	<input type="hidden" name="boardid" value="<?php echo $board->id ?>">
	<input type="submit" value="SAVE">
</form>
<?php }else{ ?>
	Your board has been "hushed", probably because of abuse.  You can not modify it's privacy settings.
<?php } ?>
<form>
<table><tr>
<?php if($board->privacy == 2) { ?>
		<th><h2 style="margin-bottom:0px;padding-bottom:2px;">Pending</h2></th>
<?php } ?>
		<th><h2 style="margin-bottom:0px;padding-bottom:2px;">Subscribers</h2></th>
		<th><h2 style="margin-bottom:0px;padding-bottom:2px;">Banned</h2></th>
</tr>
<tr>
<?php if($board->privacy == 2) { ?>
<td>
<?php if(sizeof($pusers) == 0){ echo "<li>None</li>"; }?>
<ul>
<?php foreach($pusers as $buser){?>
<li>
<?php echo html::anchor('profile/'.$buser->usernmae, '<span class="title">'.$buser->title.'</span>'); ?>
 <input type="button" value="ALLOW" onClick="allow(<?php echo $buser->id;?>);this.style.display='none';">
</li>
<?php } /*endforeach*/  ?>
</ul>
</td>
<?php } ?>
<td>
<?php if(sizeof($susers) == 0){ echo "<li>None</li>"; }?>
<ul>
<?php foreach($susers as $buser){?>
<li>
<?php echo html::anchor('users/'.$buser->id, '<span class="title">'.$buser->title.'</span>'); ?>
 <input type="button" value="DROP" onClick="delSub(<?php echo $buser->id;?>);this.style.display='none';">
</li>
<?php } /*endforeach*/  ?>
</ul>
</td>
<td>
<?php if(sizeof($users) == 0){ echo "<li>None</li>"; }?>
<ul>
<?php foreach($users as $buser){?>
<li>
<?php echo html::anchor('users/'.$buser->id, '<span class="title">'.$buser->title.'</span>'); ?>
 <input type="button" value="UNBAN" onClick="delBan(<?php echo $buser->id;?>);this.style.display='none';">
</li>
<?php } /*endforeach*/  ?>
</ul>
</td>
</tr></table>

</div>