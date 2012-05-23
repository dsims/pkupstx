<h1>Review <?php echo $game->title ?></h1>
<form id="newpostform" name="newpostform" method="POST" action="<?php echo url::site('topics/submit') ?>">
<div data-role="fieldcontain">
 <fieldset data-role="controlgroup" data-type="horizontal">
    	<legend>Rate this product:</legend>
<input id="radio-choice-1" type="radio" name="rating" value="1"> 
<label for="radio-choice-1">Good</label>
<input id="radio-choice-2"  type="radio" name="rating" value="2"> 
<label for="radio-choice-2">Meh</label>
<input id="radio-choice-3"  type="radio" name="rating" value="3">
<label for="radio-choice-3">LOVE</label>
</fieldset>
</div>
<table>
<tr><td align="right">Summary:<br />(<span id="remLen1">256</span>)</td>
	<td colspan="3"><textarea name="title" maxlength="255"
							  onKeyDown="textCounter(document.newpostform.title,document.newpostform.remLen1,256)"
							  onKeyUp="textCounter(document.newpostform.title,document.newpostform.remLen1,256)"
							  style="width:550px;height:36px;"></textarea></td>
	<td><input type="submit" value="SUBMIT"></td>
</tr>
</table>
<table>
        <tr><td>
			<textarea id="editor1" name="body" cols="500" style="width:680px;height:100px;"></textarea>
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
