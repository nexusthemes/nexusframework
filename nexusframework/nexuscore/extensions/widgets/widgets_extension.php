<?php

/* LAZYLOAD WIDGET
---------------------------------------------------------------------------------------------------- */
function nxs_ext_lazyload_widget($widget)
{
	$action = "nxs_ext_inject_widget_" . $widget;
	$ishandledbyplugin = has_action($action);
	if ($ishandledbyplugin) {
		// it appears this widget was already handled by a plugin,
		// we will assume the plugin will override the widget of the framework
		// in this case we won't inject the widget from the framework
	} else {
		add_action($action, "nxs_ext_inject_widget");
	}
}

function nxs_ext_inject_widget($widget)
{
	$filetobeincluded = NXS_FRAMEWORKPATH . '/nexuscore/widgets/' . $widget . '/widget_' . $widget . '.php';
	if (!is_readable($filetobeincluded))
	{
		nxs_webmethod_return_nack("unable to inject widget $widget; File does not exist, or is not readable; $filetobeincluded");
	}
	require_once($filetobeincluded);
}

/* LAZYLOAD THEME WIDGET
---------------------------------------------------------------------------------------------------- */
function nxs_ext_lazyload_theme_widget($widget)
{
	$action = "nxs_ext_inject_widget_" . $widget;
	$ishandledbyplugin = has_action($action);
	if ($ishandledbyplugin) {
		// it appears this widget was already handled by a plugin,
		// we will assume the plugin will override the widget of the framework
		// in this case we won't inject the widget from the framework
	} else {
		add_action($action, "nxs_ext_inject_theme_widget");
	}
}

function nxs_ext_inject_theme_widget($widget)
{
	$filetobeincluded = NXS_THEMEPATH . '/widgets/' . $widget . '/widget_' . $widget . '.php';
	require_once($filetobeincluded);
}

