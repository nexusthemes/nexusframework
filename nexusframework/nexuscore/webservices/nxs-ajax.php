<?php
	
	function nxs_ensureloadpathisset_v2()
	{
		if ( !defined('WP_LOAD_PATH') ) 
		{
			/** classic root path if wp-content and plugins is below wp-config.php */
			$classic_root = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))) . '/' ;
			if (file_exists( $classic_root . 'wp-load.php') )
			{
				define( 'WP_LOAD_PATH', $classic_root);
			}
			else
			{
				/** some rare scenario is that its up one folder up additionally; \wp-content\themes\yogainstructor\yogainstructor\ */
				$classic_root = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))))) . '/' ;
				if (file_exists( $classic_root . 'wp-load.php') )
				{
					define( 'WP_LOAD_PATH', $classic_root);
				}
				else
				{
					// for shared framework environments its 2 folders less up ....
					$classic_root_shared = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/' ;
					if (file_exists( $classic_root_shared . 'wp-load.php') )
					{
						define( 'WP_LOAD_PATH', $classic_root_shared);
					}
					else
					{
						if (file_exists( $path . 'wp-load.php') )
						{
							define( 'WP_LOAD_PATH', $path);
						}
						else
						{
							exit("Could not find wp-load.php ($classic_root) ($path), see nxs-ajax.php");
						}
					}
				}
			}		
		}
		else
		{
			//		
		}
	}

	// we explicitly choose to use this approach in favor of the traditional 'WP' way,
	// since plugins often behave different when DOING_AJAX is set, for example
	// the NextGen plugin loads less files. Impact is that these plugins won't render
	// correctly when DOING_AJAX is set.
	// this AJAX implementation does not set DOING_AJAX, solving our problems
	
	// credits to NextGen Gallery ngg-config.php
	
	/** Define the server path to the file wp-config here, if you placed WP-CONTENT outside the classic file structure */
	$path  = ''; // It should be end with a trailing slash    
	/** That's all, stop editing from here **/
	
	nxs_ensureloadpathisset_v2();
	
	// let's load WordPress
	require_once(WP_LOAD_PATH . 'wp-load.php');
	require_once(WP_LOAD_PATH . 'wp-admin/includes/admin.php');

	//send_nosniff_header();
	
	nxs_ajax_webmethods();
	
	die();
?>