<?php 

if (defined('NXS_FRAMEWORKLOADED'))
{
	echo "NXS_FRAMEWORKLOADED was already loaded?!";
	die();
}
define('NXS_FRAMEWORKLOADED', true);

if (!defined('NXS_FRAMEWORKNAME'))
{
	echo "NXS_FRAMEWORKNAME is not defined";
	return;
}

if (!defined('NXS_FRAMEWORKVERSION'))
{
	echo "NXS_FRAMEWORKVERSION is not defined";
	return;
}

if (!defined('NXS_FRAMEWORKPATH'))
{
	echo "NXS_FRAMEWORKPATH is not defined";
	return;
}

global $wp_version;
if (version_compare($wp_version, '3.3.0') < 0) 
{
	echo "NXS Framework requires at least WP 3.3.0";
	die();
}

// tell WP SUPER CACHE to not cache any page;
// if caching is wanted, user should use the 
// build in caching implementation we use
// would be best to output a warning in that 
// case in the WP backend...
define('DONOTCACHEPAGE', 'true');

//
// FEATURES IMAGES
//
add_theme_support("post-thumbnails");	// if sites use feature images we support them, the size of the thumbnails is set in the 'aftertheme'
add_action('after_setup_theme', 'nxs_addsupportforadditionalimageformats');

// whenever the current blog is switched, we clear the sitemeta
add_action("switch_blog", "nxs_sitemeta_clearcache");

function nxs_showphpwarnings()
{
	// suppresses warnings
	if (isset($_REQUEST["showwarnings"]) && $_REQUEST["showwarnings"] == "true")
	{
		$result = true;
	}
	else if (isset($_REQUEST["clientqueryparameters"]["showwarnings"]) && $_REQUEST["showwarnings"]["showwarnings"] == "true")
	{
		$result = true;
	}
	else
	{
		$result = false;
	}
	
	return $result;
}

function nxs_isdebug()
{
	$result = false;
	
	$remoteaddress = $_SERVER['REMOTE_ADDR'];
	if ($remoteaddress == "92.254.25.182")
	{
		$result = true;
	}	
	else if ($remoteaddress == "10.0.160.89")
	{
		$result = true;
	}
	else if ($remoteaddress == "80.126.37.213")
	{
		$result = true;
	}
	else if ($remoteaddress == "143.177.32.9")
	{
		$result = true;
	}
	
	return $result;
}

// 2013 08 03; fixing unwanted WP3.6 notice errors
// third party plugins and other php code (like sunrise.php) can
// cause warnings that mess up the output of the webmethod
// for example when activating the theme
// to solve this, at this stage we clean the output buffer
// 2014 12 07; in some cases the ob_clean() invoked here
// can cause weird bogus output (diamonds with question marks),
// as-if the encoding is messed up (dproost)
// to avoid this from happening we don't do a ob_clean when
// there's nothing to clean up in the first place
function nxs_saveobclean()
{
	if(ob_get_level() > 0)
	{		
		$current = ob_get_contents();
		if ($current != "")
		{
	  	//echo "its not empty";
	  	ob_clean();
		}
		else
		{
			// leave as-is
		}
	}
	else
	{
		// ignore
	}
}

if (!nxs_showphpwarnings())
{
	error_reporting(E_ERROR | E_PARSE);	
	nxs_saveobclean();
}

function nxs_getcharset()
{
	// charset wordt (verkeerd geinterpreteerd bij Edwin als je NIET bent ingelogd (?)
	// LET de aanroep
	// 
	// doet direct een ECHO
	
	$result = get_bloginfo('charset');	// "UTF-8";
	return $result;
}

//
// define a uniqueid for this request,
// can be used in various places,
// for example its used in the tinymce optiontype
//
define('NXS_UNIQUEIDFORREQUEST', rand());

//
// prerequisites
//
if (!function_exists('curl_init'))
{ 
	echo "This theme requires CURL (CURL is currently not installed!). <a target='_blank' href='http://nexusthemes.com/how-to-activate-curl/'>See instructions how to solve this</a>";
	return;
}

//
// https "fix" for some servers
//
// kudos to https://gist.github.com/webaware/4688802
if (stripos(get_option('siteurl'), 'https://') === 0) 
{
	$_SERVER['HTTPS'] = 'on';
}

//
// CONSTANTS
//

define('NXS_DEFINE_NXSSERVERVALUECACHING', true);	// default to true (improved performance), false means all transients are ignored
define('NXS_DEFINE_NXSALLOWSERVICECOMMUNICATION', false);	// default to true (improved user experience with tips etc), false means we can test failover
define('NXS_DEFINE_NXSDEBUGNEXUSSERVICEFAILURES', false);	// default to false
define('NXS_DEFINE_NXSDEBUGWEBSERVICES', true);	// default to false
define('NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT', true);	// default to true

//
// IMPORTS
// 

require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsfunctions.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/license/license.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/importers/nexusimporter/nexus-importer.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsstyles.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/shortcodes/shortcodes.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/postwizards/wizards.php');

//
// EXTENSIONS
//

require_once(NXS_FRAMEWORKPATH . '/nexuscore/extensions/webmethods/webmethods_extension.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/extensions/widgets/widgets_extension.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/extensions/pagetemplates/pagetemplates_extension.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/extensions/postwizards/postwizards_extension.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/extensions/popup_optiontypes/popup_optiontypes_extension.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/extensions/popup_genericpopups/popup_genericpopups_extension.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/extensions/popup_contextprocessors/popup_contextprocessors_extension.php');
require_once(NXS_FRAMEWORKPATH . '/nexuscore/extensions/commentsproviders/commentsproviders_extension.php');

require_once(NXS_FRAMEWORKPATH . '/nexuscore/webservices/webservices.php'); 

// handle webmethod, is this is a webmethod
// note that if this _is_ a webmethod, the system will stop execution after this method
add_action('init', 'nxs_handlewebmethods', 999999);


// compliance with feeds
nxs_addfeedsupport();

// compliance with popular third party plugins
nxs_addyoastseosupport();
nxs_addwoocommercesupport();

// plugins
nxs_loadplugin_twittertweets();

function nxs_seotab_pluginnotfound()
{
	?>
	<div id="tabs-seo" class="seo-disabled">
		<div class="content nxs-padding10">
			<h2>Search engine optimization</h2>
			<br />
			<br />
			<p>
				<?php nxs_l18n_e("Want to attract more traffic to your website? Click <a target='_blank' href='http://nexusthemes.com/support/increase-visitors/'>here</a>", "nxs_td"); ?><br />
			</p>
		</div>
	</div>
	<?php
}
add_action('nxs_ext_seotab_pluginnotfound', 'nxs_seotab_pluginnotfound');	// default implementation, can be overruled

//
// framework css
//
function nxs_framework_theme_styles()
{ 
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
		// if responsiveness is turned on
		if (isset($sitemeta["responsivedesign"]) && $sitemeta["responsivedesign"] == "true")
		{
			wp_register_style('nxs-framework-style-responsive', 
	    nxs_getframeworkurl() . '/css/framework-responsive.css', 
	    array(), 
	    nxs_getthemeversion(),
	    'all' );
	    
	    wp_enqueue_style('nxs-framework-style-responsive');
		}
	}
	
	wp_enqueue_script( 'jquery-migrate', nxs_getframeworkurl() . '/js/migrate/jquery-migrate.js', array( 'jquery' ), nxs_getthemeversion(), TRUE );
	
  do_action('nxs_action_after_enqueue_baseframeworkstyles');
}
add_action('wp_enqueue_scripts', 'nxs_framework_theme_styles');

