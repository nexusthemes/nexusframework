<?php

/* LAZYLOAD EFFECT
---------------------------------------------------------------------------------------------------- */
function nxs_ext_lazyload_effect($effect)
{
	$action = "nxs_ext_inject_effect_" . $effect;
	$ishandledbyplugin = has_action($action);
	if ($ishandledbyplugin) {
		// it appears this effect was already handled by a plugin,
		// we will assume the plugin will override the effect of the framework
		// in this case we won't inject the effect from the framework
	} else {
		add_action($action, "nxs_ext_inject_effect");
	}
}

function nxs_ext_inject_effect($effect)
{
	$filetobeincluded = NXS_FRAMEWORKPATH . '/nexuscore/effects/' . $effect . '/effect_' . $effect . '.php';
	if (!is_readable($filetobeincluded))
	{
		nxs_webmethod_return_nack("unable to inject effect $effect; File does not exist, or is not readable; $filetobeincluded");
	}
	require_once($filetobeincluded);
}

/* LAZYLOAD THEME EFFECT
---------------------------------------------------------------------------------------------------- */
function nxs_ext_lazyload_theme_effect($effect)
{
	$action = "nxs_ext_inject_effect_" . $effect;
	$ishandledbyplugin = has_action($action);
	if ($ishandledbyplugin) {
		// it appears this effect was already handled by a plugin,
		// we will assume the plugin will override the effect of the framework
		// in this case we won't inject the effect from the framework
	} else {
		add_action($action, "nxs_ext_inject_theme_effect");
	}
}

function nxs_ext_inject_theme_effect($effect)
{
	$filetobeincluded = NXS_THEMEPATH . '/effects/' . $effect . '/effect_' . $effect . '.php';
	require_once($filetobeincluded);
}

/* EXISTS / REQUIRE EFFECTS
---------------------------------------------------------------------------------------------------- */
function nxs_effectexists($effect)
{
	$action = "nxs_ext_inject_effect_" . $effect;
	if (!has_action($action))
	{
		$result = false;
	}
	else
	{
		$result = true;
	}
	
	return $result;
}

function nxs_requireeffect($effect)
{
	if (!(defined('nxs_effects_loaded')))
	{
		nxs_webmethod_return_nack("nxs_requireeffect invoked before nxs_effects_loaded");		
	}
	
	$result = array();

	// loads effect extensions in memory
	$action = "nxs_ext_inject_effect_" . $effect;
	if (!has_action($action))
	{
		// we gaan wel door, iemand kan per ongeluk of met opzet bijv. een plugin hebben uitgeschakeld		
		if (nxs_has_adminpermissions())
		{
			echo "Warning; looks like effect '" . $effect . "' is missing (maybe you deactivated a required plugin?) [nxs_requireeffect]; no action $action";
			// nxs_dumpstacktrace();
		}
		else
		{
			echo "<!-- Warning; looks like effect '" . $effect . "' is missing (maybe you deactivated a required plugin?) [nxs_requireeffect] -->";
		}
		
		$result["result"] = "NACK";
	}
	else
	{
		do_action($action, $effect);
		
		$result["result"] = "OK";
	}
	
	return $result;
}

function nxs_enableconceptualeffects()
{
	$enableconceptualeffects = false;
	if (nxs_hassitemeta())
	{
		$sitemeta = nxs_getsitemeta();
		if ($sitemeta["effectsmanagement_enableconceptual"] == "show")
		{
			$enableconceptualeffects = true;
		}
	}
	return $enableconceptualeffects;
}

