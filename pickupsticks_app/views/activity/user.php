<div style="height:60px;">
	<div style="float:right;padding-top:10px;text-align:right;">
	<?php echo $posts ?> topics<br />
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
	<?php $i=0;foreach($tabs as $tab=>$url){$i++; if($tab=='Board'){continue;}?>
	<a type="button" href="<?php echo url::site($url)?>"><?php echo $tab ?></a>
	<?php } ?>
</div>

<div id="profile-container" style="text-align:left">



<ul>
<?php
$log = new Eventlog_Model();
foreach($logs as $log){
echo '<li>'.$log->readout;
echo '</li>';
 } ?>
</ul>