function nxs_ensure_sessionstarted()
{
	// init session
  if (!session_id()) 
  {
  	// 20130329 the next line should fix issue identified by Jessica
  	// see http://www.php.net/manual/en/session.configuration.php#ini.session.save-handler
  	// see http://forums.cpanel.net/f5/error-php-fatal-error-session_start-failed-initialize-storage-module-17100-p3.html
  	// if errors shows Fatal error: session_start() [<a href='function.session-start'>function.session-start</a>]: Failed to initialize storage module: files (path: )
  	// this means the 
  	session_start();
  }
}

function nxs_framework_authentication_popup_top()
{
	?>
	<div class="nxs-loginlogowrapper">
		<a target="_blank" title="Premium WordPress themes - Nexus Themes" href='http://www.nexusthemes.com'>
			<div id="logo"></div>
			<div class="nxs-clear"></div>
		</a>
	</div>
	<?php
}
add_action('nxs_authentication_popup_top', 'nxs_framework_authentication_popup_top');

//
// sidebars (could have been any number, but 8 sounds like sufficient ...)
//
if (function_exists('register_sidebar'))
{
	register_sidebars(8, array('name' => 'WordPress Backend Widget Area %d'));
}

//
//
//

function nxs_validatethemedata()
{
	if (!defined('NXS_THEMEPATH'))
	{
		define('NXS_THEMEPATH', get_template_directory());
	}
}

add_action('wp_footer', 'nxs_wp_footer_debug');
function nxs_wp_footer_debug() 
{
	if (nxs_isdebug())
	{
		if ($_REQUEST["nxs"] == "tracelayout")
		{
			$layout = nxs_gettemplateproperties();
			echo nxs_prettyprint_array($layout);
			
			global $nxs_global_current_containerpostid_being_rendered;
			echo "we zijn ook;" . $nxs_global_current_containerpostid_being_rendered;
			echo "we zijn;" . get_the_ID();
			echo "home is;" . nxs_gethomepageid();
			
			if (is_archive())
			{
				echo "its an archive";
				
				$term = get_queried_object();
				$taxonomy = $term->taxonomy;
				if ($taxonomy == "category")
				{
					$termid = $term->term_id;
					echo "termid:<br />";
					var_dump($termid);
					echo "<br />";
					var_dump($term);
					
					
				}
				//
				//echo single_cat_title('Currently browsing '); 
				//$a = get_the_archive();
				//var_dump($a);
			}
			//$categories = get_the_category();
			die();
		}
	}
}

add_action('init', 'nxs_init');
function nxs_init() 
{	
	if (nxs_has_adminpermissions())
  {
  	if (isset($_REQUEST["nxs"]))
  	{
  		if ($_REQUEST["nxs"] == "urlinfo")
  		{
  			echo "siteurl:" . get_site_url() . "<br />";
  			echo "homeurl:" . get_home_url() . "<br />";
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "template")
  		{
				$t = get_template();
				var_dump($t);
				die();
			}
			else if ($_REQUEST["nxs"] == "get_update_themes")
			{
				$x = get_site_transient('update_themes');
				var_dump($x);
				die();
			}
			else if ($_REQUEST["nxs"] == "wp_get_theme")
			{
				$x = wp_get_theme();
				var_dump($x);
				die();
			}
  		else if ($_REQUEST["nxs"] == "HTTP_USER_AGENT")
  		{
  			echo $_SERVER["HTTP_USER_AGENT"];
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "nxs_detect_ie")
  		{
  			if (nxs_detect_ie()) { echo "IE!";} else { echo "no ie"; }
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "FILE")
  		{
  			echo __FILE__;
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "serversoftware") 
  		{
  			echo $_SERVER["SERVER_SOFTWARE"];
  			die();
  		}
	  	else if ($_REQUEST["nxs"] == "phpinfo")
		  {
		  	phpinfo();
		  	die();
		  }
		  else if ($_REQUEST["nxs"] == "wp_upload_dir")
		  {
		  	$x = wp_upload_dir();
		  	var_dump($x);
		  	die();
		  }
		  else if ($_REQUEST["nxs"] == "diskfree")
		  {
		  	echo "disk free space:<br />";
		  	$x = disk_free_space(".");
		  	var_dump($x);
		  	die();
		  }
		  else if ($_REQUEST["nxs"] == "errorlog")
		  {
			  $errorpath = ini_get('error_log');
			  echo "path: $errorpath <br />";
			  $content = file_get_contents($errorpath);
			  $content = str_replace("\r\n", "<br />", $content);			
			  echo $content;
		  	die();
		  }
		  else if ($_REQUEST["nxs"] == "phpversion")
		  {
		  	echo phpversion();
		  	die();
		  }
		  else if ($_REQUEST["nxs"] == "wpversion")
		  {
		  	global $wp_version;
		  	echo $wp_version;
		  	die();
		  }
		  else if ($_REQUEST["nxs"] == "testcurl")
		  {
		  	$url = $_REQUEST["url"];
		  	echo "[";
				$output = url_get_contents($url);
				echo $output;
				echo "]";
				
				die();
		  }
		  else if ($_REQUEST["nxs"] == "testmagicquotes")
		  {
		  	$r = get_magic_quotes_gpc() === true;
		  	echo "magic quotes?;";
		  	var_dump($r);
				
				die();
		  }
		  else if ($_REQUEST["nxs"] == "testping")
		  {
		  	$url = $_REQUEST["url"];
				$port = 80; 
				$waitTimeoutInSeconds = 1; 
				if($fp = fsockopen($url,$port,$errCode,$errStr,$waitTimeoutInSeconds)){   
					echo "nice";
				   // It worked 
				} else {
				   // It didn't work 
				   echo "not so nice";
				} 
				fclose($fp);
				die();
		  }
		  else if ($_REQUEST["nxs"] == "testdns")
		  {
		  	$url = $_REQUEST["url"];
		  	$result = dns_get_record($url);
				print_r($result);
				die();
		  }
		  else if ($_REQUEST["nxs"] == "testlocale")
		  {
		  	$env = localeconv();
		  	var_dump($env);
		  	die();
		  }
		  else if ($_REQUEST["nxs"] == "activesitesettings")
		  {
		  	echo "postid:<br />";
		  	$postids = nxs_get_postidsaccordingtoglobalid("activesitesettings");
				var_dump($postids);
				echo "<br /><br />keyvalues:<br />";
				$sitemeta = nxs_getsitemeta_internal(false);
				var_dump($sitemeta);
		  	die();
		  }
		 	else if ($_REQUEST["nxs"] == "locale")
			{
				$locale = apply_filters('theme_locale', get_locale(), $domain);
				echo "Current locale is set to:" . $locale;
				die();
			}
			else if ($_REQUEST["nxs"] == "mb_detect_order")
			{
				echo "mb_detect_order:<br />";
				$r = mb_detect_order();
				var_dump($r);
				die();
			}
			else if ($_REQUEST["nxs"] == "isssl")
			{
				if (is_ssl()) 
				{
					echo "this request is served through ssl";
				}
				else
				{
					echo "regular http";
				}
				die();
			}
			else if ($_REQUEST["nxs"] == "dumppost")
			{
				$postid = $_REQUEST["postid"];
				echo "dumppost $postid<br />";
				$exists = nxs_postexistsbyid($postid);
				if ($exists) { echo "post exist<br />"; } else { echo "post does not exist<br />"; }
				$needleglobalid = nxs_get_globalid($postid, false);
				echo "globalid: $needleglobalid<br />";
				echo "post_meta_all: $needleglobalid<br />";
				$origpost_meta_all = nxs_get_post_meta_all($postid);
				foreach ($origpost_meta_all as $key => $val)
				{
					echo "meta key: $key<br />";
					echo "meta val: <br />";
					echo "<pre>";
					var_dump($val);
					echo "</pre>";
					echo "<br />";
					echo "<br />";
					echo "<hr />";
				}
				
				die();
			}
			else if ($_REQUEST["nxs"] == "dumpsitemeta")
			{
				$sitemeta = nxs_getsitemeta();
				foreach ($sitemeta as $key => $val)
				{
					echo "meta key: $key<br />";
					echo "meta val: <br />";
					echo "<pre>";
					var_dump($val);
					echo "</pre>";
					echo "<br />";
					echo "<br />";
					echo "<hr />";
				}
				
				die();
			}
		}
	}
	
	if (nxs_iswploginpage())
	{
		// always access
	}
	else
	{
		if (nxs_has_adminpermissions())
		{
			// ok
		}
		else
		{
			// verify anonymous access allowed
			$anonymousaccess = nxs_site_get_anonymousaccess();
			if (isset($anonymousaccess) && $anonymousaccess == "block")
			{
				$url = wp_login_url();
				$url = nxs_addqueryparametertourl_v2($url, "nxsaccess", "blocked", true, true);
				?>
				<script type='text/javascript'>
					window.location.href="<?php echo $url; ?>";
				</script>
				<?php
				wp_redirect($url, 301);
				exit;
			}
		}
	}
}

