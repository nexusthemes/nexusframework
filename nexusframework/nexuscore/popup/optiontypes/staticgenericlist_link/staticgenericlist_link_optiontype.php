<?php
function nxs_popup_optiontype_staticgenericlist_link_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	
	$containerpostid = $args["clientpopupsessioncontext"]["containerpostid"];
	$currentcontainerposturl = nxs_geturl_for_postid($containerpostid);
	$nxsrefurlspecial = urlencode(base64_encode($currentcontainerposturl));
	
	$refurl = get_home_url() . "/?nxs_genericlist=" . urlencode(nxs_getslug_for_postid($value)) . "&nxsrefcontainerpostid=" . $containerpostid . "&nxsrefurlspecial=" . $nxsrefurlspecial;
	?>
	<div class="content2">
    <div class="box">
	    <?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
      <div class="box-content">
       	<input type="hidden" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>" />
      	<div style='display: flex;'>
      		<div>
      			<?php
      			if ($preview_theme == "gallerythumbs")
      			{
      				?>
      				<a href='#' onclick="nxs_js_savegenericpopup_internal(function(response){nxs_js_redirect('<?php echo $refurl;?>');}); return false;" class="nxsbutton1 nxs-float-right">
      					<div style='display: flex; flex-direction: row; flex-wrap: wrap; align-items: center;'>
		      				<?php
		      				$numdrawn = 0;
		      				//var_dump($containerpostid);
		      				$structure = nxs_parsepoststructure($value);
		      				$index = -1;
									foreach ($structure as $pagerow)
									{
										$index = $index + 1;
										$rowcontent = $pagerow["content"];
										$placeholderid = nxs_parsepagerow($rowcontent);
										$placeholdermetadata = nxs_getwidgetmetadata($value, $placeholderid);
										
										$image_imageid = $placeholdermetadata["image_imageid"];
										//var_dump($placeholdermetadata);
										if ($image_imageid != "")
										{
											$image_size = "c@1-0";
											$wpsize = nxs_getwpimagesize($image_size);
											$imagemetadata= nxs_wp_get_attachment_image_src($image_imageid, $wpsize, true);
											// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
											$imageurl 		= $imagemetadata[0];
											echo "<img src='{$imageurl}' style='max-width: 80px; height: auto; margin: 0;' />";
											$numdrawn++;
										}
									}
									
									if ($numdrawn == 0)
									{
										echo "<div style='width: 80px; height: 80px; background-color: #eee; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; '>-</div>";
									}
									
									?>
								</div>
							</a>
							<?php
							   				
      			}
      			?>
      		</div>
      		<div style='flex-grow: 1;'>
        		<a href='#' onclick="nxs_js_savegenericpopup_internal(function(response){nxs_js_redirect('<?php echo $refurl;?>');}); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Edit", "nxs_td"); ?></a>
        	</div>
        </div>
      </div>
    </div>
    <div class="nxs-clear"></div>
	</div>
	<?php
	//
}

function nxs_popup_optiontype_staticgenericlist_link_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	echo 'nxs_js_popup_storestatecontroldata_hiddenfield("' . $id . '", "' . $id . '");';	
}

function nxs_popup_optiontype_staticgenericlist_link_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
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
function nxs_popup_optiontype_staticgenericlist_link_getpersistbehaviour()
{
	return "writeid";
}

?>