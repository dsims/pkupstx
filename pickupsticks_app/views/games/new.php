<style>
input#sbox {
  font-size:            3.0em;
  font-family:          Georgia, Times New Roman, Times, serif;
  width:                90%;
  margin:               0.5em auto 0.2em;
  padding:              0.2em;
  background-color:     #FFFFFF;
  border:               1px solid #777;
  color:                #00CCFF;
}
input#sbox:hover, input#sbox:focus {
  color:                #00CCFF;
}
input#sbox:focus {
  background-color:     #fff;
}
.submit-button#search {
  text-align:           center;
  line-height:          2em;
  margin:               1em 0 4em;
}
input[type=submit]#search {
  font-family:          helvetica, arial, sans-serif;
  padding:              0.2em 0.5em;
  font-size:            2em;
  color:                #0033CC;
  background-color:     #fff;
  border:               1px solid #00CCFF;
}
input[type=submit]:hover#search {
  background-color:     #0033CC;
  color:                #fff;
}
</style>

<h2>Add a new product:</h2>
<form method="post" action="<?php echo url::site('games/submit') ?>" rel="external">
<div data-role="fieldcontain">
<label for="code">UPC (optional):</label>
<input id="code" type="text" name="code">
<label for="title">Product Name:</label>
<input id="title" type="text" name="title">
<input type="submit" value="Save">
</div>
</form>