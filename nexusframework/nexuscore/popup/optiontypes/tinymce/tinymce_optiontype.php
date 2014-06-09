<?php
function nxs_popup_optiontype_tinymce_renderhtmlinpopup($optionvalues, $args, $runtimeblendeddata) 
{
	extract($optionvalues);
	extract($args);
	extract($runtimeblendeddata);
	$value = $$id;	// $id is the parametername, $$id is the value of that parameter

	// the random id is needed, to solve a very strange bug since it looks like
	// the tinymce editor instance is not yet correctly removed from the DOM
	// to "solve" this problem, we create a unique id for each request,
	// this id is also used in one of the other functions below,
	// so we therefore introduce/use the NXS_UNIQUEIDFORREQUEST variable that is set
	// when the system uses the framework for the first time
	$internaltextareaid = "nxs_i_textarea_" . $id . "_" . NXS_UNIQUEIDFORREQUEST;
	//
	
	?>
	<div class="content2">
		<div class="box">
			<?php echo nxs_genericpopup_getrenderedboxtitle($optionvalues, $args, $runtimeblendeddata, $label, $tooltip); ?>
			<div class="box-content"> 
			</div>
		</div>
		<div class="nxs-clear"></div>
		<div class="box">			
      <div class="">
				<div class="nxs-optionid-<?php echo $id;?> nxs-optionid">
		      <textarea style='display: block;' id="<?php echo $internaltextareaid; ?>" nxs-option-id="<?php echo $id;?>" name="<?php echo $internaltextareaid; ?>" cols="50" rows="15" ><?php echo $value; ?></textarea>
		      
					<script type="text/javascript">
						jQuery(window).bind 
						(
							"nxs_jstrigger_afterpopupshows", 
							function(e) 
							{
								// nxs_js_log("detected: nxs_jstrigger_afterpopupshows for <?php echo $internaltextareaid; ?>");
								var scripturl = '//tinymce.cachefly.net/4.0/tinymce.min.js';
								var functiontoinvoke = 'nxs_loadplugins_tinymce_editor()';
								nxs_js_lazyexecute(scripturl, false, functiontoinvoke);
								nxs_js_log("lazyexecuted " + scripturl);
							}
						);
		
						function nxs_loadplugins_tinymce_editor()
						{
							var scripturl = '<?php echo nxs_getframeworkurl(); ?>/js/tinymcev4/nxslinkv4.js';
							var functiontoinvoke = 'nxs_launch_tinymce_editor()';
							nxs_js_lazyexecute(scripturl, false, functiontoinvoke);
							nxs_js_log("lazyexecuted " + scripturl);
						}
		
						function nxs_launch_tinymce_editor()
						{
							nxs_js_log("nxs_launch_tinymce_editor");
							
							// bugfix: when multiple tinymce instances are on the same popup,
							// it appears the first one loads perfect, but the ones following
							// give an error because the tinyMCE instance is not yet defined
							// this is solved by using a lazy load approacy
							if(typeof tinyMCE != 'undefined')
							{
								//nxs_js_log('looks like tinyMCE exists');
							}
							else
							{
								// nxs_js_log('Delayed execution required for TinyMCE, having to wait till tinyMCE object is initialized... retrying...');
								setTimeout(nxs_launch_tinymce_editor, 500);
								return;
							}
							
							
						
							// Remove any editor occurences, should they (still?) exist
							tinymce.remove();
							
							var plugins = "paste,advlist,wordcount,code,example";
							if (true)
							{
								tinymce.init
								(
									{
										entity_encoding : "raw",
										width: "100%",
										baseURL: "<?php echo includes_url() . "/js/tinymce"; ?>",
										selector:'#<?php echo $internaltextareaid; ?>',
										menubar: false,
										setup: setupeditorfunction,
										theme: "modern",
								    plugins: plugins,
								    toolbar1: "bold code example",
									}
								);
								
								//nxs_js_log('gogoeditor finished for <?php echo $internaltextareaid; ?>');
							}
						}
		
						function editoreventcallbackfunction(e)
					  {
							nxs_js_log('call back');
							//nxs_js_log(e);
							
					  	// het blijkt dat hier events door blijven komen, ook als de popup ondertussen al weer is gesloten...
					  	// de focus blijft namelijk in het iframe 'hangen'...
					  	if (nxs_js_popupshows)
					  	{								  	
		            if (e.keyCode==27) 
		            {
		            	// escape
		            	nxs_js_closepopup_unconditionally_if_not_dirty();
		            }
		            else if (e.keyCode==8) 
		            {
		            	// backspace
		            	nxs_js_popup_sessiondata_make_dirty();
		            }
		            else if (e.keyCode==46) 
		            {
		            	// delete
		            	nxs_js_popup_sessiondata_make_dirty();
		            }
		            else
		            {
		            	//nxs_js_log(e);
		            	//nxs_js_popup_sessiondata_make_dirty();
		            }
		          }
		          else
		          {
		          	// zeer belangrijk; we zetten de focus op de parent (oftewel de pagina die het iframe bevat van tinymce,
		          	// zodat daar weer keyboard events op binnenkomen), als we dat niet doen, dan blijft de focus op het iframe staan
		          	parent.focus();
		          } 
			      }
						
						function setupeditorfunction(ed) 
					  {
					  	ed.on('keydown', editoreventcallbackfunction);		

						  ed.on
						  (
						  	'keypress', function(e) 
						  	{
							  	nxs_js_log(e);
				        	nxs_js_popup_sessiondata_make_dirty();
				      	}
				      );
				      
				      ed.on
				      (
				      	'change', 
				      	function(e) 
				      	{
						  		nxs_js_popup_setsessiondata("<?php echo $id; ?>", ed.getContent()); 
				      		nxs_js_popup_sessiondata_make_dirty();
								}
							);
							
							ed.on
							(
								'init',
								function(nxseditor) 
								{
									nxs_js_log("init");
									nxs_js_log(ed);
									nxs_js_log(nxseditor);
								}
				    	);
							
				   	}
					</script>
				</div>
				</div>
			</div>
		</div>
		<div class="nxs-clear"></div>
	<?php
}

function nxs_popup_optiontype_tinymce_renderstorestatecontroldata($optionvalues)
{
	$id = $optionvalues["id"];
	$internaltextareaid = "nxs_i_textarea_" . $id . "_" . NXS_UNIQUEIDFORREQUEST;
	?>
	// persist all tinyMCE editors data back to textarea(s)
	tinyMCE.triggerSave();

	// Remove all editors
	tinymce.remove();	
	
	nxs_js_popup_storestatecontroldata_textbox('<?php echo $internaltextareaid; ?>', '<?php echo $id;?>');
	
	<?php
}

function nxs_popup_optiontype_tinymce_getitemstoextendbeforepersistoccurs($optionvalues, $args)
{
	$result = array();
	return $result;
}

// returns the behaviour of this optiontype,
// possible values are 
// "readonly" - nothing is/will be written
// "writeid" - persisting will store state in a single "id" field
function nxs_popup_optiontype_tinymce_getpersistbehaviour()
{
	return "writeid";
}

?>