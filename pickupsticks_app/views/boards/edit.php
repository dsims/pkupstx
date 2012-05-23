<h1>SUBMIT board</h1>
<form method="post" action="<?php echo url::site('boards/submit') ?>">
<table>
<tr><td>Title:</td><td><input type="text" name="title" value="<?php echo $board->title ?>"></td></tr>
<tr><td>Description:</td><td><textarea name="description"><?php echo $board->description ?></textarea></td></tr>
<tr><td colspan="2"><input type="submit" value="Save" name="Save"></td></tr>
</table>
</form>