//
// reconstructs the wp_query object (the 'main loop') for webrequests,
// as it was when the page was rendered that triggered the webrequest,
// see #2389724 (search in the framework for this id to find relevant places)
//
function nxs_action_webmethod_init_recontructmainwploop()
{
	// 
	$clientpopupsessioncontext = $_REQUEST["clientpopupsessioncontext"];
	
	$urlencodedjsonencodedquery_vars = $clientpopupsessioncontext["urlencodedjsonencodedquery_vars"];
	if (isset($clientpopupsessioncontext))
	{
		$urlencodedjsonencodedquery_vars = $clientpopupsessioncontext["urlencodedjsonencodedquery_vars"];
		if (isset($urlencodedjsonencodedquery_vars))
		{
			// decode 2x
			$jsonencodedquery_vars = urldecode($urlencodedjsonencodedquery_vars);
			$jsonencodedquery_vars = urldecode($jsonencodedquery_vars);
			
			$nxsqueryvars = json_decode($jsonencodedquery_vars, true);
			
			// enrich the query vars
			if ($nxsqueryvars["page_id"] != "")
			{
				// fix; if the page_id is filled, we need to explicitly set the 
				// post_type to page, otherwise 0 posts will be returned
				$nxsqueryvars["post_type"] = "page";
			}
			
			// enrich the query vars
			if ($nxsqueryvars["post_type"] == "")
			{
				// fix; if the posttype is not filled, we need to explicitly set the 
				// post_type to both post and page, otherwise 0 posts will be returned if
				// a page is queried
				$nxsqueryvars["post_type"] = array("page", "post");
			}
			
			// alter the main query that WordPress uses to display posts. 
			// It does this by putting the main query to one side, and replacing it with a new query.
			// Conditional tags that are called after you call query_posts() will also be altered.
			// see http://codex.wordpress.org/Function_Reference/query_posts
			query_posts($nxsqueryvars);

			// set the_post for the queried object, otherwise $post would always refer to the post of the homepage,
			// as these webservice requests piggy back on the home url's
			global $wp_query;
			if ($wp_query->have_posts())
			{
				$wp_query->the_post();
			}
			
			// note we wont call wp_reset_query(), because there is no need for it
		}
		else
		{
			// n/a
		}
	}
	else
	{
		// n/a
	}
}
add_action("nxs_action_webmethod_init", "nxs_action_webmethod_init_recontructmainwploop");

add_action('add_meta_boxes', 'nxs_add_metaboxes');
function nxs_add_metaboxes()
{
	global $post;
	if ($post->post_status == "auto-draft")
	{
		// Nexus content editing meta box is not available if the post is 
		// not yet created
		return;
	}
	
  add_meta_box('nexus_meta', nxs_l18n__("Content[nxs:metaboxheading]", "nxs_td"), 'nxs_backend_meta_box', 'post', 'normal', 'default');
  add_meta_box('nexus_meta', nxs_l18n__("Content[nxs:metaboxheading]", "nxs_td"), 'nxs_backend_meta_box', 'page', 'normal', 'default');
}

function nxs_backend_meta_box()
{
	?>
	<div>
		<p><?php nxs_l18n_e("Edit content explanation[nxs:button]", "nxs_td"); ?></p>
		<?php
		$postid = $_REQUEST["post"];
		if (isset($postid))
		{
			$url = nxs_geturl_for_postid($postid);
			?>
			<a href='<?php echo $url; ?>' style='font-weight: bolder;' class='button-primary'><?php nxs_l18n_e("Edit content[nxs:button]", "nxs_td"); ?></a>
			<?php
		}
		?>
	</div>
	
	<script type='text/javascript'>
		
		function nxs_js_movetotop()
		{
			jQuery('#nexus_meta').insertBefore('#normal-sortables');
		}
		
		jQuery(document).ready(function() 
		{
			// move Nexus content editing item up the DOM
			
			nxs_js_movetotop();
			
		});

	</script>
	<?php
}

