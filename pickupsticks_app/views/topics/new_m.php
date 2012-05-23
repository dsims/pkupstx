<?php // product info
?>
<h1><?php echo $board->title ?></h1>

Care to share?
<form id="newpostform" name="newpostform" method="POST" action="<?php echo url::site('topics/submit') ?>" rel="external">
<div data-role="fieldcontain">

<label for="title">Post :<br />(<span id="remLen1">256</span>)</label>
<input id="title" type="text" name="title" maxlength="255"
							  onKeyDown="textCounter(document.newpostform.title,document.newpostform.remLen1,256)"
							  onKeyUp="textCounter(document.newpostform.title,document.newpostform.remLen1,256)"
							  " />
</div>
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
						  
	<?php if($board->type == 'u' && $user->board_id != $board->id){?>
		<input type="hidden" id="isPrivate" name="isPrivate" value="1"/>
			<br />(Private)
			<?php } ?>
<div data-role="fieldcontain">			
<input type="hidden" id="boardid" name="boardid" value="<?php echo $board->id ?>"/>
			<label for="name" class="ui-input-text">More Text:</label>
			<textarea id="editor1" name="body"></textarea>
</div>			
<input type="submit" value="SUBMIT">			
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
