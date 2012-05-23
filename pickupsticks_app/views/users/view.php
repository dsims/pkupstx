<div style="height:60px;">
	<?php if($isLoggedIn && !$isfollowing){ ?>
	<div style="float:right" id="subbutton" >
	<input type="button" value="FOLLOW" onClick="subAddRequest();">
	<script>
	function subAddRequest(){
		$.post("<?php echo url::site('subscriptions/addfriend').'/'.$user->id; ?>" );
		$('#subbutton').hide();
	}
	</script>		
	</div>
	<?php } ?>
	<div style="float:right;padding-top:10px;text-align:right;">
	<?php echo $posts ?> posts<br />
	<?php echo $replies ?> replies
	<div>
	<?php foreach($awardcounts as $award){ $awardtypename = ($award->type == 3) ? 'Gold' : (($award->type == 2) ? 'Silver' : 'Bronze');?>
	<span title="<?php echo $awardtypename ?> medals" class="awardtype<?php echo $award->type?>">â—�</span> <?php echo $award->count ?>
	<?php } ?>
	</div>
	</div>
	<div style="float:left;padding:10px 10px 0px 0px;"><?php if(strlen($user->avatar)){ ?><img valign="middle" src="<?php echo url::base(); ?>content/images/avatars/create/<?php echo $user->avatar ?>_50.gif"> <?php } ?></div>
	<h1 style="margin:0px;padding-top:10px;"><?php echo $user->title ?></h1>
	<?php if($user->created > 0) {?>joined  <?php echo ($user->old_regdate > 0) ? date('M Y',$user->old_regdate) : date('M Y',$user->created); }?> <?php if(strlen($user->old_username)){ if($isLoggedIn){?>(<a href="http://digibutter.nerr.biz/profile.php?mode=viewprofile&u=<?php echo $user->old_user_id ?>" >*</a><?php echo $user->old_username ?><?}else{echo $user->old_username;}?>) <?php }?>
</div>
</div><!-- end full body -->
<div data-role="controlgroup" data-type="horizontal">
	<?php $i=0;foreach($tabs as $tab=>$url){$i++; if($i==$selectedTab){/*continue;*/}?>
	<a type="button" href="<?php echo url::site($url)?>"><?php echo $tab ?></a>
	<?php } ?>
</div>

<div id="profile-container">
<?php  if($user->sammer != '' && (date('Y-W') == '2011-05' || date('Y-W') == '2011-06')){?>
	<h1>Sammer</h1>
	<?php echo $user->sammername ?>
	<img class="imgtree" src="<?php echo url::base(); ?>content/images/sammerguys/<?php echo strlen($user->sammer) ? 'create/'.$user->sammer : 'sammerbase.png' ?>">
<?php } ?>
<?php  if($user->tree != '' && (date('n') == '12' || date('W') == '1') ){?>
	<img class="imgtree" src="<?php echo url::base(); ?>content/images/trees/<?php echo strlen($user->tree) ? 'create/'.$user->tree : 'sammerbase.png' ?>">
<?php } ?>
<h1>Badges</h1>
<div>
<?php if(sizeof($awards) == 0){echo "NONE";}foreach($awards as $award){ ?>
<a href="<?php echo url::site('awards').'/'.$award->id.'/'.slug::format($award->title); ?>"><span class="awardbutton"><span class="awardtype<?php echo $award->type?>">â—�</span> <span title="<?php echo $award->description ?>"><?php echo $award->title ?></span></span></a>
<?php } ?>
</div>
<h1>Products</h1>
<table class="listing" style="text-align:left;width:100%;"><tr><th>Own</th><th>Want</th></tr>
<tr>
<td valign="top">
<?php if(sizeof($own) == 0){echo "NONE";}foreach($own as $game){ ?>
<?php echo html::anchor('games/'.$game->id.'/'.slug::format($game->title), $game->title) ?><br />
<?php } /*endforeach*/ ?>
</td>
<td valign="top">
<?php if(sizeof($want) == 0){echo "NONE";}foreach($want as $game){ ?>
<?php echo html::anchor('games/'.$game->id.'/'.slug::format($game->title), $game->title) ?><br />
<?php } /*endforeach*/ ?>
</td>
</tr>
</table>
