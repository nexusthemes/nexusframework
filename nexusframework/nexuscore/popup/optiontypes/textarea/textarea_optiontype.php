<?php
function nxs_popup_optiontype_textarea_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	$cols = "50";	// default
	$rows = "15";	// default
	$valueadapters = array();
	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	if (array_key_exists($value, $valueadapters))
	{
		// adapt value
		$value = $valueadapters[$value];
	}
	
	// 
	$value = str_replace("@@NXSNEWLINE@@", "\n", $value);
	
	// kudos to https://stackoverflow.com/questions/17772260/textarea-auto-height for autogrowing
	
	if ($footer != "")
	{
		$footerhtml = "<div class='textarea-footer'>{$footer}</div>";
	}
	
	echo '
  <div class="content2">
    <div class="box">';
			echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip);
			echo '
      <div class="box-content">
      	<script>
      		function nxs_js_textarea_autogrow(element)
      		{
      			element.style.height = "auto";
    				element.style.height = (element.scrollHeight)+"px";
      		}
      	</script>
      	<style>
	      	nxs-textarea-autoresize.textarea {
					    resize: none;
					    overflow: hidden;
					    min-height: 50px;
					    max-height: 100px;
					    box-sizing: border-box;
					}
      	</style>
        <textarea class="nxs-textarea-autoresize" onkeyup="nxs_js_textarea_autogrow(this);" id="'. $id . '" name="content" cols="' . $cols . '" rows="' . $rows . '" placeholder="' . nxs_render_html_escape_doublequote($placeholder) . '" >' . nxs_render_html_escape_gtlt($value) . '</textarea>
        '.$footerhtml.'
        <script>
        	// trigger autogrow for the first time it renders, after a short delay
        	setTimeout(function(){ var element = document.getElementById("'. $id . '"); nxs_js_textarea_autogrow(element); }, 250);
        </script>
      </div>
  </div>
  <div class="nxs-clear"></div>
</div>
';
//
}

function nxs_popup_optiontype_textarea_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_textbox("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_textarea_getitemstoextendbeforepersistoccurs($optionvalues, $args)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_textarea_getpersistbehaviour()
{
	return "writeid";
}
