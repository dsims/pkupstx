<?php // product info
?>
<h1>Share a Product</h1>

<a href="http://zxing.appspot.com/scan?ret=<?php echo urlencode(url::site('topics/xzingscan').'/?code={CODE}') ?>" data-role="button">SCAN</a>
<a href="<?php echo url::site('products/create/') ?>" data-role="button">ADD</a>
<form action="<?php echo url::site('boards/search') ?>" method="GET">
Search: <input type="search" name="terms">
<input type="hidden" name="type" value="g">
</form>
