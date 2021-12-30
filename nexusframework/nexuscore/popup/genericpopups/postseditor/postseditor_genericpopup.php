<?php
function nxs_popup_genericpopup_postseditor_getpopup($args)
{
	extract($args);
	
	// clientpopupsessiondata bevat key values van de client side
	// deze overschrijft met opzet (tijdelijk) mogelijk waarden die via $args
	// zijn meegegeven; hierdoor kan namelijk een 'gevoel' worden gecreeerd
	// van een 'state' die client side leeft, die helpt om meerdere (popup) 
	// pagina's state te laten delen. De inhoud van clientpopupsessiondata is een
	// array die wordt gevoed door de clientside variabele "popupsessiondata",
	// die gedefinieerd is in de file 'frontendediting.php'
	if ($clientpopupsessiondata != null) { extract($clientpopupsessiondata); }	
	if ($clientshortscopedata != null) { extract($clientshortscopedata); }
	
	$result = array();
	$result["result"] = "OK";
	
	$postargs = array();
	$postargs['hide_empty'] = 0;
	$posts = get_posts($postargs);
		
	nxs_ob_start();
	
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">
			<?php nxs_render_popup_header(nxs_l18n__("Posts editor", "nxs_td")); ?>
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					<div class="content2">
		        <div class="box">
		          <div class="box-title">
								<h4><?php nxs_l18n_e("Posts[nxs:heading]", "nxs_td"); ?></h4>		                
		          </div>
		          <div class="box-content">
								<ul class="posts-checklist" id='selectedpostsids'>
						      <?php 
						    	foreach ($posts as $cpost)
						    	{
						    		$termid = $cpost->term_id;
						    		$name = $cpost->name;
		
						    		$key = "[" . $termid . "]";
						    		if (nxs_stringcontains($selectedpostsids, $key))
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
			            			<a class="nxs-float-right" href='#' onclick="handleremovepost('<?php echo $name; ?>', '<?php echo $termid; ?>'); return false;"><?php nxs_l18n_e("Remove post[nxs:posts]", "nxs_td"); ?></a>
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
								<h4><?php nxs_l18n_e("New post[nxs:heading]", "nxs_td"); ?></h4>		                
		           </div>
		          <div class="box-content">
		          	<input id="newpostname" name="newpostname" tyle='text' class="nxs-float-left nxs-width40" value="" />
		          	<a id="addpostbutton" class="nxsbutton1 nxs-float-left" href="#" onclick="handlenewpost(); return false;"><?php nxs_l18n_e("Add post[nxs:button]", "nxs_td"); ?></a>
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->		      
				</div> <!-- END nxs-popup-content-canvas -->
			</div> <!-- END nxs-popup-content-canvas-cropper -->
	
			<div class="content2">
				<div class="box">
					<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_popup_navigateto("<?php echo $nxs_postseditor_invoker; ?>"); return false;'><?php nxs_l18n_e("Back"); ?></a>
				</div>
				<div class="nxs-clear margin"></div>
			</div> <!-- END content -->
		</div> <!-- END block -->
	</div> <!-- END nxs-admin-wrap -->
	
	<script>
		
		function handleremovepost(name, postid)
		{		
			var answer = confirm("<?php nxs_l18n_e("Are you sure you want to remove this post?[nxs:confirm]", "nxs_td"); ?> (" + name + ")");			
			if (answer)
			{
				nxs_js_removepost
				(
					postid, 
					function()
					{
						nxs_js_alert('<?php nxs_l18n_e("Post removed[nxs:growl]", "nxs_td"); ?>');
						
						// sla allereerst de huidige toestand van de variabelen op in de popup session data
						nxs_js_savepopupdata();
						
						// the post was removed,
						// we refresh the pop up to see post was removed
						nxs_js_popup_refresh();
					}
				);
			}
		}
		
		function handlenewpost()
		{			
			var name = jQ_nxs('#newpostname').val();
			
			name = jQuery.trim(name);
			if (name == '')
			{
				nxs_js_alert('<?php nxs_l18n_e("Post name is required[nxs:growl]", "nxs_td"); ?>');
				jQ_nxs('#newpostname').focus();
			}
			else
			{
				nxs_js_addpost
				(
					name, 
					function(postname, postid)
					{
						// success
						<?php 
						if (isset($nxs_postseditor_appendnewitemsto)) 
						{ 
							// append the postid to the sessiondata for the specified id
							?>
							var previouspostlist = nxs_js_popup_getsessiondata("<?php echo $nxs_postseditor_appendnewitemsto; ?>");
							nxs_js_popup_setsessiondata("<?php echo $nxs_postseditor_appendnewitemsto; ?>", previouspostlist + "[" + postid + "]");
							nxs_js_alert(nxs_js_popup_getsessiondata("<?php echo $nxs_postseditor_appendnewitemsto; ?>"));
							<?php 
						} 
						?>
						
						nxs_js_alert('<?php nxs_l18n_e("Post added[nxs:growl]", "nxs_td"); ?> (' + postname + ')');
						
						// sla allereerst de huidige toestand van de variabelen op in de popup session data
						nxs_js_savepopupdata();
						
						// the post was inserted,
						// we refresh the pop up to see the newly inserted post
						nxs_js_popup_refresh();
					}
				);
			}
		}
	</script>
	<?php

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
  $html = nxs_ob_get_contents();
  nxs_ob_end_clean();
    
  // Setting the contents of the variable to the appropriate array position
  // The framework uses this array with its accompanying values to render the page
  $result["html"] = $html;
  return $result;
}
?>