<?php	
	//require_once(NXS_FRAMEWORKPATH . '/page-template.php');
	
	global $nxs_global_current_containerpostid_being_rendered;
	$containerpostid = $nxs_global_current_containerpostid_being_rendered;
	$wpbackendblogcontent = get_post_field('post_content', $containerpostid);
	$wpbackendblogcontent = wpautop($wpbackendblogcontent, true);
	echo nxs_applyshortcodes($wpbackendblogcontent);
?>