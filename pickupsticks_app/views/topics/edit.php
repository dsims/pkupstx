<h1>EDIT TOPIC</h1>
<form id="newpostform" name="newpostform" method="POST" action="<?php echo url::site('topics/edit').'/'.$topic->id ?>">
<table>
<tr><td align="right">Post:<br />(<span id="remLen1">256</span>)</td>
	<td colspan="3"><textarea name="title" maxlength="255"
							  onKeyDown="textCounter(document.newpostform.title,document.newpostform.remLen1,256)"
							  onKeyUp="textCounter(document.newpostform.title,document.newpostform.remLen1,256)"
							  style="width:550px;height:36px;"><?php echo $topic->title ?></textarea></td>
	<td><input type="submit" value="SUBMIT"></td>
</tr>
<tr><td>Board</td><td>
			<?php
		if($topic->boardtype != 'dm') { ?>
					In: <input id="boardidselect" name="boardidselect" style="width:200px"
					value="<?php echo $board->title ?>"
					/>
					<input type="hidden" id="boardid" name="boardid" value="<?php echo $board->id ?>"/>
		<?php } ?>
	</td>
</table>
		<table>
        <tr><td>
			<textarea id="editor1" name="body" cols="500" style="width:680px;height:100px;"><?php echo $topic->body ?></textarea>
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
	</table>
</form>
<script>
$(document).ready(function()
{
	$("#boardidselect").autocomplete('<?php echo url::site('boards/ddselect') ?>',{
		width:300,
		mustMatch: true
	});
	$("#boardidselect").result(function(event, data, formatted) {
		if(data == undefined)
			return;
		var hidden = $('#boardid');
		hidden.val( data[1]);
	});
});
function textCounter(field,cntfield,maxlimit) {
	if (field.value.length > maxlimit) // if too long...trim it!
	field.value = field.value.substring(0, maxlimit);
	// otherwise, update 'characters left' counter
	else
	$("#remLen1").text(maxlimit - field.value.length);
}
</script>