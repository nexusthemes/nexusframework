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
								var scripturl = '/js/tinymce/jscripts/tiny_mce/tiny_mce.js';
								var functiontoinvoke = 'gogoeditor_<?php echo $internaltextareaid; ?>()';
								nxs_js_lazyexecute(scripturl, true, functiontoinvoke);
							}
						);
		
						function nxsremoveeditor_<?php echo $internaltextareaid; ?>()
						{									
							// remove editor
							//nxs_js_log('before remove');
							tinyMCE.execCommand('mceRemoveControl', false, '<?php echo $internaltextareaid; ?>');
							//nxs_js_log('after remove');
						}
						
						function gogoeditor_<?php echo $internaltextareaid; ?>()
						{
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
								setTimeout(gogoeditor_<?php echo $internaltextareaid; ?>, 500);
								return;
							}
						
							//nxs_js_log('gogoeditor invoked for <?php echo $internaltextareaid; ?>');
							
							// mocht hij toch al /nog?/ bestaan, verwijder 'm dan
							nxsremoveeditor_<?php echo $internaltextareaid; ?>();
							
							var plugins = "paste,nexuslink,nexusemotion,advlist,wordcount,autoresize,inlinepopups";
							if (true)
							{
								tinyMCE.init
								(
									{
									 	 script_url : "<?php echo nxs_getframeworkurl(); ?>/js/tinymce/jscripts/tiny_mce/tiny_mce.js",
										  mode: "none",
										  theme: 'advanced',
										  // see http://www.tinymce.com/wiki.php/Creating_a_theme
										  handle_event_callback: editoreventcallbackfunction_<?php echo $internaltextareaid; ?>,
										  width: "100%",
											setup: setupeditorfunction_<?php echo $internaltextareaid; ?>,
										  skin: 'nexus',
										  plugins: plugins,
										  
										  theme_advanced_buttons1 : "fontsizeselect,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,cut,copy,paste|,bullist,numlist,|,link,unlink,code",
										  theme_advanced_buttons2 : "",
										  theme_advanced_buttons3 : "",
										  theme_advanced_toolbar_location : "top",
										  theme_advanced_layout_manager : "SimpleLayout",
										  theme_advanced_toolbar_align : "left",
										  theme_advanced_path : "false",
										  theme_advanced_statusbar_location : "bottom",
										  theme_advanced_resizing : true,
										  theme_advanced_resize_horizontal: false,
										  //theme_advanced_resize_vertical: true,
										  fix_list_elements : true,
										  valid_styles : {'*' : 'color,font-size,font-weight,font-style,text-decoration,text-align'},
										  paste_use_dialog : true,
										  paste_auto_cleanup_on_paste : true,
										  extended_valid_elements : "style,a[id|name|href|target|title|onclick|class],hr[width|size|noshade],span[id|align|style|class],h1,h2,h3,h4,h5,h6",
										  //content_css : "/wysiwyg_editor_rendering.css",
										  traileritem : true,
										  // do not promote spaces to non breaking spaces											  
										  // http://www.abeautifulsite.net/blog/2009/12/tinymce-removes-non-breaking-spaces/
										  entity_encoding: 'named',
											entities: '160,nbsp',
											dialog_type : "modal",
											forced_root_block : "p"
									}
								);
								
								tinyMCE.execCommand('mceAddControl', false, '<?php echo $internaltextareaid; ?>');
								
								//nxs_js_log('gogoeditor finished for <?php echo $internaltextareaid; ?>');
							}
						}
		
						function editoreventcallbackfunction_<?php echo $internaltextareaid; ?>(e)
					  {
							//nxs_js_log('editoreventcallbackfunction(inst);');
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
						
						function setupeditorfunction_<?php echo $internaltextareaid; ?>(ed) 
					  {
				      ed.onKeyPress.add(function(ed, e) 
				      {
				        nxs_js_popup_sessiondata_make_dirty();
				      });
				      
				      ed.onChange.add
				      (
				      	function(ed, l) 
				      	{
				      		nxs_js_popup_setsessiondata("<?php echo $id; ?>", l.content); 
				      		nxs_js_popup_sessiondata_make_dirty();
		        			//console.debug('Editor contents was modified. Contents: ' + l.content);
								}
							);
							
							ed.onInit.add
							(
								function(nxseditor) 
								{
									// sets the initial content of the editor
									// we cannot use the textarea for this, as the text area only is bound the very first time 
									// the editor is initialized
									// using this approach reopening the popup screens will be OK
									<?php
										$content = $value;
										$content = str_replace("\n", "", $content);
										$content = str_replace("\r", "", $content);
										$content = str_replace("'", "&#39;", $content);

										// on steve's text editor error occurs when
										// using text containing Right Single Quotation Mark; '&rsquo;';    
										$content = str_replace(chr("226") . chr('128') . chr('153'), "&rsquo;", $content);	
										
										// on sabine's text editor strange 226 chars are introduced at various places...
										// causing the output to be interpreted as having
										// newline chars, causing the JS to crash
										
										$content = str_replace(chr("226"), "", $content);
										//226 + 128 + 153
										
										// on steve's text editor strange chars are introduced at various places...
										// causing the output to be interpreted as having
										// newline chars, causing the JS to crash
										//$content = str_replace(chr("226"), "", $content);										
									?>
									var content = '<?php echo $content ;?>';
									
									nxseditor.setContent(content, { format: 'raw' });
									
									<?php if (isset($focus) && $focus == "true") { ?>
				        	nxseditor.focus();
				        	<?php } ?>
				        	
				        	// if the user hits the "code" ("html") button,
				        	// we mark the session as dirty, even though 
				        	// no modifications are made. This is because we 
				        	// haven't found a proper way to detect if the DOM
				        	// was adjusted
				        	
				        	//
				        	// note that the buttons that are showing op TOP of the 
				        	// tinymce editor are in fact stored in the DOM of the 
				        	// main HTML doc, they are NOT stored in the iframe,
				        	// this makes it possible to store a sessiondata object
				        	// pointing to the "current" optionid. This fact enables
				        	// us to change the session variables from the plugin!
				        	//
				        	
				        	jQuery(".nxs-optionid-<?php echo $id;?> .mceButton.mceButtonEnabled.mce_code").click
				        	(
				        		function()
				        		{
				        			nxs_js_popup_sessiondata_make_dirty();
				        			nxs_js_popup_setsessiondata("nxs_tinymce_invoker_optionid", "<?php echo $id;?>");
				        		}
				        	);
				        	
				        	// reset dimensions of popup after tinymce is loaded 
				        	nxs_js_reset_popup_dimensions();
				        	
				        	// rather "lame" workaround for #567; ensure the dimensions of the popup
				        	// OK, even if the height of the popup is adjusted 
				        	// after we reach this point...
				        	
							  	// repeat after 500 msecs
							  	setTimeout(nxs_js_reset_popup_dimensions, 500);
							  	// repeat after 1 sec
							  	setTimeout(nxs_js_reset_popup_dimensions, 1000);
							  	// repeat after 5 secs
							  	setTimeout(nxs_js_reset_popup_dimensions, 5000);
							  	// repeat after 10 secs
							  	setTimeout(nxs_js_reset_popup_dimensions, 10000);							        	
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
	
	nxsremoveeditor_<?php echo $internaltextareaid;?>();
	
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