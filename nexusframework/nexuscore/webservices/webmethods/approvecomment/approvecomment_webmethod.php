<?php
function nxs_webmethod_approvecomment() 
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
	
	$args = array();
	$args["comment_ID"] = $commentid;
	$args["comment_approved"] = 1;	// 1 = approved
	
	$result = wp_update_comment($args);
	
	if ($result != 1)
	{
		nxs_webmethod_return_nack("unable to approve comment;");
	}
	
	//
	// create response
	//
	$responseargs = array();
	nxs_webmethod_return_ok($responseargs);
}

function nxs_dataprotection_nexusframework_webmethod_approvecomment_getprotecteddata($args)
{
	return nxs_dataprotection_factor_createprotecteddata("webmethod-none");
}

?>