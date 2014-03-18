<?php

// patcht alle pagina's in een site naar posts, tenzij het de 404 of home zijn
function nxs_apply_patch20130604002()
{
	$publishedargs["post_type"] = array("page");
	$publishedargs["orderby"] = "post_date";
	$publishedargs["order"] = "DESC";	
	$publishedargs["numberposts"] = -1;	// allemaal!
	
  $posts = get_posts($publishedargs);
  foreach ($posts as $currentpost)
  {
    $postid = $currentpost->ID;
    
    if (nxs_ishomepage($postid))
    {
    }
    else if (nxs_is404page($postid))
    {
    }
    else
    {
	    nxs_converttopost($postid);
		}
  }
  
  //
  
	echo "patch finished";
  
  $output = ob_get_contents();
	ob_end_clean();
	
	echo "output:" . $output;
	
	return $output;
}

?>