/* ENQUEUE EFFECTS
---------------------------------------------------------------------------------------------------- */
add_action("nxs_geteffects", "nxs_geteffects_functions_AF", 10, 2);	// default prio 10, 2 parameters (result, args)
function nxs_geteffects_functions_AF($result, $args)
{
	$nxsposttype = $args["nxsposttype"];
	$pagetemplate = $args["pagetemplate"];
	
	if ($nxsposttype == "") {
		nxs_webmethod_return_nack("nxsposttype not set");
	}
	
	$enableconceptualeffects = nxs_enableconceptualeffects();
	
	// BUSINESS RULES EFFECTS
	
	if ($nxsposttype == "busrulesset") {
		$result[] = array("effectid" => "busrulepostid");
		$result[] = array("effectid" => "busrulecategory");
		$result[] = array("effectid" => "busrulepostauthor");
		$result[] = array("effectid" => "busrulearchivetype");
		$result[] = array("effectid" => "busrulecatchall");
		$result[] = array("effectid" => "busrulehome");
		$result[] = array("effectid" => "busrule404");
		$result[] = array("effectid" => "busrulearchivecat");
		$result[] = array("effectid" => "busrulearchive");
		$result[] = array("effectid" => "busrulesearch");
		$result[] = array("effectid" => "busrulemaintenance");
		$result[] = array("effectid" => "busruleposttype");
		$result[] = array("effectid" => "busrulehaspostcontent");
		
		// WOOCOMMERCE
		 
		global $woocommerce;
		if (isset($woocommerce))
		{
			$result[] = array("effectid" => "woobusrulewoopage");
			$result[] = array("effectid" => "woobusruleproduct");
			$result[] = array("effectid" => "woobusrulecategory");
			$result[] = array("effectid" => "woobusrulearchiveprodcat");
		}		
	}
	
	if ($nxsposttype == "genericlist") {
		$nxssubposttype = $args["nxssubposttype"];
		if ($nxssubposttype == "stack") {
			$args = array();
			$args["nxsposttype"] = "post";
			$args["pagetemplate"] = "blogentry";
			// return the effects for regular post / blogentry
			return nxs_geteffects_functions_AF($result, $args);
		}
	}
	
	if ($nxsposttype == "subheader") {
		$result[] = array("effectid" => "wordpresstitle");
	}
	
	if 
	(
		$nxsposttype == "subheader" ||
		$nxsposttype == "subfooter" ||
		$nxsposttype == "pagelet"
	)
	{
		$result[] = array("effectid" => "comments");
	}

	

	/* ALL POSTTYPES
	---------------------------------------------------------------------------------------------------- */
	if 
	(
		$nxsposttype == "post" || 
		$nxsposttype == "footer" || 
		$nxsposttype == "header" || 
		$nxsposttype == "subheader" ||
		$nxsposttype == "subfooter" ||
		$nxsposttype == "pagelet" ||
		$nxsposttype == "sidebar"
	)
	{
		

		// Default
		$result[] = array("effectid" => "text");
		$result[] = array("effectid" => "image");
		$result[] = array("effectid" => "blog");
		
		// Video
		$result[] = array("effectid" => "youtube");
		$result[] = array("effectid" => "vimeo");		
		
		// Social
		$result[] = array("effectid" => "fblikebox");
		$result[] = array("effectid" => "social");
		$result[] = array("effectid" => "socialsharing");
		$result[] = array("effectid" => "twittertweets");
		
		// Google
		$result[] = array("effectid" => "googledoc");
		$result[] = array("effectid" => "googlemap");
		
		// Forms
		$result[] = array("effectid" => "contactbox");
		$result[] = array("effectid" => "formbox");
		
		// Testimonials
		$result[] = array("effectid" => "bio");
		$result[] = array("effectid" => "quote");
		
		// Reference
		$result[] = array("effectid" => "signpost");
		$result[] = array("effectid" => "tumbler");
		$result[] = array("effectid" => "radial");
		$result[] = array("effectid" => "target");
		
		// Miscellaneous
		$result[] = array("effectid" => "menucontainer");
		$result[] = array("effectid" => "logo");
		$result[] = array("effectid" => "callout");
		$result[] = array("effectid" => "csv");
		$result[] = array("effectid" => "search");
		$result[] = array("effectid" => "eventsbox");
		$result[] = array("effectid" => "carousel");
		$result[] = array("effectid" => "banner");
		$result[] = array("effectid" => "flickr");
		
		// Never
		$result[] = array("effectid" => "wordpresssidebar");
		$result[] = array("effectid" => "categories");
		$result[] = array("effectid" => "archive");
		$result[] = array("effectid" => "htmlcustom");
		$result[] = array("effectid" => "squeezebox");
		$result[] = array("effectid" => "googlebusinessphoto");		
		$result[] = array("effectid" => "rssfeed");
		
		
		
		if ($enableconceptualeffects)
		{
			$result[] = array("effectid" => "wpmenu");
		}
		
		// $result[] = array("effectid" => "fbcomments");
		// $result[] = array("effectid" => "template2");
		// $result[] = array("effectid" => "stack");
		// $result[] = array("effectid" => "searchresults");	// deprecated in favor of archive effect
		// $result[] = array("effectid" => "contact"); 			// deprecated in favor of contact box effect
		
		// WOOCOMMERCE
		global $woocommerce;
		if (isset($woocommerce))
		{
			$result[] = array("effectid" => "wooproductdetail");
			$result[] = array("effectid" => "woomessages");
			$result[] = array("effectid" => "wooprodlist");
			$result[] = array("effectid" => "woocheckout");
			$result[] = array("effectid" => "woothankyou");
			$result[] = array("effectid" => "woocart");
			$result[] = array("effectid" => "wooaddtocart");
			$result[] = array("effectid" => "woogotocart");
			$result[] = array("effectid" => "wooproductreference");
		}
		
	}
	
	/* EXCLUDING SIDEBAR POSTTYPE
	---------------------------------------------------------------------------------------------------- */
	if 
	(
		$nxsposttype == "post" || 
		$nxsposttype == "footer" || 
		$nxsposttype == "header" || 
		$nxsposttype == "subheader" || 
		$nxsposttype == "subfooter" || 
		$nxsposttype == "pagelet"
	)	
	{		
		$result[] = array("effectid" => "gallerybox");
		$result[] = array("effectid" => "definitionlistbox");
		$result[] = array("effectid" => "sliderbox");
		
		
		if ($enableconceptualeffects)
		{
			$result[] = array("effectid" => "filmrollbox");
		}
	} 
	
	/* MENU POSTTYPE
	---------------------------------------------------------------------------------------------------- */
	if ($nxsposttype == "menu")
	{
		$result[] = array("effectid" => "menuitemarticle");
		$result[] = array("effectid" => "menuitemcustom");
		$result[] = array("effectid" => "menuitemcategory");
	}
	
	/* GENERIC LISTS POSTTYPE
	---------------------------------------------------------------------------------------------------- */
	if ($nxsposttype == "genericlist") {
		$nxssubposttype = $args["nxssubposttype"];
		
		// GALLERY
		if ($nxssubposttype == "gallery") {	
			$result[] = array("effectid" => "galleryitem");
		}
		
		// SLIDER
		if ($nxssubposttype == "sliderbox") {
			$result[] = array("effectid" => "slide");
		}
		
		if ($enableconceptualeffects)
		{
			// FILMROLL
			if ($nxssubposttype == "filmrollbox") {
				$result[] = array("effectid" => "slide");
			}
		}
		
		// SUPERSIZED SLIDER
		if ($nxssubposttype == "pageslider") {
			$result[] = array("effectid" => "slide");		
		}
		
		// GOOGLE BUSINESS PHOTOS
		if ($nxssubposttype == "googlebusphotoslides"){
			$result[] = array("effectid" => "googlebusphotoitem");
		}
		
		// CONTACT FORM
		if ($nxssubposttype == "contact"){
			$result[] = array("effectid" => "contactitemtext");
			$result[] = array("effectid" => "contactitemdate");
			$result[] = array("effectid" => "contactitemdatetime");
			$result[] = array("effectid" => "contactitemselect");
			$result[] = array("effectid" => "contactitemsecret");
			// $result[] = array("effectid" => "contactitemhidden");
			// $result[] = array("effectid" => "contactitemattachment");
		}
		
		// FORM
		if ($nxssubposttype == "form") 
		{
			$result[] = array("effectid" => "contactitemtext");
			$result[] = array("effectid" => "contactitemdate");
			$result[] = array("effectid" => "contactitemselect");
			$result[] = array("effectid" => "contactitemdatetime");
			$result[] = array("effectid" => "contactitemsecret");
			// $result[] = array("effectid" => "contactitemhidden");
			// $result[] = array("effectid" => "contactitemattachment");
		}
		
		// DEFINITION LIST
		if ($nxssubposttype == "definitionlist") {
			$result[] = array("effectid" => "definitionlistitemtext");
		}
		
		// ---
		// Carousel
		if ($nxssubposttype == "carousel") {
			$result[] = array("effectid" => "carouselitem");
		} 
		
		// Banner
		if ($nxssubposttype == "banner") {
			$result[] = array("effectid" => "banneritem");
		}
	}	
	
	if ($nxsposttype == "post") 
	{
		$result[] = array("effectid" => "wordpresstitle");
	}

	
	/* PAGETEMPLATES 
	---------------------------------------------------------------------------------------------------- */
	
	// EVENTS
	if ($pagetemplate == "eventsbox") {
		$result[] = array("effectid" => "eventsboxitem");
	}
	
	// PAGEDECORATOR
	if ($pagetemplate == "pagedecorator") {
		$result[] = array("effectid" => "pageslider");
		$result[] = array("effectid" => "pagebackground");
		$result[] = array("effectid" => "pagepopup");
	}
	
	
	
	/* CAPABILITIES EFFECT FILTER
	---------------------------------------------------------------------------------------------------- */
	if (nxs_cap_hasdesigncapabilities()) {
		// all are allowed
	} else {
		$subsetresult = array();
		
		$allowedeffectids = array(
			"contactitemtext", 
			"contactitemdate", 
			"contactitemdatetime",
			"contactitemselect", 
			"contactitemsecret", 
			"definitionlistitemtext", 
			"eventsboxitem", 
			"gallerybox", 
			"galleryitem", 
			"image", 
			"menuitemarticle", 
			"menuitemcustom", 
			"menuitemcategory", 
			"slide", 
			"text", 
			"vimeo", 
			"youtube"
			);
		
		foreach ($result as $currentitem) {
			$effectid = $currentitem["effectid"];
			if (in_array($effectid, $allowedeffectids)) {
				$subsetresult[] = $currentitem;
			}
		}
		$result = $subsetresult;
	}
	
	return $result;
}

