<script type="text/javascript">
var lastComment = 0;
var topicId = 0;
var lastCommentId = 0;

function checkForComments(schedule)
{
	var comments = document.getElementById('comments');
	$.getJSON(
	  "<?php echo url::site('comments/get') ?>",
	  { topicid:<?php echo $topic->id ?>, last: lastComment },
	  function(o){
            var data=o;
			tdata=data.data;
            for (var i = 0, len = tdata.length; i < len; ++i) {
                var m = tdata[i];
				if(lastComment == m.id)
					continue;
				lastComment = m.date_added;
				lastCommentId = m.id;
                var div = document.createElement('div');
				div.className = 'commentrow';
				div.id = 'comment-'+m.id;
				var ctext = '';
				if(m.user_avatar.length > 0) {
					ctext += '<div class="commentavatar-view"><a href="<?php echo url::site('users') ?>/'+m.user_id+'"><img src="<?php echo url::base() ?>content/images/avatars/create/'+m.user_avatar+'.gif"></a></div>';
				}
				else
					ctext += '>>';
				ctext += m.comment;
				ctext += '<div class="date">Reply by <span style="display:inline"><a href="<?php echo url::site('users') ?>/'+m.user_id+'" class="commentuser">'+m.user + '</a> <abbr class="timeago" title="'+m.date_added_iso+'">'+m.date_added_format+'</abbr>';
				<?php if(isset($user) && $user->id > 0 ) { ?>
						 <?php if($board->owner_id == $user->id || $isAdmin) { ?>
							 ctext +=  ' - <div style="display:inline;" class="deleteC_link" rel="'+m.id+'"><a href="#" onclick="return false;">Delete</a></div>';
							 if(m.user_id != <?php echo $user->id?>){
								ctext +=  ' | <div style="display:inline;" class="banC_link" rel="'+m.id+'"><a href="#" onclick="return false;">Ban</a></div>';
							 }
							 if(m.user_id == <?php echo $user->id ?>){
								ctext +=  ' | <div style="display:inline;" class="editC_link" rel="'+m.id+'"><a href="#" onclick="return false;">Edit</a></div>';
								}
						 <?php } else if($topic->user_id == $user->id) { ?>
							 ctext +=  ' - <div style="display:inline;" class="deleteC_link" rel="'+m.id+'"><a href="#" onclick="return false;">Delete</a></div>';
							 if(m.user_id == <?php echo $user->id ?>){
								ctext +=  ' | <div style="display:inline;" class="editC_link" rel="'+m.id+'"><a href="#" onclick="return false;">Edit</a></div>';
								}
						<?php }else { ?>
							 if(m.user_id == <?php echo $user->id ?>){
							ctext += ' - <div style="display:inline;" class="deleteC_link" rel="'+m.id+'"><a href="#" onclick="return false;">Delete</a></div>';
							ctext +=  ' | <div style="display:inline;" class="editC_link" rel="'+m.id+'"><a href="#" onclick="return false;">Edit</a></div>';
							ctext += '';
							}
						<?php } ?>
				<?php } ?>
				ctext += '</div>';

				div.innerHTML = ctext;
                comments.appendChild(div);
				jQuery('abbr.timeago').timeago();
            }
            var interval = 10000
            if(data.interval > 10000)
                interval = data.interval;
            if(schedule)
                setTimeout("checkForComments(true)", interval);
        });
}
</script>
<div id="myMarkedUpContainer"><div id="dialog"></div>
<div id="topic" class="listtopic" style="border:none;padding:0px;">
<?php if(strlen($topic->avatar)) { ?>
<div class="avatar"><?php echo html::anchor('profile/'.$topic->username, '<img src="'.url::base().'content/images/avatars/create/'.$topic->avatar.'_50.gif">');?></div>
<?php } ?>
<div class="title" style="padding-top:5px;padding-bottom:5px;"><?php echo $topic->title ?></div>
<div style="padding-bottom:10px"><div class="date">

