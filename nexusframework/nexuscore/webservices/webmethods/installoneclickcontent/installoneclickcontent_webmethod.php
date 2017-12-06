<?php

// kudos to https://stackoverflow.com/questions/10353859/is-it-possible-to-programmatically-install-plugins-from-wordpress-theme
// If you should decide to use this approach, all I ask is that I'm credited with the initial overall script, 
// along with sorich87 for his activation process.
// credits: "sorich87" / "maiorano84"

function nxs_mm_get_plugins($plugins)
{
  $args = array
  (
    'path' => ABSPATH.'wp-content/plugins/',
    'preserve_zip' => false
  );

  foreach($plugins as $plugin)
  {
  	$shoulddownloadandinstall = true;
  	
  	$isalreadythere = false; // todo: determine if the plugin is already there or not
  	if ($isalreadythere)
  	{
  		$shoulddownloadandinstall = false;
  	}
  	
  	if ($shoulddownloadandinstall)
  	{
    	nxs_mm_plugin_download($plugin['path'], $args['path'].$plugin['name'].'.zip');
    	nxs_mm_plugin_unpack($args, $args['path'].$plugin['name'].'.zip');
    }
    
    nxs_mm_plugin_activate($plugin['install']);
  }
}

function nxs_mm_plugin_download($url, $path) 
{
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $data = curl_exec($ch);
  curl_close($ch);
  if(file_put_contents($path, $data))
  {
  	return true;
  }
  else
  {
  	return false;
  }
}

function nxs_mm_plugin_unpack($args, $target)
{
	if($zip = zip_open($target))
	{
	  while($entry = zip_read($zip))
	  {
	    $is_file = substr(zip_entry_name($entry), -1) == '/' ? false : true;
	    $file_path = $args['path'].zip_entry_name($entry);
	    if($is_file)
	    {
	      if(zip_entry_open($zip,$entry,"r")) 
	      {
	        $fstream = zip_entry_read($entry, zip_entry_filesize($entry));
	        file_put_contents($file_path, $fstream );
	        chmod($file_path, 0777);
	        //echo "save: ".$file_path."<br />";
	      }
	      zip_entry_close($entry);
	    }
	    else
	    {
	      if(zip_entry_name($entry))
	      {
	        mkdir($file_path);
	        chmod($file_path, 0777);
	        //echo "create: ".$file_path."<br />";
	      }
	    }
	  }
	  zip_close($zip);
	}
	if($args['preserve_zip'] === false)
	{
		unlink($target);   
	}
}

function nxs_mm_plugin_activate($installer)
{
  $current = get_option('active_plugins');
  $plugin = plugin_basename(trim($installer));

  if(!in_array($plugin, $current))
  {
    $current[] = $plugin;
    sort($current);
    do_action('activate_plugin', trim($plugin));
    update_option('active_plugins', $current);
    do_action('activate_'.trim($plugin));
    do_action('activated_plugin', trim($plugin));
    return true;
  }
  else
  {
  	return false;
  }
}

function nxs_webmethod_installoneclickcontent_enablev2()
{
	$result = true;
	/*
	$result = false;
	
	$thememeta =  nxs_theme_getmeta();
	if (function_exists("nxs_getcurrentstudio"))
	{
		$studio = nxs_getcurrentstudio();
		$siteid = nxs_getcurrentsiteid();
		
		if ($studio == "testgj" && $siteid == 20)
		{
			$result = true;
		}
	}
	*/
	
	return $result;
}

