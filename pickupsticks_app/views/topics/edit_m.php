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
</table>
		<table>
        <tr><td>
			<textarea id="editor1" name="body" cols="500" style="width:680px;height:100px;"><?php echo $topic->body ?></textarea>
		</td></tr>
	</table>
</form>
<script>
function textCounter(field,cntfield,maxlimit) {
	if (field.value.length > maxlimit) // if too long...trim it!
	field.value = field.value.substring(0, maxlimit);
	// otherwise, update 'characters left' counter
	else
	$("#remLen1").text(maxlimit - field.value.length);
}
</script>