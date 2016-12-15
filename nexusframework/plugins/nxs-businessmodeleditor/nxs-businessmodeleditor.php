<?php
/*
Plugin Name: Nexus Business Model Editor
Version: 1.0.0
Plugin URI: http://nexusthemes.com
Description: Helper
Author: Gert-Jan Bark
Author URI: http://nexusthemes.com
*/

function nxs_businessmodeleditor_init()
{
	if (!defined('NXS_FRAMEWORKLOADED'))
	{
		function nxs_businessmodeleditor_frameworkmissing() {
	    ?>
	    <div class="error">
	      <p>The nxs_businessmodeleditor plugin is not initialized; NexusFramework dependency is missing (hint: activate a WordPress theme from NexusThemes.com first)</p>
	    </div>
	    <?php
		}
		add_action( 'admin_notices', 'nxs_businessmodeleditor_frameworkmissing' );
		return;
	}
  
	// widgets
	nxs_lazyload_plugin_widget(__FILE__, "entity");
	
	// if this is an API call, delegate it
	if 
	(
		$_REQUEST["nxs"] == "businessmodel-api" || 
		false
	)
	{
		require_once("nxs-api-dispatcher.php");
		echo "<br />Nexus API Dispatcher Error #87432";
		// if we reach this stage, the api didn't die
		die();
	}
}
add_action("init", "nxs_businessmodeleditor_init");

function nxs_businessmodeleditor_getwidgets($result, $widgetargs)
{
	$nxsposttype = $widgetargs["nxsposttype"];
	$pagetemplate = $widgetargs["pagetemplate"];

	/* GENERIC LISTS POSTTYPE
	---------------------------------------------------------------------------------------------------- */
	
	if ($nxsposttype == "genericlist") 
	{
		$nxssubposttype = $widgetargs["nxssubposttype"];
		
		

		error_log("nxs_businessmodeleditor_getwidgets now; $nxsposttype sub: $nxssubposttype");
	
		$shouldadd = false;
		
		if ($nxssubposttype == "")
		{
			// exceptional case; if the widget was deleted, and the undefined widget
			// is used, someway the nxssubposttype is not set
			$shouldadd = true;
		}
		
		// bijv. service_set
		$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
		foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
		{
		 	if ($taxonomymeta["arity"] == "n")
		 	{
		 		$singular = $taxonomymeta["singular"];
		 		
		 		if ($nxssubposttype == "{$singular}_set") 
		 		{
		 			$shouldadd = true;
		 			break;
		 		}
		 		else
		 		{
		 		}
		 	}
		}

		if ($shouldadd)
		{		
			$result[] = array("widgetid" => "entity", "tags" => array("businessmodeleditor"));
		}
	}
	
	

	//		
	return $result;
}
add_action("nxs_getwidgets", "nxs_businessmodeleditor_getwidgets", 10, 2);	// default prio 10, 2 parameters (result, args)

// -------

