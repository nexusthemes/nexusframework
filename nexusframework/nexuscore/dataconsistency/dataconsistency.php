<?php

function nxs_ensuredataconsistency($scope)
{
	$chunkedsteps == "";
	$triesleft = 1000;
	$log = "";
	while ($chunkedsteps != "finished" && $triesleft > 0)
	{
		$result = nxs_ensuredataconsistency_chunked($chunkedsteps);
		
		$chunkedsteps = $result["nextchunkedsteps"];
		$log .= $result["log"];
		$triesleft--;
	}
	
	$report = $result["report"];
	return $report;
}

function nxs_ensuredataconsistency_chunked($chunkedsteps)
{
	// import phase; the global cache should be wiped when we reach this point,
	// as the sitemeta might have been update	(for example the unistyle settings)
	global $nxs_gl_cache_sitemeta;
	$nxs_gl_cache_sitemeta = null;

	$nextchunkedsteps = "";

	if ($chunkedsteps == "" || $chunkedsteps == 0)
	{
		$scope = 1;
	}
	else
	{
		$scope = $chunkedsteps["scope"];
	}
	
	nxs_ob_start();

	// handle scopes
	
	if ($scope == "1")
	{
		// initialization
  	?><h2><?php echo nxs_l18n__("Initialization", "nxs_td"); ?></h2><?php
  	
  	$nextchunkedsteps = array("scope"=>"2");
	}
	else if ($scope == "2")
	{
		// // data consistency check step 2 - ensure each postid has max 1 globalid
		?><h2><?php echo nxs_l18n__("Verifying data - phase 1/4", "nxs_td"); ?></h2><?php
		$nextchunkedsteps = array("scope"=>"3");
	}
	else if ($scope == "3")
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
			
			// retrieve (if it exists) the activesitesettings postid
			$postids = nxs_get_postidsaccordingtoglobalid("activesitesettings");
			$activesitesettingsid = $postids[0];
			
			$postid = "";
	  	foreach ($dbresult as $dbrow)
	  	{
	  		$currentpostid = $dbrow["postid"];
	  		
	  		if ($currentpostid == $activesitesettingsid)
	  		{
	  			$currentglobalid = $dbrow["globalid"];
	  			if ($currentglobalid == "activesitesettings")
	  			{
	  				// keep it!
	  				echo "keeping $currentglobalid for $currentpostid";
	  			}
	  			else
	  			{
	  				// delete it!
	  				$currentmetaid = $dbrow["metaid"];
	  				
	  				$q = "
								delete from $wpdb->postmeta where meta_id = %s
						";
						
						echo "deleting duplicate globalid";
				
						$deletedbresult = $wpdb->get_results( $wpdb->prepare($q, $currentmetaid), ARRAY_A );
						if ($deletedbresult === false)
						{
							echo "verwijderen van duplicaat globalid mislukt?<br />";
						}
	  			}
	  		}
	  		else
	  		{
		  		if ($currentpostid != $postid)
		  		{
		  			// set postid; we will keep this record; this is the meta row with the lowest meta_id; we will
		  			// assume this is the one we should keep
		  			$postid = $currentpostid;
		  			continue;
		  		}
		  		else
		  		{
		  			$currentglobalid = $dbrow["globalid"];
		  			$currentmetaid = $dbrow["metaid"];
		  			
		  			// this is the 2nd, 3rd or further record for the same postid,
		  			// we will remove it (postid's should always max 1 globalid!)
		  			// echo "about to delete meta entry, with metaid:" . $currentmetaid . "<br />";
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
		}
		else
		{
			if ($scope == "*")
			{
				echo "<p>OK [each postids has max 1 globalid]</p>";
			}
		}
		$nextchunkedsteps = array("scope"=>"4");

	}
	else if ($scope == "4")
	{
		// step 2 - ensure each globalid is used multiple times
		?><h2><?php echo nxs_l18n__("Verifying data - phase 2/4", "nxs_td"); ?></h2><?php
		
		
		$nextchunkedsteps = array("scope"=>"5");
	}
	else if ($scope == "5")
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
	  			echo "<span title='More information in the HTML DOM'>[...]</span>";
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
			if ($scope == "*")
			{
				echo "<p>OK [globalids are unique; no duplicates found]</p>";
			}
		}
		$nextchunkedsteps = array("scope"=>"6");
	}
	else if ($scope == "6")
	{
		// step 3 - ensure widget meta is consistent
		?><h2><?php echo nxs_l18n__("Verifying data - phase 3/4", "nxs_td"); ?></h2><?php
		$nextchunkedsteps = array("scope"=>"7");
	}
	else if ($scope == "7")
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
		
		$nextchunkedsteps = array("scope"=>"8");
	}
	else if ($scope == "8")
	{
		// step 4 - ensure post meta is consistent
		?><h2><?php echo nxs_l18n__("Verifying data - phase 4/4", "nxs_td"); ?></h2><?php
		$nextchunkedsteps = array("scope"=>"9");
	}
	else if ($scope == "9")
	{
		global $wpdb;
		
		$pagesize = 300;
		$page = $chunkedsteps["page"];
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
			$nextchunkedsteps = array("scope"=>"9", "page"=>$page + 1);
			$fraction = $page + 1;
			$allfractions = $totalpages;
			?><h2><?php echo nxs_l18n__("... Fraction $fraction / $allfractions ...", "nxs_td"); ?></h2><?php
		}
		else
		{
			// this step is totally finished
			$nextchunkedsteps = array("scope"=>"9b");
		}
	}
	else if ($scope == "9b")
	{
		// stage  2; sanitize special metadata of the site
		
		// unicontent data
		nxs_dataconsistency_sanitize_unicontentdata();
		
		//echo "SUBPART 2 FINISHED";
		$nextchunkedsteps = array("scope"=>"10");
	}
	else if($scope == "10")
	{
		?><h2><?php echo nxs_l18n__("Cleaning up cache", "nxs_td"); ?></h2><?php
		$nextchunkedsteps = array("scope"=>"11");
	}
	else if ($scope == "11")
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
		$nextchunkedsteps = array("scope"=>"12");
	}
	else if ($scope == "12")
	{
		// retouch homepage
		nxs_wp_retouchhomepage();
		?><h2><?php echo nxs_l18n__("Configured homepage", "nxs_td"); ?></h2><?php
		$nextchunkedsteps = array("scope"=>"13");
	}
	else if ($scope == "13")
	{
		// error_log("step 13 invoked; nxs_wp_resetrewriterules");
		// resetting permalinks
		nxs_wp_resetrewriterules();
		?><h2><?php echo nxs_l18n__("Resetting rewrite rules", "nxs_td"); ?></h2><?php
		$nextchunkedsteps = array("scope"=>"14");
	}
	else if ($scope == "14")
	{
		?><h2><?php echo nxs_l18n__("Done", "nxs_td"); ?></h2><?php

		// this was the last step
		nxs_set_dataconsistencyvalidationnolongerrequired();
		$nextchunkedsteps = "finished";
	}

	$log = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// import phase; the global cache should be wiped when we reach this point,
	// as the sitemeta might have been update	(for example the unistyle settings)
	global $nxs_gl_cache_sitemeta;
	$nxs_gl_cache_sitemeta = null;
	
	if ($nextchunkedsteps == "")
	{
		nxs_webmethod_return_nack("nextchunkedsteps is not set");
	}
	
	$result = array();
	$result["log"] = $log;
	$result["nextchunkedsteps"] = $nextchunkedsteps;
	return $result;
}

