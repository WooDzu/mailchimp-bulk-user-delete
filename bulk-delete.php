<?php

die('Unlock first');

//const EMAILS_FILE = 'test.csv';
const EMAILS_FILE = 'emails.csv';
const API_KEY = '';
const LIST_ID = '';

function pleasedelete(array $emails) {
    /**
    This Example shows how to pull the Info for a Member of a List using the MCAPI.php 
    class and do some basic error checking.
    **/
    require_once 'inc/MCAPI.class.php';
    require_once 'inc/config.inc.php'; //contains apikey
    
    $api = new MCAPI(API_KEY);

    // $retval = $api->listUnsubscribe( $_REQUEST["listid"],$email,true,false,false);
    // $retval = 'todo: batchUnsubscribe';
    $retval = $api->listBatchUnsubscribe(LIST_ID, $emails, true, false, false);
    // todo: batchUnsubscribe

    if ($api->errorCode){
        echo "Unable to load listUnsubscribe()!\n";
    	echo "\tCode=".$api->errorCode."\n";
    	echo "\tMsg=".$api->errorMessage."\n";
        return false;
    } else {
        var_dump($retval);
    }
    return true;
}

if (!file_exists(EMAILS_FILE)) { // I got this code somewhere ...
    die("Error: File does no exists<br />"); 
}

$row = 0;
$batch = 0;
$emails = array();

if (($handle = fopen(EMAILS_FILE, "r")) !== FALSE) {

    // run forever
    set_time_limit(0);

    while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) { // $Data = row data
            
        $row++;

		// Get the email address
		$emails[] = $data[0];

        if ($row == 1000) {
            ob_start();
		    pleasedelete($emails);
            $buff = ob_get_clean();

            $batch++;

            $l = "\n\n---------------BATCH $batch -------------------\n\n";

            file_put_contents(__DIR__.'/log.log',  $l . $buff. PHP_EOL, FILE_APPEND);
            $row = 0;
            $emails = array();
        }
    }

    // Process last batch
	ob_start();
	pleasedelete($emails);
    $buff = ob_get_clean();
    $batch++;

    $l = "\n\n--------------LAST BATCH $batch -------------------\n\n";

    file_put_contents(__DIR__.'/log.log',  $l . $buff. PHP_EOL, FILE_APPEND);
}
              
              
echo "\n Processed ". (1000 * ($batch - 1) + $row);
