<?php
function nxs_popup_optiontype_custom_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	
	if (isset($runtimeblendeddata[$id]))
	{
		$value = $runtimeblendeddata[$id];
	}
	else
	{
		$value = "";
	}
	
	echo '
	<div class="" style="
	padding: 10px;
	background-color: #whiteSmoke;
	background-image: 		  linear-gradient(top,#F9F9F9,whiteSmoke);
	background-image: 	   -o-linear-gradient(top,#F9F9F9,whiteSmoke);
	background-image: 	  -ms-linear-gradient(top,#F9F9F9,whiteSmoke);
	background-image:    -moz-linear-gradient(top,#F9F9F9,whiteSmoke);
	background-image: -webkit-linear-gradient(top,#F9F9F9,whiteSmoke);
	background-image: -webkit-gradient(linear,left top,left bottom,from(#F9F9F9),to(whiteSmoke));
	
	">
	    <div class="box">
	        <div class="box-title">
						<h4>'. $label .'</h4>
	';
							if ($tooltip != "") 
							{
								echo '<span class="info">?
									<div class="info-description">' . $tooltip .'</div>
								</span>';
							}
						?>
					</div>
          <div class="" style="width: 70%; float: right;">
          	<?php
          	echo $custom;
          	// if handler is set, delegate rendering to handler
          	if (isset($customcontenthandler))
          	{
          		$functionnametoinvoke = $customcontenthandler;
							if (function_exists($functionnametoinvoke))
							{
								$p = array();
								$p["optionvalues"] = $optionvalues;
								$p["args"] = $args;
								$p["runtimeblendeddata"] = $runtimeblendeddata;
								$result = call_user_func_array($functionnametoinvoke, $p);
								echo $result;
							}
							else
							{
								echo "function not found; " . $customcontenthandler;
							}
          	}
          	echo '
          </div>
        </div>
        <div class="nxs-clear"></div>
      </div>
  ';
	//
}

function nxs_popup_optiontype_custom_renderstorestatecontroldata($optionvalues)
{
	// nothing to do here
}

function nxs_popup_optiontype_custom_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	// nothing to do here
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_custom_getpersistbehaviour()
{
	return "readonly";
}

?>