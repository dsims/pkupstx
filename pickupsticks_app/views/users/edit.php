<h1>Your Profile</h1>

<form method="post" action="edit" rel="external">
<table style="width=100%">
<tr><td width="100">Name:</td><td><input type="text" name="title" value="<?php echo $user->title ?>"></td></tr>
<tr><td>Set Password:</td><td><input type="password" name="password" maxlength="40"></td></tr>
<tr><td>Email:</td><td><input type="text" name="email" value="<?php echo $user->email ?>"></td></tr>
<tr><td></td><td><input type="submit" value="SAVE"></td></tr>
</table>
</form>