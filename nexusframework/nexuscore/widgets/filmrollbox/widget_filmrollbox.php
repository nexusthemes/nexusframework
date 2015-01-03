<?php

function nxs_widgets_filmrollbox_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_filmrollbox_gettitle() {
	return nxs_l18n__("filmrollbox[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_filmrollbox_getunifiedstylinggroup() {
	return "filmrollboxwidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_filmrollbox_home_getoptions($args) 
{
	$options = array
	(
		"sheettitle" => nxs_widgets_filmrollbox_gettitle(),
		"sheeticonid" => nxs_widgets_filmrollbox_geticonid(),
		"sheethelp" => nxs_l18n__("http://nexusthemes.com/slider-widget/"),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_filmrollbox_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			// SLIDES			
			
			array( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Slides", "nxs_td"),
			),
			
			array(
				"id" 				=> "items_genericlistid",
				"type" 				=> "staticgenericlist_link",
				"label" 			=> nxs_l18n__("Edit slides", "nxs_td"),
			),
			array(
				"id" 				=> "item_durationvisibility",	
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Visibility duration", "nxs_td"),
				"dropdown" 			=> nxs_convertindexarraytoassociativearray(array("3000","4000","5000","6000","9000","12000")),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "item_transitionduration",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("item_transitionduration", "nxs_td"),
				"dropdown" 			=> nxs_convertindexarraytoassociativearray(array("200","300","400")),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "slider_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Slider width", "nxs_td"),
				"dropdown" 			=> array("100%"=>"100%","90%"=>"90%","80%"=>"80%","70%"=>"70%","60%"=>"60%","50%"=>"50%"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image border size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "pause_transition",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Pause transition on hover", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Slide shadow", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "fullwidth_height",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Height of each slide", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("None", "nxs_td"),
					"300px" => nxs_l18n__("300px", "nxs_td"),
					"400px" => nxs_l18n__("400px", "nxs_td"),
					"500px" => nxs_l18n__("500px", "nxs_td"),
					"600px" => nxs_l18n__("600px", "nxs_td"),
					"screenheight" => nxs_l18n__("Height of screen", "nxs_td"),
				),
				"tooltip" 			=> nxs_l18n__("This option set's the height of the caption container between the header and the rest of the content", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "widescreen_slider",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Widescreen slider ", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "responsive_display",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Responsive display", "nxs_td"),
				"dropdown" 			=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Always", "nxs_td"),
					"display480" => nxs_l18n__("480", "nxs_td"),
					"display720" => nxs_l18n__("720", "nxs_td"),
					"display960" => nxs_l18n__("960", "nxs_td"),
					"display1200" => nxs_l18n__("1200", "nxs_td"),
				),
				"tooltip" 			=> nxs_l18n__("This option let's you set the sliders display at a certain viewport and up", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend"
			),
			
			// NAVIGATION AND METADATA
			
			array( 
				"id" 				=> "wrapper_input_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Navigation and metadata", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "metadata",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Metadata", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "main_controllers",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Main controllers", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "bgcolor",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Wrapper background", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_input_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
		)
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}


/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_filmrollbox_render_webpart_render_htmlvisualization($args) 
{	
	// Importing variables
	extract($args);
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "") {
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_filmrollbox_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	
	global $nxs_global_row_render_statebag;
	
	$items_genericlistid = $mixedattributes['items_genericlistid'];

	/* HOVER MENU HTML
	---------------------------------------------------------------------------------------------------- */
	
	ob_start();

	?>
	<ul>
		<?php 
		if ($items_genericlistid == "") 
		{ 
			?>
			<li title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>'>
				<a href='#' class='nxs-defaultwidgetclickhandler' title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>' onclick="nxs_js_edit_widget(this); return false;">
					<span class='nxs-icon-plug'></span>
				</a>
			</li>
			<?php 
		} 
		else 
		{
			global $nxs_global_current_containerpostid_being_rendered;
			$currentcontainerposturl = nxs_geturl_for_postid($nxs_global_current_containerpostid_being_rendered);
			$nxsrefurlspecial = urlencode(base64_encode($currentcontainerposturl));
			
			//$nxsrefurlspecial = urlencode(base64_encode(nxs_geturl_for_postid($postid)));
			$refurl = nxs_geturl_for_postid($items_genericlistid);
			$refurl = nxs_addqueryparametertourl_v2($refurl, "nxsrefurlspecial", $nxsrefurlspecial, false);
			?>
			<a href='#' class='nxs-defaultwidgetclickhandler' title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>' onclick="var url='<?php echo $refurl; ?>'; nxs_js_redirect(url); return false;">
				<li title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>'>
					<span class='nxs-icon-filmrollbox'></span>
				</li>
			</a>
			<a href='#' title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>' onclick="nxs_js_edit_widget(this); return false;">
				<li title='<?php nxs_l18n_e("Edit[nxs:tooltip]", "nxs_td"); ?>'>
					<span class='nxs-icon-plug'></span>
				</li>
			</a>
			<?php 
		} 
		?>
		<li title='<?php nxs_l18n_e("Move[nxs:tooltip]", "nxs_td"); ?>' class='nxs-draggable nxs-existing-pageitem nxs-dragtype-placeholder' id='draggableplaceholderid_<?php echo $placeholderid; ?>'>
			<span class='nxs-icon-move'></span>
			<div class="nxs-drag-helper" style='display: none;'>
				<div class='placeholder'>
					<span id='placeholdertemplate_<?php echo $placeholdertemplate; ?>' class='<?php echo nxs_widgets_filmrollbox_geticonid();?>'></span>
				</div>
			</div>					
		</li>
		<a class='nxs-no-event-bubbling' href='#' onclick='nxs_js_popup_placeholder_wipe("<?php echo $postid; ?>", "<?php echo $placeholderid; ?>"); return false;'>
			<li title='<?php nxs_l18n_e("Delete[nxs:tooltip]", "nxs_td"); ?>'><span class='nxs-icon-trash'></span></li>
		</a>
		<?php 
		if (nxs_shoulddebugmeta())
		{
			ob_start();
			?>
			<a class='nxs-no-event-bubbling' href='#' onclick="nxs_js_edit_widget_v2(this, 'debug'); return false; return false;">
       	<li title='<?php nxs_l18n_e("Debug[tooltip]", "nxs_td"); ?>'>
       		<span class='nxs-icon-search'></span>
       	</li>
    	</a>
    	<?php
    	$debughtml = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			$debughtml = "";
		}
		echo $debughtml;
		?>
	</ul>
	<?php 
	
	$menu = ob_get_contents();
	ob_end_clean();
	
	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["menutopright"] = $menu;
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	// TRANSITION DURATION
	if ($item_transitionduration == "") { $item_transitionduration = "2500"; }
	if ($item_durationvisibility == "") { $item_durationvisibility = "5000"; }
	
	// PAUSE TRANSITION
	if ($pause_transition == "") 	{ $pause_transition = "false"; }
	else 							{ $pause_transition = "true"; }
	
	// SLIDER WIDTH
	$margin = (100% - $slider_width)/2;
	$margin_left = 'margin-left: '.$margin.'%;';
	$margin_right = 'margin-right: '.$margin.'%;';
	$slider_width = 'max-width: '.$slider_width.' !important;';
	
	// SLIDE SHADOW
	if ($shadow != "") 	{ $shadow = "nxs-shadow"; }
	
	// Background Color
	if ($bgcolor == "") { $bgcolor = 'base2-a0-6'; }
	$bgcolor_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $bgcolor);
	
	// SLIDESHOW BORDER
	
		// Multiplier
		if (strlen($border_width) > 3) {
			$multiplier = substr($border_width, -4, 2);
		} else {
			$multiplier = substr($border_width, -3, 1);
		}
		// Border width
		settype($multiplier, "integer");
		$factor = 3;
		$border_width = $multiplier * $factor; 
		$slide_border_width = 'box-shadow: inset 0 0 0 '.$border_width.'px white;';
	
	// MAIN CONTROLLERS
	if ($metadata != "" && $main_controllers != "") {
		$main_controllers = '<div id="controllers_'.$placeholderid.'" class="nxs-slider-controller" style="bottom: '.($border_width + 16).'px; right: '.$border_width.'px; '.$margin_right.'"></div>';	
	} else if ($metadata == "" && $main_controllers != "") {
		$main_controllers = '
			<div class="main-controllers-bg '.$bgcolor_cssclass.'" style="bottom: '.$border_width.'px; '.$slider_width.' '.$margin_left.'"></div>
			<div id="controllers_'.$placeholderid.'" class="nxs-slider-controller fullwidth" style="bottom: '.($border_width + 16). 'px;"></div>';
	}
	
	// SIDE CONTROLLERS
	if ($side_controllers != "") {
		$side_controllers = '
		<!-- left -->
		<a id="' .$placeholderid.'" class="nxs-slider-prev"><span></span></a>
		<!-- right -->
		<a id="next_' .$placeholderid.'" class="nxs-slider-next"><span></span></a>		
		';
	}
		
	// FULLWIDTH PARAMS
	if ($fullwidth_height != "") {
		if ($fullwidth_height == "screenheight") {
			$slideset_inlinestyle = 'overflow: hidden;'; // height: 99px !important;';
			$slide_img_inlinestyle = ''; // 'min-height: 99px !important;';
		} else  {
			if (!nxs_stringendswith($fullwidth_height, "px")) {
				$fullwidth_height = $fullwidth_height . "px";
			} else {
				// already is defined in px
			}
		
			$slideset_inlinestyle = 'overflow: hidden; height: '.$fullwidth_height.' !important;';
			$slide_img_inlinestyle = 'min-height: '.$fullwidth_height.' !important;';
		}
	}
	
	// WIDESCREEN 
	if ($widescreen_slider != "") {
		// forceer dat bij het renderen (in de shortcode) de regel
		// als een fullwidth wordt getekend
		$nxs_global_row_render_statebag["upgradetowidescreen"] = "yes";	
	}
	
	ob_start();
	
	// Default name class
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-slider ";
	
	// Default media query display
	$nxs_global_placeholder_render_statebag["widgetclass"] .= " " . $responsive_display;
	
	if (false) { // $existingsidebarid != "") {
		if (nxs_has_adminpermissions()) {
			nxs_renderplaceholderwarning(nxs_l18n__("Slider can only be used when the sidebar is suppressed[nxs:warning]", "nxs_td"));
		}
	} else if ($items_genericlistid == "") {
		if (nxs_has_adminpermissions()) {
			nxs_renderplaceholderwarning(nxs_l18n__("No slides connected[nxs:warning]", "nxs_td"));
		}
	} else if (true) { // $nxs_global_row_render_statebag["pagerowtemplate"] == "one") {
		
		// we markeren dat deze regel geen filler moet krijgen (wordt verwerkt door shortcode)
		
		//$nxs_global_row_render_statebag["suppressfilleratrow0"] = true;
		// do not crop the widget, the slider contains left/right buttons that exceed the regular widget boundaries
		
		$slidesdataset = array();
		
		$structure = nxs_parsepoststructure($items_genericlistid);
		
		$slideindex = 0;
		$heighttallestslide = 0;
		foreach ($structure as $pagerow) {
			$content = $pagerow["content"];
			
			$slideplaceholderid = nxs_parsepagerow($content);

			$placeholdermetadata = nxs_getwidgetmetadata($items_genericlistid, $slideplaceholderid);
			$placeholdertype = $placeholdermetadata["type"];					
			
			if ($placeholdertype == "") {
				// ignore
			} else if ($placeholdertype == "undefined") {
				// ignore
			} else if ($placeholdertype == "slide") {
				$image_imageid = $placeholdermetadata['image_imageid'];
				$lookup = wp_get_attachment_image_src($image_imageid, 'full', true);
				$imageurl = $lookup[0];
				$imageurl = nxs_img_getimageurlthemeversion($imageurl);
				$width = $lookup[1];
				$height = $lookup[2];		
				
				if ($height > $heighttallestslide) {
					$heighttallestslide = $height;
				}
				
				$title = $placeholdermetadata['title'];
				$text = $placeholdermetadata['text'];
				$destination_articleid = $placeholdermetadata['destination_articleid'];
				
				if ($destination_articleid != 0 && $destination_articleid != "") 
				{
					$destinationurl = nxs_geturl_for_postid($destination_articleid);
				} 
				else
				{
					$destination_url = $placeholdermetadata['destination_url'];
					if ($destination_url != "")
					{
						$destinationurl = $destination_url;
					}
					else 
					{
						$destinationurl = "";
					}
				}
				
				$slidesdataset[] = array
				(
					"title" => $title,
					"text" => $text,
					"imageurl" => $imageurl,
					"destinationurl" => $destinationurl,
					"width" => $width,
					"height" => $height,
				);
			} else {
				echo "Placeholdertype is not (yet?) supported;a[" . $placeholdertype . "]";
			}
		}
				
		if (count($slidesdataset) > 0) 
		{
			// output slides
			?>
			<div id='nxs_filmroll_<?php echo $placeholderid; ?>'>
				<?php 
				foreach($slidesdataset as $currentslide)
				{
					/*
					"title" => $title,
					"text" => $text,
					"imageurl" => $imageurl,
					"destinationurl" => $destinationurl,
					"width" => $width,
					"height" => $height,
					*/
					if ($currentslide["destinationurl"] != "")
					{
						// render link
						$destinationurl = $currentslide["destinationurl"];
						?>
						<div>
							<a href='<?php echo $destinationurl; ?>'>
								<img src="<?php echo $currentslide["imageurl"]; ?>">
							</a>
						</div>
						<?php						
					}
					else
					{
						// dont render a link
						?>
						<div>
							<img src="<?php echo $currentslide["imageurl"]; ?>">
						</div>
						<?php
					}
					?>
					<?php
				}
				?>
			</div>
			
			<script type='text/javascript'>
				// load filmroll js script and start the film!
				jQuery(document).ready (
					function() {
						nxs_js_requirescript('filmroll_js', 'js', '<?php echo nxs_getframeworkurl();?>/nexuscore/widgets/filmrollbox/js/jquery.film_roll.js', nxs_js_filmrollbox_js_loaded_<?php echo $placeholderid; ?>);
					}
				);
				
				function nxs_js_filmrollbox_js_loaded_<?php echo $placeholderid; ?>()
				{
					this.film_rolls || (this.film_rolls = []);
			    this.film_rolls['nxs_filmroll_<?php echo $placeholderid; ?>'] = new FilmRoll
			    (
			    	{
				      container: '#nxs_filmroll_<?php echo $placeholderid; ?>',
				      interval: 2000,
				      hover: false,	// false: ignore hover, true: hover = pause
				      //height: 100,
				      pager: false
				    }
				  );
				}
			</script>
      <?php
		} 
		else 
		{
			if (nxs_has_adminpermissions()) 
			{
				nxs_renderplaceholderwarning(nxs_l18n__("No slides have been added.[nxs:tooltip]", "nxs_td"));
			}
		}
	} else { 
		nxs_renderplaceholderwarning(nxs_l18n__("Slider can only be used in a row with a single cell[nxs:warning]", "nxs_td"));
	}
	
	$html = ob_get_contents();
	ob_end_clean();

	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-'.$placeholderid;

	// outbound statebag
	global $nxs_global_row_render_statebag;
	if ($nxs_global_row_render_statebag["pagerowtemplate"] == "one")
	{
		// forceer dat bij het renderen (in de shortcode) de regel
		// als een fullwidth wordt getekend
		// $nxs_global_row_render_statebag["upgradetofullwidth"] = "ja";
	}	
	else
	{
		// het item is op een verkeerde plaats neergezet,
		// we promoten hierbij express niet naar een fullwidth
	}

	return $result;
}

/* INITIATING WIDGET DATA
----------------------------------------------------------------------------------------------------*/
function nxs_widgets_filmrollbox_initplaceholderdata($args)
{
	// delegate to generic implementation
	$widgetname = basename(dirname(__FILE__));
	
	// create a new generic list with subtype gallery
	// assign the newly create list to the list property
	
	$subargs = array();
	$subargs["nxsposttype"] = "genericlist";
	$subargs["nxssubposttype"] = "filmrollbox";	// NOTE!
	$subargs["poststatus"] = "publish";
	$subargs["titel"] = nxs_l18n__("Slider items[title]", "nxs_td");
	$subargs["slug"] = nxs_l18n__("Slider items[slug]", "nxs_td");
	$subargs["postwizard"] = "defaultgenericlist";
	
	$response = nxs_addnewarticle($subargs);
	if ($response["result"] == "OK")
	{
		$args["items_genericlistid"] = $response["postid"];
		$args["items_genericlistid_globalid"] = nxs_get_globalid($response["postid"], true);
	}
	else
	{
		var_dump($response);
		die();
	}
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_filmrollbox_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
	
	$args["item_durationvisibility"] = "5000";
	$args["item_transitionduration"] = "300";
	$args['ph_margin_bottom'] = "0-0";
	$args['bgcolor_cssclass'] = "base2";
	
	$result = nxs_widgets_initplaceholderdatageneric($args, $widgetname);
	return $result;
}

?>