function nxs_getdatarequiringmodificationforglobalidfix($metadata)
{
	if (is_array($metadata))
	{
		// ok, will be processed
	}
	else if ($metadata == "")
	{
		// ok, nothing to change
		return;
	}
	else if ($metadata == "index.php")
	{
		// ok, nothing to change
		return;
	}
	else
	{
		//echo "no array was supplied?";
		var_dump($metadata);
		die();
	}
	
	$metakeyvaluestoupdate = array();
	
	// metadata is for example widgetmetadata, or pagemetadata
	foreach ($metadata as $metakey => $valueformetakey)
	{
		if (nxs_stringendswith($metakey, "_globalid"))
		{
			//
			// we gaan er hierbij vanuit dat de metakey XYZ_globalid verwijst naar de metakey XYZ (_globalid is dus een suffix)
			// in onderstaande stuk noemen we XYZ_globalid de $globalkey en noemen we XYZ de $localkey.
			//
			$globalkey = $metakey;
			$globalid_that_is_correct = $valueformetakey;
			
			if ($globalid_that_is_correct == "")
			{
				// looks like bogus data
				// perhaps we should do something here...
				continue;
			}
			
			// we found a global key
			$globalkeypieces = explode("_", $globalkey);
			if (count($globalkeypieces) == 2)
			{
				$localkey = $globalkeypieces[0];
				if (!array_key_exists($localkey, $metadata))
				{
					if ($metadata[$globalkey] == "NXS-NULL")
					{
						// ok
					}
					else
					{
						// unable to locate localkey for globalkey
						// blijkbaar is er een veld XYZ_globalXYZ, zonder een overeenkomstig XYZ meta veld?!
						echo "No original key found for global key ($localkey) ?! this is not supported (aa)<br />";
						echo "Value global key: [" . $metadata[$globalkey] . "]<br />";
						die();
					}
				}
			}
			else if (count($globalkeypieces) == 3)
			{
				$localkey = $globalkeypieces[0] . "_" . $globalkeypieces[1];
				if (!array_key_exists($localkey, $metadata))
				{
					if ($metadata[$globalkey] == "NXS-NULL")
					{
						// ok
					}
					else
					{
						// unable to locate localkey for globalkey
						// blijkbaar is er een veld XYZ_globalXYZ, zonder een overeenkomstig XYZ meta veld?!
						echo "No original key found for global key ($localkey) ?! this is not supported (bb)<br />";
						echo "Value global key: [" . $metadata[$globalkey] . "]<br />";
						die();
					}
				}
			}
			else if (count($globalkeypieces) == 4)
			{
				$localkey = $globalkeypieces[0] . "_" . $globalkeypieces[1] . "_" . $globalkeypieces[2];
				if (!array_key_exists($localkey, $metadata))
				{
					if ($metadata[$globalkey] == "NXS-NULL")
					{
						// ok
					}
					else
					{
						// unable to locate localkey for globalkey
						// blijkbaar is er een veld XYZ_globalXYZ, zonder een overeenkomstig XYZ meta veld?!
						echo "No original key found for global key ($localkey) ?! this is not supported (cc)<br />";
						echo "Value global key: [" . $metadata[$globalkey] . "]<br />";
						die();
					}
				}
			}
			else if (count($globalkeypieces) == 5)
			{
				$localkey = $globalkeypieces[0] . "_" . $globalkeypieces[1] . "_" . $globalkeypieces[2] . "_" . $globalkeypieces[3];
				if (!array_key_exists($localkey, $metadata))
				{
					if ($metadata[$globalkey] == "NXS-NULL")
					{
						// ok
					}
					else
					{
						// unable to locate localkey for globalkey
						// blijkbaar is er een veld XYZ_globalXYZ, zonder een overeenkomstig XYZ meta veld?!
						echo "No original key found for global key ($localkey) ?! this is not supported (dd)<br />";
						echo "Value global key: [" . $metadata[$globalkey] . "]<br />";
						die();
					}
				}
			}
			else
			{
				echo "This key '$globalkey' contains multiple underscores; this is not supported<br />";
				die();
			}
			
			$currentlypointstopostid = $metadata[$localkey];			
			$globalid_of_post_to_which_current_pointerrefers = nxs_get_globalid($currentlypointstopostid, false);	// very important, the 2nd parameter is set to FALSE!!)


			
			//echo "[found local key:" . $localkey . " having value:" . $currentlypointstopostid . "]<br />";						
			//echo "[found global key:" . $globalkey . " having value:" . $globalid_that_is_correct . "]<br />";											
			//echo "[lookup postid:" . $currentlypointstopostid . " having globalid:" . $globalid_of_post_to_which_current_pointerrefers . "]<br />";
			
			// de originele key verwijst naar een bepaalde origpostid
	
			if ($globalid_of_post_to_which_current_pointerrefers == $globalid_that_is_correct)
			{
				// echo "[widget field is ok]<br />";
				
				// OK, both the localkey and globalkey point to the save postid; this property is consistent
			}
			else
			{
				// er is een inconsistentie; de postid en globalid verwijzen verschillend
				
				if ($globalid_that_is_correct == "NXS-NULL")
				{
					// xxxxxxxx
					
					// de metadata verwijst naar een globalid die niet (meer?) bestaat,
					// de oplossing is om te kijken of de verwezen postid wel bestaat en dan die als waarheid aan te nemen
					$correct_postid_to_which_we_should_refer = $currentlypointstopostid;
					$correct_globalid = nxs_get_globalid($correct_postid_to_which_we_should_refer, true);
					$metakeyvaluestoupdate[$globalkey] = $correct_globalid;
					
					if ($correct_globalid == "NXS-NULL")
					{
						// als blijkt dat de postid in zijn geheel niet bestaat, maken we ook die leeg
						$metakeyvaluestoupdate[$localkey] = 0;
					}
				}
				else if (nxs_global_globalidexists($globalid_that_is_correct))
				{
					// als de globalid voorkomt, gaan we er vanuit dat de postid onjuist is					
					
					//echo "[widget field $localkey is NOT ok, localvalue: " . $metadata[$localkey] . "]<br />";
		
					$postid_to_which_we_should_refer = nxs_get_postidsaccordingtoglobalid($globalid_that_is_correct);
					if (count($postid_to_which_we_should_refer) == 1)
					{
						echo "<span title='More information in the HTML DOM'>[...]</span>";
						
						// 
						if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
						{
			    		echo "<!-- ";
			    	}
						echo "[data integrity inconsistency]<br />";
						echo "[field needs to be updated; it points to " . $currentlypointstopostid . " while (according to globalid) it should point to " . $postid_to_which_we_should_refer[0] . "]<br />";
						
						if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
						{
			    		echo " -->";
			    	}
						
						// indien de postid lookup niet overeenkomt met de (reverse) postid die geldig is volgens de globals,
						// dan kunnen we hier een update uitvoeren
						$metakeyvaluestoupdate[$localkey] = $postid_to_which_we_should_refer[0];
					}
					else if (count($postidsaccordingtoglobalid) == 0)
					{
						if ($currentlypointstopostid > 0)
						{
							echo "<span title='More information in the HTML DOM'>[...]</span>";
						
							// 
							if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
							{
				    		echo "<!-- ";
				    	}
							
							// global id not found; it appears post information is missing
							echo "[data integrity inconsistency]<br />[field points to globalid (post information) that is not found; maybe you forgot to import some data? field will be ignored/not be updated]<br />";
							// update to post local postid to 0 to ensure no incorrect things are being used
							$metakeyvaluestoupdate[$localkey] = 0;
							
							if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
							{
				    		echo " -->";
				    	}
						}
						else
						{
							//echo "[inconsistency remark absorbed; postid points to 0, globalid is not found]<br />";
						}
					}
					else 
					{
						// dit had bij de eerste stap van de consistentheid al gefixed moeten zijn?
						// multiple globalids found... not sure what to do now; 
						echo "[data integrity inconsistency]<br />[sanity check failed; globalid is used multiple times, unable to update field as its uncertain to which globalid the field should be updated to]<br />";
						// we zouden er hierbij voor kunnen kiezen om te verwijzen naar de eerste, of oudste, of andere logica
						// het beste is echter om de gebruiker een keuze te laten maken aangezien de kans zeer aanwezig is dat het systeem een verkeerde keuze zou maken.
					}
				}
				else
				{
					// xxxxxxxx
					
					// de metadata verwijst naar een globalid die niet (meer?) bestaat,
					// de oplossing is om te kijken of de verwezen postid wel bestaat en dan die als waarheid aan te nemen
					$correct_postid_to_which_we_should_refer = $currentlypointstopostid;
					$correct_globalid = nxs_get_globalid($correct_postid_to_which_we_should_refer, true);
					$metakeyvaluestoupdate[$globalkey] = $correct_globalid;
					
					if ($correct_globalid == "NXS-NULL")
					{
						// als blijkt dat de postid in zijn geheel niet bestaat, maken we ook die leeg
						$metakeyvaluestoupdate[$localkey] = 0;
					}
				}
			}
		}
		else if (nxs_stringendswith($metakey, "_globalslug"))
		{
			// was used by menu's; no longer applicable ...
			//echo "[warning; found deprecated menu reference]";
		}
		else if (nxs_stringendswith($metakey, "_catglobalids"))
		{
			//
			// we gaan er hierbij vanuit dat de metakey XYZ_catglobalids verwijst naar de metakey XYZ (_catglobalids is dus een suffix)
			// in onderstaande stuk noemen we XYZ_catglobalids de $globalkey en noemen we XYZ de $localkey.
			//
			
			$globalkey = $metakey;
			$globalcatids_that_are_correct = $valueformetakey;
			
			// we found a global key
			$globalkeypieces = explode("_", $globalkey);
			if (count($globalkeypieces) == 2)
			{
				// when the property name in the (widget) has no underscores, it will look like "propertyname_catglobalids",
				// the explode will result in 2 pieces, the first one being the "propertyname" (=localkey)
				$localkey = $globalkeypieces[0];
				if (!array_key_exists($localkey, $metadata))
				{
					// unable to locate localkey for globalkey, apparently the post contains a XYZ_catglobalids property,
					// but no corresponding XYZ property. likely this is caused by using an old(er) version of the framework.
					echo "<span title='More information in the HTML DOM'>[...]</span>";
					if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
					{
		    		echo "<!-- ";
		    	}
					echo "No original key found for global key ($localkey) ?! [a]";
					if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
					{
		    		echo " -->";
		    	}
				}
				$currentlypointsto = $metadata[$localkey];
			}
			else if (count($globalkeypieces) == 3)
			{
				// when the property name in the (widget) has one underscore (for example property_name), 
				// it will look like "property_name_catglobalids", the explode will result in 3 pieces, 
				// the localkey in that case is the concatenation of the first 2 pieces "property_name" (=localkey)
				$localkey = $globalkeypieces[0] . "_" . $globalkeypieces[1];
				if (!array_key_exists($localkey, $metadata))
				{
					// unable to locate localkey for globalkey
					// blijkbaar is er een veld XYZ_catglobalids, zonder een overeenkomstig XYZ meta veld?!
					echo "<span title='More information in the HTML DOM'>[...]</span>";
					if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
					{
		    		echo "<!-- ";
		    	}
					echo "No original key found for global key ($localkey) ?! [b]";
					if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
					{
		    		echo " -->";
		    	}
				}
				$currentlypointsto = $metadata[$localkey];
			}
			else if (count($globalkeypieces) == 4)
			{
				// when the property name in the (widget) has 2 underscores (for example my_property_name), 
				// it will look like "my_property_name_catglobalids", the explode will result in 4 pieces, 
				// the localkey in that case is the concatenation of the first 3 pieces "my_property_name" (=localkey)
				$localkey = $globalkeypieces[0] . "_" . $globalkeypieces[1] . "_" . $globalkeypieces[2];
				if (!array_key_exists($localkey, $metadata))
				{
					// unable to locate localkey for globalkey
					// blijkbaar is er een veld XYZ_catglobalids, zonder een overeenkomstig XYZ meta veld?!
					echo "<span title='More information in the HTML DOM'>[...]</span>";
					if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
					{
		    		echo "<!-- ";
		    	}
					echo "No original key found for global key ($localkey) ?! [c]";
					if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
					{
		    		echo " -->";
		    	}
				}
				$currentlypointsto = $metadata[$localkey];
			}
			else
			{
				// unsupported globalkeypieces; expected to find one _
				echo "Unsupported globalkeypieces structure (" . $globalkey . "); (" . count($globalkeypieces) . ")?! this is not supported<br />";
				die();
			}
			
			// convert [foo][bar] in [{category_id_of_foo}][{category_id_of_bar}]
			$localidslookupaccordingtoglobalids = nxs_get_localids_categories_v2($globalcatids_that_are_correct);
			$correct_localids = $localidslookupaccordingtoglobalids["result"];
			
			if ($correct_localids == $currentlypointsto)
			{
				// OK, consistent
			}
			else
			{
				// an inconsistency was found
				$isglobalidscompletelyvalid = count($localidslookupaccordingtoglobalids["invalidglobalidsarray"]) == 0;
				
				if ($isglobalidscompletelyvalid)
				{
					// if the categories according to the globalids still exist 1:1,
					// we will simply override the localids accordingly
					$metakeyvaluestoupdate[$localkey] = $correct_localids;
					
					echo "<span title='More information in the HTML DOM'>[...]</span>";
					if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
					{
		    		echo "<!-- ";
		    	}
					echo "updating; key '$localkey' from '$currentlypointsto' to '$correct_localids' (d)";
					if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
					{
		    		echo " -->";
		    	}
				}
				else
				{
					// at least one category referenced by the globalids no longer exist,
					// to solve this scenario, we keep the best of both worlds (both valid categories according to globalids as well as localids)
					
					$newverifiedlocalids = array();
					// 1) we set to the localids of the valid categoryids to which the globalids were pointing
					$newverifiedlocalids = array_merge($newverifiedlocalids, $localidslookupaccordingtoglobalids["validlocalidsarray"]);
					// 2) set the localids to match the correct localids that actually still point to a real category,
					// $currentlypointsto = [1][5][10]
					$unverifiedlocal_catids_commaseperatedstring = nxs_convert_stringwithbracketlist_to_stringwithcommas($currentlypointsto); // bijv. [1][2][10] -> 1,2,10
					
					//echo "[unverifiedlocal_catids_commaseperatedstring:$unverifiedlocal_catids_commaseperatedstring]";
					
					$unverifiedlocal_catids_array = explode(",", $unverifiedlocal_catids_commaseperatedstring);	// {1,2,10}
					foreach($unverifiedlocal_catids_array as $current_unverifiedlocal_catid)
					{
						//echo "[current_unverifiedlocal_catid:$current_unverifiedlocal_catid]";
						
						$name = get_cat_name($current_unverifiedlocal_catid);
						
						if ($name == "")
						{
							// if its empty, perhaps its a product category instead of "regular" category
							$category = get_term_by('id', $current_unverifiedlocal_catid, 'product_cat', 'ARRAY_A');
    					$name = $category['name']; 
						}
						
						//echo "[name:$name]";
						
						if (isset($name) && $name != "")
						{
							// valid
							$newverifiedlocalids[] = $current_unverifiedlocal_catid;
						}
					}
					//var_dump($newverifiedlocalids);
					// make distinct
					$newverifiedlocalids = array_unique($newverifiedlocalids);
					//var_dump($newverifiedlocalids);
					
					$newverifiedlocalids_brackets = "";
					foreach ($newverifiedlocalids as $currentnewverifiedlocalid)
					{
						$newverifiedlocalids_brackets .= "[" . $currentnewverifiedlocalid . "]";
					}

					

					// 3) store localids
					if ($metadata[$localkey] != $newverifiedlocalids_brackets)
					{
						$metakeyvaluestoupdate[$localkey] = $newverifiedlocalids_brackets;
					}
					
					// 4) update globalids
					$newvalue = nxs_get_globalids_categories($newverifiedlocalids_brackets);
					if ($metadata[$globalkey] != $newvalue)
					{
						$metakeyvaluestoupdate[$globalkey] = $newvalue;
					}
					
					echo "<span title='More information in the HTML DOM'>[...]</span>";
					if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
					{
		    		echo "<!-- ";
		    	}
					echo "updating; key '$localkey' from '$currentlypointsto' to '$correct_localids' (c)";
					if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
					{
		    		echo " -->";
		    	}
				}
			}			
		}
		else
		{
			//echo "Local key found; $metakey";
		}
	}
	
	return $metakeyvaluestoupdate;
}

