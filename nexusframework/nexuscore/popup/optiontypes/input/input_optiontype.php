<?php
function nxs_popup_optiontype_input_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	$readonly = "false";
	$visibility = "show";
	$showlookuphelp = "true";
	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	if (isset($required) && $required == "true")
	{
		$isrequiredhtml = "<span class='required'>*</span>";	
	}
	else
	{
		$isrequiredhtml = "";
	}
	
	if (isset($readonly) && $readonly == "true")
	{
		$readonly = "readonly='readonly'";
	}
	else
	{
		$readonly = "";
	}
	
	if ($visibility == "hide" || $visibility == "hidden")
	{
		$content2style = "style='display: none;'";
	}
	
	$lookuphelphtml = "";
	if ($showlookuphelp == "true")
	{
		if (nxs_stringcontains($value, nxs_lookuptable_getprefixtoken()) && (nxs_stringcontains($value, nxs_lookuptable_getpostfixtoken())))
		{
			$orig = $value;
			$translateme = array
			(
				"value" => $value,
			);
			// Lookup atts
			$translations = nxs_filter_translatelookup($translateme, array("value"));
			$translatesto = $translations["value"];
			
			$lookuphelphtml = "";
			$lookuphelphtml .= "<a href='#' onclick='nxs_js_popup_site_neweditsession(\"lookuptablemanagementhome\"); return false;' class='nxsbutton1 nxs-float-right'>Manage</a>";
			if ($orig != $translatesto)
			{
				$lookuphelphtml .= nxs_l18n__("'{$orig}' translates to '{$translatesto}'") . " ";
			}
			$lookuphelphtml .= "<a href='http://nexusthemes.com/support/content-lookup-tables/' target='_blank'>" . nxs_l18n__("Learn more") . "</a>";
			$lookuphelphtml = '<div class="content" style="font-size: smaller; font-style: italic;">' . $lookuphelphtml . '</div>';
		}
	}
	
	$persistmode = $optionvalues["persistmode"];
	$autofocusattribute = "";
	if ($optionvalues["autofocus"] != "")
	{
		$autofocusattribute = "autofocus";
	}
	
	echo '
	<div class="content2" ' . $content2style . '>
	    <div class="box">
	        ' . nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip) . '
          <div class="box-content">
						<input class="nxs-persistmode-'.$persistmode.'" type="text" id="'. $id . '" name="'. $id . '" value="' . nxs_render_html_escape_doublequote($value) . '" placeholder="' . nxs_render_html_escape_doublequote($placeholder) . '" ' . $readonly . ' ' . $autofocusattribute . ' />
						' . $lookuphelphtml . '
						' . $footernote . '
          </div>
        </div>
        <div class="nxs-clear"></div>
      </div>
      ';
	//
	if ($optionvalues["autofocus"] != "")
	{
		?>
		<script>setTimeout(function (){ jQ_nxs('#<?php echo $id; ?>').focus(); }, 100);</script>
		<?php
	}
}

function nxs_popup_optiontype_input_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	$persistmode = $optionvalues["persistmode"];
	if ($persistmode == "shortscope")
	{
		// store the value in the shortscope data (only available for the upcoming post)
		?>
		if (jQ_nxs('#<?php echo $id; ?>').length > 0)
		{
			nxs_js_popup_setshortscopedata('<?php echo $id; ?>', jQ_nxs('#<?php echo $id; ?>').val());
		}
		<?php
	}
	else if ($persistmode == "sessiondata" || $persistmode == "")
	{
		// default; store in session
		echo 'nxs_js_popup_storestatecontroldata_textbox("' . $id . '", "' . $id . '");';	
	}
}

function nxs_popup_optiontype_input_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_input_getpersistbehaviour()
{
	return "writeid";
}

?>