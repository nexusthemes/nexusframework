<?php 

// escape post, request, n ensure, escape
// see nxs_ensure_slashesstripped()

if (defined('NXS_FRAMEWORKLOADED'))
{
	echo "NXS_FRAMEWORKLOADED was already loaded?!";
	die();
}
define('NXS_FRAMEWORKLOADED', true);

function nxs_die()
{
	error_log("nxs die");
	die();
}

function nxs_ob_start($output_callback = "")
{
	$shouldbufferoutput = true;
	
	if ($_REQUEST["nxs"] == "nobuffer")
	{
		if (nxs_has_adminpermissions())
		{
			$shouldbufferoutput = false;
		}
	}
	
	if ($shouldbufferoutput)
	{
		if ($output_callback  != "") { $result = ob_start($output_callback); } else { $result = ob_start(); }
	}
	else
	{
		$result = "overruled (no output buffering)";
	}
	
	return $result;
}


function nxs_ob_get_contents()
{
	$shouldbufferoutput = true;
	
	if ($_REQUEST["nxs"] == "nobuffer")
	{
		//$bt = debug_backtrace();
		//print_r($bt);
		//echo "that it :)";
		//die();
		
		if (nxs_has_adminpermissions())
		{
			$shouldbufferoutput = false;
		}
	}
	
	if ($shouldbufferoutput)
	{
		$result = ob_get_contents();
	}
	else
	{
		$result = "overruled (no output buffering)";
	}
	
	return $result;
}

function nxs_ob_end_clean()
{
	$shouldbufferoutput = true;
	
	if ($_REQUEST["nxs"] == "nobuffer")
	{
		if (nxs_has_adminpermissions())
		{
			$shouldbufferoutput = false;
		}
	}
	
	if ($shouldbufferoutput)
	{
		$result = ob_end_clean();
	}
	else
	{
		$result = "overruled (no output buffering)";
	}
	
	return $result;
}

function nxs_ob_get_clean()
{
	$shouldbufferoutput = true;
	
	if ($_REQUEST["nxs"] == "nobuffer")
	{
		if (nxs_has_adminpermissions())
		{
			$shouldbufferoutput = false;
		}
	}
	
	if ($shouldbufferoutput)
	{
		$result = ob_get_clean();
	}
	else
	{
		$result = "overruled (no output buffering)";
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
	return;
}

function nxs_get_minimal_mb_memory_for_themes(){
	return 64;
}

function nxs_has_enough_memory_available()
{
	$shouldtrytoincrease = true;
	return nxs_has_enough_memory_available_v2($shouldtrytoincrease);
}

function nxs_mem_increasememifneeded()
{
	$shouldtrytoincrease = true;
	nxs_has_enough_memory_available_v2($shouldtrytoincrease);
}

// check if the memory limit is at least 64M
function nxs_has_enough_memory_available_v2($shouldtrytoincrease)
{
	// we store the configured mem limit of ini in global
	// variable as its possible that the value changes
	// in time...
	global $nxs_gl_memlimitini;
	$memory_limit = ini_get('memory_limit');
	
	// some servers return a lowercase "m" (p.e. 256m),
	// instead of upper case; convert those
	$memory_limit = str_replace("m", "M", $memory_limit);
	
	$nxs_gl_memlimitini = $memory_limit;
	if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
	    if ($matches[2] == 'G') {
	        $memory_limit = $matches[1] * 1024 * 1024 * 1024; // nnnG -> nnn GB
	    } else if ($matches[2] == 'M') {
	        $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MV
	    } else if ($matches[2] == 'K') {
	        $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
	    }
	}

	$max_limit_in_mb = nxs_get_minimal_mb_memory_for_themes();
	$max_limit_in_bytes = $max_limit_in_mb * 1024 * 1024; // 64M

	$result = ($memory_limit >= $max_limit_in_bytes); // at least 64M?
	
	if ($result === false)
	{
		if ($shouldtrytoincrease)
		{
			// increase it
			$required_mem_in_mb = nxs_get_minimal_mb_memory_for_themes() . 'M';
			ini_set('memory_limit', $required_mem_in_mb);
			// recursion (1x)
			$result = nxs_has_enough_memory_available_v2(false);
		}
	}
	
	return $result;
}

function nxs_memory_notifynotenoughmemory()
{
	global $nxs_gl_memlimitini;
	?>
	<div class="error">
    <p>
    	This theme requires at least <?php echo nxs_get_minimal_mb_memory_for_themes(); ?>M of memory. Currently there is only <?php echo $nxs_gl_memlimitini; ?> memory configured on the server.
    </p>
  </div>
	<?php
}

if (is_admin()){
	if (!nxs_has_enough_memory_available())
	{
		add_action('admin_notices', 'nxs_memory_notifynotenoughmemory');
		return;
	}
}

//
nxs_mem_increasememifneeded();

// tell cache plugins to not cache any page;
// if caching is wanted, user should use the 
// build in caching implementation we use
// would be best to output a warning in that 
// case in the WP backend...
if (!defined("DONOTCACHEPAGE")) { define('DONOTCACHEPAGE', 'true'); } // WP SUPER CACHE
if (!defined("LSCACHE_NO_CACHE")) { define('LSCACHE_NO_CACHE', true);	} // LIGHT SPEED CACHE

//
// FEATURES IMAGES
//
add_theme_support("post-thumbnails");	// if sites use feature images we support them, the size of the thumbnails is set in the 'aftertheme'
add_action('after_setup_theme', 'nxs_after_setup_theme');

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
	else if ($remoteaddress == "188.207.117.125")
	{
		$result = true;
	}
	
	return $result;
}

// somehow pages archive pages result in a 404 (is_archive == false),
// to fix this we use the following code,
// kudos to http://ilikekillnerds.com/2012/11/fixing-wordpress-404-custom-post-type-archive-pagination-issues-with-posts-per-page/
function custom_posts_per_page( $query ) 
{
  if ( $query->is_archive() ) 
  {
  	$ppp = get_option('posts_per_page');
    set_query_var('posts_per_page', $ppp);
  }
}
add_action( 'pre_get_posts', 'custom_posts_per_page' );