function nxs_effects_registerhooksforpageeffect($effect, $args)
{
	$functionnametoinvoke = 'nxs_effects_' . $effect . '_registerhooksforpageeffect';
	//
	// invokefunction
	//
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("function not found; " . $functionnametoinvoke);	
	}
}

/* LAZYLOADING EFFECTS
---------------------------------------------------------------------------------------------------- */
function nxs_lazyload_effects()
{
	if (defined('nxs_effects_loaded'))
	{
		return;
	}

	$enableconceptualeffects = nxs_enableconceptualeffects();

	define('nxs_effects_loaded', true);
	
	do_action("nxs_lazyload_effects");
	// lazy load effects. Note, if plugins load a effect with the same name, that effect will load first, ignoring this one same for effects loaded by themes
	
	// EFFECTS
	nxs_ext_lazyload_effect("generic");
	nxs_ext_lazyload_effect("undefined");
	nxs_ext_lazyload_effect("comments");
	nxs_ext_lazyload_effect("menucontainer");
	nxs_ext_lazyload_effect("wordpresssidebar");
	nxs_ext_lazyload_effect("menuitemgeneric");
	nxs_ext_lazyload_effect("menuitemarticle");
	nxs_ext_lazyload_effect("menuitemcustom");
	nxs_ext_lazyload_effect("menuitemcategory");
	nxs_ext_lazyload_effect("socialsharing");
	nxs_ext_lazyload_effect("categories");
	nxs_ext_lazyload_effect("htmlcustom");
	nxs_ext_lazyload_effect("googlemap");
	nxs_ext_lazyload_effect("slide");
	nxs_ext_lazyload_effect("sliderbox");
	
	if ($enableconceptualeffects)
	{
		nxs_ext_lazyload_effect("filmrollbox");
	}
	
	nxs_ext_lazyload_effect("youtube");
	nxs_ext_lazyload_effect("vimeo");
	nxs_ext_lazyload_effect("twittertweets");
	nxs_ext_lazyload_effect("gallerybox");
	nxs_ext_lazyload_effect("galleryitem");
	nxs_ext_lazyload_effect("definitionlistbox");
	nxs_ext_lazyload_effect("definitionlistitemtext");
	nxs_ext_lazyload_effect("contactbox");
	nxs_ext_lazyload_effect("formbox");
	nxs_ext_lazyload_effect("contactitemtext");
	nxs_ext_lazyload_effect("contactitemsecret");
	nxs_ext_lazyload_effect("contactitemdate");
	nxs_ext_lazyload_effect("contactitemdatetime");	
	nxs_ext_lazyload_effect("contactitemselect");
	nxs_ext_lazyload_effect("contactitemhidden");
	nxs_ext_lazyload_effect("contactitemattachment");
	nxs_ext_lazyload_effect("blog");
	nxs_ext_lazyload_effect("archive");
	nxs_ext_lazyload_effect("logo");
	nxs_ext_lazyload_effect("signpost");
	nxs_ext_lazyload_effect("social");
	nxs_ext_lazyload_effect("callout");
	nxs_ext_lazyload_effect("bio");
	nxs_ext_lazyload_effect("tumbler");
	nxs_ext_lazyload_effect("text");
	nxs_ext_lazyload_effect("fblikebox");
	nxs_ext_lazyload_effect("googledoc");
	nxs_ext_lazyload_effect("rssfeed");
	nxs_ext_lazyload_effect("template2");
	nxs_ext_lazyload_effect("image");
	nxs_ext_lazyload_effect("search");
	nxs_ext_lazyload_effect("contact");
	nxs_ext_lazyload_effect("stack");
	nxs_ext_lazyload_effect("wordpresstitle");
	nxs_ext_lazyload_effect("quote");
	nxs_ext_lazyload_effect("radial");
	nxs_ext_lazyload_effect("squeezebox");
	nxs_ext_lazyload_effect("googlebusinessphoto");
	nxs_ext_lazyload_effect("googlebusphotoitem");
	nxs_ext_lazyload_effect("eventsbox");
	nxs_ext_lazyload_effect("eventsboxitem");
	nxs_ext_lazyload_effect("csv");
	
	if ($enableconceptualeffects)
	{
		nxs_ext_lazyload_effect("wpmenu");
	}
	
	nxs_ext_lazyload_effect("target");
	nxs_ext_lazyload_effect("flickr");
	nxs_ext_lazyload_effect("carousel");
	nxs_ext_lazyload_effect("carouselitem");
	nxs_ext_lazyload_effect("banner");
	nxs_ext_lazyload_effect("banneritem");

	// PAGEDECORATORS
	nxs_ext_lazyload_effect("pageslider");
	nxs_ext_lazyload_effect("pagebackground");
	nxs_ext_lazyload_effect("pagepopup");
	
	// BUSINESS RULES
	nxs_ext_lazyload_effect("busrulecatchall");	
	nxs_ext_lazyload_effect("busrulepostid");	
	nxs_ext_lazyload_effect("busrulecategory");	
	nxs_ext_lazyload_effect("busrulepostauthor");	
	nxs_ext_lazyload_effect("busrulearchivetype");	
	nxs_ext_lazyload_effect("busrulehome");
	nxs_ext_lazyload_effect("busrule404");
	nxs_ext_lazyload_effect("busrulearchivecat");	
	nxs_ext_lazyload_effect("busrulearchive");	
	nxs_ext_lazyload_effect("busrulesearch");	
	nxs_ext_lazyload_effect("busrulemaintenance");	
	nxs_ext_lazyload_effect("busruleposttype");
	nxs_ext_lazyload_effect("busrulehaspostcontent");
	
	// WOOCOMMERCE
	global $woocommerce;
	if (isset($woocommerce))
	{
		// effects
		nxs_ext_lazyload_effect("wooproductdetail");
		nxs_ext_lazyload_effect("woomessages");
		nxs_ext_lazyload_effect("woocheckout");
		nxs_ext_lazyload_effect("woothankyou");
		nxs_ext_lazyload_effect("woocart");
		nxs_ext_lazyload_effect("wooprodlist");
		nxs_ext_lazyload_effect("wooaddtocart");
		nxs_ext_lazyload_effect("woogotocart");
		nxs_ext_lazyload_effect("wooproductreference");
		
		// business rules
		nxs_ext_lazyload_effect("woobusrulewoopage");
		nxs_ext_lazyload_effect("woobusruleproduct");
		nxs_ext_lazyload_effect("woobusrulecategory");
		nxs_ext_lazyload_effect("woobusrulearchiveprodcat");
	}
	
	// DEPRECATED
	
	// nxs_ext_lazyload_effect("fbcomments");
	// nxs_ext_lazyload_effect("searchresults");	// deprecated in favor of archive effect
}

