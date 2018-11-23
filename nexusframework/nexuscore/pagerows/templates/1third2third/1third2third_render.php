<?php

require_once(NXS_FRAMEWORKPATH . '/nexuscore/pagerows/includes/pagerowfunctions.php');

function nxs_pagerowtemplate_render_1third2third_toolbox($args)
{
	extract($args);
	
	nxs_ob_start();
	?>
  
	<p class="nxs-one-third">&#8531;</p>
	<p class="nxs-two-third">&#8532;</p>
	<div class="nxs-drag-helper" style='display: none;'>
		<ul class='nxs-fraction'>
			<li>
				<p class="nxs-one-third">&#8531;</p>
	                <p class="nxs-two-third">&#8532;</p>
			</li>
		</ul>
	</div>
	
	<?php
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	echo $result;
}

function nxs_pagerowtemplate_render_1third2third($args)
{
	extract($args);
	
	nxs_ob_start();

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
		$slot2_placeholdertemplate = nxs_getplaceholdertemplate($postid, $placeholderids[1]);
		
		$slot1_placeholderid = "id='nxs_x_ph_". $placeholderids[0] . "'";
		$slot2_placeholderid = "id='nxs_x_ph_". $placeholderids[1] . "'";

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
	
	$imageclassattribute_slot1 = "class=\"" . $imageclass . " " . nxs_getwidgeticonid($slot1_placeholdertemplate) . "\" ";
	$imageclassattribute_slot2 = "class=\"" . $imageclass . " " . nxs_getwidgeticonid($slot2_placeholdertemplate) . "\" ";
		
	?>
		<li <?php echo $rowidattribute; ?>>
			<span class="<?php echo $headerclass; ?>" <?php echo $headerattribute; ?>>
				<ul class="header-list">
			    <li class="nxs-one-third">&#8531;</li>
		    	<li class="nxs-two-third">&#8532;</li>
				</ul>
				<div class="nxs-clear"></div>
			</span>	
			<?php
			if ($onderdrukcontent === true)
			{
				// suppress
			}
			else if ($onderdrukcontent == '' || $onderdrukcontent == '_new')
			{
			?>
			<span class="content">
				<div class="nxs-clear">
					<ul class="nxs-content-list">
				    <li class="nxs-one-third droppable_placeholdercontainer">
				    	<span <?php echo $slot1_placeholderid; echo $imageclassattribute_slot1; ?>></span>
				    </li>
			    	<li class="nxs-two-third droppable_placeholdercontainer">
				    	<span <?php echo $slot2_placeholderid; echo $imageclassattribute_slot2; ?>></span>
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
	
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
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