Posted by
 <?php echo html::anchor('profile/'.$topic->username, '<span style="color:black;">'.$topic->user.'</span>') ?>
 <abbr class="timeago" title="<?php echo $topic->date_added_iso?>"><?php echo $topic->date_added_format ?></abbr> in <?php echo html::anchor('topics/board/'.$topic->board_id.'/'.slug::format($topic->board), '<span style="color:black;">'.$topic->board.'</span>');?>
	<div class="likearea" style="display:inline;" rel="<?php echo (isset($user) && $topic->user_id != $user->id && $user->id > 0 ) ? $topic->id : 0; echo '-'.$topic->like1.'-'.$topic->like2.'-'.$topic->like3; ?>">
		- <?php
		if($topic->like1 > 0){
			echo '<img src="'.url::base().'content/images/like1.png" />'.$topic->like1.' ';
		}
		if($topic->like2 > 0){
			echo '<img src="'.url::base().'content/images/like2.png" />'.$topic->like2.' ';
		} if($topic->like3 > 0){
			echo '<img src="'.url::base().'content/images/like3.png" />'.$topic->like3.' ';
		} ?> <a href="#" onclick="return false;">Like?</a>
	</div>
<?php if(isset($user)  && $user->id > 0 && ($board->owner_id == $user->id || $topic->user_id == $user->id || $isAdmin)) { ?>
	<?php if($topic->user_id == $user->id || $isAdmin) { ?>
<?php if($topic->locked == 0) { ?>
 - <div style="display:inline;" rel="<?php echo $topic->id ?>" class="lock_link"><a href="#" onclick="return false;">Lock</a></div>
 <?php } else { ?>
 - <div style="display:inline;" rel="<?php echo $topic->id ?>" class="unlock_link"><a href="#" onclick="return false;">Unlock</a></div>
 <?php } ?>
 - <div style="display:inline;"><a class="edit_link" href="<?php echo url::site('topics/edit/'.$topic->id) ?>">Edit</a></div>
 - <div style="display:inline;" rel="<?php echo $topic->id ?>" class="delete_link"><a href="#" onclick="return false;">Delete</a></div>
	<?php }
}?>
</div></div>

<div class="topicarea">
<?php if(sizeof($topic->urlsdisplay) > 0) { ?>
<div class="url-embed"><?php echo $topic->urlsdisplay ?></div>
<?php } ?>
<div class="body"><?php echo $topic->body?></div>
<br />
<?php if(sizeof($relatedTopics) > 0) { echo 'Related Posts:'; ?>
	<ul>
	<?php foreach($relatedTopics as $related) {?>
		<li><?php echo html::anchor('topics/'.$related->id.'/'.$related->slug, ''.$related->title) ?> - <span class="date"><?php echo $related->username ?></span></li>
	<?php } ?>
	</ul>
	<br />
<?php } ?>
		<?php if($topic->comments > 0) { echo 'Replies:'; } ?>
<br /><br/>
	
<div id="commentarea" class="commentarea">
<?php $lastcomment = 0;$lastcommentid=0;
foreach($comments as $comment){ if($comment->date_added > $lastcomment){$lastcomment=$comment->date_added;$lastcommentid = $comment->id;}?>
<div class="commentrow" id="comment-<?php echo $comment->id?>">
		<div class="commentavatar-view"><?php if(strlen($comment->user_avatar)){?><?php echo html::anchor('profile/'.$comment->username, '<img src="'.url::base().'content/images/avatars/create/'.$comment->user_avatar.'.gif">');?> <?php } else { ?>>><?php } ?></div>
		<?php echo $comment->comment ?>
		<div class="date" style="display:block">Reply by <span style="display:inline"><?php echo html::anchor('profile/'.$comment->username, $comment->user) ?> <abbr class="timeago" title="<?php echo $comment->date_added_iso?>"><?php echo $comment->date_added_format ?></abbr>
		<?php
		if(isset($user)){
			if($board->owner_id == $user->id || $isAdmin) { ?>
			- <div style="display:inline;" class="deleteC_link" rel="<?php echo $comment->id ?>"><a href="#" onclick="return false;">Delete</a></div>
			<?php if($comment->user_id != $user->id){ ?>
			- <div style="display:inline;" class="banC_link" rel="<?php echo $comment->id ?>"><a href="#" onclick="return false;">Ban</a></div>
			<?php } ?>
		<?php }
			  else if($topic->user_id == $user->id || $comment->user_id == $user->id) { ?>
			- <div style="display:inline;" class="deleteC_link" rel="<?php echo $comment->id ?>"><a href="#" onclick="return false;">Delete</a></div>
		<?php } if($comment->user_id == $user->id){ ?>
			- <div style="display:inline;"><a href="<?php echo url::site('comments/edit/'.$comment->id) ?>">Edit</a></div>
		<?php  }
		} ?>
		</div>
</div>
<?php } /*endforeach*/ ?>
<div id="comments"></div>
</div>