if (is_admin())
{
	function nxs_businessmodelmetabox_callback($post)
	{
		// icon property
		if (true)
		{
			// load the icon font
			$nxs_entityicon = get_post_meta($post->ID, 'nxs_entityicon', true);
			require_once(NXS_FRAMEWORKPATH . '/nexuscore/license/license.php');
			$licensekey = nxs_license_getlicensekey();
			add_thickbox();
			?>
			<script>
				function nxs_js_filliconcontainer()
				{
					var licensekey = '<?php echo $licensekey; ?>';
					var url = 'https://mediamanager.websitesexamples.com/api/1/prod/icons/?callback=?';
					
					jQuery.getJSON
					(
						url,
						{
							nxs : "icons-api",
							licensekey: licensekey,
						},
						nxs_js_handlegeticonsresult
					);
				}
				
				function nxs_js_iconselected(anchor)
				{
					var value = jQuery(anchor).data("id");
					nxs_js_iconsetpreview(value);
					
					// close popup
					jQuery("#TB_closeWindowButton").click();
				}
				
				function nxs_js_iconsetpreview(value)
				{
					// remove all classes
					jQuery('#nxs-entity-icon-preview').removeClass();
					// re-decorate
					jQuery('#nxs-entity-icon-preview').addClass("nxs-icon");
					jQuery('#nxs-entity-icon-preview').addClass(value);
					
					// update hidden field
					jQuery("#nxs_entity_icon_input").val(value);
				}
				
				function nxs_js_handlegeticonsresult(data)
				{
					console.log("finished retrieving icon data :)");
					console.log(data);
					
					jQuery("#iconpicker").show();
					jQuery("#iconpickeritemlist").empty();
					var slug = data.slug;
					jQuery("#iconpicker").data("slug", slug);
					
					jQuery.each
					(
						data.sections, 
						function(index, value) 
						{
							var section = value.section;
							
							jQuery("#iconpickeritemlist").append("<li>---" + section + "---</li>");
							
							var items = value.items;
							//console.log("items for " + section);
							//console.log(items);
							
							var itemshtml = "";
							
							jQuery.each
							(
								items, 
								function(index, item) 
								{
									//console.log("item " + index + " " + item);
									
									var id = "nxs-icon-" + item.id;
									// the item itself
									var itemhtml = "<span class='nxs-icon " + id + "'></span>";
									// wrap in a clickable unit					
									var itemhtml = "<a href='#' onclick='nxs_js_iconselected(this); return false;' data-destinationdomselector='#nxs_entity_icon_input' data-id='" + id + "'>" + itemhtml + "</a>";
									itemshtml += itemhtml;
								}
							);
							
							jQuery("#iconpickeritemlist").append("<li>" + itemshtml + "</li>");
						}
					);
				}
				
			</script>
	
			<div id="modal-window-id" style="display:none;">
				<div id="iconpicker" style="xdisplay: none;">
					<div>
						<a href='#' onclick='nxs_js_closeiconpicker(); return false;'>Close</a>
					</div>
					<div>
						<style>
							#iconpickeritemlist li a
							{
								font-size: 32px; margin: 5px;
								line-height: 40px;
							}
						</style>
						Pick one of the icons from the list below:<br />
						<hr /><br />
						<ul id="iconpickeritemlist">
						</ul>
						<br />
						<hr />
					</div>
				</div>
			</div>
			<?php
			
			// Noncename needed to verify where the data originated
			// echo '<input type="hidden" name="nxs_semantic_media_noncename" id="nxs_semantic_media_noncename" value="' . wp_create_nonce( __FILE__ ) . '" />';
			
			// Get the location data if its already been entered
			$nxs_entity_icon = get_post_meta($post->ID, 'nxs_entity_icon', true);
	
			if (true)
			{
				?>
				<p>
					The icon connected:<br />
					<span id='nxs-entity-icon-preview' class='nxs-icon <?php echo $nxs_entity_icon; ?>' style='font-size:64px;' ></span>
					
					<br />
					<a id='nxs_semantic_media_button' class='button openiconpicker thickbox' href="#TB_inline?width=600&height=550&inlineId=modal-window-id">
						Configure
					</a>
					
					<!-- -->
					
					<script>
						$(".openiconpicker").on
						(
							"click", function()
							{
								//jQuery(".secret").show();
								console.log("you opened the dialog :)");
								nxs_js_filliconcontainer();
							} 
						);
					</script>
				</p>
		    <input type='hidden' name='nxs_entity_icon_input' id='nxs_entity_icon_input' value='<?php echo $nxs_entity_icon; ?>' />
		    <?php
		  }
		}
		
		$fields = nxs_businessmodelmetabox_getfieldsmeta($post);
		foreach ($fields as $field => $fieldmeta)
		{
			$type = $fieldmeta["type"];
			if ($type == "text" || $type == "")
			{
				$id = "nxs_entity_{$field}";
				$inputid = "nxs_entity_{$field}_input";
				$value = get_post_meta($post->ID, $id, true);
				?>
				<?php echo $field ;?>: 
				<input type='text' name='<?php echo $inputid; ?>' id='<?php echo $inputid; ?>' value='<?php echo $value; ?>' />
				<hr />
				<?php
			}
			else if ($type == "iconpicker")
			{
				// zie logica hierboven
			}
			
			else
			{
				echo "skipped: $field type $type <hr />";
			}
		}
		
		//
		echo "<hr />";
		
	}
	
	function nxs_businessmodelmetabox_getfieldsmeta($post)
	{
		$result = array();
		
		// get posttype bijv. nxs_reseller
		$posttype = $post->post_type;
		// get taxonomy for posttype
		$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
		foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
		{
			$singular = $taxonomymeta["singular"];
			$cpt = "nxs_{$singular}";
			if ($posttype == $cpt)
			{
				$additionalfields = $taxonomymeta["instanceextendedproperties"];
				break;
			}
		}
		
		if ($additionalfields == "")
		{
			$additionalfields = array();
		}
		
		$result = array_merge($result, $additionalfields);
		
		/*
		$fields = array
		(
			"icon" => array("type" => "iconpicker"),
			"stars" => array(),						// used by taxonomies: testimonials
			"rating_text" => array(),			// used by taxonomies: testimonials
			"source" => array(),					// used by taxonomies: testimonials
			"destination_url" => array(),	// used by taxonomies: testimonials
			"role" => array(),						// used by taxonomies: employees
			//"imperative_m" => array(),		// used by taxonomies: calltoactions
			//"imperative_l" => array(),		// used by taxonomies: calltoactions
			//"destination_cta" => array(
			//"type" => "ctadestinationpicker"
			//  MOET UITEINDELIJK EEN DDL WORDEN
			//),		// used by taxonomies: calltoactions
		);
		*/
		
		return $result;
	}
	
	function nxs_businessmodelmetabox_save_callback($post_id, $post) 
	{
		// Is the user allowed to edit the post or page?
		if ( !current_user_can( 'edit_post', $post->ID ))
		{
			return $post->ID;
		}
	
		// OK, we're authenticated: we need to find and save the data
		// We'll put it into an array to make it easier to loop though.
		
		$fields = nxs_businessmodelmetabox_getfieldsmeta($post);
		
		foreach ($fields as $field => $fieldmeta)
		{
			$id = "nxs_entity_{$field}";
			$value = $_POST["nxs_entity_{$field}_input"];
			$meta[$id] = $value;
		}
		
		// ----
		
		foreach ($meta as $key => $value) 
		{ 
			if( $post->post_type == 'revision' ) 
			{
				return; // Don't store custom data twice
			}
			
			$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
			if(get_post_meta($post->ID, $key, FALSE)) 
			{ 
				// If the custom field already has a value
				update_post_meta($post->ID, $key, $value);
			} 
			else 
			{ 
				// If the custom field doesn't have a value
				add_post_meta($post->ID, $key, $value);
			}
			if(!$value)
			{
				delete_post_meta($post->ID, $key); // Delete if blank
			}
		}
	}
	add_action('save_post', 'nxs_businessmodelmetabox_save_callback', 1, 2); // save the custom fields
	
	// -----
	
	function nxs_semanticlayout_callback($post)
	{
		// Noncename needed to verify where the data originated
		echo '<input type="hidden" name="nxs_semanticlayout_noncename" id="nxs_semanticlayout_noncename" value="' . wp_create_nonce( __FILE__ ) . '" />';
		
		// Get the location data if its already been entered
		$nxs_semanticlayout = get_post_meta($post->ID, 'nxs_semanticlayout', true);
		
		// TODO: dynamically populate the possible layout engines based upon a the configured rules
		$values = array("", "default", "landingpage");
		
		// 
		$query = new WP_Query(array('name' => nxs_templates_getslug(),'post_type' => 'nxs_busrulesset'));
		if ( $query->have_posts() ) 	
		{
			$thepostid = $query->posts[0]->ID;
		
			$filter = array
			(
				"postid" => $thepostid,
				"widgettype" => "busrulesemanticlayout",
			);
			$widgetsmetadata = nxs_getwidgetsmetadatainpost_v2($filter);
			
			foreach ($widgetsmetadata as $widgetmetadata)
			{
				$filter_id = $widgetmetadata["filter_id"];
				if (!in_array($filter_id, $values))
				{
					$values[] = $filter_id;
				}
			}
		}
		
		// add the current option if its not there (preventing us from wiping it)
		if (!in_array($nxs_semanticlayout, $values))
		{
			$values[] = $nxs_semanticlayout;
		}
		
		?>
		<p>
			The theme will determine the layout by default. To override the layout, pick a custom layout.
			<a target='_blank' href='https://docs.google.com/spreadsheets/d/1lTcFyiKYRUiUdlJilsVaigkHT7a69eL-lVKKPp53v9c/edit#gid=1679867339'>More here.</a>
			
		</p>
    <select name='nxs_semanticlayout' id='nxs_semanticlayout'>
      <?php 
      foreach ($values as $value)
      {
      	$selected = "";
    		if ($value == $nxs_semanticlayout)
    		{
    			$selected = "selected ";	
    		}  	
      	?>
      	<option <?php echo $selected; ?> value="<?php echo $value; ?>"><?php echo esc_html($value); ?></option>
      	<?php 
      }
      ?>
    </select>
    
    
    <?php
	}
	
	function nxs_semanticlayout_save_callback($post_id, $post) 
	{
		// error_log("nxs_semanticlayout_save_callback triggered");
		
		/*
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( !wp_verify_nonce( $_POST['nxs_semanticlayout_noncename'], wp_create_nonce( __FILE__ ) )) 
		{
			return $post->ID;
		}
		*/
	
		// Is the user allowed to edit the post or page?
		if ( !current_user_can( 'edit_post', $post->ID ))
		{
			return $post->ID;
		}
	
		// OK, we're authenticated: we need to find and save the data
		// We'll put it into an array to make it easier to loop though.
		
		$nxs_semanticlayout = $_POST['nxs_semanticlayout'];
		$meta['nxs_semanticlayout'] = $nxs_semanticlayout;
		
		// error_log("nxs_semanticlayout_save_callback; selected: $nxs_semanticlayout");
		
		
		// Add values of $events_meta as custom fields
		
		foreach ($meta as $key => $value) 
		{ 
			// Cycle through the $events_meta array!
			if( $post->post_type == 'revision' ) 
			{
				// error_log("nxs_semanticlayout_save_callback; revision");
				return; // Don't store custom data twice
			}
			// error_log("nxs_semanticlayout_save_callback; NO revision");
			
			$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
			if(get_post_meta($post->ID, $key, FALSE)) 
			{ 
				// If the custom field already has a value
				update_post_meta($post->ID, $key, $value);
				// error_log("nxs_semanticlayout_save_callback; update");
			} 
			else 
			{ 
				// If the custom field doesn't have a value
				add_post_meta($post->ID, $key, $value);
				// error_log("nxs_semanticlayout_save_callback; add");
			}
			if(!$value)
			{
				delete_post_meta($post->ID, $key); // Delete if blank
				// error_log("nxs_semanticlayout_save_callback; delete");
			}
		}
	}
	add_action('save_post', 'nxs_semanticlayout_save_callback', 1, 2); // save the custom fields
	
	function nxs_add_meta_boxes()
	{
		// 2016 12 08
		$taxonomiesmeta = nxs_business_gettaxonomiesmeta();
		foreach ($taxonomiesmeta as $taxonomy => $taxonomymeta)
		{
			$singular = $taxonomymeta["singular"];
			$cpt = "nxs_{$singular}";
			add_meta_box('nxs_businessmodelmetabox', 'Nxs Extended Properties', 'nxs_businessmodelmetabox_callback', $cpt, 'side', 'default');
		}
		
		//
		$cpts = nxs_cpt_getcptswithoutslug();
		foreach ($cpts as $cpt)
		{
			add_meta_box('nxs_semanticlayout', 'Layout', 'nxs_semanticlayout_callback', $cpt, 'side', 'default');
		}
		add_meta_box('nxs_semanticlayout', 'Layout', 'nxs_semanticlayout_callback', 'post', 'side', 'default');
		add_meta_box('nxs_semanticlayout', 'Layout', 'nxs_semanticlayout_callback', 'page', 'side', 'default');
		
		// 2016 12 08
		add_meta_box('nxs_semanticlayout', 'Layout', 'nxs_semanticlayout_callback', 'post', 'side', 'default');
	}
	
	add_action( 'add_meta_boxes', 'nxs_add_meta_boxes' );
}

// -------

?>