add_action('nxs_action_postfooterlink', 'nxs_render_postfooterlink');
function nxs_render_postfooterlink()
{
	$url = nxs_geturlcurrentpage();
	$homeurl = nxs_geturl_home();
	
	$sitemeta = nxs_getsitemeta();
	$catitem_themeid = $sitemeta["catitem_themeid"];
	$footerhtmltemplate = $sitemeta["footerhtmltemplate"];
	
	if ($footerhtmltemplate == "")
	{
		// default
		$footerhtmltemplate = "{{{themelink}}} | {{{authenticatelink}}}";	
	}
	
	$lookup = array
	(
		"fitnessclub;" => array
		(
			"href"=>"/wordpress-themes/fitness-wordpress-themes/fitness-club-wordpress-theme/",
			"title"=>"Fitness Club WordPress theme",
		),	
		"chiropractor;" => array
		(
			"href"=>"/wordpress-themes/medical-wordpress-themes/chiropractor-wordpress-theme/",
			"title"=>"Chiropractor WordPress theme",
		),	
		"optometrist;" => array
		(
			"href"=>"/wordpress-themes/medical-wordpress-themes/optometrist-wordpress-theme/",
			"title"=>"Optometrist WordPress theme",
		),	
		"photographystudio;" => array
		(
			"href"=>"/wordpress-themes/photographer/photography-studio-wordpress-theme/",
			"title"=>"Photography studio WordPress theme",
		),	
		"webdesignagency;" => array
		(
			"href"=>"/wordpress-themes/business/web-design-agency-wordpress-theme/",
			"title"=>"Web design agency WordPress theme",
		),	
		"danceschool;" => array
		(
			"href"=>"/wordpress-themes/dancing-wordpress-themes/dance-school-wordpress-theme/",
			"title"=>"Dance school WordPress theme",
		),	
		"dancestudio;" => array
		(
			"href"=>"/wordpress-themes/dancing-wordpress-themes/dance-studio-wordpress-theme/",
			"title"=>"Dance studio WordPress theme",
		),	
		"partyplanner;" => array
		(
			"href"=>"/wordpress-themes/entertainment-wordpress-themes/party-planner-wordpress-theme/",
			"title"=>"Party planner WordPress theme",
		),
		"daycare;" => array
		(
			"href"=>"/wordpress-themes/education/daycare-wordpress-theme/",
			"title"=>"Day care WordPress theme",
		),
		"musicstudio;" => array
		(
			"href"=>"/wordpress-themes/music/music-studio-wordpress-theme/",
			"title"=>"Music Studio WordPress theme",
		),
		"marketingagency;" => array
		(
			"href"=>"/wordpress-themes/business/marketing-agency-wordpress-theme/",
			"title"=>"Marketing agency WordPress theme",
		),
		"defenseattorney;" => array
		(
			"href"=>"/wordpress-themes/legal-wordpress-themes/defense-attorney-wordpress-theme/",
			"title"=>"Beauty salon WordPress theme",
		),
		"beautysalon;" => array
		(
			"href"=>"/wordpress-themes/beauty/beauty-salon-wordpress-theme/",
			"title"=>"Beauty salon WordPress theme",
		),
		"homeimprovement;" => array
		(
			"href"=>"/wordpress-themes/construction/home-improvement-wordpress-theme/",
			"title"=>"Home improvement WordPress theme",
		),		
		"securitycompany;" => array
		(
			"href"=>"/wordpress-themes/business/security-company-wordpress-theme/",
			"title"=>"Security company WordPress theme",
		),		
		"homebuilder;" => array
		(
			"href"=>"/wordpress-themes/construction/home-builder-wordpress-theme/",
			"title"=>"Home builder WordPress theme",
		),	
		"beautyspa;" => array
		(
			"href"=>"/wordpress-themes/beauty/beauty-spa-wordpress-theme/",
			"title"=>"Beauty spa WordPress theme",
		),	
		"hairstylist;" => array
		(
			"href"=>"/wordpress-themes/beauty/hair-stylist-wordpress-theme/",
			"title"=>"Hair stylist WordPress theme",
		),	
		"photographyportfolio;" => array
		(
			"href"=>"/wordpress-themes/photographer/photography-portfolio-wordpress-theme/",
			"title"=>"Photography portfolio WordPress theme",
		),	
		"pcepair;" => array
		(
			"href"=>"/wordpress-themes/computer-repair/pc-repair-wordpress-theme/",
			"title"=>"PC repair WordPress theme",
		),
		"spasalon;" => array
		(
			"href"=>"/wordpress-themes/beauty/spa-salon-wordpress-theme/",
			"title"=>"Spa salon WordPress theme",
		),
		"barbershop;" => array
		(
			"href"=>"/wordpress-themes/beauty/barber-shop-wordpress-theme/",
			"title"=>"Barber shop WordPress theme",
		),
		"mortgagebroker;" => array
		(
			"href"=>"/wordpress-themes/business/mortgage-broker-wordpress-theme/",
			"title"=>"Mortgage broker WordPress theme",
		),
		"hairsalon;" => array
		(
			"href"=>"/wordpress-themes/beauty/hair-salon-wordpress-theme/",
			"title"=>"Hair salon WordPress theme",
		),
		"drivingschool;" => array
		(
			"href"=>"/wordpress-themes/automotive/driving-school-wordpress-theme/",
			"title"=>"Driving school WordPress theme",
		),
		"logistics;" => array
		(
			"href"=>"/wordpress-themes/automotive/logistics-wordpress-theme/",
			"title"=>"Logistics WordPress theme",
		),
		"limousine;" => array
		(
			"href"=>"/wordpress-themes/automotive/limousine-wordpress-theme/",
			"title"=>"Limousine WordPress theme",
		),
		"financialadvisor;" => array
		(
			"href"=>"/wordpress-themes/business/financial-advisor-wordpress-theme/",
			"title"=>"Financial advisor WordPress theme",
		),
		"interiordesign;" => array
		(
			"href"=>"/wordpress-themes/interior-furniture/interior-design-wordpress-theme/",
			"title"=>"Interior design WordPress theme",
		),
		"carwash;" => array
		(
			"href"=>"/wordpress-themes/automotive/car-wash-wordpress-theme/",
			"title"=>"Car wash WordPress theme",
		),
		"paintball;" => array
		(
			"href"=>"/wordpress-themes/sports/paintball-wordpress-theme/",
			"title"=>"Paintball WordPress theme",
		),
		"sushi;" => array
		(
			"href"=>"/wordpress-themes/restaurant/sushi-wordpress-theme/",
			"title"=>"Sushi WordPress theme",
		),
		"golfclub;" => array
		(
			"href"=>"/wordpress-themes/sports/golf-club-wordpress-theme/",
			"title"=>"Golf club WordPress theme",
		),
		"pettingzoo;" => array
		(
			"href"=>"/wordpress-themes/agriculture/petting-zoo-wordpress-theme/",
			"title"=>"Petting zoo WordPress theme",
		),
		"funeralhome;" => array
		(		
			"href"=>"/wordpress-themes/death-care/funeral-home-wordpress-theme/",
			"title"=>"Funeral home WordPress theme",
		),
		"swimmingpooldealer;" => array
		(		
			"href"=>"/wordpress-themes/maintenance-services/swimming-pool-dealer-wordpress-theme/",
			"title"=>"Swimming pool dealer WordPress theme",
		),
		"nailsalon;" => array
		(		
			"href"=>"/wordpress-themes/beauty/nail-salon-wordpress-theme/",
			"title"=>"Nail salon WordPress theme",
		),
		"seocompany;" => array
		(		
			"href"=>"/wordpress-themes/business/seo-company-wordpress-theme/",
			"title"=>"SEO Company WordPress theme",
		),
		"pizzeria;" => array
		(		
			"href"=>"/wordpress-themes/restaurant/pizzeria-wordpress-theme/",
			"title"=>"Pizzeria WordPress theme",
		),
		"cargarage;" => array
		(		
			"href"=>"/wordpress-themes/automotive/car-garage-wordpress-theme/",
			"title"=>"Car Garage WordPress theme",
		),
		"singer;" => array
		(
			"href"=>"/wordpress-themes/music/singer-wordpress-theme/",
			"title"=>"Singer WordPress theme",
		),
		"highschool;" => array
		(
			"href"=>"/wordpress-themes/education/high-school-wordpress-theme/",
			"title"=>"High school WordPress theme",
		),
		"veterinary;" => array
		(
			"href"=>"/wordpress-themes/animals-pets/veterinary-wordpress-theme/",
			"title"=>"Veterinary WordPress theme",
		),
		"propertymanagement;" => array
		(
			"href"=>"/wordpress-themes/real-estate/property-management-wordpress-theme/",
			"title"=>"Property management WordPress theme",
		),
		"brewery;" => array
		(
			"href"=>"/wordpress-themes/restaurant/brewery-wordpress-theme/",
			"title"=>"Brewery WordPress theme",
		),
		"photoartist;" => array
		(
			"href"=>"/wordpress-themes/photographer/photoartist-wordpress-theme/",
			"title"=>"Photo Artist WordPress theme",
		),
		"recordingstudio;" => array
		(
			"href"=>"/wordpress-themes/music/recording-studio-wordpress-theme/",
			"title"=>"Recording studio WordPress theme",
		),
		"summercamp;" => array
		(
			"href"=>"/wordpress-themes/resort/summer-camp-wordpress-theme/",
			"title"=>"Summer camp WordPress theme",
		),
		"lawncare;" => array
		(
			"href"=>"/wordpress-themes/landscaping/lawn-care-wordpress-theme/",
			"title"=>"Lawn care WordPress theme",
		),
		"cleaningservices;" => array
		(
			"href"=>"/wordpress-themes/maintenance-services/cleaning-services-wordpress-theme/",
			"title"=>"Cleaning services WordPress theme",
		),
		"autorepair;" => array
		(
			"href"=>"/wordpress-themes/automotive/auto-repair-wordpress-theme/",
			"title"=>"Auto repair WordPress theme",
		),
		"martialarts;" => array
		(
			"href"=>"/wordpress-themes/sports/martial-arts-wordpress-theme/",
			"title"=>"Martial Arts WordPress theme",
		),
		"landscaping;" => array
		(
			"href"=>"/wordpress-themes/landscaping/landscaping-wordpress-theme/",
			"title"=>"Landscaping WordPress theme",
		),
		"massage;" => array
		(
			"href"=>"/wordpress-themes/therapy/massage-wordpress-theme/",
			"title"=>"Massage WordPress theme",
		),
		"psychologist;" => array
		(
			"href"=>"/wordpress-themes/therapy/psychologist-wordpress-theme/",
			"title"=>"Psychologist WordPress theme",
		),
		"trucking;" => array
		(
			"href"=>"/wordpress-themes/automotive/trucking-wordpress-theme/",
			"title"=>"Trucking WordPress theme",
		),	
		"handyman;" => array
		(
			"href"=>"/wordpress-themes/maintenance-services/handyman-wordpress-theme/",
			"title"=>"Handyman WordPress theme",
		),
		"astrology;" => array
		(
			"href"=>"/wordpress-themes/astrology/astrology-wordpress-theme/",
			"title"=>"Astrology WordPress theme",
		),
		"computerrepair;" => array
		(
			"href"=>"/wordpress-themes/computer-repair/computer-repair-wordpress-theme/",
			"title"=>"Computer repair WordPress theme",
		),
		"carrepair;" => array
		(
			"href"=>"/wordpress-themes/automotive/car-repair-wordpress-theme/",
			"title"=>"Car Repair WordPress theme",
		),
		"accounting;" => array
		(
			"href"=>"/wordpress-themes/accounting/accounting-wordpress-theme/",
			"title"=>"Accounting WordPress theme",
		),
		"preschool;" => array
		(
			"href"=>"/wordpress-themes/education/preschool-wordpress-theme/",
			"title"=>"Preschool WordPress theme",
		),
		"pestcontrol;" => array
		(
			"href"=>"/wordpress-themes/pest-control/pest-control-wordpress-theme/",
			"title"=>"Pest control WordPress theme",
		),
		"hvac;" => array
		(
			"href"=>"/wordpress-themes/hvac/hvac-wordpress-theme/",
			"title"=>"HVAC WordPress theme",
		),
		"actor;" => array
		(
			"href"=>"/wordpress-themes/actor/actor-wordpress-theme/",
			"title"=>"Actor WordPress theme",
		),
		"beautician;" => array
		(
			"href"=>"/wordpress-themes/beautician/beautician-wordpress-theme/",
			"title"=>"Beautician WordPress theme",
		),
		"makeupartist;" => array
		(
			"href"=>"/wordpress-themes/makeup-artist/makeup-artist-wordpress-theme/",
			"title"=>"Makeup artist WordPress theme",
		),
		"contractor;" => array
		(
			"href"=>"/wordpress-themes/construction/contractor-wordpress-theme/",
			"title"=>"Contractor WordPress theme",
		),
		"constructioncompany;" => array
		(
			"href"=>"/wordpress-themes/construction/construction-company-wordpress-theme/",
			"title"=>"Construction company WordPress theme",
		),
		"weddingplanner;" => array
		(
			"href"=>"/wordpress-themes/wedding/wedding-planner-wordpress-theme/",
			"title"=>"Wedding planner WordPress theme",
		),
		"physicaltherapy;" => array
		(
			"href"=>"/wordpress-themes/therapy/physical-therapy-wordpress-theme/",
			"title"=>"Physical therapy WordPress theme",
		),
		"movingcompany;" => array
		(
			"href"=>"/wordpress-themes/moving-company/moving-company-wordpress-theme/",
			"title"=>"Moving company WordPress theme",
		),
		"gardener;" => array
		(
			"href"=>"/wordpress-themes/gardener/gardener-wordpress-theme/",
			"title"=>"Gardener WordPress theme",
		),
		"personaltrainer;" => array
		(
			"href"=>"/wordpress-themes/trainer/personal-trainer-wordpress-theme/",
			"title"=>"Personal trainer WordPress theme",
		),
		"taxi;" => array
		(
			"href"=>"/wordpress-themes/taxi/taxi-wordpress-theme/",
			"title"=>"Taxi WordPress theme",
		),
		"drivinginstructor;" => array
		(
			"href"=>"/wordpress-themes/driving-instructor/driving-instructor-wordpress-theme/",
			"title"=>"Driving instructor WordPress theme",
		),
		"locksmith;" => array
		(
			"href"=>"/wordpress-themes/locksmith/locksmith-wordpress-theme/",
			"title"=>"Locksmith WordPress theme",
		),
		"carpenter;" => array
		(
			"href"=>"/wordpress-themes/carpenter/carpenter-wordpress-theme/",
			"title"=>"Carpenter WordPress theme",
		),
		"carpetcleaning;" => array
		(
			"href"=>"/wordpress-themes/carpet-cleaning/carpet-cleaning-wordpress-theme/",
			"title"=>"Carpet cleaning WordPress theme",
		),
		"yogainstructor;" => array
		(
			"href"=>"/wordpress-themes/yoga/yoga-instructor-wordpress-theme/",
			"title"=>"Yoga instructor WordPress theme",
		),
		"yogateacher;" => array
		(
			"href"=>"/wordpress-themes/yoga/yoga-teacher-wordpress-theme/",
			"title"=>"Yoga teacher WordPress theme",
		),
		"yoga;" => array
		(
			"href"=>"/wordpress-themes/yoga/yoga-wordpress-theme/",
			"title"=>"Yoga WordPress theme",
		),
		"yogastudio;" => array
		(
			"href"=>"/wordpress-themes/yoga/yoga-studio-wordpress-theme/",
			"title"=>"Yoga studio WordPress theme",
		),
		"webdesigner;" => array
		(
			"href"=>"/wordpress-themes/webdesigner/webdesigner-wordpress-theme/",
			"title"=>"Webdesigner WordPress theme",
		),
		"painter;" => array
		(
			"href"=>"/wordpress-themes/painter/painter-wordpress-theme/",
			"title"=>"Painter WordPress theme",
		),
		"lawyer;" => array
		(
			"href"=>"/wordpress-themes/lawyer/lawyer-wordpress-theme/",
			"title"=>"Laywer WordPress theme",
		),
		"lifecoach;" => array
		(
			"href"=>"/wordpress-themes/coach/lifecoach-wordpress-theme/",
			"title"=>"Lifecoach WordPress theme",
		),
		"consultant;" => array
		(
			"href"=>"/wordpress-themes/consultant/consultant-wordpress-theme/",
			"title"=>"Consultant WordPress theme",
		),
		"physiotherapist;" => array
		(
			"href"=>"/wordpress-themes/physiotherapist/physiotherapist-wordpress-theme/",
			"title"=>"Physiotherapist WordPress theme",
		),
		"hairdresser;" => array
		(
			"href"=>"/wordpress-themes/hairdresser/hairdresser-wordpress-theme/",
			"title"=>"Hairdresser WordPress theme",
		),
		"nutritionist;" => array
		(
			"href"=>"/wordpress-themes/nutritionist/nutritionist-wordpress-theme/",
			"title"=>"Nutritionist WordPress theme",
		),
		"dentist;" => array
		(
			"href"=>"/wordpress-themes/dentist/dentist-wordpress-theme/",
			"title"=>"Dentist WordPress theme",
		),
		"dental;" => array
		(
			"href"=>"/wordpress-themes/dentist/dental-wordpress-theme/",
			"title"=>"Dental WordPress theme",
		),
		"dentalclinic;" => array
		(
			"href"=>"/wordpress-themes/dentist/dental-clinic-wordpress-theme/",
			"title"=>"Dental clinic WordPress theme",
		),
		"dentalhygienist;" => array
		(
			"href"=>"/wordpress-themes/dentist/dental-hygienist-wordpress-theme/",
			"title"=>"Dental hygienist WordPress theme",
		),
		"restaurant;" => array
		(
			"href"=>"/wordpress-themes/restaurant/restaurant-wordpress-theme/",
			"title"=>"Restaurant WordPress theme",
		),
		"restaurantspanish;" => array
		(
			"href"=>"/wordpress-themes/restaurant/spanish-restaurant-wordpress-theme/",
			"title"=>"Spanish restaurant WordPress theme",
		),
		"restaurantfrench;" => array
		(
			"href"=>"/wordpress-themes/restaurant/french-restaurant-wordpress-theme/",
			"title"=>"French restaurant WordPress theme",
		),
		"restaurantitalian;" => array
		(
			"href"=>"/wordpress-themes/restaurant/italian-restaurant-wordpress-theme/",
			"title"=>"Italian restaurant WordPress theme",
		),
		"restaurantgreek;" => array
		(
			"href"=>"/wordpress-themes/restaurant/greek-restaurant-wordpress-theme/",
			"title"=>"Greek restaurant WordPress theme",
		),
		"restaurantchinese;" => array
		(
			"href"=>"/wordpress-themes/restaurant/chinese-restaurant-wordpress-theme/",
			"title"=>"Chinese restaurant WordPress theme",
		),
		"pizza;" => array
		(
			"href"=>"/wordpress-themes/restaurant/pizza-wordpress-theme/",
			"title"=>"Pizza WordPress theme",
		),
		"electrician;" => array
		(
			"href"=>"/wordpress-themes/electrician/electrician-wordpress-theme/",
			"title"=>"Electrician WordPress theme",
		),
		"kindergarten;" => array
		(
			"href"=>"/wordpress-themes/kindergarten/kindergarten-wordpress-theme/",
			"title"=>"Kindergarten WordPress theme",
		),
		"resort;" => array
		(
			"href"=>"/wordpress-themes/resort/resort-wordpress-theme/",
			"title"=>"Resort WordPress theme",
		),
		"plumber;" => array
		(
			"href"=>"/wordpress-themes/plumber/plumber-wordpress-theme/",
			"title"=>"Plumber WordPress theme",
		),
		"plumbing;" => array
		(
			"href"=>"/wordpress-themes/plumber/plumbing-wordpress-theme/",
			"title"=>"Plumbing WordPress theme",
		),
		"naturephotographer;" => array
		(
			"href"=>"/wordpress-themes/photographer/nature-photographer-wordpress-theme/",
			"title"=>"Nature photographer WordPress theme",
		),
		"weddingphotographer;" => array
		(
			"href"=>"/wordpress-themes/photographer/wedding-photographer-wordpress-theme/",
			"title"=>"Wedding photographer WordPress theme",
		),
		"magazineowner;" => array
		(
			"href"=>"/wordpress-themes/blogger/magazine-owner-wordpress-theme/",
			"title"=>"Magazine owner WordPress theme",
		),
		"equestrian;" => array
		(
			"href"=>"/wordpress-themes/equestrian/equestrian-wordpress-theme/",
			"title"=>"Equestrian WordPress theme",
		),
		"horseridinginstructor;" => array
		(
			"href"=>"/wordpress-themes/equestrian/horse-riding-instructor-theme/",
			"title"=>"Horse riding instructor WordPress theme",
		),
		"horseranch;" => array
		(
			"href"=>"/wordpress-themes/equestrian/horse-ranch-wordpress-theme/",
			"title"=>"Horse ranch WordPress theme",
		),
		"horseriding;" => array
		(
			"href"=>"/wordpress-themes/equestrian/horse-riding-wordpress-theme/",
			"title"=>"Horse riding WordPress theme",
		),
	);
	
	$href = "/wordpress-themes/";
	$title = "Premium Business WordPress themes";
	foreach ($lookup as $keylist => $lookupval)
	{
		foreach (explode(";", $keylist) as $key)
		{
			if ($key == $catitem_themeid && $key != "")
			{
				$href = $lookupval["href"];
				$title = $lookupval["title"];
				break;
			}
		}
	}
	$baseurl = "";
	$baseurl .= "http://";
	$baseurl .= "nexusthemes";
	$baseurl .= ".";
	$baseurl .= "com";
	
	$nexuslink = "<a target='_blank' href='" . $baseurl . "' title='WordPress themes'>WordPress themes</a>";

	if (!function_exists('nxs_theme_getmeta'))
	{
		// downwards compatibility		
		$themeurl = $baseurl . $href;
		$themetitle = $title;
	}
	else
	{
		$meta = nxs_theme_getmeta();
		$themeurl = $meta["url"];
		$themetitle = $meta["title"];
	}
	
	?>
  <p id="nxs-copyright" class="nxs-clear padding nxs-applylinkvarcolor">
	  <?php
		$themelink = "<a target='_blank' href='" . $themeurl . "' title='" . $themetitle . "'>" . $themetitle . "</a>";
		//echo $themelink;
		
		if (is_user_logged_in())
		{
			$authenticatelink = "<a href=\"#\" onclick=\"nxs_js_popup_site_neweditsession('logouthome'); return false;\">Logout</a>";
		}
		else
		{
			$authenticatelink = "<a href=\"#\" onclick=\"nxs_js_popup_site_neweditsession('loginhome'); return false;\">Login</a>";
		}
		
		$footerhtmltemplate = str_replace("{{{authenticatelink}}}", $authenticatelink, $footerhtmltemplate);
		$footerhtmltemplate = str_replace("{{{themelink}}}", $themelink, $footerhtmltemplate);
		$footerhtmltemplate = str_replace("{{{nexuslink}}}", $nexuslink, $footerhtmltemplate);
		
		echo $footerhtmltemplate;
		?>
	</p>
	<?php
}