<?php if($topic->locked == 1) { echo 'Topic is Locked'; } else if(isset($user)){ ?>
<div style="padding-left:15px">
<script>
	$(document).ready(
	function () {
		$("#newpostform").submit(function() {
			var datatosubmit = $('#newpostform').serialize();
			datatosubmit += "&ckBody="+encodeURIComponent(CKEDITOR.instances.editor1.getData());
			$.post("<?php echo url::site('comments/submit') ?>", datatosubmit,
				function(data){
						CKEDITOR.instances.editor1.setData( '', function()
						{
							  CKEDITOR.instances.editor1.resetDirty();
						} );
						//clear fields
						$(':input','#newpostform')
						 .not(':button, :submit, :reset, :hidden, #boardidselectu, #boardidselect')
						 .val('');
						checkForComments(false);
				}, "json"
			);
			return false;
		});
	});
</script>
<br />
<form id="newpostform" name="newpostform" method="POST" action="<?php echo url::site('comments/submit') ?>" onsubmit="return false;">
			<textarea id="editor1" name="value" cols="300" style="width:640px;height:100px;"></textarea>
			<input type="hidden" name="topicid" value="<?php echo $topic->id ?>">
			<script type="text/javascript">
				CKEDITOR.replace( 'editor1', {
					linkShowAdvancedTab : false,
					linkShowTargetTab : false,
					width : '640px',
					toolbar :
						[
							['NewPage'],
							['Undo','Redo','-','PasteText','RemoveFormat','-','Find'],
							['NumberedList','BulletedList','Blockquote'],
							['JustifyLeft','JustifyCenter'],
							['Link','Unlink'],
							['Image','SpecialChar'],
							'/',
							['Bold','Italic','Underline','Strike'],
							['Font','FontSize'],
							['TextColor','BGColor']
						]
				} );
			</script>
			<input id="newpostbutton" type="submit" value="SUBMIT">
</form>
</div>
<?php }else{  ?>
Sign-in to post a reply.
<?php } ?>
<script>lastComment = <?php echo $lastcomment ?>;lastCommentId = <?php echo $lastcommentid ?>;</script>
</div>
</div>
</div>

