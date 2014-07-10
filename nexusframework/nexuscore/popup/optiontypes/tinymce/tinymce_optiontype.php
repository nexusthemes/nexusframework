<?php


/*
*/

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
	$internaltextareaid = "nxs_i_textarea_" . $id . "_" . "UNIQUE"; // NXS_UNIQUEIDFORREQUEST;
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
								nxs_js_log("Received nxs_jstrigger_afterpopupshows");
								// nxs_js_log("detected: nxs_jstrigger_afterpopupshows for <?php echo $internaltextareaid; ?>");
								var scripturl = '//tinymce.cachefly.net/4.0/tinymce.min.js';
								var functiontoinvoke = 'nxs_loadplugins_tinymce_editor()';
								nxs_js_lazyexecute(scripturl, false, functiontoinvoke);
								nxs_js_log("lazyexecuted " + scripturl);
							}
						);
						
						jQuery(window).bind 
						(
							"nxs_jstrigger_beforepopupcloses", 
							function(e) 
							{
								nxs_js_log("removing tinymce evidence from the DOM");
								// 
								tinymce.remove();
								
								// remove textarea from dom
								jQuery("textarea").remove();
								
								//kljsdf();
							}
						);
						
						
						
						function tinymcekeyuphandler(e)
						{
					  	// het blijkt dat hier events door blijven komen, ook als de popup ondertussen al weer is gesloten...
					  	// de focus blijft namelijk in het iframe 'hangen'...
					  	if (nxs_js_popupshows)
					  	{								  	
		            if (e.keyCode==27) 
		            {
			            e.preventDefault();
	        				e.stopPropagation();
		            	// escape
		            	nxs_js_closepopup_unconditionally_if_not_dirty();
		            	return false;
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
						
						function nxs_tinymce_claimfocus_internal()
						{
							nxs_js_log("setting focus...");
							// sets the actual focus to the body
							jQuery('#nxs_i_textarea_text_UNIQUE_ifr').contents().find('body').focus();
						}
						
						function nxs_tinymce_claimfocus()
						{
							// the settimeout is required, otherwise the focus is not set!
							setTimeout(nxs_tinymce_claimfocus_internal, 0);
						}
		
						function nxs_loadplugins_tinymce_editor()
						{
							var scripturl = '<?php echo nxs_getframeworkurl(); ?>/js/tinymcev4/nxslinkv4.js';
							var functiontoinvoke = 'nxs_launch_tinymce_editor()';
							nxs_js_lazyexecute(scripturl, false, functiontoinvoke);
							nxs_js_log("lazyexecuted " + scripturl);
						}
		
						function nxs_tinymce_registereventhandlers(ed) 
					  {
					  	// remove any other listeners
					  	ed.off();
					  
					  	ed.on('keyup', tinymcekeyuphandler);
						  ed.on('keypress', function(e) { nxs_js_popup_sessiondata_make_dirty(); });
				      ed.on
				      (
				      	'change', 
				      	function(e) 
				      	{
						  		nxs_js_popup_setsessiondata("<?php echo $id; ?>", ed.getContent()); 
				      		nxs_js_popup_sessiondata_make_dirty();
								}
							);
							
							// set focus to the tinymce editor
							nxs_tinymce_claimfocus();
							
							nxs_js_log("finished setup of handlers");
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
								setTimeout(nxs_launch_tinymce_editor, 100);
								return;
							}
						
							tinymce.init
							(
								{
									entity_encoding : "raw",
									width: "100%",
									baseURL: "<?php echo includes_url() . "/js/tinymce"; ?>",
									relative_urls: false,
									remove_script_host: false,
									convert_urls: false,
									browser_spellcheck: true,
									fix_list_elements: true,
									entities : "38,amp,60,lt,62,gt",
									keep_styles: false,
									forced_root_block: "p",
									selector:'#<?php echo $internaltextareaid; ?>',
									fontsize_formats: "12px 15px 18px 21px 24px 27px 30px 33px 36px 39px 42px 45px 60px 75px 90px",
									menubar: false,
									theme: "modern",
							    plugins:  [
						        "advlist lists image charmap print preview anchor",
						        "searchreplace visualblocks code fullscreen",
						        "insertdatetime media table contextmenu paste link"
							    ],
						      toolbar: "undo redo | styleselect fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink | code",
							    setup: nxs_tinymce_registereventhandlers,							    
								}
							);
								
							var ed = tinyMCE.activeEditor;
							jQuery(window).trigger('nxs_tinymce_setup', ed);
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