add_action('init', 'nxs_performdataconsistencycheck');
add_action('init', 'nxs_register_menus');
add_action('init', 'nxs_create_post_types_and_taxonomies');
add_action('init', 'nxs_clearunwantedscripts');

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
  }
}

function nxs_addsupportforadditionalimageformats()
{
	add_image_size('nxs_cropped_200x200', 200, 200, TRUE );
	add_image_size('nxs_cropped_320x200', 320, 200, TRUE );	// used by the gallerybox
	add_image_size('nxs_cropped_320x512', 320, 512, TRUE );	// used by the gallerybox
}

add_filter('image_size_names_choose', 'nxs_custom_sizes');

function nxs_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'nxs_cropped_200x200' => "GJ 200200",
        'nxs_cropped_320x200' => "GJ 320200",
        'nxs_cropped_320x512' => "GJ 320512",
    ) );
}

// 
function nxs_pre_get_posts_categorypageextension($query)
{
  if( $query->is_category )
  {
  	$currentposttypes = $query->get('post_type');
  	if ($currentposttypes == "page")
  	{
  	}
  	else if (!isset($currentposttypes) || $currentposttypes == "")
  	{
  		$currentposttypes = array("post");
  		$currentposttypes[] = "page";
  	}
  	  	
  	$query->set("post_type", $currentposttypes);
  }
  return $query;
}

