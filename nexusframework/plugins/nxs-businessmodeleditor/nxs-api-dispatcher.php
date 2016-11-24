<?php

$api = $_REQUEST["nxs"];	// for example wpdiscountprovider-api
$currenturi = nxs_geturicurrentpage();
$uripieces = explode("/", $currenturi);

if ($uripieces[1] == "api")
{
	$version = $uripieces[2];
	$env = $uripieces[3];
	
	// log the event
	$ip = $_SERVER['REMOTE_ADDR'];
	$time = time();
	$date = date("Ymd H:i:s", $time);
	$method = $_SERVER['REQUEST_METHOD'];
	$logpath = "/srv/mnt/resources/logs/api/{$api}/ip-{$ip}.{$api}.log";
	$httpreferrer = $_SERVER["HTTP_REFERER"];
	$line = "$time|$date|$ip|$method|$httpreferrer|$currenturi\r\n";
	// append log data
	$r = file_put_contents($logpath, $line, FILE_APPEND);
	if ($r === false)
	{
		error_log("api call; failed to write to log $logpath");
	}
	
	$implementationpath = __DIR__ . "/api/{$api}/{$version}/{$env}/{$api}-impl.php";
	
	require_once($implementationpath);

	echo "<br />Nexus API Dispatcher Error #426346 $implementationpath";
	// if we reach this stage, the api didn't die
	die();
}
else
{
	echo "<br />Nexus API Dispatcher Error #436734<br />";
	var_dump($uripieces);
	// if we reach this stage, the api didn't die
	die();
}