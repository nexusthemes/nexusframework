<?php
function nxs_webmethod_removecomment() 
{	
	extract($_REQUEST);
	
	if ($commentid == "")
	{
		nxs_webmethod_return_nack("commentid niet gevuld");
	}
	if ($postid == "")
	{
		nxs_webmethod_return_nack("postid niet gevuld");
	}
	
	$haspermission = false;
	if (nxs_has_adminpermissions())
	{
		$haspermission = true;
	}

	if (!$haspermission)
	{
		nxs_webmethod_return_nack("insufficient rights to delete comment");
	}
	
	$result = wp_delete_comment( $commentid );
	
	if ($result == false)
	{
		nxs_webmethod_return_nack("unable to delete comment;");
	}
	
	//
	// create response
	//
	$responseargs = array();
	nxs_webmethod_return_ok($responseargs);
}
?>