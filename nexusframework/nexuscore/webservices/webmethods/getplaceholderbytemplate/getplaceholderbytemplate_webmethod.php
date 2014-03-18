<?php
function nxs_webmethod_getplaceholderbytemplate() 
{
	extract($_REQUEST);

	if ($postid == "")
	{
		nxs_webmethod_return_nack("postid not set /nxs_webmethod_getplaceholderbytemplate/");
	}
	if ($placeholderid == "")
	{
		nxs_webmethod_return_nack("placeholderid not set");
	}
 	
	$placeholdertemplate = nxs_getplaceholdertemplate($postid, $placeholderid);

 	$result = "<span class='draggable_placeholder' id='nxs_x_ph_" . $placeholderid . "' class='" . nxs_getplaceholdericonid($placeholdertemplate) . "'></span>";
	
	$args = array
	(
		"html" => $result,
	);	
 	
	nxs_webmethod_return_ok($args);
}
?>