/* EXISTS / REQUIRE WIDGETS
---------------------------------------------------------------------------------------------------- */
function nxs_widgetexists($widget)
{
	$action = "nxs_ext_inject_widget_" . $widget;
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

function nxs_requirewidget($widget)
{
	if (!(defined('nxs_widgets_loaded')))
	{
		nxs_webmethod_return_nack("nxs_requirewidget invoked before nxs_widgets_loaded");		
	}
	
	$result = array();

	// loads widget extensions in memory
	$action = "nxs_ext_inject_widget_" . $widget;
	if (!has_action($action))
	{
		// we gaan wel door, iemand kan per ongeluk of met opzet bijv. een plugin hebben uitgeschakeld		
		if (nxs_has_adminpermissions())
		{
			echo "Warning; looks like widget '" . $widget . "' is missing (maybe you deactivated a required plugin?) [nxs_requirewidget]; no action $action";
			// nxs_dumpstacktrace();
		}
		else
		{
			echo "<!-- Warning; looks like widget '" . $widget . "' is missing (maybe you deactivated a required plugin?) [nxs_requirewidget] -->";
		}
		
		$result["result"] = "NACK";
	}
	else
	{
		do_action($action, $widget);
		
		$result["result"] = "OK";
	}
	
	return $result;
}

/* ENQUEUE WIDGETS
---------------------------------------------------------------------------------------------------- */
add_action("nxs_getwidgets", "nxs_getwidgets_functions_AF", 10, 2);	// default prio 10, 2 parameters (result, args)
function nxs_getwidgets_functions_AF($result, $args)
{
	$nxsposttype = $args["nxsposttype"];
	$pagetemplate = $args["pagetemplate"];
	
	if ($nxsposttype == "") {
		nxs_webmethod_return_nack("nxsposttype not set");
	}
	
	// BUSINESS RULES WIDGETS
	
	if ($nxsposttype == "busrulesset") {
		$result[] = array("widgetid" => "busrulepostid");
		$result[] = array("widgetid" => "busrulecategory");
		$result[] = array("widgetid" => "busrulepostauthor");
		$result[] = array("widgetid" => "busrulearchivetype");
		$result[] = array("widgetid" => "busrulecatchall");
		$result[] = array("widgetid" => "busrulehome");
		$result[] = array("widgetid" => "busrule404");
		$result[] = array("widgetid" => "busrulearchivecat");
		$result[] = array("widgetid" => "busrulearchive");
		$result[] = array("widgetid" => "busrulesearch");
		$result[] = array("widgetid" => "busrulemaintenance");
		$result[] = array("widgetid" => "busruleposttype");
		
		// WOOCOMMERCE
		 
		global $woocommerce;
		if (isset($woocommerce))
		{
			$result[] = array("widgetid" => "woobusrulewoopage");
			$result[] = array("widgetid" => "woobusruleproduct");
			$result[] = array("widgetid" => "woobusrulecategory");
			$result[] = array("widgetid" => "woobusrulearchiveprodcat");
		}		
	}
	
	if ($nxsposttype == "genericlist") {
		$nxssubposttype = $args["nxssubposttype"];
		if ($nxssubposttype == "stack") {
			$args = array();
			$args["nxsposttype"] = "post";
			$args["pagetemplate"] = "blogentry";
			// return the widgets for regular post / blogentry
			return nxs_getwidgets_functions_AF($result, $args);
		}
	}
	
	if ($nxsposttype == "subheader") {
		$result[] = array("widgetid" => "wordpresstitle");
	}
	
	if 
	(
		$nxsposttype == "subheader" ||
		$nxsposttype == "subfooter" ||
		$nxsposttype == "pagelet"
	)
	{
		$result[] = array("widgetid" => "comments");
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
		$result[] = array("widgetid" => "text");
		$result[] = array("widgetid" => "image");
		$result[] = array("widgetid" => "blog");
		
		// Video
		$result[] = array("widgetid" => "youtube");
		$result[] = array("widgetid" => "vimeo");		
		
		// Social
		$result[] = array("widgetid" => "fblikebox");
		$result[] = array("widgetid" => "social");
		$result[] = array("widgetid" => "socialsharing");
		$result[] = array("widgetid" => "twittertweets");
		
		// Google
		$result[] = array("widgetid" => "googledoc");
		$result[] = array("widgetid" => "googlemap");
		
		// Forms
		$result[] = array("widgetid" => "contactbox");
		$result[] = array("widgetid" => "formbox");
		
		// Testimonials
		$result[] = array("widgetid" => "bio");
		$result[] = array("widgetid" => "quote");
		
		// Reference
		$result[] = array("widgetid" => "signpost");
		$result[] = array("widgetid" => "tumbler");
		$result[] = array("widgetid" => "radial");
		$result[] = array("widgetid" => "target");
		
		// Miscellaneous
		$result[] = array("widgetid" => "menucontainer");
		$result[] = array("widgetid" => "logo");
		$result[] = array("widgetid" => "callout");
		$result[] = array("widgetid" => "csv");
		$result[] = array("widgetid" => "search");
		$result[] = array("widgetid" => "eventsbox");
		$result[] = array("widgetid" => "carousel");
		$result[] = array("widgetid" => "banner");
		
		// Never
		$result[] = array("widgetid" => "wordpresssidebar");
		$result[] = array("widgetid" => "categories");
		$result[] = array("widgetid" => "archive");
		$result[] = array("widgetid" => "htmlcustom");
		$result[] = array("widgetid" => "squeezebox");
		$result[] = array("widgetid" => "googlebusinessphoto");		
		$result[] = array("widgetid" => "rssfeed");
		
		
		
		
		// $result[] = array("widgetid" => "wpmenu");
		// $result[] = array("widgetid" => "fbcomments");
		// $result[] = array("widgetid" => "template2");
		// $result[] = array("widgetid" => "stack");
		// $result[] = array("widgetid" => "searchresults");	// deprecated in favor of archive widget
		// $result[] = array("widgetid" => "contact"); 			// deprecated in favor of contact box widget
		
		// WOOCOMMERCE
		global $woocommerce;
		if (isset($woocommerce))
		{
			$result[] = array("widgetid" => "wooproductdetail");
			$result[] = array("widgetid" => "woomessages");
			$result[] = array("widgetid" => "wooprodlist");
			$result[] = array("widgetid" => "woocheckout");
			$result[] = array("widgetid" => "woothankyou");
			$result[] = array("widgetid" => "woocart");
			$result[] = array("widgetid" => "wooaddtocart");
			$result[] = array("widgetid" => "woogotocart");
			$result[] = array("widgetid" => "wooproductreference");
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
		$result[] = array("widgetid" => "gallerybox");
		$result[] = array("widgetid" => "definitionlistbox");
		$result[] = array("widgetid" => "sliderbox");
	} 
	
	/* MENU POSTTYPE
	---------------------------------------------------------------------------------------------------- */
	if ($nxsposttype == "menu")
	{
		$result[] = array("widgetid" => "menuitemcustom");
	}
	
	/* GENERIC LISTS POSTTYPE
	---------------------------------------------------------------------------------------------------- */
	if ($nxsposttype == "genericlist") {
		$nxssubposttype = $args["nxssubposttype"];
		
		// GALLERY
		if ($nxssubposttype == "gallery") {	
			$result[] = array("widgetid" => "galleryitem");
		}
		
		// SLIDER
		if ($nxssubposttype == "sliderbox") {
			$result[] = array("widgetid" => "slide");
		}
		
		// SUPERSIZED SLIDER
		if ($nxssubposttype == "pageslider") {
			$result[] = array("widgetid" => "slide");		
		}
		
		// GOOGLE BUSINESS PHOTOS
		if ($nxssubposttype == "googlebusphotoslides"){
			$result[] = array("widgetid" => "googlebusphotoitem");
		}
		
		// CONTACT FORM
		if ($nxssubposttype == "contact"){
			$result[] = array("widgetid" => "contactitemtext");
			$result[] = array("widgetid" => "contactitemdate");
			$result[] = array("widgetid" => "contactitemdatetime");
			$result[] = array("widgetid" => "contactitemselect");
			// $result[] = array("widgetid" => "contactitemhidden");
			// $result[] = array("widgetid" => "contactitemattachment");
		}
		
		// FORM
		if ($nxssubposttype == "form") {
			$result[] = array("widgetid" => "contactitemtext");
			$result[] = array("widgetid" => "contactitemdate");
			$result[] = array("widgetid" => "contactitemselect");
			$result[] = array("widgetid" => "contactitemdatetime");
			// $result[] = array("widgetid" => "contactitemhidden");
			// $result[] = array("widgetid" => "contactitemattachment");
		}
		
		// DEFINITION LIST
		if ($nxssubposttype == "definitionlist") {
			$result[] = array("widgetid" => "definitionlistitemtext");
		}
		
		// ---
		// Carousel
		if ($nxssubposttype == "carousel") {
			$result[] = array("widgetid" => "carouselitem");
		} 
		
		// Banner
		if ($nxssubposttype == "banner") {
			$result[] = array("widgetid" => "banneritem");
		}
	}	
	
	/* PAGETEMPLATES 
	---------------------------------------------------------------------------------------------------- */
	
	// EVENTS
	if ($pagetemplate == "eventsbox") {
		$result[] = array("widgetid" => "eventsboxitem");
	}
	
	// PAGEDECORATOR
	if ($pagetemplate == "pagedecorator") {
		$result[] = array("widgetid" => "pageslider");
		$result[] = array("widgetid" => "pagebackground");
		$result[] = array("widgetid" => "pagepopup");
	}
	
	/* CAPABILITIES WIDGET FILTER
	---------------------------------------------------------------------------------------------------- */
	if (nxs_cap_hasdesigncapabilities()) {
		// all are allowed
	} else {
		$subsetresult = array();
		
		$allowedwidgetids = array(
			"contactitemtext", 
			"contactitemdate", 
			"contactitemdatetime",
			"contactitemselect", 
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
			$widgetid = $currentitem["widgetid"];
			if (in_array($widgetid, $allowedwidgetids)) {
				$subsetresult[] = $currentitem;
			}
		}
		$result = $subsetresult;
	}
	
	return $result;
}

function nxs_widgets_registerhooksforpagewidget($widget, $args)
{
	$functionnametoinvoke = 'nxs_widgets_' . $widget . '_registerhooksforpagewidget';
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

/* LAZYLOADING WIDGETS
---------------------------------------------------------------------------------------------------- */
function nxs_lazyload_widgets()
{
	if (defined('nxs_widgets_loaded'))
	{
		return;
	}

	define('nxs_widgets_loaded', true);
	
	do_action("nxs_lazyload_widgets");
	// lazy load widgets. Note, if plugins load a widget with the same name, that widget will load first, ignoring this one same for widgets loaded by themes
	
	// WIDGETS
	nxs_ext_lazyload_widget("generic");
	nxs_ext_lazyload_widget("undefined");
	nxs_ext_lazyload_widget("comments");
	nxs_ext_lazyload_widget("menucontainer");
	nxs_ext_lazyload_widget("wordpresssidebar");
	nxs_ext_lazyload_widget("menuitemarticle");
	nxs_ext_lazyload_widget("menuitemcustom");
	nxs_ext_lazyload_widget("menuitemcategory");
	nxs_ext_lazyload_widget("socialsharing");
	nxs_ext_lazyload_widget("categories");
	nxs_ext_lazyload_widget("htmlcustom");
	nxs_ext_lazyload_widget("googlemap");
	nxs_ext_lazyload_widget("slide");
	nxs_ext_lazyload_widget("sliderbox");
	nxs_ext_lazyload_widget("youtube");
	nxs_ext_lazyload_widget("vimeo");
	nxs_ext_lazyload_widget("twittertweets");
	nxs_ext_lazyload_widget("gallerybox");
	nxs_ext_lazyload_widget("galleryitem");
	nxs_ext_lazyload_widget("definitionlistbox");
	nxs_ext_lazyload_widget("definitionlistitemtext");
	nxs_ext_lazyload_widget("contactbox");
	nxs_ext_lazyload_widget("formbox");
	nxs_ext_lazyload_widget("contactitemtext");
	nxs_ext_lazyload_widget("contactitemdate");
	nxs_ext_lazyload_widget("contactitemdatetime");	
	nxs_ext_lazyload_widget("contactitemselect");
	nxs_ext_lazyload_widget("contactitemhidden");
	nxs_ext_lazyload_widget("contactitemattachment");
	nxs_ext_lazyload_widget("blog");
	nxs_ext_lazyload_widget("archive");
	nxs_ext_lazyload_widget("logo");
	nxs_ext_lazyload_widget("signpost");
	nxs_ext_lazyload_widget("social");
	nxs_ext_lazyload_widget("callout");
	nxs_ext_lazyload_widget("bio");
	nxs_ext_lazyload_widget("tumbler");
	nxs_ext_lazyload_widget("text");
	nxs_ext_lazyload_widget("fblikebox");
	nxs_ext_lazyload_widget("googledoc");
	nxs_ext_lazyload_widget("rssfeed");
	nxs_ext_lazyload_widget("template2");
	nxs_ext_lazyload_widget("image");
	nxs_ext_lazyload_widget("search");
	nxs_ext_lazyload_widget("contact");
	nxs_ext_lazyload_widget("stack");
	nxs_ext_lazyload_widget("wordpresstitle");
	nxs_ext_lazyload_widget("quote");
	nxs_ext_lazyload_widget("radial");
	nxs_ext_lazyload_widget("squeezebox");
	nxs_ext_lazyload_widget("googlebusinessphoto");
	nxs_ext_lazyload_widget("googlebusphotoitem");
	nxs_ext_lazyload_widget("eventsbox");
	nxs_ext_lazyload_widget("eventsboxitem");
	nxs_ext_lazyload_widget("csv");
	nxs_ext_lazyload_widget("wpmenu");
	nxs_ext_lazyload_widget("target");
	nxs_ext_lazyload_widget("carousel");
	nxs_ext_lazyload_widget("carouselitem");
	nxs_ext_lazyload_widget("banner");
	nxs_ext_lazyload_widget("banneritem");

	// PAGEDECORATORS
	nxs_ext_lazyload_widget("pageslider");
	nxs_ext_lazyload_widget("pagebackground");
	nxs_ext_lazyload_widget("pagepopup");
	
	// BUSINESS RULES
	nxs_ext_lazyload_widget("busrulecatchall");	
	nxs_ext_lazyload_widget("busrulepostid");	
	nxs_ext_lazyload_widget("busrulecategory");	
	nxs_ext_lazyload_widget("busrulepostauthor");	
	nxs_ext_lazyload_widget("busrulearchivetype");	
	nxs_ext_lazyload_widget("busrulehome");
	nxs_ext_lazyload_widget("busrule404");
	nxs_ext_lazyload_widget("busrulearchivecat");	
	nxs_ext_lazyload_widget("busrulearchive");	
	nxs_ext_lazyload_widget("busrulesearch");	
	nxs_ext_lazyload_widget("busrulemaintenance");	
	nxs_ext_lazyload_widget("busruleposttype");	
	
	// WOOCOMMERCE
	global $woocommerce;
	if (isset($woocommerce))
	{
		// widgets
		nxs_ext_lazyload_widget("wooproductdetail");
		nxs_ext_lazyload_widget("woomessages");
		nxs_ext_lazyload_widget("woocheckout");
		nxs_ext_lazyload_widget("woothankyou");
		nxs_ext_lazyload_widget("woocart");
		nxs_ext_lazyload_widget("wooprodlist");
		nxs_ext_lazyload_widget("wooaddtocart");
		nxs_ext_lazyload_widget("woogotocart");
		nxs_ext_lazyload_widget("wooproductreference");
		
		// business rules
		nxs_ext_lazyload_widget("woobusrulewoopage");
		nxs_ext_lazyload_widget("woobusruleproduct");
		nxs_ext_lazyload_widget("woobusrulecategory");
		nxs_ext_lazyload_widget("woobusrulearchiveprodcat");
	}
	
	// DEPRECATED
	
	// nxs_ext_lazyload_widget("fbcomments");
	// nxs_ext_lazyload_widget("searchresults");	// deprecated in favor of archive widget
}

// if framework is loaded by the plugins, we load the widgets after all plugins are available
// lazyloading the widgets cannot be executed directly, as plugins might not be loaded yet 
// (for example woocommerce)
add_action("plugins_loaded", "nxs_lazyload_widgets");
// if framework is loaded by the theme, we load the widgets after the theme is setup
add_action("after_setup_theme", "nxs_lazyload_widgets");

?>