// custom post types cpt
function nxs_create_post_types_and_taxonomies() 
{
	// allow categories to be linked to pages too
  register_taxonomy_for_object_type('category', 'page');
  // we also need to extend the query when a category page is requested,
  // as register_taxonomy_for_object_type unfortunately doesn't handle this itself (weird!?!)
  add_action('pre_get_posts', 'nxs_pre_get_posts_categorypageextension');  
  
	nxs_registernexustype("header", true);				// holds content that is positioned at the top of the screen
	nxs_registernexustype("sidebar", true);				// holds content that is positioned at the side of the screen
	nxs_registernexustype("footer", true);				// holds content that is positioned at the bottom of the screen

	nxs_registernexustype("subheader", true);			// holds content that is positioned below header, left of sidebar and above main content of the screen
	nxs_registernexustype("subfooter", true); 		// holds content that is positioned: above footer, left of sidebar and below main content of the screen
	
	nxs_registernexustype("menu", false);					// since the WP menu's are not easily im/exportable, we use our own
	
	nxs_registernexustype("admin", false);
	nxs_registernexustype("settings", false);			// used to store various site-wide-settings such as colours, homepageid's, etc.
	
	nxs_registernexustype("systemlog", false);		// used by data consistency reports
	
	// posts and pages
	// taxonomie: both posts and pages have a subtype (for example "webpage", "searchpage", "blogentry", "...", etc.)
	register_taxonomy
	(
		'nxs_tax_subposttype',
		array('post','page'),
		array(
			'hierarchical' => false,
			'label' => 'Sub type',
			'query_var' => true,
			'show_ui' => true,	// hide from ui
			'rewrite' => true
		)
	);
	
	$ispublic = false;
	nxs_registernexustype_withtaxonomies("genericlist", array("nxs_tax_subposttype"), $ispublic);
	
	$ispublic = true;
	nxs_registernexustype_withtaxonomies("templatepart", array("nxs_tax_subposttype"), $ispublic);	// holds content that is positioned in between subheader and subfooter
	nxs_registernexustype_withtaxonomies("busrulesset", array("nxs_tax_subposttype"), $ispublic);		// holds a set of business rules
}