// hide php warning outputs on the screen
$shouldlimiterrorreporting = true;
if (nxs_isdebug())
{
	if (isset($_REQUEST["nxs"]) && $_REQUEST["nxs"] == "nobuffer")
	{
		$shouldlimiterrorreporting = false;
	}
}

if ($shouldlimiterrorreporting)
{
	error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE);
}

// always
nxs_saveobclean();

function nxs_getcharset()
{
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
define('NXS_DEFINE_NXSDEBUGWEBSERVICES', false);	// default to false
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

// data protection
require_once(NXS_FRAMEWORKPATH . '/nexuscore/dataprotection/dataprotection.php');



// handle webmethod, is this is a webmethod
// note that if this _is_ a webmethod, the system will stop execution after this method
add_action('init', 'nxs_handlewebmethods', 999990);

add_action('init', 'nxs_dataprotection_enforcedataprotectiontypeatstartwebrequest', 999991);

//After category is updated, set a flag to do a data consistency check
add_action('edited_terms', 'nxs_dataconsistency_after_edited_terms');

// compliance with feeds
nxs_addfeedsupport();

// compliance with popular third party plugins
nxs_addwoocommercesupport();

function nxs_session_hasstartedactivesession()
{
	$r = isset($_COOKIE[session_name()]);
	return $r;
}

function nxs_initializesessionfrombrowsercookieifexists()
{
	if (isset($_COOKIE[session_name()]))
	{
		nxs_ensure_sessionstarted();
	}
}

function nxs_ensure_sessionstarted()
{
	// don't start session for wp cron
	if ( defined( 'DOING_CRON' ) )
	{
		return;
	}
	
	// init session
  if (!session_id()) 
  {
  	$r = session_start();
  	if ($r == false)
  	{
  		// it fails, one (most likely) reason is that the program has already outputted content to the user, 
  		// meaning its too late to send the cookie value
			$url = nxs_geturlcurrentpage();
  		$msg = "nxs_ensure_sessionstarted; unable to start session; most likely some other script already outputted to the browser ($url)";
  		error_log($msg);
  		echo $msg;
  		die();
  	}
  }
}

function nxs_widgets_gettotalwidgetareacount()
{
	//
	// sidebars (could have been any number, but 8 sounds like sufficient ...)
	//
	$result = 12;
	$result = apply_filters("nxs_f_widgets_gettotalwidgetareacount", $result);
	return $result;
}

function nxs_widgets_emptyallwidgetareas()
{
	error_log("debug; widget; empty; 1");
	
	$widgets = get_option( 'sidebars_widgets', array());

	error_log("debug; widget; empty; 2");
	
	// get backups
	$backupwidgets = get_option( 'nxs_sidebars_widgets_backup', array() );
	$backupwidgets[] = $widgets;
	
	error_log("debug; widget; empty; 3");

	// add previous one to backups
	update_option('nxs_sidebars_widgets_backup', $backupwidgets);
	
	error_log("debug; widget; empty; 4");

	
	$count = nxs_widgets_gettotalwidgetareacount();
	for ($areaindex = 1; $areaindex <= $count; $areaindex++)
	{
		$widgets["sidebar-{$areaindex}"] = array();
	}
	update_option('sidebars_widgets', $widgets);
}

if (function_exists('register_sidebar'))
{
	$count = nxs_widgets_gettotalwidgetareacount();
	register_sidebars($count, array('name' => 'WP Backend Widget Area %d'));
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
			
			global $nxs_gl_templates_wp;
			echo "original template for WP;" . $nxs_gl_templates_wp;
			
			global $nxs_global_current_containerpostid_being_rendered;
			echo "we zijn ook;" . $nxs_global_current_containerpostid_being_rendered;
			echo "we zijn;" . get_the_ID();
			echo "home is;" . nxs_gethomepageid();
			echo "<br />";
			echo "<br />";
			echo "<br />";
			echo "<br />";
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

add_action('init', 'nxs_init', 200);
add_action('admin_init', 'nxs_init', 200);
function nxs_init() 
{
	// support for ads.txt
	$uricurrentpage = nxs_geturicurrentpage();
	if (nxs_stringendswith($uricurrentpage, "ads.txt"))
	{
		$lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		$value = $lookups["ads.txt"];
		if ($value != "")
		{
			echo $value;
			//echo "ads.txt :)";
			die();
		}
	}
	
	if (nxs_has_adminpermissions())
  {
  	if (isset($_REQUEST["nxs"]))
  	{
  		if ($_REQUEST["nxs"] == "debug_rules")
  		{
  			$rules = get_option('rewrite_rules');
  			echo nxs_prettyprint_array($rules);
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "debug_widgets")
  		{
  			echo "sidebars:<br />";
  			$sidebars = get_option( 'sidebars_widgets', array() );
  			var_dump($sidebars);
  			
  			echo "<br />backups:<br />";
  			
  			$backups = get_option( 'nxs_sidebars_widgets_backup', array() );
  			var_dump($backups);
  			
  			echo "<br />so far :)";
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "fix_rules")
  		{
  			nxs_wp_resetrewriterules();
  			echo "tuned rules :)";
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "flush_rewrite_rules")
  		{
  			//echo "category_base:";
  			//$category_base       = get_option( 'category_base' );
  			//var_dump($category_base);
  			//die();
  			global $wp_rewrite;
  			
  			$wp_rewrite->set_permalink_structure( '' );
				$wp_rewrite->flush_rules( true );
  			
  			$permalink_structure = get_option( 'permalink_structure' );
  			$category_base = get_option( 'category_base' );
  			$wp_rewrite->set_permalink_structure($permalink_structure);
  			$wp_rewrite->set_category_base($category_base);
  			
  			$wp_rewrite->flush_rules( $hard );

  			$wp_rewrite->set_permalink_structure( $permalink_structure );
  			$wp_rewrite->set_category_base( $category_base );
  			
  			$wp_rewrite->flush_rules( $hard );
  			
  			// update_option( 'rewrite_rules', '' );
  			
  			
  			echo "flush, aha!";
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "requirethemeactivation")
  		{
  			update_option('nxs_do_postthemeactivation', 'true');
				nxs_setuprolesandcapabilities();
				echo "now switch to 2015 and back and the re-activation should start :)";
				die();
  		}
  		else if ($_REQUEST["nxs"] == "getoption")
  		{
  			$key = $_REQUEST["key"];
  			$r = get_option($key);
  			var_dump($r);
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "nxs_reset_globalidtovalue")
  		{
  			$postid = $_REQUEST["postid"];
  			$value = $_REQUEST["value"];
  			if ($postid != "")
  			{
  				echo "resetting globalid for postid $postid<br />";
  				if ($value == "")
  				{
  					nxs_reset_globalid($postid);
  				}
  				else
  				{
  					nxs_reset_globalidtovalue($postid, $value);
  				}
  			}
  			else
  			{
  				echo "no postid set?";
  			}
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "urlinfo")
  		{
  			echo "siteurl:" . get_site_url() . "<br />";
  			echo "homeurl:" . get_home_url() . "<br />";
  			echo "nxs homeurl:" . nxs_geturl_home() . "<br />";  			
  			die();
  		} 
  		else if ($_REQUEST["nxs"] == "showclip")
  		{
  			echo "clipboardmeta:<br />";
  			nxs_ensure_sessionstarted();
  			var_dump($_SESSION["nxs_clipboardmeta"]);
  			var_dump($_SESSION);
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "testsession")
  		{
				echo "sessionid: " . session_id() . "<br />";
  			nxs_ensure_sessionstarted();
  			echo "sessionid: " . session_id() . "<br />";
				if (empty($_SESSION['count'])) {
				   $_SESSION['count'] = 1;
				} else {
				   $_SESSION['count']++;
				}
				?>
				<p>
				Hello visitor, you have seen this page <?php echo $_SESSION['count']; ?> times.
				</p>
				<?php
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "checkphoton")
  		{
  			if (function_exists("jetpack_photon_url"))
  			{
  				echo "Jetpack is installed and can generate photon URLs";
  			}
  			else
  			{
  				echo "No";
  			}
  			echo "<br />";
  			if (class_exists( 'Jetpack' ) && method_exists( 'Jetpack', 'get_active_modules' ) && in_array( 'photon', Jetpack::get_active_modules() ))
  			{
  				echo "Photon is an active Jetpack module";
  			}
  			else
  			{
  				echo "No";
  			}
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "uploaddir")
  		{
  			$uploaddir = wp_upload_dir();
  			var_dump($uploaddir);
  			
  			if ($_REQUEST["v2"] == "true")
  			{
  				// update cache!
  				$uploaddir = wp_upload_dir(null, true, true);
  				var_dump($uploaddir);
  			}
  			
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "server")
  		{
  			var_dump($_SERVER);
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "pluginfolderwritable")
  		{
  			$folder = ABSPATH.'wp-content/plugins/';
  			var_dump($folder);
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "checklicenseserver")
  		{
  			$urls = array("https://www.example.com", "http://www.ip-adress.eu/");
  			if ($_REQUEST["url"] != "")
  			{
  				$urls = array($_REQUEST["url"]);
  			}
  			foreach ($urls as $url)
  			{
  				var_dump($url);
  				echo "<br />";
  				
  				// fetch
	  			$content_filegetcontents = file_get_contents($url);
	  			// report
	  			echo "Testing - {$url}<br />";
	  			echo "file_get_contents returns:<br />";
	  			if ($_REQUEST["output"] == "asis")
	  			{
	  				echo $content_filegetcontents;
	  			}
	  			else
	  			{
						echo htmlentities($content_filegetcontents);
					}
					echo "<br /><br />";
	  			
	  			if ($_REQUEST["curl"] == "skip")
	  			{
	  				echo "curl returns: skipped<br />";
	  			}
	  			else if ($_REQUEST["curl"] == "initonly")
	  			{
	  				echo "curl_init function exists?";
	  				var_dump(function_exists("curl_init"));
	  				
	  				echo "curl_version function exists?";
	  				var_dump(function_exists("curl_version"));
	  				
	  				
	  				
	  				echo "curl initing...<br />";
	  				$ch = curl_init();
	  				
	  				echo "curl version...<br />";
	  				$v = curl_version();
	  				var_dump($v);
	  				
	  				curl_close($ch);
	  				echo "closed :)<br />";
	  			}
	  			else
	  			{ 
		  			$ch = curl_init();
				    curl_setopt($ch, CURLOPT_URL, $url);
				    curl_setopt($ch, CURLOPT_REFERER, "https://www.example.org/yay.htm");
				    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
				    curl_setopt($ch, CURLOPT_HEADER, 0);
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				    $content_curl = curl_exec($ch);
				    curl_close($ch);
				    echo "curl returns:<br />";
						echo htmlentities($content_curl);  			
						echo "<br /><br />";
				  }

					echo "-----------<br />";
  			}
  			
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
			else if ($_REQUEST["nxs"] == "nxs_cache_getcachefolder")
			{
				$x = nxs_cache_getcachefolder();
				var_dump($x);
				echo "<br />";
				$r = is_writable($x);
				var_dump($r);
				echo "<br />";
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
		  else if ($_REQUEST["nxs"] == "wpversion")
			{
				echo "wpversion:" . get_bloginfo('version');
				die();
			}
		  else if ($_REQUEST["nxs"] == "dumppostidswithmeta")
		  {
		  	$key = $_REQUEST["key"];
		  	$value = $_REQUEST["value"];
		  	$r = nxs_wp_getpostidsbymeta($key, $value);
		  	var_dump($r);
		  	die();
		  }
		  else if ($_REQUEST["nxs"] == "rand")
		  {
		  	$random = rand($_REQUEST["min"], $_REQUEST["max"]);
		  	$highest = getrandmax();
		  	echo "random result: [{$random}] highest: [{$highest}]";
		  	
		  	
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
		  	echo "activesitesettings:<br /><br />";
		  	echo "postid:<br />";
		  	$postids = nxs_get_postidsaccordingtoglobalid("activesitesettings");
				var_dump($postids);
				//echo "<br /><br />prettyprint:<br />";
				$sitemeta = nxs_getsitemeta_internal(false);
				//echo nxs_prettyprint_array($sitemeta);
				//echo "<br /><br />dumped:<br />"; 
				//var_dump($sitemeta);

				echo "<br /><br />json:<br />"; 
				echo "<textarea>";
				$jsonsitemeta = json_encode($sitemeta);
				echo "$jsonsitemeta";
				echo "</textarea>";
				echo "<br />";

				echo "<br /><br />json2:<br />"; 
				$jsonsitemeta = str_replace("\r\n", "<br class='nxsrnfix' />", $jsonsitemeta);
				$jsonsitemeta = str_replace("\r", "<br class='nxsrfix' />", $jsonsitemeta);
				$jsonsitemeta = str_replace("\n", "<br class='nxsnfix' />", $jsonsitemeta);
				echo "$jsonsitemeta<br />";

		  	die();
		  }
		  else if ($_REQUEST["nxs"] == "fixwrongglobalidsmanual")
		  {
		  	global $wpdb;
	
				$q = "
						select post_id postid, meta_value globalid
						from $wpdb->postmeta
						where meta_value in 
						(
							select distinct meta_value 
							from 
								$wpdb->postmeta 
							where meta_key = 'nxs_globalid' 
							group by meta_value 
							having  count(1) > 1
						)
						order by globalid asc, postid desc
					";
					
				$dbresult = $wpdb->get_results($q, ARRAY_A );
				var_dump($dbresult);
				
				if (count($dbresult) > 0)
				{
					// er zijn globalids gevonden die gedeeld worden over meerdere postid's; inconsistentie!
					// we resetten de global ids van de nieuwste post's, de oudste (met de laagste postid) is leidend!
					
					$globalid = "";
					$postid = "";
					
			  	foreach ($dbresult as $dbrow)
			  	{
			  		$currentpostid = $dbrow["postid"];
			  		$currentglobalid = $dbrow["globalid"];
			  		
			  		if ($currentpostid == $postid && $currentglobalid == $globalid)
			  		{
			  			// found a duplicate; replace all existing ones with 
			  			// the single new one (keeping the globalid)
			  			delete_post_meta($currentpostid, "nxs_globalid");
			  			nxs_reset_globalidtovalue($currentpostid, $currentglobalid);
			  		}
			  		else if ($currentpostid != $postid)
			  		{
			  			// we found a new postid
			  			$globalid = $currentglobalid;
			  			$postid = $currentpostid;
			  			continue;
			  		}
			  		else if ($currentglobalid != $globalid)
			  		{
			  			// the postid is the same, but the globalid is different
				  		$currentpostid = $dbrow["postid"];
		
			  			// resetten globalid voor deze postid (we keep the last one)
			  			$newglobalidforthispostid = nxs_reset_globalid($currentpostid);
			  			$result["log"] .= "[...]";
			  			
			  			if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
							{
				    		echo "<!-- ";
				    	}
			  			echo "$currentpostid; from $globalid to $newglobalidforthispostid<br />";
			  			if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
							{
				    		echo " -->";
				    	}
			  		}
			  	}
				}
				echo "manual fix done :)";
				die();
		  }
		  else if ($_REQUEST["nxs"] == "setactivesitesettingspostid")
		  {
		  	$postid = $_REQUEST["postid"];
		  	nxs_reset_globalidtovalue($postid, "activesitesettings");
		  	echo "done";
		  	die();
		  }
		  else if ($_REQUEST["nxs"] == "setactivesitesettings")
		  {
		  	//var_dump($_POST);
		  	$sitesettingsjson = $_POST["sitesettingsjson"];
				$sitesettingsjson = stripslashes($sitesettingsjson);
		  	
		  	if ($sitesettingsjson == "")
		  	{
		  		?>
		  		<form method="POST">
		  			<input type="text" name="sitesettingsjson" value="yourjson here" />
		  			<input type="submit" value="Set site settings" />
		  		</form>
		  		<?php
		  	}
		  	else
		  	{
			  	$newsettings = json_decode($sitesettingsjson, true);
			  	if (count($newsettings) == 0)
			  	{
			  		echo "no, or invalid json found, breaking..<br />";
			  		echo "found:" . $sitesettingsjson . "<br />";
			  		var_dump($newsettings);
			  		die();
			  	}

		  		echo "about to override site settings...";
		  		
			  	$postids = nxs_get_postidsaccordingtoglobalid("activesitesettings");
			  	$cnt = count($postids);
			  	if ($cnt == 0 || $cnt > 1)
			  	{
			  		if ($_REQUEST["fix"] == "true")
			  		{
			  			// create CPT record
						  $my_post = array
						  (
								'post_title' => "site settings f",
								'post_name' => "site settings f",	// url
								'post_content' => '',
								'post_status' => "publish",
								'post_author' => wp_get_current_user()->ID,
								'post_excerpt' => '',
								'post_type' => "settings",
							);
							$postid = wp_insert_post($my_post, $wp_error);
							var_dump($postid);
							
							if ($postid != 0)
							{
								// 
								nxs_reset_globalidtovalue($postid, "activesitesettings");
								echo "resetted globalid of postid";
								die();
							}
							else
							{
								echo "nope";
								die();
							}
			  		}
			  		else
			  		{
			  			nxs_webmethod_return_nack("error; found $cnt postids for activesitesettings? (use fix=true)");
			  		}
			  	}
			  	$postid = $postids[0];
			  	
			  	echo "active site settings is using postid: $postid <br />";
			  	
			  	$metadatakey = 'nxs_core';
			  	$updateresult = update_post_meta($postid, $metadatakey, nxs_get_backslashescaped($newsettings));
			  	
		  		echo "done :)";
		  		die();
		  	}
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
			else if ($_REQUEST["nxs"] == "dumpglobalid")
			{
				$globalid = $_REQUEST["globalid"];
				echo "dumpglobalid $globalid<br />";
				$destinationpostids = nxs_get_postidsaccordingtoglobalid($globalid);
				if (count($destinationpostids) == 0)
				{
					echo "not found";
					die();
				}
				else if (count($destinationpostids) > 1)
				{
					echo "multiple posts match;";
					var_dump($destinationpostids);
					die();
				}
				// one match
				$postid = $destinationpostids[0];
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
			else if ($_REQUEST["nxs"] == "dumppost")
			{
				$postid = $_REQUEST["postid"];
				echo "dumppost $postid<br />";
				
				$exists = nxs_postexistsbyid($postid);
				if ($exists) 
				{
					$poststatus = get_post_status($postid);
					echo "poststatus: $poststatus<br />";
					
					echo "post: $poststatus<br />";
					$the_post = get_post($postid);
					var_dump($the_post);
					echo "<br />";
					
					$posttype = get_post_type($postid);
					if ($posttype == "attachment")
					{
						$attachmenturl = wp_get_attachment_url($postid);
						echo "attachmenturl {$attachmenturl}<br />"; 
					}
					$link = get_permalink($postid);
					echo "post exists (open <a target='_blank' href='$link'>$link</a>)<br />";
					
					//
					echo "<br />--------------<br />";
					echo "<br />CPT: {$posttype}<br />";
					
					if (function_exists("nxs_qa_ispostidreferenced"))
					{
						echo "<br />--------------<br />";
						echo "<br />Is referenced?<br />";
						$verbose = true;
						$result = nxs_qa_ispostidreferenced($postid, $verbose);
						var_dump($result);
						echo "<br />--------------<br />";
					}
					
					$subposttype = nxs_get_nxssubposttype($postid);
					echo "subposttype {$subposttype}<br />"; 
				} 
				else
				{ 
					echo "post does not exist<br />"; 
				}
				
				echo "post type:{$posttype}<br />";
				$needleglobalid = nxs_get_globalid($postid, false);
				echo "globalid: $needleglobalid<br />";
				
				echo "----- local meta ----";
				$origpost_meta_all = get_post_meta($postid);
				foreach ($origpost_meta_all as $key => $val)
				{
					echo "meta key: $key<br />";
					echo "meta val: <br />";
					echo "<pre>";
					if ($_REQUEST["fix"] == "true")
					{
						$val = preg_replace('~\xc2\xa0~', '&nbsp;', $val);
						//$val = esc_html($val);
					}
					var_dump($val);
					echo "</pre>";
					echo "<br />";
					echo "<br />";
					echo "<hr />";
				}
				
				echo "post_meta_all: $needleglobalid<br />";
				$origpost_meta_all = nxs_get_post_meta_all($postid);
				foreach ($origpost_meta_all as $key => $val)
				{
					echo "meta key: $key<br />";
					echo "meta val: <br />";
					echo "<pre>";
					if ($_REQUEST["fix"] == "true")
					{
						$val = preg_replace('~\xc2\xa0~', '&nbsp;', $val);
						//$val = esc_html($val);
					}
					var_dump($val);
					echo "</pre>";
					echo "<br />";
					echo "<br />";
					echo "<hr />";
				}
				
				$post_content = get_post_field('post_content', $postid);
				echo "post_content: $post_content";
				
				die();
			}
			else if ($_REQUEST["nxs"] == "dumpmodel")
			{
				global $nxs_g_modelmanager;
				$modeluri = $_REQUEST["modeluri"];
				if ($modeluri == "")
				{
					echo "modeluri not set";
					die();
				}
				$contentmodel = $nxs_g_modelmanager->getcontentmodel($modeluri);
				var_dump($contentmodel);
				die();
			}
			else if ($_REQUEST["nxs"] == "parsepost")
			{
				$postid = $_REQUEST["postid"];
				echo "dumppost $postid<br />";
				$poststructure = nxs_parsepoststructure($postid);
				echo nxs_prettyprint_array($poststructure);
				/*
				sanitizes broken poststructure;
				$i = 0;
				$maxi = count($poststructure);
				for ($i = 0; $i < $maxi; $i++)
				{
					if ($poststructure[$i]["pagerowtemplate"] == "")
					{
						unset($poststructure[$i]);
					}
				}
				$poststructure = array_values($poststructure);
				echo "becomes:<br />";
				echo nxs_prettyprint_array($poststructure);
				//die();
				nxs_storebinarypoststructure($postid, $poststructure);
				die();
				*/
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
			else if ($_REQUEST["nxs"] == "listactiveplugins")
  		{
  			if ( ! function_exists( 'get_plugins' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}
				
				$all_plugins = get_plugins();
				
				// Save the data to the error log so you can see what the array format is like.
				//error_log( print_r( $all_plugins, true ) );

  			echo "list:";
  			$apl=get_option('active_plugins');
  			///var_dump($apl);
				$plugins=get_plugins();
				
				$activated_plugins=array();
				foreach ($apl as $p)
				{
					if(isset($plugins[$p]))
					{
				  	array_push($activated_plugins, $plugins[$p]);
					}           
				}
				
				echo nxs_prettyprint_array($activated_plugins);
				die();
  		}
  		else if ($_REQUEST["nxs"] == "urlcurrentpage")
  		{
  			echo nxs_geturlcurrentpage();
  			die();
  		}
  		else if ($_REQUEST["nxs"] == "server")
  		{
  			var_dump($_SERVER);
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
			// OK
		}
		else if( defined( 'WP_CLI' ) )
		{
			// WP-CLI always should have access
		}
		else if (is_user_logged_in())
		{
			// OK
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
				<script>
					window.location.href="<?php echo $url; ?>";
				</script>
				<?php
				wp_redirect($url, 302);
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
add_action("nxs_action_webmethod_init", "nxs_ext_initialize_frontendframework");

add_filter('wpseo_robots', 'nxs_webmethod_robots');
function nxs_webmethod_robots($result)
{
	if (nxs_iswebmethodinvocation())
	{
		$result = "noindex";
	}
	return $result;
}

// allow uploads of kml and kmz files (Google Maps)
function add_upload_mimes($mimes) 
{
  $mimes['kml'] = 'application/xml';
  $mimes['kmz'] = 'application/zip';
  return $mimes;
}
add_filter('upload_mimes', 'add_upload_mimes');

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
}

function nxs_theme_getversion()
{
	$meta = nxs_theme_getmeta();
	$version = $meta["version"];
	
	$version = apply_filters('nxs_f_theme_getversion', $version);	

	return $version;
}

add_action('nxs_action_postfooterlink', 'nxs_render_postfooterlink');
function nxs_render_postfooterlink()
{
	global $nxs_g_modelmanager;
	
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
	$footerhtmltemplate = apply_filters('nxs_footerhtmltemplate', $footerhtmltemplate);
	
	// apply lookup tables
	$temp  = array("text" => $footerhtmltemplate);
	$temp = nxs_filter_translatelookup($temp, array("text"));
	$footerhtmltemplate = $temp["text"];
	
	$lookup = array();
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
	$baseurl .= "https://";
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
	
	if (trim($footerhtmltemplate) != "")
	{
		?>
	  <p id="nxs-copyright" class="nxs-clear padding nxs-applylinkvarcolor">
		  <?php
			$themelink = "<a target='_blank' href='" . $themeurl . "' title='" . $themetitle . "'>" . $themetitle . "</a>";
			
			//echo $themelink;
			
			if (is_user_logged_in())
			{
				$text = "Logout";
				
				if (nxs_has_adminpermissions())
				{
					$authenticatelink = "<a href=\"#\" onclick=\"nxs_js_ensureeditoractive(); nxs_js_popup_site_neweditsession('logouthome'); return false;\">{$text}</a>";
				}
				else
				{
					// cannot use the fancier popup, as subscribers don't see the popup
					$url = wp_logout_url();
					$authenticatelink = "<a href=\"{$url}\" return false;\">{$text}</a>";
				}
			}
			else
			{
				// user is not logged in
				
				$behaviour = "frontend";
				if (has_action("login_form"))
				{
					$behaviour = "backend";
				}
    		$behaviour = apply_filters("nxs_login_newloginbehaviour", $behaviour);
    		if (nxs_hassitemeta())
    		{
    			$lookup = nxs_lookuptable_getlookup();
    			if ($lookup["nxs_login_newloginbehaviour"] != "")
    			{
    				$behaviour = $lookup["nxs_login_newloginbehaviour"];
    			}
    		}
    		
    		if ($behaviour == "backend")
    		{
					$currenturl = get_permalink();
					$url = wp_login_url($currenturl);
					$authenticatelink = "<a href=\"{$url}\">Login</a>";
				}
				else
				{
					$authenticatelink = "<a href=\"#\" rel=\"nofollow\" onclick=\"nxs_js_popup_site_neweditsession('loginhome'); return false;\">Login</a>";
				}
			}
			
			$footerhtmltemplate = str_replace("{{{authenticatelink}}}", $authenticatelink, $footerhtmltemplate);
			$footerhtmltemplate = str_replace("{{{themelink}}}", $themelink, $footerhtmltemplate);
			$footerhtmltemplate = str_replace("{{{nexuslink}}}", $nexuslink, $footerhtmltemplate);
			
			echo $footerhtmltemplate;
			?>
		</p>
		<?php
	}
}

add_action('init', 'nxs_ensure_proper_permalinks', 1);
add_action('init', 'nxs_register_menus');
add_action('init', 'nxs_create_post_types_and_taxonomies');

function nxs_performdataconsistencycheck()
{
	if (nxs_isdataconsistencyvalidationrequired())
	{
		require_once(NXS_FRAMEWORKPATH . '/nexuscore/dataconsistency/dataconsistency.php');
		$isdataconsistent = nxs_ensuredataconsistency("*");
	}
}

function nxs_setjQ_nxs()
{
	?>
	<script>
		var jQ_nxs = jQuery.noConflict(true);
		var jQuery = jQ_nxs;
		
		if (typeof $ === 'undefined') 
		{
			// only if $ was not yet set, set it!
    	var $ = jQ_nxs;
		}
	</script>
	<?php
}		

function nxs_after_setup_theme()
{
	add_theme_support('title-tag');
	
	// support for additional image sizes
	add_image_size('nxs_cropped_200x200', 200, 200, TRUE );
	add_image_size('nxs_cropped_320x200', 320, 200, TRUE );	// used by the gallerybox
	add_image_size('nxs_cropped_320x512', 320, 512, TRUE );	// used by the gallerybox
}

// note; "Post Duplicator" is not compatible; use "duplicate post"; a different plugin
// tell the duplicate_post plugin to not clone the nxs_globalid custom meta field
add_filter('pre_option_duplicate_post_blacklist', 'nxs_pre_option_duplicate_post_blacklist');
function nxs_pre_option_duplicate_post_blacklist($result)
{
	$exclude = "nxs_globalid";
	if ($result == "")
	{
		$result = $exclude;
	}
	else
	{
		$ignorecasing = false;
		if (!nxs_stringcontains_v2($result, $exclude, $ignorecasing))
		{
			$result .= ",{$exclude}";
		}
	}
	return $result;
}

add_filter('image_size_names_choose', 'nxs_custom_sizes');
// 

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
  
  // allow pages to have excerpts too
  add_post_type_support('page', 'excerpt');
  
  // we also need to extend the query when a category page is requested,
  // as register_taxonomy_for_object_type unfortunately doesn't handle this itself (weird!?!)
  add_action('pre_get_posts', 'nxs_pre_get_posts_categorypageextension');  
  
  $hasadmin = nxs_has_adminpermissions();
  
  // posttype: "nxs_header"
  
  
	nxs_registernexustype("header", $hasadmin);				// holds content that is positioned at the top of the screen
	nxs_registernexustype("sidebar", $hasadmin);				// holds content that is positioned at the side of the screen
	nxs_registernexustype("footer", $hasadmin);				// holds content that is positioned at the bottom of the screen

	nxs_registernexustype("subheader", $hasadmin);			// holds content that is positioned below header, left of sidebar and above main content of the screen
	nxs_registernexustype("subfooter", $hasadmin); 		// holds content that is positioned: above footer, left of sidebar and below main content of the screen
	
	nxs_registernexustype("menu", false);					// since the WP menu's are not easily im/exportable, we use our own
	
	nxs_registernexustype("admin", false);
	nxs_registernexustype("settings", false);			// used to store various site-wide-settings such as colours, homepageid's, etc.
	
	nxs_registernexustype("systemlog", false);		// used by data consistency reports
	
	// posts and pages
	// taxonomie: both posts and pages have a subtype (for example "webpage", "searchpage", "blogentry", "...", etc.)
	if ($_REQUEST["nxs_showsubtype"] == "true") {
		$show_ui = true;
	}
	else {
		$show_ui = false;
	}

	register_taxonomy
	(
		'nxs_tax_subposttype',
		array('post','page'),
		array(
			'hierarchical' => false,
			'label' => 'Sub type',
			'query_var' => $hasadmin,
			'show_ui' => $show_ui,	// hide from ui
			'rewrite' => true
		)
	);

	$ispublic = false;
	nxs_registernexustype_withtaxonomies("genericlist", array("nxs_tax_subposttype"), $ispublic);
	
	$hadadmin = nxs_has_adminpermissions();
	nxs_registernexustype_withtaxonomies("templatepart", array("nxs_tax_subposttype"), $hadadmin);	// holds content that is positioned in between subheader and subfooter
	nxs_registernexustype_withtaxonomies("busrulesset", array("nxs_tax_subposttype"), $hadadmin);		// holds a set of business rules	
	
	// by default custom post types in WP get a "slug" in their
	// url, to be able to identify them. Our semantic entities
	// like "service", etc. should not get such a slug, as it
	// would make the URLs ugly (like site.com/service/my-service),
	// to resolve this we use the workaround as documented here
	// http://wordpress.stackexchange.com/questions/203951/remove-slug-from-custom-post-type-post-urls
	function nxs_cpt_getcptswithoutslug()
	{
		// the list of custom post types that should not get a slug in the
		// url in the permalinks (i.e. which should behave like pages/posts)
		$result = array();
		
		return $result;
	}
	
	// by default the cpt put their slug in front of the address, we dont want that
	// kudos to http://wordpress.stackexchange.com/questions/203951/remove-slug-from-custom-post-type-post-urls
	function na_remove_slug( $post_link, $post, $leavename ) 
	{
		$cpt = nxs_cpt_getcptswithoutslug();
		
    if (!in_array($post->post_type, $cpt) || 'publish' != $post->post_status ) 
    {
      return $post_link;
    }

    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

    return $post_link;
	}
	add_filter( 'post_type_link', 'na_remove_slug', 10, 3 );
	
	function na_parse_request( $query ) 
	{
    if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) 
    {
      return;
    }

    if ( ! empty( $query->query['name'] ) ) 
    {
    	$cpt = nxs_cpt_getcptswithoutslug();
			$new = array('post', 'page');
    	$merged = array_merge($new, $cpt);
      $query->set( 'post_type', $merged);
    }
	}
	add_action( 'pre_get_posts', 'na_parse_request' );
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
	if (!defined('NXS_WHITELABEL'))
	{
		add_menu_page('Nexus Theme', 'Nexus Theme', 'switch_themes', 'nxs_backend_overview', 'nxs_lazyactivate_backend_overview', '', nxs_getframeworkurl() . "/nexuscore/widgets/quote/img/quote_icon.png", '55.5');
		add_submenu_page("nxs_backend_overview", 'Overview', 'Overview', 'switch_themes', 'nxs_backend_overview', 'nxs_lazyactivate_backend_overview', '', nxs_getframeworkurl() . "/nexuscore/widgets/quote/img/quote_icon.png", '55.5');
	}
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
	$result = current_user_can(nxs_cap_getdesigncapability()) || is_super_admin();
	// allow plugins to override the behaviour
	$result = apply_filters('nxs_f_cap_hasdesigncapabilities', $result);
	return $result;
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
	
	// only mark the theme to require postthemeactivation if there's no active site settings
	// (preventing theme from doing sanity check if this was done before)
  if (!nxs_hassitemeta())
  {
		update_option('nxs_do_postthemeactivation', 'true');
		nxs_setuprolesandcapabilities();
	}
	
	do_action("nxs_theme_switchedmanually");
	
	header("Location: " . admin_url() . "admin.php?page=nxs_backend_overview&nxstrigger=afterswitchtheme");
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

// ensures all templates are processed by our drag'drop system, 
// enabling configurable (sub)headers, sidebars, (sub)footers and pagedecorators
// uses nxs_gettemplateproperties()
function nxs_template_include($template)
{
	define('NXS_TEMPLATEINCLUDED', true);	
	
	// force all pages to be handled by page-template.php
	// note, this overrides all regular templates (like woocommerce), on purpose

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
		
	return $template;
}
add_filter('template_include', 'nxs_template_include', 9999);

function nxs_framework_getheadmeta($result)
{
	$option .= "n"."x"."s";
	$option .= "_";
	$option .= "l"."i"."c";
	$option .= "e"."n"."s";
	$option .= "e"."k"."e";
	$option .= "y";
	$val = esc_attr(get_option($option));
	if ($val == "") 
	{ 
		$val = "freemium"; 
	}
	else
	{
		$pieces = explode(".", $val);
		if ($pieces[2] == "V3")
		{
			$val = $pieces[2] . "." . $pieces[3] . "." . $pieces[4] . "." . $pieces[5] . "." . $pieces[6];
		}
		else
		{
			$val = $pieces[2] . "." . $pieces[3];
		}
	}
	$result .= "L:{$val}" . " | ";
	return $result;
}
add_filter("nxs_f_getheadmeta", "nxs_framework_getheadmeta");

add_filter('get_header', 'nxs_template_getheader');
function nxs_template_getheader($name)
{
	global $nxs_global_row_render_statebag;
	if ($nxs_global_row_render_statebag != null)
	{
		// nothing to do here
		return;
	}
	
	if (!defined('NXS_TEMPLATEINCLUDED'))
	{
		// if we reach this stage, it means some plugin used
		// the template_redirect 
		
		if (is_singular())
		{
			// the containerpostid is the id of the (one and only) post
			global $post;
			$containerpostid = $post->ID;
		}
		else if (is_archive())
		{
			$containerpostid = "ARCHIVE";
		}
		else
		{
			// this happens if a plugin has a specific URL 
			// rewritten to a specific template include.
			// in that case we will render that specific content,
			// even though the front end editor features will be suppressed
			$containerpostid = "SUPPRESSED";
		}
		
		global $nxs_global_current_containerpostid_being_rendered;
		$nxs_global_current_containerpostid_being_rendered = $containerpostid;
		
		require_once(NXS_FRAMEWORKPATH . '/nexuscore/pagetemplates/blogentry/pagetemplate_blogentry.php');
		nxs_pagetemplate_handleheader();
		
		do_action('nxs_ext_betweenheadandcontent');
		
		nxs_pagetemplate_handlecontent_fraction("top");
	}
	else
	{
		// echo "template included :)";
	}
}


add_filter('get_footer', 'nxs_template_getfooter');
function nxs_template_getfooter($name)
{	
	global $nxs_global_row_render_statebag;
	if ($nxs_global_row_render_statebag != null)
	{
		// nothing to do here
		return;
	}
	
	if (true)
	{
		if (!defined('NXS_TEMPLATEINCLUDED'))
		{
			global $nxs_global_current_containerpostid_being_rendered;
			$containerpostid = $nxs_global_current_containerpostid_being_rendered;
			
			if (is_admin())
			{
				// this is the case with thrive architect
			}
			else if ($containerpostid === null)
			{
			}
			else
			{
				// if we reach this stage, it means some plugin used
				// the template_redirect 
				require_once(NXS_FRAMEWORKPATH . '/nexuscore/pagetemplates/blogentry/pagetemplate_blogentry.php');
				
				nxs_pagetemplate_handlecontent_fraction("bottom");
				
				nxs_pagetemplate_handlefooter();
			}
		}
		else
		{
			// echo "template included :)";
		}
	}
}

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
		}
	}
	
	//
	// debug / patch section
	//
	if (isset($_REQUEST["nxspatch"]))
	{
		if (true) // $_REQUEST["nxspatch"] == "menu")
		{
			require_once(NXS_FRAMEWORKPATH . '/nexuscore/patches/patches.php');
			nxs_applypatch($_REQUEST["nxspatch"], $args);
			echo "Applied upgrade patch... please refresh screen";
			die();
		}
	}
}

function nxs_load_plugins()
{
	// always load these
	$plugins = array
	(
		"nxs-businesssite",
		"nxs-pods-bridge",
	);

	// dynamically inject additional plugins 
	// based upon the configuration of the site
	if (nxs_hassitemeta())
	{
		$includeruntimeitems = false;
		$lookup = nxs_lookuptable_getlookup_v2($includeruntimeitems);
		
		$nxs_plugins = $lookup["nxs_plugins"];
		$nxs_plugins = str_replace(";", ",", $nxs_plugins);
		$nxs_plugins = str_replace(".", ",", $nxs_plugins);
		$nxs_plugins = str_replace("|", ",", $nxs_plugins);
		// 
		$moreplugins = explode(",", $nxs_plugins);
		
		$plugins = array_merge($plugins, $moreplugins);
	}
	
	$loaded = array();
	foreach ($plugins as $plugin)
	{
		// get rid of spaces before and after
		$plugin = trim($plugin);
		if ($plugin != "")
		{
			if (!in_array($plugin, $loaded))
			{
				//
				$path = NXS_FRAMEWORKPATH . "/plugins/{$plugin}/{$plugin}.php";
				if (file_exists($path))
				{
					require_once($path);
					$loaded[] = $path;
				}
				else
				{
					//echo "not found; $path";
					//die();
				}
			}
			else
			{
				// already had this one
				//echo "duplicate; $path";
				//die();
			}
		}

	}
}

function nxs_title_format($content) 
{
	return '%s';
}
add_filter('private_title_format', 'nxs_title_format');
add_filter('protected_title_format', 'nxs_title_format');

// ---

function nxs_ext_initialize_frontendframework()
{
	// initialization happens AFTER the pagetemplate rules are derived,
	// and when performing a webmethod (GUI editing)
	
	// only one time...
	if (defined('NXS_FRONTENDFRAMEWORK_INITIALIZED'))
	{
		return;
	}
	define('NXS_FRONTENDFRAMEWORK_INITIALIZED', true);

	//
	
	$frontendframework = nxs_frontendframework_getfrontendframework();
	$filetoinclude = NXS_FRAMEWORKPATH . "/nexuscore/frontendframeworks/{$frontendframework}/frontendframework_{$frontendframework}.php";
	if (file_exists($filetoinclude))
	{
		require_once($filetoinclude);
	}
	
	// invoke the init of the framework
	$functionnametoinvoke = "nxs_frontendframework_{$frontendframework}_init";
	$result = call_user_func_array($functionnametoinvoke, array($args));
}

// kudos to https://wordpress.org/support/topic/wpseoselect2locale-javascript-reference-error/
function yoast_bug_fix() {
    echo '<script>var wpseoSelect2Locale = wpseoSelect2Locale || "en";</script>';
}
add_action('admin_footer', 'yoast_bug_fix');

// each time a backend menu item is saved,
// we stored the globalid if the parent item, as well as the linked article
// in the postmeta of the item, so after an import of the theme the
// consistency check will be able to fix the relations
// see *3987394587394587
function nxs_wp_update_nav_menu_item($menu_id, $menu_item_db_id, $args)
{
	if (true)
	{
		$postid = $menu_item_db_id;
		
		$keys = array("menu-item-object-id", "menu-item-parent-id", "_menu_item_menu_item_parent", "_menu_item_object_id");
		foreach ($keys as $key)
		{
			$relatedpostid = get_post_meta($postid, $key, true);
			if ($relatedpostid != "")
			{
				$globalid = nxs_get_globalid($relatedpostid, true);
				// stored globalid in meta of this object
				add_post_meta($postid, "{$key}_globalid", $globalid, true);
			}
		}
	}
}
add_action('wp_update_nav_menu_item', 'nxs_wp_update_nav_menu_item', 10, 3);

function nxs_browser_ishuman()
{
	if( defined( 'WP_CLI' ) )
	{
		$result = false;
	}
	else if (nxs_browser_iscrawler())
	{
		$result = false;
	}
	else
	{
		$result = true;
	}
	return $result;
}

function nxs_browser_iscrawler() 
{
	$result = false;
	
  if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|Qwantify|qwant|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) 
  {
    $result = true;
  }
  
  return $result;
}

// ---

//
nxs_load_plugins();

do_action('nxs_framework_loaded');