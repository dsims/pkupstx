<ul>
<?php
$log = new Eventlog_Model();
foreach($logs as $log){
echo '<li>'.$log->readout;
echo '</li>';
 } ?>
</ul>