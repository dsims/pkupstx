<h1>I'm going to have to see some identification...</h1>

<p>Login using your account at Yahoo, Google, AOL, Windows Live (hotmail, Xbox Live, MSN), or any OpenID.
<br />
Don't have one?  Then get an OpenID from <a href="https://www.myopenid.com/signup">myOpenID.com</a>
</p>
<div style="text-align:center;margin-top:20px;">
<iframe src="https://nerr.rpxnow.com/openid/embed?token_url=<?php 
		$subdomain = $_SERVER['SERVER_NAME'];
		//list($subdomain) = explode(".", $subdomain);
		echo urlencode('http://'.$subdomain.'/auth/rpx');
?>"
  scrolling="no" frameBorder="no" style="width:400px;height:240px;">
</iframe>
	<br >
</div>
	<br /><br />
	<br /><br/>
	Or, if you set a password, you can use the alternate <?php echo html::anchor('auth/basic', 'old-school login') ?>.
	<br />If you have any problems, post in the chat box or send an email to francis<b></b>@nerr.biz
