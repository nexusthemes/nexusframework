<?php

// patcht alle posts en pagina's in een site;
// de globalids werden niet altijd goed gezet
// waardoor een restore van exported data verkeerde sidebar/header/footer/subheader/subfooter
// kan opleveren.
function nxs_apply_patch20130602001()
{
	$publishedargs["post_type"] = array("post", "page");
	$publishedargs["orderby"] = "post_date";
	$publishedargs["order"] = "DESC";	
	$publishedargs["numberposts"] = -1;	// allemaal!
	
  $posts = get_posts($publishedargs);
  foreach ($posts as $currentpost)
  {
    $postid = $currentpost->ID;
    echo "[" . $postid . "]";
		$pagemeta = nxs_get_postmeta($postid);

		$pmd = array();
		$pmd["header_postid_globalid"] = nxs_get_globalid($pagemeta["header_postid"], true);
		$pmd["subheader_postid_globalid"] = nxs_get_globalid($pagemeta["subheader_postid"], true);
		$pmd["sidebar_postid_globalid"] = nxs_get_globalid($pagemeta["sidebar_postid"], true);
		$pmd["subfooter_postid_globalid"] = nxs_get_globalid($pagemeta["subfooter_postid"], true);
		$pmd["footer_postid_globalid"] = nxs_get_globalid($pagemeta["footer_postid"], true);

		// update pagemeta
		nxs_merge_postmeta($postid, $pmd);
  }
  
  //
  
	echo "patch finished";
  
  $output = ob_get_contents();
	ob_end_clean();
	
	echo "output:" . $output;
	
	return $output;
}

?>