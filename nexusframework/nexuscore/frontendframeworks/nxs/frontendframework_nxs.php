<?php

function nxs_frontendframework_nxs_gethtmlforbutton($args)
{
	extract($args);
	
	if ($button_text == "")
	{
		return "";
	}
	if ($destination_articleid == "" && $destination_url == "" && $destination_js == "")
	{
		return "";
	}		

	$button_alignment = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
	$button_color = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	$button_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $button_fontzen);
	
	if ($destination_articleid != "")
	{
		$posttype = get_post_type($destination_articleid);
		if ($posttype == "attachment")
		{
			$url = wp_get_attachment_url($destination_articleid);
		}
		else
		{
			$url = nxs_geturl_for_postid($destination_articleid);
		}
		$onclick = "";
	}
	else if ($destination_url != "")
	{
		if (nxs_stringstartswith($destination_url, "tel:"))
		{
			// a phone link; if parenthesis or spaces are used; absorb them
			$url = $destination_url;
			$url = str_replace(" ", "", $url);
			$url = str_replace("(", "", $url);
			$url = str_replace(")", "", $url);
		}
		else
		{
			// regular link
			$url = $destination_url;
		}
		$onclick = "";
	}
	else if ($destination_js != "")
	{
		$url = "#";
		$onclick = "onclick='" . nxs_render_html_escape_singlequote($destination_js) . "' ";
	}
	else
	{
		// unsupported
		$url = "nxsunsupporteddestination";
		$onclick = "";
	}
	
	if ($onclick != "")
	{
		$onclick = " " . $onclick . " ";
 	}
 
 	if ($destination_target == "@@@empty@@@" || $destination_target == "")
 	{
 		// auto
 		if ($destination_articleid != "")
 		{
 			// local link = self
 			$destination_target = "_self";
 		}
 		else
 		{
 			$homeurl = nxs_geturl_home();
 			if (nxs_stringstartswith($url, $homeurl))
 			{
 				$destination_target = "_self";
 			}
 			else
 			{
 				$destination_target = "_blank";
 			}
 		}
 	}
 	if ($destination_target == "_self")
 	{
 		$destination_target = "_self";
 	}
 	else if ($destination_target == "_blank")
 	{
 		$destination_target = "_blank";
 	}
 	else
 	{
 		$destination_target = "_self";
	}

	$destination_relation_html = '';
	if ($destination_relation == "nofollow") {
		$destination_relation_html = 'rel="nofollow"';
	}
	
	$result = '';
	$result .= '<p class="' . $button_alignment . ' nxs-padding-bottom0">';
	$result .= '<a target="' . $destination_target . '" ' . $destination_relation_html . ' ' . $onclick . ' class="nxs-button ' . $button_scale_cssclass . ' ' . $button_color . ' ' . $button_fontzen_cssclass . '" href="' . $url . '">' . $button_text . '</a>';
	$result .= '</p>';
	
	return $result;
}

//
// framework css
//
function nxs_framework_theme_styles()
{
	if ($_REQUEST["frontendframework"] != "")
	{
		echo "nxs_framework_theme_styles";
		die();
	}
	
  // Register the style like this for a theme:  
  // (First the unique name for the style (custom-style) then the src, 
  // then dependencies and ver no. and media type)
  
  wp_register_style('nxs-framework-style-css-reset', 
    nxs_getframeworkurl() . '/css/css-reset.css', 
    array(), 
    nxs_getthemeversion(),    
    'all' );
  
  wp_register_style('nxs-framework-style', 
    nxs_getframeworkurl() . '/css/framework.css', 
    array(), 
    nxs_getthemeversion(), 
    'all' );


  if (is_child_theme()) 
  {
  	wp_register_style('nxs-framework-style-child', 
    nxs_getframeworkurl() . '/css/style.css', 
    array(), 
    nxs_getthemeversion(), 
    'all' );

  	// enqueing:
    wp_enqueue_style('nxs-framework-style-child');
	}
  
	// enqueing:
	
	// indien we in de WP backend zitten, dan geen css reset!
	$iswordpressbackendshowing = is_admin();
	if (!$iswordpressbackendshowing)
	{
		wp_enqueue_style('nxs-framework-style-css-reset');
	}
	
  wp_enqueue_style('nxs-framework-style');
    
	if (!$iswordpressbackendshowing)
	{
		$sitemeta = nxs_getsitemeta();  

		wp_register_style('nxs-framework-style-responsive', 
	    nxs_getframeworkurl() . '/css/framework-responsive.css', 
	    array(), 
	    nxs_getthemeversion(),
	    'all' );
	    
	    wp_enqueue_style('nxs-framework-style-responsive');
	}
	
	wp_enqueue_script( 'jquery-migrate', nxs_getframeworkurl() . '/js/migrate/jquery-migrate.js', array( 'jquery' ), nxs_getthemeversion(), TRUE );
	
  do_action('nxs_action_after_enqueue_baseframeworkstyles');
}
add_action('wp_enqueue_scripts', 'nxs_framework_theme_styles');
add_action('admin_enqueue_scripts', 'nxs_framework_theme_styles');

function nxs_clearunwantedscripts()
{
	// if we are in the frontend ...
	if (!is_admin())
	{
		// the theme could break if pointing to an incompatible version
		// therefore we remove jquery scripts added by third party plugins, such as NGG
  	//wp_deregister_script('jquery');
  	
  	
  	// 25 aug 2014; removed; woocommerce adds various scripts that are dependent upon
  	// jquery, and we ignore those too when using the approach below...
  	function nxs_modify_scripts() 
  	{
  		wp_deregister_script('jquery');
			wp_deregister_script('jquery-ui');
			$dependencies = false;
      wp_register_script('jquery', "//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js", $dependencies);
      wp_enqueue_script('jquery');
      
      wp_enqueue_script('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js', array('jquery'), '1.11.1');
		}
		add_action('wp_print_scripts', 'nxs_modify_scripts', 100);
		add_action('wp_head','nxs_setjQ_nxs');
  }
  else
  {
  	add_action('admin_head','nxs_setjQ_nxs');
  }
}
add_action('init', 'nxs_clearunwantedscripts');

function nxs_frontendframework_nxs_render_frontendeditor()
{
	require_once("nxsmenu.php");
	require_once("frontendediting.php");
	/*
	if ($_REQUEST["huh"] == "007")
	{
		echo "JA!";
		die();
	}
	*/
}
add_action('nxs_render_frontendeditor', 'nxs_frontendframework_nxs_render_frontendeditor');
