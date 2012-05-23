<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width = 320" />
	<title><?php echo $title; ?></title>

	<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/south-street/jquery-ui.css" />
	<link rel="stylesheet"  href="http://code.jquery.com/mobile/1.0b2/jquery.mobile-1.0b2.min.css" /> 
	<script src="http://code.jquery.com/jquery-1.6.2.min.js"></script> 
	<script src="http://code.jquery.com/mobile/1.0b2/jquery.mobile-1.0b2.min.js"></script> 
	<script type="text/javascript" src="<?php echo url::base(); ?>content/js/jquery-ui-1.7.2.custom.min.js"></script>	
	<script type="text/javascript" src="<?php echo url::base(); ?>content/js/lib/jquery.autocomplete.min.js"></script>	
	<link rel="stylesheet" type="text/css" href="<?php echo url::base(); ?>content/js/lib/jquery.autocomplete.css" />	
	<style>
	.date{
	font-size:.8em;
	}
	</style>

<script type="text/javascript" src="<?php echo url::base(); ?>content/js/jquery.timeago.js"></script>

</head>

<body>

<div data-role="page" id="home">

 <div data-role="header">
		<div class="navigation">
			<div class="navigationbg"></div>
			<div class="navinside">
		<?php
				$menu = html::anchor_array(array('posts' => 'Posts'));

				if(!isset($user)){
				echo  html::anchor('', 'Home').' - ';
				}
				foreach($menu as $menuitem)
					echo ' '.$menuitem . ' - ';
				echo  ' '.html::anchor('boards/browse/products', 'Products').' - ';
				echo  ' '.html::anchor('lists', 'Lists').' - ';
				if(isset($user)){
					echo  ' '.html::anchor('users/edit','Edit Profile').' - ';
					echo html::anchor('profile/'.$user->username, $user->title) .' - '. html::anchor('auth/logout', 'Sign Out');
				}else{
					echo html::anchor('auth/basic/', 'Sign In');
				}
		?>
			</div>
		</div>

</div>
	 <div data-role="content">
	<?php echo $content ?>
	</div>
	<div data-role="footer"><a type="button" href="http://contact.pkupstx.com">Contact us for more info</a></div>

</div>
<script>
jQuery(document).ready(function() {
	jQuery("#fafboardidselect").autocomplete('<?php echo url::site('boards/ddselectany') ?>',{
		width:300,
		mustMatch: true
	});
	jQuery("#fafboardidselect").result(function(event, data, formatted) {
		if(data == undefined)
			return;
		var hidden = $('#fafboardid');
		hidden.val( data[1]);
	});
  jQuery('abbr.timeago').timeago();
});</script>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-CODE";
urchinTracker();
</script>
</body>
</html>