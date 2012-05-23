<?php
$log = new Eventlog_Model();
foreach($logs as $log){
echo $log->id.' - '.$log->formatTime($log->date_added).' - ';
echo Eventlog_Model::$Types[$log->type];
echo ' - '.$log->target_id.' - '.$log->location_id.' - '.$log->username.' - '.$log->username2.'<br />';

 } ?>