//
//
//

if (isset($_REQUEST["reinitializetheme"]) && $_REQUEST["reinitializetheme"] == "true")
{
	if (nxs_has_adminpermissions())
	{
		nxs_reinitializetheme();
		wp_redirect(site_url());
	}
	else
	{
		echo "Sorry, no access";
		die();	
	}
}

// wordt aangeroepen nadat de theme in het geheugen is geladen (bij ieder request dus)
// laad l18n in
add_action('after_setup_theme', 'nxs_after_theme_setup');
add_action('init', 'nxs_init_themeboot');

//
// --------------- admin pages
// kudos to http://wp.tutsplus.com/tutorials/theme-development/create-a-settings-page-for-your-wordpress-theme/
// 

add_action("admin_menu", "nxs_admin_menu");  
function nxs_admin_menu() 
{
	add_menu_page('Nexus Theme', 'Nexus Theme', 'manage_options', 'nxs_backend_overview', 'nxs_lazyactivate_backend_overview', '', nxs_getframeworkurl() . "/nexuscore/widgets/quote/img/quote_icon.png", '55.5');
	add_submenu_page("nxs_backend_overview", 'Overview', 'Overview', 'manage_options', 'nxs_backend_overview', 'nxs_lazyactivate_backend_overview', '', nxs_getframeworkurl() . "/nexuscore/widgets/quote/img/quote_icon.png", '55.5');
}  

function nxs_lazyactivate_backend_overview() 
{
	$nxs_do_postthemeactivation = get_option('nxs_do_postthemeactivation');
	if ($nxs_do_postthemeactivation == 'true')
	{
		require_once(NXS_FRAMEWORKPATH . '/nexuscore/backend/activated.php');
	}
	else
	{
		require_once(NXS_FRAMEWORKPATH . '/nexuscore/backend/overview.php');
	}
}

//
// -------- DATA CONSISTENCY / INTEGRITY / GLOBALIDS
//

//
// triggert de behoefte om de data na een import, theme switch of na expliciete trigger van de gebruiker te valideren
//

// na een import van data is een globalidsvalidation verplicht
add_action('import_begin', 'nxs_after_data_import');
add_action('import_end', 'nxs_after_data_import');
function nxs_after_data_import()
{
	// after an import it would be wise to require the globalids to be validated
	nxs_set_dataconsistencyvalidationrequired();
}

