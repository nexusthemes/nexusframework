<?php

// theme localization occurs through the following functions,
// wrapping traditional __ and _e, as POIEDIT does not (yet)
// allow us to exclude folders or filter domains when 
// scanning folders, see 
function nxs_l18n__($key, $domain)
{
	return __($key, $domain);
}

function nxs_l18n_e($key, $domain)
{
	return _e($key, $domain);
}

// kudos to http://stackoverflow.com/questions/5266945/wordpress-how-detect-if-current-page-is-the-login-page
function nxs_iswploginpage()
{
  return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

function nxs_getthemeurl()
{
	return get_bloginfo('template_url');
}

function nxs_font_getfontfamiliesforfontidentifier($fontidentifier)
{
	$sitemeta	= nxs_getsitemeta();
	$configuredfontfamily = $sitemeta["vg_fontfam_{$fontidentifier}"];
	$fontfams = nxs_getmappedfontfams($configuredfontfamily);
	return $fontfams;
}

// kudos to http://stackoverflow.com/questions/5266945/wordpress-how-detect-if-current-page-is-the-login-page
function nxs_is_login_page() 
{
  return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

function nxs_disabledwprevisions()
{
	// prevent revisions from being created
	remove_action('pre_post_update', 'wp_save_post_revision');
}

function nxs_getframeworkurl()
{
	$templateurl = get_bloginfo('template_url');
	if (NXS_FRAMEWORKSHARED == "true")
	{
		// remove last 2 "folders" from the template url
		$templateurl = dirname(dirname($templateurl));
	}
	
	$result = $templateurl . "/" . NXS_FRAMEWORKNAME . "/" . NXS_FRAMEWORKVERSION;
	return $result;
}

function nxs_shouldshowadminbar()
{
	$result = false;	// default hide the admin bar / adminbar / topbar
	
	if (nxs_hassitemeta())
	{
		$sitemeta = nxs_getsitemeta();
		if ($sitemeta["wpmanagement_showadminbar"] == "show")
		{
			$result = true;
		}
	}
	
	// filters can overrule the output
	
	$result = apply_filters("nxs_f_shouldshowadminbar", $result);
	
	return $result;
}

function nxs_hideadminbar()
{
	if (!nxs_shouldshowadminbar())
	{
		// verberg het toolbar menu aan de bovenkant
		add_filter('show_admin_bar', '__return_false');
		wp_deregister_style('admin-bar');
		remove_action('wp_head', '_admin_bar_bump_cb');
	}
}

// helper function used by some filters 
// return_true
function nxs_returntrue()
{
	return true;
}

// search tags; return_false
function nxs_returnfalse()
{
	return false;
}

// kudos to http://www.php.net//manual/en/function.shuffle.php
function nxs_shuffle_assoc($list) 
{ 
  if (!is_array($list)) return $list; 

  $keys = array_keys($list); 
  shuffle($keys); 
  $random = array(); 
  foreach ($keys as $key) 
  {
    $random[$key] = $list[$key];
  }

  return $random; 
} 

function nxs_ensure_theme_translations_are_loaded()
{
	if (!defined('NXS_DEFINE_NXSTHEMETRANSLATIONLOADED'))
	{
		// 
		// localization
		//
		$domain = 'nxs_td';
		$locale = apply_filters('theme_locale', get_locale(), $domain);
		$mofile = dirname(dirname(dirname(__FILE__))) . "/lang/nxs-theme-" . $locale . ".mo"; 
		if (file_exists($mofile))
		{
			$mofile = dirname(dirname(dirname(__FILE__))) . "/lang/nxs-theme-" . $locale . ".mo"; 
		}
		else
		{
			// if the specified locale is N/A, use English as the fallback
			$mofile = dirname(dirname(dirname(__FILE__))) . "/lang/nxs-theme-en_US.mo"; 
		}
		
		$res = load_textdomain($domain, $mofile);
		
		define('NXS_DEFINE_NXSTHEMETRANSLATIONLOADED', true);	// default to true (improved performance), false means all transients are ignored
	}
	else
	{
		// already loaded 
	}
}
add_action("nxs_load_l18ns", "nxs_ensure_theme_translations_are_loaded");

// let op, dit is na INITIALISATIE van de theme, dit betekent niet dat de theme is gekozen/geactiveerd of dat er van theme is geswitcht!
// we kiezen hier met opzet voor een option, niet voor een post meta field
// de options worden immers niet ge-importeerd en ge-exporteerd.
function nxs_after_theme_setup()
{
	
	
	do_action("nxs_load_l18ns");
	
	// set het nxs_themepath indien dat nog niet was gedaan	
	nxs_validatethemedata();
	
	$nxs_theme_status = get_option('nxs_theme_setup_status');
	if ($nxs_theme_status != 'initialized')
	{
		nxs_reinitializetheme();
	}
	
	// fix; the wp backend can use customize.php, which uses ajax
	// call to get the content of the homepage. For some reason these ajax
	// calls result in a sort of endless loop, producing time outs and resource problems
	// on the client site. This particular code fixes this problem
	if (is_admin())
	{
 		if (is_user_logged_in())
	 	{
	 		$lowercaseurl = strtolower(nxs_geturlcurrentpage());
	 		if (nxs_stringcontains($lowercaseurl, "customize.php"))
	 		{
	 	 		// if user is logged in, and if customize.php is requested, intercept it
		 		// and redirect
				$url = nxs_geturl_home();
				$url = nxs_addqueryparametertourl_v2($url, "nxstrigger", "wpcustomize", true, true);
				
 				wp_redirect($url);
 				die();
	 		}
	 	}
 	}
	
	//
	if (nxs_shouldusecache_stage0())
	{
		nxs_setupcache();
	}
	
	if (!nxs_shouldshowadminbar())
	{
		// we disable the admin bar, as 
		show_admin_bar(false);
	}
}

// stage0; first consideration stage; whether or not to cache data on this site
function nxs_shouldusecache_stage0()
{
	$result = false;
	
	if (nxs_hassitemeta())
	{
		$sitemeta	= nxs_getsitemeta();
		if ($sitemeta["pagecaching_enabled"] != "")
		{
			$result = true;
		}
	}
	
	// allow plugins/extensions to intercept this
	$result = apply_filters("nxs_shouldusecache_stage0", $result);
	
	return $result;
}

// stage1; second consideration stage; whether or not to cache data on this site
function nxs_shouldusecache_stage1()
{
	
	
	$result = false;
	
	if (!is_user_logged_in())
	{
		$result = true;
	}
	
	global $woocommerce;
	if (isset($woocommerce))
	{
		if (isset($woocommerce->cart))
		{
			//check if any product is in the cart
			if ( sizeof( $woocommerce->cart->get_cart() ) > 0 )
			{
				$result = false;
			}
		}
	}
	
	if ($result)
	{
		if (nxs_isnxswebservicerequest())
		{
			$result = false;	
		}
	}
	
	$url = nxs_geturlcurrentpage();
	if (nxs_stringcontains($url, "cart"))
	{
		$result = false;
	}
	else if (nxs_stringcontains($url, "checkout"))
	{
		$result = false;
	}
	else if (nxs_stringcontains($url, "nonce"))
	{
		// never cache (wp)nonces 
		$result = false;
	}
	else if (nxs_stringcontains($url, "remove_item"))
	{
		$result = false;
	}
	else if (nxs_stringcontains($url, "add-to-cart"))
	{
		$result = false;
	}
	
	if ($_REQUEST["nxscache"] == "off")
	{
		// no cache is explicitly instructed not to use cache
		$result = false;
	}
	
	if ($result)
	{
		if(session_id() == '') 
		{
			// session has not yet started
		}
		else
		{
			// if session has started, dont use the cache
			$result = false;
		}
	}
	
	// only GET requests can be cached
	if ($result)
	{
		$request = $_SERVER['REQUEST_METHOD'];
		if ($request == "GET")
		{
			// ok
		}
		else
		{
			// post (and other) requests are never cached
			$result = false;
		}
	}
	
	// allow plugins/extensions to intercept this
	$result = apply_filters("nxs_shouldusecache_stage1", $result);
	
	if ($result)
	{
		//echo "will cache";
	}
	
	return $result;	
}

function nxs_cache_getmd5hash()
{
	$data = "";
	$data .= nxs_geturlcurrentpage();
	$result = md5($data);
	return $result;
}

function nxs_cache_getcachefolder()
{
	$md5hash = nxs_cache_getmd5hash();
	$uploaddir = wp_upload_dir();
	$basedir = $uploaddir["basedir"];
	$result = $basedir . DIRECTORY_SEPARATOR . "nxscache";
	return $result;
}

function nxs_cache_clear()
{
	if (nxs_has_adminpermissions())
	{
		$path = nxs_cache_getcachefolder();
		nxs_recursive_removedirectory($path);
	}
}

function nxs_cache_getcachedfilename()
{
	$md5hash = nxs_cache_getmd5hash();
	$cachedfile = nxs_cache_getcachefolder() . DIRECTORY_SEPARATOR . $md5hash . ".cache";
	return $cachedfile;
}

function nxs_cache_getexpirationinsecs()
{
	$sitemeta	= nxs_getsitemeta();
	$result = $sitemeta["pagecaching_expirationinsecs"];
	if ($result == "")
	{
		$result = "86400";
	}
	
	// allow plugins to override the behaviour
	$result = apply_filters("nxs_cache_getexpirationinsecs", $result);
	
	return $result;
}


function nxs_ensurenocacheoutput($buffer)
{
	$file = nxs_cache_getcachedfilename();
	if (is_file($file))
	{
		// remove file
		unlink($file);
	}
	// return buffer as-is
	return $buffer;
}

function nxs_storecacheoutput($buffer)
{
	$shouldstore = true;
	$nocacheexplanations = array();
	
	if(session_id() != '') 
	{
		// dont store; the session is set
		$shouldstore = false;
		$nocacheexplanations[] = "session is set/active";
	}
	
	if ($shouldstore)
	{
		if (is_404())
		{
			// dont store 404's
			$shouldstore = false;
			$nocacheexplanations[] = "is 404";
		}
	}
	
	if ($shouldstore)
	{
		global $woocommerce;
		if (isset($woocommerce))
		{
			if (isset($woocommerce->cart))
			{
				//check if product already in cart
				if ( sizeof( $woocommerce->cart->get_cart() ) > 0 )
				{
					$shouldstore = false;
					$nocacheexplanations[] = "woocommerce cart contains at least 1 item";
				}
			}
		}
	}
	
	if ($shouldstore)
	{
		if(session_id() == '') 
		{
			// session has not yet started
		}
		else
		{
			// if session has started, dont use the cache
			$shouldstore = false;
			$nocacheexplanations[] = "session is started";
		}
	}
	
	if ($shouldstore)
	{
		global $nxs_gl_cache_pagecache;
		if (isset($nxs_gl_cache_pagecache))
		{
			if ($nxs_gl_cache_pagecache == false)
			{
				$shouldstore = false;
				$nocacheexplanations[] = "global nxs_gl_cache_pagecache told us to not cache";
			}
		}
	}
	
	
	$lowercasecontenttype = "";
	if ($shouldstore)
	{
		$headerssent = headers_list();
		
		foreach ($headerssent as $currentheadersent)
		{
			$lowercase = strtolower($currentheadersent);
			if (nxs_stringstartswith($lowercase, "content-type:"))
			{
				$pieces = explode(":", $lowercase, 2);
				if (count($pieces) == 2)
				{
					$lowercasecontenttype = trim($pieces[1]);
				}
				else
				{
					//
				}
			}
			else
			{
				
			}
		}
		
		if (nxs_stringstartswith($lowercasecontenttype, "text/html"))
		{
			// okidoki, cache!
		}
		else
		{
			// unknown content, likely we dont want to store this
			// return "$contenttype; fiets:" . $a . "]";
			$shouldstore = false;
			$nocacheexplanations[] = "unsupported contenttype";
		}
	}
	
	if ($shouldstore)
	{
		if ($buffer == "")
		{
			// useless to store
			$shouldstore = false;
			$nocacheexplanations[] = "buffer is empty";
		}
		else if (!nxs_stringcontains($buffer, "</html>"))
		{
			// case 2435987; this would indicate a partially rendered page is outputted
			// partially rendered pages should never be stored as cached items
			$shouldstore = false;
			$nocacheexplanations[] = "no end of html tag found in buffer";
		}
	}
	
	if($shouldstore) 
	{
		$file = nxs_cache_getcachedfilename();
		$dir = dirname($file);
		
		if(!is_dir($dir)) 
		{
			// if the folder doesn't yet exist, create it!
			mkdir($dir, 0777, true);
		}

		// enhance the output so we know its cached
		$cached = $buffer;
		$cached = str_replace("</body>", "</body><!-- CACHED " . NXS_UNIQUEIDFORREQUEST . " -->", $cached);			
		
		// allow plugins to enhance the cached output even further
		$cached = apply_filters("nxs_getcachedoutput", $cached);
		
		// fix: first encode the cached data to UTF8
		file_put_contents($file, $cached, LOCK_EX);
	}
	else
	{
	}
	
	if ($_REQUEST["nxs"] == "debugcache")
	{
		$buffer = nxs_prettyprint_array($nocacheexplanations);
	}
	
	return $buffer;
}

function nxs_setupcache()
{
	if (nxs_shouldusecache_stage1())
	{
		$nxs_shouldusecache_stage2 = false;
		
		$file = nxs_cache_getcachedfilename();
		if (file_exists($file))
		{
			$cachetime = filectime($file);
			$now = time();
			$diff = $now - $cachetime;
			if ($diff > 0)
			{
				$pagecaching_expirationinsecs = nxs_cache_getexpirationinsecs();
				
				if ($pagecaching_expirationinsecs == "never")
				{
					$nxs_shouldusecache_stage2 = true;
				}
				else if ($diff < $pagecaching_expirationinsecs)
				{
					$nxs_shouldusecache_stage2 = true;	
				}
				else
				{
					// cache is deprecated; its too old; dont use the cache
				}
			}
			else
			{
				// cache was written in the future?
			}
			
			if ($nxs_shouldusecache_stage2)
			{
				$filesize = filesize($file);
				if ($filesize == 0)
				{
					$nxs_shouldusecache_stage2 = false;
				}
			}
		}
		else
		{
			//error_log("cache stage 2 file not found $file");
		}
			
		if ($nxs_shouldusecache_stage2)
		{
			// set headers
			$htmltype = get_bloginfo('html_type');
			$charset = nxs_getcharset();
			
			header("Content-type: {$htmltype}; charset={$charset}");
			
			echo file_get_contents_utf8($file);
			die();
		}
		else
		{
			nxs_ob_start("nxs_storecacheoutput");
		}
	}
	else
	{
		// proceed as usual... don't cache anything
		nxs_ob_start("nxs_ensurenocacheoutput");
	}
}

function file_get_contents_utf8($fn) 
{
 	$content = file_get_contents($fn);
  return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

function nxs_nositesettings_adminnotice()
{
	$url = nxs_geturlcurrentpage();
	$url = nxs_addqueryparametertourl_v2($url, "reimportsource", "true", true, true);
	$noncedurl = wp_nonce_url($url, 'reimportresource');
  ?>
  <div class="error">
    <p><?php nxs_l18n_e("This theme requires active settings. <a href='$noncedurl'>Import initial contents for this theme</a> to recreate these active settings.", "nxs_td"); ?></p>
  </div>
  <?php
}

function nxs_init_themeboot()
{
	if (is_admin())
	{
 		if ($_REQUEST["reimportsource"] == "true")
 		{
 			check_admin_referer('reimportresource');
 			// this will reset roles & capabilities and import content
 			nxs_after_switch_theme();
 		}
 		if (!nxs_hassitemeta())
 		{
 			if ($_REQUEST["oneclickcontent"] != "")
 			{
 				// absorb message; system is importing
 			}
 			else
 			{
 				add_action('admin_notices', 'nxs_nositesettings_adminnotice');
 			}
 		}
 	}
 	
}

function nxs_renderplaceholderwarning($message)
{
	if (nxs_has_adminpermissions())
	{
		?>
		<div class="empty nxs-border-dash nxs-admin-wrap nxs-hidewheneditorinactive autosize-smaller">
			<div class='placeholder-warning'>
				<p><?php echo $message; ?></p>
			</div>
		</div>
		<?php
	}
	else
	{
		?>
		<!-- warning detected; please sign in to see the warning -->
		<?php
	}
}

function nxs_date_todatestring($timestamp)
{
	$dayhtml = date('j', $timestamp);
	
	$monthhtml = nxs_getlocalizedmonth(date('m', $timestamp));
	$yearhtml = date('Y', $timestamp);
	$result = $dayhtml . " " . $monthhtml . " " . $yearhtml;
	return $result;
}

function nxs_date_gettotaldaysinterval($timestamp)
{
	$result = round($timestamp / 86400);
	return $result;
}

function nxs_getlocalizedmonth($monthleadingzeros)
{
	$result = "";
	
	if ($monthleadingzeros == "") { $result = nxs_l18n__("month_none", "nxs_td"); }
	else if ($monthleadingzeros == "01") { $result = nxs_l18n__("month_jan", "nxs_td"); }
	else if ($monthleadingzeros == "02") { $result = nxs_l18n__("month_feb", "nxs_td"); }
	else if ($monthleadingzeros == "03") { $result = nxs_l18n__("month_mrt", "nxs_td"); }
	else if ($monthleadingzeros == "04") { $result = nxs_l18n__("month_apr", "nxs_td"); }
	else if ($monthleadingzeros == "05") { $result = nxs_l18n__("month_may", "nxs_td"); }
	else if ($monthleadingzeros == "06") { $result = nxs_l18n__("month_jun", "nxs_td"); }
	else if ($monthleadingzeros == "07") { $result = nxs_l18n__("month_jul", "nxs_td"); }
	else if ($monthleadingzeros == "08") { $result = nxs_l18n__("month_aug", "nxs_td"); }
	else if ($monthleadingzeros == "09") { $result = nxs_l18n__("month_sep", "nxs_td"); }
	else if ($monthleadingzeros == "10") { $result = nxs_l18n__("month_oct", "nxs_td"); }
	else if ($monthleadingzeros == "11") { $result = nxs_l18n__("month_nov", "nxs_td"); }
	else if ($monthleadingzeros == "12") { $result = nxs_l18n__("month_dec", "nxs_td"); }
	else
	{
		$result = nxs_l18n__("month_unknown", "nxs_td");
	}
	
	return $result;
}

function nxs_getrowswarning($message)
{
	nxs_ob_start();
	nxs_renderrowswarning($message);
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

function nxs_renderrowswarning($message)
{
	if (nxs_has_adminpermissions())
	{
		?>
		
		<div class="nxs-postrows nxs-admin-wrap nxs-hidewheneditorinactive">
		  <div class="nxs-row">
		  	<div class="nxs-row-container nxs-containsimmediatehovermenu nxs-row1">
			    <ul class="nxs-placeholder-list">
			      <li class="nxs-one-whole nxs-placeholder">
							<div class="nxs-border-dash nxs-runtime-autocellwidth nxs-runtime-autocellsize border-radius autosize-smaller">
								<div class='placeholder-warning'>
									<p><?php echo $message; ?></p>
								</div>
							</div>
						</li>					
					</ul>
					<div class="nxs-clear"></div>
				</div>
			</div>
		</div>
		<?php
	}
	else
	{
		?>
		<!-- warning detected; please sign in to see the warning -->
		<?php
	}
}

function nxs_getplaceholderwarning($message)
{
	nxs_ob_start();
	nxs_renderplaceholderwarning($message);
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

function nxs_renderpagenotfoundwarning($message)
{
	?>
	<div class='nxs-postrows'>
		<div class="nxs-row">
			<div class="nxs-row-container nxs-containsimmediatehovermenu nxs-row1">
				<ul class="nxs-placeholder-list">
					<li class="nxs-one-whole nxs-placeholder nxs-containshovermenu1">		
						<?php echo nxs_renderplaceholderwarning($message); ?>
					</li>
				</ul>
				<div class="nxs-clear"></div>
			</div>
		</div>
	</div>
	<?php
}

function nxs_gettimestampasstring()
{
	// returns for example "05 jun 2012 15uur42"
	return strtolower(strftime("%d %b %Y %Huur%M"));
}

function nxs_reinitializetheme()
{
	// enable plugins/themes to do something when the theme is initialized at this stage
	// for example, themeswitcher could import data ...
	do_action('nxs_reinitializetheme');

	update_option('nxs_theme_setup_status', 'initialized');
	
	do_action('nxs_reinitializetheme_finished');
}

function nxs_pagetemplates_getsitewideelements()
{
	$result = array("header_postid", "footer_postid", "sidebar_postid", "subheader_postid", "subfooter_postid", "pagedecorator_postid", "content_postid", "wpcontenthandler");
	return $result;
}

function nxs_busrule_process($busruletype, $metadata, &$statebag)
{
	// load widget!
	$requireresult = nxs_requirewidget($busruletype);
	if ($requireresult["result"] == "OK")
	{
		// delegate
		$functionnametoinvoke = "nxs_busrule_{$busruletype}_process";
		if (function_exists($functionnametoinvoke))
		{
			$args = array();
			$args["template"] = $template;
			$args["metadata"] = $metadata;
			$parameters = array($args, &$statebag);
			$result = call_user_func_array($functionnametoinvoke, $parameters);
		}
		else
		{
			nxs_webmethod_return_nack("function not found; $functionnametoinvoke");
		}
	}
	else
	{
		$result = $requireresult;
	}
	
	return $result;
}

function nxs_templates_getslug()
{
	return "pagetemplaterules";
}

function nxs_cap_getdesigncapability()
{
	return "nxs_cap_design_site";
}

function nxs_hastemplateproperties()
{
	
	$result = true;
	
	return $result;
}

// derives the template properties for the current executing request, cached
function nxs_gettemplateproperties()
{
	global $nxs_gl_cache_templateprops;
	if (!isset($nxs_gl_cache_templateprops))
	{
		$result = nxs_gettemplateproperties_internal();
		
		$nxs_gl_cache_templateprops = $result;
	}
	else
	{
		$result = $nxs_gl_cache_templateprops;
	}
	
	return $result;
}

function nxs_getbusinessruleimpact($metadata)
{
	$result = "";
	
	$impact = array();
		
	$sitewideelements = nxs_pagetemplates_getsitewideelements();
	foreach($sitewideelements as $currentsitewideelement)
	{
		if ($currentsitewideelement == "header_postid")
		{
			$translatedcurrentsitewideelement = nxs_l18n__("Header", "nxs_td");
		}
		else if ($currentsitewideelement == "footer_postid")
		{
			$translatedcurrentsitewideelement = nxs_l18n__("Footer", "nxs_td");
		}
		else if ($currentsitewideelement == "sidebar_postid")
		{
			$translatedcurrentsitewideelement = nxs_l18n__("Sidebar", "nxs_td");
		}
		else if ($currentsitewideelement == "subheader_postid")
		{
			$translatedcurrentsitewideelement = nxs_l18n__("Subheader", "nxs_td");
		}
		else if ($currentsitewideelement == "subfooter_postid")
		{
			$translatedcurrentsitewideelement = nxs_l18n__("Subfooter", "nxs_td");
		}
		else if ($currentsitewideelement == "content_postid")
		{
			$translatedcurrentsitewideelement = nxs_l18n__("Content", "nxs_td");
		}
		else if ($currentsitewideelement == "wpcontenthandler")
		{
			$translatedcurrentsitewideelement = nxs_l18n__("WP Content", "nxs_td");
		}
		else if ($currentsitewideelement == "pagedecorator_postid")
		{
			$translatedcurrentsitewideelement = nxs_l18n__("Pagedecorator", "nxs_td");
		}
		else
		{
			// not yet translated
			$translatedcurrentsitewideelement = "[" . $currentsitewideelement . "]";
		}
		
		$selectedvalue = $metadata[$currentsitewideelement];
		if ($selectedvalue == "")
		{
			// skip
		} 
		else if ($selectedvalue == "@leaveasis")
		{
			// skip
		}
		else if ($selectedvalue == "@suppressed")
		{
			// reset
			$impact[] = "{$translatedcurrentsitewideelement}: none";
		}
		else if (nxs_stringstartswith($selectedvalue, "@template"))
		{
			// template
			$selectedvaluepieces = explode("@", $selectedvalue);
			$impact[] = "{$translatedcurrentsitewideelement}: {$selectedvaluepieces[2]}";
		}
		else
		{
			// set the value as selected
			$title = nxs_gettitle_for_postid($selectedvalue);
			$url = nxs_geturl_for_postid($selectedvalue);
			
			$poststatus = get_post_status($selectedvalue);
			if ($poststatus != "publish" && $poststatus != "future")
			{
				$title .= " (<b class='blink'>" . nxs_l18n__("warning, not found!", "nxs_td") . "</b> <span class='nxs-icon-point-left'></span>)";
			}
			
			$impact[] = "<a target='_blank' href='{$url}'>{$translatedcurrentsitewideelement}: " . $title . "</a>";
		}
	}
	
	if (count($impact) == 0)
	{
		$impact[] = "no impact";
	}

	$result .= "<ul><li>";
	$result .= implode("</li><li>", $impact);
	$result .= "</li></ul>";
	
	return $result;
}

function nxs_gettemplateproperties_internal()
{
	$result = array();
	
	$ishandled = false;
	
	if (is_singular())
	{
		// users can overrule the layout engine
		$postid = get_the_ID();
		
		$nxs_semanticlayout = get_post_meta($postid, 'nxs_semanticlayout', true);
		if ($nxs_semanticlayout == "landingpage")
		{
			$ishandled = true;
			$result = array
			(
				"content_postid" => $postid,
				"wpcontenthandler" => "@template@onlywhenset",
				"result" => "OK",
			);
		}
	}
	
	if (!$ishandled)
	{	
		$query = new WP_Query(array('name' => nxs_templates_getslug(),'post_type' => 'nxs_busrulesset'));
			
		$statebag = array();
		$statebag["vars"] = array();
		$statebag["out"] = array();
		
		//
		// initial values
		//
		
		if (is_singular())
		{
			$statebag["out"]["content_postid"] = get_the_ID();
		}
		else if (is_archive())
		{
		}
		
		if ( $query->have_posts() ) 	
		{
			$postid = $query->posts[0]->ID;
			$result["templaterulespostid"] = $postid;
			$businessrules = nxs_parsepoststructure($postid);
					
			$index = 0;
			foreach ($businessrules as $currentbusinessrule) 
			{
				$content = $currentbusinessrule["content"];
				$businessruleelementid = nxs_parsepagerow($content);
				$placeholdermetadata = nxs_getwidgetmetadata($postid, $businessruleelementid);
				$placeholdertype = $placeholdermetadata["type"];					
				
				if ($placeholdertype == "" || $placeholdertype == "undefined" || !isset($placeholdertype)) 
				{
					// empty row / rule, ignore it
				}
				else 
				{
					// store this item as one of the matching rules
					$busrule_processresult = nxs_busrule_process($placeholdertype, $placeholdermetadata, $statebag);
					if ($busrule_processresult["result"] == "OK")
					{
						$traceitem = array
						(
							"placeholdertype" => $placeholdertype,
							"ismatch" => $busrule_processresult["ismatch"],
						);
						$result["trace"][] = $traceitem;
						
						if ($busrule_processresult["ismatch"] == "true")
						{
							$lastmatchingrule = $placeholdertype;
							
							// the process function is responsible for filling the out property
							if ($busrule_processresult["stopruleprocessingonmatch"] == "true")
							{
								break;
							}
						}
						else
						{
							// continu to next rule
						}
					}
					else
					{
						// if applying of a rule failed, we skip it
					}
				}
			}
			
			// the system should have derived site wide elements
			$sitewideelements = nxs_pagetemplates_getsitewideelements();
			foreach($sitewideelements as $currentsitewideelement)
	  	{
	  		$result[$currentsitewideelement] = $statebag["out"][$currentsitewideelement];
	  	}
	  	
	  	$result["lastmatchingrule"] = $lastmatchingrule;
			
			$result["result"] = "OK";
		}
		else
		{
			$result["result"] = "NACK";
		}
	}
	
	return $result;
}

// add_new_article
function nxs_addnewarticle($args)
{	
	extract($args);
	
	if ($slug == "")
	{
		$slug = 'new-' . nxs_getrandompostname();
	}
	if ($titel == "")
	{
		nxs_webmethod_return_nack("title not set");
	}
	if ($nxsposttype == "")
	{
		nxs_webmethod_return_nack("nxsposttype not set");
	}
	
	if ($wpposttype == "")
	{
		// derive from nxsposttype
		$posttype = nxs_getposttype_by_nxsposttype($nxsposttype);
	}
	else
	{
		$posttype = $wpposttype;
	}
	
	if (!isset($poststatus) || $poststatus == "")
	{
		$poststatus = "publish";	// if not specified, publish immediately
	}

	$original_slug = $slug;

	$c = 2;
	while ( true ) 
	{
		$check_post_id = nxs_getpostidbyslug($slug);
		if ( !$check_post_id ) 
		{
			// means it doesn't yet exist
			break;
		}

		if ( $c > 25 ) {
			nxs_webmethod_return_nack("unable to find a available slug $slug");
		}
		
		$slug = $original_slug . '-' . $c;
		$c ++;	
	}
	
	// Create post object
  $my_post = array
  (
		'post_title' => $titel,
		'post_name' => $slug,	// url
		'post_content' => $post_content,
		'post_status' => $poststatus,
		'post_author' => wp_get_current_user()->ID,
		'post_excerpt' => $post_excerpt,
		'post_type' => $posttype,
	);
	$postid = wp_insert_post($my_post, $wp_error);
	
	if ($postid == 0)
	{
		nxs_webmethod_return_nack("unable to insert post; $titel; $slug; $posttype; " . $postid);
	}
	
	if ($globalid != "")
	{
		nxs_reset_globalidtovalue($postid, $globalid);
	}
	
	// if specified, store the subposttype,
	// this is needed for generic lists, for example
	// there the subposttype is used 
	if ($nxssubposttype != "")
	{
		// we store the subposttype
		nxs_set_nxssubposttype($postid, $nxssubposttype);
	}
	
	//
	// add categories (if supplied)
	//
	if ($selectedcategoryids != "")
	{
		// voeg categorien toe aan deze postid
		// [5][2][1]
		$newcats = array();
		$splitted = explode("[", $selectedcategoryids);
		foreach($splitted as $splittedpiece)
		{
			// bijv. "1]"
			if ($splittedpiece == "")
			{
				// ignore
			}
			else
			{
				// bijv. "1]"
				$newcats[] = substr($splittedpiece, 0, -1);
			}			
		}
		
		// Update categories
		wp_set_post_categories($postid, $newcats);
	}
	else
	{
		// echo "TEMP;NOT SET";
	}
	
	if (!isset($postwizard) || $postwizard == "")
	{
		if ($nxsposttype == "post")
		{
			nxs_postwizard_setuppost_noparameters($postid, "newpost");
		}
		else if ($nxsposttype == "sidebar")
		{
			nxs_postwizard_setuppost_noparameters($postid, "newsidebar");
		}		
		else
		{
			// empty for other types
		}
	}
	else if ($postwizard == "skip")
	{
		// no wizard logic
	}
	else
	{
		$args["postid"] = $postid;
		$args["postwizard"] = $postwizard;
		nxs_postwizard_setuppost($args);
	}
	
	if ($poststructure != "")
	{
		nxs_updatepoststructure($postid, $poststructure);
	}
	
	//
	//
	
	//
	// add 'wppagetemplate', if supplied
	//
	if ($wppagetemplate == "")
	{
		// not supplied
	}
	else
	{
		update_post_meta($postid, '_wp_page_template', nxs_get_backslashescaped($wppagetemplate));
	}
	
	//
	// update page contents to ensure any 'after processing' is handled
	// (bijv. cache output)
	//
	nxs_after_postcontents_updated($postid);
	
	// instruct the system to create a page (we don't want post here)
	if ($args["createpage"] == "true")
	{
		// convert to page...
		nxs_converttopage($postid);
		// mark pagetemplate as 'webpage'
		nxs_updatepagetemplate(array('postid'=>$postid, 'pagetemplate'=>'webpage'));
	}
	
	$url = nxs_geturl_for_postid($postid);

	if ($poststatus == "publish")
	{
		wp_publish_post($postid);
	}
	
	if ($postmetas != "")
	{
		foreach ($postmetas as $postmetakey => $postmetavalue)
		{
			$r = add_post_meta($postid, $postmetakey, $postmetavalue);
		}
	}
	
	
	
	//
	// create response
	//

	$responseargs = array();
	$responseargs["result"] = "OK";
	$responseargs["postid"] = $postid;
	$responseargs["globalid"] = nxs_get_globalid($postid, true);
	$responseargs["url"] = $url;
	
	return $responseargs;
}

// custom post type cpt
function nxs_registernexustype($title, $ispublic)
{
	$taxonomies = array();
	nxs_registernexustype_withtaxonomies($title, $taxonomies, $ispublic);
}


function nxs_cutstring($string, $length)
{
	return (strlen($string) > ($length + 3)) ? substr($string,0,$length).'...' : $string;
}

function nxs_getpostidbyslug($slug)
{
	global $wpdb;

	$sql = "SELECT ID FROM $wpdb->posts WHERE post_name = %s LIMIT 1";
	$result = $wpdb->get_var( $wpdb->prepare( $sql, $slug) );
	
	return $result;
}

function nxs_getcategoryidbyname($name)
{
	$term = get_term_by('name', $name, 'category');
	return $term->term_id;
}

function nxs_getcategorynameandslugs($postid)
{
	$post_categories = wp_get_post_categories($postid);
	$cats = array();
	
	foreach($post_categories as $c){
		$cat = get_category($c);
		$cats[] = array('name' => $cat->name, 'slug' => $cat->slug, 'id' => $cat->cat_ID);
	}
	
	return $cats;
}

function nxs_stringstartswith($haystack, $needle)
{
	$length = strlen($needle);
	return (substr($haystack, 0, $length) === $needle);
}

// kudos to http://stackoverflow.com/questions/3225538/delete-first-instance-of-string-with-php
function nxs_stringreplacefirst($input, $search, $replacement)
{
  $pos = stripos($input, $search);
  if($pos === false)
  {
     return $input;
  }
  else
  {
  	$result = substr_replace($input, $replacement, $pos, strlen($search));
   	return $result;
  }
}

function nxs_stringendswith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function nxs_stringcontains($haystack, $needle)
{
	$ignorecasing = false;
	$result = nxs_stringcontains_v2($haystack, $needle, $ignorecasing);
	return $result;
}

function nxs_stringcontains_v2($haystack, $needle, $ignorecasing)
{
	if ($ignorecasing === true)
	{
		$pos = stripos($haystack,$needle);
	}
	else
	{
		$pos = strpos($haystack,$needle);
	}
	
	if($pos === false) 
	{
	 // string needle NOT found in haystack
	 return false;
	}
	else 
	{
	 // string needle found in haystack
	 return true;
	}
}

/** 
 * Convert a date format to a jQuery UI DatePicker format  
 * 
 * @param string $dateFormat a date format 
 * @return string 
 */ 
function nxs_date_getdatepickerformatclientside() 
{ 
	$result = get_option("date_format");
	
  	$chars = array( 
      // Day
      'd' => 'dd', 'j' => 'd', 'l' => 'DD', 'D' => 'D',
      // Month 
      'm' => 'mm', 'n' => 'm', 'F' => 'MM', 'M' => 'M', 
      // Year 
      'Y' => 'yy', 'y' => 'y', 
      // Suffix
      'S' => '',
  	); 

  	$result = strtr((string)$result, $chars); 
  	return $result;
} 

function nxs_insertarrayindex($array, $new_element, $index) 
{
	$start = array_slice($array, 0, $index); 
	$end = array_slice($array, $index);
	$start[] = $new_element;
	return array_merge($start, $end);
}

function nxs_addqueryparametertourl($url, $parameter, $value)
{
	return nxs_addqueryparametertourl_v2($url, $parameter, $value, true);
}

// return the url after setting/updating the parameter (other occurences of the same parameter are removed)
function nxs_addqueryparametertourl_v2($url, $parameter, $value, $shouldurlencode, $shouldremoveparameterfirst)
{
	if (!isset($shouldremoveparameterfirst))
	{
		$shouldremoveparameterfirst = true;
	}
	
	if ($shouldremoveparameterfirst === true)
	{
		// first remove parameter (if set)
		$url = nxs_removequeryparameterfromurl($url, $parameter);
	}
	
	$result = $url;
	if (nxs_stringcontains($url, "?"))
	{
		$result = $result . "&";
	}
	else
	{
		$result = $result . "?";
	}
	
	if ($shouldurlencode === true)
	{
		$result = $result . $parameter . "=" . urlencode($value);
	}
	else
	{
		$result = $result . $parameter . "=" . $value;
	}
	
	return $result;
}

// kudos to http://stackoverflow.com/questions/4937478/strip-off-url-parameter-with-php
function nxs_removequeryparameterfromurl($url, $parametertoremove)
{
	$parsed = parse_url($url);
	if (isset($parsed['query'])) 
	{
		$params = array();
		foreach (explode('&', $parsed['query']) as $param) 
		{
		  $item = explode('=', $param);
		  if ($item[0] != $parametertoremove) 
		  {
		  	$params[$item[0]] = $item[1];
		  }
		}
		//
		$result = '';
		if (isset($parsed['scheme']))
		{
		  $result .= $parsed['scheme'] . "://";
		}
		if (isset($parsed['host']))
		{
		  $result .= $parsed['host'];
		}
		if (isset($parsed['path']))
		{
		  $result .= $parsed['path'];
		}
		if (count($params) > 0) 
		{
		  $result .= '?' . urldecode(http_build_query($params));
		}
		if (isset($parsed['fragment']))
		{
		  $result .= "#" . $parsed['fragment'];
		}
	}
	else
	{
		$result = $url;
	}
	return $result;
}

function nxs_getwidgets($widgetargs)
{
	return nxs_getwidgets_v2($widgetargs, false);
}

function nxs_getwidgets_v2($widgetargs, $filterobsoletewidgets)
{	
	$stage1result = array();
	
	// plugins can extend this list by using the following filter
	$stage1result = apply_filters("nxs_getwidgets", $stage1result, $widgetargs);
	
	if ($stage1result == null)
	{
		$stage1result = array();
	}
	
	// 
	//
	//
	$result = array();
	$distinct = array();
	
	$obsoletewidgetids = nxs_getobsoletewidgetids();
	
	//
	// enrich the data; lookup the title for each widget added
	//
	$index = 0;
	foreach ($stage1result as $widgetdata)
	{
		$widgetid = $widgetdata["widgetid"];
		$includeitem = true;
		if ($includeitem && $filterobsoletewidgets && in_array($widgetid, $obsoletewidgetids))
		{
			$includeitem = false;
		}
		
		//
		if ($includeitem && in_array($widgetid, $distinct))
		{
			// already there
			$includeitem = false;
		}
		
		if ($includeitem)
		{
			$distinct[] = $widgetid;
			$title = nxs_getplaceholdertitle($widgetid);
			$result[$index] = $widgetdata;	// clone all meta from the original function
			$result[$index]["widgetid"] = $widgetid;
			$result[$index]["title"] = $title;
			$index++;
		}
		else
		{
			// duplicate, filtered out!
		}
	}
	
	return $result;
}

function nxs_getpagetemplates($args)
{
	// plugins can extend this list by using the following filter
	$result = apply_filters("nxs_getpagetemplates", $result, $args);
	
	//
	// enrich the data; lookup the title for each template added
	//
	$index = 0;
	foreach ($result as $pagetemplatedata)
	{
		$pagetemplateid = $pagetemplatedata["pagetemplate"];
		$title = nxs_getpagetemplatetitle($pagetemplateid);
		$result[$index]["title"] = $title;
		$index++;
	}
	
	return $result;
}

function nxs_getpostrowtemplates($args)
{
	$result = array();

	$nxsposttype = $args["nxsposttype"];
	
	if 
	(
		$nxsposttype == "post" || 
		$nxsposttype == "header" || 
		$nxsposttype == "footer" || 
		$nxsposttype == "subheader" || 
		$nxsposttype == "template" || 
		$nxsposttype == "subfooter" || 
		$nxsposttype == "pagelet"
	)
	{
		$result[] = "one";
		$result[] = "131313";
		$result[] = "1third2third";
		$result[] = "twothirdonethird";
		$result[] = "1212";
		$result[] = "121414";
		$result[] = "141412";
		$result[] = "141214";
		$result[] = "14141414";
	}
	else if ($nxsposttype == "sidebar")
	{
		$result[] = "one";
	}
	else if ($nxsposttype == "menu")
	{
		$result[] = "one";
	}
	else if ($nxsposttype == "genericlist")
	{
		$result[] = "one";
	}
	else if ($nxsposttype == "busrulesset")
	{
		$result[] = "one";
	}
	else if ($nxsposttype == "admin")
	{
		// nothing
	}
	else if ($nxsposttype == "undefined")
	{
		// nothing
	}
	else if ($nxsposttype == "searchresults")
	{
		// nothing
	}
	else if ($nxsposttype == "systemlog")
	{
		// nothing
	}
	else
	{
		if (!has_filter("nxs_getpagerowtemplates"))
		{
			nxs_webmethod_return_nack("please add filter nxs_getpagerowtemplates filter for nxsposttype {$nxsposttype}");
		}
		else
		{
			// we will assume the filter will handle this
		}		
	}
	
	if (nxs_cap_hasdesigncapabilities())
	{
		// all are allowed
	}
	else
	{
		// apply filter based on capabilities
		$subsetresult = array();
		$allowedrowtemplates = array("one");
		foreach ($result as $currentitem)
		{
			if (in_array($currentitem, $allowedrowtemplates))
			{
				$subsetresult[] = $currentitem;
			}
		}
		$result = $subsetresult;
	}
	
	// themes and plugins can extend this list by using the following filter	
	return apply_filters("nxs_getpagerowtemplates", $result, $args);
}

function nxs_getrandomplaceholderid()
{
	$random = rand(1, getrandmax());
	
	if ($random == "")
	{
		nxs_webmethod_return_nack("random is empty?! (2)");
	}
	
	return $random;
}

function nxs_getrandompagerowid()
{
	$random = rand(1, getrandmax());
	
	if ($random == "")
	{
		nxs_webmethod_return_nack("random is empty?! (3)");
	}
	
	return $random;
}

function nxs_getrandompostname()
{
	$random = rand(1000000, 9999999) . 'PID';
	
	if ($random == "")
	{
		nxs_webmethod_return_nack("random is empty?! (4)");
	}
	
	return $random;
}

// kudos to http://stackoverflow.com/questions/4356289/php-random-string-generator
function nxs_generaterandomstring($length = 10)
{
  $characters = 'abcdefghijklmnopqrstuvwxyz';
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
  }
  return $randomString;
}

function nxs_get_var_dump($variable)
{
	nxs_ob_start();
	var_dump($variable);
	$result = nxs_ob_get_clean();
	return $result;
}

// workaround for file_get_contents, kudo's to http://stackoverflow.com/questions/3979802/alternative-to-file-get-contents
// download function, can also be used to fix problems with cross domain javascript
function url_get_contents($url) 
{
	$args = array();
	$args["url"] = $url;
	return nxs_geturlcontents($args) ;
}

function nxs_geturlcontents($args) 
{
	$url = $args["url"];
	
	// note; function.php already ensures curl is available
  $session = curl_init();
  curl_setopt($session, CURLOPT_URL, $url);
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
  $timeoutsecs = $args["timeoutsecs"];
  if (!$timeoutsecs)
  {
  	$timeoutsecs = 300;
  }
	curl_setopt($session, CURLOPT_TIMEOUT, $timeoutsecs);
	curl_setopt($session, CURLOPT_USERAGENT, 'NexusService');
	
	curl_setopt($session, CURLOPT_FORBID_REUSE, 1);	// 1 means true
	curl_setopt($session, CURLOPT_FRESH_CONNECT, 1);	// 1 means true
	
	$postargs = $args["postargs"];
	if (isset($postargs))
	{
		curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
	}
	$output = curl_exec($session);
	
	$haserror = false;	
	
	if (FALSE === $output)
	{
		$haserror = true;
		$curlerror = curl_error($session);
		$curlerrorno = curl_errno($session);
  }
	
  curl_close($session);
  
  if ($haserror)
  {
  	if ($curlerrorno == 28)
  	{
  		//echo "connection timeout, retrying";
  		
  		// connection time out
  		$args["connectiontimeoutretriesleft"] = $args["connectiontimeoutretriesleft"] - 1;
  		if ($args["connectiontimeoutretriesleft"] > 0)
  		{
  			// recursion
  			$output = nxs_geturlcontents($args);
		  }
		  else
		  {
		  	// fatal
		  	error_log("Nxs; time out for $url;");
		  	return false;
		  }
  
  		// timeout
  	}
  }
  
  return $output;
}

function nxs_isdesktop()
{
	return !nxs_ishandheld();
}

function nxs_ishandheld()
{
	$filetoinclude = NXS_FRAMEWORKPATH . '/plugins/mobiledetect/Mobile_Detect.php';
	require_once($filetoinclude);
	
	$mobiledetector = new Nxs_Mobile_Detect();
	$isTablet = $mobiledetector->isTablet();
	$isMobile = $mobiledetector->isMobile();
	$result = $isTablet || $isMobile;

	// for nexusthemes.com
	// for the product image screenshots the parameter handheld can be specified 
	// this is because of a bug in the ipad landscape screenshot
	if ($_REQUEST['handheld'] == "true")
	{
		$result = true;
	}

	return $result;
}

function nxs_storemedia($args)
{	
	//error_log("nxs_storemedia INVOKED");
	
	if (has_filter("nxs_storemedia"))
	{
		// allow plugin to retrieve/store the file in a different way
		$result = apply_filters("nxs_storemedia", true, $args);
		
		if ($result === false)
		{
			error_log("storing image from filter failed, using fallback implementation; $sourcefile");
		 	
		 	$sourcefile = get_template_directory() . "/resources/data/";
			if (file_exists($sourcefile))
			{
				//error_log("found data directory in theme, using that one; $sourcefile");
				// if the data folder exists, import from that directory
				$result = nxs_storemedia_fromtheme($args);
				
				if ($result === false)
				{
					// TODO: do not log error; on our prod server this is not very practical
					error_log("storing image from theme failed, using fallback implementation; $sourcefile");
				 	$result = nxs_storemedia_remotehttpdownload($args);
				}
			}
			else
			{
				// TODO: do not log error; on our prod server this is not very practical
				error_log("did NOT find a data directory in theme, using fallback implementation; $sourcefile");
			 	$result = nxs_storemedia_remotehttpdownload($args);
			}
		}
	}
	else
	{
		// if there is no plugin available, use the default implementation
		$sourcefile = get_template_directory() . "/resources/data/";
		if (file_exists($sourcefile))
		{
			//error_log("found data directory in theme, using that one; $sourcefile");
			// if the data folder exists, import from that directory
			$result = nxs_storemedia_fromtheme($args);
			
			if ($result === false)
			{
				// TODO: do not log error; on our prod server this is not very practical
				error_log("storing image from theme failed, using fallback implementation; $sourcefile");
			 	$result = nxs_storemedia_remotehttpdownload($args);
			}
		}
		else
		{
			// TODO: do not log error; on our prod server this is not very practical
			error_log("did NOT find a data directory in theme, using fallback implementation; $sourcefile");
		 	$result = nxs_storemedia_remotehttpdownload($args);
		}
	}
	
	return $result;
}

function nxs_storemedia_fromtheme($args)
{		
	extract($args);
	
	//error_log("nxs_storemedia_fromtheme; URL: $url");
	
	// http://89.18.175.44/bbeautician/wp-content/uploads/sales/beautician/2013/10/logo.png
	// http://89.18.175.44/pestcontrol/wp-content/uploads/sites/110/2014/04/logo.png
	// http://nexus_themes/wp-content/uploads/2014/02/reference11.jpg
	
	// url is bijv.
	// http://89.18.175.44/beautician/wp-content/uploads/sales/beautician/2013/10/logo.png
	
	if (nxs_stringcontains($url, "sales/"))
	{
		// we splitten eerst de "sales/"
		$urlpiecesfirst = explode("sales/", $url);
		$url2 = $urlpiecesfirst[1];	// p.e. beautician/2013/10/logo.png
		$expected = true;
	}
	else if (nxs_stringcontains($url, "sites/"))
	{
		// for example http://89.18.175.44/pestcontrol/wp-content/uploads/sites/110/2014/04/logo.png
		// we splitten eerst de "sales/"
		$urlpiecesfirst = explode("sites/", $url);
		$url2 = $urlpiecesfirst[1];	// p.e. 110/2014/04/logo.png
		$expected = true;
	}
	else if (nxs_stringcontains($url, "nexus_themes"))
	{
		// for example http://nexus_themes/wp-content/uploads/2014/02/reference11.jpg
		// we splitten eerst de "uploads/"
		$urlpiecesfirst = explode("uploads/", $url);
		$url2 = "placeholder/" . $urlpiecesfirst[1];	// p.e. placeholder/2014/02/reference11.jpg
		$expected = true;
	}
	else
	{
		$expected = false;
		error_log("nxs_storemedia_fromtheme; unexpected format (it this an external file; $url ?)");
	}
	
	if ($expected)
	{
		// url2 is bijv.
		// beautician/2013/10/logo.png	
		// het begint dus met {themeid}/, echter, 
		// we weten de themeid hier nog niet, dus de exploden deze opnieuw ...
		$urlpieces = explode("/", $url2, 2);
		
		// $urlpieces[1] is nu dus "2013/10/logo.png"
			
		// check if the url has a local variant file
		// url is for example http://89.18.175.44/plumber/wp-content/uploads/sites/26/2013/09/offer.jpg
		$sourcefile = get_template_directory() . "/resources/data/" . $urlpieces[1];
		//echo $sourcefile;
		if (!file_exists($sourcefile))
		{
			error_log("nxs_storemedia_fromtheme; NOT FOUND; $sourcefile");
			$result = false;
		}
		else
		{
			//error_log("nxs_storemedia_fromtheme; STORING from $sourcefile to $destinationpath");
			
			// don't copy the file, just copy its contents!!
			// (keeping the other file atts in place)
			$content = file_get_contents($sourcefile);
			file_put_contents($destinationpath, $content);
			$result = true;
		}
	}
	else
	{		
		$result = false;
	}
	
	return $result;
}

function nxs_storemedia_remotehttpdownload($args)
{
	$url = $args["url"];
	$destinationpath = $args["destinationpath"];

	// default implementation
	$args = array();
	$args["url"] = $url;
	
	// get content
	$args["timeoutsecs"] = 1;
	$args["connectiontimeoutretriesleft"] = 6;
	$content = nxs_geturlcontents($args);
	
	// override content
	file_put_contents($destinationpath, $content);
	
	// assumed OK
	$result = true;
	return $result;
}

function nxs_getpagerowtemplatecontent($template)
{
	$templatefile = NXS_FRAMEWORKPATH . '/nexuscore/pagerows/templates/' . $template . "/pagetemplate.html";
	$newcontent = file_get_contents($templatefile);
	
	// each templates will likely contain variable name for identifying placeholders
	// note that the template is responsible for making the placeholderids unique;
	// if there's 2 placeholders to be used on the page, the template should use for example "A%nxs_random%" and "B%nxs_random%"
	$randomnummer = nxs_getrandomplaceholderid();
	$newcontent = str_replace("%nxs_random%", $randomnummer, $newcontent);
	
	return $newcontent;
}

// deserialize / unserialize an array of values, as serialized by for example the javascript call
// see function nxs_js_getescapeddictionary(input)
function nxs_urldecodearrayvalues($array)
{
	return nxs_urldecodearrayvalues_internal($array, 0);
}

function nxs_urldecodearrayvalues_internal($array, $depth)
{
	$result = array();
	
	// prevent endless loop
	$maxdepth = 10;	// increase if you need structures that are nested further
	if ($depth > $maxdepth) 
	{ 
		nxs_webmethod_return_nack("not sure, but suspecting a loop? increase the maxdepth if this is done on purpose ($maxdepth)"); 
	}
	
	if ($array == null)
	{
		//
	}
	else if (count($array) == 0)
	{
		//
	}
	else
	{
		foreach ($array as $key => $val)
		{
			if ($val != null)
			{
				if (is_array($val))
				{
					// recursive call
					$result[$key] = nxs_urldecodearrayvalues_internal($val, $depth + 1);
				}
				else
				{
					$result[$key] = utf8_urldecode($val);
					
					// nxs_js_getescapeddictionary() also escapes single quotes,
					// here we de-escape \' back into ' 
					$result[$key] = str_replace("\'","'", $result[$key]);
				}
			}
			else
			{
				$result[$key] = null;
			}
		}
	}
	
	// fix issue seen in TandenOnline; if client encodes json (originating from server),
	// the string passed to us isn't utf8, causing problems when decoding the string to php json objects
	// therefore before returning the result, we will convert the string's in the array to valid utf8 strings (if present)
	$result = nxs_array_toutf8string($result);
	
	return $result;
}

function utf8_urldecode($val) 
{
 	$result = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($val));
 	
 	/*
 	if (nxs_stringcontains($val, "%5C"))
	{
		$result = $val;
	}
	*/
	
	
	
 	
  return $result;
}

function nxs_urlencodearrayvalues($array)
{
	$result = array();
	
	if ($array == null)
	{
		//
	}
	else if (count($array) == 0)
	{
		//
	}
	else
	{
		foreach ($array as $key => $val)
		{
			if ($val != null)
			{
				$result[$key] = urlencode($val);
			}
			else
			{
				$result[$key] = null;
			}
		}
	}
	
	return $result;
}

function nxs_getplaceholdertemplate($postid, $placeholderid)
{
	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set (gpht)"); };
	if ($postid== "") { nxs_webmethod_return_nack("postid not set (gpht)"); };
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	if ($temp_array == "")
	{
		$result = "";
	}
	else
	{
		$result = $temp_array['type'];
	}
	
	return $result;
}

function nxs_cloneplaceholder($postid, $placeholderidtobecloned)
{
	if ($postid== "")
	{
		nxs_webmethod_return_nack("postid not set (cloneph)");
	}
	if ($placeholderidtobecloned == "")
	{
		nxs_webmethod_return_nack("placeholderidtobecloned not set");
	}
	
	$metadatatoclone = nxs_getwidgetmetadata($postid, $placeholderidtobecloned);
	if ($metadatatoclone["type"] == "")
	{
		nxs_webmethod_return_nack("source placeholderid ($placeholderidtobecloned) to be cloned not found on page ($postid)");
	}
	
	// TODO: add loop in case the placeholderid was already allocated (retry mechanism with max limit or retries)
	
	$placeholderid = rand(1000000, 9999999) . 'ID';

	// ensure the placeholderid is not in use
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	if (isset( $temp_array['type']))
	{
		nxs_webmethod_return_nack("unable to allocate unused ID, please retry");
	}
	
	// vrije id gevonden
	update_post_meta($postid, $metadatakey, nxs_get_backslashescaped($metadatatoclone));
		
	return $placeholderid;
}

function nxs_storebinarypoststructure($postid, $poststructure)
{
	$newpostcontents = nxs_getcontentsofpoststructure($postid, $poststructure);

	nxs_updatepoststructure($postid, $newpostcontents);

	//
	// na het updaten kunnen bepaalde velden zijn bijgewerkt, deze halen we direct weer op
	// 
	$the_post = get_page($postid);
	$post_modified = $the_post->post_modified;	// de nieuwe timestamp
	
	$result = array();
	$result["modified"] = $post_modified;
	
	return $result;
}

function nxs_getcontentsofpoststructure($postid, $poststructure)
{
	$content = "";
		
	foreach ($poststructure as $pagerow)
	{
		// pagerowtemplate is verplicht
		$pagerowtemplate = $pagerow["pagerowtemplate"];
		if (array_key_exists("pagerowid", $pagerow))
		{
			// pagerowid is already set
			$pagerowid = $pagerow["pagerowid"];
		}
		else
		{
			// no id (yet)
			$pagerowid = "";
		}
		
		$content .= "[nxspagerow pagerowid=\"" . $pagerowid . "\" pagerowtemplate=\"" . $pagerowtemplate . "\"]";
		$content .= $pagerow["content"];
		$content .= "[/nxspagerow]";
	}
	
	$content = str_replace("\r\n", "", $content);
	
	return $content;
}

function nxs_getrowindex_forpostidplaceholderid($postid, $placeholderid)
{
	$parsedpoststructure = nxs_parsepoststructure($postid);
	$result = nxs_getrowindex_for_placeholderid($parsedpoststructure, $placeholderid);
	return $result;
}

// NOTE; this is the rowindex, not the pagerowid! (rowindexes always start with 0,
// the pagerowid is the unique id of the row!
function nxs_getrowindex_for_placeholderid($parsedpoststructure, $placeholderid)
{
	$result = "nvt (" . $placeholderid . ")";
	foreach ($parsedpoststructure as $rowindex => $row)
	{		
		$outercontent = $row["outercontent"];
		if (nxs_stringcontains($outercontent, $placeholderid))
		{
			// gotcha
			$rowindex = $row["rowindex"];
			$result = $rowindex;
			break;
		}
	}
	return $result;
}

/*
*/

function nxs_getpagerowid_forpostidplaceholderid($postid, $placeholderid)
{
	$parsedpoststructure = nxs_parsepoststructure($postid);
	$result = nxs_getpagerowid_for_placeholderid($parsedpoststructure, $placeholderid);
	return $result;
}

// NOTE; this is the pagerowid (not the rowindex!)
function nxs_getpagerowid_for_placeholderid($parsedpoststructure, $placeholderid)
{
	$result = "notset";
	foreach ($parsedpoststructure as $rowindex => $row)
	{		
		$outercontent = $row["outercontent"];
		if (nxs_stringcontains($outercontent, $placeholderid))
		{
			// gotcha
			$result = $row["pagerowid"];
			break;
		}
	}
	return $result;
}

/*
*/

function nxs_parserowidfrompagerow($parsedrowfromstructure)
{
	return $parsedrowfromstructure["pagerowid"];
}

function nxs_getwidgetsmetadatainpost($postid)
{
	$filter = array();
	$filter["postid"] = $postid;
	return nxs_getwidgetsmetadatainpost_v2($filter);
}

// widgets metadata 
function nxs_getwidgetsmetadatainpost_v2($filter)
{
	$result = array();
	
	$postid = $filter["postid"];
	$widgettype = $filter["widgettype"];
	
	$rows = nxs_parsepoststructure($postid);
	$index = 0;
	foreach ($rows as $currentrow) 
	{
		$content = $currentrow["content"];
		$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
		foreach ($placeholderids as $placeholderid)
		{
			$placeholdermetadata = nxs_getwidgetmetadata($postid, $placeholderid);
			$shouldinclude = true;
			if ($widgettype != "")
			{
				if ($placeholdermetadata["type"] != $widgettype)
				{
					// wrong type; ignore!
					$shouldinclude = false;
				}
			}
			
			if ($shouldinclude)
			{
				$result[$placeholderid] = $placeholdermetadata;
			}
		}
	}
	
	return $result;
}

function nxs_getallpostids()
{
	$result = array();
	
	global $wpdb;

	// we do so for truly EACH post (not just post, pages, but also for entities created by third parties,
	// as these can use the nxsstructure too (for example WooCommerce 'product'). This saves development
	// time for plugins, and increases consistency of data for end-users
	$q = "
			select ID postid
			from $wpdb->posts
		";
		
	$dbresult = $wpdb->get_results($q, ARRAY_A );
	
	if (count($dbresult) > 0)
	{
		$cnt = 0;
		foreach ($dbresult as $dbrow)
		{	  		
			$result[] = $dbrow["postid"];
		}
	}
	
	return $result;
}

function nxs_getwidgetsmetadatainsite($filter)
{
	$result = array();
	
	$widgettype = $filter["widgettype"];
	$postids = nxs_getallpostids();
	foreach ($postids as $postid)
	{
		$subfilter = array
		(
			"postid" => $postid,
			"widgettype" => $widgettype,
		);
		$subresult = nxs_getwidgetsmetadatainpost_v2($subfilter);
		if (count($subresult) > 0)
		{
			$result[$postid] = $subresult;
		}
	}
	
	return $result;
}

function nxs_parsepoststructure($postid)
{
	// haal de contents op van de pagina voor postid
 	$content = nxs_getpoststructure($postid);

	// haal een array op van alle pagerows op de pagina
 	$regex_pattern = "/\[nxspagerow(.*)\](.*)\[\/nxspagerow\]/Us";
 	preg_match_all($regex_pattern,$content,$matches);

	$result = array();

	// loop over the pagerowtemplates as currently stored in the page
	foreach ($matches[1] as $rowindex => $pagerowtemplateidentification)
	{
		//
		//
		//
		$pagerowattributes = $matches[1][$rowindex];	// contains the attributes
		$content = $matches[2][$rowindex];	// contains the content
		$outercontent = $matches[0][$rowindex];		
		
		//
		// pagerowtemplate (required)
		//
		$prt_sub_regex_pattern = "/" . "pagerowtemplate=" . "[\'\"]" . "(.*)" . "[\'\"]" . "/" . "Us";
 		$prt_identificationsfound = preg_match($prt_sub_regex_pattern,$pagerowtemplateidentification,$prt_sub_matches);
 		// ensure row has pagerowtemplate attribute
 		if ($prt_identificationsfound != 1)
 		{
 			// not found?
			// incorrect page contents layout?
			nxs_webmethod_return_nack("incorrect page contents layout; no pagerowtemplate or multiple pagerowtemplates found?;" . $prt_identificationsfound);
	 	}
		$pagerowtemplate = $prt_sub_matches[1];

		//
		// pagerowid (optional)
		//
		$prid_sub_regex_pattern = "/" . "pagerowid=" . "[\'\"]" . "(.*)" . "[\'\"]" . "/" . "Us";
 		$prid_identificationsfound = preg_match($prid_sub_regex_pattern,$pagerowtemplateidentification,$prid_sub_matches);
 		
 		if ($prid_identificationsfound == 0)
 		{
 			$pagerowid = "";
 		}
 		else if ($prid_identificationsfound == 1)
 		{
 			$pagerowid = $prid_sub_matches[1];
 		}
 		else
 		{
 			nxs_webmethod_return_nack("incorrect page contents layout; multiple pagerowids found?"); 		
 		}
		
		$result[] = array
		(
			"rowindex" => $rowindex,
			"pagerowtemplate" => $pagerowtemplate,
			"pagerowid" => $pagerowid,	// could be empty
			"pagerowattributes" => $pagerowattributes,
			"content" => $content,
			"outercontent" => $outercontent,
		);
	}
		
	return $result;
}

function nxs_parsepagerow($pagerowcontent)
{
	// extract the placeholderid
	$sub_regex_pattern = "/" . "placeholderid=" . "[\'\"]" . "(.*)" . "[\'\"]" . "/" . "Us";
	$identificationsfound = preg_match($sub_regex_pattern,$pagerowcontent,$sub_matches);
	
	// ensure row has placeholderid attribute
	if ($identificationsfound == 1)
	{
		$result = $sub_matches[1];
	}
	else
	{
		$result = null;
	}
	
	return $result;
}

function nxs_parseplaceholderidsfrompagerow($pagerowcontent)
{
	//
	//
	//
	// haal een array op van alle placeholderids binnen de row
 	$regex_pattern = "/" . "placeholderid=" . "[\'\"]" . "(.*)" . "[\'\"]" . "/" . "Us";
 	preg_match_all($regex_pattern,$pagerowcontent,$matches);

	$result = array();

	// loop over the placeholderids as currently stored in the row
	foreach ($matches[1] as $placeholderindex => $placeholderid)
	{
		$result[] = $placeholderid;
	}
		
	return $result;
}

function nxs_getnxsposttype_by_wpposttype($posttype)
{
	$result = "";
	
	if ($posttype == "post" || $posttype == "page")
	{
		$result = "post";
	}
	else if ($posttype == "nxs_sidebar")
	{
		$result = "sidebar";
	}
	else if ($posttype == "nxs_footer")
	{
		$result = "footer";
	}
	else if ($posttype == "nxs_header")
	{
		$result = "header";
	}
	else if ($posttype == "nxs_menu")
	{
		$result = "menu";
	}
	else if ($posttype == "nxs_genericlist")
	{
		$result = "genericlist";
	}
	else if ($posttype == "nxs_admin")
	{
		$result = "admin";
	}
	else if ($posttype == "nxs_pagelet")
	{
		$result = "pagelet";
	}
	else if ($posttype == "nxs_subheader")
	{
		$result = "subheader";
	}
	else if ($posttype == "nxs_template")
	{
		$result = "template";
	}
	else if ($posttype == "nxs_subfooter")
	{
		$result = "subfooter";
	}
	else if ($posttype == "nxs_systemlog")
	{
		$result = "systemlog";
	}
	else if ($posttype == "nxs_busrulesset")
	{
		$result = "busrulesset";
	}
	else if ($posttype == "")
	{
		// dit is het geval bij de search form
		if (is_search())
		{
			$result = "searchresults";
		}
	}
	
	$tag = "nxs_getnxsposttype_for_" . $posttype;
	if ($result == "" && !has_filter($tag))
	{
		$result = "post";
		// nxs_webmethod_return_nack("no filter found for {$posttype} please add filter {$tag}");
	}

	// extensions can implement additional supported posttypes, or override existing ones
	$result = apply_filters("nxs_getnxsposttype_for_" . $posttype, $result);
	
	if ($result == "")
	{
		nxs_webmethod_return_nack("posttype not (yet?) supported; [$posttype] a");
	}
	
	return $result;
}

function nxs_getposttype_by_nxsposttype($nxsposttype)
{	
	if ($nxsposttype == "post")
	{
		$result = "post";
	}
	else if ($nxsposttype == "admin")
	{
		$result = "nxs_admin";
	}	
	else if ($nxsposttype == "menu")
	{
		$result = "nxs_menu";
	}
	else if ($nxsposttype == "genericlist")
	{
		$result = "nxs_genericlist";
	}
	else if ($nxsposttype == "sidebar")
	{
		$result = "nxs_sidebar";
	}
	else if ($nxsposttype == "footer")
	{
		$result = "nxs_footer";
	}
	else if ($nxsposttype == "header")
	{
		$result = "nxs_header";
	}
	else if ($nxsposttype == "pagelet")
	{
		$result = "nxs_pagelet";
	}
	else if ($nxsposttype == "subheader")
	{
		$result = "nxs_subheader";
	}
	else if ($nxsposttype == "templatepart")
	{
		$result = "nxs_templatepart";
	}
	else if ($nxsposttype == "subfooter")
	{
		$result = "nxs_subfooter";
	}
	else if ($nxsposttype == "settings")
	{
		$result = "nxs_settings";
	}	
	else if ($nxsposttype == "systemlog")
	{
		$result = "nxs_systemlog";
	}
	else if ($nxsposttype == "busrulesset")
	{
		$result = "nxs_busrulesset";
	}
	else
	{
		nxs_webmethod_return_nack("nxsposttype not (yet?) supported; [$nxsposttype] b");
	}
	
	return $result;
}

function nxs_dumpstacktrace()
{
	print_r(nxs_getstacktrace());
}

function nxs_getstacktrace()
{
	if (is_super_admin())
	{
		$result = debug_backtrace();
	}
	else
	{
		$result = array();
		$result["tip"] = "stacktrace suppressed; only available for admin users";
			
		/* 
		// uncomment these lines to debug issues
		if ($_REQUEST["a"] == "2")
		{
			$result = debug_backtrace();
		}
		else
		{
			$result = array();
			$result["tip"] = "stacktrace suppressed; only available for admin users";
		}
		*/
	}
	
	return $result;
}

// posttitle
function nxs_gettitle_for_postid($postid)
{
	$result = get_the_title($postid);
	return $result; 
}

// 2012 06 04; GJ; in some particular situation (unclear yet when exactly) the result cannot be json encoded
// erroring with 'Invalid UTF-8 sequence in range'.
// Solution appears to be to UTF encode the input
function nxs_array_toutf8string($result)
{
	foreach ($result as $resultkey => $resultvalue)
	{
		if (is_string($resultvalue))
		{
			if (!nxs_isutf8($resultvalue))
			{
				$result[$resultkey] = nxs_toutf8string($resultvalue);
			}

			// also fix the special character \u00a0 (no breaking space),
			// as this one also could result into issues
			$result[$resultkey] = preg_replace('~\x{00a0}~siu', ' ', $result[$resultkey]);   
		}
		else if (is_array($resultvalue))
		{
			$result[$resultkey] = nxs_array_toutf8string($resultvalue);
		}
		else
		{
			// leave as is...
		}
	}
	
	return $result;
}

if(!function_exists('mb_detect_order')) 
{
	
	function mb_detect_order($encoding_list)
	{
		$result = false;
		if (is_null($encoding_list))
		{
			$result = array("ASCII", "UTF-8");	
		}
	}
}

// 17 aug 2013; workaround if mb_detect_encoding is not available (milos)
// kudos to http://php.net/manual/de/function.mb-detect-encoding.php
if(!function_exists('mb_detect_encoding')) 
{ 
	function mb_detect_encoding($string, $enc=null) 
	{ 	    
    static $list = array('utf-8', 'iso-8859-1', 'windows-1251');
    
    foreach ($list as $item) {
        $sample = iconv($item, $item, $string);
        if (md5($sample) == md5($string)) { 
            if ($enc == $item) { return true; }    else { return $item; } 
        }
    }
    return null;
	}
}

// 17 aug 2013; workaround if mb_convert_encoding is not available (milos)
// kudos to http://php.net/manual/de/function.mb-detect-encoding.php
if(!function_exists('mb_convert_encoding')) 
{ 
	function mb_convert_encoding($string, $target_encoding, $source_encoding) 
	{ 
		if ($source_encoding == "UTF-8" && $target_encoding == "HTML-ENTITIES")
		{
			// 2016 05 27; found issue while rendering html widgets in WC; resulting in blank output
			// to avoid error; leave string as is for this particular convert task
			// Notice: iconv(): Wrong charset, conversion from `UTF-8' to `HTML-ENTITIES'
			// resulting in string that only have a space as output
		}
		else
		{
    	$string = iconv($source_encoding, $target_encoding, $string);
    }
    
    return $string; 
	}
}

function nxs_isutf8($string) 
{
  if (function_exists("mb_check_encoding")) 
  {
    return mb_check_encoding($string, 'UTF8');
  }
  
  return (bool)preg_match('//u', serialize($string));
}

function nxs_toutf8string($in_str)
{
	$in_str_v2=mb_convert_encoding($in_str,"UTF-8","auto");
	if ($in_str_v2 === false)
	{
		$in_str_v2 = $in_str;
	}
	
	$cur_encoding = mb_detect_encoding($in_str_v2) ; 
  if($cur_encoding == "UTF-8" && nxs_isutf8($in_str_v2)) 
  {
  	$result = $in_str_v2; 
  }
  else 
  {
    $result = utf8_encode($in_str_v2); 
  }
    
  return $result;
}

function nxs_get_main_titles_on_page($postid)
{
	$result = array();

	// allereerst de naam van de pagina zelf	
	$item = nxs_toutf8string(strip_tags(nxs_gettitle_for_postid($postid)));
	if ($item != "")
	{
		if (!in_array($item, $result))
		{
			$result[] = $item;
		}
	}
	
	// parse de pagina
	$parsedpoststructure = nxs_parsepoststructure($postid);
	// loop over each row, get placeholderid,
	$rowindex = 0;
	foreach ($parsedpoststructure as $pagerow)
	{
		$content = $pagerow["content"];
		$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
		foreach ($placeholderids as $placeholderid)
		{
			$placeholdermetadata = nxs_getwidgetmetadata($postid, $placeholderid);
			// per placeholderid get all meta data
			// find in meta data "title" and / or "koptekst"
			$item = nxs_toutf8string(strip_tags($placeholdermetadata["title"]));
			if ($item != "")
			{
				if (!in_array($item, $result))
				{
					$result[] = $item;
				}
			}
			$item = nxs_toutf8string(strip_tags($placeholdermetadata["titel"]));
			if ($item != "")
			{
				if (!in_array($item, $result))
				{
					$result[] = $item;
				}
			}
			$item = nxs_toutf8string(strip_tags($placeholdermetadata["head"]));
			if ($item != "")
			{
				if (!in_array($item, $result))
				{
					$result[] = $item;
				}
			}
			$item = nxs_toutf8string(strip_tags($placeholdermetadata["koptekst"]));
			if ($item != "")
			{
				if (!in_array($item, $result))
				{
					$result[] = $item;
				}
			}
			// check for other attributes too, if that's needed in the future
		}
	}
	
	if (count($result) == 0)
	{
		$result[] = "Geen passende titel gevonden";
	}	
	
	return $result;
}

function nxs_clonepost($postid)
{
	if (!nxs_postexistsbyid($postid))
	{
		echo "nothing to clone? $postid";
		die();
	}
	
	//$sessionid = nxs_generaterandomstring(5);
	
	// grab the basic lookups, like the posttype and the globalid, etc.
	$posttype = get_post_type($postid);
	$nxsposttype = nxs_getnxsposttype_by_postid($postid);
	$nxssubposttype = nxs_get_nxssubposttype($postid);
	$globalid = nxs_get_globalid($postid, true);
	$title = nxs_gettitle_for_postid($postid);
	$slug = nxs_getslug_for_postid($postid);
	$nxs_semanticlayout = get_post_meta($postid, 'nxs_semanticlayout', true);
	$structure = nxs_parsepoststructure($postid);
	
	$destinationpostid = "";
	$destinationglobalid = "";
	
	if (in_array($posttype, array("nxs_genericlist")))
	{
		$newpost_args = array();
		$newpost_args["slug"] = $slug;	// sessionid toevoegen?
		$newpost_args["titel"] = $title;
		$newpost_args["wpposttype"] = $posttype;
		$newpost_args["nxsposttype"] = $nxsposttype;
		$newpost_args["nxssubposttype"] = $nxssubposttype;
		$newpost_args["postwizard"] = "skip";
		//$newpost_args["globalid"] = $currentpostglobalid;
		//$newpost_args["postmetas"] = $postmetas;
		$response = nxs_addnewarticle($newpost_args);
		$destinationpostid = $response["postid"];
		$destinationglobalid = $response["globalid"];
		
		// replicate the structure of the post
		nxs_storebinarypoststructure($destinationpostid, $structure);
		
		// replicate the data per row
		$rowindex = 0;
		foreach ($structure as $pagerow)
		{
			// ---------------- ROW META
			
			// replicate the metadata of the row
			$pagerowid = nxs_parserowidfrompagerow($pagerow);
			if (isset($pagerowid))
			{
				// get source meta
				$rowmetadata = nxs_getpagerowmetadata($postid, $pagerowid);
				// store destination meta
				nxs_overridepagerowmetadata($destinationpostid, $pagerowid, $rowmetadata);
			}
			
			// ---------------- WIDGET META
			
			// replicate the metadata of the widgets in the row
			$filter = array("postid" => $postid);
			$widgetsmetadata = nxs_getwidgetsmetadatainpost_v2($filter);
			
			foreach ($widgetsmetadata as $placeholderid => $widgetmetadata)
			{
				nxs_overridewidgetmetadata($destinationpostid, $placeholderid, $widgetmetadata);
			}
		}
	}
	else
	{
		echo "to be implemented; $posttype";
		die();
	}
	
	$result = array
	(
		"destinationpostid" => $destinationpostid,
		"destinationglobalid" => $destinationglobalid,
	);
	
	return $result;
}

// will update the metadata of the widget postid, placeholderid,
// for the fields that reference other widgets, by cloning the existing
// referencing posts, and then updating the meta fields of the widget itself
// this is a function that is being used AFTER the pasting of a widget, row or
// entire page is has cloned al fields by value, to ensure that referenced
// fields are cloned too (instead of both the source and destination using
// the same generic lists for example)
function nxs_clonereferencedfieldsforwidget($postid, $placeholderid)
{
	// grab the existing widgetmetadata of this widget
	$metadata = nxs_getwidgetmetadata($postid, $placeholderid);
	$placeholdertemplate = $metadata["type"];
	
	// clone referenced entities
	// for debugging this could be done based upon the IP address
	//$ip = $_SERVER['REMOTE_ADDR'];
	//$shouldclonereferencedgenericlists = ($ip == "83.162.43.67");
	$shouldclonereferencedgenericlists = true;				
	
	$isdirty = false;
	
	if ($shouldclonereferencedgenericlists)
	{
		// loop over properties of the widgettype
		// if one of the properties represents a genericlist,
		// then clone that genericlist post,
		// give it a new unique globalid and postid
		// and update the widgetmeta of "this" widget we are pasting,
		// such that it will point to that cloned entity
		nxs_requirewidget($placeholdertemplate);
		$widget = $placeholdertemplate;
		$sheet = "home";
		$functionnametoinvoke = 'nxs_widgets_' . $widget . '_' . $sheet . '_getoptions';
		if (function_exists($functionnametoinvoke))
		{
			// todo: 20161007; a better solution would be to loop over the properties through reflection
			// instead of looking for the hardcoded "items_genericlistid" fieldname,
			// but for now this should be enough
			if ($metadata["items_genericlistid"] != "" && $metadata["items_genericlistid"] != "0")
			{
				//error_log("clone generic lists = genericlist set");
				
				// we found a referenced post; clone that post first
				$tobeclonedpostid = $metadata["items_genericlistid"];
				//error_log("clone generic lists = cloning $tobeclonedpostid");
				$clonedresult = nxs_clonepost($tobeclonedpostid);
				//error_log("clone generic lists = clone finished " . json_encode($clonedresult));
				// update the metadata with the cloned result
				$metadata["items_genericlistid"] = $clonedresult["destinationpostid"];
				$metadata["items_genericlistid_globalid"] = $clonedresult["destinationglobalid"];
				
				$isdirty = true;
			}
		}
		else
		{
			// for old style widgets we don't support this
		}
	}
	
	if ($isdirty)
	{
		// update the widgetmetadata
		nxs_overridewidgetmetadata($postid, $placeholderid, $metadata);
	}
}

function nxs_replicatepoststructure($replicatemetadata)
{
	extract($replicatemetadata);
	
	if ($destinationpostid == "") { nxs_webmethod_return_nack("destinationpostid empty? (shp)"); }
	if ($destinationpostid == "0") { nxs_webmethod_return_nack("destinationpostid empty? (shp)"); }
	if ($sourcepostid == "") { nxs_webmethod_return_nack("sourcepostid empty? (shp)"); }
	if ($sourcepostid == "0") { nxs_webmethod_return_nack("sourcepostid empty? (shp)"); }
	if ($sourcepostid == $destinationpostid) { nxs_webmethod_return_nack("sourcepostid is the same as the destination postid (shp)"); }
	
	// replicate the data structure and metafields from source to destination
	$structure = nxs_parsepoststructure($sourcepostid);
	nxs_storebinarypoststructure($destinationpostid, $structure);
	
	// replicate the data per row
	$rowindex = 0;
	foreach ($structure as $pagerow)
	{
		// ---------------- ROW META
		
		// replicate the metadata of the row
		$pagerowid = nxs_parserowidfrompagerow($pagerow);
		if (isset($pagerowid))
		{
			// get source meta
			$metadata = nxs_getpagerowmetadata($sourcepostid, $pagerowid);
			// store destination meta
			nxs_overridepagerowmetadata($destinationpostid, $pagerowid, $metadata);
		}
		
		// ---------------- WIDGET META
		
		// replicate the metadata of the widgets in the row
		$content = $pagerow["content"];
		$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
		foreach ($placeholderids as $placeholderid)
		{
			// get source metadata
			$metadata = nxs_getwidgetmetadata($sourcepostid, $placeholderid);
			// store destination metadata
			nxs_overridewidgetmetadata($destinationpostid, $placeholderid, $metadata);
		}
	}
	
	// huray!
	$result = array();
	return $result;
}

// first_image get first image
function nxs_get_key_imageid_in_post($postid)
{
	$result = 0;
	$imageids = nxs_get_images_in_post($postid);
	foreach ($imageids as $currentimageid)
	{
		$result = $currentimageid;
		break;
	}
	return $result;
}

// gets an improved version of the image, based on the themeversion
function nxs_img_getimageurlthemeversion($result)
{
	if (function_exists("nxs_theme_getmeta"))
	{
		$meta = nxs_theme_getmeta();
		$version = $meta["version"] . nxs_img_getstamp();
		$decimals = preg_replace("/[^0-9]/","",$version);
		// we use the quality parameter, since that's the only one 
		// to trick Photon
		$result = nxs_addqueryparametertourl_v2($result, "quality", "100." . $decimals, true, true);
	}
	return $result;
}

function nxs_img_getstamp()
{
	global $nxs_gl_img_stamp;
	if ($nxs_gl_img_stamp == "")
	{
		$nxs_gl_img_stamp = get_option("nxs_img_stamp");
		if ($nxs_gl_img_stamp == "")
		{
			$nxs_gl_img_stamp = "0";
		}
	}
	return $nxs_gl_img_stamp;
}

function nxs_get_images_in_post($postid)
{
	$result = array();
	
	// add featured image (if set)
	$featuredimageid = get_post_thumbnail_id($postid);
	if ($featuredimageid != "" && $featuredimageid != 0)
	{
		if (!in_array($featuredimageid, $result))
		{
			$result[] = $featuredimageid;
		}
	}
	
	// parse de pagina
	$parsedpoststructure = nxs_parsepoststructure($postid);
	// loop over each row, get placeholderid,
	$rowindex = 0;
	foreach ($parsedpoststructure as $pagerow)
	{
		$content = $pagerow["content"];		
		$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
		foreach ($placeholderids as $placeholderid)
		{
			$placeholdermetadata = nxs_getwidgetmetadata($postid, $placeholderid);
			
			$item = strip_tags($placeholdermetadata["thumbid"]);
			if ($item != "")
			{
				if (!in_array($item, $result))
				{
					$result[] = $item;
				}
			}			
			$item = strip_tags($placeholdermetadata["image_imageid"]);
			if ($item != "")
			{
				if (!in_array($item, $result))
				{
					$result[] = $item;
				}
			}
			
			// check for other attributes too, if that's needed in the future
		}
		$rowindex++;
	}
	
	return $result;
}

function nxs_get_images_in_site()
{
	global $wpdb;

	$q = "
				select ID postid
				from $wpdb->posts
			";
	
	$origpostids = $wpdb->get_results($q, ARRAY_A);

	$imageidsinsite = array();
	// LOOP; all nexus structs, try to find
	foreach ($origpostids as $origrow)
	{
		$origpostid = $origrow["postid"];
		$imageidsinpost = nxs_get_images_in_post($origpostid);
		$imageidsinsite = array_merge($imageidsinsite, $imageidsinpost);
		//echo "<br />postid: " . $origpostid . " uses images: ";
		//var_dump($imageidsinpost);
	}
	$result = array_unique($imageidsinsite);
	
	return $result;
}

function nxs_get_advanced_strippedtags($data_str, $allowable_tags, $allowable_atts)
{
	// $allowable_tags = '<p><a><img><ul><ol><li><table><thead><tbody><tr><th><td>';
	// $allowable_atts = array('href','src','alt');
	
	// strip collector
	$strip_arr = array();
	
	// load XHTML with SimpleXML
	$data_sxml = simplexml_load_string('<root>'. $data_str .'</root>', 'SimpleXMLElement', LIBXML_NOERROR | LIBXML_NOXMLDECL);
	
	if ($data_sxml ) 
	{
    // loop all elements with an attribute
    foreach ($data_sxml->xpath('descendant::*[@*]') as $tag) 
    {
      // loop attributes
      foreach ($tag->attributes() as $name=>$value) 
      {
        // check for allowable attributes
        if (!in_array($name, $allowable_atts)) 
        {
          // set attribute value to empty string
          $tag->attributes()->$name = '';
          // collect attribute patterns to be stripped
          $strip_arr[$name] = '/ '. $name .'=""/';
        }
      }
    }
	}
	else
	{
		// als originele string een \' bevat, dan zal false worden teruggegeven
		nxs_webmethod_return_nack("unable to load string as simplexml (tip: vergeten iets te unescapen?)..." . $data_str);
	}
	
	// strip unallowed attributes and root tag
	$data_str = strip_tags(preg_replace($strip_arr,array(''),$data_sxml->asXML()), $allowable_tags);
	return $data_str;
}

function nxs_get_text_blocks_on_page($postid)
{
	return nxs_get_text_blocks_on_page_v2($postid, "...");
}

function nxs_get_text_blocks_on_page_v2($postid, $emptyplaceholder)
{
	return nxs_get_text_blocks_on_page_v3($postid, $emptyplaceholder, "before");

}

function nxs_get_text_blocks_on_page_v3($postid, $emptyplaceholder, $wpcontentrenderbehaviour)
{
	$result = array();

	if ($wpcontentrenderbehaviour == "none")
	{
		//
	}
	else if ($wpcontentrenderbehaviour == "before")
	{
		// the wp content
		$text = do_shortcode(nxs_getwpcontent_for_postid($postid));
		// 20151009 - disabled nxs_toutf8string; it produces garbled output on the blog widgets/
		// on one of Kacems sites
		//$item = nxs_toutf8string(strip_tags($text));	
		$item = strip_tags($text);
		
		if ($item != "")
		{
			if (!in_array($item, $result))
			{
				$result[] = $item;
			}
		}
	}
	else
	{
		// unknown
	}
	
	// parse de pagina
	$parsedpoststructure = nxs_parsepoststructure($postid);

	// loop over each row, get placeholderid,
	foreach ($parsedpoststructure as $pagerow)
	{
		$content = $pagerow["content"];		
		$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
		foreach ($placeholderids as $placeholderid)
		{
			$placeholdermetadata = nxs_getwidgetmetadata($postid, $placeholderid);

			// per placeholderid get all meta data
			$text = $placeholdermetadata["text"];
			// the <br /> tag should be replaced with a space rather than being removed
			// the <br> tag should also be replaced with a space rather than being removed
			$text = str_ireplace("<br />", " ", $text);
			$text = str_ireplace("<br>", " ", $text);
			
			$stripped = strip_tags($text);
			$item = $stripped;
			
			// apply lookup tables
			$temp  = array("text" => $item);
			$temp = nxs_filter_translatelookup($temp, array("text"));
			$item = $temp["text"];
			
			if ($item != "")
			{
				if (!in_array($item, $result))
				{
					$result[] = $item;
				}
			}
			// check for other attributes too, if that's needed in the future
		}
	}
	
	if (count($result) == 0)
	{
		$result[] = $emptyplaceholder;
	}
	
	return $result;
}

function nxs_getwpposttype($postid)
{
	$postdata = get_page($postid);
	if ($postdata == null)
	{
		nxs_webmethod_return_nack("nxs_getwpposttype; postid not found;" . $postid);
	}
	$result = $postdata->post_type;
	return $result;
}

function nxs_converttopage($postid)
{
	$resultaat = array();
	
	set_post_type($postid, 'page');
	
	return $resultaat;
}

function nxs_converttopost($postid)
{
	$result = array();
	
	$iscurrentpagethehomepage = nxs_ishomepage($postid);
	if ($iscurrentpagethehomepage)
	{
		// leave as-is
		echo "error; homepage cannot be marked as post; please first assign the homepage to another post";
		return;
	}
	
	set_post_type($postid, 'post');
	
	return $result;
}

// TODO: check if following method is still in use?
function nxs_updatewpposttype($postid, $wpoldposttype, $wpnewposttype, $shouldflushrewriterules)
{
	$result = array();
	
	global $wpdb;
	$query = "UPDATE " . $wpdb->posts . " SET post_type = '" . $wpnewposttype . "' WHERE id=" . $postid; // . " and post_type = '" . $wpoldposttype . "'";
	$queryresult = $wpdb->query($query);
	$queryerror = $wpdb->print_error();
	
	if ($queryresult == 1)
	{
		// 1 row updated; OK!
		$result["result"] = "OK";
		$result["qr"] = $queryresult;
		$result["qe"] = $queryerror;
	} 
	else 
	{
	  // failed
		$result["result"] = "NACK";
	}
	
	if ($shouldflushrewriterules)
	{
		global $wp_rewrite;
		//Important! Rewrites permalinks for post/page files 
		$wp_rewrite->flush_rules();
	}
	
	return $result;
}

function nxs_getnxsposttype_by_postid($postid)
{
	$wpposttype = nxs_getwpposttype($postid);
	if ($wpposttype == "")
	{
		nxs_webmethod_return_nack("unknown wp posttype");
	}
	else
	{
		$result = nxs_getnxsposttype_by_wpposttype($wpposttype);
	}
	return $result;
}

function nxs_getslug_for_postid($postid)
{
	$postdata = get_post($postid);
	if (isset($postdata))
	{
		$result = $postdata->post_name;
	}
	else
	{
		$result = "";
	}
	return $result; 
}

function nxs_getwpcontent_for_postid($postid)
{
	$postdata = get_post($postid);
	$title = $postdata->post_content;
	return $title; 
}

function nxs_setwpcontent_for_postid($postid, $wpcontent)
{
	nxs_disabledwprevisions();
	
  $my_post = array();
  $my_post['ID'] = $postid;
  $my_post['post_content'] = $wpcontent;

	// Update the post into the database
  wp_update_post( $my_post );
}

function nxs_getwpcategoryids_for_postid($postid)
{
	$result = "";
	
	$categoryids = wp_get_post_categories($postid);
	foreach ($categoryids as $categoryid)
	{
		$result .= "[" . $categoryid . "]";
	}
	
	return $result;
}

function nxs_getpostid_for_title_and_nxstype($title, $nxsposttype)
{
	$posttype = nxs_getposttype_by_nxsposttype($nxsposttype);
	$post = get_page_by_title($title, "OBJECT", $posttype);
	return $post->ID;
}

function nxs_getpostid_for_title_and_wpposttype($title, $wpposttype)
{
	$post = get_page_by_title($title, "OBJECT", $wpposttype);
	return $post->ID;	
}

function nxs_htmlescape($input)
{
	$result = htmlentities($input);
	$result = str_replace("'","&#039;", $result);
		
	return $result;
}

function nxs_render_html_escape_gtlt($input)
{
	//$result = htmlentities($input);
	$result = $input;

	$result = str_replace("'","&#039;", $result);
	$result = str_replace("\"","&quot;", $result);
	$result = str_replace("<","&lt;", $result);
	$result = str_replace(">","&gt;", $result);

	return $result;
}

function nxs_render_html_escape_singlequote($input)
{
	$result = $input;
	$result = str_replace("'","&#039;", $result);
		
	return $result;
}

function nxs_render_html_escape_doublequote($input)
{
	$result = $input;
	$result = str_replace("\"","&quot;", $result);
		
	return $result;
}

function nxs_htmlunescape($input)
{	
	return $input;
}

// homeurl home_url homepage_url homepageurl gethome get_home site home site_home site_url homepage
function nxs_geturl_home()
{
	$url = get_bloginfo('url') . "/";
	return $url; 
}

function nxs_getsiteslug()
{
	$result = strtolower(nxs_geturl_home());
	$result = str_replace("http://", "", $result);
	$result = str_replace("https://", "", $result);
	$result = str_replace("/", "-", $result);
	$result = str_replace(".", "-", $result);
	// its easily possible multiple dashes are found next to one another; http://www.xyz.com would become http---www-xyx.com
	$result = str_replace("--", "-", $result);
	$result = str_replace("--", "-", $result);
	$result = str_replace("--", "-", $result);
	$result = str_replace("--", "-", $result);
}

function nxs_getsites()
{
	$result = array();
	
	global $wpdb;
  $blogs = $wpdb->get_results("SELECT blog_id, registered FROM $wpdb->blogs");
  foreach ($blogs as $currentblog)
  {
  	$blog_id = $currentblog->blog_id;
  	$url = get_site_url($blog_id);
  	$result[$blog_id] = $url;
  }
  
  return $result;
}

// kudos to http://stackoverflow.com/questions/3835636/php-replace-last-occurence-of-a-string-in-a-string
function nxs_str_lastreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

// todo: add in mem caching go to speed things up?
// url get posturl getposturl geturlforpost
function nxs_geturl_for_postid($postid)
{
	if ($postid == null)
	{
		$result = "";
	}
	else if ($postid == 0)
	{
		$result = "";
	}
	else if ($postid == "")
	{
		$result = "";
	}
	else if (nxs_ishomepage($postid))
	{
		$result = nxs_geturl_home();
	}
	else
	{
		$result = get_permalink($postid);
	}
	return $result; 
}

// geturlforglobalid (convenience function)
function nxs_geturl_for_globalid($globalid)
{
	$postid = nxs_get_postidaccordingtoglobalid($globalid);
	$result = nxs_geturl_for_postid($postid);	
	return $result;
}

function nxs_ensure_validsitemeta()
{
	// following line will crash if 0 or multiple sitemeta's exist
	$sitemeta = nxs_getsitemeta();
}

function nxs_getsitemeta()
{
	$nackwhenerror = true;
	return nxs_getsitemeta_internal($nackwhenerror);
}

function nxs_hassitemeta()
{
	$nackwhenerror = false;
	$tempsitemeta = nxs_getsitemeta_internal($nackwhenerror);
	if (count($tempsitemeta) == 0)
	{
		$result = false;
	}
	else
	{
		$result = true;
	}
	
	return $result;
}

function nxs_sitemeta_clearcache()
{
	global $nxs_gl_cache_sitemeta;
	$nxs_gl_cache_sitemeta = null;
	
	global $nxs_gl_cache_postmeta;
	$nxs_gl_cache_postmeta = null;
}

function nxs_getsitemeta_internal($nackwhenerror)
{
	global $nxs_gl_cache_sitemeta;
	if (!isset($nxs_gl_cache_sitemeta))
	{
		$postids = nxs_get_postidsaccordingtoglobalid("activesitesettings");
		if (count($postids) == 0)
		{
			if ($nackwhenerror)
			{
				error_log("no active site settings");
				$st = nxs_getstacktrace();
				$textst = json_encode($st);
				error_log("stack:" . $textst);
				
				if (nxs_isnxswebservicerequest())
				{
					nxs_webmethod_return_nack("tried to retrieve site settings, while no active site settings were found (webmethod)");
				}
				
				nxs_saveobclean();
 				
				if (is_user_logged_in())
				{
					$backendurl = get_admin_url();
 				}
 				else
 				{
 					$backendurl = wp_login_url();
 				}
 				// add a reason
				$backendurl = nxs_addqueryparametertourl_v2($backendurl, "nxstrigger", "noactivesitesettings", true, true);
 				wp_redirect($backendurl);
 				
 				//echo "This theme is not yet initialized. Click <a href='" . $backendurl . "'>here</a> to go to the backend";
 				die();
 				
				nxs_webmethod_return_nack("no active site settings not found?! (a)");
			}
			
			$result = array();
		}
		else 
		{
			if (count($postids) > 1)
			{
				// kudos to http://shibashake.com/wordpress-theme/wordpress-page-redirect
				add_filter('wp_redirect', 'nxs_redirectafterimport', 10, 2);
			}
			// store site settings as pagemeta of specific postid
			$postid = $postids[0];
			$result = nxs_get_postmeta($postid);
			
			// allow plugins to tune the result
			// (for example the stans plugin will post-process the 
			// output to set the colors)
			$result = apply_filters("nxs_f_getsitemeta", $result);
			
			$nxs_gl_cache_sitemeta = $result;
		}
	}
	else
	{
		$result = $nxs_gl_cache_sitemeta;
	}
	
	return $result;
}

function nxs_redirectafterimport($location, $status)
{
	// this is allowed only if the system will perform a sanity check; the sanity check
	// will fix tis problem
	
	if ($_REQUEST["redirectedtohomeafterimport"] == "true")
	{
		nxs_webmethod_return_nack("found multiple activesite settings, while not redirecting after import?! (if you just (re)imported data you will see this message only 1x)");
	}
	else
	{
		// ok
		$location = get_home_url();
		$location = nxs_addqueryparametertourl_v2($location, "redirectedtohomeafterimport", "true", true, true);
	}
	return $location;
}

function nxs_mergesitemeta($modifiedmetadata)
{
	nxs_mergesitemeta_internal($modifiedmetadata, true);
}

// turn off sanitycheck to update all activesitesettings, if multiple
// ones are found (only the case when user imports 1-click-content
// while the current site already has an activesitesettings itself)
function nxs_mergesitemeta_internal($modifiedmetadata, $performsanitycheck)
{
	$postids = nxs_get_postidsaccordingtoglobalid("activesitesettings");
	if ($performsanitycheck)
	{
		if (count($postids) != 1)
		{
			nxs_webmethod_return_nack("no or multiple active site settings not found?! (C)");
		}
	}

	// note that its theoretically possible to have multiple postids that match
	// this is the case when a user switches themes, in that case that $performsanitycheck is turned off
	foreach ($postids as $postid)
	{
		// store site settings as pagemeta of specific postid
		nxs_merge_postmeta($postid, $modifiedmetadata);
	}
	// very important step; wipe the cache
	
	nxs_sitemeta_clearcache();
}


// turn off sanitycheck to update all activesitesettings, if multiple
// ones are found (only the case when user imports 1-click-content
// while the current site already has an activesitesettings itself)
function nxs_wipe_sitemetakey_internal($keytoberemoved, $performsanitycheck)
{
	$postids = nxs_get_postidsaccordingtoglobalid("activesitesettings");
	if ($performsanitycheck)
	{
		if (count($postids) != 1)
		{
			nxs_webmethod_return_nack("no or multiple active site settings not found?! (C)");
		}
	}

	// note that its theoretically possible to have multiple postids that match
	// this is the case when a user switches themes, in that case that $performsanitycheck is turned off
	foreach ($postids as $postid)
	{
		// store site settings as pagemeta of specific postid
		nxs_wipe_postmetakey($postid, $keytoberemoved);
	}
	
	// very important step; wipe the cache
	nxs_sitemeta_clearcache();
}

// turn off sanitycheck to update all activesitesettings, if multiple
// ones are found (only the case when user imports 1-click-content
// while the current site already has an activesitesettings itself)
function nxs_wipe_sitemetakeys_internal($keystoberemoved, $performsanitycheck)
{
	$postids = nxs_get_postidsaccordingtoglobalid("activesitesettings");
	if ($performsanitycheck)
	{
		if (count($postids) != 1)
		{
			nxs_webmethod_return_nack("no or multiple active site settings not found?! (C)");
		}
	}

	// note that its theoretically possible to have multiple postids that match
	// this is the case when a user switches themes, in that case that $performsanitycheck is turned off
	foreach ($postids as $postid)
	{
		// store site settings as pagemeta of specific postid
		nxs_wipe_postmetakeys($postid, $keystoberemoved);
	}
	// very important step; wipe the cache
	nxs_sitemeta_clearcache();
}

//called after category is edited
function nxs_dataconsistency_after_edited_terms() {
	nxs_set_dataconsistencyvalidationrequired();
}

function nxs_dataconsistency_notify_data_inconsistent() {
	$url = admin_url('admin.php?page=nxs_data_verification_page_content');
	echo '<div class="error">
	    <p>
	    	Data verification required.
	    	<br />
	    	<A HREF="'.$url.'">Click here to verify the data of the website</A>
	    	
	    </p>
	  </div>';
}

function nxs_set_dataconsistencyvalidationrequired()
{
	// we gebruiken hiervoor met opzet geen sitemetadata maar de "option"
	// aangezien met deze "vlag" de de consistentheid van de sitemetadata juist ter 
	// discussie staat
	$metadatakey = 'nxs_dataconsistencyvalidationrequired';
	
	update_option($metadatakey, "true");
	do_action('nxs_dataconsistency_validationchanged');
}

function nxs_set_dataconsistencyvalidationnolongerrequired()
{
	// we gebruiken hiervoor met opzet geen sitemetadata,
	// aangezien de sitemetadata incorrect kan zijn
	$metadatakey = 'nxs_dataconsistencyvalidationrequired';
	
	update_option($metadatakey, "");
	do_action('nxs_dataconsistency_validationchanged');	
}

function nxs_isdataconsistencyvalidationrequired()
{
	$result = false;
	
	if (nxs_isnxswebservicerequest())
	{
		// while activating the theme, we don't want the data consistency check
		$result = false;
	}
	else
	{
		// we gebruiken hiervoor met opzet geen sitemetadata,
		// aangezien de sitemetadata incorrect kan zijn
		$metadatakey = 'nxs_dataconsistencyvalidationrequired';
	
		$result = get_option($metadatakey) == "true";
	}
	
	return $result;
}

function nxs_updatepagetemplate($args)
{
	extract($args);
	
 	if ($postid == "") { nxs_webmethod_return_nack("postid empty? (uphd)"); }
 	if ($pagetemplate == "") { nxs_webmethod_return_nack("pagetemplate empty?"); }

	// ensure it exists
	nxs_requirepagetemplate($pagetemplate);
 	
 	//
 	$articlesubtype = $pagetemplate;
 	
 	// store as taxonomy
	$result = wp_set_object_terms(strval($postid), $articlesubtype, "nxs_tax_subposttype");
	
	return $result;
}

function nxs_set_nxssubposttype($postid, $nxssubposttype)
{
	$result = wp_set_object_terms(strval($postid), $nxssubposttype, "nxs_tax_subposttype");
	if (is_wp_error($result)) 
	{
		$msg = $result->get_error_message();
		error_log("nxs_set_nxssubposttype invoked with $postid $nxssubposttype error result $msg");
	}
	else
	{
		//error_log("nxs_set_nxssubposttype; result: $result");
	}
	return $result;
}

// note; only invoke this function AFTER the nxs_create_post_types_and_taxonomies() function 
// of the framework is invoked, or else the taxonomy won't be available,
// meaning it will return "nxserr" error messages!
function nxs_get_nxssubposttype($postid)
{
	if ($postid == "")
	{
		echo "postid is niet geset? (subpt a)";
		return "postid is niet geset? (subpt) b";
	}
	
	$terms = wp_get_object_terms(strval($postid), 'nxs_tax_subposttype');
	
	if(!empty($terms))
	{
		if(!is_wp_error($terms))
		{
			if (count($terms) == 1)
			{
				$term = $terms[0];
				$result = $term->name;
				
				// fix; on rare installations system returns capitalized first letters, "Gallery" instead of "gallery",
				// messing up the behaviour. Fix is to always lowercase the result

				$result = strtolower($result); 
			}
			else
			{
				// unexpected; we found 0, or multiple taxonomies?
				error_log("nxs_get_nxssubposttype for $postid nxserr(1)");
				$result = false;
			}
		}
		else
		{
			error_log("nxs_get_nxssubposttype for $postid nxserr(2)");
			$result = false;
		}		
	}
	else
	{
		error_log("nxs_get_nxssubposttype for $postid nxserr(3)");
		$result = false;
	}
	
	return $result;
}

// rather complicated matter; if user stored a string with a backslash,
// the backslash was removed. We can't use wp_slash(), as this one will
// add a backslash to single quotes too. The solution appears to be this
// function; it will first remove backslashes in front of single and double quotes,
// then wp_slash it and return the result
// fix; 20140307; backslashes are removed in anything we stored, unless
// double-backslashed, see: 
// - http://codex.wordpress.org/Function_Reference/update_post_meta
// - https://codex.wordpress.org/Function_Reference/wp_slash
function nxs_get_backslashescaped($arrayorstring)
{
	if (is_array($arrayorstring))
	{
		$result = nxs_get_backslashescaped_internal($arrayorstring, 0);
	}
	else
	{
		$result = nxs_get_backslashescaped_string($arrayorstring);
	}
	
	return $result;
}

function nxs_get_backslashescaped_internal($array, $depth)
{
	$result = array();
	
	// prevent endless loop
	$maxdepth = 10;	// increase if you need structures that are nested further
	if ($depth > $maxdepth) 
	{ 
		nxs_webmethod_return_nack("not sure, but suspecting a loop? increase the maxdepth if this is done on purpose ($maxdepth)"); 
	}
	
	if ($array == null)
	{
		//
	}
	else if (count($array) == 0)
	{
		//
	}
	else
	{
		foreach ($array as $key => $val)
		{
			if ($val != null)
			{
				if (is_array($val))
				{
					// recursive call
					$result[$key] = nxs_get_backslashescaped_internal($val, $depth + 1);
				}
				else
				{
					$result[$key] = nxs_get_backslashescaped_string($val);
				}
			}
			else
			{
				$result[$key] = null;
			}
		}
	}
	
	return $result;
}

function nxs_get_backslashescaped_string($val)
{
	// first we remove backslashes for single quotes and double quotes
	$val = str_replace('\\\'', '\'', $val);	// thus \' becomes '
	$val = str_replace('\\\"', '\"', $val);	// thus \" becomes "
	// next we slash the whole thing
	$val = wp_slash($val);	// here ' becomes \' and " becomes \" and \ becomes \\
	// if this array will be stored using update_post_meta, this will invoke stripslashes,
	// which will remove the backslash (*sigh*)
	return $val;
}

function nxs_merge_postmeta($postid, $modifiedmetadata)
{	
	if ($postid == "")
	{
		echo "postid is niet geset? (mpm)";
		return "postid is niet geset? (mpm)";
	}

	$metadatakey = 'nxs_core';
	$temp_array = array();
	$temp_array = maybe_unserialize(get_post_meta($postid, $metadatakey, true));
	$result = array_merge((array)$temp_array, (array)$modifiedmetadata);
	// fix; 20140307; backslashes are removed in anything we stored, unless
	// double-backslashed, see: 
	// - http://codex.wordpress.org/Function_Reference/update_post_meta
	// - https://codex.wordpress.org/Function_Reference/wp_slash
	$updateresult = update_post_meta($postid, $metadatakey, nxs_get_backslashescaped($result));

	// wipe cached data	
	global $nxs_gl_cache_postmeta;
	if (!isset($nxs_gl_cache_postmeta))
	{
		$nxs_gl_cache_postmeta = array();
	}
	if (array_key_exists($postid, $nxs_gl_cache_postmeta))
	{
		// remove item
		unset($nxs_gl_cache_postmeta[$postid]);
	}
	
	// TODO: handle updateresult (false means error for example)
}

function nxs_wipe_postmetakey($postid, $keytoberemoved)
{
	$keystoberemoved = array();
	$keystoberemoved[] = $keytoberemoved;
	return nxs_wipe_postmetakeys($postid, $keystoberemoved);
}

function nxs_wipe_postmetakeys($postid, $keystoberemoved)
{
	if ($postid == "")
	{
		echo "postid is niet geset? (mpm)";
		return "postid is niet geset? (mpm)";
	}

	$metadatakey = 'nxs_core';
	$temp_array = array();
	$temp_array = maybe_unserialize(get_post_meta($postid, $metadatakey, true));
	
	foreach ($keystoberemoved as $currentkeytoberemoved)
	{
		if (isset($temp_array[$currentkeytoberemoved]))
		{
			unset($temp_array[$currentkeytoberemoved]);
		}
	}
	
	$updateresult = update_post_meta($postid, $metadatakey, nxs_get_backslashescaped($temp_array));

	// wipe cached data	
	global $nxs_gl_cache_postmeta;
	if (!isset($nxs_gl_cache_postmeta))
	{
		$nxs_gl_cache_postmeta = array();
	}
	if (array_key_exists($postid, $nxs_gl_cache_postmeta))
	{
		// remove item
		unset($nxs_gl_cache_postmeta[$postid]);
	}
}

// getpostmeta
function nxs_get_postmeta($postid)
{
	if ($postid == "")
	{
		nxs_webmethod_return_nack("postid not set (getpostmeta)");
	}
	
	global $nxs_gl_cache_postmeta;
	if (!isset($nxs_gl_cache_postmeta))
	{
		$nxs_gl_cache_postmeta = array();
	}
	
	if (!array_key_exists($postid, $nxs_gl_cache_postmeta))
	{
		// its not (yet/anymore) in the cache; fetch!
		$metadatakey = 'nxs_core';
		$result = maybe_unserialize(get_post_meta($postid, $metadatakey, true));
		if ($result == null || $result == "index.php")
		{
			$result = array();
		}
		// store fetched result in the cache
		$nxs_gl_cache_postmeta[$postid] = $result;
	}
	else
	{
		$result = $nxs_gl_cache_postmeta[$postid];
	}
	
	return $result;
}

function nxs_analytics_handleanalytics()
{
	// see https://developers.google.com/analytics/devguides/collection/analyticsjs/#the_javascript_tracking_snippet
	$analyticsUA = nxs_seo_getanalyticsua();
	if ($analyticsUA != "") 
	{
		if (!is_user_logged_in())
		{
			?>
			<!-- Google Analytics -->
			<script>
				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
				
				ga('create', '<?php echo $analyticsUA; ?>', 'auto');
				ga('send', 'pageview');	
			</script>
			<?php 
		}
		else
		{
			?>
			<!-- Google Analytics is set but not rendered (<?php echo $analyticsUA; ?>); tracking script is not rendered for authenticated users (see https://github.com/nexusthemes/communityrfc/issues/60) -->
			<?php
		}
	} 
}

function nxs_wpseo_video_index_content($content, $vid)
{
  $postid = $vid['post_id'];
  // $postid will or at least point to revision
  
  $content = "";
	// include nxs content
	$rendermode = "anonymous";
	
	
	global $nxs_global_current_postmeta_being_rendered;
	$nxs_global_current_postmeta_being_rendered = nxs_get_postmeta($postid);
	
	$content .= nxs_getrenderedhtml($postid, $rendermode); 
	// include regular WP content
	//$postcontent .= " " . apply_filters('the_content', get_post_field('post_content', $postid));	
	$content .= " " . get_post_field('post_content', $postid);
  
  return $content;
}

function nxs_seo_getanalyticsua()
{
	$sitemeta = nxs_getsitemeta_internal(false);
	$result = $sitemeta["analyticsUA"];
	if ($result == "UA-38116525-1")
	{
		$result = "";
	}
	// for logged in users we will skype the 
	return $result;
}

function nxs_wpseo_pre_analysis_post_content($content)
{
	global $nxs_doing_seo;
	$nxs_doing_seo = true;
	global $nxs_seo_output;
	
	global $post;
	$postid = $post->ID;
	
	if ($postid != 0)
	{
		$rendermode = "anonymous";
	
		// we use a postadapter, since we want to output the analysis based on the entire content,
		// including the drag'n'drop items, not "just" the wp content.
	
		$content = "";
		// include nxs content
		$content .= nxs_getrenderedhtml($postid, $rendermode); 
		// include regular WP content
		//$postcontent .= " " . apply_filters('the_content', get_post_field('post_content', $postid));	
		$content .= " " . get_post_field('post_content', $postid);
	} 
	else
	{
		// leave content as-is, this is for example the case when user creates a new post or page in the WP backend
	}	
	
	return $content;
}

function nxs_addyoastseosupport()
{
	// for the page analysis, note that for the video content, a different patch is applied,
	// since the video plugin doesn't apply the wpseo_pre_analysis_post_content filter, see #438957387

	// deprecated since Yoast 3.0
	//add_filter("wpseo_pre_analysis_post_content", "nxs_wpseo_pre_analysis_post_content");
	//add_filter("wpseo_video_index_content", "nxs_wpseo_video_index_content", 10, 2 );

	if (is_admin())
	{	
		wp_enqueue_script( 'yoastbackendbridge', nxs_getframeworkurl() . '/js/seo/yoastbackendbridge.js', array(), nxs_getthemeversion(), TRUE );	
	}
	
	add_action( 'admin_head', 'nxs_admin_addyoastv3support' );
	
	function nxs_admin_addyoastv3support() 
	{
		if (is_admin() && defined('WPSEO_VERSION') && version_compare(WPSEO_VERSION, '3.0.0') > 0)
		{
			global $post;
			$postid = $post->ID;
			if ($postid != "")
			{
				?>
				<script>
					var nxs_seo_backend_context = 'post';
					var nxs_seo_backend_id = '<?php echo $postid; ?>';
					var nxs_seo_backend_content = '';
					
					function nxs_js_get_adminurladminajax() 
					{ 
						return "<?php 	
						// fix 20141002; we should use the wpurl instead of url
						$result = get_bloginfo("wpurl");
						if (!nxs_stringendswith($result, '/'))
						{
							// fix bug detected on Gerbers server
							$result = $result . "/";
						}
						$result .= "index.php?nxs-webmethod-queryparameter=true";
						echo $result;
						?>";
					}
					
					function nxs_js_log(s)
					{
						if ('console' in self && 'log' in console) 
						{
							console.log(s);
							//var stacktrace = nxs_js_getqueryparametervalue("stacktrace");
							
							// practical debug tool; if ctrl is pressed, output the stacktrac
							try { throw new Error("Stracktrace"); } catch (e) { console.log(e.stack); }
						}
					}
				</script>
				<?php
			}
		}
	}
}

function nxs_getpagecssclass($pagemeta)
{
	if (isset($pagemeta["cssclass"]))
	{
		return $pagemeta["cssclass"];
	}
	else
	{
		return "";
	}
}

function nxs_getpagetemplateforpostid($postid)
{
	if ($postid== "")
	{
		echo "postid is niet geset? (subpt a)";
		return "postid is niet geset? (subpt) b";
	}
	
	$terms = wp_get_object_terms(strval($postid), 'nxs_tax_subposttype');
	
	if(!empty($terms))
	{
		if(!is_wp_error($terms))
		{
			if (count($terms) == 1)
			{
				$term = $terms[0];
				$result = $term->name;
			}
			else if (count($terms) > 1)
			{
				// unexpected; we found 0, or multiple taxonomies?
				$result = "nxserr (n>1;n==" . count($terms) . ";postid=$postid)";
			} 
			else
			{
				// unexpected; we found 0, or multiple taxonomies?
				$result = "nxserr (n==0)";
			}
		}
		else
		{
			$result = "nxserr (wperr)";
			var_dump($terms);
		}		
	}
	else
	{
		// for legacy pages/posts/custom posts and for items created by 
		// third parties (extensions / plugins),
		// but only if they are publicly available		
		$wpposttype = nxs_getwpposttype($postid);
		
		$publicposttypes = get_post_types( array( 'public' => true));
		if (array_key_exists($wpposttype, $publicposttypes))
		{
			if ($wpposttype == "post")
			{
				// for legacy pages
				$result = "blogentry";
			}
			else if ($wpposttype == "page")
			{
				$result = "webpage";
			}
			else
			{
				// assumed webpage-like
				$result = "webpage";
			}
		}
		else
		{
			$result = "nxserr ($wpposttype) (not public)";
		}
	}
	
	return $result;
}

function nxs_updatepoststructure($postid, $postcontents)
{
	$metadatakey = 'nxs_struct';

	// 2012 06 16 GJ; bug fix	
	// sanitize the post contents, see http://wordpress.org/support/topic/plugin-wordpress-importer-how-to-import-multiline-post-metadata
	// we replace '\r\n' with '\r\' to prevent the import (when export/importing) from mis-interpreting the \r\rn resulting in empty structures
	$postcontents = str_replace("\r\n", "\r", $postcontents);
	
	$temp_array = array();
	$temp_array = maybe_unserialize(get_post_meta($postid, $metadatakey, true));
	$temp_array["structure"] = $postcontents;
	$result = array_merge((array)$temp_array, (array)$modifiedmetadata);
	
	update_post_meta($postid, $metadatakey, nxs_get_backslashescaped($result));
}

function nxs_getpoststructure($postid)
{
	$metadatakey = 'nxs_struct';
	
	$temp_array = array();
	$temp_array = maybe_unserialize(get_post_meta($postid, $metadatakey, true));
	if ($temp_array == "")
	{
		// empty
		$result = "";
	} 
	else
	{
		// downwards compatibility...
		$result = $temp_array['structure'];
	}
	return $result;
}

function nxs_updaterendercache($postid, $structure)
{
	// update the nexus struct	
	$metadatakey = 'nxs_rendercache';
	
	$temp_array = array();
	$temp_array = maybe_unserialize(get_post_meta($postid, $metadatakey, true));
	$temp_array["structure"] = $structure;
	$result = array_merge((array)$temp_array, (array)$modifiedmetadata);
	
	update_post_meta($postid, $metadatakey, nxs_get_backslashescaped($result));
}

function nxs_getrendercache($postid)
{
	$metadatakey = 'nxs_rendercache';
	
	$temp_array = array();
	$temp_array = maybe_unserialize(get_post_meta($postid, $metadatakey, true));
	return $temp_array["structure"];
}

function nxs_getrenderedrowhtml($postid, $rowindex, $rendermode)
{	
	$parsedpoststructure = nxs_parsepoststructure($postid);

	$result = nxs_getrenderedrowhtmlforparsedpoststructure($postid, $rowindex, $rendermode, $parsedpoststructure);
	
	return $result;
}

function nxs_getrenderedrowhtmlforparsedpoststructure($postid, $rowindex, $rendermode, $parsedpoststructure)
{
	global $nxs_global_current_nxsposttype_being_rendered;
	global $nxs_global_current_postid_being_rendered;
	global $nxs_global_current_postmeta_being_rendered;
	global $nxs_global_current_render_mode;
	global $nxs_global_current_rowindex_being_rendered;
	
	// temporarily replace the variables being rendered

	$original_nxs_global_current_nxsposttype_being_rendered = $nxs_global_current_nxsposttype_being_rendered;
	$nxs_global_current_nxsposttype_being_rendered = nxs_getnxsposttype_by_postid($postid);

	$original_nxs_global_current_postid_being_rendered = $nxs_global_current_postid_being_rendered;
	$nxs_global_current_postid_being_rendered = $postid;
	
	$original_nxs_global_current_postmeta_being_rendered = $nxs_global_current_postmeta_being_rendered;
	$nxs_global_current_postmeta_being_rendered = nxs_get_postmeta($postid);
	
	// same for the rendermode
	$original_nxs_global_current_render_mode = $nxs_global_current_render_mode;
	$nxs_global_current_render_mode = $rendermode;
	
	// same for the rowindex
	$original_nxs_global_current_rowindex_being_rendered = $nxs_global_current_rowindex_being_rendered;
	// let op, als de variabele op 0 wordt gezet wordt deze op null gezet,
	// vandaar de quotes die we ervoor plaatsen.
	$nxs_global_current_rowindex_being_rendered = "" . $rowindex;
	
	if (!array_key_exists($rowindex, $parsedpoststructure))
	{
		nxs_webmethod_return_nack("postid ($postid) does not contain rowindex ($rowindex).");
	}
	
	// LET OP, de quote is noodzakelijk; als deze op 0 wordt gezet wordt de variabele
	// op NULL gezet!!
	$nxs_global_current_rowindex_being_rendered = "" . $rowindex;	
	
	$row = $parsedpoststructure[$rowindex];
	$outercontent = $row["outercontent"];

	nxs_ob_start();
	
	echo do_shortcode($outercontent);
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
		
	// revert nxs_global_current_postid_being_rendered to its original value
	$nxs_global_current_postid_being_rendered = $original_nxs_global_current_postid_being_rendered;
	
	// same for the rendermode
	$nxs_global_current_render_mode = $original_nxs_global_current_render_mode;
	
	// same for the rowindex
	$nxs_global_current_rowindex_being_rendered = $original_nxs_global_current_rowindex_being_rendered;
	
	// nxsposttype
	$nxs_global_current_nxsposttype_being_rendered = $original_nxs_global_current_nxsposttype_being_rendered;

	// postmeta
	$nxs_global_current_postmeta_being_rendered = $original_nxs_global_current_postmeta_being_rendered;

	return $result;
}

function nxs_getrenderedhtml($postid, $rendermode)
{
	return nxs_getrenderedhtmlincontainer($postid, $postid, $rendermode);
}

function nxs_getrenderedhtmlincontainer($containerpostid, $postid, $rendermode)
{
	if ($containerpostid == "")
	{
		nxs_webmethod_return_nack("containerpostid is not set");
	}
	
	$poststatus = get_post_status($postid);
	if ($poststatus == "publish")
	{
		$parsedpoststructure = nxs_parsepoststructure($postid);
		$result = nxs_getrenderedhtmlinparsedpoststructure($containerpostid, $postid, $parsedpoststructure, $rendermode);
	}
	else if ($poststatus === false)
	{
		if ($postid == "SUPPRESSED")
		{
		}
		else
		{
			$reconfigure = "<a class='nxsbutton1' href='#' onclick='nxs_js_popup_pagetemplate_neweditsession(&quot;layout&quot;); return false;'>Change</a>";
			$result = nxs_getrowswarning(nxs_l18n__("Deleted section. Consider (re)configuring it.", "nxs_td") . $reconfigure);
		}
	}
	else if ($poststatus == "draft" || $poststatus == "private" || $poststatus == "future")
 		{
 		if (!is_user_logged_in())
 		{
 			nxs_webmethod_return_nack("unexpected; user is not logged on?");
 		}
 				
 		$parsedpoststructure = nxs_parsepoststructure($postid);
 		$result = nxs_getrenderedhtmlinparsedpoststructure($containerpostid, $postid, $parsedpoststructure, $rendermode);
 	}	
	else if ($poststatus == "trash")
	{
		$reconfigure = "<a class='nxsbutton1' href='#' onclick='nxs_js_popup_pagetemplate_neweditsession(&quot;layout&quot;); return false;'>Change</a>";
		$result = nxs_getrowswarning(nxs_l18n__("Trashed section. Consider (re)publishing it.", "nxs_td") . $reconfigure);
	}	
	else
	{
		$reconfigure = "<a class='nxsbutton1' href='#' onclick='nxs_js_popup_pagetemplate_neweditsession(&quot;layout&quot;); return false;'>Change</a>";
		$result = nxs_getrowswarning(nxs_l18n__("Section found, but in unsupported state ($poststatus). Consider (re)publishing it.", "nxs_td") . $reconfigure);
	}
	
	return $result;
}

function nxs_getrenderedhtmlinparsedpoststructure($containerpostid, $postid, $parsedpoststructure, $rendermode)
{
	global $nxs_global_current_nxsposttype_being_rendered;
	global $nxs_global_current_containerpostid_being_rendered;
	global $nxs_global_current_postid_being_rendered;
	global $nxs_global_current_postmeta_being_rendered;
	global $nxs_global_current_render_mode;

	// temporarily replace variables for rendering
	// this is required to render the shortcodes 

	$original_nxs_global_current_containerpostid_being_rendered = $nxs_global_current_containerpostid_being_rendered;
	$nxs_global_current_containerpostid_being_rendered = $containerpostid;

	$original_nxs_global_current_nxsposttype_being_rendered = $nxs_global_current_nxsposttype_being_rendered;
	$nxs_global_current_nxsposttype_being_rendered = nxs_getnxsposttype_by_postid($postid);

	$original_nxs_global_current_postid_being_rendered = $nxs_global_current_postid_being_rendered;
	$nxs_global_current_postid_being_rendered = $postid;

	$original_nxs_global_current_postmeta_being_rendered = $nxs_global_current_postmeta_being_rendered;
	$nxs_global_current_postmeta_being_rendered = nxs_get_postmeta($postid);
		
	$original_nxs_global_current_render_mode = $nxs_global_current_render_mode;
	$nxs_global_current_render_mode = $rendermode;
	
	//
	
	$result = "";
	$result .= "<div class='nxs-postrows'>";
	
	$rowindex = 0;
	foreach ($parsedpoststructure as $pagerow)
	{
		$result .= nxs_getrenderedrowhtmlforparsedpoststructure($postid, $rowindex, $rendermode, $parsedpoststructure);	
		$rowindex++;
	}
	
	$result .= "</div>";
	
	// revert nxs_global_current_postid_being_rendered to its original value
	$nxs_global_current_postid_being_rendered = $original_nxs_global_current_postid_being_rendered;

	// revert nxs_global_current_postid_being_rendered to its original value
	$nxs_global_current_containerpostid_being_rendered = $original_nxs_global_current_containerpostid_being_rendered;
	
	// same for the rendermode
	$nxs_global_current_render_mode = $original_nxs_global_current_render_mode;
	
	// same for the nxsposttype
	$nxs_global_current_nxsposttype_being_rendered = $original_nxs_global_current_nxsposttype_being_rendered;

	// same for postmeta
	$nxs_global_current_postmeta_being_rendered = $original_nxs_global_current_postmeta_being_rendered;

	return $result;
}

// function to filter an array of posts by complex filters (practical when filtering using sql is impossible or too complex)
function nxs_getfilteredposts(&$postshaystack, $filters)
{
	if (count($filters) == 0)
	{
		//echo "no filter specified";
		
		// no filters means we are done :)
		return;
	}
		
	foreach($postshaystack as $elementKey => $element) 
	{
		$shouldremoveitem = false;
		
		/*
		if (!$shouldremoveitem && array_key_exists("foobarkey", $filters))
		{			
			$nxsposttypefound = getsomepropertyfromcurrentelement(.);
			$nxsposttypetokeep = $filters["foobarkey"];
			
			if ($nxsposttypefound == $nxsposttypetokeep)			
			{
				// keep
			}
			else
			{
				$shouldremoveitem = true;
			}
		}
		// else if ( . ) { . }
		else
		{
			// no more filters to check
			
		}
		*/
    
    if ($shouldremoveitem)
    {
    	unset($postshaystack[$elementKey]);
    }
	}
	
	// restructure array
	$postshaystack = array_values($postshaystack);
}

function nxs_getfilteredcategories(&$categorieshaystack, $filters)
{
	//echo "filtering.";
	if (count($filters) == 0)
	{
		//echo "no filter specified";
		
		// no filters means we are done :)
		return;
	}
		
	foreach($categorieshaystack as $elementKey => $element) 
	{
		$shouldremoveitem = false;
		
		if (!$shouldremoveitem && array_key_exists("uncategorized", $filters))
		{			
			// 
			if ($element->name == 'Uncategorized')			
			{
				$shouldremoveitem = true;
			}
			else if (isset($element->ID) && $element->ID == 1)
			{
				$shouldremoveitem = true;
			}
		}
		// else if ( . ) { . }
		else
		{
			// no more filters to check
		}
    
        if ($shouldremoveitem)
        {
            unset($categorieshaystack[$elementKey]);
        }
	}
	
	// restructure array
	$categorieshaystack = array_values($categorieshaystack);
}

function nxs_after_postcontents_updated($postid)
{
	$result["result"] = "OK";
	
	// perform after save actions based on the nxsposttype
	$nxsposttype = nxs_getnxsposttype_by_postid($postid);
	if ($nxsposttype == "menu")
	{
		// no longer needed; we render the menu ourselves
	}
	else if (
		$nxsposttype == "sidebar" ||
		$nxsposttype == "post" ||
		$nxsposttype == "footer" ||
		$nxsposttype == "header" ||
		$nxsposttype == "genericlist" ||
		$nxsposttype == "admin" || 
		$nxsposttype == "subfooter" ||
		$nxsposttype == "subheader" ||
		$nxsposttype == "template" ||
		$nxsposttype == "pagelet")
	{
		// nothing needs to be derived
	}
	else
	{
		$args = array();
		$result = apply_filters("nxs_after_postcontents_updated", $result, $args);
	}
	
	// patching; implements handling of post updates for extensions not supporting front-end editing out of the box
	
	// patch #438957387; the video plugin of yoast does not apply the "wpseo_pre_analysis_post_content" filter
	// but we need this when we update the post on the front-end part. This patch ensures that the video is automatically
	// updated when the front-end content is updated
	if (class_exists('wpseo_Video_Sitemap'))
	{
		global $shortcode_tags;
		$old_shortcode_tags = $shortcode_tags;

		// WP VIDEO SEO plugin only updated the post when is_admin() is true
		$nxs_wpseo_Video_Sitemap = new wpseo_Video_Sitemap();
		$nxs_wpseo_Video_Sitemap->update_video_post_meta($postid);
		
		$shortcode_tags= $old_shortcode_tags;
	}
	
	return $result;
}

function nxs_isnxswebservicerequest()
{
	$result = false;
	if (isset($_REQUEST["webmethod"]) && $_REQUEST["action"])
	{
		if ($_REQUEST["webmethod"] != "" && $_REQUEST["action"] == "nxs_ajax_webmethods")
		{
			$result = true;
		}
	}
	return $result;	
}

function nxs_ishomepage($postid)
{
	$sitemeta = nxs_getsitemeta();
	$homepageid = $sitemeta["home_postid"];
	if ($homepageid == "" || $homepageid == null)
	{
		// legacy; to be removed eventually
		$homepageid = get_option("nxs_home_postid");
	}
	
	$result = ($homepageid == $postid);
		
	return $result;
}

function nxs_sethomepage($postid)
{
	// phase 1; promote to page
	$r = nxs_converttopage($postid);
	
	// phase 2; store the value used by the framework
	$modifiedmetadata = array();
	$modifiedmetadata["home_postid"] = $postid;
	$modifiedmetadata["home_postid_globalid"] = nxs_get_globalid($postid, true);
	nxs_mergesitemeta($modifiedmetadata);
	
	//
	// ensure WP 'knows' we have set an updated homepage
	//
	nxs_wp_retouchhomepage();
}

function nxs_wp_retouchhomepage()
{
	$postid = nxs_gethomepageid();
	
	update_option('show_on_front', 'page');
	update_option('page_on_front', $postid);
}

function nxs_wp_resetrewriterules()
{
	// 20160801; weird situation; on rare themes (like the mobile_repair_wordpress_theme),
	// the rewrite rules are not properly set when the themes activates. Accessing
	// blog posts in that case results in a 404 even though the posts are there.
	// to resolve this, we wipe the rewrite rules and instruct WP to rebuild them
	global $wp_rewrite;
	// 
	update_option('rewrite_rules', '');
	$wp_rewrite->flush_rules();
	
	$wp_rewrite->wp_rewrite_rules();
	$wp_rewrite->flush_rules();
}

function nxs_is404page($postid)
{
	$sitemeta = nxs_getsitemeta();
	$configuredpostid = $sitemeta["404_postid"];
	if ($configuredpostid == "" || $configuredpostid == null)
	{
		// legacy; to be removed eventually
		$configuredpostid = get_option("nxs_404_postid");
	}
	
	$result = ($configuredpostid == $postid);
		
	return $result;
}

function nxs_set404page($postid)
{
	$modifiedmetadata = array();
	$modifiedmetadata["404_postid"] = $postid;
	$modifiedmetadata["404_postid_globalid"] = nxs_get_globalid($postid, true);
	
	nxs_mergesitemeta($modifiedmetadata);
}

function nxs_isserppage($postid)
{
	$sitemeta = nxs_getsitemeta();
	$serppageid = $sitemeta["serp_postid"];
	$result = ($serppageid == $postid);
		
	return $result;
}

function nxs_setserppage($postid)
{
	$modifiedmetadata = array();
	$modifiedmetadata["serp_postid"] = $postid;
	$modifiedmetadata["serp_postid_globalid"] = nxs_get_globalid($postid, true);
	
	nxs_mergesitemeta($modifiedmetadata);
}

function nxs_cleanupobsoletewidgetmetadata($postid, $persistchanges)
{
	$parsedpoststructure = nxs_parsepoststructure($postid);
	
	// get placeholderids in use according to structure
	$rowindex = 0;
	$metakeysinuse = array();
	foreach ($parsedpoststructure as $pagerow)
	{
		$content = $pagerow["content"];		
		$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
		foreach ($placeholderids as $placeholderid)
		{
			$metakeysinuse[] = "nxs_ph_" . $placeholderid;
		}
	}
	
	$obsoletemetakeys = array();
	$origpost_meta_all = nxs_get_post_meta_all($postid);
	$keystokeep = array("nxs_page_meta", "nxs_struct", "nxs_core", "nxs_globalid");
	foreach ($origpost_meta_all as $meta_key => $meta_value)
	{
		if (in_array($meta_key, $keystokeep))
		{
			// keep :)
		}
		else if (nxs_stringstartswith($meta_key, "ewm"))
		{
			// old product name eigenwebsitemaken (EWM); deprecated field
			$obsoletemetakeys[] = $meta_key;
		}
		else if ($meta_key == "nxs_version")
		{
			// no longer used; deprecated field
			$obsoletemetakeys[] = $meta_key;
		}
		else if (nxs_stringstartswith($meta_key, "_yoast_"))
		{
			// keep
		}
		else if (nxs_stringstartswith($meta_key, "_woocommerce_"))
		{
			// keep
		}
		else if (nxs_stringstartswith($meta_key, "_edit_"))
		{
			// keep
		}
		else if (nxs_stringstartswith($meta_key, "_wp_"))
		{
			// keep
		}
		else if (nxs_stringstartswith($meta_key, "nxs_pr_"))
		{
			// keep product row meta
		}
		else if (nxs_stringstartswith($meta_key, "_order"))
		{
			// woocommerce
		}
		else if (nxs_stringstartswith($meta_key, "_billing"))
		{
			// woocommerce
		}
		else if (nxs_stringstartswith($meta_key, "_shipping"))
		{
			// woocommerce
		}
		else if (nxs_stringstartswith($meta_key, "_payment"))
		{
			// woocommerce
		}
		else if (nxs_stringstartswith($meta_key, "_prices"))
		{
			// woocommerce
		}
		else if (nxs_stringstartswith($meta_key, "_customer"))
		{
			// woocommerce
		}
		else if (nxs_stringstartswith($meta_key, "Customer"))
		{
			// woocommerce
		}
		else if (nxs_stringstartswith($meta_key, "_thumbnail"))
		{
			// not sure who creates this one
		}
		else if (nxs_stringstartswith($meta_key, "totalsales"))
		{
			// woocommerce
		}
		else if (nxs_stringstartswith($meta_key, "_tax"))
		{
			// woocommerce
		}
		else if (nxs_stringstartswith($meta_key, "_cart"))
		{
			// woocommerce
		}
		else if (nxs_stringstartswith($meta_key, "nxs_ph_"))
		{
			// potential item to be removed
			if (in_array($meta_key, $metakeysinuse))
			{
				// keep!
			}
			else
			{
				$obsoletemetakeys[] = $meta_key;
			}
		}
		else
		{
			// assumed to have to keep
			//echo "<br />other metakey found; keeping;" . $meta_key . "<br />";
		}
	}
	
	foreach ($obsoletemetakeys as $current_obsoletemetakey)
	{
		echo "<br />removing;" . $current_obsoletemetakey . "<br />";
		$olddata = get_post_meta($postid, $current_obsoletemetakey);
		var_dump($olddata);
		
		if ($persistchanges == true)
		{
			delete_post_meta($postid, $current_obsoletemetakey); 
		}
	}
}

function nxs_getwidgettype($postid, $placeholderid)
{
	$placeholdermetadata = nxs_getwidgetmetadata($postid, $placeholderid);
	$result = $placeholdermetadata["type"];
	return $result;
}

// obsolete function; the getwidgetmetadata didn't process the 
// unistyle and unicontent values causing widgets to be rendered
// incorrectly; in the new method the processing of lookupunistyle and unicontent
// can be configured by an additional parameter
function nxs_getwidgetmetadata($postid, $placeholderid)
{
	$behaviourargs = array();
	$behaviourargs["lookupunistyle"] = true;
	$behaviourargs["lookupunicontent"] = true;

	$result = nxs_getwidgetmetadata_v2($postid, $placeholderid, $behaviourargs);

	return $result;
}

function nxs_getwidgetmetadata_v2($postid, $placeholderid, $behaviourargs)
{
	$metadatakey = 'nxs_ph_' . $placeholderid;
	$result = array();
	$result = maybe_unserialize(get_post_meta($postid, $metadatakey, true));
	
	if ($result == "")
	{
		$result = array();
	}
	
	// optionally process unistyle
	// if the widget has a unistyle, the properties of the unistyle should 
	// override the properties stored in the widget itself
	// this method is pretty fast since the unistyle configurations are cached in mem
	if ($behaviourargs["lookupunistyle"] == true && $result["unistyle"] != "")
	{
		// unistyle lookup should override the result
		$widgetname = $result["type"];
		$unistyle = $result["unistyle"];
		$unistylegroup = nxs_getunifiedstylinggroup($widgetname);
		if ($unistylegroup != "")
		{
			$unistyleproperties = nxs_unistyle_getunistyleproperties($unistylegroup, $unistyle);
			$result = array_merge($result, $unistyleproperties);
		}
	}
	
	// optionally process unicontent
	// if the widget has a unicontent, the properties of the unicontent should 
	// override the properties stored in the widget itself
	// this method is pretty fast since the unicontent configurations are cached in mem
	if ($behaviourargs["lookupunicontent"] == true && $result["unicontent"] != "")
	{
		// unicontentlookup should override the result
		$widgetname = $result["type"];
		$unicontent = $result["unicontent"];
		$unifiedcontentgroup = nxs_unicontent_getunifiedcontentgroup($widgetname);
		if ($unifiedcontentgroup != "")
		{
			$unicontentproperties = nxs_unicontent_getunicontentproperties($unifiedcontentgroup, $unicontent);
			$result = array_merge($result, $unicontentproperties);
		}
	}
	
	// allow plugins to further manipulate the output
	$args = array
	(
		
	);
	$result = apply_filters("nxs_f_getwidgetmetadata", $result, $args);
	
	return $result;
}

//

function nxs_row_getunifiedstylinggroup()
{
	return "row";
}

function nxs_getpagerowmetadata($postid, $pagerowid)
{
	$behaviourargs = array();
	$behaviourargs["lookupunistyle"] = true;
	
	return nxs_getpagerowmetadata_v2($postid, $pagerowid, $behaviourargs);
}

function nxs_getpagerowmetadata_v2($postid, $pagerowid, $behaviourargs)
{
	$metadatakey = 'nxs_pr_' . $pagerowid;
	$result = array();
	$result = maybe_unserialize(get_post_meta($postid, $metadatakey, true));
	
	if ($result == "")
	{
		$result = array();
	}
	
	// optionally process unistyle
	// if the row has a unistyle, the properties of the unistyle should 
	// override the properties stored in the row itself
	// this method is pretty fast since the unistyle configurations are cached in mem
	if ($behaviourargs["lookupunistyle"] == true && $result["unistyle"] != "")
	{
		$unistyle = $result["unistyle"];

		// unistyle lookup should override the result
		$unistylegroup = nxs_row_getunifiedstylinggroup();
		if ($unistylegroup != "")
		{
			$unistyleproperties = nxs_unistyle_getunistyleproperties($unistylegroup, $unistyle);
			$result = array_merge($result, $unistyleproperties);
		}
	}
	
	return $result;
}

// persists the widget's metadata (assumes the global data is already enriched)
function nxs_mergewidgetmetadata_internal($postid, $placeholderid, $updatedvalues)
{
	$behaviourargs = array();
	$behaviourargs["updateunistyle"] = true;
	$behaviourargs["updateunicontent"] = true;
	return nxs_mergewidgetmetadata_internal_v2($postid, $placeholderid, $updatedvalues, $behaviourargs);
}

// persists the widget's metadata (assumes the global data is already enriched)
function nxs_mergewidgetmetadata_internal_v2($postid, $placeholderid, $updatedvalues, $behaviourargs)
{
	if ($postid == "") { nxs_webmethod_return_nack("postid not set (nxs_mergewidgetmetadata_internal_v2)"); }
	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set"); }
	if ($updatedvalues == "") { nxs_webmethod_return_nack("updatedvalues not set"); }
	
	if (count($updatedvalues) == 0)
	{
		return;
	}
	
	//
	// determine the entire "set" of properties to store for this widget,
	// this is the serialized properties combined with the updated key/values
	//
	$metadatakey = 'nxs_ph_' . $placeholderid;
	$result = array();
	$existing = maybe_unserialize(get_post_meta($postid, $metadatakey, true));
	if ($existing == "")
	{
		// 2012 07 23; bug fix; first time the widget is initialized, the meta data is empty(""),
		// in this case we only set the new values and ignore the old one
		$allvalues = $updatedvalues;
	}
	else
	{
		$allvalues = array_merge($existing, $updatedvalues);
	}
	
	//
	// step 1; store the metadata of the widget itself
	//
	update_post_meta($postid, $metadatakey, nxs_get_backslashescaped($allvalues));
	 
	//
	// step 2; update the metadata of the unistyle
	//
	$unistyle = $allvalues["unistyle"];
	if (isset($unistyle) && $unistyle != "" && $behaviourargs["updateunistyle"] == true)
	{
		$widget = $allvalues["type"];
		if (!isset($widget) || $widget == "") { nxs_webmethod_return_nack("widget type not set"); }
		
		nxs_requirewidget($widget);
		$sheet = "home";
		
		nxs_requirepopup_contextprocessor("widgets");
		$options = nxs_popup_contextprocessor_widgets_getoptions_widgetsheet($widget, $sheet);
		
		// we store 'all' unistyleable fields (not just the fieldids that are unistyleable, also
		// the derived globalids. To determine which global fields there are, we look over
		// all fields, and we include all ones starting with unistylablefields,
		// for example "foo" and "foo_globalid"; all ones are added, "foo*").
		$unistyleablefields = array();
		$fieldids = nxs_unistyle_getunistyleablefieldids($options);
		foreach ($fieldids as $currentfieldid)
		{
			// find derivations of this field, also the globalids
			foreach ($allvalues as $currentkey => $currentvalue)
			{
				if ($currentkey == $currentfieldid)
				{
					// exact match
					$unistyleablefields[$currentkey] = $currentvalue;
				}
				else if (nxs_stringstartswith($currentkey, "{$currentfieldid}_g"))
				{
					// for the globalid "versions" (p.e. "a" => "a_globalid")
					// note that this does not make much sense for unistyling,
					// as unistyling does not likely have globalid fields (those
					// are more unicontent related, but regardless).
					$unistyleablefields[$currentkey] = $currentvalue;
				}
			}
		}
		
		$unigroup = $options["unifiedstyling"]["group"];
		if (!isset($unigroup) || $unigroup == "") { echo "a) options: "; var_dump($options);nxs_webmethod_return_nack("unigroup not set"); }
		nxs_unistyle_persistunistyle($unigroup, $unistyle, $unistyleablefields);
	}
	
	//
	// step 3; update the metadata of the unicontent
	//
	$unicontent = $allvalues["unicontent"];
	if (isset($unicontent) && $unicontent != "" && $behaviourargs["updateunicontent"] == true)
	{
		$widget = $allvalues["type"];
		if (!isset($widget) || $widget == "") { nxs_webmethod_return_nack("widget type not set"); }
		
		nxs_requirewidget($widget);
		$sheet = "home";
		
		nxs_requirepopup_contextprocessor("widgets");
		$options = nxs_popup_contextprocessor_widgets_getoptions_widgetsheet($widget, $sheet);

		// we store 'all' unicontentable fields (not just the fieldids that are unicontentable, also
		// the derived globalids. To determine which global fields there are, we look over
		// all fields, and we include all ones starting with unicontentablefieldids,
		// for example "foo" and "foo_globalid"; all ones are added, "foo*").

		$unicontentablefields = array();
		$fieldids = nxs_unicontent_getunicontentablefieldids($options);
		
		foreach ($fieldids as $currentfieldid)
		{
			// find derivations of this field, also the globalids
			foreach ($allvalues as $currentkey => $currentvalue)
			{
				if ($currentkey == $currentfieldid)
				{
					// exact match
					$unicontentablefields[$currentkey] = $currentvalue;
				}
				else if (nxs_stringstartswith($currentkey, "{$currentfieldid}_g"))
				{
					// for the globalid "versions" (p.e. "image_imageid" => "image_imageid_globalid")
					$unicontentablefields[$currentkey] = $currentvalue;
				}
			}
		}
		 
		$unigroup = $options["unifiedcontent"]["group"];
		if (!isset($unigroup) || $unigroup == "") { echo "b) options: "; var_dump($options);nxs_webmethod_return_nack("unigroup not set"); }
		nxs_unicontent_persistunicontent($unigroup, $unicontent, $unicontentablefields);
	}
	else
	{
		// unicontent not applicable
	}
}

function nxs_purgeplaceholdermetadata($postid, $placeholderid)
{	
	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set (owmd)"); };
	if ($postid== "") { nxs_webmethod_return_nack("postid not set (owmd)"); };

	$metadatakey = 'nxs_ph_' . $placeholderid;
	delete_post_meta($postid, $metadatakey);
}

function nxs_resetplaceholdermetadata($postid, $placeholderid)
{
	$metadata = array();
	$metadata["type"] = "undefined";
	nxs_overridewidgetmetadata($postid, $placeholderid, $metadata);
}

function nxs_overridewidgetmetadata($postid, $placeholderid, $metadata)
{
	//
	//
	//

	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set (owmd)"); };
	if ($postid== "") { nxs_webmethod_return_nack("postid not set (owmd)"); };
	
	$metadatakey = 'nxs_ph_' . $placeholderid;
	update_post_meta($postid, $metadatakey, nxs_get_backslashescaped($metadata));
}

function nxs_persistwidgettype($postid, $placeholderid, $placeholdertemplate)
{
	if ($postid == "") { nxs_webmethod_return_nack("postid not set (nxs_persistwidgettype)"); }
	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set"); }
	
 	$datatomerge = array();
 	$datatomerge["type"] = $placeholdertemplate;
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $datatomerge);
}

function nxs_initializewidget($args) 
{
	if (!isset($args["clientpopupsessioncontext"])) { nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	
	extract($args["clientpopupsessioncontext"]);
	$placeholdertemplate = $args["placeholdertemplate"];
	
	if (!isset($postid)) { nxs_webmethod_return_nack("postid not set (nxs_initializewidget)"); }
	if (!isset($placeholderid)) { nxs_webmethod_return_nack("placeholderid not set"); }
	if (!isset($placeholdertemplate)) { nxs_webmethod_return_nack("placeholdertemplate not set"); }

	// ensure widget exists
 	nxs_requirewidget($placeholdertemplate);
 	
 	// crucial step; first we set the type for this widget to the specified type
 	nxs_persistwidgettype($postid, $placeholderid, $placeholdertemplate);

	// load context processor 	
 	nxs_requirepopup_contextprocessor("widgets");
 	
	if (nxs_genericpopup_supportsoptions($args))
	{
		$blendedmetadata = array();
		
		// the initial data
		$initialmetadata = nxs_genericpopup_getinitialoptionsvalues($args);
  	$blendedmetadata = array_merge($blendedmetadata, $initialmetadata);
  	
		// enrich the initialdata; globalids
  	$enrichedmetadata = nxs_genericpopup_getderivedglobalmetadata($args, $initialmetadata);
  	$blendedmetadata = array_merge($blendedmetadata, $enrichedmetadata);
  
  	$result = nxs_popup_contextprocessor_widgets_mergedata_internal($args, $blendedmetadata);
	}
 	
	// if it exists, invoke nxs_widgets_image_initplaceholderdata($args)
	// else invoke nxs_widgets_image_updateplaceholderdata($args)
	$functionnametoinvoke = 'nxs_widgets_' . $placeholdertemplate . '_initplaceholderdata';
	if (function_exists($functionnametoinvoke))
	{
		$p = array();
		$p["postid"] = $postid;
		$p["placeholderid"] = $placeholderid;
		
		$result = call_user_func($functionnametoinvoke, $p);
	}
	
	return $result;
}

function nxs_getdepth($postid, $placeholderid)
{
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	$result = $temp_array['depthindex'];
	return $result;
}

function nxs_register_menus() 
{
	// we register the nav menu only to be able to show the menus in the backend
	register_nav_menus(
	array(
		'nxs-menu-generic' => 'Generic Nexus Menu',
	)
	);
}

function nxs_has_adminpermissions()
{
	global $nxs_gl_cache_hasadminpermissions;
	if ($nxs_gl_cache_hasadminpermissions == null || $nxs_gl_cache_hasadminpermissions == "")
	{
		$result = "false";
		
		if (is_user_logged_in())
		{
			if 
			(
				current_user_can("edit_published_pages") // && current_user_can("manage_options")
			)		
			{				
				$result = "true";

				// when the querparameter nxs_impersonate has value 'anonymous' is,
				// the permissions are "lowered" to "anonymous" access (this is used by the
				// pagepopup widget, which opens a popup where its very annoying to
				// have the editor features
				if ($_REQUEST["nxs_impersonate"] == "anonymous")
				{
					$result = "false";
				}

			}
			else
			{
				$result = "false";
			}
		}
		else
		{
			$result = "false";
		}

		// enable theme/plugins to override this (practical for example
		// if you only want network administrators to have admin rights
		$result = apply_filters('nxs_has_adminpermissions', $result);	
		$nxs_gl_cache_hasadminpermissions = $result;
	}

	$result = ($nxs_gl_cache_hasadminpermissions == "true");

	return $result;
}

function nxs_render_popup_header($title)
{
	nxs_render_popup_header_v2($title, "", "");
}

function nxs_render_popup_header_v2($title, $iconid, $sheethelp) {

	if ($title == "") {
		$title = "&nbsp;";
	}
	
	if ($iconid != "") {
		$imagehtml = "";
		$iconspan = "<span class='" . $iconid . "'></span>";
	} else {
		$iconspan = "";
		if ($imageurl != "") {
			$imagehtml = "<img src='" . $imageurl . "' />";
		} else {
			$imagehtml = "";
		}
	}
	
	echo '
	<div class="nxs-admin-header">
		<a href=\'#\' onclick=\'nxs_js_closepopup_unconditionally_if_not_dirty(); return  false;\'>
			<span class="nxs-popup-closer nxs-icon-remove-sign" title="'. nxs_l18n__("Close popup", "nxs_td") .'"></span>
		</a>
		';
		
		if ($sheethelp != "") { echo '
		<a target="_blank" href="'.$sheethelp.'">
			<span class="nxs-icon-support" style="text-decoration: none; line-height: 45px; font-size: 16px; position: absolute; right: 40px;" title="Help"></span>
		</a>';
		}
	
		echo $imagehtml;
		echo '<h3>'.$iconspan.' '.$title.'</h3>';
				
	echo'	
	</div>
	';
  
		
			
	
}

function nxs_applyshortcodes($content)
{
	$result = do_shortcode(shortcode_unautop($content));
	return $result;
}

function nxs_cleanup_paragraphbreaks($content)
{
	$content = preg_replace('#^<\/p>|^<br \/>|<p>$#', '', $content);
	$content = preg_replace('#<br \/>#', '', $content);
		
	return trim($content);
}

function nxs_invokenexusservicevalue_internal($data)
{
	$result = array();
	$result["result"] = "NACK";
	return $result;
}

function nxs_invokenexusservicevalue($key, $subkey, $data)
{
	$result = array();
	$result["result"] = "NACK";
	return $result;
}

function nxs_getfailovertransientnexusservervalue($key, $subkey, $additionalparams)
{
	if ($key == "")
	{
		nxs_webmethod_return_nack("key was not specified!");
	}
	
	$filetobeincluded = NXS_FRAMEWORKPATH . "/nexuscore/failovervalues/failover_" . $key . ".php";
	if (file_exists($filetobeincluded))
	{
		require_once($filetobeincluded);
		
		if ($subkey == "")
		{
			$functionnametoinvoke = 'nxs_failover_' . $key . '_getvalue';
		}
		else
		{
			$functionnametoinvoke = 'nxs_failover_' . $key . '_' . $subkey . '_getvalue';
		}
		
		if (function_exists($functionnametoinvoke))
		{
			// extend the parameters
			$args["key"] = $key;
			$args["subkey"] = $subkey;
			$args["additionalparams"] = $additionalparams;
			
			$result = call_user_func($functionnametoinvoke, $args);
			
			if (!is_array($result))
			{
				var_dump($result);
				nxs_webmethod_return_nack("expected an array to be returned?");
			}
		}
		else
		{
			nxs_webmethod_return_nack("function not found; $functionnametoinvoke");
		}
	}
	else
	{		
		nxs_webmethod_return_nack("file $filetobeincluded not found!");
	}
	
	return $result;
}

function nxs_gettransientnexusservervalue($key, $subkey, $additionalparams)
{
	if ($key == "")
	{
		nxs_webmethod_return_nack("key not set");
	}
	
	$value = nxs_getfailovertransientnexusservervalue($key, $subkey, $additionalparams);
	
	return $value;
}

function nxs_sendhtmlmail($fromname, $fromemail, $toemail, $subject, $body)
{
	$ccemail = "";
	$bccemail = "";
	return nxs_sendhtmlmail_v2($fromname, $fromemail, $toemail, $ccemail, $bccemail, $subject, $body);
}

function nxs_f_wp_mail_from($result)
{
	global $nxs_global_mail_fromemail;
	if ($nxs_global_mail_fromemail != "")
	{
		$result = $nxs_global_mail_fromemail;
	}
	return $result;
}

function nxs_f_wp_mail_from_name($result)
{
	global $nxs_global_mail_fromname;
	if ($nxs_global_mail_fromname != "")
	{
		$result = $nxs_global_mail_fromname;
	}
	return $result;
}

function nxs_sendhtmlmail_v2($fromname, $fromemail, $toemail, $ccemail, $bccemail, $subject, $body)
{
	$replytomail = "";
	$result = nxs_sendhtmlmail_v3($fromname, $fromemail, $toemail, $ccemail, $bccemail, $replytomail, $subject, $body);
	return $result;
}

function nxs_sendhtmlmail_v3($fromname, $fromemail, $toemail, $ccemail, $bccemail, $replytomail, $subject, $body)
{
	$headers = "";
	$headers .= 'From: ' . $fromname . ' <' . $fromemail . '>' . "\r\n";
	if ($replytomail != "")
	{	
		$headers .= "Reply-to: {$replytomail}\r\n";
	}
	
	if ($ccemail != "")
	{
		if (is_string($ccemail))
		{
			$headers .= "Cc: " . $ccemail . "\r\n";
		}
		else if (is_array($ccemail))
		{
			foreach ($ccemail as $currentccemail)
			{
				$headers .= "Cc: $currentccemail" . "\r\n";;
			}
		}
		else
		{
			// unknown?
		}
	}
	else
	{
		//
	}
	// bcc
	if ($bccemail != "")
	{
		if (is_string($bccemail))
		{
			$headers .= "Bcc: $bccemail" . "\r\n";
		}
		else if (is_array($bccemail))
		{
			foreach ($bccemail as $currentbccemail)
			{
				$headers .= "Bcc: $currentbccemail" . "\r\n";
			}
		}
		else
		{
			// unknown?
		}
	}
	else
	{
		//
	}
	
	global $nxs_global_mail_fromname;
	$nxs_global_mail_fromname = $fromname;
	global $nxs_global_mail_fromemail;
	$nxs_global_mail_fromemail = $fromemail;
	
	//error_log("nxs_sendhtmlmail_v3 adding filters 999 $nxs_global_mail_fromname $nxs_global_mail_fromemail");
	
	add_filter('wp_mail_from', 'nxs_f_wp_mail_from', 999, 1);
	add_filter('wp_mail_from_name', 'nxs_f_wp_mail_from_name', 999, 1);
	
	//
	$headers .= 'Content-Type: text/html;' . "\r\n";
	//error_log("nxs_sendhtmlmail_v3 before wp_mail");
	//error_log("nxs_sendhtmlmail_v3 headers; $headers");
	$result = wp_mail($toemail, $subject, $body, $headers);
	//error_log("nxs_sendhtmlmail_v3 after wp_mail");
	
	if ($result == false)
	{
		error_log("Error sending mail $fromname, $fromemail, $toemail, $ccemail, $bccemail, $subject");
	}
	
	return $result;
}

function nxs_sendhtmlmailtemplate($fromname, $fromemail, $toemail, $ccemail, $bccemail, $subject, $body, $lookup)
{
	foreach ($lookup as $key => $val)
	{
		$body = str_replace($key, $val, $body);
		$subject = str_replace($key, $val, $subject);
	}
	
	return nxs_sendhtmlmail_v2($fromname, $fromemail, $toemail, $ccemail, $bccemail, $subject, $body);
}

function nxs_dump_post_meta_all($post_id)
{
	$output = get_post_custom($post->ID);
	var_dump($output);
}

function nxs_getpostwizards($args)
{
	$result = array();
	
	// plugins can extend this list by using the following filter
	$result = apply_filters("nxs_getpostwizards", $result, $args);
	
	//
	// enrich the data; lookup the title for each postwizard added
	//
	$index = 0;
	foreach ($result as $widgetdata)
	{
		$postwizard = $widgetdata["postwizard"];
		$title = nxs_getpostwizardtitle($postwizard);
		$result[$index]["titel"] = $title;
		$index++;
	}
	
	return $result;
}

function nxs_renderpagetemplatepreview($pagetemplate)
{
	nxs_requirepagetemplate($pagetemplate);
	
	$functionnametoinvoke = 'nxs_pagetemplate_' . $pagetemplate . '_renderpreview';
	
	//
	// invoke
	//
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		echo "function not found; " . $functionnametoinvoke;
	}

	return $result;
}

function nxs_renderpostwizardpreview($postwizard, $args) 
{
	nxs_requirepostwizard($postwizard);

	$functionnametoinvoke = 'nxs_postwizard_' . $postwizard . '_renderpreview';
	
	//
	// invoke
	//
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		echo "function not found; " . $functionnametoinvoke;
	}

	return $result;
}

function nxs_renderpagetemplate($pagetemplate, $args) 
{
	nxs_requirepagetemplate($pagetemplate);

	$functionnametoinvoke = "nxs_pagetemplate_{$pagetemplate}_render";
	
	//
	// invoke
	//
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		echo "function not found; " . $functionnametoinvoke;
	}

	return $result;
}

function nxs_gethomepageid()
{
	$meta = nxs_getsitemeta();
	return $meta["home_postid"];
}

function nxs_gethomeglobalid()
{
	$homepostid = nxs_gethomepageid();
	$result = nxs_get_globalid($homepostid, true);
	return $result;
}

function nxs_getmaintenancedurationinsecs()
{
	$meta = nxs_getsitemeta();
	$result = $meta["maintenance_duration"];
	if ($result == "-" || $result == "")
	{
		$result = 0;
	}
	return $result;
}

function nxs_site_get_anonymousaccess()
{
	$meta = nxs_getsitemeta_internal(false);
	if (count($meta) == 0)
	{
		$result = null;
	}
	else
	{
		$result = $meta["accessrestrictions_anonymousaccess"];
	}
	
	return $result;
}

function nxs_issiteinmaintenancemode()
{
	$result = false;
	$maintenanceduration = nxs_getmaintenancedurationinsecs();
	if (isset($maintenanceduration) && $maintenanceduration != 0)
	{
		$result = true;
	}
	return $result;
}

// taken from http://stackoverflow.com/questions/79960/how-to-truncate-a-string-in-php-to-the-word-closest-to-a-certain-number-of-chara
function nxs_truncate_string($string, $your_desired_width)
{
  $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
  $parts_count = count($parts);

  $length = 0;
  $last_part = 0;
  for (; $last_part < $parts_count; ++$last_part) {
    $length += strlen($parts[$last_part]);
    if ($length > $your_desired_width) { break; }
  }

  return implode(array_slice($parts, 0, $last_part));
}

// credits: http://www.kavoir.com/2010/03/php-how-to-detect-get-the-real-client-ip-address-of-website-visitors.html
function nxs_get_ip_address()
{
  foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) 
  {
    if (array_key_exists($key, $_SERVER) === true) 
    {
      foreach (explode(',', $_SERVER[$key]) as $ip) 
      {
        if (filter_var($ip, FILTER_VALIDATE_IP) !== false) 
        {
          return $ip;
        }
      }
    }
  }
  return "";
}

function nxs_getcommentidswithparent($allcomments, $parentcommentid)
{	
	$result = array();
	foreach($allcomments as $currentcomment) 
	{
		$currentparentcommentid = $currentcomment->comment_parent;
		if ($currentparentcommentid == $parentcommentid)
		{
			$result[] = $currentcomment->comment_ID;
		}
		else
		{
			//echo "nope:";
			//var_dump($currentparentcommentid);
			//var_dump($parentcommentid);
		}
	}
	
	return $result;
}

function nxs_getcommentwithid($allcomments, $commentid)
{	
	$result = null;
	
	foreach($allcomments as $currentcomment) 
	{
		if ($currentcomment->comment_ID == $commentid)
		{
			$result = $currentcomment;
			break;
		}
	}
	
	return $result;
}

// kudos to http://stackoverflow.com/questions/6232846/best-email-validation-function-in-general-and-specific-college-domain
function nxs_isvalidemailaddress($email) 
{
  // First, we check that there's one @ symbol, and that the lengths are right
  if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
      // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
      return false;
  }
  // Split it into sections to make life easier
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
      if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
          return false;
      }
  }
  if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
      $domain_array = explode(".", $email_array[1]);
      if (sizeof($domain_array) < 2) {
          return false; // Not enough parts to domain
      }
      for ($i = 0; $i < sizeof($domain_array); $i++) {
          if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
              return false;
          }
      }
  }

  return true;
}

function nxs_create_guid($namespace = '') 
{
	// credits: http://php.net/manual/en/function.uniqid.php
  static $guid = '';
  $uid = uniqid("", true);
  $data = $namespace;
  $data .= $_SERVER['REQUEST_TIME'];
  $data .= $_SERVER['HTTP_USER_AGENT'];
  $data .= $_SERVER['LOCAL_ADDR'];
  $data .= $_SERVER['LOCAL_PORT'];
  $data .= $_SERVER['REMOTE_ADDR'];
  $data .= $_SERVER['REMOTE_PORT'];
  $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
  $guid = substr($hash,  0,  8) . 
          '-' .
          substr($hash,  8,  4) .
          '-' .
          substr($hash, 12,  4) .
          '-' .
          substr($hash, 16,  4) .
          '-' .
          substr($hash, 20, 12);
  return $guid;
}

function nxs_postwithstatusexistsbyid($postid, $status)
{
	//
	global $wpdb;

  $dbresult = $wpdb->get_results( $wpdb->prepare("
      	SELECT * FROM $wpdb->posts
		where ID = %s and post_status = %s
	", $postid, $status), OBJECT );

	//var_dump($dbresult);

	if (count($dbresult) == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function nxs_postexistsbyid($postid)
{
	global $wpdb;

  $dbresult = $wpdb->get_results( $wpdb->prepare("
      	SELECT 1 FROM $wpdb->posts
		where ID = %s
	", $postid), OBJECT );

	if (count($dbresult) == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function nxs_global_globalidexists($globalid)
{
	if ($globalid == "" || $globalid == "0" || $globalid == "NXS-NULL")
	{
		nxs_webmethod_return_nack("invalid parameter globalid; [{$globalid}]");
	}
	
	$postids = nxs_get_postidsaccordingtoglobalid($globalid);
	if (count($postids) == 0)
	{
		$result = false;
	}
	else
	{
		$result = true;
	}
	
	return $result;
}

// get postidbyglobalid, postid_by_globalid, postid_for_globalid, postidforglobalid
function nxs_get_postidaccordingtoglobalid($globalid)
{
	$postids = nxs_get_postidsaccordingtoglobalid($globalid);
	if (count($postids) != 1)
	{
		nxs_webmethod_return_nack("no, or multiple globalids found;" . count($postids) . " / " . $globalid);
	}
	return $postids[0];
}

function nxs_get_postidsaccordingtoglobalid($globalid)
{
	global $wpdb;

  $dbresult = $wpdb->get_results("
      	SELECT DISTINCT ID FROM $wpdb->posts
		INNER JOIN $wpdb->postmeta ON ID = post_id
		WHERE meta_key = 'nxs_globalid' AND meta_value = '" . $globalid . "' ORDER BY ID ASC", ARRAY_A );

	$result = array();
	foreach ($dbresult as $dbrow)
	{
		$result[] = $dbrow["ID"];
	}

	return $result;
}

function nxs_reset_globalid($postid)
{
	$result = nxs_create_guid();
	return nxs_reset_globalidtovalue($postid, $result);
}

function nxs_reset_globalidtovalue($postid, $globalid)
{
	/*
	if (nxs_isdebug())
	{
		var_dump(nxs_getstacktrace());
		die();
	}
	*/   
	
	$metadatakey = 'nxs_globalid';
	update_post_meta($postid, $metadatakey, nxs_get_backslashescaped($globalid));
	return $globalid;
}

// getglobalid_by_postid,getglobalid_for_postid
function nxs_get_globalid($postid, $createwhennotexists)
{	
	if ($postid == "")
	{
		// valide antwoord
		$result = "NXS-NULL";
	}
	else if ($postid == 0)
	{
		// valide antwoord
		$result = "NXS-NULL";
	}
	else
	{
		$posttype = get_post_type($postid);
		if (!$posttype)
		{
			$result = "NXS-NULL";
		}
		else
		{
			$metadatakey = 'nxs_globalid';
			$result = get_post_meta($postid, $metadatakey, true);
			
			if ($result == "")
			{
				if ($createwhennotexists)
				{
					// globalid was (nog) niet gealloceerd, maar we hebben toestemming om deze dan nu te alloceren
					$result = nxs_reset_globalid($postid);
				}
				else
				{
					//echo "[not found, no permission to update]";
				}
			}
		}
	}
	
	return $result;
}

function nxs_convert_stringwithbracketlist_to_stringwithcommas($stringwithbrackets)
{
	$result = $stringwithbrackets;
	$result = str_replace("[", "", $result);
	$result = str_replace("]", ",", $result);
	$result = trim($result, ",");
	return $result;
}

function nxs_get_globalids_categories($selectedcategoryidsinbrackets)
{
	$result = "";
	$commaseperated = nxs_convert_stringwithbracketlist_to_stringwithcommas($selectedcategoryidsinbrackets);
	foreach (explode(',', $commaseperated) as $categoryid) 
	{
		$name = get_cat_name($categoryid);
		if ($name == "")
		{
			// if its empty, perhaps its a product category instead of "regular" category
			$category = get_term_by('id', $categoryid, 'product_cat', 'ARRAY_A');
			$name = $category['name']; 
		}
		$result = $result . "[" . $name . "]";
	}
	return $result;
}

function nxs_get_localids_categories($globalcatidsinbrackets)
{
	$result = nxs_get_localids_categories_v2($globalcatidsinbrackets);
	return $result["result"];
}

// returns localids for a list of globalcatidsinbrackets,
// for example [foo][bar] will return [{category_id_of_foo}][{category_id_of_bar}]
function nxs_get_localids_categories_v2($globalcatidsinbrackets)
{
	$result = array();
	$result["result"] = "";
	$result["validlocalidsarray"] = array();
	$result["invalidglobalidsarray"] = array();
	$result["validglobalidsbracketstring"] = "";
	$commaseperated = nxs_convert_stringwithbracketlist_to_stringwithcommas($globalcatidsinbrackets);
	foreach (explode(',', $commaseperated) as $globalcatid) 
	{
		$id = get_cat_id($globalcatid);
		if ($id == 0)
		{
			// no category found
			$result["invalidglobalidsarray"][] = $globalcatid;
		}
		else
		{
			$result["result"] .= "[" . $id . "]";
			$result["validlocalidsarray"][] = $id;
			$result["validglobalidsbracketstring"] .= "[" . $globalcatid . "]";
		}
	}
	return $result;
}

function nxs_getplaceholdericonid($placeholdertemplate)
{
 	// inject widget if not already loaded, implements *dsfvjhgsdfkjh*
 	nxs_requirewidget($placeholdertemplate);
	
	$functionnametoinvoke = 'nxs_widgets_' . $placeholdertemplate . '_geticonid';
	if (function_exists($functionnametoinvoke))
	{
		$args = array();
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("function not found;" . $functionnametoinvoke);
	}
	
	return $result;
}

function nxs_getwidgeticonid($widgetname)
{
 	// inject widget if not already loaded, implements *dsfvjhgsdfkjh*
 	nxs_requirewidget($widgetname);
	
	$functionnametoinvoke = 'nxs_widgets_' . $widgetname . '_geticonid';
	if (function_exists($functionnametoinvoke))
	{
		$args = array();
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		$result = "";
	}
	
	return $result;
}

function nxs_getunifiedstylinggroup($widgetname)
{
 	// inject widget if not already loaded, implements *dsfvjhgsdfkjh*
 	nxs_requirewidget($widgetname);
	
	$functionnametoinvoke = 'nxs_widgets_' . $widgetname . '_getunifiedstylinggroup';
	if (function_exists($functionnametoinvoke))
	{
		$args = array();
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		$result = "";
	}
	
	return $result;
}

function nxs_getpostwizardtitle($postwizard)
{
	// inject postwizard
 	nxs_requirepostwizard($postwizard);
	
	// nxs_postwizard_pdt1_renderpreview($args)
	$functionnametoinvoke = 'nxs_postwizard_' . $postwizard . '_gettitle';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("function not found;" . $functionnametoinvoke);
	}
	
	return $result;
}

function nxs_getplaceholdertitle($placeholdertemplate)
{
 	// inject widget if not already loaded, implements *dsfvjhgsdfkjh*
 	$rwr = nxs_requirewidget($placeholdertemplate);
	
	$functionnametoinvoke = 'nxs_widgets_' . $placeholdertemplate . '_gettitle';
	if (function_exists($functionnametoinvoke))
	{
		$args = array();
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("function not found;" . $functionnametoinvoke);
	}
	
	return $result;
}

function nxs_getpagetemplatetitle($pagetemplateid)
{
	// inject pagetemplate if not already loaded, implements *dsfvjhgsdfkjh*
 	nxs_requirepagetemplate($pagetemplateid);
	
	$functionnametoinvoke = 'nxs_pagetemplate_' . $pagetemplateid . '_gettitle';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("function not found;" . $functionnametoinvoke);
	}
	
	return $result;
}

function nxs_append_posttemplate($postid, $pagetemplate)
{
	if ($pagetemplate == "")
	{
		nxs_webmethod_return_nack("pagetemplate is leeg?");
	}
	
	$poststructure = nxs_parsepoststructure($postid);
			
	foreach ($pagetemplate as $pagetemplateitem)
	{
		$pagerowtemplate = $pagetemplateitem["pagerowtemplate"];
		$pagerowid = $pagetemplateitem["pagerowid"];
		$placeholdertemplatestogetherwithargs = $pagetemplateitem["pagerowtemplateinitializationargs"];
			
		$newrow = array();
		$newrow["rowindex"] = "new";
		$newrow["pagerowtemplate"] = $pagerowtemplate;
		$newrow["pagerowid"] = nxs_getrandompagerowid();
		$newrow["pagerowattributes"] = "pagerowtemplate='" . $pagerowtemplate . "' pagerowid='" . $pagerowid . "'";
		$newrow["content"] = nxs_getpagerowtemplatecontent($pagerowtemplate);
	
		$rowindex = count($poststructure);	// begint bij 0 dus wordt altijd ge-append
	
		//echo "inserting at index " . $rowindex;
	
		// insert row into structure
		$updatedpoststructure = nxs_insertarrayindex($poststructure, $newrow, $rowindex);
		
		// persist structure
		$updateresult = nxs_storebinarypoststructure($postid, $updatedpoststructure);
		
		// get the updated structure; should now contain 1 row
		$poststructure = nxs_parsepoststructure($postid);
	
		//echo "poststructure after update:";
		//var_dump($poststructure);
	
		$pagerow = $poststructure[$rowindex];
		
		//echo "pagerow:";	
		//var_dump($pagerow);
		
		$content = $pagerow["content"];
		
		//echo "content:";	
		//var_dump($content);
	
		$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
		
		//echo "placeholderids:";	
		//var_dump($placeholderids);
		
		$placeholderindex = -1;
		foreach ($placeholderids as $placeholderid)
		{	
			$placeholderindex++;
			
			//echo "current placeholderindex:" . $placeholderid;
			
			$args = array();
			
			$args["clientpopupsessioncontext"] = array();	// hierrr
			$args["clientpopupsessioncontext"]["postid"] = $postid;
			$args["clientpopupsessioncontext"]["placeholderid"] = $placeholderid;
			$args["clientpopupsessioncontext"]["placeholdertemplate"] = $placeholdertemplate;
			$args["clientpopupsessioncontext"]["contextprocessor"] = "widgets";
			$args["clientpopupsessioncontext"]["sheet"] = "home";
			
			$args["postid"] = $postid;
			$args["placeholderid"] = $placeholderid;
			$placeholdertemplate = $placeholdertemplatestogetherwithargs[$placeholderindex]["placeholdertemplate"];
			
			$args["placeholdertemplate"] = $placeholdertemplate;
			nxs_initializewidget($args);
			// hierrr
						
			//echo "init finished";
			
			// update de waarden op basis van de argumenten die mee zijn gegeven
			
			$args = array();
			$args["postid"] = $postid;
			$args["placeholderid"] = $placeholderid;
			$args["placeholdertemplate"] = $placeholdertemplate;
			$updatedvalues = $placeholdertemplatestogetherwithargs[$placeholderindex]["args"];
			nxs_mergewidgetmetadata_internal($postid, $placeholderid, $updatedvalues);
		}
	}
	
	//
	// ensure related items (menu's for example) are updated too
	//
	nxs_after_postcontents_updated($postid);
}

function nxs_getthemeversion() 
{
	global $nxs_global_themeversion;
	
	if ($nxs_global_themeversion != "")
	{
		$value = $nxs_global_themeversion;
	}
	else
	{
		if (function_exists("nxs_theme_getmeta"))
		{
			$thememeta = nxs_theme_getmeta();
			$value = $thememeta["version"];
		}
		else
		{
			$value = "0.1.0.0";
		}
		
		$nxs_global_themeversion = $value;
	}
	return $value;
}

// obsolete function; should eventually be removed
function nxs_getthemename()
{
	return nxs_getthemeid();
}

function nxs_getthemeid() 
{
	if (function_exists("nxs_theme_getmeta"))
	{
		$thememeta = nxs_theme_getmeta();
		$value = $thememeta["id"];
	}
	else
	{
		$value = "unknown";
	}
	
	$nxs_global_themeid = $value;

	return $value;
}

function nxs_ispostfound($postid)
{
	global $wpdb;
	
	$q = "
		select 
			* 
		from 
			$wpdb->posts posts
		where	posts.ID = %s
	";

	$posts = $wpdb->get_results( $wpdb->prepare($q, $postid), OBJECT); // ARRAY_A );
	
	$result = false;
	if (count($posts) == 1)
	{
		$result = true;
	}
	return $result;
}

function nxs_getsearchresults($searchargs)
{
	$searchphrase = $searchargs["phrase"];
	$itemsperpage = $searchargs["itemsperpage"];
	if ($itemsperpage == "")
	{
		$itemsperpage = 10;
	}
	
	$currentpage = $searchargs["currentpage"];
	if ($currentpage == "" || $currentpage < 0)
	{
		$currentpage = 0;
	}
	
	$paging_skip = $currentpage * $itemsperpage;
	
	$sitemeta = nxs_getsitemeta();
	
	$skip404postid = $sitemeta["404_postid"];
	if (!isset($skip404postid))
	{
		$skip404postid = -1;
	}
	
	$skipserp_postid = $sitemeta["serp_postid"];
	if (!isset($skipserp_postid))
	{
		$skipserp_postid = -1;
	}
	
	//echo "search...";
	$args = array
	(
		"public" => true,
		"exclude_from_search" => false,
	);
	$output = "names";
	$operator = "and";
	$posttypes = get_post_types($args, $output, $operator);
	$posttypevalues = array_values($posttypes);
	$posttypelist = "'" . implode("','", $posttypevalues) . "'";
	$posttypelist = str_replace("''", "", $posttypelist);
	
	global $wpdb;
	
	$q = "
		select 
			* 
		from 
			$wpdb->posts posts
		where	
		posts.ID NOT in
		(
			" . $skip404postid . "," . $skipserp_postid . "
		) AND
		posts.ID in
		(
			select 
				distinct ID 
			from 
				$wpdb->posts posts
			where 
			(
				(
					post_type in ({$posttypelist}) 
				)
				and
				(
					post_status = 'publish'
				)
				and
				(
					(
						ID in 
						(
							select post_id from $wpdb->postmeta 
							where meta_key like %s 
							and meta_value like %s
						)
					) 
					or
					(
						posts.post_title like %s
					)
					or
					(
						posts.post_content like %s
					)
				)
			)
		)
		ORDER BY
			posts.post_date desc
		LIMIT 
			{$paging_skip},{$itemsperpage}		
	";

	$metafields = 'nxs_ph_%';
	$search = '%' . $searchphrase . '%';
	$posts = $wpdb->get_results( $wpdb->prepare($q, $metafields, $search, $search, $search), OBJECT); // ARRAY_A );
	
	return $posts;
}

function nxs_getpageletid_forpageletinpost($postid, $pageletname)
{
	$postmeta = nxs_get_postmeta($postid);
	
	$key = "pagelets_" . $pageletname . "_pageletid";
	if (array_key_exists($key, $postmeta))
	{
		$result = $postmeta[$key];
	}
	else
	{
		// pageletid is niet expliciet gezet op pagina niveau
		// we nemen de generi
		$result = nxs_getpostid_for_title_and_nxstype($pageletname, 'pagelet');
		if ($result == null || $result == "")
		{
			$postwizard = 'pagelet_default_' . $pageletname;
			$result = nxs_postwizard_createpost_noparameters($pageletname, $pageletname, 'pagelet', $postwizard);
			nxs_setpageletid_forpageletinpost($postid, $pageletname, $result);
		}
		else
		{
			nxs_setpageletid_forpageletinpost($postid, $pageletname, $result);
		}
	}	
	
	// todo: for now we always perform existence check; this could be optimized
	if (!nxs_ispostfound($result))
	{
		$postwizard = 'pagelet_default_' . $pageletname;
		$result = nxs_postwizard_createpost_noparameters($pageletname, $pageletname, 'pagelet', $postwizard);
		nxs_setpageletid_forpageletinpost($postid, $pageletname, $result);
	}
	
	return $result;
}

function nxs_setpageletid_forpageletinpost($postid, $pageletname, $pageletid)
{
	$modifiedmetadata = array();
	$key = "pagelets_" . $pageletname . "_pageletid";
	$modifiedmetadata[$key] = $pageletid;
	$key = "pagelets_" . $pageletname . "_pageletid_globalid";
	$modifiedmetadata[$key] = nxs_get_globalid($pageletid, true);	// global referentie
	nxs_merge_postmeta($postid, $modifiedmetadata);
}

// pretty_print
function nxs_prettyprint_array($arr)
{
	$retStr = '<h1>Pretty print</h1>';
  $retStr = '<ul>';
  if (is_array($arr)){
      foreach ($arr as $key=>$val){
          if (is_array($val))
          {
          	$retStr .= '<li>' . $key . ' => ' . nxs_prettyprint_array($val) . '</li>';
          } 
          else if (is_string($val))
          {
          	$retStr .= '<li>' . $key . ' => ' . $val . '</li>';
          }
          else
          {
          	$type = get_class($val);
          	if ($type === false)
          	{
          		// primitive
          		$retStr .= '<li>' . $key . ' => ' . $val . '</li>';
          	}
          	else
          	{
          		$retStr .= '<li>' . $key . ' => {some object of type ' . $type . ' }</li>';
          	}
          }
      }
  }
  else
  {
  	$retStr .= '<li>Not an array</li>';
  }
  $retStr .= '</ul>';
  return $retStr;
}

function nxs_outputbuffer_popall()
{
	$existingoutput = array();
	
	$numlevels = ob_get_level();
	for ($i = 0; $i < $numlevels; $i++)
	{
		$existingoutput[] = ob_get_clean();
	}
	
	return $existingoutput;
}

// TODO : nxs_webmethod_return_nack should be renamed to nxs_throw_nack
// within this function, 
function nxs_webmethod_return_nack($message)
{
	// cleanup output that was possibly produced before,
	// if we won't this could cause output to not be json compatible
	$existingoutput = nxs_outputbuffer_popall();
	
	http_response_code(500);
	//header($_SERVER['SERVER_PROTOCOL'] . " 500 Internal Server Error");
	//header("Status: 500 Internal Server Error"); // for fast cgi
	
	$output = array
	(
		"result" => "NACK",
		"message" => "Halted; " . $message
	);
	
	if (NXS_DEFINE_NXSDEBUGWEBSERVICES)
	{
		// very practical; the stacktrace and request are returned too,
		// see the js console window to ease the debugging
		$output["outputbeforenack"] = $existingoutput;
		$output["request"] = $_REQUEST;
		$output["stacktrace"] = nxs_getstacktrace();
	}

	if (nxs_is_nxswebservice())
	{
		// system is processing a nxs webmethod; output in json
		$output=json_encode($output);
		echo $output;
	}
	else
	{
		// system is processing regular request; output in text
		echo "<div style='background-color: white; color: black;'>NACK;<br />";
		echo "raw print:<br />";
		var_dump($output);
		echo "pretty print:<br />";
		if ($_REQUEST["pp"] == "false")
		{
			// in some situation the prettyprint can stall
			
		}
		else
		{
			echo "<!-- hint; in case code breaks after this comment, add querystring parameter pp with value false (pp=false) to output in non-pretty format -->";
			echo nxs_prettyprint_array($output);
		}
		echo "<br />raw print:<br />";
		echo "</div>";
	}
	die();
}

function nxs_webmethod_return($args)
{
	if ($args["result"] == "OK")
	{
		nxs_webmethod_return_ok($args);
	}
	else 
	{
		nxs_webmethod_return_nack($args["message"]);
	}
}

// kudos to http://www.anyexample.com/programming/php/how_to_detect_internet_explorer_with_php.xml
function nxs_detect_ie()
{
  if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
  {
    return true;
  }
  else
  {
  	// IE11 works in a slightly different way...
  	if (preg_match("/Trident\/7.0;(.*)rv:11.0/", $_SERVER["HTTP_USER_AGENT"], $match) != 0)
  	{
  		return true;
  	}
  	else
  	{
    	return false;
    }
  }
}

function nxs_set_jsonheader()
{
		// set headers
	if (!nxs_detect_ie())
	{
		if(!headers_sent())
		{
			header('Content-Type: application/json; charset=utf-8');
		}
	}
	else
	{
		// for IE / Internet Explorer, use text/javascript, implements bug 931
		// kudos to http://stackoverflow.com/questions/6114360/stupid-ie-prompts-to-open-or-save-json-result-which-comes-from-server
		if(!headers_sent())
		{
			header('Content-type: text/html');
		}
	}
}

// For 4.3.0 <= PHP <= 5.4.0
if (!function_exists('http_response_code'))
{
  function http_response_code($newcode = NULL)
  {
    static $code = 200;
    if($newcode !== NULL)
    {
      header('X-PHP-Response-Code: '.$newcode, true, $newcode);
      if(!headers_sent())
      {
        $code = $newcode;
      }
    }       
    return $code;
  }
}

function nxs_webmethod_return_ok($args)
{
	if (headers_sent($filename, $linenum)) 
	{
		echo "nxs headers already send; $filename $linenum";
		exit();
	}
	
	$existingoutput = array();
	
	$numlevels = ob_get_level();
	for ($i = 0; $i < $numlevels; $i++)
	{
		$existingoutput[] = nxs_ob_get_clean();
	}
	
	
	
	nxs_set_jsonheader();
	http_response_code(200);



	//header($_SERVER['SERVER_PROTOCOL'] . " 200 OK");
	//header("Status: 200 OK"); // for fast cgi

	if (NXS_DEFINE_NXSDEBUGWEBSERVICES)
	{
		// very practical; the stacktrace and request are returned too,
		// see the js console window to ease the debugging
		$args["outputbeforeok"] = $existingoutput;
		$args["request"] = $_REQUEST;
		$args["stacktrace"] = nxs_getstacktrace();
	}

	// add 'result' to array
	$args["result"] = "OK";
	
	// sanitize malformed utf8 (if the case)
	$args = nxs_array_toutf8string($args);
	
	// in some very rare situations the json_encode
	// can stall/break the execution (see support ticket 13459)
	// if there's weird Unicode characters in the HTML such as (C2 A0)
	// which is a no-break character that is messed up
	// (invoking json_encode on that output would not throw an exception
	// but truly crash the server). To solve that problem, we use the following
	// kudos to:
	// http://stackoverflow.com/questions/12837682/non-breaking-utf-8-0xc2a0-space-and-preg-replace-strange-behaviour
	foreach ($args as $k => $v)
	{
		if (is_string($v))
		{
			$v = preg_replace('~\xc2\xa0~', ' ', $v);
			$args[$k] = $v;
		}
		
		
	}
	
	if ($_REQUEST["nxs_json_output_format"] == "prettyprint")
	{
		// only works in PHP 5.4 and above
		$options = 0;
		$options = $options | JSON_PRETTY_PRINT;
		$output = json_encode($args, $options);
	}
	else
	{
		// important!! the json_encode can return nothing,
		// on some servers, when the 2nd parameter (options),
		// is specified; ticket 22986!
		
	  if ($_REQUEST["aaa"] == "aaa")
		{
			
		}
		
		$output = json_encode($args);
	}
	

	
	echo $output;
	
	exit();
}

function webmethod_return_alternativeflow($altflowid, $args)
{
	nxs_webmethod_return_alternativeflow($altflowid, $args);
}

function nxs_webmethod_return_alternativeflow($altflowid, $args)
{
	nxs_set_jsonheader();
		
	// add 'result' to array
	$args["result"] = "ALTFLOW";
	$args["altflowid"] = $altflowid;
	// sanitize malformed utf8 (if the case)
	$args = nxs_array_toutf8string($args);
	$output=json_encode($args);
	echo $output;
	die();
}

// see http://ottopress.com/2011/tutorial-using-the-wp_filesystem/
function nxs_downloadframeworkversion($version, $url)
{
	// todo: ensure framework is not already in place, if so, complain!
	
	if (!nxs_has_adminpermissions())
	{
		// no access
		echo "Sorry, no access";
		return;
	}
	
	if ($url == "")
	{
		// no access
		echo "Sorry, no access";
		return;
	}

	require_once(ABSPATH . "wp-admin" . "/includes/file.php");

	/**/
	
	global $wp_filesystem;

	// inner functions, required for request_filesystem_credentials
	function screen_icon(){}	
	function submit_button(){	echo '<input name="save" type="submit" value="Save a file" />'; }
	
	if ($_POST["save"] == "")
	{
		$form_fields = array ('save');
		$context = false;
		$nonce = 'theme-upload';
		$url = wp_nonce_url($url, $nonce);
		
		// request_filesystem_credentials($form_post, $type = '', $error = false, $context = false, $extra_fields = null) {
		if (false === ($credentials = request_filesystem_credentials($url, '', false, false, $form_fields)))
		{
			//echo "NXS file system not accessible";
			//die();
		}
		die();
	}

	$credentials = $_POST;
	if (!WP_Filesystem($credentials)) 
	{
		nxs_webmethod_return_nack("NXS file system not accessible (2)");
	}

	if (!is_object($wp_filesystem))
	{
		echo "NXS file system not accessible (3)";
		die();
	}

	if (is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code())
	{
		echo "NXS file system not accessible (4)";
		die();
	}
	
	/**/
	
	$temp_file_addr = download_url($url);
	
	echo "[downloaded file:[";
	var_dump($temp_file_addr);
	echo "]] ";

	if (is_wp_error($temp_file_addr) ) 
	{
		echo "framework update error";
		
		$error = $temp_file_addr->get_error_code();
		if($error == 'http_no_url') 
		{
			//The source file was not found or is invalid
			function nxs_framework_update_missing_source_warning() 
			{
				echo "<div id='source-warning' class='updated fade'><p>Failed: Invalid URL Provided</p></div>";
			}
			add_action( 'admin_notices', 'nxs_framework_update_missing_source_warning' );
		} 
		else 
		{
			function nxs_framework_update_other_upload_warning() 
			{
				echo "<div id='source-warning' class='updated fade'><p>Failed: Upload - $error</p></div>";
			}
			add_action( 'admin_notices', 'nxs_framework_update_other_upload_warning' );
		}
		return;
	}

	// framework update file was downloaded succesfully
	
	global $wp_filesystem;
	
	//$to = NXS_FRAMEWORKPATH . "/";
	$to = TEMPLATEPATH . "/" . NXS_FRAMEWORKNAME . "/" . $version . "/";
	$dounzip = unzip_file($temp_file_addr, $to);

	unlink($temp_file_addr); // Delete Temp File

	if (is_wp_error($dounzip)) 
	{
		echo "framework update error; extract failed";
		
		//DEBUG
		$error = $dounzip->get_error_code();
		$data = $dounzip->get_error_data($error);
		
		echo $error. ' - ';
		print_r($data);

		if($error == 'incompatible_archive') 
		{
			//The source file was not found or is invalid
			function nxs_framework_update_no_archive_warning() 
			{
				echo "<div id='woo-no-archive-warning' class='updated fade'><p>Failed: Incompatible archive</p></div>";
			}
			add_action( 'admin_notices', 'nxs_framework_update_no_archive_warning' );
		}
		else if($error == 'empty_archive') 
		{
			function nxs_framework_update_empty_archive_warning() {
				echo "<div id='woo-empty-archive-warning' class='updated fade'><p>Failed: Empty Archive</p></div>";
			}
			add_action('admin_notices', 'nxs_framework_update_empty_archive_warning');
		}
		else if($error == 'mkdir_failed') 
		{
			function nxs_framework_update_mkdir_warning() 
			{
				echo "<div id='woo-mkdir-warning' class='updated fade'><p>Failed: mkdir Failure, probably the permissions on the folder are write only for user, not for group?</p></div>";
			}
			add_action('admin_notices', 'nxs_framework_update_mkdir_warning');
		}
		else if($error == 'copy_failed') 
		{
			function nxs_framework_update_copy_fail_warning() {
				echo "<div id='woo-copy-fail-warning' class='updated fade'><p>Failed: Copy Failed</p></div>";
			}
			add_action('admin_notices', 'nxs_framework_update_copy_fail_warning');
		}
		else
		{
			var_dump($error);
			die();
		}
		
		echo " [end]";
		die();
		
		return;
	}

	//
	// TODO: update site settings; update the frameworkname and version#
	//

	function nxs_framework_updated_success() 
	{
		echo "<div id='framework-upgraded' class='updated fade'><p>New framework successfully downloaded, extracted and updated.</p></div>";
	}	
	add_action('admin_notices', 'nxs_framework_updated_success');
}

function nxs_getheader($name)
{
	get_header($name);
	require_once(NXS_FRAMEWORKPATH . "/header-$name.php");
}

function nxs_getfooter($name)
{
	get_footer($name);
	require_once(NXS_FRAMEWORKPATH . "/footer-$name.php");
}

function nxs_strleft($s1, $s2) 
{
	return substr($s1, 0, strpos($s1, $s2));
}

function nxs_ishttps()
{
	$result = false;
	if ($_SERVER["HTTPS"] == "on")
	{
		$result = true;
	}
	return $result;
}

function nxs_geturicurrentpage()
{
	// note; the "fragment" part (after "#"), is not available by definition;
	// its something browsers use; its not send to the server (unless some clientside
	// logic does so)
  if(!isset($_SERVER['REQUEST_URI']))
  {
  	$result = $_SERVER['PHP_SELF'];
  }
  else
  {
    $result = $_SERVER['REQUEST_URI'];
  }
  return $result;
}

// current url geturl currentpage currenturl
function nxs_geturlcurrentpage()
{
	// note; the "fragment" part (after "#"), is not available by definition;
	// its something browsers use; its not send to the server (unless some clientside
	// logic does so)
  $serverrequri = nxs_geturicurrentpage();
  $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
  $protocol = nxs_strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
  $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
  return $protocol."://".$_SERVER['HTTP_HOST'].$port.$serverrequri;   
}

function nxs_updateseooption($postid, $key, $val)
{
	if (!defined('WPSEO_PATH'))
	{
		nxs_webmethod_return_nack("please install the WordPress Seo plugin by Yoast");
	}
		
	if (!class_exists ("WPSEO_Metabox"))
	{
		require WPSEO_PATH.'admin/class-metabox.php';
	}
	if (!class_exists ("WPSEO_Admin"))
	{
		require WPSEO_PATH.'admin/class-admin.php';
	}
	
	require_once ABSPATH . 'wp-admin/includes/post.php';
	
	wpseo_set_value($key, $val, $postid);
}

// allocates a new random (but non-existing) pagerowid
// for the specified postid, to be used for a new row
// that will be inserted or appended to the post
function nxs_allocatenewpagerowid($postid)
{
	$random = rand(1, getrandmax());
	
	if ($random == "")
	{
		nxs_webmethod_return_nack("random is empty?! (1)");
	}
	
	return "prid" . $random;
}

function nxs_mergepagerowmetadata_internal($postid, $pagerowid, $updatedvalues)
{
	$behaviourargs = array();
	$behaviourargs["updateunistyle"] = true;
	return nxs_mergepagerowmetadata_internal_v2($postid, $pagerowid, $updatedvalues, $behaviourargs);
}

function nxs_mergepagerowmetadata_internal_v2($postid, $pagerowid, $updatedvalues, $behaviourargs)
{
	$metadatakey = 'nxs_pr_' . $pagerowid;
	$result = array();
	$existing = maybe_unserialize(get_post_meta($postid, $metadatakey, true));
	if ($existing == "")
	{
		// first time the widget is initialized, the meta data is empty(""),
		// in this case we only set the new values and ignore the old one
		$allvalues = $updatedvalues;
	}
	else
	{
		$allvalues = array_merge($existing, $updatedvalues);
	}
	
	//
	// step 1; store the metadata of the row itself
	//
	update_post_meta($postid, $metadatakey, nxs_get_backslashescaped($allvalues));
	
	//
	// step 2; update the metadata of the unistyle
	//
	$unistyle = $allvalues["unistyle"];
	if (isset($unistyle) && $unistyle != "" && $behaviourargs["updateunistyle"] == true)
	{
		$filetobeincluded = NXS_FRAMEWORKPATH . "/nexuscore/row/row.php";
		if (!file_exists($filetobeincluded)) { nxs_webmethod_return_nack("file not found"); }
		require_once($filetobeincluded);
		
		$sheet = "home";
		$args = array();
		$options = nxs_pagerow_home_getoptions($args);
		
		// we store 'all' unistyleable fields (not just the fieldids that are unistyleable, also
		// the derived globalids. To determine which global fields there are, we look over
		// all fields, and we include all ones starting with unistylablefields,
		// for example "foo" and "foo_globalid"; all ones are added, "foo*").
		$unistyleablefields = array();
		$fieldids = nxs_unistyle_getunistyleablefieldids($options);
		foreach ($fieldids as $currentfieldid)
		{
			// find derivations of this field, also the globalids
			foreach ($allvalues as $currentkey => $currentvalue)
			{
				if (nxs_stringstartswith($currentkey, $currentfieldid))
				{
					$unistyleablefields[$currentkey] = $currentvalue;
				}
			}
		}
		
		$unigroup = $options["unifiedstyling"]["group"];
		if (!isset($unigroup) || $unigroup == "") { echo "c) options: "; var_dump($options); nxs_webmethod_return_nack("unigroup not set"); }
		nxs_unistyle_persistunistyle($unigroup, $unistyle, $unistyleablefields);
	}
}

function nxs_overridepagerowmetadata($postid, $pagerowid, $metadata)
{
	if ($postid== "") { nxs_webmethod_return_nack("postid not set (owmd)"); };
	if ($pagerowid== "") { nxs_webmethod_return_nack("pagerowid not set (owmd)"); };
	$metadatakey = 'nxs_pr_' . $pagerowid;
	update_post_meta($postid, $metadatakey, nxs_get_backslashescaped($metadata));
}

//
// checks whether the sitewideelement (for example "header", "footer", 
// "content", or custom value) is set to 'widescreen' or not
//
function nxs_iswidescreen($sitewideelement)
{
	$sitemeta = nxs_getsitemeta();	// note this is cached
	$key = "swe_" . $sitewideelement . "_widescreen";
	if (array_key_exists($key, $sitemeta))
	{
		$result = $sitemeta[$key];
		if ($result == "t")
		{
			$result = true;
		}
		else
		{
			$result = false;
		}
	}
	else
	{
		// set the default behaviour and return it
		$result = false;
		nxs_setwidescreensetting($sitewideelement, "f");
	}
	
	return $result;
}

//
// modifies the widescreensetting for the specified sitewideelement 
// (for example "header", "footer", or custom part) to the value specified
//
function nxs_setwidescreensetting($sitewideelement, $iswidescreen)
{
	$modifiedmetadata = array();
	$key = "swe_" . $sitewideelement . "_widescreen";
	if ($iswidescreen)
	{
		$iswidescreenvalue = "t";
	}
	else
	{
		$iswidescreenvalue = "f";
	}
	$modifiedmetadata[$key] = $iswidescreenvalue;
	nxs_mergesitemeta($modifiedmetadata);
}

// initialized the widget based on the values specified in the option lists
// the args contains postid, placeholderid, etc.
function nxs_widgets_initplaceholderdatageneric_v2($widget, $args)
{
	// widget specific variables
  $initialmetadata = nxs_genericpopup_getinitialoptionsvalues($args);
  
  // enrich global items
  $extendedmetadata = nxs_genericpopup_getderivedglobalmetadata($args, $initialmetadata);
  
  // blend the two
  $blendedmetadata = array_merge($initialmetadata, $extendedmetadata);
  
  // persist the combined result
	nxs_genericpopup_mergedata_internal($blendedmetadata, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_optiontype_getpersistbehaviour($type)
{
	nxs_requirepopup_optiontype($type);
	$functionnametoinvoke = 'nxs_popup_optiontype_' . $type . '_getpersistbehaviour';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke);
	}
	else
	{
		nxs_webmethod_return_nack("missing function name $functionnametoinvoke");
	}
	
	return $result;
}

// get an array of initial option values to use,
// for each option in the options list, 
// taking into consideration the persistbehaviour
function nxs_genericpopup_getinitialoptionsvalues($args)
{
	// get all options for this context
	$options = nxs_genericpopup_getoptions($args);
	$fields = $options["fields"];
	
	$result = array();
	
	// loop over options
  foreach ($fields as $key => $optionvalues) 
  {
  	$type = $optionvalues["type"];
  	$persistbehaviouroftype = nxs_optiontype_getpersistbehaviour($type);
  	if ($persistbehaviouroftype == "writeid")
  	{
	    $id = $optionvalues["id"];
	    if (array_key_exists("initialvalue", $optionvalues))
	    {
	    	// sanity check: the initialvalue should be set unique
		    if (array_key_exists($id, $result))
		    {
		    	nxs_webmethod_return_nack("initial value for $id was already set?");
		    }
	    	$result[$id] = $optionvalues["initialvalue"];
	    }
	    else
	    {
	    	// this structure entry does not have an initial value, we leave it empty
	    }
	  }
	  else if ($persistbehaviouroftype == "readonly")
	  {
	  	// ignore
	  }
	  else
	  {
	  	nxs_webmethod_return_nack("unsupported persistbehaviouroftype '$persistbehaviouroftype' for optiontype '$type'");
	  }
  }
  
  return $result;
}

// gets a list of global metadata based on the keys in the metadata,
// for the popup data specified by the context in the specified args
function nxs_genericpopup_getderivedglobalmetadata($args, $metadata)
{
	$result = array();

	$options = nxs_genericpopup_getoptions($args);
	$fields = $options["fields"];
	
	// loop over each option
  foreach ($fields as $key => $currentoptionvalues) 
  {
  	// get id of the option
    $id = $currentoptionvalues["id"];
    if (array_key_exists($id, $metadata))
    {
    	// the metadata has a value for this $id
    	// delegate behaviour to the specific option (pluggable)
			$type = $currentoptionvalues["type"];
    	nxs_requirepopup_optiontype($type);
			
			$functionnametoinvoke = 'nxs_popup_optiontype_' . $type . '_getitemstoextendbeforepersistoccurs';
			if (function_exists($functionnametoinvoke))
			{
				// extend the parameters 
				$additionalitemstostore = call_user_func($functionnametoinvoke, $currentoptionvalues, $metadata);
				if (count($additionalitemstostore) > 0)
				{
					// append $additionalitemstostore to the temp_array
					// optiontypes can use this function to store _globalids
					$result = array_merge($result, $additionalitemstostore);
				}
				else
				{
					// nothing to add (this will be the case for simplistic values
					// (values that don't use globalids)
				}
			}
			else
			{
				nxs_webmethod_return_nack("missing function name $functionnametoinvoke");
			}
		}
		else
		{
			// if metadata doesn't contain a value for this id,
			// the globalid should not be used
		}
	}
	
	return $result;
}

//
// gets all data from the metadataset that is part of the given metadata
//
function nxs_genericpopup_getunenrichedmetadataforcontext($args)
{
	$result = array();
	
	$options = nxs_genericpopup_getoptions($args);
	$fields = $options["fields"];
	
	// Popup specific variables
  foreach ($fields as $key => $propertiesofcurrentoption) 
  {
    $id = $propertiesofcurrentoption["id"];
    if (array_key_exists($id, $args))
    {
	    $result[$id] = $args[$id];
		}
		else
		{
			// skip, item is not specified
		}
	}
	
	return $result;
}

//
// persists (merges) metadata that has NOT been enriched for the specified widget
// assuming the widget supports options
// note: args contains both the postid, placeholderid as well as the properties
//
function nxs_widgets_mergeunenrichedmetadata($widget, $args)
{
	$postid = $args["postid"];
	$placeholderid = $args["placeholderid"];

	if ($postid == "") { nxs_webmethod_return_nack("postid not set (nxs_widgets_mergeunenrichedmetadata)"); }
 	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid not set"); }

	// determine what data to store	
	$datatomerge = array();
	
	// 1 - data corresponding with the args for each id in the option list
	$metadata = nxs_genericpopup_getunenrichedmetadataforcontext($args);
	$datatomerge = array_merge($datatomerge, $metadata);
	
	// 2 - the globalid counterparts (where available)
	$globalmetadata = nxs_genericpopup_getderivedglobalmetadata($args, $metadata);
	$datatomerge = array_merge($datatomerge, $globalmetadata);
	
	// 3 - it's required to also set the 'type' (used when dragging an item from the toolbox to existing placeholder)
	$datatomerge['type'] = $widget;
	
	// persist the data
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $datatomerge);

	// result
	$result = array();
	$result["result"] = "OK";

	return $result;
}

function nxs_genericpopup_getpersisteddata($args)
{
	// get the context processor (for example "widget@logo" returns "widget",
	// or "row" returns "row")
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	$contextprocessor = $clientpopupsessioncontext["contextprocessor"];
	
	// load the context processor if its not yet loaded
	nxs_requirepopup_contextprocessor($contextprocessor);
	
	// delegate request to the contextprocessor
	$functionnametoinvoke = 'nxs_popup_contextprocessor_' . $contextprocessor . '_getpersisteddata';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("missing function name $functionnametoinvoke");
	}
	
	return $result;
}

//
// merges the specified data for the specified context in $args
// this function assumes the metadata is already enriched such that
// globalid's are set too
//
function nxs_genericpopup_mergedata_internal($metadata, $args)
{
	// delegate request to contextprocessor
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	$contextprocessor = $clientpopupsessioncontext["contextprocessor"];
	
	// load the context processor if its not yet loaded
	nxs_requirepopup_contextprocessor($popup_contextprocessor);
	
	// delegate request to the contextprocessor
	$functionnametoinvoke = 'nxs_popup_contextprocessor_' . $popup_contextprocessor . '_mergedata_internal';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $metadata, $args);
	}
	else
	{
		nxs_webmethod_return_nack("missing function name $functionnametoinvoke");
	}
	
	return $result;
}

function nxs_genericpopup_getpopuphtml($args)
{
	if (nxs_genericpopup_supportsoptions($args))
	{
		$result = nxs_genericpopup_getpopuphtml_basedonoptions($args);
	}
	else
	{
		// this popupcontext does not support "options",
		// delegate the implementation to the custom implementation
		$result = nxs_genericpopup_getpopuphtml_basedoncustomimplementation($args);
	}
	
	return $result;
}

/* LOCALIZATION */

/*
function nxs_localization_getoptions()
{
	$result = array
	(
		"nl" => array
		(
			"key" => "val"
		),
		"en" => array
		(
			"key" => "val"
		),
	);
	return $result;
}
*/

function nxs_localization_isactivated()
{
	$result = false;
	
	if ($_SERVER["HTTP_HOST"] == "nexusthemes.com")
	{
		$result = true;
	}
	else if ($_SERVER["HTTP_HOST"] == "nl.nexusthemes.com")
	{
		$result = true;
	}
	/*
	else if (nxs_stringcontains($_SERVER["HTTP_HOST"], "10.0.160"))
	{
		$result = true;
	}
	*/
	
	return $result;
}

// returns the foreign languages (this is the list EXCLUDING the native one)
function nxs_localization_getsupportedforeignlanguages()
{
	$result = array();
	return $result;
	/*
	if ($_SERVER["HTTP_HOST"] == "nexusthemes.com")
	{
		$result = array("nl");
	}
	else if ($_SERVER["HTTP_HOST"] == "nl.nexusthemes.com")
	{
		$result = array("nl");
	}
	else if ($_SERVER["HTTP_HOST"] == "10.0.160.89")
	{
		$result = array("nl");
	}
	else
	{
		$result = array();
	}
	// todo: retrieve using nxs site properties
	// $result = array("nl");
	
	return $result;
	*/
}

function nxs_localization_usenativelanguage()
{
	$currentlanguage = nxs_localization_getcurrent_hl_language();
	
	if ($currentlanguage == "" || $currentlanguage == "en")
	{
		$result = true;
	}
	else
	{
		$result = false;
	}
	
	/*
	$foreignlanguages = nxs_localization_getsupportedforeignlanguages();
	if (in_array($currentlanguage, $foreignlanguages))
	{
		$result = false;
	}
	else
	{
		$result = true;
	}
	*/
	return $result;
}

function nxs_localization_getcurrent_hl_language()
{
	// returns "" when native language is active,
	// or the specific foreign language otherwise
	$result = "en";
	
	if ($_SERVER["HTTP_HOST"] == "nl.nexusthemes.com")
	{
		$result = "nl";
	}
	
	if (nxs_has_adminpermissions())
	{
		// using non-native language is only available for administrators
		if (isset($_COOKIE["nxs_cookie_hl"]))
		{
			$result = $_COOKIE["nxs_cookie_hl"];
		}
		
		if (isset($_REQUEST["hl"]))
		{
			$result = $_REQUEST["hl"];
		}
		else
		{
			if (isset($_REQUEST["clientqueryparameters"]["hl"]))
			{
				$result = $_REQUEST["clientqueryparameters"]["hl"];
			}
		}
	}
	
	return $result;
}

function nxs_localization_getblogidforlanguage($lang)
{
	if ($_SERVER["HTTP_HOST"] == "nexusthemes.com")
	{
		if ($lang == "nl")
		{
			$result = 25;
		}
	}
	else
	{
		nxs_webmethod_return_nack("error; language not (yet) supported");
	}
	return $result;
}

function nxs_localization_getlocalizedsitetype()
{
	if ($_SERVER["HTTP_HOST"] == "nexusthemes.com")
	{
		$result = "master";
	}
	else if ($_SERVER["HTTP_HOST"] == "nl.nexusthemes.com")
	{
		$result = "slave";
	}
	else
	{
		$result = "unknown";
	}
	return $result;
}

// kudos to http://wpsnipp.com/index.php/functions-php/get-all-post-meta-data/
function nxs_get_post_meta_all($post_id)
{
	$result = array();
	
	$keys = get_post_custom_keys($post_id);
	foreach ($keys as $current_key)
	{
		$currentvalue = get_post_meta($post_id, $current_key, true);	// deserialize if its an object
		$result[$current_key] = $currentvalue;
	}
	
	return $result;
}

function nxs_resetthumbnaildimensions()
{
	// set dimensions thumbnails
	update_option('thumbnail_size_w', 82);
	update_option('thumbnail_size_h', 82);
	update_option('thumbnail_crop', 1);			// cropping
}

// cleans up images not found
// sometimes the attachment (post) is still there,
// but the actual image no longer exists,
// in which case it simply means the attachment is useless, slowing down the system
function nxs_cleanimg()
{
		
	// Load API
	require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
	require_once ABSPATH . 'wp-admin/includes/import.php';
	require_once ABSPATH . 'wp-admin/includes/post.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	
	$imageidsinsite = nxs_get_images_in_site();

	//echo "<br />Total set of unique image ids:";
	//var_dump($imageidsinsite);
	
	// LOOP; media files
	foreach ($imageidsinsite as $origpostid)
	{
		if ($origpostid === 0 || $origpostid === '0')
		{
			continue;
		}
		echo $origpostid . "/";
		
		$image = get_post($origpostid);
		
		$posttype = get_post_type($origpostid);
		if ($image && 'attachment' == $image->post_type && 'image/' == substr( $image->post_mime_type, 0, 6 ) )
		{
			if (in_array($origpostid, $imageidsinsite))
			{
				// echo "exists, and is in use";
				
				$sourceattachedfile = get_attached_file($origpostid);	// will look like for example /opt/bitnami/apps/wordpress/htdocs/wp-content/blogs.dir/87/files/2013/08/banner11.jpg
				if (file_exists($sourceattachedfile))
				{
					// good, source file is there
					
					$metadata = wp_generate_attachment_metadata($origpostid, $sourceattachedfile );
					wp_update_attachment_metadata($origpostid, $metadata);
					echo "<br />regenerated imgs for postid:" . $origpostid . "<br />";
				}
				else
				{
					echo "<br />file not found:" . $sourceattachedfile . "; we should delete postid:" . $origpostid . "<br />";
					wp_delete_post($origpostid, true);
				}
				
			}
			else
			{
				echo "Deleting image {$origpostid}, its not in use";
				wp_delete_post($origpostid, true);
			}
			
			
		}
		else
		{
			//
			echo "wat is dit?";
			
		}
	}
	
	echo "end of cleanup img";
	die();
}

function nxs_localization_getlocalizablefieldids($options)
{
	if (!nxs_localization_isactivated())
	{
		return array();
	}
	
	$result = array();
	
	$fields = $options["fields"];
  foreach ($fields as $key => $optionvalues) 
  {
  	$localizablefield = $optionvalues["localizablefield"];
  	if (isset($localizablefield) && $localizablefield === true)
  	{
  		$result[] = $optionvalues["id"];
  	}
  }
  
  return $result;
}

/* COLOR PALETTE */

function nxs_colorization_hextorgb($hex) 
{
	$hex = str_replace("#", "", $hex);
	
	if(strlen($hex) == 3) 
	{
	  $r = hexdec($hex[0].$hex[0]);
	  $g = hexdec($hex[1].$hex[1]);
	  $b = hexdec($hex[2].$hex[2]);
	} 
	else if(strlen($hex) == 6)
	{
	  $r = hexdec($hex[0].$hex[1]);
	  $g = hexdec($hex[2].$hex[3]);
	  $b = hexdec($hex[4].$hex[5]);
	}
	else
	{
		nxs_webmethod_return_nack("unsupported input? $hex (expected 3 or 6 chars, p.e. FFFFFF)");
	}

	$result= array
	(
		"r" => $r, 
		"g" => $g,
		"b" => $b
	); 
	return $result;
}

function nxs_colorization_rgbtohsl($rgb)
{
	$r = $rgb["r"] / 255;
	$g = $rgb["g"] / 255;
	$b = $rgb["b"] / 255;
	
  $max = max($r, $g, $b);
  $min = min($r, $g, $b);
  $h = $s = $l = ($max + $min) / 2;

  if($max == $min)
  {
    $h = $s = 0; // achromatic
  }
  else
  {
  	$d = $max - $min;
    $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
    switch($max)
    {
      case $r: 
      	$h = ($g - $b) / $d + ($g < $b ? 6 : 0); 
      	break;
      case $g: 
      	$h = ($b - $r) / $d + 2; 
      	break;
      case $b: 
      	$h = ($r - $g) / $d + 4; 
      	break;
    }
    $h /= 6;
  }
  
  $result = array
  (
  	"h" => $h,
  	"s" => $s,
  	"l" => $l,
  );

  return $result;
}

// get list of possible unistyle names that can be selected
// for a speficic group
// for example will return the names "foo" and "bar" for "textwidget"
function nxs_colorization_getpalettenames($addnonefirstelement)
{
	if ($addnonefirstelement)
	{
		$result = array("@@@nxsempty@@@"=>"none");
	}
	
	$sitemeta = nxs_getsitemeta();
	$metakeystart = "colorization_palette_";
	
	foreach ($sitemeta as $currentkey => $currentval)
	{
		if (nxs_stringstartswith($currentkey, $metakeystart))
		{
			$currentname = nxs_stringreplacefirst($currentkey, $metakeystart, "");
			if ($currentname != "")
			{
				$result[$currentname] = $currentname;
			}
		}
	}
	
	// sort
	ksort($result);
	
	return $result;
}

// Function to render color palette
function nxs_colorization_renderpalette($palettename)
{
	$colors = nxs_getcolorsinpalette();
	$colorprops = nxs_colorization_getpersistedcolorizationproperties($palettename);
	
	echo '<ul class="nxs-float-left">';
															
	foreach ($colors as $currentcolor){
		if ($currentcolor == "base"){
			// the 'base' color is the same for all ("black"), so skip it!
			continue;
		}
		
		// skip
		$key = "colorvalue_" . $currentcolor . "2";
		$hexcolorval = $colorprops[$key];
		
		echo '<li class="miniColors-trigger" style="background-color: ' . $hexcolorval . ';"></li>';
	}	
	
	echo '</ul>';
	
}

function nxs_colorization_getactivepalettename()
{
	$sitemeta = nxs_getsitemeta();
	$metakey = "colorization_activepalette";

	$result = $sitemeta[$metakey];
	
	return $result;
}

function nxs_colorization_setactivepalettename($palettename)
{
	// ensure palettename exists
	if (!nxs_colorization_paletteexists($palettename))
	{
		nxs_webmethod_return_nack("cannot activate palette; palette does not exist?" . $palettename);
	}
	
	$metakey = "colorization_activepalette";
	
	$metadata = array();
	$metadata[$metakey] = $palettename;

	nxs_mergesitemeta($metadata);
}

// helper function
function nxs_colorization_getactivepalettecolorizationproperties()
{
	$palettename = nxs_colorization_getactivepalettename();
	return nxs_colorization_getpersistedcolorizationproperties($palettename);
}

function nxs_colorization_getpersistedcolorizationproperties($palettename)
{
	$sitemeta = nxs_getsitemeta();
	$metakey = "colorization_palette_" . $palettename;

	$result = $sitemeta[$metakey];
	
	return $result;
}

function nxs_colorization_paletteexists($palettename)
{
	$result = false;
	$sitemeta = nxs_getsitemeta();
	$metakey = "colorization_palette_" . $palettename;
	if (isset($sitemeta[$metakey]))
	{
		$result = true;
	}
	return $result;
}

function nxs_colorization_getunallocatedpalettename($palettename)
{
	$result = "";
	$triesleft = 100;
	while ($triesleft > 0)
	{
		$triesleft = $triesleft - 1;
		$randomnummer = rand(1000000, 9999999) . 'RND';
		if (!nxs_colorization_paletteexists($randomnummer))
		{
			$result = $randomnummer;
			break;	// break while
		}
	}
	
	if ($result == "")
	{
		nxs_webmethod_return_nack("error; unable to allocate a free palette name");
	}	
	
	return $result;
}

function nxs_colorization_persistcolorizationproperties($palettename, $colorizationproperties)
{
	$metakey = "colorization_palette_" . $palettename;
	
	$metadata = array();
	$metadata[$metakey] = $colorizationproperties;

	nxs_mergesitemeta($metadata);
}

function nxs_colorization_deletepalettename($palettename)
{
	$metakey = "colorization_palette_" . $palettename;
	nxs_wipe_sitemetakey_internal($metakey, true);
}

function nxs_colorization_renamepalettename($oldname, $newname)
{
	$oldproperties = nxs_colorization_getpersistedcolorizationproperties($oldname);
	nxs_colorization_persistcolorizationproperties($newname, $oldproperties);
	
	$activepalettename = nxs_colorization_getactivepalettename();
	if ($activepalettename == $oldname)
	{
		// update active palette name too
		nxs_colorization_setactivepalettename($newname);
	}
	
	// wipe the old name
	nxs_colorization_deletepalettename($oldname);
}

/* UNISTYLING */

// get list of possible unistyle names that can be selected
// for a speficic group
// for example will return the names "foo" and "bar" for "textwidget"
function nxs_unistyle_getunistylenames($group)
{
	$result = array("@@@nxsempty@@@"=>"none");
	
	$sitemeta = nxs_getsitemeta();
	$metakeystart = "unistyle_" . $group . "_";
	
	foreach ($sitemeta as $currentkey => $currentval)
	{
		if (nxs_stringstartswith($currentkey, $metakeystart))
		{
			$currentname = nxs_stringreplacefirst($currentkey, $metakeystart, "");
			$result[$currentname] = $currentname;
		}
	}
	
	// sort
	ksort($result);
	
	return $result;
}

function nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup)
{
	$unistyle = nxs_unistyle_getdefaultname($unistylegroup);
	$args['unistyle'] = $unistyle;
	if (isset($unistyle) && $unistyle != "") 
	{
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties($unistylegroup, $unistyle);
		$args = array_merge($args, $unistyleproperties);
	}
	return $args;
}

// get list of stored unistyle keys and values connected to
// the specified unistylename for the specified set of (widget) options
// for example will return the styles key/values for the "foo" textwidget.
function nxs_unistyle_getpersistedunistylablefieldsandvalues($options, $unistylename)
{
	$group = $options["unifiedstyling"]["group"];
	if (!isset($group) || $group == "")
	{
		var_dump($options);
		nxs_webmethod_return_nack("error; no unifiedstyling group found in options");
	}
	
	$sitemeta = nxs_getsitemeta();
	$metakey = "unistyle_" . $group . "_" . $unistylename;	

	$result = $sitemeta[$metakey];
	
	return $result;
}

// returns a set of all groups available in the site
// for example will return "textwidget" and "signpostwidget"
function nxs_unistyle_getgroups()
{
	$result = array();

	// set 1	
	$phtargs = array();
	$phtargs["invoker"] = "getunistyles";
	$phtargs["nxsposttype"] = "post";
	$widgetsset1 = nxs_getwidgets($phtargs);

	// set 2
	$phtargs = array();
	$phtargs["invoker"] = "getunistyles";
	$phtargs["nxsposttype"] = "post";
	$phtargs["pagetemplate"] = "pagedecorator";
	$widgetsset2 = nxs_getwidgets($phtargs);
	
	$widgets = array();
	$widgets = array_merge($widgets, $widgetsset1);
	$widgets = array_merge($widgets, $widgetsset2);
	
	foreach ($widgets as $currentwidget)
	{
		$currentwidgetid = $currentwidget["widgetid"];
		$group = nxs_unistyle_getunifiedstylinggroup($currentwidgetid);
		if ($group != "")
		{
			$result[] = $group;
		}
	}
	
	// set 3
	// also include the "row" group
	$result[] = nxs_row_getunifiedstylinggroup();
	
	// some widgets share the same name; we don't want duplicates, so make distinct list
	$result = array_unique($result);
	
	// sort
	ksort($result);

	return $result;
}

// removes a unistyle with the specified name in the specified group,
// and will also "void" the unistyle setting of all widgets in the entire site 
// that used this unistyle name. This function is used in unistyle management.
// for example will delete the unistyle name "main" in group "textwidget", and 
// all references to this unistyle.
function nxs_unistyle_deleteunistyle($group, $name)
{
	if (!isset($group) || $group == "") { nxs_webmethod_return_nack("group is not set"); }
	if (!isset($name) || $name == "" || $name == "@@@nxsempty@@@") { nxs_webmethod_return_nack("name is not set"); }
	
	global $wpdb;
	// rename the unistyle of all widgets on all posts having a nxs structure
	$q = "select ID postid from $wpdb->posts";
	$postids = $wpdb->get_results($q, ARRAY_A);
	
	foreach ($postids as $currentrow)
	{
		$currentpostid = $currentrow["postid"];
		
		// step 1; cleanup unistyles used in widgets
		$placeholderidstometadatainpost = nxs_getwidgetsmetadatainpost($currentpostid);
		foreach ($placeholderidstometadatainpost as $currentplaceholderid => $currentmetadata)
		{
			$currentwidgetid = $currentmetadata["type"];
			$currentgroup = nxs_unistyle_getunifiedstylinggroup($currentwidgetid);
			if ($currentgroup == $group)
			{
				if ($currentmetadata["unistyle"] == $name)
				{
					// update the unistyle for this widget
					$datatomerge = array();
				 	$datatomerge["unistyle"] = "";
					nxs_mergewidgetmetadata_internal($currentpostid, $currentplaceholderid, $datatomerge);
				}
			}
			
		}
		
		// step 2; TODO: cleanup unistyles used in rows
		//
	}
	
	// remove the old one
	nxs_unistyle_wipeunistyle_internal($group, $name);
}

// removes a unistyle with the specified name in the specified group,
// WITHOUT taking into consideration the voiding of the usage of this unistyle.
// so if there's still widgets using the unistyle, their pointer will be 
// invalid. See nxs_unistyle_deleteunistyle to also have the references be voided.
function nxs_unistyle_wipeunistyle_internal($group, $name)
{
	if (!isset($group) || $group == "") { nxs_webmethod_return_nack("group is not set"); }
	if (!isset($name) || $name == "" || $name == "@@@nxsempty@@@") { nxs_webmethod_return_nack("name is not set"); }
	
	$metakey = "unistyle_" . $group . "_" . $name;	

	nxs_wipe_sitemetakey_internal($metakey, true);
}

// renames the unistyle name from old to new name, and also updates the references
// in the widgets, pointing to the unistyle.
function nxs_unistyle_renameunistyle($group, $oldname, $newname)
{
	if (!isset($group) || $group == "") { nxs_webmethod_return_nack("group is not set"); }
	if (!isset($oldname) || $oldname == "" || $oldname == "@@@nxsempty@@@") { nxs_webmethod_return_nack("oldname is not set"); }
	if (!isset($newname) || $newname == "" || $newname == "@@@nxsempty@@@") { nxs_webmethod_return_nack("newname is not set"); }
	if (!nxs_unistyle_exists($group, $oldname)) { nxs_webmethod_return_nack("oldname unistyle not found"); }
	if (nxs_unistyle_exists($group, $newname)) { nxs_webmethod_return_nack("newname unistyle already exists"); }
	
	// clone the styleproperties
	$styleproperties = nxs_unistyle_getunistyleproperties($group, $oldname);
	nxs_unistyle_persistunistyle($group, $newname, $styleproperties);
	
	global $wpdb;
	// rename the unistyle of all widgets on all posts having a nxs structure
	$q = "select ID postid from $wpdb->posts";
	$postids = $wpdb->get_results($q, ARRAY_A);
	foreach ($postids as $currentrow)
	{
		$currentpostid = $currentrow["postid"];
		
		// step 1; rename the unistyles in the metadata of widgets in this post
		$placeholderidstometadatainpost = nxs_getwidgetsmetadatainpost($currentpostid);
		foreach ($placeholderidstometadatainpost as $currentplaceholderid => $currentmetadata)
		{
			$currentwidgetid = $currentmetadata["type"];
			$currentgroup = nxs_unistyle_getunifiedstylinggroup($currentwidgetid);
			if ($currentgroup == $group)
			{
				if ($currentmetadata["unistyle"] == $oldname)
				{
					// update the unistyle for this widget
					$datatomerge = array();
				 	$datatomerge["unistyle"] = $newname;
					nxs_mergewidgetmetadata_internal($currentpostid, $currentplaceholderid, $datatomerge);
				}
			}
		}
		
		// step 2; TODO: rename the unistyles in the metadta of rows in this post
		//
	}
	
	// remove the old one
	nxs_unistyle_wipeunistyle_internal($group, $oldname);
}

// persists the specified list of styleproperties for the unistyle name in the prefi
// for example, persists key foo=bar for unistyle "main" for the "textwidget" group
function nxs_unistyle_persistunistyle($group, $name, $styleproperties)
{
	if (!isset($group) || $group == "") { nxs_webmethod_return_nack("group is not set"); }
	if (!isset($name) || $name == "" || $name == "@@@nxsempty@@@") { nxs_webmethod_return_nack("name is not set"); }
	
	$metakey = "unistyle_" . $group . "_" . $name;	

	$metadata = array();
	$metadata[$metakey] = $styleproperties;

	nxs_mergesitemeta($metadata);
}

// returns the default unistyle to use for this widget
function nxs_unistyle_getdefaultname($group)
{
	$name = "main";
	if (nxs_unistyle_exists($group, $name))
	{
		$result = $name;
	}
	else
	{
		$result = "";
	}
	// todo: add filter to enable plugins/themes to override the default
	return $result;
}

// determines if there is already a unistyle name used in the 
// unistyle group.
function nxs_unistyle_exists($group, $name)
{
	$result = false;
	$unistyle = nxs_unistyle_getunistyleproperties($group, $name);
	if (count($unistyle) > 0)
	{
		$result = true;
	}
	return $result;
}

function nxs_unistyle_getunistyleproperties($group, $name)
{
	if (!isset($group) || $group == "")
	{
		nxs_webmethod_return_nack("group is not set (1)");
	}
	if (!isset($name) || $name == "")
	{
		nxs_webmethod_return_nack("name is not set");
	}
	
	$sitemeta = nxs_getsitemeta();
	$metakey = "unistyle_" . $group . "_" . $name;
	$result = $sitemeta[$metakey];
	
	if (!isset($result))
	{
		$result = array();
	}
	
	return $result;
}

function nxs_unistyle_getunistyleablefieldids($options)
{
	$result = array();
	
	$fields = $options["fields"];
  foreach ($fields as $key => $optionvalues) 
  {
  	$unistylablefield = $optionvalues["unistylablefield"];
  	if (isset($unistylablefield) && $unistylablefield === true)
  	{
  		$result[] = $optionvalues["id"];
  	}
  }
  
  return $result;
}

function nxs_unistyle_containsatleastoneunistyleablefield($options)
{
	$result = false;
	$unistyleablefieldids = nxs_unistyle_getunistyleablefieldids($options);
	if (count($unistyleablefieldids) > 0)
	{
		$result = true;
	}
	return $result;
}

function nxs_unistyle_getunifiedstylinggroup($widgetid)
{
	nxs_requirewidget($widgetid);
	
	$functionnametoinvoke = "nxs_widgets_{$widgetid}_getunifiedstylinggroup";
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke);
	}
	else
	{
		$result = "";
	}
	return $result;
}

// -------------

/* DICTIONARY */

function nxs_lookuptable_persist($lookuptable)
{
	if (!isset($lookuptable) || $lookuptable == "") { nxs_webmethod_return_nack("lookuptable is not set"); }
	
	$metakey = "lookuptable";

	$metadata = array();
	$metadata[$metakey] = $lookuptable;

	nxs_mergesitemeta($metadata);
}

function nxs_lookuptable_getlookup()
{
	$includeruntimeitems = false;
	$result = nxs_lookuptable_getlookup_v2($includeruntimeitems);
	return $result;
}

function nxs_lookuptable_getlookup_v2($includeruntimeitems)
{
	$sitemeta = nxs_getsitemeta();
	$metakey = "lookuptable";
	$result = $sitemeta[$metakey];
	
	if (!isset($result))
	{
		$result = array();
	}
	
	if ($includeruntimeitems)
	{
		$result["currentyear"] = date("Y");
	}
	
	return $result;
}

function nxs_lookuptable_setlookupvalueforkey($key, $value)
{
	$lookuptable = nxs_lookuptable_getlookup();
	// set, or override
	$lookuptable[$key] = $value;
	
	nxs_lookuptable_persist($lookuptable);
}

function nxs_lookuptable_getlookupvalueforkey($key)
{
	$lookuptable = nxs_lookuptable_getlookup();
	return $lookuptable[$key];
}

function nxs_lookuptable_deletekey($key)
{
	$lookuptable = nxs_lookuptable_getlookup();
	unset($lookuptable[$key]);
	nxs_lookuptable_persist($lookuptable);
}

// -------------

function nxs_get_referringpageurl()
{
	$requestp = $_REQUEST["nxsrefurlspecial"];
	$result = urldecode($requestp);
	
	return $result;
}

function nxs_render_backbutton()
{
	if (nxs_has_adminpermissions())
	{
		if ($_REQUEST["nxsrefurlspecial"] != "") 
		{
			// two-step
			$url = nxs_get_referringpageurl();
			$url2 = base64_decode($url);
			?>
			<a href='<?php echo $url2; ?>' class='nxsbutton nxs-float-right'>OK</a>
			<?php 
		} 
		else
		{
			?>
			<a href='<?php echo nxs_geturl_home(); ?>' class='nxsbutton nxs-float-right'>OK</a>
			<?php
		}
	}
}

// -------------

/* UNICONTENTING */

// get list of possible unicontent names that can be selected
// for a speficic group
// for example will return the names "foo" and "bar" for "textwidget"
function nxs_unicontent_getunicontentnames($group)
{
	$result = array("@@@nxsempty@@@"=>"none");
	
	$sitemeta = nxs_getsitemeta();
	$metakeystart = "unicontent_" . $group . "_";
	
	foreach ($sitemeta as $currentkey => $currentval)
	{
		if (nxs_stringstartswith($currentkey, $metakeystart))
		{
			$currentname = nxs_stringreplacefirst($currentkey, $metakeystart, "");
			$result[$currentname] = $currentname;
		}
	}
	
	// sort
	ksort($result);
	
	return $result;
}

// get list of stored unicontent keys and values connected to
// the specified unicontentname for the specified set of (widget) options
// for example will return the content key/values for the "foo" textwidget.
function nxs_unicontent_getpersistedunicontentablefieldsandvalues($options, $unicontentname)
{
	$group = $options["unifiedcontent"]["group"];
	if (!isset($group) || $group == "")
	{
		var_dump($options);
		nxs_webmethod_return_nack("error; no unifiedcontent group found in options");
	}
	
	$sitemeta = nxs_getsitemeta();
	$metakey = "unicontent_" . $group . "_" . $unicontentname;	

	$result = $sitemeta[$metakey];
	
	return $result;
}

// returns a set of all groups available in the site
// for example will return "textwidget" and "signpostwidget"
function nxs_unicontent_getgroups()
{	
	$result = array();
	
	$phtargs = array();
	$phtargs["invoker"] = "getunicontents";
	//$phtargs["wpposttype"] = $wpposttype;
	$phtargs["nxsposttype"] = "post";
	//$phtargs["nxssubposttype"] = $nxssubposttype;
	
	$phtargs["pagetemplate"] = $pagetemplate;
	
	$widgets = nxs_getwidgets($phtargs);
	
	foreach ($widgets as $currentwidget)
	{
		$currentwidgetid = $currentwidget["widgetid"];
		$group = nxs_unicontent_getunifiedcontentgroup($currentwidgetid);
		if ($group != "")
		{		
			$result[] = $group;
		}
	}
	
	// make distinct list (some widgets share the same name)
	$result = array_unique($result);
	
	// sort
	ksort($result);

	
	return $result;
}

// removes a unicontent with the specified name in the specified group,
// and will also "void" the unicontent setting of all widgets in the entire site 
// that used this unicontent name. This function is used in unicontent management.
// for example will delete the unicontent name "main" in group "textwidget", and 
// all references to this unicontent.
function nxs_unicontent_deleteunicontent($group, $name)
{
	if (!isset($group) || $group == "") { nxs_webmethod_return_nack("group is not set"); }
	if (!isset($name) || $name == "" || $name == "@@@nxsempty@@@") { nxs_webmethod_return_nack("name is not set"); }
	if (!nxs_unicontent_exists($group, $name)) { nxs_webmethod_return_nack("name unicontent not found ([$group] [$name])"); }
	
	global $wpdb;
	// rename the unicontent of all widgets on all posts having a nxs structure
	$q = "select ID postid from $wpdb->posts";
	$postids = $wpdb->get_results($q, ARRAY_A);
	
	foreach ($postids as $currentrow)
	{
		$currentpostid = $currentrow["postid"];
		$placeholderidstometadatainpost = nxs_getwidgetsmetadatainpost($currentpostid);
		foreach ($placeholderidstometadatainpost as $currentplaceholderid => $currentmetadata)
		{
			$currentwidgetid = $currentmetadata["type"];
			$currentgroup = nxs_unicontent_getunifiedcontentgroup($currentwidgetid);
			if ($currentgroup == $group)
			{
				if ($currentmetadata["unicontent"] == $name)
				{
					// update the unicontent for this widget
					$datatomerge = array();
				 	$datatomerge["unicontent"] = "";
					nxs_mergewidgetmetadata_internal($currentpostid, $currentplaceholderid, $datatomerge);
				}
			}
		}
	}
	
	// remove the old one
	nxs_unicontent_wipeunicontent_internal($group, $name);
}

// removes a unicontent with the specified name in the specified group,
// WITHOUT taking into consideration the voiding of the usage of this unicontent.
// so if there's still widgets using the unicontent, their pointer will be 
// invalid. See nxs_unicontent_deleteunicontent to also have the references be voided.
function nxs_unicontent_wipeunicontent_internal($group, $name)
{
	if (!isset($group) || $group == "") { nxs_webmethod_return_nack("group is not set"); }
	if (!isset($name) || $name == "" || $name == "@@@nxsempty@@@") { nxs_webmethod_return_nack("name is not set"); }
	if (!nxs_unicontent_exists($group, $name)) { nxs_webmethod_return_nack("unicontent not found"); }
	
	$metakey = "unicontent_" . $group . "_" . $name;	

	nxs_wipe_sitemetakey_internal($metakey, true);
}

// renames the unicontent name from old to new name, and also updates the references
// in the widgets, pointing to the unicontent.
function nxs_unicontent_renameunicontent($group, $oldname, $newname)
{
	if (!isset($group) || $group == "") { nxs_webmethod_return_nack("group is not set"); }
	if (!isset($oldname) || $oldname == "" || $oldname == "@@@nxsempty@@@") { nxs_webmethod_return_nack("oldname is not set"); }
	if (!isset($newname) || $newname == "" || $newname == "@@@nxsempty@@@") { nxs_webmethod_return_nack("newname is not set"); }
	if (!nxs_unicontent_exists($group, $oldname)) { nxs_webmethod_return_nack("oldname unicontent not found"); }
	if (nxs_unicontent_exists($group, $newname)) { nxs_webmethod_return_nack("newname unicontent already exists"); }
	
	// clone the contentproperties
	$contentproperties = nxs_unicontent_getunicontentproperties($group, $oldname);
	nxs_unicontent_persistunicontent($group, $newname, $contentproperties);
	
	global $wpdb;
	// rename the unicontent of all widgets on all posts having a nxs structure
	$q = "select ID postid from $wpdb->posts";
	$postids = $wpdb->get_results($q, ARRAY_A);
	foreach ($postids as $currentrow)
	{
		$currentpostid = $currentrow["postid"];
		$placeholderidstometadatainpost = nxs_getwidgetsmetadatainpost($currentpostid);
		
		//var_dump($placeholderidstometadatainpost);
		
		foreach ($placeholderidstometadatainpost as $currentplaceholderid => $currentmetadata)
		{
			$currentwidgetid = $currentmetadata["type"];
			$currentgroup = nxs_unicontent_getunifiedcontentgroup($currentwidgetid);
			if ($currentgroup == $group)
			{
				if ($currentmetadata["unicontent"] == $oldname)
				{
					// update the unicontent for this widget
					$datatomerge = array();
				 	$datatomerge["unicontent"] = $newname;
					nxs_mergewidgetmetadata_internal($currentpostid, $currentplaceholderid, $datatomerge);
				}
			}
		}
	}
	
	// remove the old one
	nxs_unicontent_wipeunicontent_internal($group, $oldname);
}

// persists the specified list of contentproperties for the unicontent name in the prefi
// for example, persists key foo=bar for unicontent "main" for the "textwidget" group.
function nxs_unicontent_persistunicontent($group, $name, $contentproperties)
{
	if (!isset($group) || $group == "") { nxs_webmethod_return_nack("group is not set"); }
	if (!isset($name) || $name == "" || $name == "@@@nxsempty@@@") { nxs_webmethod_return_nack("name is not set"); }
	
	$metakey = "unicontent_" . $group . "_" . $name;	

	$metadata = array();
	$metadata[$metakey] = $contentproperties;
	
	nxs_mergesitemeta($metadata);
}

// returns the default unicontent to use for this widget
function nxs_unicontent_getdefaultname($group)
{
	$name = "main";
	if (nxs_unicontent_exists($group, $name))
	{
		$result = $name;
	}
	else
	{
		$result = "";
	}
	// todo: add filter to enable plugins/themes to override the default
	return $result;
}

// determines if there is already a unicontent name used in the 
// unicontent group.
function nxs_unicontent_exists($group, $name)
{
	$result = false;
	$unicontent = nxs_unicontent_getunicontentproperties($group, $name);
	if (count($unicontent) > 0)
	{
		$result = true;
	}
	return $result;
}

function nxs_unicontent_getunicontentproperties($group, $name)
{
	if (!isset($group) || $group == "")
	{
		nxs_webmethod_return_nack("group is not set (2)");
	}
	if (!isset($name) || $name == "")
	{
		nxs_webmethod_return_nack("name is not set");
	}
	
	$sitemeta = nxs_getsitemeta();
	$metakey = "unicontent_" . $group . "_" . $name;
	$result = $sitemeta[$metakey];
	
	if (!isset($result))
	{
		$result = array();
	}

	return $result;
}

function nxs_unicontent_getunicontentablefieldids($options)
{
	$result = array();
	
	$fields = $options["fields"];
  foreach ($fields as $key => $optionvalues) 
  {
  	$unicontentablefield = $optionvalues["unicontentablefield"];
  	if (isset($unicontentablefield) && $unicontentablefield === true)
  	{
  		$result[] = $optionvalues["id"];
  	}
  }
  
  return $result;
}

function nxs_unicontent_containsatleastoneuniunicontentablefield($options)
{
	$result = false;
	$uniunicontentablefieldids = nxs_unicontent_getunicontentablefieldids($options);
	if (count($uniunicontentablefieldids) > 0)
	{
		$result = true;
	}
	return $result;
}

function nxs_unicontent_getunifiedcontentgroup($widgetid)
{
	nxs_requirewidget($widgetid);

	$functionnametoinvoke = "nxs_widgets_{$widgetid}_getunifiedcontentgroup";
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke);
	}
	else
	{
		$result = "";
	}
	return $result;
}

function nxs_unicontent_blendinitialunicontentproperties($args, $unicontentgroup)
{
	$unicontent = nxs_unicontent_getdefaultname($unicontentgroup);
	$args['unicontent'] = $unicontent;
	if (isset($unicontent) && $unicontent != "") 
	{
		// blend unicontent properties
		$unicontentproperties = nxs_unicontent_getunicontentproperties($unicontentgroup, $unicontent);
		$args = array_merge($args, $unicontentproperties);
	}
	return $args;
}

// -------------

function nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip, $title)
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	
	if (isset($required) && $required == "true")
	{
		$isrequiredhtml = "<span class='required'>*</span>";	
	}
	else
	{
		$isrequiredhtml = "";
	}
	
	$result = "";
	$result .= "<div class='box-title'>";
	$result .= "<h4>" . $label . $isrequiredhtml . "</h4>";
	
	//var_dump($optionvalues);
	
	//var_dump($optionvalues);
	
	
	if ($tooltip != "") {
		$result .= "
			<span class='nxs-icon-info info'>
				<div class='info-description'>" . $tooltip ."</div>
			</span>";
	}
	
	if ($optionvalues["unistylablefield"] == true) {
		if ($runtimeblendeddata["unistyle"] != "") {
			$result .= "
				<span class='nxs-icon-pagedecorator info'>
					<div>Style modifications will be synced automatically to other widgets on your site having the &quot;<strong>" . $runtimeblendeddata["unistyle"] . "</strong>&quot; unistyle configuration.</div>
				</span>";
		} else {
			$result .= "
				<span class='nxs-icon-pagedecorator info'>
					<div>This is a <strong>styling</strong> option. You can automatically fill this option by creating a new <strong>unistyle</strong> instance or use an existing one for this widget. That way you are able to reuse and update the styling for unistyled widgets instantly throughout your website.<br/>
					<a href='http://www.youtube.com/watch?v=2jIgUNg71Uo#t=210' target='_new'><strong>Click here to watch a Youtube movie about this feature</strong></a></div>
				</span>";
		}
	}
	
	if ($optionvalues["unicontentablefield"] == true) {		
		if ($runtimeblendeddata["unicontent"] != "") {
			$result .= "
				<span class='nxs-icon-pen info'>
					<div>Content modifications will be synced automatically to other widgets on your site having the &quot;<strong>" . $runtimeblendeddata["unicontent"] . "</strong>&quot; unicontent configuration.</div>
				</span>";
		} else {
			$result .= "
				<span class='nxs-icon-pen info'>
					<div>This is a <strong>content</strong> option. You can automatically fill this option by creating a new <strong>unicontent</strong> instance or use an existing one for this widget. That way you are able to reuse and update the content for unicontented widgets instantly throughout your website.<br/>
					<a href='http://www.youtube.com/watch?v=bCoiu02YCko#t=387' target='_new'><strong>Click here to watch a Youtube movie about this feature</strong></a></div>
				</span>";
		}
	}
	
  $result .= "</div>";

	return $result;
}

function nxs_genericpopup_getpopuphtml_basedonoptions($args)
{
	$result = array();
	
	$options = nxs_genericpopup_getoptions($args);

	
	$persisteddata = nxs_genericpopup_getpersisteddata($args);
	
	// reconstructing the properties for unistyle; if the unistyle is 
	// set in persisted data, that unistyle will be used, unless the unistyle
	// is set in the session data (that one prefails), note we can't apply the
	// unistyle after blending with all other values (client session data),
	// as we allow end-users to override the unistyle while editing.
	$unistyledata = array();
	$unistyle_persisted = $persisteddata["unistyle"];
	if ($unistyle_persisted != "")
	{
		$unistyle = $unistyle_persisted;
	}
	$unistyle_session = $args["clientpopupsessiondata"]["unistyle"];
	if ($unistyle_session != "")
	{
		$unistyle = $unistyle_session;
	}
	$unistyleprevious_session = $args["clientpopupsessiondata"]["unistyleprevious"];
	if ($unistyleprevious_session != "" && $unistyle_session == "")
	{
		// user removed the unistyle
		$unistyle = "";
		$persisteddata["unistyle"] = "";
	}
	
	if (isset($unistyle) && $unistyle != "") 
	{
		// apply the universal style (from persisted datasource, or sessiondata)
		$group = $options["unifiedstyling"]["group"];
		
		if ($group == "")
		{
			/*
			echo "options:";
			var_dump($options);
			echo "args:";
			var_dump($args);			
			nxs_webmethod_return_nack("group not set?!");
			*/
		}
		else
		{
			// blend unistyle properties
			$unistyledata = nxs_unistyle_getunistyleproperties($group, $unistyle);
			
			if (false) // $_SERVER['REQUEST_METHOD'] === 'POST') 
			{
				echo "lala;";
				echo "unistyle_persisted:[$unistyle_persisted] | ";
				echo "unistyle session:[$unistyle_session] ";
				$json = json_encode($unistyledata);
				
				echo "json unistyle data: [$json] ";
				
				//echo "testgj";
				die();
			}
			
			// NOTE; the unistyledata _CAN_ be overridden by the user,
			// since the unistyledata is blended with clientpopupsessiondata and shortscopedata
		}
	}
	
	
	
	// ---
	
	// reconstructing the properties for unicontent; if the unicontent is 
	// set in persisted data, that unicontent will be used, unless the unicontent
	// is set in the session data (that one prefails), note we can't apply the
	// unicontent after blending with all other values (client session data),
	// as we allow end-users to override the unicontent while editing.
	$unicontentdata = array();
	$unicontent_persisted = $persisteddata["unicontent"];
	if ($unicontent_persisted != "")
	{
		$unicontent = $unicontent_persisted;
	}
	$unicontent_session = $args["clientpopupsessiondata"]["unicontent"];
	if ($unicontent_session != "")
	{
		$unicontent = $unicontent_session;
	}
	if (isset($unicontent) && $unicontent != "") 
	{
		// apply the universal content (from persisted datasource, or sessiondata)
		$group = $options["unifiedcontent"]["group"];
		if ($group == "")
		{
			//echo "aaaaaaaaa";
			//var_dump($options);
			//nxs_webmethod_return_nack("empty2;debug[" . $group . "]");
		}
		else
		{
			// blend unicontent properties
			$unicontentdata = nxs_unicontent_getunicontentproperties($group, $unicontent);
			// NOTE; the unicontentdata _CAN_ be overridden by the user,
			// since the unicontentdata is blended with clientpopupsessiondata and shortscopedata
		}
	}
	
	$clientpopupsessiondata = $args["clientpopupsessiondata"];
	$clientshortscopedata = $args["clientshortscopedata"];
	
	// mix persisteddata with clientpopup and clientshortscopedata
	$runtimewidgetmetadata = nxs_genericpopup_getblendeddata($persisteddata, $unistyledata, $unicontentdata, $clientpopupsessiondata, $clientshortscopedata);
	
	$sheettitle = "";
	$sheeticonid = "";
		
	$fields = $options["fields"];
	
	if (isset($options['sheettitle']))
	{
		$sheettitle = $options['sheettitle'];
	}
	if (isset($options['sheeticonid']))
	{
		$sheeticonid = $options['sheeticonid'];
	}
	if (isset($options['sheethelp']))
	{
		$sheethelp = $options['sheethelp'];
	}
	$footerfiller = false;
	if (isset($options['footerfiller']))
	{
		$footerfiller = $options['footerfiller'];
	}
	
	// turn all fields into variables
	extract($runtimewidgetmetadata);

	//
	$result = array();
	$result["result"] = "OK";
	
	// Turn on output buffering
	nxs_ob_start();
	
	// Actual rendering of HTML elements
	?>	
	<!-- HTML -->
	<div class="nxs-admin-wrap">
    <div class="block">
      <?php nxs_render_popup_header_v2($sheettitle, $sheeticonid, $sheethelp); ?>
      <div class="nxs-popup-content-canvas-cropper">
        <div class="nxs-popup-content-canvas">
        	
          <?php 
          $containsatleastoneunistylablefield = nxs_unistyle_containsatleastoneunistyleablefield($options);
          $isunifiedstyleactive = isset($unistyle) && $unistyle != "" && $containsatleastoneunistylablefield;
          
          foreach ($fields as $key => $optionvalues) 
          {
          	$shouldshowfield = true;
          
						// derive visibility based on capabilities
          	if ($shouldshowfield === true)
          	{
          		// condition 1; unistylable fields (note: regardless of whether unistyle is turned on for this widget, or not!)
          		$unistylablefield = $optionvalues["unistylablefield"];
	          	if (isset($unistylablefield) && $unistylablefield === true)
	          	{
	          		// unistylable fields are not visible when user has no design capabilities
	          		if (!nxs_cap_hasdesigncapabilities())
	          		{
	          			$shouldshowfield = false;
	          		}
	          	}

          		// condition 2; explicit required capability
          		$requirecapability = $optionvalues["requirecapability"];
	          	if ($requirecapability && isset($requirecapability))
	          	{
	          		if (is_super_admin())
	          		{
	          			// should show!
	          		}
	          		else
	          		{
		          		if (!current_user_can($requirecapability))
		          		{
		          			$shouldshowfield = false;
		          		}
		          	}
	          	}
	          	else
	          	{
	          		// assumed visible
	          	}
			}
          	
          	if ($shouldshowfield === true)          	
          	{
		    	// delegate behaviour to the specific option (pluggable)
				$type = $optionvalues["type"];
		    	nxs_requirepopup_optiontype($type);
		    	
		    	$functionnametoinvoke = "nxs_popup_optiontype_" . $type . "_renderhtmlinpopup";
				if (function_exists($functionnametoinvoke))
				{
					call_user_func($functionnametoinvoke, $optionvalues, $args, $runtimewidgetmetadata);
				}
				else
				{
					nxs_webmethod_return_nack("missing function name $functionnametoinvoke");
				}
			}
			else
			{
				//
			}
  		  }
  		  ?>
	      	<?php if ($footerfiller) { ?>
	      	<div class="nxs-canvas-footerfiller content2 nxs-popup-heading">
	      	  <div class="box">
	      		&nbsp;
	      	  </div> <!-- END box -->
	      	<div class="nxs-clear margin"></div>
	      </div>
	      <?php } ?>
	  	</div> <!-- nxs-popup-content-canvas -->
	  </div> <!-- END nxs-popup-content-canvas-cropper -->
	  <div class="content2 popup-footer-container">          
        <div class="box">
      	  <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'>Save</a>
          <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'>OK</a>
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'>Cancel</a>
      	</div> <!-- END box -->
      <div class="nxs-clear margin"></div>
  	</div> <!-- END content2 -->
  	</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<!-- UPDATING POPUP SESSION DATA -->
	<script type='text/javascript'>
    // generic implementation, same for all contextprocessors
    function nxs_js_setpopupdatefromcontrols() 
    {        
      // Widget specific data
      <?php 
			foreach ($fields as $key => $value) 
			{	
	    	// delegate behaviour to the specific option (pluggable)
				$type = $value["type"];
	    	nxs_requirepopup_optiontype($type);
	    	$functionnametoinvoke = 'nxs_popup_optiontype_' . $type . '_renderstorestatecontroldata';
				if (function_exists($functionnametoinvoke))
				{
					call_user_func($functionnametoinvoke, $value);
				}
				else
				{
					nxs_webmethod_return_nack("missing function name $functionnametoinvoke");
				}
      }
      ?>
    }
    
    // start - nxs_js_savegenericpopup below is rendered by the contextprocessor
    <?php
    nxs_render_nxs_js_savegenericpopup($args);
    ?>
    // end - nxs_js_savegenericpopup above is rendered by the contextprocessor
	</script>
	<?php
	
	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;
	
	return $result;
}

function nxs_render_nxs_js_savegenericpopup($args)
{
	// delegate request to contextprocessor
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	$contextprocessor = $clientpopupsessioncontext["contextprocessor"];
	
	// load the context processor if its not yet loaded
	nxs_requirepopup_contextprocessor($contextprocessor);
	
	// delegate request to the contextprocessor
	$functionnametoinvoke = 'nxs_popup_contextprocessor_' . $contextprocessor . '_render_nxs_js_savegenericpopup';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("missing function name $functionnametoinvoke");
	}
	
	return $result;
}

function nxs_genericpopup_getpopuphtml_basedoncustomimplementation($args)
{
	// delegate request to contextprocessor
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	$contextprocessor = $clientpopupsessioncontext["contextprocessor"];
	
	// load the context processor if its not yet loaded
	nxs_requirepopup_contextprocessor($contextprocessor);
	
	// delegate request to the contextprocessor
	$functionnametoinvoke = 'nxs_popup_contextprocessor_' . $contextprocessor . '_getcustompopuphtml';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("missing function name $functionnametoinvoke");
	}
	
	return $result;
}

function nxs_render_widgetbackgroundstyler($widgetname)
{
 	$widgeticonid = nxs_getwidgeticonid($widgetname);
	?>
  <li title='<?php nxs_l18n_e("Decorate[tooltip]", "nxs_td"); ?>' class='paintroller' onclick="nxs_js_edit_widget_v2(this, 'backgroundstyle'); return false;">
    <div class="nxs-drag-helper" style='display: none;'>
      <div class='placeholder'>
       	<span class='<?php echo $widgeticonid; ?>'></span>
      </div>
    </div>					
  </li>
 	<?php
}

function nxs_widgets_setgenericwidgethovermenu($postid, $placeholderid, $placeholdertemplate)
{
	$args = array();
	$args["postid"] = $postid;
	$args["placeholderid"] = $placeholderid;
	$args["placeholdertemplate"] = $placeholdertemplate;
	return nxs_widgets_setgenericwidgethovermenu_v2($args);
}

function nxs_shoulddebugmeta()
{
	$result = false;
	if (isset($_REQUEST["debugmeta"]) && $_REQUEST["debugmeta"] == "true")
	{
		$result = true;
	}
	return $result;
}

function nxs_print_filters_for($hook = '') 
{
  global $wp_filter;
  if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
  {
  	return;
  }

  print '<pre>';
  print_r( $wp_filter[$hook] );
  print '</pre>';
}

function nxs_widgets_setgenericwidgethovermenu_v2($args)
{
	// defaults
	$enable_decoratewidget = false;
	$defaultwidgetclickhandler = "edit";
	
	// if (support
	
	$enable_editwidget = true;
	$enable_movewidget = true;
	$enable_deletewidget = true;
	$enable_deleterow = false;
	$enable_debugmeta = nxs_shoulddebugmeta();
	
	extract($args);
	
	if (!isset($postid)) { nxs_webmethod_return_nack("postid not set (nxs_widgets_setgenericwidgethovermenu_v2)"); }
	if (!isset($placeholderid)) { nxs_webmethod_return_nack("placeholderid not set"); }
	if (!isset($placeholdertemplate)) { nxs_webmethod_return_nack("placeholdertemplate not set"); }
	
	//
	// 
	//

	// check permission
	if (nxs_cap_hasdesigncapabilities())
	{
		// ok
	}
	else
	{
		$enable_movewidget = false;
		$enable_deletewidget = false;
		$enable_deleterow = false;
		$lockedwidget = true;
	}
	
	

 	$widgeticonid = nxs_getwidgeticonid($placeholdertemplate);
 	
	// Turn on output buffering
	nxs_ob_start();
	// --------------------------------------------------------------------------------------------------
	$islocked = false;
	
	$metadata = $args["metadata"];
	if (isset($metadata))
	{
		if ($metadata["lock"] == "locked")
		{
			$islocked = true;
		}
	}
	
 	if (!$islocked)
 	{
		?>
	  <ul class="">
	  	<?php
	  	if ($enable_movewidget === "first")
	  	{
	  		?>
		    <li title='<?php nxs_l18n_e("Move[tooltip]", "nxs_td"); ?>' class='nxs-draggable nxs-existing-pageitem nxs-dragtype-placeholder' id='draggableplaceholderid_<?php echo $placeholderid; ?>'>
	      	<span class='nxs-icon-move'></span>
	        <div class="nxs-drag-helper" style='display: none;'>
	          <div class='placeholder'>
	          	<span class='<?php echo $widgeticonid; ?>'></span>
	          </div>
	        </div>
	        <!-- li is closed further on -->
		    <?php
	  	}
	  	else if ($enable_editwidget === true)
	  	{
		  	?>
		    <li title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' class='nxs-hovermenu-button'>
		  		<a href='#' title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' <?php if ($defaultwidgetclickhandler=='edit') { echo 'class="nxs-defaultwidgetclickhandler"'; } ?> onclick="nxs_js_edit_widget(this); return false;">
		      	<span class='<?php echo $widgeticonid; ?>'></span>
		      </a>
		      <!-- li is closed further on -->
		    <?php
	  	}
	  	else
	  	{
	  		nxs_webmethod_return_nack("unsupported first widget menu item");
	  	}
	    ?>
	      <ul class="">
	      	<?php
	      	if ($enable_editwidget === "second")
	      	{
	      		?>
				    <li title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' class='nxs-hovermenu-button'>
				  		<a href='#' title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' <?php if ($defaultwidgetclickhandler=='edit') { echo 'class="nxs-defaultwidgetclickhandler"'; } ?> onclick="nxs_js_edit_widget(this); return false;">
				      	<span class='<?php echo $widgeticonid; ?>'></span>
				      </a>
						</li>	      		
	      		<?php
	      	}
	      	?>
      		<?php 
      		if ($enable_decoratewidget === true)
      		{
      			echo nxs_render_widgetbackgroundstyler($placeholdertemplate); 
      		}
      		?>
      		<?php
      		if ($enable_movewidget === true)
      		{
      			$widgeticonid = nxs_getplaceholdericonid($placeholdertemplate);
      			?>
		        <li title='<?php nxs_l18n_e("Move[tooltip]", "nxs_td"); ?>' class='nxs-draggable nxs-existing-pageitem nxs-dragtype-placeholder' id='draggableplaceholderid_<?php echo $placeholderid; ?>'>
		        	<span class='nxs-icon-move'></span>
	            <div class="nxs-drag-helper" style='display: none;'>
                <div class='placeholder'>
                	<span class='<?php echo $widgeticonid; ?>'></span>
                </div>
	            </div>					
		        </li>
		       	<?php
		      }
		      ?>
		      <?php
      		if ($enable_deletewidget === true)
      		{
      			?>
	        	<a class='nxs-no-event-bubbling' href='#' onclick='nxs_js_popup_placeholder_wipe("<?php echo $postid; ?>", "<?php echo $placeholderid; ?>"); return false;'>
	           	<li title='<?php nxs_l18n_e("Delete[tooltip]", "nxs_td"); ?>'>
	           		<span class='nxs-icon-trash'></span>
	           	</li>
	        	</a>		
	        	<?php
	        }
	        ?>
	        <?php
      		if ($enable_deleterow === true)
      		{
      			?>
	        	<a class='nxs-no-event-bubbling nxs-defaultwidgetdeletehandler' href='#' onclick='nxs_js_row_remove(this); return false;'>
	           	<li title='<?php nxs_l18n_e("Delete[tooltip]", "nxs_td"); ?>'><span class='nxs-icon-trash'></span></li>
	        	</a>		
	        	<?php
	        }
	        ?>
	        <?php
	        if ($enable_debugmeta === true)
	        {
	        	?>
	         	<li title='<?php nxs_l18n_e("Debug[tooltip]", "nxs_td"); ?>'>
	  	      	<a class='nxs-no-event-bubbling' href='#' onclick="nxs_js_edit_widget_v2(this, 'debug'); return false; return false;">
		          		<span class='nxs-icon-search'></span>
		        	</a>	
	         	</li>    		
	        	<?php
	        }
	        ?>
		    </ul>	
	  	</li>
		</ul>
		<?php
	}
	else
	{
		if (nxs_cap_hasdesigncapabilities())
		{
			?>
		  <ul class="">
			 	<li title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' class='nxs-hovermenu-button'>
		  		<a href='#' title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>'  class="nxs-defaultwidgetclickhandler" onclick="nxs_js_edit_widget_v2(this, 'unlock'); return false;">
		      	<span class='<?php echo $widgeticonid; ?>'></span>
		      </a>
	    	</li>
	  		<li>
		  		<a href='#' title='<?php nxs_l18n_e("Unlock", "nxs_td"); ?>' onclick="nxs_js_edit_widget_v2(this, 'unlock'); return false;">
		      	<span class='nxs-icon-unlocked'></span>
		      </a>
		  	</li>
			</ul>
			<?php
		}
		else
		{
			// hide all icons
			?>
			<ul class="">
			 	<li title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>' class='nxs-hovermenu-button'>
		  		<a href='#' title='<?php nxs_l18n_e("Edit[tooltip]", "nxs_td"); ?>'  class="nxs-defaultwidgetclickhandler" onclick="nxs_js_alert('<?php nxs_l18n_e("This item is locked, only a webdesigner can modify it", "nxs_td"); ?>');">
		      	<span class='<?php echo $widgeticonid; ?>'></span>
		      </a>
	    	</li>
		  	<li>
		  		<a href='#' title='<?php nxs_l18n_e("Locked", "nxs_td"); ?>' onclick="nxs_js_alert('<?php nxs_l18n_e("This item is locked, only a webdesigner can modify it", "nxs_td"); ?>');">
		      	<span class='nxs-icon-lock'></span>
		      </a>
		  	</li>
			</ul>
			<?php
		}
	}
	
  // --------------------------------------------------------------------------------------------------
    
  // Setting the contents of the output buffer into a variable and cleaning up te buffer
  $menu = nxs_ob_get_contents();
  nxs_ob_end_clean();
  
  // Setting the contents of the variable to the appropriate array position
  // The framework uses this array with its accompanying values to render the page
  global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["menutopright"] = $menu;
}

function nxs_genericpopup_supportsoptions($args)
{
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	$contextprocessor = $clientpopupsessioncontext["contextprocessor"];
	
	// load the context processor if its not yet loaded
	nxs_requirepopup_contextprocessor($contextprocessor);
	
	// delegate request to the contextprocessor
	$functionnametoinvoke = 'nxs_popup_contextprocessor_' . $contextprocessor . '_supportsoptions';
	if (function_exists($functionnametoinvoke))
	{		
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("missing function name $functionnametoinvoke");
	}
	
	return $result;
}

// kudos to http://stackoverflow.com/questions/3797239/insert-new-item-in-array-on-any-position-in-php
function nxs_array_insert(&$array, $value, $index)
{
	return $array = array_merge(array_splice($array, max(0, $index - 1)), array($value), $array);
}

// kudos to http://stackoverflow.com/questions/6418903/how-to-clone-an-array-of-objects-in-php
function nxs_array_copy($arr) 
{
  $newArray = array();
  foreach($arr as $key => $value) {
      if(is_array($value)) $newArray[$key] = nxs_array_copy($value);
      elseif(is_object($value)) $newArray[$key] = clone $value;
      else $newArray[$key] = $value;
  }
  return $newArray;
}

// retrieves a set of options for the specified context
function nxs_genericpopup_getoptions($args)
{
	if (!isset($args["clientpopupsessioncontext"])) { nxs_webmethod_return_nack("clientpopupsessioncontext not set"); }
	if (!isset($args["clientpopupsessioncontext"]["contextprocessor"])) { nxs_webmethod_return_nack("contextprocessor not set"); }
	
	$clientpopupsessioncontext = $args["clientpopupsessioncontext"];
	$contextprocessor = $clientpopupsessioncontext["contextprocessor"];
	
	// load the context processor if its not yet loaded
	nxs_requirepopup_contextprocessor($contextprocessor);
	
	// delegate request to the contextprocessor
	$functionnametoinvoke = 'nxs_popup_contextprocessor_' . $contextprocessor . '_getoptions';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		nxs_webmethod_return_nack("missing function name $functionnametoinvoke");
	}
	
	return $result;
}

// blends the result of the specified persisted meta data, client popup and clientshortscopedata,
// persisteddata will be overriden by clientpopupsessiondata, and 
// clientpopupsessiondata will be overriden by clientshortscopedata.
//
// persisteddata is filled by values persisted in the database,
// clientpopupsessiondata is filled by values in the runtime session (cross popup),
// clientshortscopedata is filled by values for the current/previous popup only
function nxs_genericpopup_getblendeddata($persisteddata,  $unistyledata, $unicontentdata, $clientpopupsessiondata, $clientshortscopedata)
{
	//
	$result = array();
	
	// override persisteddata, if present
	if (isset($persisteddata))
	{
		$result = array_merge($result, $persisteddata);
	}
	
	//
	
	if (isset($unistyledata))
	{
		$result = array_merge($result, $unistyledata);
	}
	
	//
	
	if (isset($unicontentdata))
	{
		$result = array_merge($result, $unicontentdata);
	}
	
	//
	
	if (isset($clientpopupsessiondata))
	{
		$result = array_merge($result, $clientpopupsessiondata);
	}
	
	//
	
	if (isset($clientshortscopedata))
	{
		$result = array_merge($result, $clientshortscopedata);
	}
	
	return $result;
}

function nxs_getcssvariables()
{
	$result = array();
	// enable themes and plugins to extend the variables
	$args = array();
	$result = apply_filters("nxs_getcssvariables", $result, $args);
	
	return $result;
}

// renders dynamic (with variables) css as defined by the server side,
// these can be overruled by manual css
function nxs_render_dynamicservercss($sitemeta)
{
	// to support lame IE we need chunks of CSS script tags, instead of 1...
	for ($currentchunk = 0; $currentchunk < nxs_getmaxservercsschunks(); $currentchunk++)
	{
		?>
		<style type="text/css" id="nxs-dynamiccss-server-chunk-<?php echo $currentchunk; ?>"></style>
		<?php
	}
	// add more chunks when needed... 
}

function nxs_render_manualcss($sitemeta)
{
	// to support lame IE we need chunks of CSS script tags, instead of 1...
	for ($currentchunk = 0; $currentchunk < nxs_getmaxservercsschunks(); $currentchunk++)
	{
		?>
		<style type="text/css" id="nxs-dynamiccss-manual-chunk-<?php echo $currentchunk; ?>"></style>
		<?php
	}
}

// renders the class tag for the html element
function nxs_render_htmlclasstag()
{
	$class = "";
	
	// 
	$class = apply_filters('nxs_render_htmlclasstag', $class);
	if ($class != "")
	{
		echo "class='" . $class . "' ";
	}
	
	// enable plugins to render custom attributes on the HTML element
	do_action('nxs_render_htmlatts');
}

function nxs_render_htmlstarttag()
{
	?>
	<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?> prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#" <?php nxs_render_htmlclasstag(); ?>>
	<?php
}

function nxs_render_htmlcorescripts()
{
	?>
	<script type='text/javascript'> var nxsboxL10n = { loadingAnimation: "<?php echo nxs_getframeworkurl(); ?>/images/loadingnxsbox.png" }; </script>	
	<script type="text/javascript">	
		document.documentElement.className = 'js'; <?php do_action('nxs_ext_injectinlinejsscriptfunctions'); ?>		
	</script>
	<noscript><?php do_action('nxs_ext_injectnoscript'); ?></noscript>
	<?php 
}

function nxs_font_getcleanfontfam($fontfamily)
{
	// some fonts have noise appended, for example Playball::blabla
	$pieces = explode(":", $fontfamily);
	$result = $pieces[0];
	// replace + with space
	$result = str_replace("+", " ", $result);
	$result = str_replace("'", "", $result);
	return $result;
}

function nxs_font_getskipfonts()
{
	return array("Arial", "Georgia", "Verdana", "Times New Roman");
}

function nxs_render_headstyles()
{
	// mutaties hierin ook doorvoeren in nxsmenu.php en header-post.php
	$sitemeta = nxs_getsitemeta();
	?>
	<style type="text/css" id="dynamicCssCurrentConfiguration">
		<?php
		$css = "";	

		// lettertypen
		$hetfont = str_replace("\'", "'", nxs_font_getcleanfontfam($sitemeta["vg_fontfam_1"]));
		$css .= "body { font-family: " . $hetfont . "; }";
		$hetfont = str_replace("\'", "'", nxs_font_getcleanfontfam($sitemeta["vg_fontfam_2"]));
		$css .= ".nxs-title, .nxs-logo { font-family: " . $hetfont . "; }";
		$css .= ".entry-content h1,.entry-content h2,.entry-content h3,.entry-content h4,.entry-content h5,.entry-content h6 { font-family: " . $hetfont . "; }";
		
		
		// new style
		$fontidentifiers = nxs_font_getfontidentifiers();
		foreach ($fontidentifiers as $currentfontidentifier)
		{
			$hetfont = str_replace("\'", "'", nxs_font_getcleanfontfam($sitemeta["vg_fontfam_{$currentfontidentifier}"]));
			$css .= ".nxs-fontzen-{$currentfontidentifier} { font-family: $hetfont }";	
		}

		// output		
		echo $css;
		?>
	</style>
	<style type="text/css" id="dynamicCssVormgevingKleuren"></style>
	<style type="text/css" id="dynamicCssVormgevingLettertypen"></style>
	<?php nxs_render_dynamicservercss($sitemeta); ?>
	<?php nxs_render_manualcss($sitemeta); ?>
	<?php
}

function nxs_iswidgetallowedonpost($postid, $widgetid, $isundefinedallowed)
{
	$wpposttype = nxs_getwpposttype($postid);
	$nxsposttype = nxs_getnxsposttype_by_postid($postid);
	$nxssubposttype = nxs_get_nxssubposttype($postid);
	
	$pagetemplate = nxs_getpagetemplateforpostid($postid);

	$phtargs = array();
	$phtargs["invoker"] = "nxsextundefined";
	$phtargs["wpposttype"] = $wpposttype;
	$phtargs["nxsposttype"] = $nxsposttype;
	$phtargs["nxssubposttype"] = $nxssubposttype;
	
	$phtargs["pagetemplate"] = $pagetemplate;

	$widgets = nxs_getwidgets($phtargs);	

	$result = false;
	foreach ($widgets as $currentwidget)
	{
		$currentwidgetid = $currentwidget["widgetid"];
		if ($widgetid == $currentwidgetid)
		{
			$result = true;
			break;
		}
	}
	
	if ($result === false)
	{		
		if ($widgetid == "undefined" && $isundefinedallowed)
		{
			// exceptional case; undefined is allowed (used when swapping elements)
			$result = true;
		}
	}
	
	return $result;
}

function nxs_popup_rendergenericpopup($sheet, $args)
{
	nxs_requirepopup_genericpopup($sheet);
	$functionnametoinvoke = 'nxs_popup_genericpopup_' . $sheet . '_getpopup';
	if (function_exists($functionnametoinvoke))
	{					
		$result = call_user_func($functionnametoinvoke, $args);				
		return $result;
	}
	else
	{
		nxs_webmethod_return_nack("function not found; " . $functionnametoinvoke . " for sheet; " . $sheet);
	}
}

// kudos to http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-numeric
function nxs_isassociativearray($array) 
{
	$result = array_values($array) === $array;
	return $result;
}

// extends the widgetoptions by adding additional fields 
function nxs_extend_widgetoptionfields(&$existingoptions, $extendoptions)
{
	nxs_requirewidget("generic");
	foreach ($extendoptions as $currentextendoption)
	{
		$functionnametoinvoke = "nxs_widgets_generic_{$currentextendoption}_getoptions";
		if (function_exists($functionnametoinvoke))
		{
			$args = array();
			$currentoptions = call_user_func($functionnametoinvoke, $args);
			$existingoptions["fields"] = array_merge($existingoptions["fields"], $currentoptions["fields"]);
		}
		else
		{
			nxs_webmethod_return_nack("function not found; " . $functionnametoinvoke);	
		}
	}
}

function nxs_getrenderedwidget($args) 
{
	extract($args);

	if ($placeholdertemplate == "")
	{
		// indien nog niet gezet, halen we deze op, op basis van de postid en placeholderid wordt het template type bepaald
		$placeholdertemplate = nxs_getplaceholdertemplate($postid, $placeholderid);
		$args["placeholdertemplate"] = $placeholdertemplate;
	}

 	// inject widget if not already loaded, implements *dsfvjhgsdfkjh*
 	$requirewidgetresult = nxs_requirewidget($placeholdertemplate);
 	if ($requirewidgetresult["result"] == "NACK")
 	{
 		if ($placeholdertemplate == "")
 		{
 		}
 		else
 		{
	 		// too bad, it failed
	 		if (true) // nxs_has_adminpermissions())
	 		{
	 			echo "[nxs: rendering of widget [$placeholdertemplate] failed, returning]";
	 		}
	 		else
	 		{
		 		echo "[nxs: rendering of widget failed, returning]";
		 	}
		}
 		return;
 	}
		
	// derive function to invoke
	//
	if ($contenttype == "webpart")
	{
		if ($webparttemplate == "")
		{
			nxs_webmethod_return_nack("unspecified webparttemplate");
		}
		$functionnametoinvoke = 'nxs_widgets_' . $placeholdertemplate . '_render_webpart_' . $webparttemplate;
	}
	else
	{
		nxs_webmethod_return_nack("unsupported renderdisplay; " . $args["renderdisplay"]);
	}
			
	//
	// invokefunction
	//
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $args);
	}
	else
	{
		// vroeger lieten we een harde foutmelding zien, maar nu gaan we ervanuit dat een gebruiker
		// een plugin kan hebben uitstaan, en tonen we een "zachte" foutmelding
		
		nxs_webmethod_return_nack("function not found; " . $functionnametoinvoke);	
	}
 	
 	return $result;
}

function nxs_widgets_initplaceholderdatageneric($args, $widget_name)
{
	$postid = $args["postid"];
	$placeholderid = $args["placeholderid"];
	$metadata = $args;
	unset($metadata["postid"]);
	unset($metadata["placeholderid"]);
	return nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
}

// TODO: use this function to load initial data, instead of relying on the import features

function nxs_import_file_to_media($filepath)
{
	$importmeta = array
	(
		"filepath" => $filepath,
	);
	$compoundresult = nxs_import_file_to_media_v2($importmeta);
	
	$result = $compoundresult["postid"];
	
	return $result;
}

function nxs_import_file_to_media_v2($importmeta)
{
	$filepath = $importmeta["filepath"];
	if ($filepath == "")
	{
		nxs_webmethod_return_nack("filepath not set?");
	}
	
	$uploadinfo = wp_upload_dir();
	$uploadpath = $uploadinfo["path"];
	
	$filename = $filepath;
	$basename = basename($filename);
	
	// get rid of possible query parameters, if they exist
	$basenamepieces = explode("?", $basename);
	$basename = $basenamepieces[0];
	
	if ($importmeta["basename"] != "")
	{
		// overrule basename
		$basename = $importmeta["basename"];
	}
	
	$fullpath = $uploadpath . "/" . $basename;

	if (is_file($fullpath))
	{
		// ok, no override, we will re-use this one and attach it
	}
	else
	{
		$output = copy($filename,$fullpath);
		if (!$output)
		{
			error_log("nxs; unable to copy $filename to $fullpath");
			error_log("nxs; importmeta basename: [" . $importmeta["basename"] . "]");
			
			// TODO: retry using different name
			// echo "unable to copy file, skipping";
			return 0;
		}
	}
	
	// if we reach this point the file is copied or was already there
	
	// Set up options array to add this file as an attachment
  $attachment = array
  (
    'post_mime_type' => 'image/png',
    'post_title' => addslashes($basename),
    'post_content' => '',
    'post_status' => 'inherit'
  );
	
	// Run the wp_insert_attachment function. This adds the file to the media library and 
	// generates the thumbnails. If you wanted to attach this image to a post, you could 
	// pass the post id as a third 
	// param and it'd magically happen.
  $postid = wp_insert_attachment($attachment, $fullpath);
  if ($postid != 0)
  {
  	require_once(ABSPATH . 'wp-admin/includes/image.php');
  	
  	// generate alternative formats
  	//echo "generating meta";
  	$generatemetaresult = wp_generate_attachment_metadata($postid, $fullpath);
  	//var_dump($metadata);
  	
  	//echo "updatemeta:";
		$updatemetaresult = wp_update_attachment_metadata($postid, $generatemetaresult);
		//var_dump($updatemetaresult);
		
		//
		//
		//
		$postmetas = $importmeta["postmetas"];
		if ($postmetas != "")
		{
			foreach ($postmetas as $postmetakey => $postmetavalue)
			{
				$r = add_post_meta($postid, $postmetakey, $postmetavalue);
			}
		}
	}
	else
	{
		//echo "failed";
	}
	
	$result = array
	(
		"postid" => $postid,
		"generatemetaresult" => $generatemetaresult,
		"updatemetaresult" => $updatemetaresult,
	);
  return $result;
}

function nxs_concatenateargswithspaces($variablearguments)
{
	$args = func_get_args();
		
	$result = "";
	
	foreach ($args as $currentargument)
	{
		if (isset($currentargument))
		{
			if ($currentargument != "")
			{
				$result = $result . $currentargument . " ";
			}
		}
	}
	
	return $result;	
}

function nxs_getdarkercsscolorvariation($cssname)
{
	$splitted = explode("-", $cssname);
	$last = end($splitted);
	$lastindex= end(array_keys($splitted));
	if ($last == "dd")
	{
		$last = "dd";
	}
	else if ($last == "d")
	{
		$last = "dd";
	}
	else if ($last == "m")
	{
		$last = "d";
	}
	else if ($last == "l")
	{
		$last = "m";
	}
	else if ($last == "ll")
	{
		$last = "l";
	}
	else
	{
		// keep as is (error)
	}
	$splitted[$lastindex] = $last;
	// join the elements
	$result = implode("-", $splitted);
	return $result;
}

function nxs_getslidetotopdistance($value)
{
	return 10 + nxs_getpixelsfrommultiplier($value);
}

function nxs_getpixelsfrommultiplier($value, $factor = 30)
{
	$value = str_replace("-",".", $value);
	$value = floatval($value);
	return $value * $factor;
}

function nxs_getimagecssalignmentclass($value)
{
	if ($value == "l" || $value == "left")
	{
		// default
		$result = "nxs-icon-left";
	}
	else if ($value == "r" || $value == "right")
	{
		// default
		$result = "nxs-icon-right";
	}
	return $result;
}

function nxs_getimagecsssizeclass($value)
{
	if ($value == "" || $value == "full" || $value == "auto-fit")
	{
		// default
		$result = "nxs-stretch";
	}
	else if ($value == "orig@contain")
	{
		// default
		$result = "nxs-size-contain nxs-ratio-original";
	}
	else if (nxs_stringstartswith($value, "orig@contain@"))
	{
		// original, fixed width (but maximized to container), for example orig@contain@1-0
		$splitted = explode("@", $value);
		$factor = $splitted[2];	// for example 1-0 (for factor 1.0)
		$result = "nxs-size-contain nxs-ratio-original nxs-img-width-" . $factor;
	}
	else if (nxs_stringstartswith($value, "c@"))
	{
		// cropped
		$splitted = explode("@", $value);
		$factor = $splitted[1];	// for example 1-0 (for factor 1.0)
		$result = "nxs-icon-width-" . $factor;
	}
	return $result;
}

function nxs_gethtmlfortitleimagetextbutton($htmltitle, $htmlforimage, $image_size, $htmltext, $htmlforbutton, $htmlfiller)
{
	$result = '';
	$result .= '<div>';
	$result .= $htmltitle;
	
	if ($htmlforimage != "") 
	{
		// add filler if the title was set
		$addfiller = false;
		if ($htmltitle != "") { $addfiller = true; }
		if ($addfiller)
		{
			$result .= $htmlfiller;
		}
		$result .= $htmlforimage;
	}
	
	if ($htmltext != "")
	{
		// add filler if htmlimage is set _and_ image is stretched (auto-fit) 
		// [note; if not stretched, the text should float and no filler is needed]
		// add filler also, if the title was set, and no image shows
		$addfiller = false;
		if ($htmlforimage != "" && nxs_isimageautofit($image_size)) { $addfiller = true; }
		if ($htmlforimage == "" && $htmltitle != "") { $addfiller = true; }
		if ($addfiller)
		{
			$result .= $htmlfiller; 
		}
		$result .= $htmltext;
	}
	
	if ($htmlforbutton != "")
	{
		// add filler if text is set
		// add fillter also, if the text was not set and the htmlforimage was set
		// add fillter also, if the text was not set and the image is not set and the title is set
		$addfiller = false;
		if ($htmltext != "") { $addfiller = true; }
		if ($htmltext == "" && $htmlforimage != "") { $addfiller = true; }
		if ($htmltext == "" && $htmlforimage == "" && $htmltitle != "") { $addfiller = true; }
		if ($addfiller)
		{
			$result .= $htmlfiller; 
		}				
		$result .= $htmlforbutton;  
	}
	
	$result .= '</div>';
	
	return $result;
}

function nxs_gethtmlforfiller()
{
	return '<div class="nxs-clear nxs-filler"></div>';
}

function nxs_gethtmlfortext($text, $text_alignment, $text_showliftnote, $text_showdropcap, $wrappingelement, $text_heightiq, $text_fontzen)
{
	if ( $text == "")
	{
	return "";
	}
	
	if ($wrappingelement == "") {
	$wrappingelement = 'p';
	}
	
	// Text styling
	if ($text_showliftnote != "") { $text_showliftnote_cssclass = 'nxs-liftnote'; }
	if ($text_showdropcap != "") { $text_showdropcap_cssclass = 'nxs-dropcap'; }
	
	$text_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $text_alignment);
	$text_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $text_fontzen);
	
	$cssclasses = nxs_concatenateargswithspaces("nxs-default-p", "nxs-applylinkvarcolor", "nxs-padding-bottom0", $text_alignment_cssclass, $text_showliftnote_cssclass, $text_showdropcap_cssclass, $text_fontzen_cssclass);
	
	if ($text_heightiq != "") {
		$heightiqprio = "p1";
		$text_heightiqgroup = "text";
		$cssclasses = nxs_concatenateargswithspaces($cssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$text_heightiqgroup}");
	}
	
	// apply shortcode on text widget
	$text = do_shortcode($text);
		
	$result .= '<'. $wrappingelement . ' class="' . $cssclasses . '">' . $text . '</'. $wrappingelement . '>';
	
	return $result;
}

function nxs_gethtmlfortitle($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, $destination_articleid, $destination_url)
{
	$microdata = "";
	return nxs_gethtmlfortitle_v2($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, $destination_articleid, $destination_url, $microdata);
}

function nxs_gethtmlfortitle_v2($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, $destination_articleid, $destination_url, $microdata)
{
	$destination_target = "";
	return nxs_gethtmlfortitle_v3($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, $destination_articleid, $destination_url, $destination_target, $microdata);
}
	
function nxs_gethtmlfortitle_v3($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, $destination_articleid, $destination_url, $destination_target, $microdata, $destination_relation = false)
{
	if ($title == "")
	{
		return "";
	}
	
	if ($destination_target == "_self") {
		$destination_target_html = 'target="_self"';
	} else if ($destination_target == "_blank") {
		$destination_target_html = 'target="_blank"';
	} else {
		if ($destination_articleid != "") {
			$destination_target_html = 'target="_self"';
		} else {
			$homeurl = nxs_geturl_home();
 			if (nxs_stringstartswith($destination_url, $homeurl)) {
 				$destination_target_html = 'target="_self"';
 			} else {
 				$destination_target_html = 'target="_blank"';
 			}
		}
	}

	$destination_relation_html = '';
	if ($destination_relation == "nofollow") {
		$destination_relation_html = 'rel="nofollow"';
	}
	
	// Title alignment
	$title_alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $title_alignment);
	$title_fontsize_cssclass = nxs_getcssclassesforlookup("nxs-head-fontsize-", $title_fontsize);
	
	$heading = "";
	
	// Title importance (H1 - H6)
	if ($title_heading != "")
	{
		$headingelement = "h" . $title_heading;

	}
	else
	{
		// TODO: derive the title_importance based on the title_fontsize
		//nxs_webmethod_return_nack("to be implemented; derive title_heading from title_fontsize");
		$headingelement = "h1";
	}
	
	$cssclasses = nxs_concatenateargswithspaces("nxs-title", $title_alignment_cssclass, $title_fontsize_cssclass);
	if ($title_heightiq != "")
	{
		$heightiqprio = "p1";
		$title_heightiqgroup = "title";
		$cssclasses = nxs_concatenateargswithspaces($cssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$title_heightiqgroup}");
	}
	
	if ($microdata != "")
	{
		$itemprop = "itemprop='name'";
	}
	else
	{
		$itemprop = "";
	}
	
	$result = '<' . $headingelement . ' ' . $itemprop . ' class="' . $cssclasses . '">' . $title . '</' . $headingelement . '>';
	
	// link
	if ($destination_articleid != "") 
	{
		$destination_url = nxs_geturl_for_postid($destination_articleid);
		$result = '<a href="' . $destination_url .'" '.$destination_target_html.' '.$destination_relation_html.'>' . $result . '</a>';
	}
	else if ($destination_url != "") 
	{
		$result = '<a href="' . $destination_url .'" '.$destination_target_html.' '.$destination_relation_html.'>' . $result . '</a>';
	}
	
	return $result;
}

function nxs_gethtmlforbutton($button_text, $button_scale, $button_color, $destination_articleid, $destination_url, $destination_target, $button_alignment, $destination_js, $text_heightiq, $button_fontzen, $destination_relation = false)
{
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

function nxs_gethtmlforbutton_mailchimp($button_text, $button_scale, $button_color, $button_alignment)
{
	$button_alignment = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
	$button_color = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	
	$result = '';
	$result .= '<p class="' . $button_alignment . '">';
	$result .= '<a target="' . $destination_target . '" class="nxs-button ' . $button_scale_cssclass . ' ' . $button_color . '" href="#" onclick="nxs_js_mailchimpsubmit(this); return false;">' . $button_text . '</a>';
	$result .= '</p>';
	
	return $result;
}

function nxs_gethtmlforimage($image_imageid, $image_border_width, $image_size, $image_alignment, $image_shadow, $image_alt, $destination_articleid, $destination_url, $image_title, $grayscale, $enlarge)
{
	$image_src = "";
	return nxs_gethtmlforimage_v2($image_imageid, $image_src, $image_border_width, $image_size, $image_alignment, $image_shadow, $image_alt, $destination_articleid, $destination_url, $image_title, $grayscale, $enlarge);
}

function nxs_gethtmlforimage_v2($image_imageid, $image_src, $image_border_width, $image_size, $image_alignment, $image_shadow, $image_alt, $destination_articleid, $destination_url, $image_title, $grayscale, $enlarge)
{
	$image_alt = trim($image_alt);
	$image_title = trim($image_title);

	if ($image_size == "")
	{
		$image_size = "auto-fit";
	}
	
	// Image metadata
	if ($image_imageid == "" && $image_src == "") 
	{
		return "";
	}
	if (!nxs_isimagesizevisible($image_size))
	{
		return "";
	}
	
	// Image shadow
	if ($image_shadow != "") {
		$image_shadow = 'nxs-shadow';
	}
	
	// Hover effects
	if ($enlarge != "") { $enlarge = 'nxs-enlarge'; }
	if ($grayscale != "") {	$grayscale = 'nxs-grayscale'; }
	
	// escape quotes used in title and alt, preventing malformed html
	$image_title = str_replace("\"", "&quote;", $image_title);
	$image_alt = str_replace("\"", "&quote;", $image_alt);
	
	$wpsize = nxs_getwpimagesize($image_size);
	
	if ($image_imageid != "")
	{
		$imagemetadata= wp_get_attachment_image_src($image_imageid, $wpsize, true);
	
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imageurl = nxs_img_getimageurlthemeversion($imageurl);
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
	}
	else if ($image_src != "")
	{
		$imageurl = $image_src;
	}
	
	$image_size_cssclass = nxs_getimagecsssizeclass($image_size);
	$image_alignment_cssclass = nxs_getimagecssalignmentclass($image_alignment); // "nxs-icon-left";
	
	// Border size
	$image_border_width = nxs_getcssclassesforlookup("nxs-border-width-", $image_border_width);
	
	// Image border
	$image_border = '';
	$image_border .= '<div class="nxs-image-wrapper ' . $image_shadow . ' ' . $image_size_cssclass . ' ' . $image_alignment_cssclass . ' ' . '">';
	$image_border .= '<div style="right: 0; left: 0; top: 0; bottom: 0; border-style: solid;" class="nxs-overflow ' . $image_border_width . '">';
	// note the display: block is essential/required! else the containing div
	// will have two additional pixels; kudos to http://stackoverflow.com/questions/8828215/css-a-2-pixel-line-appears-below-image-img-element
	$image_border .= '<img class="' . $grayscale . ' ' . $enlarge . '" ';
	$image_border .= 'src="' . $imageurl . '" ';
	if ($image_alt != "")
	{
		$image_border .= 'alt="' . $image_alt . '" ';
	}
	if ($image_title != "")
	{
		$image_border .= 'title="' . $image_title . '" ';
	}
	$image_border .= '/>';
	$image_border .= '</div>';
	$image_border .= '</div>';
	
	// Image shadow
	// TODO: make ddl too
	if ($image_shadow != "") 				{ $image_shadow = 'nxs-shadow'; }
	
	// Image link
	if ($destination_articleid != "") 
	{
		$destination_articleid = nxs_geturl_for_postid($destination_articleid);
		$image_border = '<a href="' . $destination_articleid .'">' . $image_border . '</a>';
	} else if ($destination_url != "") {
		$image_border = '<a href="' . $destination_url .'" target="_blank">' . $image_border . '</a>';
	}
	
	// Image
	$result = '';
	if ($image_imageid != "" || $image_src != "")
	{
		$result .= '<div class="nxs-relative">';
		$result .= $image_border;
		$result .= '</div>';
	}
	
	return $result;	
}

function nxs_isimagesizevisible($value)
{
	if ($value == "-")
	{
		$result = false;
	}
	else
	{
		$result = true;
	}	
	return $result;
}

function nxs_isimageautofit($value)
{
	if ($value == "auto-fit" || $value == "")
	{
		$result = true;
	}
	else
	{
		$result = false;
	}	
	return $result;
}

// returns the wordpress imagesize to use for the stored image_size property
function nxs_getwpimagesize($value)
{
	if ($value == "-")
	{
		nxs_webmethod_return_nack("unsupported value for getwpimagesize;" . $value);
	}
	else if ($value == "" || $value == "full" || $value == "auto-fit" || $value == "orig-max")
	{
		// default
		$result = "full";
	}
	else if (nxs_stringstartswith($value, "c@"))
	{
		// cropped
		$splitted = explode("@", $value);	// for example 1-0 (for factor 1.0)
		$factor = $splitted[1];
		if ($factor == "1-0")
		{
			$result = "thumbnail";
		}
		else if ($factor == "0-75")
		{
			$result = "thumbnail";
		}
		else if ($factor == "1-5")
		{
			$result = "nxs_cropped_200x200";
		}
		else if ($factor == "2-0")
		{
			$result = "nxs_cropped_200x200";
		}
		else
		{
			// not (yet) supported
			nxs_webmethod_return_nack("unsupported cropped factor;" . $factor);
		}
	}
	else
	{
		// not (yet) supported, assumed "full"
		$result = "full";
	}
	return $result;
}

function nxs_getcssclassesforsitepage()
{
	$metadata = nxs_getsitemeta();
				
	$site_page_colorzen = nxs_getcssclassesforlookup("nxs-colorzen-", $metadata["site_page_colorzen"]);
	$site_page_linkcolorvar = nxs_getcssclassesforlookup("nxs-linkcolorvar-", $metadata["site_page_linkcolorvar"]);
	
	$site_page_margin_top = nxs_getcssclassesforlookup("nxs-margin-top-", $metadata["site_page_margin_top"]);
	$site_page_padding_top = nxs_getcssclassesforlookup("nxs-padding-top-", $metadata["site_page_padding_top"]);
	$site_page_padding_bottom = nxs_getcssclassesforlookup("nxs-padding-bottom-", $metadata["site_page_padding_bottom"]);
	$site_page_margin_bottom = nxs_getcssclassesforlookup("nxs-margin-bottom-", $metadata["site_page_margin_bottom"]);
	$site_page_border_top_width = nxs_getcssclassesforlookup("nxs-border-top-width-", $metadata["site_page_border_top_width"]);
	$site_page_border_bottom_width = nxs_getcssclassesforlookup("nxs-border-bottom-width-", $metadata["site_page_border_bottom_width"]);
	$site_page_border_radius = nxs_getcssclassesforlookup("nxs-border-radius-", $metadata["site_page_border_radius"]);
	
	$result = nxs_concatenateargswithspaces($site_page_colorzen, $site_page_linkcolorvar, $site_page_margin_top, $site_page_padding_top, $site_page_padding_bottom, $site_page_margin_bottom, $site_page_border_top_width, $site_page_border_bottom_width, $site_page_border_radius);

	return $result;
}

function nxs_getcssclassesforrowcontainer($rowcontainerid)
{
	$metadata = nxs_get_postmeta($rowcontainerid);
	
	$rc_colorzen = nxs_getcssclassesforlookup("nxs-colorzen-", $metadata["rc_colorzen"]);;
	$rc_linkcolorvar = nxs_getcssclassesforlookup("nxs-linkcolorvar-", $metadata["rc_linkcolorvar"]);
	$rc_margin_top = nxs_getcssclassesforlookup("nxs-margin-top-", $metadata["rc_margin_top"]);
	$rc_margin_bottom = nxs_getcssclassesforlookup("nxs-margin-bottom-", $metadata["rc_margin_bottom"]);
	$rc_padding_top = nxs_getcssclassesforlookup("nxs-padding-top-", $metadata["rc_padding_top"]);
	$rc_padding_bottom = nxs_getcssclassesforlookup("nxs-padding-bottom-", $metadata["rc_padding_bottom"]);
	$rc_border_top_width = nxs_getcssclassesforlookup("nxs-border-top-width-", $metadata["rc_border_top_width"]);
	$rc_border_bottom_width = nxs_getcssclassesforlookup("nxs-border-bottom-width-", $metadata["rc_border_bottom_width"]);
	$rc_border_radius = nxs_getcssclassesforlookup("nxs-border-radius-", $metadata["rc_border_radius"]);
	
	$rc_cssclass = $metadata["rc_cssclass"];
	
	$postclass = "nxs-post-{$rowcontainerid}";
	
	// editable classes
	if (nxs_cap_hasdesigncapabilities())
	{
		$layouteditable = "nxs-layout-editable";
		$widgetseditable = "nxs-widgets-editable";
	}
	else
	{
		$layouteditable = "";	// layout is not editable
		$widgetseditable = "nxs-widgets-editable";
	}
	
	//
	$elementscontainer = "nxs-elements-container";
	
	$result = nxs_concatenateargswithspaces($rc_colorzen, $rc_linkcolorvar, $rc_margin_top, $rc_padding_top, $rc_padding_bottom, $rc_margin_bottom, $rc_border_top_width, $rc_border_bottom_width, $rc_border_radius, $layouteditable, $widgetseditable, $elementscontainer, $postclass);
	
	return $result;
}

function nxs_getcssclassesforrow($metadata)
{
	$r_colorzen = nxs_getcssclassesforlookup("nxs-colorzen-", $metadata["r_colorzen"]);
	$r_linkcolorvar = nxs_getcssclassesforlookup("nxs-linkcolorvar-", $metadata["r_linkcolorvar"]);
	$r_margin_top = nxs_getcssclassesforlookup("nxs-margin-top-", $metadata["r_margin_top"]);
	$r_margin_bottom = nxs_getcssclassesforlookup("nxs-margin-bottom-", $metadata["r_margin_bottom"]);
	$r_padding_top = nxs_getcssclassesforlookup("nxs-padding-top-", $metadata["r_padding_top"]);
	$r_padding_bottom = nxs_getcssclassesforlookup("nxs-padding-bottom-", $metadata["r_padding_bottom"]);
	$r_border_top_width = nxs_getcssclassesforlookup("nxs-border-top-width-", $metadata["r_border_top_width"]);
	$r_border_bottom_width = nxs_getcssclassesforlookup("nxs-border-bottom-width-", $metadata["r_border_bottom_width"]);
	$r_border_radius = nxs_getcssclassesforlookup("nxs-border-radius-", $metadata["r_border_radius"]);
	
	$r_cssclass = $metadata["r_cssclass"];
	
	// unistyle css classes	
	if (isset($metadata["unistyle"]) && $metadata["unistyle"] != "")
	{
		$r_unistyleindicator_cssclass = "nxs-unistyled";
		$r_unistyle_cssclass = "nxs-unistyle-" . nxs_stripspecialchars($metadata["unistyle"]);
	}
	else
	{
		$r_unistyleindicator_cssclass = "nxs-not-unistyled";
		$r_unistyle_cssclass = "";
	}
	
	$result = nxs_concatenateargswithspaces($r_unistyleindicator_cssclass, $r_unistyle_cssclass, $r_colorzen, $r_linkcolorvar, $r_margin_top, $r_padding_top, $r_padding_bottom, $r_margin_bottom, $r_border_top_width, $r_border_bottom_width, $r_border_radius, $r_cssclass);
	
	return $result;
}

function nxs_style_getstyletypevalues($styletype)
{
	$options = nxs_getstyletypeoptions();
	
	if (!array_key_exists($styletype, $options))
	{
		nxs_webmethod_return_nack("unsupported styletype;" . $styletype);
	}
	if (!array_key_exists("values", $options[$styletype]))
	{
		nxs_webmethod_return_nack("no 'values' property found for styletype;" . $styletype);
	}
	$result = $options[$styletype]["values"];
	return $result;
}

function nxs_style_getstyletypevaluesandicons($styletype)
{
	$options = nxs_getstyletypeoptions();
	
	if (!array_key_exists($styletype, $options))
	{
		nxs_webmethod_return_nack("unsupported styletype;" . $styletype);
	}
	if (!array_key_exists("values", $options[$styletype]))
	{
		nxs_webmethod_return_nack("no 'values' property found for styletype;" . $styletype);
	}
	if (!array_key_exists("icons", $options[$styletype]))
	{
		$result = array(
			"values" => $options[$styletype]["values"],
		);
	} else {
		$result = array(
			"values" => $options[$styletype]["values"],
			"icons" => $options[$styletype]["icons"]
		);
	}
	return $result;
}

function nxs_style_getsubtype($styletype)
{
	$options = nxs_getstyletypeoptions();
	if (!array_key_exists($styletype, $options))
	{
		nxs_webmethod_return_nack("unsupported styletype;" . $styletype);
	}
	if (!array_key_exists("subtype", $options[$styletype]))
	{
		nxs_webmethod_return_nack("no 'subtype' property found for styletype;" . $styletype);
	}
	$result = $options[$styletype]["subtype"];
	return $result;
}

// returns all styletypes that can be used in the system
// each styletype will produce CSS specific style
// each styletype returned has to have a corresponding function nxs_style_XYZ_getstyletypevalues
function nxs_getstyletypes()
{
	$result = array();
	
	$options = nxs_getstyletypeoptions();
	foreach ($options as $currentstyletype => $currentstyletypesuboptions)
	{
		$result[] = $currentstyletype;
	}
	
	return $result;
}

// converts a certain factor into a textrepresentation,
// for example "1.5" becomes "1-5".
function nxs_getdashedtextrepresentation_for_numericvalue($currentvalue)
{
	// convert "1.5" to "1-5"
	$key = "" . $currentvalue;	// tostring
	$key = str_replace(".", "-", $key);
	// 
	if (strpos($key, "-") === false) 
	{
		// and convert "1" to "1-0"
		$key = $key . "-0";
	}
	return $key;
}

function nsx_style_validate_dropdownitems($styletypevalues, $subtype)
{
	$result = array();

	foreach ($styletypevalues as $currentkey => $currentvalue)
	{
		if ($subtype == "multiplier")
		{
			if (is_numeric($currentvalue))
			{
				$key = nxs_getdashedtextrepresentation_for_numericvalue($currentvalue);
				$result[$key] = "" . $currentvalue . "x";	// for example "1.5x"
			}
			else
			{
				if ($currentkey == "")
				{
					// we will use the key as the indexer of the array,
					// its not allowed to use "" for the indexer,
					// thus we use a self defined constant
					$key = "@@@nxsempty@@@";
				}
				else
				{
					$key = $currentkey;	// tostring
				}
				
				// check for duplicates
				if (array_key_exists($key, $result))
				{
					nxs_webmethod_return_nack("duplicate key found;" . $key);
				}
				$result[$key] = $currentvalue;
			}
		}
		else if ($subtype == "encodedmultiplier")
		{
			if ($currentkey == "")
			{
				// we will use the key as the indexer of the array,
				// its not allowed to use "" for the indexer,
				// thus we use a self defined constant
				$key = "@@@nxsempty@@@";
			}
			else
			{
				$key = $currentkey;	// tostring
			}
			
			// check for duplicates
			if (array_key_exists($key, $result))
			{
				nxs_webmethod_return_nack("duplicate key found;" . $key);
			}
			$result[$key] = $currentvalue;
		}
		else if ($subtype == "textlookup")
		{
			if ($currentkey == "")
			{
				// we will use the key as the indexer of the array,
				// its not allowed to use "" for the indexer,
				// thus we use a self defined constant
				$key = "@@@nxsempty@@@";
			}
			else
			{
				$key = $currentkey;	// tostring
			}
			
			$result[$key] = "" . $currentvalue;
		}
		else
		{
			nxs_webmethod_return_nack("unsupported subtype;" . $subtype);
		}
	}

	return $result;
}

function nxs_style_getdropdownitems($styletype)
{
	$result = array();
	
	$styletypevalues = nxs_style_getstyletypevalues($styletype);
	
	// if not associative, make associative!
	//if (!nxs_isassociativearray($styletypevalues))
	//{
	//	$styletypevalues = nxs_convertindexarraytoassociativearray($styletypevalues);
	//}
	
	$subtype = nxs_style_getsubtype($styletype);

	$result = nsx_style_validate_dropdownitems($styletypevalues, $subtype);
	
	return $result;
}

function nxs_style_getradiobuttonsitems($styletype)
{
	$result = array();
	
	$styletypevalues = nxs_style_getstyletypevaluesandicons($styletype);
	
	$subtype = nxs_style_getsubtype($styletype);
	
	$values = nsx_style_validate_dropdownitems($styletypevalues["values"], $subtype);
	
	if ($styletypevalues["icons"]) {
		$icons = $styletypevalues["icons"];
		$result = array_merge_recursive($values, $icons);
	} else {
		$result = $values;
	}
	
	return $result;
}

function nxs_numerics_to_comma_sep_array_string($values)
{
	$locale = localeconv();
	$decimalseperator = $locale["decimal_point"];
	
	// note that the values could contain values like 0.8,
	// which when converted to strings could become misinterpreted
	// as "0,8", messing up our wanted behaviour
	// we therefore first build the string with SEP as the seperator
	// for example [0,8SEP1,2]
	$itemseperator = ",";
	$seperatorplaceholder = "[NXS:SEP]";
	$result = "[" . implode($seperatorplaceholder, $values) . "]";
	// convert any possible locale issues to dot's [0.8SEP1.2]
	$result = str_replace($decimalseperator, ".", $result);
	// convert seperator to commas [0.8,1.2]
	$result = str_replace($seperatorplaceholder, $itemseperator, $result);
	
	return $result;
}

// is used to initialize for example colors or widths or other variations
// clientside (used as part of creating CSS for, for example multipliers)
function nxs_style_getstyletypevaluesjsinitialization($styletype)
{
	$result = "";
	$subtype = nxs_style_getsubtype($styletype);
	$styletypevalues = nxs_style_getstyletypevalues($styletype);
	
	if ($subtype == "multiplier")
	{
		// filter out all non-numeric items (like "" / "-")
		// these cannot be initialized (they are used to show up 
		// in the DDL's only
		$styletypevalues = array_filter($styletypevalues, "is_numeric");
		$result = nxs_numerics_to_comma_sep_array_string($styletypevalues);
	}
	else if ($subtype == "encodedmultiplier")
	{
		$decoded = array();
		foreach ($styletypevalues as $currentkey => $currentstyletypevalue)
		{
			$splitted = explode("@", $currentkey);
			if (count($splitted) == 2)
			{
				if ($splitted[0] == "c")
				{
					// convert c@1-0 to 1.0
					$stringvalue = str_replace("-", ".", $splitted[1]);
					$decoded[] = $stringvalue;
				}
				else
				{
				}
			}
			else
			{
			}
		}

		$result = "[" . implode(",", $decoded) . "]";
	}
	else if ($subtype == "textlookup")
	{
		// filter out all non-numeric items (like "" / "-")
		// these cannot be initialized (they are used to show up 
		// in the DDL's only
		if (count($styletypevalues) > 1)
		{
			$result = "['" . implode("','", $styletypevalues) . "']";
		}
		else if (count($styletypevalues) == 1)
		{
			// this clause ensures 
			$result = "['" . $styletypevalues[0] . "']";
		}
		else if (count($styletypevalues) == 0)
		{
			$result = "[]";
		}
	}
	else
	{
		nxs_webmethod_return_nack("unexpected subtype:" . $subtype);
	}
	return $result;
}

// kudos to http://stackoverflow.com/questions/14114411/remove-all-special-characters-from-a-string
// tags: characters remove strip strange chars
function nxs_stripspecialchars($input) 
{
	return preg_replace('/[^A-Za-z0-9]/', '', $input); // Removes special chars.
}

function nxs_stripkeepdigits($input) 
{
	return preg_replace('/[^0-9]/', '', $input); // Removes special chars.
}

// converts array ["a", "b"] to "a"=>"a", "b"=>"b"
function nxs_convertindexarraytoassociativearray($items)
{
	$result = array();
	foreach ($items as $currentitem)
	{
		$result[$currentitem] = $currentitem;
	}
	return $result;
}

/*******
*******/

// TODO: eventually this function should be phased out; 
// the widgets should invoke nxs_genericpopup_getpopuphtml($args) directly themselves

function nxs_widgets_getgenericpopuphtml($args, $widget_name)
{
	return nxs_genericpopup_getpopuphtml($args);
}

// TODO: eventually this function should be phased out; 
function nxs_widgets_getgeneric_mediapicker_popup($args)
{
	nxs_requirepopup_genericpopup("mediapicker");
	return nxs_popup_genericpopup_mediapicker_getpopup($args);
}

// TODO: eventually this function should be phased out; 
function nxs_widgets_updateplaceholderdatageneric($metadatatoupdate, $widget)
{
	return nxs_widgets_mergeunenrichedmetadata($widget, $metadatatoupdate);
}

function webmethod_return($args)
{
	return nxs_webmethod_return($args);
}

function webmethod_return_nack($args)
{
	nxs_webmethod_return_nack($args);
}

function webmethod_return_ok($args)
{
	nxs_webmethod_return_ok($args);
}

function nxs_loadplugin_twittertweets()
{
	// load api
	$filetoinclude = NXS_FRAMEWORKPATH . '/plugins/display-tweets-php/includes/Twitter/api.php';
	require_once($filetoinclude);
	
	// dispatch requests made by/for twitter
	nxs_twitter_dispatchrequest();
}

function nxs_addfeedsupport()
{
	// text
	add_filter("the_content_feed", "nxs_the_content_feed");
	add_filter("the_excerpt_rss", "nxs_the_excerpt_rss");
	// image
	add_filter("the_content_feed", "nxs_ext_feed_img");
	add_filter("the_excerpt_rss", "nxs_ext_feed_img");
	// title
	add_filter("the_content_feed", "nxs_ext_feed_title");
	add_filter("the_excerpt_rss", "nxs_ext_feed_title");
}

function nxs_ext_feed_title($content)
{
	global $post;
	$postid = $post->ID;
	$title = nxs_gettitle_for_postid($postid);
	$url = nxs_geturl_for_postid($postid);
	$nxscontent = "<h1><a target='_blank' href='$url'>$title</a></h1>";
	$content = $nxscontent . $content;
	
  return $content;
}

function nxs_ext_feed_img($content)
{
	global $post;
	$postid = $post->ID;
	$imageid = nxs_get_key_imageid_in_post($postid);
	$nxscontent = "";
	if ($imageid != 0)
	{
		$url = nxs_geturl_for_postid($postid);
	
		$image_attributes = wp_get_attachment_image_src($imageid, "full", false);
		$src = $image_attributes[0];
		$src = nxs_img_getimageurlthemeversion($src);
		$width = $image_attributes[1];
		$height = $image_attributes[2];
		//$nxscontent = "<img src='{$src}' width='{$width}' height='{$height}' /><br />" . $content;
		$nxscontent .= "<a target='_blank' href='$url'>";
		$nxscontent .= "<img src='{$src}' style='width:200px; display: block; float: left; padding-right: 10px; padding-bottom: 5px;' />";
		$nxscontent .= "</a>";
	}
	$content = $nxscontent . $content;
  return $content;
}

function nxs_the_excerpt_rss($content)
{
	
	$content = nxs_the_content_feed($content, $feedtype);
	$content = str_replace("\n", " ", $content);
	$content = str_replace("&nbsp;", " ", $content);
	
	$content = wp_strip_all_tags($content, true);
	
	$content = wp_trim_words( $content, 5*40, '<a href="'. get_permalink() .'"> ...Read More</a>' );
	
	//$content = preg_replace('/[^A-Za-z0-9()!:.\' ]/', '', $content); // Removes special chars.
	// prefix the image
	
	//$content = "GJGJ";

	return $content;
}

function nxs_the_content_feed($content, $feedtype)
{
	$origcontent = $content;
	
	// eerst de output van de nxs structuur,
	// aangevuld met de $content
	$nxscontent = "";
	global $post;
	$postid = $post->ID;
	
	$textblocks = nxs_get_text_blocks_on_page_v3($postid, "", "none");
	foreach ($textblocks as $currenttextblock)
	{
		$nxscontent .= $currenttextblock;
	}
	
	$content = $nxscontent . $origcontent;

	// 
	$content = html_entity_decode($content, ENT_QUOTES, 'UTF-8')  ;
	
  return $content;
}

function nxs_addwoocommercesupport()
{
	// yes we do
	add_theme_support("woocommerce");
	
	// the rendering of woocommerce pages, headers, etc. is handled by the pagetemplates, we ignore
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
	
	// TODO: we could make this optional; perhaps some people like the default wc styles?
	// since wooc 2.1.0 the css has to be turned off programmatically
	add_filter( 'woocommerce_enqueue_styles', '__return_false' );
}

function nxs_is_nxswebservice()
{
	$result = defined('NXS_DEFINE_NXSWEBWEBMETHOD');
	return $result;
}

function nxs_busrules_getgenericoptions()
{
	$options = array
	(
		"fields" => array
		(
			//
			// OUTPUT 
			//
			
			array( 
				"id" 				=> "wrapper_template_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Template", "nxs_td"),
			),
			array
			( 
				"id"								=> "header_postid",
				"type" 							=> "selectpost",
				"post_status"				=> array("publish", "future"),
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Header", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a header to show on the top of your page", "nxs_td"),
				"post_type" 				=> "nxs_header",
				"buttontext" 				=> nxs_l18n__("Style header", "nxs_td"),
				"emptyitem_enable"	=> false,
				"beforeitems" 			=> nxs_widgets_busrule_pagetemplates_getbeforeitems(),
			),
			array
			( 
				"id"								=> "footer_postid",
				"type" 							=> "selectpost",
				"post_status"				=> array("publish", "future"),
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Footer", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a header to show on the top of your page", "nxs_td"),
				"post_type" 				=> "nxs_footer",
				"emptyitem_enable"	=> false,
				"beforeitems" 			=> nxs_widgets_busrule_pagetemplates_getbeforeitems(),
			),
			array
			( 
				"id"								=> "sidebar_postid",
				"type" 							=> "selectpost",
				"post_status"				=> array("publish", "future"),
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Sidebar", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a sidebar to show on the right side of your page", "nxs_td"),
				"post_type" 				=> "nxs_sidebar",
				"emptyitem_enable"	=> false,
				"beforeitems" 			=> nxs_widgets_busrule_pagetemplates_getbeforeitems(),
			),
			array
			( 
				"id"								=> "subheader_postid",
				"type" 							=> "selectpost",
				"post_status"				=> array("publish", "future"),
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Sub header", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a sub header to show above your main content", "nxs_td"),
				"post_type" 				=> "nxs_subheader",
				"emptyitem_enable"	=> false,
				"beforeitems" 			=> nxs_widgets_busrule_pagetemplates_getbeforeitems(),
			),
			array
			( 
				"id"								=> "subfooter_postid",
				"type" 							=> "selectpost",
				"post_status"				=> array("publish", "future"),
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Sub footer", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a sub footer to show below your main content", "nxs_td"),
				"post_type" 				=> "nxs_subfooter",
				"emptyitem_enable"	=> false,
				"beforeitems" 			=> nxs_widgets_busrule_pagetemplates_getbeforeitems(),
			),
			array
			( 
				"id"								=> "pagedecorator_postid",
				"type" 							=> "selectpost",
				"post_status"				=> array("publish", "future"),
				"post_type" 				=> "nxs_genericlist",
				"subposttype" => "pagedecorator", 
				"previewlink_enable"=> "false",
				"label" 						=> nxs_l18n__("Decorator", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a decorator to decorate your page", "nxs_td"),
				"emptyitem_enable"	=> false,
				"beforeitems" 			=> nxs_widgets_busrule_pagetemplates_getbeforeitems(),
			),
			array
			( 
				"id"								=> "content_postid",
				"type" 							=> "selectpost",
				"post_status"				=> array("publish", "future"),
				"previewlink_enable"=> "false",
				"post_type" 				=> "nxs_templatepart",
				"subposttype"				=> "content",				
				"label" 						=> nxs_l18n__("Frontend Content", "nxs_td"),
				"tooltip" 					=> nxs_l18n__("Select a content template", "nxs_td"),
				"emptyitem_enable"	=> false,
				"beforeitems" 			=> nxs_widgets_busrule_pagetemplates_getbeforeitems(),
			),
			array
			( 
				"id"								=> "wpcontenthandler",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("WP Content", "nxs_td"),
				"defaultblankvalue" => "@leaveasis",
				"dropdown" 		=> array
				(
					"@leaveasis" =>nxs_l18n__("Leave as is", "nxs_td"),
					"@template@onlywhenset"	=>nxs_l18n__("Show if contains content", "nxs_td"), 
					"@template@always"		=>nxs_l18n__("Always show", "nxs_td"), 
					"@template@never"			=>nxs_l18n__("Never show", "nxs_td"), 
				)
			),
			array( 
				"id" 				=> "wrapper_template_end",
				"type" 				=> "wrapperend"
			),
			
			//RULE CONTROL
			
			array( 
				"id" 				=> "wrapper_flowcontrol_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Flowcontrol", "nxs_td"),
			),
			
			array(
				"id" 				=> "flow_stopruleprocessingonmatch",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Stop flow on match", "nxs_td"),
			),	
		
			array( 
				"id" 				=> "wrapper_flowcontrol_end",
				"type" 				=> "wrapperend"
			),
		) 
	);

	return $options;
}

function nxs_lookuptable_getprefixtoken()
{
	return "{{";
}

function nxs_lookuptable_getpostfixtoken()
{
	return "}}";
}

// generic function to translate certain keys from metadata (listed in "fields"),
// with a value marked in between the specified prefix and postfix tokens (p.e. {{ and }})
// with the specified lookup array (placeholders, lookup)
function nxs_filter_translategeneric($metadata, $fields, $prefixtoken, $postfixtoken, $lookup)
{
	$patterns = array();
	$replacements = array();
	$build = true;
	
	foreach ($fields as $currentfield)
	{
		$source = $metadata[$currentfield];
		if (isset($source))
		{
			if (nxs_stringcontains($source, $prefixtoken))
			{				
				// very likely there's a lookup used, let's replace the tokens!
				
				if ($build)
				{
					$build = false;

					// optimization; only do this when the lookup is not yet set,
					// note this can be further optimized					
					foreach ($lookup as $key => $val)
					{
						$patterns[] = '/' . $prefixtoken . $key . $postfixtoken . '/';
						$replacements[] = $val;
					}
				}

				// the actual replacing of tokens for this item
				$metadata[$currentfield] = preg_replace($patterns, $replacements, $metadata[$currentfield]);
			}
			else
			{
				// ignore
			}
		}
	}
	
	return $metadata;
}

// 
function nxs_filter_translatelookup($metadata, $fields)
{
	$prefixtoken = nxs_lookuptable_getprefixtoken();
	$postfixtoken = nxs_lookuptable_getpostfixtoken();

	$includeruntimeitems = true;
	$lookup = nxs_lookuptable_getlookup_v2($includeruntimeitems);
	
	$result = nxs_filter_translategeneric($metadata, $fields, $prefixtoken, $postfixtoken, $lookup);
	return $result;
}

// obsolete function
function nxs_filter_getwidgetmetadata_localize($result)
{
	return nxs_localization_localize($result);
}

// retrieves widget metadata, taking into consideration
// localized fields that could be applicable for this widget,
// if widget meta = "title" => "foo" and "title_hl_nl" => "bar",
// the function will return meta as "title" = "bar" and "title_hl_nl" = "bar" if the hl language is set to "nl".
function nxs_localization_localize($result)
{
	if (count($result) == 0)
	{
		// lookup will fail
	}
	else
	{
		nxs_requirepopup_contextprocessor("widgets");
		
		$widget = $result["type"];
		nxs_requirewidget($widget);
		$sheet = "home";
		
		$options = nxs_popup_contextprocessor_widgets_getoptions_widgetsheet($widget, $sheet);
		$localizablefieldids = nxs_localization_getlocalizablefieldids($options);
		if (!nxs_localization_usenativelanguage())
		{
			// translation is required
			$currenthllanguage = nxs_localization_getcurrent_hl_language();
			
			foreach ($localizablefieldids as $currentlocalizablefieldid)
			{
				// override widgetmeta["{$id}"] with widgetmeta["{$id}_hl_{$currenthllanguage}"]
				
				//echo $currentlocalizablefieldid . " / " . $currentlocalizablefieldid . "_hl_" . $currenthllanguage;
				//echo $result[$currentlocalizablefieldid] . " / " . $result[$currentlocalizablefieldid . "_hl_" . $currenthllanguage];
				
				if ($result[$currentlocalizablefieldid . "_hl_" . $currenthllanguage] == "")
				{
					// fallback scenario; we stick to the native language if the translated version is empty
					$result[$currentlocalizablefieldid];
				}
				else
				{
					$result[$currentlocalizablefieldid] = $result[$currentlocalizablefieldid . "_hl_" . $currenthllanguage];
				}
			}
		}
		else
		{
			// stick to native language		
		}
	}
	return $result;
}

function nxs_widgets_busrule_pagetemplates_getbeforeitems()
{
	$result = array
	(
		"@leaveasis"=>nxs_l18n__("Leave as is", "nxs_td"),
		"@suppressed"=>nxs_l18n__("Suppress", "nxs_td"),
	);
	return $result;
}

function nxs_widgets_busrule_pagetemplate_renderrow($iconids, $filteritemshtml, $mixedattributes)
{
	if (is_string($iconids))
	{
		// convert string to single array
		$iconid = $iconids;
		$iconids = array($iconid);
	}
	
	$flow_stopruleprocessingonmatch = $mixedattributes["flow_stopruleprocessingonmatch"];
	?>
	<div class="nxs-dragrow-handler nxs-padding-menu-item">
		<div class="content2">
			<div class="box-content nxs-width20 nxs-float-left">
			 	<div class="">
			 		<?php
			 		foreach ($iconids as $currenticonid)
			 		{
						//var_dump($currenticonid);
			 			?>
						<span class="<?php echo $currenticonid; ?>"></span>
						<?php
					}
					?>
					<?php
						if ($flow_stopruleprocessingonmatch != "")
				  	{
							?>
							<span class="nxs-icon-blocked"></span>
							<?php
				  	}
				  	else
				  	{
				  		?>
							<span class="nxs-icon-arrow-down"></span>
							<?php
				  	}
					?>
		    	</div>
		  	</div>
			<div class="box-content nxs-width30 nxs-float-left">
				<?php
				if ($filteritemshtml != "")
				{
					echo $filteritemshtml;
				}
				else
				{
					echo "&nbsp;";
				}
				?>
			</div>
		  	<div class="box-content nxs-width50 nxs-float-left">
			  	<?php
					echo nxs_getbusinessruleimpact($mixedattributes);
			  	?>
			</div>
	  		<div class="nxs-clear"></div>
	  	</div>
	</div>
	<?php
}

add_filter('sanitize_file_name_chars', 'nxs_sanitize_chars');
function nxs_sanitize_chars($chars)
{
    $chars[] = '+';
    $chars[] = '%';
    return $chars;
}

// kudos to http://stackoverflow.com/questions/11267086/php-unlink-all-files-within-a-directory-and-then-deleting-that-directory
function nxs_recursive_removedirectory($directory)
{
	if ($directory == "")
	{
		return;
	}
	if (!file_exists($directory))
	{
		return;
	}
	if (!is_dir($directory))
	{
		return;
	}
	
	//
  foreach(glob("{$directory}/*") as $file)
  {
		if(is_dir($file)) 
		{ 
			nxs_recursive_removedirectory($file);
		} 
		else 
		{
			unlink($file);
		}
  }
  rmdir($directory);
}

// kudos to http://stackoverflow.com/questions/5707806/recursive-copy-of-directory
function nxs_recursive_copyfolders($source, $dest)
{
	$skipitemscontaininglowercase = array();
	return nxs_recursive_copyfolders_v2($source, $dest, $skipitemscontaininglowercase);
}

function nxs_recursive_copyfolders_v2($source, $dest, $skipitemscontaininglowercase)
{
	foreach 
	(
		$iterator = new RecursiveIteratorIterator
	 	(
	 		new RecursiveDirectoryIterator
	 		(
	 			$source, 
	 			RecursiveDirectoryIterator::SKIP_DOTS
	 		), 
	 		RecursiveIteratorIterator::SELF_FIRST
	 	) as $item
	) 
	{
		//var_dump($item);
		//echo "item:<br />";
		
	  if ($item->isDir()) {
	    mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
	  } 
	  else 
	  {
	  	//echo "copying $name<br />";
	  	
	  	$name = $iterator->getSubPathName();
	  	$lowername = strtolower($name);
	  	$shouldinclude = true;
	  	foreach ($skipitemscontaininglowercase as $excludeitem)
	  	{
	  		$lowercaseexcludeitem = strtolower($excludeitem);
	  		if (nxs_stringcontains($lowername, $lowercaseexcludeitem))
	  		{
	  			$shouldinclude = false;
	  		}
	  	}
	  	
	  	if ($shouldinclude)
	  	{
	  		$sourcepath = $source . DIRECTORY_SEPARATOR . $name;
	  		$destinationpath = $dest . DIRECTORY_SEPARATOR . $name;
	    	$r = copy($sourcepath, $destinationpath);
	    	
	    	// lenght limitations apply! if the folder becomes too long,
	    	// the copying could fail
	  		//$lengte = strlen($sourcepath);
	  		//$lengte = strlen($destinationpath);
	  		//echo "COPIED TO FILE; (name length:$lengte) $destinationpath <br />";

	    	if ($r === false)
	    	{
	    		$errors= error_get_last();
	    		var_dump($errors);
	    		
	    		nxs_webmethod_return_nack("error ; copying of $sourcepath failed");
	    	}

	    }
	    else
	    {
	    	echo "excluding $name<br />";
	    }
	  }
	}
}

function nxs_function_invokefunction($functionnametoinvoke, $args)
{
	if (function_exists($functionnametoinvoke))
	{
		$parameters = array( &$args );
		$result = call_user_func_array($functionnametoinvoke, $parameters);
	}
	else
	{
		nxs_webmethod_return_nack("function not found; $functionnametoinvoke");
	}
	
	return $result;
}

// custom post type cpt
function nxs_registernexustype_withtaxonomies($title, $taxonomies, $ispublic)
{
	if ($title == "")
	{
		nxs_webmethod_return_nack("title not set");
	}

	$show_ui = false;
	if ($_REQUEST["shownexustypesinbackend"] == "true")
	{
		$show_ui = true;
	}
	
	$hasadmin = nxs_has_adminpermissions();
	
	register_post_type
	( 
		'nxs_' . $title,
		array
		(
			'labels' => array
			(
				'name' => __('Nxs ' . $title),
				'singular_name' => __('Nexus Struct ' . $title)
			),
			'public' => $ispublic,
			'has_archive' => false,
			'exclude_from_search' => true,
			'publicly_queryable' => true,	// Whether queries can be performed on the front end as part of parse_request(). MOET OP TRUE !
			'show_in_nav_menus' => false, 	// Whether post_type is available for selection in navigation menus.
			'show_ui' => $show_ui, 	// True, if you want this type to show in in WP backend's menu (see show_in_menu too!)
			'show_in_menu' => true,	// True, if you want this type to show in in WP backend's menu (see show_ui too!)
			'show_in_admin_bar' => false,
			
			'supports' => array
			(
				'title', 
				'custom-fields',
			),
			
			'taxonomies' => $taxonomies,
			'hierarchical' => false,
			'query_var' => $hasadmin,	// only admin/authenticated users should be able to query
			'rewrite' => false,
		)
	);
}

if(!function_exists('mb_list_encodings')) 
{ 
	function mb_list_encodings()
	{
		$list_encoding = array("pass", "auto", "wchar", "byte2be", "byte2le", "byte4be", "byte4le", "BASE64", "UUENCODE", "HTML-ENTITIES", "Quoted-Printable", "7bit", "8bit", "UCS-4", "UCS-4BE", "UCS-4LE", "UCS-2", "UCS-2BE", "UCS-2LE", "UTF-32", "UTF-32BE", "UTF-32LE", "UTF-16", "UTF-16BE", "UTF-16LE", "UTF-8", "UTF-7", "UTF7-IMAP", "ASCII", "EUC-JP", "SJIS", "eucJP-win", "SJIS-win", "JIS", "ISO-2022-JP", "Windows-1252", "ISO-8859-1", "ISO-8859-2", "ISO-8859-3", "ISO-8859-4", "ISO-8859-5", "ISO-8859-6", "ISO-8859-7", "ISO-8859-8", "ISO-8859-9", "ISO-8859-10", "ISO-8859-13", "ISO-8859-14", "ISO-8859-15", "EUC-CN", "CP936", "HZ", "EUC-TW", "BIG-5", "EUC-KR", "UHC", "ISO-2022-KR", "Windows-1251", "CP866", "KOI8-R");
		return $list_encoding;
	}
}

// downwards compatibility
if ( ! function_exists( 'wp_slash' ) ) {
	/**
	 * Add slashes to a string or array of strings.
	 *
	 * This should be used when preparing data for core API that expects slashed data.
	 * This should not be used to escape data going directly into an SQL query.
	 *
	 * @since 3.6.0
	 *
	 * @param string|array $value String or array of strings to slash.
	 * @return string|array Slashed $value
	 */
	function wp_slash( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				if ( is_array( $v ) ) {
					$value[$k] = wp_slash( $v );
				} else {
					$value[$k] = addslashes( $v );
				}
			}
		} else {
			$value = addslashes( $value );
		}

		return $value;
	}
}

function nxs_getheadmeta()
{
	$result = "";
	$result = apply_filters("nxs_f_getheadmeta", $result);
	
	return $result;
}

function nxs_getcurrentuserrole()
{
	global $wp_roles;
	$current_user = wp_get_current_user();
	$roles = $current_user->roles;
	$role = array_shift($roles);
	return isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
}

function nxs_iseditor()
{
	$role = nxs_getcurrentuserrole();
	if ($role == "Editor")
	{
		return true;
	}
	return false;
}

function nxs_wp_getpostidbymeta($key, $value)
{
	$result = "";
	
	// find post that has a particular key/value combination as metadata
	global $wpdb;
	$r = $wpdb->get_results("SELECT p.ID
	  FROM $wpdb->posts as p
	  LEFT JOIN 
	      $wpdb->postmeta as m on (p.ID = m.post_id and 
	                                        m.meta_key = '{$key}')
	                                        where m.meta_value = '{$value}'
	                                        ");
	
	if (count($r) == 1)
	{
		$post = $r[0];
		$result = $post->ID;
	}
	else if (count($r) == 0)
	{
		$result = "";
	}
	if (count($r) > 1)
	{
		echo "fatal; multiple posts found with $key $value <br />";
		var_dump($r);
		die();
	}
	
	return $result;
}

function nxs_wp_getpostidsbymeta($key, $value)
{
	$result = array();
	
	// find post that has nxs_themeid as metadata
	global $wpdb;
	$r = $wpdb->get_results("SELECT p.ID
	  FROM $wpdb->posts as p
	  LEFT JOIN 
	      $wpdb->postmeta as m on (p.ID = m.post_id and 
	                                        m.meta_key = '{$key}')
	                                        where m.meta_value = '{$value}'
	                                        ");
	
	foreach ($r as $post)
	{
		$result[] = $post->ID;
	}
	
	return $result;
}

function nxs_reseller_getreseller()
{
	$result = esc_attr(get_option('nxs_reseller'));
	return $result;
}

function nxs_reseller_setreseller($reseller)
{
	update_option('nxs_reseller', "reseller");
}

function nxs_warranty_getwarrantystate()
{
	$result = esc_attr(get_option('nxs_warrantystate'));
	return $result;
}

function nxs_warranty_break()
{
	update_option('nxs_warrantystate', "broken");
}

// appends a new "one" row, with the specified widget properties to an existing post
function nxs_add_widget_to_post($args) 
{	
	extract($args);
	
	if ($postid == "") { nxs_webmethod_return_nack("postid not set"); }
	if ($widgetmetadata == "") { nxs_webmethod_return_nack("widgetmetadata not set"); }
	if ($widgetmetadata["type"] == "") { nxs_webmethod_return_nack("typeof widgetmetadata not set"); }
	if ($widgetmetadata["postid"] != "") { nxs_webmethod_return_nack("postid of widgetmetadata should be empty"); }
	if ($widgetmetadata["placeholderid"] != "") { nxs_webmethod_return_nack("placeholderid of widgetmetadata should be empty"); }
	
	$pagerowtemplate = "one";
	
	$wpposttype = nxs_getwpposttype($postid);
	$nxsposttype = nxs_getnxsposttype_by_postid($postid);
	$pagetemplate = nxs_getpagetemplateforpostid($postid);
	
	$prtargs = array();
	$prtargs["invoker"] = "code";
	$prtargs["wpposttype"] = $wpposttype;
	$prtargs["nxsposttype"] = $nxsposttype;
	$prtargs["pagetemplate"] = $pagetemplate;		
	$postrowtemplates = nxs_getpostrowtemplates($prtargs);

	// verify the pagerowtemplate is allowed to be placed
	if (!in_array($pagerowtemplate, $postrowtemplates)) { nxs_webmethod_return_nack("unsupport pagerowtemplate?"); }
	
	// get poststructure (list of rowindex, pagerowtemplate, pagerowattributes, content)
	$poststructure = nxs_parsepoststructure($postid);
	
	$pagerowid = nxs_allocatenewpagerowid($postid);

	// create new row
	$newrow = array();
	$newrow["rowindex"] = "new";
	$newrow["pagerowtemplate"] = $pagerowtemplate;
	$newrow["pagerowid"] = $pagerowid;
	$newrow["pagerowattributes"] = "pagerowtemplate='" . $pagerowtemplate . "' pagerowid='" . $pagerowid . "'";
	$newrow["content"] = nxs_getpagerowtemplatecontent($pagerowtemplate);

	$tailindex = count($poststructure);	// to be inserted AFTER the last item
	
	// insert row into structure
	$updatedpoststructure = nxs_insertarrayindex($poststructure, $newrow, $tailindex);
	
	// persist structure
	$updateresult = nxs_storebinarypoststructure($postid, $updatedpoststructure);
	
	// apply placeholdertemplates for the placeholders just created...
	$placeholderid = nxs_parsepagerow($newrow["content"]);

	$clientpopupsessioncontext = array();
	$clientpopupsessioncontext["postid"] = $postid;
	$clientpopupsessioncontext["placeholderid"] = $placeholderid;
	$clientpopupsessioncontext["contextprocessor"] = "widgets";
	$clientpopupsessioncontext["sheet"] = "home";

	$args = $widgetmetadata;
	$args["clientpopupsessioncontext"] = $clientpopupsessioncontext;
	$args["placeholdertemplate"] = $widgetmetadata["type"];
	
	// for downwards compatibility we replicate the postid and placeholderid to the 'root'
	$args["postid"] = $postid;
	$args["placeholderid"] = $placeholderid;
	
	nxs_initializewidget($args);
	// 
	nxs_widgets_mergeunenrichedmetadata($widgetmetadata["type"], $args);
	
	// update items that are derived (based upon the structure and contents of the page, such as menu's)
	nxs_after_postcontents_updated($postid);
	
	//
	
	return $result;
}