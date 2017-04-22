<?php

$file = 'migrate_step_2.log';
$current = file_get_contents($file);
$current = '';
$host = "https://svc3.ffmalpha.com";
$productIds = [];



function logme(& $cur, $msg = "") {
    $cur .= $msg . "\n";
}




file_put_contents($file, $current);


?>