function nxs_webmethod_installoneclickcontent() 
{	
	extract($_REQUEST);

	// 

	$maxstep = 5;
	$currentstep = $_REQUEST["currentstep"];
	
	if ($currentstep == 0 || $currentstep == "")
	{
		$currentstep = 1;
	}

	nxs_ob_start();

	if ($currentstep == 1) 
	{
		$isok = true;
		echo "<h2>Checking dependencies</h2>";
		
		$ver = (float)phpversion();
		if ($ver < 5.6) 
		{
			error_log("theme; requires php 5.6 or above");
			echo "Error; this theme requires at least PHP 5.6 (found: $ver). Please contact your hosting provider and ask them to upgrade the PHP version.";
			$isok = false;
		}
		
		if(!function_exists("zip_open"))
		{
			error_log("function zip_open not found; install /enable/ the php-zip module to fix this problem");
			echo "Error; function zip_open not found. Unable to proceed. <a href='https://www.wpsupporthelp.com/answer/i-get-error-quot-function-zip-open-not-found-quot-php-zip-module-not-in-1513/'  target='_blank'>Click here to learn how to fix this</a>";
			$isok = false;
		}
		
		if ($isok)
		{		
			echo "<h2>Checking, installing and activating dependent plugins</h2>";
		}
	}
	else if ($currentstep == 2) 
	{
		if (nxs_webmethod_installoneclickcontent_enablev2())
		{
			$thememeta = nxs_theme_getmeta_v2();
			$plugins = $thememeta["activation"]["dependencies"]["plugins"];
			$pluginscount = count($plugins);
			
			if ($pluginscount > 0)
			{				
				echo "<h2>Installing $pluginscount plugin(s) :)</h2>";
				
				foreach ($plugins as $plugin)
				{
					$name = $plugin["name"];
					echo "<h2>Plugin $name ...</h2>";
					
					$seperatelist = array($plugin);
					nxs_mm_get_plugins($seperatelist);

					echo "<h2>Done</h2>";
				}
			}
			else
			{
				echo "<h2>No dependent plugins need to be installed - Done</h2>";
			}

		}
		
		echo "<span>Done</span>";
	}
	else if ($currentstep == 3) 
	{
		echo "<h2>" . nxs_l18n__("Started importing", "nxs_td") . "</h2>";
		
		// mark that 1clickcontent is passed, this will ensure site settings and site contents
		// is not overriden in upcoming theme activations
		$modifiedmetadata = array();
		$date = date('d-m-Y H:i:s');
		$modifiedmetadata['passed1clickcontent'] = "Passed on " . $date;	
		// explicitly marked false, as its 99% chance multiple sitesettings
		// are active at this stage (the current site has one, and the imported data
		// will have a sitesetting too (unless they imported will have ignored
		// it if it detects its the same).
		$performsanitycheck = false;
		nxs_mergesitemeta_internal($modifiedmetadata, $performsanitycheck);
	}
	else if ($currentstep == 4) 
	{
		require_once(NXS_FRAMEWORKPATH . '/nexuscore/importers/oneclick/nxsimporter.php');
		$importer = new NXS_importer();
	
		// inner stacked ob_start		
		nxs_ob_start();
		$importer->dispatch();
		$importoutput = nxs_ob_get_contents();
		nxs_ob_end_clean();
		
 		echo "<span title='More information in the HTML DOM'>[...]</span>";
		
		if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
		{
  		echo "<!-- ";
  	}
  	echo $importoutput;
		if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
		{
  		echo " -->";
  	}
	}
	else if ($currentstep == 5) 
	{
		echo "<h2>" . nxs_l18n__("Finished importing", "nxs_td") . "</h2>";
		
		// todo: this should be moved till the phase AFTER the sanitization has finished
		if (nxs_webmethod_installoneclickcontent_enablev2())
		{
			// PHRASE; options
			if (true)
			{
				// update options as specified by the options.json file
				$filelocation = TEMPLATEPATH . "/" . "resources" . "/" . "options.json";
				if (file_exists($filelocation))
				{					
					$json = file_get_contents($filelocation);
					$decoded = json_decode($json, true);
					$options = $decoded["options"];
					$optioncount = count($options);
					
					echo "<h2>Options import - Found $optioncount option(s)</h2>";
					
					foreach ($options as $optiondata)
					{
						$option = $optiondata["option"];
						$type = $optiondata["type"];
						$optionvalue = "";
						
						if ($type == "namevalue_valuebyglobalidlookup")
						{
							//
							$globalid = $optiondata["globalid"];
							
							$postids = nxs_get_postidsaccordingtoglobalid($globalid);
							if (count($postids) == 0)
							{
								if ($globalid == "NXS-NULL")
								{
									// ok, makes sense
								}
								else
								{
									echo "Warning; no postid found with globalid $globalid";
								}
								$optionvalue = "";	// meaning not set
							}
							else if (count($postids) == 1)
							{
								$optionvalue = $postids[0];
							}
							else
							{
								echo "Error; multiple postids match $globalid";
								$optionvalue = $postids[0];
							}
						}
						else
						{
							// errr
							error_log("installoneclickcontent; options; unexpected type; $type");
						}
							
						error_log("installoneclickcontent; options; debug; updated $option to $optionvalue");
						update_option($option, $optionvalue);
						
						echo " (option $option -> $postid) ... ";
					}
					
					error_log("installoneclickcontent; options; debug; done");
						
				}
				else
				{
					echo "<h2>Options import - none</h2>";
				}
			}
			
			// PHASE; reset existing widget areas
			if (true)
			{
				error_log("debug; emptying widget areas start");
				nxs_widgets_emptyallwidgetareas();
				error_log("debug; emptying widget areas end");
			}
			
			//  PHASE; wie (import widgets)
			if (true)
			{
				$file = "includes/widgets.php";
				$path = trailingslashit( WIE_PATH ) . $file;
				require_once($path);
				
				$file = "includes/import.php";
				$path = trailingslashit( WIE_PATH ) . $file;
				require_once($path);
				
				$filelocation = TEMPLATEPATH . "/" . "resources" . "/" . "widgets.wie";
				if (file_exists($filelocation))
				{
					echo "<h3>widgets import wie - started</h3>";
					// todo: get this from a file in the theme's resources folder (if it exists)
					$data = file_get_contents($filelocation);
					$d = json_decode($data);
					wie_import_data($d);
					echo "<h3>widgets import wie - done</h3>";
				}
				else
				{
					echo "<h3>widgets import wie - none</h3>";
				}
			}
		}
			
		
	}
	else 
	{
		echo "<h2>unexpected step: $currentstep ?</h2>";
	}
	
	$log = nxs_ob_get_contents();
	nxs_ob_end_clean();

	//	
	$currentstep = $currentstep + 1;
	
	$responseargs = array();
	$responseargs["log"] = $log;
	$responseargs["nextstep"] = $currentstep;
	$responseargs["maxstep"] = $maxstep;
	
	nxs_webmethod_return_ok($responseargs);
}
?>