<?php
function nxs_popup_optiontype_wrapperbegin_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	// default settings, overriden by extract statements below
	$initial_toggle_state = "open";
	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	if ($initial_toggle_state == "open")
	{
		$nxs_toggled_cssclass = "nxs-toggled-open";
	}
	else
	{
		$nxs_toggled_cssclass = "nxs-toggled-closed";
	}
	
	?>
	<div class="nxs-option-toggler <?php echo $nxs_toggled_cssclass; ?>"> <!-- closed by wrapperend -->
		<a href="#" onclick="nxs_js_popuptogglewrapper(this, 'nxs-wrapperbegin-<?php echo $id; ?>'); return false;">
			<div class="content2 nxs-popup-heading <?php echo $heading_cssclass;?>">
			  <div class="box">
			  	<div class="box-title">
						<h4><?php echo $label; ?></h4>
					</div>
		      <div class="box-content">
	      		<span class="nxs-switcher-open nxs-icon-circle-arrow-up" title='<?php nxs_l18n_e("Close section", "nxs_td"); ?>'></span>
	      		<span class="nxs-switcher-close nxs-icon-circle-arrow-down" title='<?php nxs_l18n_e("Open section", "nxs_td"); ?>'></span>
		      </div>
		    </div>
		    <div class="nxs-clear"></div>
		  </div>
		</a>
	  <div class="nxs-option-wrapper">
	  	<div id="nxs-wrapperbegin-<?php echo $id; ?>" class="nxs-wrapperbegin">
  <?php
	//
}


function nxs_popup_optiontype_wrapperbegin_renderstorestatecontroldata($optionvalues)
{
	// nothing to do here
}

function nxs_popup_optiontype_wrapperbegin_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	// nothing to do here
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_wrapperbegin_getpersistbehaviour()
{
	return "readonly";
}

?>