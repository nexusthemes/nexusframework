<?php
function nxs_sm_processstate_dataverification_impl($currentstate)
{
	require_once(NXS_FRAMEWORKPATH . '/nexuscore/dataconsistency/dataconsistency.php');
	global $nxs_gl_cache_sitemeta;
	$nxs_gl_cache_sitemeta = null;

	$task = $currentstate["task"];
	$stepintask = $currentstate["stepintask"];

	$chunkedsteps = array();
	$chunkedsteps["task"] = $task;
	
	if ($task == "0")
	{
		$task = 1;	// todo: terugzetten naar 1
	}
	
	if ($stepintask == "0")
	{
		$stepintask = 1;
	}
	
	$result["log"] = "<!--task: {$task}, step: {$stepintask}-->";


	//ob_start();

	// handle steps
	
	if ($task == 1)
	{
		// initialization
		$result["log"] = "<h2>".nxs_l18n__("Initialization", "nxs_td")."</h2>";
  	
  	$task = 2;
	$stepintask = 1;
	}
	else if ($task == 2)
	{
		// // data consistency check step 2 - ensure each postid has max 1 globalid
		$result["log"] = "<h2>".nxs_l18n__("Verifying data - phase 1/4", "nxs_td")."</h2>";
		$task = 3;
		$stepintask = 1;
	}
	else if ($task == 3)
	{

		global $wpdb;
		
		$q = "
				select post_id postid, meta_id metaid, meta_value globalid from $wpdb->postmeta postmeta
				where 
					postmeta.meta_key = 'nxs_globalid' and
					postmeta.post_id in
					(
						select post_id from $wpdb->postmeta where meta_key = 'nxs_globalid' group by post_id having count(1) > 1
					)
				order by
					post_id, meta_id
		";

		$dbresult = $wpdb->get_results($q, ARRAY_A );
		
		if (count($dbresult) > 0)
		{
			// er zijn dubbele globalids gevonden die behoren bij 1 postid; inconsistentie!
			
			$postid = "";
	  	foreach ($dbresult as $dbrow)
	  	{
	  		$currentpostid = $dbrow["postid"];
	  		
	  		if ($currentpostid != $postid)
	  		{
	  			// set postid; we will keep this record; this is the meta row with the lowest meta_id; we will
	  			// assume this is the one we should keep
	  			$postid = $currentpostid;
	  			continue;
	  		}
	  		else
	  		{
	  			$currentmetaid = $dbrow["metaid"];
				// this is the 2nd, 3rd or further record for the same postid,
	  			// we will remove it (postid's should always max 1 globalid!)
	  			//echo "about to delete meta entry, with metaid:" . $currentmetaid . "<br />";
	  			// in theorie zou het kunnen zijn dat er verwijzingen bestaan naar deze globalid; die verwijzing raken we dus kwijt..
	  			
	  			$q = "
							delete from $wpdb->postmeta where meta_id = %s
					";
			
					$deletedbresult = $wpdb->get_results( $wpdb->prepare($q, $currentmetaid), ARRAY_A );
					if ($deletedbresult === false)
					{
						echo "verwijderen van duplicaat globalid mislukt?<br />";
					}
	  		}
	  	}
		}
		else
		{
			if ($task == "*")
			{
				echo "<p>OK [each postids has max 1 globalid]</p>";
			}
		}
		$task = 4;
		$stepintask = 1;
	}
	else if ($task == 4)
	{
		// step 2 - ensure each globalid is used multiple times
		$result["log"] = "<h2>".nxs_l18n__("Verifying data - phase 2/4", "nxs_td")."</h2>";
		$task = 5;
		$stepintask = 1;
	}
	else if ($task == 5)
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
		
		if (count($dbresult) > 0)
		{
			// er zijn globalids gevonden die gedeeld worden over meerdere postid's; inconsistentie!
			// we resetten de global ids van de nieuwste post's, de oudste (met de laagste postid) is leidend!
			
			$globalid = "";
			
	  	foreach ($dbresult as $dbrow)
	  	{
	  		$currentglobalid = $dbrow["globalid"];
	  		
	  		if ($currentglobalid != $globalid)
	  		{
	  			// set postid; we will keep this record; this is the meta row with the highest postid; we will
	  			// assume this is the one we should keep (higher postid means added later to the system)
	  			$globalid = $currentglobalid;
	  			continue;
	  		}
	  		else
	  		{
		  		$currentpostid = $dbrow["postid"];

	  			// resetten globalid voor deze postid
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
		else
		{
			if ($task == "*")
			{
				echo "<p>OK [globalids are unique; no duplicates found]</p>";
			}
		}
		$task = 6;
		$stepintask = 1;
	}
	else if ($task == 6)
	{
		// step 3 - ensure widget meta is consistent
		$result["log"] = "<h2>".nxs_l18n__("Verifying data - phase 3/4", "nxs_td")."</h2>";
		$task = 7;
		$stepintask = 1;
	}
	else if ($task == 7)
	{
		global $wpdb;

		// we do so for truly EACH post (not just post, pages, but also for entities created by third parties,
		// as these can use the nxsstructure too (for example WooCommerce 'product'). This saves development
		// time for plugins, and increases consistency of data for end-users
		$q = "
				select ID postid
				from $wpdb->posts
			";
			
		$dbresult = $wpdb->get_results($q, ARRAY_A );
		
		//echo "komt ie:";
		//nxs_dataconsistency_sanitize_postwidgetmetadata(393);
		
		if (count($dbresult) > 0)
		{
			$cnt = 0;
	  	foreach ($dbresult as $dbrow)
	  	{	  		
	  		$cnt++;
	  		$postid = $dbrow["postid"];
	  		nxs_dataconsistency_sanitize_postwidgetmetadata($postid);
			}
		}
		
		$task = 8;
		$stepintask = 1;
	}
	else if ($task == 8)
	{
		// step 4 - ensure post meta is consistent
		$result["log"] = "<h2>".nxs_l18n__("Verifying data - phase 4/4", "nxs_td")."</h2>";
		$task = 9;
		$stepintask = 1;
	}
	else if ($task == 9)
	{
		global $wpdb;
		
		$pagesize = 300;
		$page = $stepintask;
		if ($page == "")
		{
			$page = 0;
		}
		
		// we do so for truly EACH post (not just post, pages, but also for entities created by third parties,
		// as these can use the pagetemplate concept too. This saves development
		// time for plugins, and increases consistency of data for end-users
		$q = "
				select ID postid
				from $wpdb->posts
			";
			
		$dbresult = $wpdb->get_results($q, ARRAY_A );		
		$totalsize = count($dbresult);
		$totalpages = ceil((float)$totalsize / (float)$pagesize);
		
		$startoffset = $page * $pagesize;	// 0, 100, 200...
		$upanduntilmaxoffset = ($page + 1) * $pagesize - 1;	// 99, 199, 299 ...
		$postindex = -1;
		
		//echo " Page $page is from $startoffset up and until $upanduntilmaxoffset ";
		
  	foreach ($dbresult as $dbrow)
  	{
  		$postindex++;
  		
  		if ($postindex < $startoffset)
  		{
  			// skip it, was already processed in previous batch
  			continue;
  		}
  		else if ($postindex > $upanduntilmaxoffset)
  		{
  			// past the end of this batch
  			break;
  		}
  		
  		$postid = $dbrow["postid"];
  		nxs_dataconsistency_sanitize_postmetadata($postid);
  	}
		
		if ($totalsize > $upanduntilmaxoffset)
		{
			// stick to the existing step, and proceed with the next page
			$task = 9;
			$stepintask++;
			$fraction = $page + 1;
			$allfractions = $totalpages;
			$result["log"] = "<h2>".nxs_l18n__("... Fraction $fraction / $allfractions ...", "nxs_td")."</h2>";
			
		}
		else
		{
			// this step is totally finished
			$task = 10;
			$stepintask = 1;
		}
	}
	else if ($task == 10)
	{
		// stage  2; sanitize special metadata of the site
		
		// unicontent data
		nxs_dataconsistency_sanitize_unicontentdata();
		
		//echo "SUBPART 2 FINISHED";
		$task = 11;
		$stepintask = 1;
	}
	else if($task == 11)
	{
		$result["log"] = "<h2>".nxs_l18n__("Cleaning up cache", "nxs_td")."</h2>";
		$task = 12;
		$stepintask = 1;
	}
	else if ($task == 12)
	{
		$args = array();
		$args["post_status"] = "publish";
		$args["post_type"] = "nxs_list";
		$args["orderby"] = "post_date";//$order_by;
		$args["order"] = "DESC"; //$order;
		$args["numberposts"] = -1;	// allemaal!
	  $posts = get_posts($args);
		
    // loop over available pages
    foreach ($posts as $currentpost)
    {
	    $postid = $currentpost->ID;
	    //echo "List <a href='" . nxs_geturl_for_postid($postid) . "'>" . $postid . "</a><br />";
			// get postids
			nxs_after_postcontents_updated($postid);
		}
		$task = 13;
		$stepintask = 1;
	}
	else if ($task == 13)
	{
		// retouch homepage
		nxs_wp_retouchhomepage();
		$result["log"] = "<h2>".nxs_l18n__("Configured homepage", "nxs_td")."</h2>";
		$task = 14;
		$stepintask = 1;
	}
	else if ($task == 14)
	{
		$result["log"] = "<h2>".nxs_l18n__("Data verification completed", "nxs_td")."</h2>";
		
		// this was the last step
		nxs_set_dataconsistencyvalidationnolongerrequired();
		$task = 15;
		$stepintask = 1;
	}
	else if ($task == 15) // end of tasks, state machine is finished
	{
		$result["nextstate"] = "finished";
		nxs_webmethod_return_ok($result);	
	}
	else
	{
		nxs_webmethod_return_nack("unsupported task ({$task})");	
	}

	$result["nextstate"] = array(
		"task"=>$task, 
		"stepintask"=> $stepintask
	);
	
	// import phase; the global cache should be wiped when we reach this point,
	// as the sitemeta might have been update	(for example the unistyle settings)
	global $nxs_gl_cache_sitemeta;
	$nxs_gl_cache_sitemeta = null;
	
	return $result;
}

function nxs_data_verification()
{
	?>
	<div class='nxs-width100 nxs-align-center nxs-margin-top40'>
		<h1><?php echo nxs_l18n__("Please be patient", "nxs_td"); ?></h1>
	</div>
      
  <div class='nxs-clear nxs-padding-top20'></div>
      
	<div class='nxs-width100 nxs-align-center'>
		<div style='width: 600px; margin: 0 auto; border: 1px; background-color: #EEE; border-color: #DDD; border-style: solid; border-width: 3px; padding: 5px;' class="nxs-gray nxs-border-radius5">
			<p>
				<div id='nxsprocessingwrapper' style='height: 300px; overflow-y:scroll;'>
					<div id='nxsprocessingindicator'></div>
					<span id='nxsprocessingspacer'></span><span id='nxsprocessingspacer2'>...</span><img id='nxsspinner' style='padding-left: 10px;' src='<?php echo $imageurl; ?>' />
				</div>
			</p>
		</div>
	</div>
	<div id='waitwrap' style='display:none;'>
		<form method="get">
			<div class='nxs-width100 nxs-align-center'>
				<h1><?php echo nxs_l18n__("One moment ...", "nxs_td"); ?></h1>
			</div>			
		</form>
	</div>
      
  <div class='nxs-clear nxs-padding-top20'></div>
      
	<div id='finishedwrap' style='display:none;'>
		<?php
		$url = admin_url('admin.php?page=nxs_backend_overview&trigger=finished');
		?>
		<div class='nxs-width100 nxs-align-center'>
			<a href='<?php echo $url; ?>' class='nxs-big-button nxs-green nxs-border-radius5'><?php echo nxs_l18n__("Finish", "nxs_td"); ?></a>
		</div>			
	</div>
	
	<div id='errorwrap' style='display:none;'>
		<?php
		$url = nxs_geturl_home();
		?>
		<div class='nxs-width100 nxs-align-center'>
			<a href='<?php echo $url; ?>' class='nxs-big-button nxs-green nxs-border-radius5'><?php echo nxs_l18n__("Continu anyways", "nxs_td"); ?></a>
		</div>			
	</div>
	<?php
	?>
	<script>
		jQuery(window).load
		(
			function() 
			{ 
				nxs_sm_statemachineid = "dataverification";
				nxs_sm_timerid = setInterval(nxs_js_sm_processsmstate, 1000);
			}
		);
	</script>
	<?php
	
	die();
}
?>