function nxs_dataconsistency_sanitize_unicontentdata()
{
	//echo "s1";
	
	$groups = nxs_unicontent_getgroups();
	
	//echo "s2";
	//var_dump($groups);
	
	foreach ($groups as $currentgroup)
	{
		$contentnames = nxs_unicontent_getunicontentnames($currentgroup);
		foreach ($contentnames as $currentname)
		{
			if ($currentgroup != "")
			{
				$unicontentprops = nxs_unicontent_getunicontentproperties($currentgroup, $currentname);
				$metakeyvaluestoupdate = nxs_getdatarequiringmodificationforglobalidfix($unicontentprops);
				if (count($metakeyvaluestoupdate) > 0)
				{
					$newprops = array_merge($unicontentprops, $metakeyvaluestoupdate);
					nxs_unicontent_persistunicontent($currentgroup, $currentname, $newprops);
				}
				else
				{
					// leave as is
				}
			} 
			else
			{
				// skip
			}
		}
	}
}

function nxs_dataconsistency_sanitize_postwidgetmetadata($postid)
{ 		
	$parsedpoststructure = nxs_parsepoststructure($postid);
	$rowindex = 0;
	foreach ($parsedpoststructure as $pagerow)
	{
		$content = $pagerow["content"];
		$placeholderids = nxs_parseplaceholderidsfrompagerow($content);
						
		foreach ($placeholderids as $placeholderid)
		{
			$currentwidgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);

			$metakeyvaluestoupdate = nxs_getdatarequiringmodificationforglobalidfix($currentwidgetmetadata);
			if (count($metakeyvaluestoupdate) > 0)
			{
				if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
				{
	    		echo "<!-- ";
	    	}
	    	echo "Post <a href='" . nxs_geturl_for_postid($postid) . "'>" . $postid . "</a><br />";
	    	if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
				{
	    		echo " -->";
	    	}
				
				//echo "[This placeholder has items that are required to be updated, as follows]<br />";
				//var_dump($metakeyvaluestoupdate);
				//echo "<br />";
				
				// FIX 8 feb 2014
				// here we should explicitly NOT update the values of unistyle and unicontent,
				// as they would mess up the centralized stored data, if the properties of this
				// meta would not be in sync (happens in for example the nutritionist / lifecoach
				// theme, where a blogwidget locally had data that was not in sync with the unistyled
				// value on the homepage.
				
				$behaviourargs = array();
				$behaviourargs["updateunistyle"] = false;
				$behaviourargs["updateunicontent"] = false;
				nxs_mergewidgetmetadata_internal_v2($postid, $placeholderid, $metakeyvaluestoupdate, $behaviourargs);
				// echo "[The metadata of the widget was updated]<br />";
				
				//$currentwidgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
				//var_dump($currentwidgetmetadata);
			}
		}
	}
}

function nxs_dataconsistency_sanitize_postmetadata($postid)
{
	echo "<span title='More information in the HTML DOM'>[...]</span><!-- pm " . $postid ." -->";
	//
	$currentpagemeta = nxs_get_postmeta($postid);
	$modifiedmetadata = nxs_getdatarequiringmodificationforglobalidfix($currentpagemeta);
	if (count($modifiedmetadata) > 0)
	{
		// 
		if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
		{
  		echo "<!-- ";
  	}
		
		echo "Post <a href='" . nxs_geturl_for_postid($postid) . "'>" . $postid . "</a><br />";
		echo "[This page has items that are required to be updated, as follows]<br />";
		var_dump($modifiedmetadata);
		echo "<br />";
		
		nxs_merge_postmeta($postid, $modifiedmetadata);
		echo "[The metadata of the widget was updated]<br />";
		//$currentwidgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
		//var_dump($currentwidgetmetadata);
		
		if (NXS_DEFINE_MINIMALISTICDATACONSISTENCYOUTPUT)
		{
  		echo " -->";
  	}
	}
}

?>