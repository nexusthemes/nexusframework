<?php
function nxs_popup_genericpopup_categorieseditor_getpopup($args)
{
	extract($args);
	
	// clientpopupsessiondata bevat key values van de client side
	// deze overschrijft met opzet (tijdelijk) mogelijk waarden die via $args
	// zijn meegegeven; hierdoor kan namelijk een 'gevoel' worden gecreeerd
	// van een 'state' die client side leeft, die helpt om meerdere (popup) 
	// pagina's state te laten delen. De inhoud van clientpopupsessiondata is een
	// array die wordt gevoed door de clientside variabele "popupsessiondata",
	// die gedefinieerd is in de file 'frontendediting.php'
	extract($clientpopupsessiondata);	
	extract($clientshortscopedata);
	
	$result = array();
	$result["result"] = "OK";
	
	$catargs = array();
	$catargs['hide_empty'] = 0;
	$categories = get_categories($catargs);
	
	$categoriesfilters = array();
  $categoriesfilters["uncategorized"] = "skip";
  nxs_getfilteredcategories($categories, $categoriesfilters);	
	
	ob_start();
	
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">
			<?php nxs_render_popup_header(nxs_l18n__("Categories editor", "nxs_td")); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					<div class="content2">
		        <div class="box">
		          <div class="box-title">
								<h4><?php nxs_l18n_e("Categories[nxs:heading]", "nxs_td"); ?></h4>		                
		          </div>
		          <div class="box-content">
								<ul class="cat-checklist" id='selectedcategoryids'>
						      <?php 
						    	foreach ($categories as $category)
						    	{
						    		$termid = $category->term_id;
						    		$name = $category->name;
		
						    		$key = "[" . $termid . "]";
						    		if (nxs_stringcontains($selectedcategoryids, $key))
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
			            			<?php echo $name; ?>
			            			<a class="nxs-float-right" href='#' onclick="handleremovecategory('<?php echo $name; ?>', '<?php echo $termid; ?>'); return false;"><?php nxs_l18n_e("Remove category[nxs:categories]", "nxs_td"); ?></a>
			            		</label>
			            	</li>
							    	<?php
							    }
							    ?>	   
							  </ul>         	
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->    		
		      
					<div class="content2">
		        <div class="box">
		          <div class="box-title">
								<h4><?php nxs_l18n_e("New category[nxs:heading]", "nxs_td"); ?></h4>		                
		           </div>
		          <div class="box-content">
		          	<input id="newcategoryname" name="newcategoryname" tyle='text' class="nxs-float-left nxs-width40" value="" />
		          	<a id="addcategorybutton" class="nxsbutton1 nxs-float-left" href="#" onclick="handlenewcategory(); return false;"><?php nxs_l18n_e("Add[nxs:button]", "nxs_td"); ?></a>
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->		      
				</div> <!-- END nxs-popup-content-canvas -->
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
				<div class="box">
					<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_popup_navigateto("<?php echo $nxs_categorieseditor_invoker; ?>"); return false;'><?php nxs_l18n_e("Back"); ?></a>
				</div>
				<div class="nxs-clear margin"></div>
			</div> <!-- END content -->
		</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<script type='text/javascript'>
		
		function handleremovecategory(name, catid)
		{		
			var answer = confirm("<?php nxs_l18n_e("Are you sure you want to remove this category?[nxs:confirm]", "nxs_td"); ?> (" + name + ")");			
			if (answer)
			{
				nxs_js_removecategory
				(
					catid, 
					function()
					{
						nxs_js_alert('<?php nxs_l18n_e("Category removed[nxs:growl]", "nxs_td"); ?>');
						
						// sla allereerst de huidige toestand van de variabelen op in de popup session data
						nxs_js_savepopupdata();
						
						// the category was removed,
						// we refresh the pop up to see category was removed
						nxs_js_popup_refresh();
					}
				);
			}
		}
		
		function handlenewcategory()
		{			
			var name = jQ_nxs('#newcategoryname').val();
			
			name = jQuery.trim(name);
			if (name == '')
			{
				nxs_js_alert('<?php nxs_l18n_e("Category name is required[nxs:growl]", "nxs_td"); ?>');
				jQ_nxs('#newcategoryname').focus();
			}
			else
			{
				nxs_js_addcategory
				(
					name, 
					function(categoryname, categoryid)
					{
						// success
						<?php 
						if (isset($nxs_categorieseditor_appendnewitemsto)) 
						{ 
							// append the categoryid to the sessiondata for the specified id
							?>
							var previouscatlist = nxs_js_popup_getsessiondata("<?php echo $nxs_categorieseditor_appendnewitemsto; ?>");
							nxs_js_popup_setsessiondata("<?php echo $nxs_categorieseditor_appendnewitemsto; ?>", previouscatlist + "[" + categoryid + "]");
							nxs_js_alert(nxs_js_popup_getsessiondata("<?php echo $nxs_categorieseditor_appendnewitemsto; ?>"));
							<?php 
						} 
						?>
						
						nxs_js_alert('<?php nxs_l18n_e("Category added[nxs:growl]", "nxs_td"); ?> (' + categoryname + ')');
						
						// sla allereerst de huidige toestand van de variabelen op in de popup session data
						nxs_js_savepopupdata();
						
						// the category was inserted,
						// we refresh the pop up to see the newly inserted category
						nxs_js_popup_refresh();
					}
				);
			}
		}
	</script>
	<?php

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
  $html = ob_get_contents();
  ob_end_clean();
    
  // Setting the contents of the variable to the appropriate array position
  // The framework uses this array with its accompanying values to render the page
  $result["html"] = $html;
  return $result;
}
?>