// if framework is loaded by the plugins, we load the effects after all plugins are available
// lazyloading the effects cannot be executed directly, as plugins might not be loaded yet 
// (for example woocommerce)
add_action("plugins_loaded", "nxs_lazyload_effects");
// if framework is loaded by the theme, we load the effects after the theme is setup
add_action("after_setup_theme", "nxs_lazyload_effects");

/* *************** */

// USAGE: nxs_lazyload_plugin_effect(__FILE__, "nameofeffect");

function nxs_lazyload_plugin_effect($file, $effect)
{
	// store file loc in lookup (mem)
	global $nxs_gl_effect_file;
	if ($nxs_gl_effect_file == null)
	{
		$nxs_gl_effect_file = array();
	}
	$nxs_gl_effect_file[$effect] = $file;
	
	$action = "nxs_ext_inject_effect_" . $effect;
	add_action($action, "nxs_inject_plugin_effect");
}

function nxs_inject_plugin_effect($effect)
{
	global $nxs_gl_effect_file;
	$file = $nxs_gl_effect_file[$effect];
	$path = plugin_dir_path($file);
	$filetobeincluded = $path . '/effects/' . $effect . '/effect_' . $effect . '.php';
	require_once($filetobeincluded);
}

/* *************** */

function nxs_getobsoleteeffectids()
{
	$result = array();
	
	$result[] = "contactbox";
	
	if (!nxs_enableconceptualeffects())
	{
		$result[] = "googlebusinessphoto";
		$result[] = "squeezebox";
	}

	return $result;
}

?>
