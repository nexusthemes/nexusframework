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
		if ($widget == "")
		{
			// absorb the error; 
		}
		else
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

function nxs_enableconceptualwidgets()
{
	$enableconceptualwidgets = false;
	if (nxs_hassitemeta())
	{
		$sitemeta = nxs_getsitemeta();
		if ($sitemeta["widgetsmanagement_enableconceptual"] == "show")
		{
			$enableconceptualwidgets = true;
		}
	}
	return $enableconceptualwidgets;
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
	
	$enableconceptualwidgets = nxs_enableconceptualwidgets();
	
	// BUSINESS RULES WIDGETS
	
	if ($nxsposttype == "busrulesset") 
	{
		$result[] = array("widgetid" => "busrulepostid", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busrulecategory", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busrulepostauthor", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busrulearchivetype", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busrulecatchall", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busrulehome", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busrule404", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busrulearchivecat", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busrulearchive", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busrulesearch", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busrulemaintenance", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busruleposttype", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busrulehaspostcontent", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busruleauthentication", "tags" => array("nexus"));		
		
		if ($enableconceptualwidgets)
		{
			// obsolete widget
			$result[] = array("widgetid" => "busrulesemanticlayout", "tags" => array("nexus"));
		}
		
		$result[] = array("widgetid" => "busruleurl", "tags" => array("nexus"));
		$result[] = array("widgetid" => "busruledeclarativecondition", "tags" => array("nexus"));
	}
	
	if ($nxsposttype == "subheader" || $nxsposttype == "header") {
		$result[] = array("widgetid" => "wordpresstitle", "tags" => array("nexus"));
	}
	
	if 
	(
		$nxsposttype == "subheader" ||
		$nxsposttype == "subfooter" ||
		$nxsposttype == "pagelet"
	)
	{
		$result[] = array("widgetid" => "comments", "tags" => array("nexus"));
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
		$result[] = array("widgetid" => "text", "tags" => array("nexus"));
		$result[] = array("widgetid" => "image", "tags" => array("nexus"));
		$result[] = array("widgetid" => "blog", "tags" => array("nexus"));
		
		// Video
		$result[] = array("widgetid" => "youtube", "tags" => array("nexus"));
		$result[] = array("widgetid" => "vimeo", "tags" => array("nexus"));		
		
		// Social
		$result[] = array("widgetid" => "fblikebox", "tags" => array("nexus"));
		$result[] = array("widgetid" => "social", "tags" => array("nexus"));
		$result[] = array("widgetid" => "socialsharing", "tags" => array("nexus"));
		$result[] = array("widgetid" => "twittertweets", "tags" => array("nexus"));
		
		// Google
		$result[] = array("widgetid" => "googledoc", "tags" => array("nexus"));
		$result[] = array("widgetid" => "googlemap", "tags" => array("nexus"));
		
		// Forms
		$result[] = array("widgetid" => "contactbox", "tags" => array("nexus"));
		$result[] = array("widgetid" => "formbox", "tags" => array("nexus"));
		
		// Testimonials
		$result[] = array("widgetid" => "bio", "tags" => array("nexus"));
		$result[] = array("widgetid" => "quote", "tags" => array("nexus"));
		
		// Reference
		$result[] = array("widgetid" => "signpost", "tags" => array("nexus"));
		$result[] = array("widgetid" => "tumbler", "tags" => array("nexus"));
		$result[] = array("widgetid" => "radial", "tags" => array("nexus"));
		$result[] = array("widgetid" => "target", "tags" => array("nexus"));
		
		// Miscellaneous
		$result[] = array("widgetid" => "logo", "tags" => array("nexus"));
		$result[] = array("widgetid" => "callout", "tags" => array("nexus"));
		$result[] = array("widgetid" => "csv", "tags" => array("nexus"));
		$result[] = array("widgetid" => "section", "tags" => array("nexus"));
		$result[] = array("widgetid" => "search", "tags" => array("nexus"));
		$result[] = array("widgetid" => "eventsbox", "tags" => array("nexus"));
		
		$result[] = array("widgetid" => "banner", "tags" => array("nexus"));
		$result[] = array("widgetid" => "flickr", "tags" => array("nexus"));
		$result[] = array("widgetid" => "seo", "tags" => array("nexus"));
		$result[] = array("widgetid" => "lang", "tags" => array("nexus"));
		
		// Never
		$result[] = array("widgetid" => "wordpresssidebar", "tags" => array("nexus"));
		$result[] = array("widgetid" => "categories", "tags" => array("nexus"));
		$result[] = array("widgetid" => "archive", "tags" => array("nexus"));
		$result[] = array("widgetid" => "htmlcustom", "tags" => array("nexus"));
		$result[] = array("widgetid" => "rssfeed", "tags" => array("nexus"));	
		$result[] = array("widgetid" => "breadcrumb", "tags" => array("nexus"));
		$result[] = array("widgetid" => "wpmenu", "tags" => array("nexus"));
		
		if ($enableconceptualwidgets)
		{
			// menucontainer is obsolete as of jan 2017
			$result[] = array("widgetid" => "menucontainer", "tags" => array("nexus"));
      $result[] = array("widgetid" => "carousel", "tags" => array("nexus"));
		}
		
		// $result[] = array("widgetid" => "fbcomments");
		// $result[] = array("widgetid" => "searchresults");	// deprecated in favor of archive widget
		// $result[] = array("widgetid" => "contact"); 			// deprecated in favor of contact box widget
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
		$result[] = array("widgetid" => "gallerybox", "tags" => array("nexus"));
		$result[] = array("widgetid" => "definitionlistbox", "tags" => array("nexus"));
		$result[] = array("widgetid" => "sliderbox", "tags" => array("nexus"));
		$result[] = array("widgetid" => "vectorart", "tags" => array("nexus"));
	} 
	
	/* MENU POSTTYPE
	---------------------------------------------------------------------------------------------------- */
	if ($nxsposttype == "menu")
	{
		$result[] = array("widgetid" => "menuitemarticle", "tags" => array("nexus"));
		$result[] = array("widgetid" => "menuitemcustom", "tags" => array("nexus"));
		$result[] = array("widgetid" => "menuitemcategory", "tags" => array("nexus"));
		// $result[] = array("widgetid" => "menuitementities", "tags" => array("nexus"));
	}
	
	/* GENERIC LISTS POSTTYPE
	---------------------------------------------------------------------------------------------------- */
	if ($nxsposttype == "genericlist") {
		$nxssubposttype = $args["nxssubposttype"];
		
		// GALLERY
		if ($nxssubposttype == "gallery") {	
			$result[] = array("widgetid" => "galleryitem", "tags" => array("nexus"));
		}
		
		// SLIDER
		if ($nxssubposttype == "sliderbox") 
		{
			$result[] = array("widgetid" => "slide", "tags" => array("nexus"));
			$result[] = array("widgetid" => "slidesincat", "tags" => array("nexus"));
		}
		
		if ($enableconceptualwidgets)
		{
		}
		
		// SUPERSIZED SLIDER
		if ($nxssubposttype == "pageslider") {
			$result[] = array("widgetid" => "slide", "tags" => array("nexus"));		
		}
				
		// CONTACT FORM
		if ($nxssubposttype == "contact"){
			$result[] = array("widgetid" => "contactitemtext", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemdate", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemdatetime", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemselect", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemmultiselect", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemsecret", "tags" => array("nexus"));
			// $result[] = array("widgetid" => "contactitemhidden");
			// $result[] = array("widgetid" => "contactitemattachment");
			$result[] = array("widgetid" => "contactitemfileattachment", "tags" => array("nexus"));
		}
		
		// FORM
		if ($nxssubposttype == "form") 
		{
			$result[] = array("widgetid" => "contactitemtext", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemdate", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemselect", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemmultiselect", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemdatetime", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemsecret", "tags" => array("nexus"));
			$result[] = array("widgetid" => "formitemcaptcha", "tags" => array("nexus"));
			$result[] = array("widgetid" => "formitemcheckbox", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemreplyto", "tags" => array("nexus"));
			$result[] = array("widgetid" => "contactitemfileattachment", "tags" => array("nexus"));
			$result[] = array("widgetid" => "formitemhtml", "tags" => array("nexus"));
		}
		
		// DEFINITION LIST
		if ($nxssubposttype == "definitionlist") {
			$result[] = array("widgetid" => "definitionlistitemtext", "tags" => array("nexus"));
		}
		
		// ---
		// Carousel
		if ($nxssubposttype == "carousel") {
			$result[] = array("widgetid" => "carouselitem", "tags" => array("nexus"));
		} 
		
		// Banner
		if ($nxssubposttype == "banner") {
			$result[] = array("widgetid" => "banneritem", "tags" => array("nexus"));
		}
	}	
	
	if ($nxsposttype == "post") 
	{
		$result[] = array("widgetid" => "wordpresstitle", "tags" => array("nexus"));
	}

	
	/* PAGETEMPLATES 
	---------------------------------------------------------------------------------------------------- */
	
	// EVENTS
	if ($pagetemplate == "eventsbox") {
		$result[] = array("widgetid" => "eventsboxitem");
	}
	
	// PAGEDECORATOR
	if ($pagetemplate == "pagedecorator") {
		$result[] = array("widgetid" => "pageslider", "tags" => array("nexus"));
		$result[] = array("widgetid" => "pagebackground", "tags" => array("nexus"));
		$result[] = array("widgetid" => "pagepopup", "tags" => array("nexus"));
		$result[] = array("widgetid" => "pageslidetotop", "tags" => array("nexus"));
		$result[] = array("widgetid" => "pageinpagesectionmenu", "tags" => array("nexus"));
		$result[] = array("widgetid" => "pagefixedheader", "tags" => array("nexus"));

		if ( $enableconceptualwidgets )
		{
			
		}
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
			"contactitemmultiselect",
			"contactitemsecret",
			"contactitemreplyto",
			"formitemcaptcha",
			"formitemcheckbox",
			"definitionlistitemtext",
			"eventsboxitem", 
			"gallerybox", 
			"galleryitem", 
			"image", 
			"menuitemarticle", 
			// "menuitementities",
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

	$enableconceptualwidgets = nxs_enableconceptualwidgets();

	define('nxs_widgets_loaded', true);
	
	do_action("nxs_lazyload_widgets");
	// lazy load widgets. Note, if plugins load a widget with the same name, that widget will load first, ignoring this one same for widgets loaded by themes
	
	// WIDGETS
	nxs_ext_lazyload_widget("generic");
	nxs_ext_lazyload_widget("undefined");
	nxs_ext_lazyload_widget("comments");
	nxs_ext_lazyload_widget("menucontainer");
	nxs_ext_lazyload_widget("wordpresssidebar");
	nxs_ext_lazyload_widget("menuitemgeneric");
	// nxs_ext_lazyload_widget("menuitementities");
	nxs_ext_lazyload_widget("menuitemarticle");
	nxs_ext_lazyload_widget("menuitemcustom");
	nxs_ext_lazyload_widget("menuitemcategory");
	nxs_ext_lazyload_widget("socialsharing");
	nxs_ext_lazyload_widget("categories");
	nxs_ext_lazyload_widget("htmlcustom");
	nxs_ext_lazyload_widget("googlemap");
	nxs_ext_lazyload_widget("slide");
	nxs_ext_lazyload_widget("slidesincat");
	nxs_ext_lazyload_widget("sliderbox");
	
	if ($enableconceptualwidgets)
	{
		// nxs_ext_lazyload_widget("...");
	}
	
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
	nxs_ext_lazyload_widget("contactitemsecret");
	nxs_ext_lazyload_widget("contactitemreplyto");
	nxs_ext_lazyload_widget("contactitemdate");
	nxs_ext_lazyload_widget("contactitemdatetime");	
	nxs_ext_lazyload_widget("contactitemselect");
	nxs_ext_lazyload_widget("contactitemmultiselect");
	nxs_ext_lazyload_widget("contactitemhidden");
	nxs_ext_lazyload_widget("contactitemattachment");
	nxs_ext_lazyload_widget("contactitemfileattachment");
	nxs_ext_lazyload_widget("formitemcaptcha");
	nxs_ext_lazyload_widget("formitemcheckbox");
	nxs_ext_lazyload_widget("formitemhtml");
	nxs_ext_lazyload_widget("blog");
	nxs_ext_lazyload_widget("archive");
	nxs_ext_lazyload_widget("logo");
	nxs_ext_lazyload_widget("signpost");
	nxs_ext_lazyload_widget("social");
	nxs_ext_lazyload_widget("callout");
	nxs_ext_lazyload_widget("seo");
	nxs_ext_lazyload_widget("lang");
	nxs_ext_lazyload_widget("bio");
	nxs_ext_lazyload_widget("tumbler");
	nxs_ext_lazyload_widget("text");
	nxs_ext_lazyload_widget("fblikebox");
	nxs_ext_lazyload_widget("googledoc");
	nxs_ext_lazyload_widget("rssfeed");
	nxs_ext_lazyload_widget("breadcrumb");
	nxs_ext_lazyload_widget("image");
	nxs_ext_lazyload_widget("search");
	nxs_ext_lazyload_widget("contact");
	nxs_ext_lazyload_widget("wordpresstitle");
	nxs_ext_lazyload_widget("quote");
	nxs_ext_lazyload_widget("radial");
	nxs_ext_lazyload_widget("eventsbox");
	nxs_ext_lazyload_widget("eventsboxitem");
	nxs_ext_lazyload_widget("csv");
	nxs_ext_lazyload_widget("section");
	nxs_ext_lazyload_widget("vectorart");
	nxs_ext_lazyload_widget("wpmenu");
    
	if ($enableconceptualwidgets)
	{
		// nxs_ext_lazyload_widget("...");
	}
	
	nxs_ext_lazyload_widget("target");
	nxs_ext_lazyload_widget("flickr");
	nxs_ext_lazyload_widget("carousel");
	nxs_ext_lazyload_widget("carouselitem");
	nxs_ext_lazyload_widget("banner");
	nxs_ext_lazyload_widget("banneritem");

	// PAGEDECORATORS
	nxs_ext_lazyload_widget("pageslider");
	nxs_ext_lazyload_widget("pagebackground");
	nxs_ext_lazyload_widget("pagepopup");
	nxs_ext_lazyload_widget("pageslidetotop");
	nxs_ext_lazyload_widget("pageinpagesectionmenu");
	nxs_ext_lazyload_widget("pagefixedheader");

	if ($enableconceptualwidgets)
	{
		
	}
	
	// BUSINESS RULES
	nxs_ext_lazyload_widget("busrulecatchall");	
	nxs_ext_lazyload_widget("busrulepostid");	
	nxs_ext_lazyload_widget("busrulecategory");	
	nxs_ext_lazyload_widget("busrulepostauthor");	
	nxs_ext_lazyload_widget("busrulearchivetype");	
	nxs_ext_lazyload_widget("busrulehome");
	nxs_ext_lazyload_widget("busrule404");
	nxs_ext_lazyload_widget("busrulearchivecat");
	nxs_ext_lazyload_widget("busruleauthentication");
	nxs_ext_lazyload_widget("busrulearchive");	
	nxs_ext_lazyload_widget("busrulesearch");	
	nxs_ext_lazyload_widget("busrulemaintenance");	
	nxs_ext_lazyload_widget("busruleposttype");
	nxs_ext_lazyload_widget("busrulehaspostcontent");
	nxs_ext_lazyload_widget("busrulesemanticlayout");
	nxs_ext_lazyload_widget("busruleurl");
	nxs_ext_lazyload_widget("busruledeclarativecondition");
	
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

/* *************** */

// USAGE: nxs_lazyload_plugin_widget(__FILE__, "nameofwidget");

function nxs_lazyload_plugin_widget($file, $widget)
{
	// store file loc in lookup (mem)
	global $nxs_gl_widget_file;
	if ($nxs_gl_widget_file == null)
	{
		$nxs_gl_widget_file = array();
	}
	$nxs_gl_widget_file[$widget] = $file;
	
	$action = "nxs_ext_inject_widget_" . $widget;
	add_action($action, "nxs_inject_plugin_widget");
}

function nxs_inject_plugin_widget($widget)
{
	global $nxs_gl_widget_file;
	$file = $nxs_gl_widget_file[$widget];
	$path = plugin_dir_path($file);
	$filetobeincluded = $path . '/widgets/' . $widget . '/widget_' . $widget . '.php';
	require_once($filetobeincluded);
}

/* *************** */

function nxs_getobsoletewidgetids()
{
	$result = array();
	
	$result[] = "contactbox";
	$result[] = "busrulesemanticlayout";
	
	if (!nxs_enableconceptualwidgets())
	{
		// $result[] = "...";
	}

	return $result;
}

?>