function nxs_cap_hasdesigncapabilities()
{
	return current_user_can(nxs_cap_getdesigncapability());
}

function nxs_setuprolesandcapabilities()
{
	// the capability to design a site; determines whether ...
	// * rows can be added or removed from site-wide-elements (containers),
	// * widgets can be moved and removed from site-wide-elements (containers),
	// * design-specific widgets can be placed on undefined widgets,
	// * various menu items are enabled
	$role = get_role('administrator');
	if ($role != null)
	{
		$res = $role->add_cap(nxs_cap_getdesigncapability());
	}
}

//
// Show Options Panel after activation
//

function nxs_after_switch_theme()
{
	// toggle option
	update_option('nxs_do_postthemeactivation', 'true');
	nxs_setuprolesandcapabilities();
	header("Location: " . admin_url() . "admin.php?page=nxs_backend_overview");
	die(); 
}

global $pagenow;
if (is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php')
{
	nxs_after_switch_theme();
}

function nxs_after_theme_activate_notice_admin()
{
	$messagedata = nxs_gettransientnexusservervalue("themespage", "activated", array());
	echo $messagedata["html"];
}

add_action('admin_enqueue_scripts', 'nxs_framework_theme_styles');

// ensures all templates are processed by our drag'drop system, 
// enabling configurable (sub)headers, sidebars, (sub)footers and pagedecorators
// uses nxs_gettemplateproperties()
function nxs_template_include($template)
{
	// force all pages to be handled by page-template.php
	// note, this overrides all regular templates (like woocommerce), on purpose

	if (!nxs_hastemplateproperties())
	{
		// obsolete old version...
		if (is_attachment())
		{
			// leave template as-is
		}
		else if (is_archive())
		{
			// leave template as-is
		}
		else
		{
			$template = NXS_FRAMEWORKPATH . '/page-template.php';
		}
	}
	else
	{
		// store the original template that was about to render this request
		global $nxs_gl_templates_wp;
		$nxs_gl_templates_wp = $template;
		
		
		if (is_attachment())
		{
			// leave template as-is
		}
		else
		{
			$template = NXS_FRAMEWORKPATH . '/page-template.php';
		}
	}
		
	return $template;
}
add_filter('template_include', 'nxs_template_include', 9999);

add_action("init", "nxs_init_handledebug", 30);

function nxs_init_handledebug()
{
	if (isset($_REQUEST["nxslocalizetest"]) && $_REQUEST["nxslocalizetest"] == "sync")
	{
		if (nxs_has_adminpermissions())
		{
			$destinationlang = "nl";
			if (!isset($_REQUEST["scope"]))
			{
				$scope = "*";
			}
			else
			{
				$scope = $_REQUEST["scope"];
			}
			nxs_localization_distributetolang($destinationlang, $scope);
		}
	}
	
	if (isset($_REQUEST["nxslocalizetest"]) && $_REQUEST["nxslocalizetest"] == "thumbs")
	{
		if (nxs_has_adminpermissions())
		{
			$destinationlang = "nl";
			if (!isset($_REQUEST["scope"]))
			{
				$scope = "*";
			}
			else
			{
				$scope = $_REQUEST["scope"];
			}
			nxs_localization_distributetolang_stage2($destinationlang, $scope);
		}
	}
	
	if (isset($_REQUEST["nxslocalizetest"]) && $_REQUEST["nxslocalizetest"] == "list")
	{
		if (nxs_has_adminpermissions())
		{
			echo "blogs:";
			global $wpdb;
			$blogs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM wp_blogs ORDER BY blog_id" ) );
			var_dump($blogs);
			die();
		}
	}
	
	if (isset($_REQUEST["nxslocalizetest"]) && $_REQUEST["nxslocalizetest"] == "sanitize")
	{
		if (nxs_has_adminpermissions())
		{
			$destinationlang = "nl";
			$destinationblogid = nxs_localization_getblogidforlanguage($destinationlang);
			// GLOBAL IDS / SANITY CHECK / DATA CONSISTENCY FIXER
			require_once(NXS_FRAMEWORKPATH . '/nexuscore/dataconsistency/dataconsistency.php');
			switch_to_blog($destinationblogid);
			$report = nxs_ensuredataconsistency("*");
			restore_current_blog();
			echo "consistency report;<br />";
			echo $report;
			echo "<br />";
			echo "back :)";
			die();
		}
	}
	
	if (isset($_REQUEST["nxslocalizetest"]) && $_REQUEST["nxslocalizetest"] == "cleanimg")
	{
		if (nxs_has_adminpermissions())
		{
			nxs_cleanimg();
			echo "<br />";
			echo "back :)";
			die();
		}
	}
	
	
	if (isset($_REQUEST["nxslocalizetest"]) && $_REQUEST["nxslocalizetest"] == "listposttypes")
	{
		if (nxs_has_adminpermissions())
		{
			global $wpdb;
			
			// we do so for truly EACH post (not just post, pages, but also for entities created by third parties,
			// as these can use the pagetemplate concept too. This saves development
			// time for plugins, and increases consistency of data for end-users
			$q = "
						select ID postid
						from $wpdb->posts
					";
			$origpostids = $wpdb->get_results($q, ARRAY_A);
			$distinct = array();
			// filter out posts that are of specific post-types
			foreach ($origpostids as $i => $origrow)
			{
				$origpostid = $origrow["postid"];
				$posttype = get_post_type($origpostid);
				if (!in_array($posttype, $distinct))
				{
					$distinct[] = $posttype;
					echo "$posttype; <br />";
					if ($posttype == "shop_order")
					{
						$pm = get_post_meta($origpostid);
						var_dump($pm);
					}
				}

			}
			
			echo "<br />";
			echo "back :)";
			die();
		}
	}
	
	//
	// debug / patch section
	//
	
	if (isset($_REQUEST["nxspatch"]))
	{
		if ($_REQUEST["nxspatch"] == "patch20131011001_turbo")
		{
			require_once(NXS_FRAMEWORKPATH . '/nexuscore/patches/patches.php');
			nxs_applypatch($_REQUEST["nxspatch"], $args);
			echo "Applied upgrade patch... please refresh screen";
			die();
		}		
		if ($_REQUEST["nxspatch"] == "patch20130610002_clear")
		{
			require_once(NXS_FRAMEWORKPATH . '/nexuscore/patches/patches.php');
			nxs_applypatch($_REQUEST["nxspatch"], $args);
			echo "Applied upgrade patch... please refresh screen";
			die();
		}
		
		if ($_REQUEST["nxspatch"] == "patch20131003001_imgrename")
		{
			require_once(NXS_FRAMEWORKPATH . '/nexuscore/patches/patches.php');
			nxs_applypatch($_REQUEST["nxspatch"], $args);
			echo "Applied upgrade patch... please refresh screen";
			die();
		}
		
		if ($_REQUEST["nxspatch"] == "patch20131010001_addrolecapabilities")
		{
			require_once(NXS_FRAMEWORKPATH . '/nexuscore/patches/patches.php');
			nxs_applypatch($_REQUEST["nxspatch"], $args);
			echo "Applied upgrade patch... please refresh screen";
			die();
		}
	}
}