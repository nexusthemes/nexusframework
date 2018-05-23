<?php
function nxs_popup_optiontype_gotosheet_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	$buttontext = "Go to sheet";
	
	extract($optionvalues);
	extract($runtimeblendeddata);
	
	if (!isset($sheet))
	{
		nxs_webmethod_return_nack("sheet not set; in option list?");
	}
	
	echo '
	<div class="content2">
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
          <div class="box-content">
          	<?php
          	echo $custom;
          	
          	echo '
          	<a href="#" class="nxsbutton1 nxs-float-right" onclick="nxs_js_gotosheet_' . $id . '(); return false;">' . $buttontext . '</a>
          </div>
        </div>
        <div class="nxs-clear"></div>
      </div>
  ';
  ?>
  <script>
		function nxs_js_gotosheet_<?php echo $id;?>()
		{
			<?php
			if (!isset($contextprocessor)) {
			?>
			nxs_js_setpopupdatefromcontrols(); 
			nxs_js_popup_navigateto("<?php echo $sheet;?>");
			<?php
			}
			else if ($contextprocessor == "rowscontainer")
			{
				?>
				nxs_js_popup_rowscontainer_neweditsession("<?php echo $postid;?>", "<?php echo $sheet;?>");
				<?php
			}
			else if ($contextprocessor == "site")
			{
				?>
				 nxs_js_popup_site_neweditsession("<?php echo $sheet;?>");
				<?php
			}
			else if ($contextprocessor == "pagetemplate")
			{
				?>
				 nxs_js_popup_pagetemplate_neweditsession("<?php echo $sheet;?>");
				<?php
			}
			else
			{
				nxs_webmethod_return_nack("unsupported context processor;" . $contextprocessor);
			}
			?>			
		}
	</script>
  <?php
	//
}

function nxs_popup_optiontype_gotosheet_renderstorestatecontroldata($optionvalues)
{
	// nothing to do here
}

function nxs_popup_optiontype_gotosheet_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	// nothing to do here
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_gotosheet_getpersistbehaviour()
{
	return "readonly";
}

?>