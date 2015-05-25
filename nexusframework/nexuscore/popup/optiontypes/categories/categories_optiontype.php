<?php
function nxs_popup_optiontype_categories_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	// default
	$taxonomy = "category";
	$maxselectable = "";
	
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter
	?>
	<div class="content2">
  	<div class="box">
      <div class="box-title">
        <h4><?php echo $label;?></h4>
        
        <?php
        if ($tooltip != "") 
				{
					echo '
					<span class="info">?
						<div class="info-description">' . $tooltip .'</div>
					</span>';
				}
        if ($editable != "false")
				{
					?>
        	<a href="#" class="nxsbutton1 nxs-float-left" onclick="nxs_js_startcategorieseditor(); return false;"><?php nxs_l18n_e("Edit", "nxs_td"); ?></a>
        	<?php
        }
        ?>
      </div>
      <div class="box-content">
     		<ul class="cat-checklist" id='<?php echo $id; ?>'>
        	<?php
        	$catargs = array();
					$catargs['hide_empty'] = 0;
				  $catargs["taxonomy"] = $taxonomy;
					$categories = get_categories($catargs);
					
					$categoriesfilters = array();
				  $categoriesfilters["uncategorized"] = "skip";
				  
          nxs_getfilteredcategories($categories, $categoriesfilters);
					
					foreach ($categories as $currentcategory)
		    	{
		    		$termid = $currentcategory->term_id;
		    		$name = $currentcategory->name;

		    		$key = "[" . $termid . "]";
		    		if (nxs_stringcontains($value, $key))
		    		{
		    			$possiblyselected = "checked='checked'";
		    		}
		    		else
		    		{
		    			$possiblyselected = "";
		    		}
		    		
		    		?>
						<li>
							<label>
          			<input class='selectable_category' id="catid_<?php echo $termid; ?>" type="checkbox" <?php echo $possiblyselected; ?> />
          			<?php echo $name; ?>
          		</label>
          	</li>
			    	<?php
			    }
			    ?>
			  </ul>
			  <div class='nxs-clear nxs-margin-top5'></div>
      </div>
  	</div>
    <div class="nxs-clear"></div>
	</div> <!--END content-->
	
	<script type='text/javascript'>
		
		<?php if ($maxselectable == "1") { ?>
			jQ_nxs(".selectable_category").on
			(
				{
					click: function(e) 
					{
						if (jQ_nxs(this).prop("checked"))
						{
							// turn off all checkboxes
							jQ_nxs("#<?php echo $id; ?> input").prop("checked", false); 
							// turn on the current one
							jQ_nxs(this).prop("checked", true);
        			// nxs_js_log(e);
        		}
    			}
    		}
			)
		<?php } ?>

		
		function nxs_js_startcategorieseditor()
		{
			nxs_js_setpopupdatefromcontrols(); 
			nxs_js_popup_setsessiondata("nxs_categorieseditor_invoker", nxs_js_popup_getcurrentsheet()); 
			nxs_js_popup_setsessiondata("nxs_categorieseditor_appendnewitemsto", "<?php echo $id;?>"); 
			
			nxs_js_popup_navigateto("categorieseditor");
		}
	</script>
	
	<?php
}

function nxs_popup_optiontype_categories_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	
	echo 'nxs_js_popup_storestatecontroldata_listofcheckbox("' . $id . '", "selectable_category", "' . $id . '");';
}

function nxs_popup_optiontype_categories_getitemstoextendbeforepersistoccurs($optionvalues, $metadata)
{
	$result = array();
	
	$id = $optionvalues["id"];
	$value = $metadata[$id];
	
	// some types could use this function to insert additional meta data fields to be stored
	// just before the values are saved. This is practical to store for set globalids
	// of items that are to be threated in a special way when being imported/exported
	
	$result[$id . '_catglobalids'] = nxs_get_globalids_categories($value);
	
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_categories_getpersistbehaviour()
{
	return "writeid";
}

?>