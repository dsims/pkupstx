<h1>EDIT REPLY</h1>
<div class="title">
	<?php echo html::anchor('topics/'.$topic->id.'/'.$topic->slug, ''.$topic->title) ?><?php if(strlen($topic->titlebody)){echo '<span class="titlebody"> - '.$topic->titlebody.'</span>';} ?>
</div>
<form id="newpostform" name="newpostform" method="POST" action="<?php echo url::site('comments/editsubmit') ?>">
		<table>
        <tr><td>
			<textarea id="editor1" name="comment" cols="500" style="width:680px;height:100px;"><?php echo $comment->comment ?></textarea>
			<script type="text/javascript">
				CKEDITOR.replace( 'editor1', {
					linkShowAdvancedTab : false,
					linkShowTargetTab : false,
					width : '660px',
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
		</td></tr>
		<tr><td><input type="hidden" name="commentId" value="<?php echo $comment->id ?>"><input type="submit" value="SUBMIT"></td></tr>
	</table>
</form>