<h2>Your Lists of Products</h2>
<p>You can group products how ever you like.  Create a List and add any products you want.  So when you view a List, you
will only see posts from products in that list.</p>

<div id="boardlists">
Create New List: <form style="display:inline;" action="<?php echo url::site('lists/addlist') ?>" method="POST">
<input type="text" name="title">
<input type="submit" value="Submit">
</form>
<hr>
    <script>
	/*
    var oDS = new YAHOO.util.XHRDataSource("<?php echo url::site('subscriptions/get/') ?>");
    oDS.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;
    oDS.responseSchema = {
        resultsList : "data", // String pointer to result data
        fields : [
            { key: "board_title" },
            { key: "board_id" }
        ]
    };
    oDS.maxCacheEntries = 5;

    function AddAC(id)
    {
        var oAC = new YAHOO.widget.AutoComplete("boardtitle-"+id, "boardselect-"+id, oDS);
        oAC.forceSelection = true;
        var boardidField = YAHOO.util.Dom.get("boardid-"+id);
        var boardHandler = function(sType, aArgs) {
            var myAC = aArgs[0];
            var elLI = aArgs[1];
            var oData = aArgs[2];
            boardidField.value = oData[1];
        };
        oAC.itemSelectEvent.subscribe(boardHandler);
    }
	*/
    </script>

<script>
function AddAC(id)
{
	$("#boardidselect-"+id).autocomplete('<?php echo url::site('boards/ddselectany') ?>',{
		width:300,
		mustMatch: true
	});
	$("#boardidselect-"+id).result(function(event, data, formatted) {
		if(data == undefined)
			return;
		var hidden = $('#boardid-'+id);
		hidden.val( data[1]);
	});
}
</script>

<?php foreach($lists as $list){?>
<div id="boardlist-<?php echo $list->id ?>" class="boardlist">
<?php echo html::anchor('topics/?listid='.$list->id, '<span class="title">'.$list->title.'</span>'); ?>
	<form style="display:inline;" action="<?php echo url::site('lists/delete') ?>" method="POST">
    <input style ="margin-left:20px;" type="submit" value="Delete">
    <input type="hidden" name="listid" value="<?php echo $list->id; ?>">
    </form>
<ul>
<?php foreach($list->GetBoards() as $board){?>
    <li>
    <?php echo html::anchor('topics/?boardid='.$board->id, ''.$board->title.''); ?>
    <form style="display:inline;" action="<?php echo url::site('lists/deleteboard') ?>" method="POST">
    <input style="margin-left:20px;"  type="submit" value="Delete">
    <input type="hidden" name="boardid" value="<?php echo $board->id; ?>">
    <input type="hidden" name="listid" value="<?php echo $list->id; ?>">
    </form>
    </li>
<?php } ?>
</ul>
<form action="<?php echo url::site('lists/addboard') ?>" method="POST">
    <input type="hidden" id="boardid-<?php echo $list->id; ?>" name="boardid">
    <input type="hidden" id="listid" name="listid" value="<?php echo $list->id; ?>">
        <!--<input style="width:15em;" id="boardtitle-<?php echo $list->id; ?>" type="text"><div style="width:15em;" id="boardselect-<?php echo $list->id; ?>"></div>-->
					<input id="boardidselect-<?php echo $list->id; ?>" name="boardidselect" style="width:200px"	/>
    <script>
        AddAC("<?php echo $list->id; ?>");
    </script>
    <input type="submit" value="Add to <?php echo $list->title ?>">
</form>

</div>
<br />
<?php } /*endforeach*/ ?>
</div>