<script>
	$("div.likearea").live("click", function()
	{
		var vals = $(this).attr('rel');
		vals = vals.split('-');
		var dialogbuttons;
		var dialogtitle = "What do you think about this post?";
		if(vals[0]==0)
		{
			dialogbuttons = {"Cancel": function() { $(this).dialog("close"); }};
			dialogtitle = "What people think about this post";
		}
		else
			dialogbuttons = {
				"Cancel": function() { $(this).dialog("close"); },
				"Cookie": function(){$.post("<?php echo url::site('topics/addLike')?>", { type: 3, topicId: vals[0] } );$(this).dialog("close");},
				"Dislike": function(){$.post("<?php echo url::site('topics/addLike')?>", { type: 2, topicId: vals[0] } );$(this).dialog("close");},
				"Like": function(){$.post("<?php echo url::site('topics/addLike')?>", { type: 1, topicId: vals[0] } );$(this).dialog("close");}
			};
		//$("#dialog").append('TEST');
		$('<div style="text-align:left"><img src="<?php echo url::base().'content/images/' ?>like1.png"> <b>'+vals[1]+'</b> <b>like</b> it.<br /><img src="<?php echo url::base().'content/images/' ?>like2.png"> <b>'+vals[2]+'</b> <b>dislike</b> it.<br /><img src="<?php echo url::base().'content/images/' ?>like3.png"> <b>'+vals[3]+'</b> gave it a <b>cookie</b>!*<br /><br />*You can only give one cookie per day.</div>').dialog(
		{
			title: dialogtitle,
			modal: true,
			width:400,
			height:220,
			autoOpen: true,
			buttons: dialogbuttons
		});
	});

	$("div.delete_link").live("click", function()
	{
		var vals = $(this).attr('rel');
		vals = vals.split('-');
		var dialogbuttons;
		var dialogtitle = "Are you sure?";
		dialogbuttons = {
			"Cancel": function() { $(this).dialog("close"); },
			"DELETE": function(){$.post("<?php echo url::site('topics/delete')?>/"+vals[0]);$(this).dialog("close");$("div#topic").hide();}
		};
		$('<div style="text-align:left">Are you sure you want to <span style="color:red;font-weight:bold;">delete</span> this topic?</div>').dialog(
		{
			title: dialogtitle,
			modal: true,
			width:400,
			height:220,
			autoOpen: true,
			buttons: dialogbuttons
		});
	});

	$("div.deleteC_link").live("click", function()
	{
		var vals = $(this).attr('rel');
		vals = vals.split('-');
		var dialogbuttons;
		var dialogtitle = "Are you sure?";
		dialogbuttons = {
			"Cancel": function() { $(this).dialog("close"); },
			"DELETE": function(){$.post("<?php echo url::site('comments/delete')?>/"+vals[0]);$(this).dialog("close");$("#comment-"+vals[0]).hide();}
		};
		$('<div style="text-align:left">Are you sure you want to <span style="color:red;font-weight:bold;">delete</span> this comment?</div>').dialog(
		{
			title: dialogtitle,
			modal: true,
			width:400,
			height:220,
			autoOpen: true,
			buttons: dialogbuttons
		});
	});

	$("div.banC_link").live("click", function()
	{
		var vals = $(this).attr('rel');
		vals = vals.split('-');
		var dialogbuttons;
		var dialogtitle = "Are you sure?";
		dialogbuttons = {
			"Cancel": function() { $(this).dialog("close"); },
			"BAN": function(){$.post("<?php echo url::site('boards/ban')?>", {cid:vals[0]});$(this).dialog("close");}
		};
		$('<div style="text-align:left">Are you sure you want to <span style="color:red;font-weight:bold;">ban</span> this user from this board?</div>').dialog(
		{
			title: dialogtitle,
			modal: true,
			width:400,
			height:220,
			autoOpen: true,
			buttons: dialogbuttons
		});
	});

	$("div.lock_link").live("click", function()
	{
		var vals = $(this).attr('rel');
		vals = vals.split('-');
		var dialogbuttons;
		var dialogtitle = "Are you sure?";
		dialogbuttons = {
			"Cancel": function() { $(this).dialog("close"); },
			"Lock": function(){$.post("<?php echo url::site('topics/lock')?>/"+vals[0]);$(this).dialog("close");$("div.lock_link").removeClass('lock_link').addClass('unlock_link');}
		};
		$('<div style="text-align:left">Are you sure you want to lock this topic?</div>').dialog(
		{
			title: dialogtitle,
			modal: true,
			width:400,
			height:220,
			autoOpen: true,
			buttons: dialogbuttons
		});
	});
	$("div.unlock_link").live("click", function()
	{
		var vals = $(this).attr('rel');
		vals = vals.split('-');
		var dialogbuttons;
		var dialogtitle = "Are you sure?";
		dialogbuttons = {
			"Cancel": function() { $(this).dialog("close"); },
			"UnLock": function(){$.post("<?php echo url::site('topics/lock')?>/"+vals[0]);$(this).dialog("close");$("div.unlock_link").removeClass('unlock_link').addClass('lock_link');}
		};
		$('<div style="text-align:left">Are you sure you want to unlock this topic?</div>').dialog(
		{
			title: dialogtitle,
			modal: true,
			width:400,
			height:220,
			autoOpen: true,
			buttons: dialogbuttons
		});
	});

	$("div.editC_link").live("click", function()
	{
		var vals = $(this).attr('rel');
		vals = vals.split('-');
		window.location="<?php echo url::site('comments/edit') ?>/"+vals[0];
	});
setTimeout("checkForComments(true)", 10000);
</script>
<script type="text/javascript" src="http://scripts.embed.ly/embedly.js" ></script>