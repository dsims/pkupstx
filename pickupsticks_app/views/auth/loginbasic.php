<h1>Login</h1>
<p>Or <?php echo html::anchor('auth/create', 'Register') ?></p>
<form method="post" rel="external">

<div data-role="fieldcontain">
<label for="username">Username:</label>
<input id="username" type="text" name="username">
<label for="password">Password:</label>
<input id="password" type="password" name="password">
<input type="submit" value="GO!">
</form>
