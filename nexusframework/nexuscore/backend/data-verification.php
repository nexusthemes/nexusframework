<?php
function nxs_nexuscom_data_verification() 
{
	if (nxs_has_adminpermissions())
	{
		if ($_REQUEST["nxs"] == "dataverification")
		{
			?>
			<?php do_action("nxs_render_frontendeditor"); ?>
			<?php
			
			require_once("data-verification-impl.php");
			nxs_data_verification();
			die();
		}
	}
}

add_action('nxs_bodybegin', 'nxs_nexuscom_data_verification');


function nxs_sm_processstate_dataverification($result)
{
	require_once("data-verification-impl.php");
	$currentstate = $_REQUEST["currentstate"];
	$result = nxs_sm_processstate_dataverification_impl($currentstate);
	return $result;
}

add_action('admin_menu', 'nxs_data_verification_addadminpages', 11);

function nxs_data_verification_addadminpages() {
	add_submenu_page("nxs_backend_overview", 'Data Verification', 'Data Verification', 'switch_themes', 'nxs_data_verification_page_content', 'nxs_data_verification_page_content', '', 81 );
}

add_filter("nxs_sm_processstate_dataverification", "nxs_sm_processstate_dataverification");

function nxs_data_verification_page_content() {
	$url = nxs_geturl_home();
	$url = nxs_addqueryparametertourl_v2($url, "nxs", "dataverification", true, true);
  ?>
  <div class="wrap">
    <h2>Data verification</h2>
    <p>
    	<a href='<?php echo $url; ?>' class='button button-primary'>Start data verification</a>
    </p>
    
	</div>
	<?php
}
/* --------------------------------------------------------------------- */
?>