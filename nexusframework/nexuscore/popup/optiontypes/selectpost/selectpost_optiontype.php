<?php
function nxs_popup_optiontype_selectpost_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	// default values
	$emptyitem_enable = "true";
	$emptyitem_text = "";
	$emptyitem_value = "";
	
	$previewlink_enable = "true";
	$previewlink_text = "preview";
	$previewlink_target = "_blank";
	
	$beforeitems = array();
	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	
	$value = $runtimeblendeddata[$id];	// $id is the parametername, $$id is the value of that parameter
	
	if (!isset($orderby))
	{
		$orderby = "post_data";
	}
	if (!isset($order))
	{
		$order = "DESC";
	}
	if (!isset($post_status))
	{
		$post_status = "publish";
	}
	if (!isset($post_type))
	{
		$post_type = array("post", "page");
	}
	
	$publishedargs = array();
	$publishedargs["post_status"] 	= $post_status;
	$publishedargs["post_type"] 	= $post_type;
	
	if (isset($subposttype))
	{
		// if a subposttype is specified,
		// add the subposttype taxonomy to the filter 
		$publishedargs["nxs_tax_subposttype"] = $subposttype;
	}
	
	$publishedargs["orderby"] 		= $orderby;
	$publishedargs["order"] 		= $order;
	$publishedargs["numberposts"] 	= -1;	// all!
	$items = get_posts($publishedargs);
							              
	?>
	<div class="content2">
		<div class="box">
	    <?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
			<div class="box-content">
				<select id="<?php echo $id; ?>" class="chosen-select" name="<?php echo $id; ?>" onchange="nxs_js_popup_sessiondata_make_dirty();">
				<?php				 
					if ($id == "") 
					{
						$selected = "selected='selected'";
					} 
					else 
					{
						$selected = "";
					}
				?>
				<?php
				if ($emptyitem_enable == "true")
				{
					?>
					<option value='<?php echo $emptyitem_value; ?>' <?php echo $selected; ?>><?php echo $emptyitem_text; ?></option>
					<?php
				}
				
				/**/
				
    		// dropdown is specified as keys and values
      	foreach ($beforeitems as $currentkey => $currentvalue) 
      	{
      		if ($currentkey == "@@@nxsempty@@@")
      		{
      			$currentkey = "";
      		}
      		
      		$selected = "";
      		if ($currentkey == $value) 
      		{
      			$selected ="selected='selected'";
      		}
      		?>
          <option <?php echo $selected; ?> value='<?php echo $currentkey; ?>'><?php echo $currentvalue; ?></option>
          <?php 
        }
				
				/**/
				
				$isfound = false;
				foreach ($items as $currentpost) 
				{
					$currentpostid = $currentpost->ID;
					$currentpoststatus = $currentpost->post_status;
					$posttitle = nxs_cutstring($currentpost->post_title, 50);
				
					if ($posttitle == "") 
					{
						$posttitle = "(empty, ID:" . $currentpostid . ")";
					}
					
					if ($currentpoststatus != "publish")
					{
						$posttitle .= " ($currentpoststatus)";
					}
					
					$selected = "";
					if ($currentpostid == $value) 
					{
						$isfound = true;
						$selected = "selected='selected'";
					} 
					else 
					{
						$selected = "";
					}
					echo "<option value='$currentpostid' $selected	>$posttitle</option>";
				}
				?>
				</select>
				<?php 
				if ($previewlink_enable == "true")
				{
					echo "<a href='#' class='nxsbutton1' onclick='nxs_js_preview(this)'; return false;'>" . $previewlink_text . "</a>";
				}
				if ($isfound === false && $value != 0)
				{
					echo "<span title='" . nxs_l18n__("Warning; current value is no longer listed", "nxs_td") . "'>(!)</span>";
					?>
					<script type='text/javascript'>
						nxs_js_popup_sessiondata_make_dirty();
					</script>
					<?php
				}
				?>
			</div>
		</div>
		<div class="nxs-clear"></div>
	</div> <!--END content-->
	<script type='text/javascript'>
		function nxs_js_preview(element)
		{
			var postid = jQ_nxs(element).closest(".box-content").find("select option:selected").val();
			if (postid == '')
			{
				nxs_js_alert(nxs_js_gettrans('Please select a value for'));
			}
			else
			{
				nxs_js_geturl("postid", postid, "notused", 
				function(response) 
				{
					var url = response.url;
					window.open(url, "_blank");
				},
				function(response) 
				{
					nxs_js_alert(nxs_js_gettrans('Unable to retrieve the URL'));
				});
			}
		}
	</script>
	<?php
}

function nxs_popup_optiontype_selectpost_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_dropdown("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_selectpost_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	
	$id = $optionvalues["id"];
	$value = $metadata[$id];
	
	$globalid = nxs_get_globalid($value, true);
	$result[$id . "_globalid"] = $globalid;
	
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_selectpost_getpersistbehaviour()
{
	return "writeid";
}

?>