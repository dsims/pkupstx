<?php foreach($awards as $award){ ?>
<br /><a href="<?php echo url::site('awards').'/'.$award->id.'/'.slug::format($award->title); ?>"><span class="awardbutton"><span title="<?php echo $award->description ?>"><?php echo $award->title ?></span></span></a><br />
<?php } ?>