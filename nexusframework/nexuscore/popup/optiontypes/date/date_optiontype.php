<?php
function nxs_popup_optiontype_date_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	$readonly = "false";
	$visibility = "show";
	
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
	
	if ($visibility == "hide")
	{
		$content2style = "style='display: none;'";
	}
	
	echo '
	<div class="content2" ' . $content2style . '>
	    <div class="box">
					' . nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip) . '
          <div class="box-content">
						<input type="text" id="'. $id . '" name="'. $id . '" value="' . nxs_render_html_escape_doublequote($value) . '" placeholder="' . nxs_render_html_escape_doublequote($placeholder) . '" ' . $readonly . ' />
          </div>
        </div>
        <div class="nxs-clear"></div>
      </div>
      <script>
      	nxs_js_log("initializing datepicker");
      	jQ_nxs("#' . $id . '").datepicker({ dateFormat: "' . $dateformat . '", 
      	beforeShow: function(input, inst) 
				{
      		setTimeout(nxs_js_beautifyalldatepickers, 1);
      		setTimeout(nxs_js_beautifyalldatepickers, 100);
      		setTimeout(nxs_js_beautifyalldatepickers, 500);
      		setTimeout(nxs_js_beautifyalldatepickers, 1000);
	    	}
	    	}
      	);
      	
      	nxs_js_log("datepicker initialized");
      	
      	function nxs_js_beautifyalldatepickers()
      	{
	        jQ_nxs(".ui-datepicker-prev").addClass("nxsbutton1");
      		jQ_nxs(".ui-datepicker-next").addClass("nxsbutton1");
      	}
      </script>
      ';
	//
}

function nxs_popup_optiontype_date_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_textbox("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_date_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_date_getpersistbehaviour()
{
	return "writeid";
}

?>