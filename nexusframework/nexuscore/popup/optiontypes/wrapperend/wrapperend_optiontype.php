<?php
function nxs_popup_optiontype_wrapperend_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	echo '
			</div>
  	</div>
  </div>
	';
	//
}


function nxs_popup_optiontype_wrapperend_renderstorestatecontroldata($optionvalues)
{
	// nothing to do here
}

function nxs_popup_optiontype_wrapperend_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	// nothing to do here
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_wrapperend_getpersistbehaviour()
{
	return "readonly";
}

?>