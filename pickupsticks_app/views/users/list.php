<h1>LIST OF USERS</h1>

<table class="listing">
<?php foreach($users as $user){ ?>
<tr>
<td><a href="users/<?php echo $user->id?>/<?php echo $user->twitter_name?>"><?php echo $user->username?></a></td>
<td><?php if(strlen($user->twitter_name)) { ?><a href="http://twitter.com/<?php echo $user->twitter_name ?>"><?php echo $user->twitter_name ?></a><?php } else echo '&nbsp;' ?></td>
<td><?php echo date::printTime($user->date_added) ?></td>
</tr>
<?php } /*endforeach*/ ?>
</table>