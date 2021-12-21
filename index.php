<?php
header('Access-Control-Allow-Origin: *');
header("Content-type: application/json; charset=utf-8");
require_once 'LogParser.php';
$logParser = new LogParser('access_log');
echo $logParser->responseLog();
