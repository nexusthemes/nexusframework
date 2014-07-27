<?php 
//
// (C) Nexus Studios
//

?>
<div id="nxs_ajax_thickbox" style="display:none">
	<div class="nxs-popup-dyncontentcontainer nxs-do-selectable nxs-shadow1"></div>
</div>
<script type="text/javascript">
	<?php

	global $nxs_global_current_containerpostid_being_rendered;
	$containerpostid = $nxs_global_current_containerpostid_being_rendered;
	
	if ($containerpostid == 0)
	{
		global $post;
		$containerpostid = $post->ID;
	}

	$sitemeta = nxs_getsitemeta_internal(false);	// note this is cached
	$locale = apply_filters( 'theme_locale', get_locale(), "");
	$clipboardhandler = apply_filters('nxs_clipboardhandler', "clipboard");
	
	$guieffectsenabled = "true";
	if (is_admin())
	{
		$guieffectsenabled = "false";
	}
	
	if (isset($containerpostid) && $containerpostid != 0)
	{
		$pagetemplate = nxs_getpagetemplateforpostid($containerpostid);
	}
	else
	{
		$pagetemplate = "notapplicable";
	}
	
	// encode the query of the main loop,
	// this is used by webservices that need to re-use the context (#2389724)
	global $wp_query;
	$query_vars = $wp_query->query_vars;
	$jsonencodedquery_vars = json_encode($query_vars);
	$urlencodedjsonencodedquery_vars = urlencode($jsonencodedquery_vars);

	?>
	// returns the css to be used as a template for the theme, plus plugins using extensions
	function nxs_js_get_customcsstemplate(csslookup)
	{
		var u = "";
		<?php
		
		$cssfilestoinclude = array();
		
		// plugins and themes can extend this list by using the following filter
		$cssfilestoinclude = apply_filters("nxs_getcsstemplates", $cssfilestoinclude);
		
		foreach ($cssfilestoinclude as $cssfilename)
		{
			if (file_exists($cssfilename))
			{
				$csscontent = file_get_contents($cssfilename);
				$csscontent = trim(preg_replace('/\s+/', ' ', $csscontent));
				$escapedcsscontent = nxs_render_html_escape_doublequote($csscontent);
				?>
				/* CSS TEMPLATE: <?php echo $cssfilename; ?> */
				u = u + "<?php echo $escapedcsscontent; ?>";
				<?php
			}
			else
			{
				?>
				/* Error; CSS file not found; <?php echo $cssfilename; ?> */
				<?php
			}
		}
		?>
		
		//nxs_js_log('u:');
		//nxs_js_log(u);
		
		return u;
	}
	
	function nxs_js_getruntimecsslookup()
	{
		var result = 
		{
			<?php
			$isfirst = true;
			$cssvariables = nxs_getcssvariables();
			foreach($cssvariables as $cssvariable => $cssvariablevalue)
			{
				?>
				'<?php echo $cssvariable; ?>':'<?php echo $cssvariablevalue;?>',
				<?php
			}
			?>
			'nxs-trailer': 'unused'	// no trailing comma!
		}
		
		//nxs_js_log('nxs_js_getruntimecsslookup:');
		//nxs_js_log(result);
		
		return result;
	}
	
	// returns the alpha colors available for this theme
	function nxs_js_getcoloralphas()
	{
		<?php
		$coloralphas = nxs_getcoloralphas();
		if (count($coloralphas) > 1)
		{
			$result = nxs_numerics_to_comma_sep_array_string($coloralphas);
		}
		else if (count($coloralphas) == 1)
		{
			// this clause ensures 
			$result = "[" . $coloralphas[0] . "]";
		}
		else if (count($coloralphas) == 0)
		{
			$result = "[]";
		}
		echo "return " . $result . ";";
		?>
	}
	
	// returns the color scheme lookup, according to the latest persisted value on the server
	function nxs_js_getcolorsinpalette()
	{
		<?php
		$colortypes = nxs_getcolorsinpalette();
		if (count($colortypes) > 1)
		{
			$result = "['" . implode("','", $colortypes) . "']";
		}
		else if (count($colortypes) == 1)
		{
			// this clause ensures 
			$result = "['" . $colortypes[0] . "']";
		}
		else if (count($colortypes) == 0)
		{
			$result = "[]";
		}
		echo "return " . $result . ";";
		?>
	}
	
	// returns the color scheme lookup, according to the latest persisted value on the server
	function nxs_js_getcolorschemeaccordingtoserverside()
	{
		// inject current colorscheme
		var colorschemelookup = 
		{
			<?php
			if (nxs_hassitemeta())
			{
				$colortypes = nxs_getcolorsinpalette();
				
				//
				// when there's an activesettings
				//
				
				$palettename = nxs_colorization_getactivepalettename();
				if (isset($palettename) && $palettename != "")
				{
					// use colorization v2 implementation
					$colorizationproperties = nxs_colorization_getpersistedcolorizationproperties($palettename);
				}
				
				foreach($colortypes as $currentcolortype)
				{
					$subtypes = array("1", "2");
					foreach($subtypes as $currentsubtype)
					{
						$identification = $currentcolortype . $currentsubtype;
						
						if (isset($colorizationproperties))
						{
							// use colorization v2 implementation
							if (isset($colorizationproperties["colorvalue_" . $identification]))
							{
								$middle = $colorizationproperties["colorvalue_" . $identification];
							}
							else
							{
								// color is not (yet) supported in this palette
								$middle = "777777";
							}
						}
						else
						{
							// use fallback implementation (v1)
							if (isset($sitemeta["vg_color_" . $identification . "_m"]))
							{
								$middle = $sitemeta["vg_color_" . $identification . "_m"];
							}
							else
							{
								$middle = "777777";
							}
						}
						?>
						'color_<?php echo $identification; ?>_m':'<?php echo $middle; ?>',
						<?php
					}
				}
			}
			?>
			'nxs-trailer': 'unused'	// no trailing comma!
		};
		
		return colorschemelookup;
	}
	
	// retrieves the runtime manual css "template" to use,
	// note that the css could contain placeholders (that's why its called a "template")
	function nxs_js_get_manualcsstemplate()
	{
		<?php 
		// static lookup
		$csscontent = $sitemeta["vg_manualcss"];
		
		if ($_REQUEST["ignorecss"] == "true")
		{
			$csscontent = "/* overruled by request parameter */";
		}
		
		$csscontent = trim(preg_replace('/\s+/', ' ', $csscontent));
		$escapedcsscontent = $csscontent;
		
		// in the escapedcsscontent sometimes we see double quotes,
		// these are not allowed, as the result is wrapped in double quotes themselves
		$escapedcsscontent = str_replace("\"", "'", $escapedcsscontent);
		// backslashes should be double escaped
		$escapedcsscontent = str_replace("\\", "\\\\", $escapedcsscontent);
				
		if (is_user_logged_in()) 
		{ 
			// dynamic lookup from the flyout menu
			?>
			if (nxs_js_doesuserimpactstyle())
			{
				// IE fix; in IE the first css element is ignored...
				return ".nxsiefixignored {} " + jQuery('#vg_manualcss').val();
			}
			else
			{
				return ".nxsiefixignored {} <?php echo $escapedcsscontent; ?>";
			}
			<?php
		}
		else
		{
			?>
			return ".nxsiefixignored {} <?php echo $escapedcsscontent; ?>";
			<?php
		}
		?>
	}
	
	// see #2389724
	function nxs_js_geturlencodedjsonencodedquery_vars() { return "<?php echo $urlencodedjsonencodedquery_vars; ?>"; }
	function nxs_js_issiteresponsive() { return <?php echo ($sitemeta["responsivedesign"] == "true"); ?>; }
	function nxs_js_isinfrontend() { return <?php echo (!is_admin()); ?>; }
	function nxs_js_getlocale() { return "<?php echo get_locale(); ?>"; }
	function nxs_js_enableguieffects() { return <?php echo $guieffectsenabled;?>; }
	function nxs_js_getcontainerpostid() { return <?php echo $containerpostid;?>; }
	function nxs_js_getclipboardhandler() { return "<?php echo $clipboardhandler;?>"; }
	function nxs_js_getcontainerpagetemplate() { return "<?php echo $pagetemplate;?>"; }
	<?php
	/*
	// Note; its not a bug; we intentionally point the ajax calls to the site's root.
	// Why? Quite often 3rd party WP plugins don't expect AJAX calls to render HTML, and thus
	// they load less files in order to optimize their plugins. Its a good thing to optimize
	// plugins this way, ofcourse, but for us this is unpractical. An example is the 
	// NextGenGallery plugin. The NGG gallery plugin would not be compatible with our
	// framework if we would invoke the ajax calls through the regular URL. 
	// A certain "FLAG" is set by WP, on which the plugin behaves differently, resulting
	// (in the case of NGG) in an error when rendering HTML through AJAX calls.
	// This workaround solves this problem.
	*/
	?>
	function nxs_js_get_adminurladminajax() { return "<?php 	
		$result = get_bloginfo("url");
		if (!nxs_stringendswith($result, '/'))
		{
			// fix bug detected on Gerbers server
			$result = $result . "/";
		}
		
		// depending on the permalink structure,
		// we will add a postfix. this helps to 
		// serve webmethods on sites that
		// have a plugin or their homepage be
		// reversed proxies to some other 
		// site ("maintenance mode")
		$permalink = get_option('permalink_structure');
		if ($permalink == "")
		{
			// if no rewriting occurs (?p=1234) we will
			// add a queryparameter to the URL instead
			// of a folder/path combination (very likely
			// that would not be mapped correctly 
			// to WP, likely causing each webmethod to fail)
			$result .= "?nxs-webmethod-queryparameter=true";
		}
		else
		{
			// the permalink structure is set, quite likely
			// its safe to use a folder and file mapping
			$result .= "nxs-webmethod/nxs-webmethod.php/";
		}
		
		echo $result;
		?>";
	}
	
	// returns the set of possible values that could possibly be assigned to the specified styletype
	function nxs_js_getstyletypevalues(styletype)
	{
		var result;
		if (false) 
		{
			// nothing to do here :)
		}
		<?php
		$styletypes = nxs_getstyletypes();
		foreach ($styletypes as $currentstyletype)
		{
			?>
			else if (styletype == '<?php echo $currentstyletype; ?>')
			{
				result = <?php 
				echo nxs_style_getstyletypevaluesjsinitialization($currentstyletype);
				?>;
			}
			<?php
		}
		?>
		else
		{
			nxs_js_alert('Unsupported currentstyletype;' + styletype);
		}
		return result;
	}
	
	function nxs_js_inwpbackend() { return <?php if (is_admin()) { echo "true"; } else { echo "false"; } ?>; }
	function nxs_js_getmaxservercsschunks() { return <?php echo nxs_getmaxservercsschunks(); ?>; }
	function nxs_js_geturlcurrentpage() { return "<?php echo nxs_geturlcurrentpage(); ?>"; }
	function nxs_js_gettemplateurl() { return "<?php echo get_bloginfo('template_url'); ?>"; }
	function nxs_js_getframeworkurl() { return "<?php echo nxs_getframeworkurl(); ?>"; }
	function nxs_js_userhasadminpermissions() { return <?php if (nxs_has_adminpermissions()) { echo "true"; } else { echo "false"; } ?>; }
	
	// TODO: use webservice to retrieve the values (+caching); lazy load!
	function nxs_js_gettrans(msg)
	{
		if (msg == "Loading information") { return "<?php nxs_l18n_e("Loading information[nxs:popup,newrow,button]", "nxs_td"); ?>"; }
		if (msg == "Are you sure you want to delete this row?") { return "<?php nxs_l18n_e("Are you sure you want to delete this row?[nxs:confirm]", "nxs_td"); ?>"; }
		if (msg == "Are you sure you want to close this window?") { return "<?php nxs_l18n_e("Are you sure you want close this window?[nxs:confirm]", "nxs_td"); ?>"; }
		if (msg == "Are you sure you want to delete this page?") { return "<?php nxs_l18n_e("Are you sure you want to delete this page?[nxs:confirm]", "nxs_td"); ?>"; }
		if (msg == "Are you sure you want to delete this menu item?") { return "<?php nxs_l18n_e("Are you sure you want to delete this menu item (and its children)?[nxs:confirm]", "nxs_td"); ?>"; }
		if (msg == "Editor is now disabled") { return "<?php nxs_l18n_e("Editor is now disabled[nxs:growl]", "nxs_td"); ?>"; }
		if (msg == "Click to reactivate editor") { return "<?php nxs_l18n_e("Click to reactivate editor[nxs:button,tooltip]", "nxs_td"); ?>"; }
		if (msg == "Editor is now active again") { return "<?php nxs_l18n_e("Editor is now active again[nxs:growl]", "nxs_td"); ?>"; }
		if (msg == "Click to deactivate editor") { return "<?php nxs_l18n_e("Click to deactivate editor[nxs:button,tooltip]", "nxs_td"); ?>"; }
		if (msg == "Loading page") { return "<?php nxs_l18n_e("Loading page[nxs:growl]", "nxs_td"); ?>"; }
		if (msg == "Tip to move widget") { return "<?php nxs_l18n_e("Tip to move widget[nxs:growl]", "nxs_td"); ?>"; }
		if (msg == "Ignore unsaved popup data?") { return "<?php nxs_l18n_e("Ignore unsaved popup data?[nxs:confirm]", "nxs_td"); ?>"; }
		if (msg == "Ignore unsaved changes?") { return "<?php nxs_l18n_e("Ignore unsaved changes?[nxs:confirm]", "nxs_td"); ?>"; }
		if (msg == "Are you sure you want to remove this widget?") { return "<?php nxs_l18n_e("Are you sure you want to remove this widget?[nxs:confirm]", "nxs_td"); ?>"; }
		if (msg == "Drop here") { return "<?php nxs_l18n_e("Drop here[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "Error transferring data. Please try again later") { return "<?php nxs_l18n_e("Error transferring data. Please try again later[nxs:growl]", "nxs_td"); ?>"; }
		if (msg == "Enter a valid email address (yourname@example.org)") { return "<?php nxs_l18n_e("Enter a valid email address (yourname@example.org)[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "First accept the conditions") { return "<?php nxs_l18n_e("First accept the conditions[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "One moment") { return "<?php nxs_l18n_e("One moment[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "Drag the column layout on one of the highlighted sections") { return "<?php nxs_l18n_e("Drag the column layout on one of the highlighted sections[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "Drag the widget on one of the highlighted sections") { return "<?php nxs_l18n_e("Drag the widget on one of the highlighted sections[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "Widget was not moved") { return "<?php nxs_l18n_e("Widget was not moved[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "Please enter your name") { return "<?php nxs_l18n_e("Please enter your name[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "Please enter your email address") { return "<?php nxs_l18n_e("Please enter your email address[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "Please enter your phone number") { return "<?php nxs_l18n_e("Please enter your phone number[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "Please enter your message") { return "<?php nxs_l18n_e("Please enter your message[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "Refetched SEO") { return "<?php nxs_l18n_e("Refetched SEO[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "Refetching SEO") { return "<?php nxs_l18n_e("Refetching SEO[nxs:tip]", "nxs_td"); ?>"; }
		if (msg == "Widgets swapped") { return "<?php nxs_l18n_e("Widgets swapped", "nxs_td"); ?>"; }
		if (msg == "Widget is now empty") { return "<?php nxs_l18n_e("Widget is now empty", "nxs_td"); ?>"; }		
		if (msg == "menu is now disabled") { return "<?php nxs_l18n_e("Menu is now disabled", "nxs_td"); ?>"; }
		if (msg == "Click to reactivate menu") { return "<?php nxs_l18n_e("Click to reactivate menu", "nxs_td"); ?>"; }
		if (msg == "menu is now active again") { return "<?php nxs_l18n_e("Menu is now active again", "nxs_td"); ?>"; }
		if (msg == "Click to deactivate menu") { return "<?php nxs_l18n_e("Click to deactivate menu", "nxs_td"); ?>"; }
		if (msg == "Loading script") { return "<?php nxs_l18n_e("Loading script", "nxs_td"); ?>"; }
		if (msg == "Invalid pagenumber") { return "<?php nxs_l18n_e("Invalid pagenumber", "nxs_td"); ?>"; }
		
		return msg;
	}
</script>

<?php 
if (is_admin) 
{ 
	// WP backend is showing
	?>
	<script type='text/javascript'> var thickboxL10n = { loadingAnimation: "<?php echo nxs_getframeworkurl(); ?>/images/loadingthickbox.png" }; </script>	
	<?php 
}
?>
<script type="text/javascript" src="<?php echo nxs_getframeworkurl(); ?>/nexuscore/includes/nxs-script.js"></script>
<script type="text/javascript" src="<?php echo nxs_getframeworkurl(); ?>/nexuscore/includes/nxs-script-deferred.js" defer></script>
<script type="text/javascript" src="<?php echo nxs_getframeworkurl(); ?>/nexuscore/includes/nxs-script-admin-deferred.js" defer></script>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load('webfont','1');
</script>
<?php
$fontsbeingused = array();
// add font fam for the "active" font1
$fontfams = nxs_getmappedfontfams($sitemeta['vg_fontfam_1']);
foreach ($fontfams as $fontfam)
{
	$fontsbeingused[]= $fontfam;
}
// add font fam for the "active" font2
$fontfams = nxs_getmappedfontfams($sitemeta['vg_fontfam_2']);
foreach ($fontfams as $fontfam)
{
	$fontsbeingused[]= $fontfam;
}
?>	
<script>
	WebFont.load
	(
		{
			google: 
			{ 
      	families: 
      	[
      		<?php
      		$isfirstfont = true;
      		foreach ($fontsbeingused as $currentfont)
      		{
      			if ($isfirstfont == false)
      			{
      				echo ",";
      			}
      			else
      			{
      				$isfirstfont = false;
      			}
      			echo "'{$currentfont}'";
      		}
      		?>
      		
      	] 
      }
		}
	); 
</script>


<script type='text/javascript'>
	// fast shake
	nxs_js_colorshake();
	
	// reshake when the window is loaded; custom css tab and color pickers could apply
	// the colorshake is not executed second time, if the user was not logged on
	jQuery(window).load
	(
		function()
		{
			nxs_js_colorshake();
			nxs_js_refreshtopmenufillerheight();
		}
	);
</script>


