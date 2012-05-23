<style>
input#twbox {
  font-size:            3.0em;
  font-family:          Georgia, Times New Roman, Times, serif;
  width:                360px;
  margin:               0px auto 0em;
  padding:              0em;
  background-color:     #FFFFFF;
  border:               2px solid #777;
  color:                #00CCFF;
}
input#xtwbox:hover, input#twbox:focus {
  color:                #00CCFF;
}
input#xtwbox:focus {
  background-color:     #fff;
}
div#profilebutton{text-align:center;padding:0px;margin:0px;}
input.profilesubmit{
    line-height:24px;
    background-color:#0066FF;
    font-size:2em;
    color:#fff;
    font-weight:bold;
    padding:6px;
    margin:0px;
    border:2px solid #00CCFF;
    cursor:pointer;
    -moz-border-radius:.4em;
    vertical-align:top;
    text-align:center;}
input.profilesubmit:hover{background-color:#00CCFF;}
.xjoin{text-align:center;}
.xjoin input{background-color:red;color:#417596;color:white;font-size:11pt;padding:.3em 2.5em;font-weight:bold;border:1px solid black;}
.xjoin input:hover{background-color:#294B60;}
</style>

<h1>Welcome!</h1>
<p>Please tell us your twitter username so we can load your info and friends!</p><br />
<form method="post">
<div id="profilebutton">
<input length="16" maxlength="16" id="twbox" type="text" name="twitter_name" value="<?php echo $user->twitter_name ?>"> <input type="submit" class="profilesubmit" id="join" name="load" value="LOAD"/>
</div>
</form>