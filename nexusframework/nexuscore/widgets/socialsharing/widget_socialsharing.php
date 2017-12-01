<?php

function nxs_widgets_socialsharing_geticonid()
{
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

function nxs_widgets_socialsharing_gettitle()
{
	return nxs_l18n__("Social sharing", "nxs_td");
}

function nxs_widgets_socialsharing_fblike_render($url)
{
	// facebook like, see http://developers.facebook.com/docs/reference/plugins/like/
	$result = '
				<div class="nxs-share nxs-fblike">
				<div class="fb-like" data-send="false" data-href="' . $url . '" data-layout="box_count" data-show-faces="false"></div>
				</div>
	';
	return $result;
}

function nxs_widgets_socialsharing_twitter_render($url, $text)
{
	if (isset($text))
	{
		$datatext = 'data-text="' . $text . '"';
	}
	else
	{
		$datatext = "";
	}
	
	// see https://dev.twitter.com/discussions/890 for solution regarding injecting reloading using ajax
	// twitter see http://twitter.com/about/resources/buttons#tweet 
	$result = '
	
	<div class="nxs-share">
	<a href="https://twitter.com/share" class="twitter-share-button" data-count="vertical" ' . $datatext . ' data-via="" data-url="' . $url . '"></a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	</div>
	<script type="text/javascript">
		if (typeof(twttr) != "undefined" && twttr != null ) 
		{
			twttr.widgets.load();
		}
	</script>
	';
	return $result;
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_socialsharing_render_webpart_render_htmlvisualization($args)
{
	//
	extract($args);
	
	global $nxs_global_row_render_statebag;

	global $nxs_global_current_containerpostid_being_rendered;
	global $nxs_global_current_postid_being_rendered;
	
	if ($nxs_global_current_containerpostid_being_rendered == "")
	{
		echo "nxs_global_current_containerpostid_being_rendered nog niet geset (SOCIALSHARE)";
		die();
	}

	// get root post that was requested (the socialsharing placeholder can be 'wrapped' in a pagelet container,
	// in that case we don't want to show the socialsharing ot the pagelet, but the socialsharing of the page requested :)
	global $post;
	$rootpostid = $post->ID;
	
	$result = array();
	$result["result"] = "OK";
		
	$currentpost = get_post($nxs_global_current_containerpostid_being_rendered);
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$mixedattributes = array_merge($temp_array, $args);
	
	$items = $mixedattributes['items'];

	global $nxs_global_placeholder_render_statebag;

	if ($render_behaviour == "code")
	{
		//
	}
	else
	{
		//	
		$hovermenuargs = array();
		$hovermenuargs["postid"] = $postid;
		$hovermenuargs["placeholderid"] = $placeholderid;
		$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
		$hovermenuargs["metadata"] = $mixedattributes;
		nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	}
	
	//
	// render actual control / html
	//
	
	nxs_ob_start();

	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-sharing";
	
	$args = array("post_id" => $rootpostid);
	$url = nxs_geturl_for_postid($rootpostid);
	?>
	<!-- -->
	
	<div <?php echo $class; ?>>
		<?php
		if ($items == "")
		{
			nxs_renderplaceholderwarning(nxs_l18n__("No media selected[nxs:warning]", "nxs_td"));
		}
		else
		{
			if (nxs_stringcontains($items, "twitter"))
			{
				echo nxs_widgets_socialsharing_twitter_render($url, "");
			}
			?>
			<?php
			if (nxs_stringcontains($items, "fblike"))
			{
				echo nxs_widgets_socialsharing_fblike_render($url);
			}
			?>
			<?php
			if (nxs_stringcontains($items, "fbshare"))
			{
				$urlcurrentpage = nxs_geturlcurrentpage();
				?>
				<!-- facebook share -->		
				<div class='nxs-share nxs-fbshare'>
					<div class="fb-share-button" data-href="<?php echo $urlcurrentpage; ?>" data-layout="box_count"></div>
				</div>
				<?php
			}
			if (nxs_stringcontains($items, "fblike") || nxs_stringcontains($items, "fbshare"))
			{
				// see http://stackoverflow.com/questions/8961567/render-like-button-after-ajax-call
				?>
				<script type='text/javascript'>
					// load and render DOM
					nxs_js_inject_facebook();
				</script>
				<?php
			}
			?>
			<?php
			if (nxs_stringcontains($items, "googleplus"))
			{
				// http://stackoverflow.com/questions/7583892/google-plus-one-button-on-a-page-that-is-refreshed-with-ajax
				?>
				<!-- google plus, zie http://www.google.com/webmasters/+1/button/ -->
				<div class='nxs-share nxs-googleplus'>
				<g:plusone size="tall"></g:plusone>
				</div>
				<script type="text/javascript">
					nxs_js_inject_googleplus();
					if (typeof(gapi) != 'undefined' && gapi != null ) 
					{
						gapi.plusone.go();
					}
				</script>
				<?php
			}
			?>
			<?php
			if (nxs_stringcontains($items, "linkedin"))
			{
				$widgeturl = nxs_getframeworkurl() . "/js/linkedin/widget.js";
				?>
				<!-- linkedin -->
				<div class='nxs-share nxs-linkedin'>
					<script src="//platform.linkedin.com/in.js" type="text/javascript"></script>
					<script type="IN/Share" data-showZero="true" data-url="<?php echo $url; ?>" data-counter="top"></script>
					<script type='text/javascript'>
						 IN.init();
					</script>
				</div>
				<?php
			}
			?>
			<?php      
			if (nxs_stringcontains($items, "pinterest"))
			{
				?>
				<!-- pinterest -->
				<?php
				$pintitle = $currentpost->post_title;
				$pinref = $url;
				global $nxs_glb_pinterestdone;
				
				?>
				<div class='nxs-share nxs-pinterest'>

					<a data-pin-do="buttonBookmark" data-pin-count="above" href="https://www.pinterest.com/pin/create/button/"><img src="//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_gray_20.png" /></a>
					<!-- Please call pinit.js only once per page -->
					<?php
					if ($nxs_glb_pinterestdone == "")
					{
						$nxs_glb_pinterestdone = "done";
						?>
						<script async defer src="//assets.pinterest.com/js/pinit.js"></script>
						<?php
					}
					?>
				</div>
				<?php
			}
		}
		?>		
		<div class='nxs-clear'></div>
	</div>
	
	<?php 
	
	if ($nxs_global_row_render_statebag == null)
	{
		echo "warning; nxs_global_row_render_statebag is null";
	}
	else
	{
		//echo "width:" . $nxs_global_row_render_statebag["width"];
		//echo "pagerowtemplate:" . $nxs_global_row_render_statebag["pagerowtemplate"];
	}
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	// outbound statebag
	// $nxs_global_row_render_statebag["foo"] = "bar";
	
	return $result;
}

function nxs_widgets_socialsharing_getallshares()
{
	$shares = array
	(
		array
		(
			"id" => "twitter",
			"name" => nxs_l18n__("Twitter[nxs:socialshareoption]", "nxs_td"),
		),
		array
		(
			"id" => "fblike",
			"name" => nxs_l18n__("Facebook Like[nxs:socialshareoption]", "nxs_td"),
		),
		array
		(
			"id" => "fbshare",
			"name" => nxs_l18n__("Facebook Share[nxs:socialshareoption]", "nxs_td"),
		),
		array
		(
			"id" => "googleplus",
			"name" => nxs_l18n__("Google+[nxs:socialshareoption]", "nxs_td"),
		),
		array
		(
			"id" => "linkedin",
			"name" => nxs_l18n__("LinkedIn[nxs:socialshareoption]", "nxs_td"),
		),
		array
		(
			"id" => "pinterest",
			"name" => nxs_l18n__("Pinterest[nxs:socialshareoption]", "nxs_td"),
		),
	);
	return $shares;
}

//
// het eerste /hoofd/ scherm dat wordt getoond in de popup als de gebruiker
// het editen van een placeholder initieert
//
function nxs_widgets_socialsharing_home_rendersheet($args)
{
	//
	extract($args);
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	$meta = nxs_getsitemeta();
	
	$items = $temp_array['items'];
	
	$allshares = nxs_widgets_socialsharing_getallshares();
	
	// clientpopupsessiondata bevat key values van de client side, deze overschrijven reeds bestaande variabelen
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);


	$result = array();
	$result["result"] = "OK";

	nxs_ob_start();

	?>

    <div class="nxs-admin-wrap">
      <div class="block">
      
       	<?php nxs_render_popup_header(nxs_l18n__("Social sharing[nxs:title]", "nxs_td")); ?>

				<div class="nxs-popup-content-canvas-cropper">
					<div class="nxs-popup-content-canvas">

						<div class="content2">
			        <div class="box">
			          <div class="box-title">
									<h4><?php nxs_l18n_e("Social shares[nxs:heading]", "nxs_td"); ?></h4>
			          </div>
			          <div class="box-content">
									<ul class="shares-checklist" id='items'>
							      <?php 
							      
							    	foreach ($allshares as $currentshare)
							    	{
							    		$shareid = $currentshare["id"];
							    		$sharename = $currentshare["name"];
			
							    		$key = "[" . $shareid . "]";
							    		if (nxs_stringcontains($items, $key))
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
				            			<input class='selectable_share' id="shareid_<?php echo $shareid; ?>" type="checkbox" <?php echo $possiblyselected; ?> />
				            			<?php echo $sharename; ?>
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
		            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save[nxs:button]", "nxs_td"); ?></a>
		            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:button]", "nxs_td"); ?></a>
		            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:button]", "nxs_td"); ?></a>
		          </div>
		        	<div class="nxs-clear margin"></div>
		      	</div> <!--END content-->
		        
      		</div>
    		</div>
    	</div>
    </div>

    <script type='text/javascript'>
				
		function nxs_js_setpopupdatefromcontrols()
		{
			nxs_js_popup_storestatecontroldata_listofcheckbox('items', 'selectable_share', 'items');
		}
		
		function nxs_js_savegenericpopup()
		{
			nxs_js_setpopupdatefromcontrols();
			
			nxs_js_log(nxs_js_popup_getsessiondata('items'));
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updateplaceholderdata",
						"placeholderid": "<?php echo $placeholderid;?>",
						"postid": "<?php echo $postid;?>",
						"placeholdertemplate": "socialsharing",
						"items": nxs_js_popup_getsessiondata('items')
					},
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							nxs_js_alert('<?php nxs_l18n_e("Data updated[nxs:tooltip]", "nxs_td"); ?>');
							
							// update UI, the 'current' id will be overriden because null is specified as third parameter
							nxs_js_rerender_row_for_placeholder("<?php echo $postid;?>", "<?php echo $placeholderid;?>");
														
							// close the pop up
							nxs_js_closepopup_unconditionally();
						}
						else
						{
							nxs_js_popup_notifyservererror();
							nxs_js_log(response);
						}
					},
					error: function(response)
					{
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
					}										
				}
			);
		}
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#items').focus();
		}
		
	</script>

<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	$result["html"] = $html;
	
	return $result;    
}

//
// wordt aangeroepen bij het initialiseren van data van deze placeholder
//
function nxs_widgets_socialsharing_initplaceholderdata($args)
{
	extract($args);

	$args['items'] = "[twitter][fblike][fbshare][googleplus][linkedin][pinterest]";	// all are turned on
	
	nxs_widgets_socialsharing_updateplaceholderdata($args);

	$result = array();	
	$result["result"] = "OK";
	
	return $result;
}

//
// wordt aangeroepen bij het opslaan van data van deze placeholder
//
function nxs_widgets_socialsharing_updateplaceholderdata($args)
{
	extract($args);
	
	$temp_array = array();
	
	// its required to also set the 'type' (used when dragging an item from the toolbox to existing placeholder)
	$temp_array['type'] = 'socialsharing';

	// placeholder specifieke data
	$temp_array['items'] = $items;
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $temp_array);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
