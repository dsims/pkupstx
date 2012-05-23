<?php switch($step) {
			 case 0:?>
Hmm, i dont see you in our records.  Are you new here?
<br/>
<form method="post">
<input type="hidden" name="step" value="1">
<input type="submit" value="YES">
</form>
<?php break; case 1:?>
Oh ok, welcome!  I'm BorderMeow, what's your name?
<br/>
<form method="post">
<input type="hidden" name="step" value="1">
<input type="text" name="name">
<input type="submit" value=">>">
</form>
<?php break; case 2:?>
Oh, that's a great name for a guy.
<br/>
<form method="post">
<input type="hidden" name="step" value="1">
<input type="text" name="name">
<select name="response">
	<option selected>Thanks</option>
	<option selected>No!  I'm female!</option>
	<option selected>Ha!  I'm not male OR female!</option>
</select>
<input type="submit" value=">>">
</form>
<?php break; case 3:?>
Ok then.  I have to take your picture, but our camera is broken, so you are going to have to draw your picture for us.
Just click in the box to draw, and press done when ready.
<form method="post">
<input type="submit" value="DONE">
</form>
<?php break; case 4:?>
Beautiful!  I think I have everything I need now.  You can change your information later if you want.
Take a look at the New Users message board to introduce yourself and ask questions.  Have fun!
<?php break;} ?>