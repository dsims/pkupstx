<h1>Are you sure you want to delete?</h1>
<i><?php echo $topic->title ?></i><br />
<form method="POST" action="<?php echo url::site('topics/delete') ?>">
	<input type="submit" value="YES">
	<input type="hidden" name="topicId" value="<?php echo $topic->id ?>">
</form>
<form method="POST" action="<?php echo url::site('topics/view').'/'.$topic->id ?>">
	<input type="submit" value="CANCEL">
</form>