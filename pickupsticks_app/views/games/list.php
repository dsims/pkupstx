<h1>LIST OF PRODUCTS</h1>

<table class="listing">
<?php foreach($games as $game){ ?>
<tr>
<td><a href="games/<?php echo $game->id?>/<?php echo $game->slug?>"><?php echo $game->title?></a></td>
</tr>
<?php } /*endforeach*/ ?>
</table>