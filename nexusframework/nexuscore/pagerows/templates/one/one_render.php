<?php

require_once(NXS_FRAMEWORKPATH . '/nexuscore/pagerows/includes/pagerowfunctions.php');

function nxs_pagerowtemplate_render_one_toolbox($args)
{
	extract($args);
	
	ob_start();
	?>
  
	<p class="nxs-one-whole">1</p>
	<div class="nxs-drag-helper" style='display: none;'>
		<ul class='nxs-fraction'>
			<li>
				<p class="nxs-one-whole">1</p>
			</li>
		</ul>
	</div>

	<?php
	$result = ob_get_contents();
	ob_end_clean();
	
	echo $result;
}

function nxs_pagerowtemplate_render_one($args)
{
	extract($args);
	
	ob_start();

	if ($onderdrukcontent == '')
	{
		//
		// zoek alle placeholders op die binnen de pagerowtemplate zitten
		//
		
	 	$regex_pattern = "/\[nxsplaceholder(.*)\](.*)\[\/nxsplaceholder\]/Us";
	 	preg_match_all($regex_pattern,$content,$matches);
	
		$placeholderids = array();
	
		foreach ($matches[1] as $placeholderindex => $placeholderidentification)
		{
			$sub_regex_pattern = "/" . preg_quote("placeholderid='") . "(.*)" . preg_quote("'") . "/U";
			$identificationsfound = preg_match($sub_regex_pattern,$placeholderidentification,$sub_matches);
					 		
			if ($identificationsfound == 1)
			{
				$placeholderids[] = $sub_matches[1];
			}
			else
			{
			}
		}
		
		$slot1_placeholdertemplate = nxs_getplaceholdertemplate($postid, $placeholderids[0]);
		
		$slot1_placeholderid = "id='nxs_x_ph_". $placeholderids[0] . "'";
		
		$slot1_placeholderid_container = "id='placeholdercontainer_" . $slot1_placeholdertemplate . "_existing_". $placeholderids[0] . "'";
	}
	
	
	$imageclass .= "draggable_placeholder";
	$headerclass = "";
	$headerclass .= "header ";
	
	if ($draggable == 'true')
	{
		$headerclass .= "draggable_pagerowtemplate ui-widget-content ui-draggable"; 
		
	}
	
	if ($headerid != '')
	{
		$headerattribute = "id=\"" . $headerid . "\"";
	}
	
	if ($rowid != '')
	{
		$rowidattribute="id=\"" . $rowid . "\"";
	}
	
	$imageclassattribute_slot1 = "class=\"" . $imageclass . " " . nxs_getplaceholdericonid($slot1_placeholdertemplate) . "\" ";
		
	?>
		<li <?php echo $rowidattribute; ?>>
			<span class="<?php echo $headerclass; ?>" <?php echo $headerattribute; ?>>
				<ul class="header-list">
			    <li class="nxs-one-whole">1</li>
				</ul>
				<div class="nxs-clear"></div>
			</span>	
			<?php
			if ($onderdrukcontent === true)
			{
				// suppress
			}
			else if ($onderdrukcontent == '_new')
			{
    		nxs_webmethod_return_nack("no longer supported");
			}
			else if ($onderdrukcontent == '')
			{
			?>
			<span class="content">
				<div class="nxs-clear">
					<ul class="nxs-content-list">
				    <li class="nxs-one-whole droppable_placeholdercontainer">
				    	<span <?php echo $slot1_placeholderid; echo $imageclassattribute_slot1; ?>></span>
				    </li>
					</ul>
				</div>
				<div class="nxs-clear"></div>
				
				<?php
				// add plumbing
				if ($args['allowdelete'] == "true")
				{
					?>
					<a href='#' class='nxsbutton2' onclick='removeRow("<?php echo $rowid; ?>");'>Rij verwijderen</a>
					<?php
				}
				?>
			</span>
			<?php
			}
			?>
		</li>
	<?php
	
	$result = ob_get_contents();
	ob_end_clean();
	
	if ($output == "return")
	{
		return $result;		
	}
	else
	{
		echo $result;
	}
}
?>