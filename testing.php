<?php
require_once('autoload.php');

$a = new levidurfee\AttImport\ImportOld;
$a->loadFile('csv/2014-12.csv');
$a->insertTextRecords();