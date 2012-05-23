<?php if($game->mc_score > 0 || $isAdmin) { ?>
<a title="Metacritic Score" href="<?php echo $game->mc_url ?>"><div style="margin-top:5px;padding:5px;background:<?php echo $game->color ?>;float:right"><span style="color:black;font-size:20px;"><?php echo $game->mc_score ?></span></div></a>
<?php 
	if($isAdmin) { echo html::anchor('games/refreshMetaScore/'.$game->id, '<div style="float:right">RELOAD</div>');  }
}
?>
<?php 
	if($isLoggedIn) { echo html::anchor('topics/review/'.$game->id, '<div style="float:right">REVIEW</div>');  }
?>

<h1 style="display:inline;line-height:50px;"><?php echo $game->title ?></h1> <span style="font-size:smaller;"><?php echo $tagstr ?></span>

<div class="rightlist" style="color:white;text-align:center;">
	<div class="listheader">User Stats</div>
</div>
<div style="text-align:center;font-weight:bold;font-size:large;">
<?php echo $owned ?> <?php echo html::anchor('games/own/'.$game->id.'/'.slug::format($game->title), "Own") ?>,
<?php echo $want ?> <?php echo html::anchor('games/want/'.$game->id.'/'.slug::format($game->title), "Want") ?>,
<?php echo $reviewed ?> <?php echo html::anchor('games/reviews/'.$game->id.'/'.slug::format($game->title), "Reviewed") ?>
</div>

<div class="rightlist" style="color:white;text-align:center;">
	<div class="listheader">Latest Posts</div>
</div>
<?php
foreach($topics as $topic){ ?>

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
<div style="text-align:center">>><?php echo html::anchor('topics/board/'.$game->board_id.'/'.slug::format($game->title), "VIEW MORE POSTS") ?><<</div>

<div class="rightlist" style="color:white;text-align:center;">
	<div class="listheader">Images</div>
<?php
$base = new Base_Model();
foreach($pics as $pic) {

	echo sprintf('<a href="%1$s" rel="gb_imageset[%2$d]"><img class="imggallery" src="%1$s" alt="%1$s" title="%1$s" /></a> ', $pic->url, '1');

} ?>
</div>

<div class="rightlist" style="color:white;text-align:center;">
	<div class="listheader">Videos</div>
<?php
$base = new Base_Model();
foreach($vids as $vid) {

	echo $base->encodeFun($vid->url).'<br /><br />';

} ?>
</div>
