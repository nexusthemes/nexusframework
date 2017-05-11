<?php

function nxs_widgets_comments_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_comments_gettitle() {
	return nxs_l18n__("Discussion", "nxs_td");
}

// Unistyle
function nxs_widgets_comments_getunifiedstylinggroup() {
	return "commentswidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_comments_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	//$initialcommentstate = $temp_array['initialcommentstate'];

	$options = array
	(
		"sheettitle" => nxs_widgets_comments_gettitle(),
		"sheeticonid" => nxs_widgets_comments_geticonid(),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_comments_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			array
			( 
				"id" 				=> "title",
				"type" 				=> "input",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("If your callout has an eye-popping title put it here.", "nxs_td"),
				"unistylablefield"	=> false
			),
			array
			( 
				"id" 				=> "initialcommentstate",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Initial comment state", "nxs_td"),
				"dropdown" 			=> array(""=>nxs_l18n__("hold", "nxs_td"), "approved"=>nxs_l18n__("approved", "nxs_td")),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "comment_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Comment color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample text", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "comment_order",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Comment order", "nxs_td"),
				"dropdown" 			=> array(
					"past to present"=>nxs_l18n__("past (top) to present (bottom)", "nxs_td"),
					"present to past"=>nxs_l18n__("present (top) to past (bottom)", "nxs_td"), 
				),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "button_color",
				"type" 				=> "colorzen",
				"label" 			=> nxs_l18n__("Button color", "nxs_td"),
				"sampletext"		=> nxs_l18n__("Sample", "nxs_td"),
				"unistylablefield"	=> true
			),
			array(
				"id" 				=> "button_scale",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Button size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("button_scale"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "padding",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Padding", "nxs_td"),
				"dropdown"   		=> nxs_style_getdropdownitems("padding"),
				"unistylablefield"	=> true
			),	
			array( 
				"id" 				=> "border_radius",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Border radius", "nxs_td"),
				 "dropdown"   		=> nxs_style_getdropdownitems("border_radius"),
				"unistylablefield"	=> true
			),		
			array
			( 
				"id" 				=> "border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Comment border width", "nxs_td"),
				 "dropdown"   		=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),	
			array
			( 
				"id" 				=> "avatar_border_width",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Avatar border width", "nxs_td"),
				 "dropdown"   		=> nxs_style_getdropdownitems("border_width"),
				"unistylablefield"	=> true
			),	
			array
			( 
				"id" 				=> "avatar_shadow",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Avatar shadow", "nxs_td"),
				"unistylablefield"	=> true
			),		
			array
			(
				"id" 				=> "avatar_size",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Image size", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("image_size"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "formfields",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Fields", "nxs_td"),
				"dropdown" 			=> array(
					"name|email|website"=>nxs_l18n__("Name | Email | Website", "nxs_td"),
					"name|email"=>nxs_l18n__("Name | Email", "nxs_td"), 
				),
				"unistylablefield"	=> true
			),
		),
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

/* RECURSIVE FUNCTIONS TO RENDER COMMENTS
---------------------------------------------------------------------------------------------------- */
function nxs_widgets_comments_helper_countapproved_recursive($allcomments, $parentcommentid, $result) {
	$commentidssharingsameparent = nxs_getcommentidswithparent($allcomments, $parentcommentid);
	foreach ($commentidssharingsameparent as $currentcommentidsharingsameparent) {
		$currentcomment = nxs_getcommentwithid($allcomments, $currentcommentidsharingsameparent);
		if ($currentcomment->comment_approved == "1") {
			$result = $result + 1;
			// recursion
			$result = nxs_widgets_comments_helper_countapproved_recursive($allcomments, $currentcomment->comment_ID, $result);
		}
	}
	return $result;
}

function nxs_widgets_comments_helper_render_comment_recursive(
	$postid, 
	$placeholderid, 
	$allcomments, 
	$parentcommentid, 
	$depth, 
	$button_color, 
	$button_scale, 
	$comment_color, 
	$padding, 
	$border_radius, 
	$border_width,
	$avatar_border_width,
	$avatar_shadow,
	$avatar_size,
	$formfields
	) {
		
	/* EXPRESSIONS RECURSIVE FUNCTION
	---------------------------------------------------------------------------------------------------- */
	
	if ($depth > 0) {
		$debth60 = $depth * 60;
		$debthmarginclass = "nxs-margin-left" . $debth60;
	} else {
		$debthmarginclass = "";
	}
	
	// Image shadow
	if ($avatar_shadow != "") { $avatar_shadow = 'nxs-shadow'; }
	
	// Default variables
	$button_color_cssclass 			= nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	$button_scale_cssclass 			= nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	$comment_color_cssclass 		= nxs_getcssclassesforlookup("nxs-colorzen-", $comment_color);
	$padding_cssclass 				= nxs_getcssclassesforlookup("nxs-padding-", $padding);
	$border_radius_cssclass 		= nxs_getcssclassesforlookup("nxs-border-radius-", $border_radius);
	$border_width_cssclass 			= nxs_getcssclassesforlookup("nxs-border-width-", $border_width);
	$avatar_border_width_cssclass 	= nxs_getcssclassesforlookup("nxs-border-width-", $avatar_border_width);
	$avatar_size_cssclass 			= nxs_getimagecsssizeclass($avatar_size);
	
	// Concatenations
	$concatenated_comments_css = nxs_concatenateargswithspaces(
		$comment_color_cssclass, 
		$padding_cssclass, 
		$border_radius_cssclass, 
		$border_width_cssclass, 
		$debthmarginclass
	);
	$concatenated_button_css = nxs_concatenateargswithspaces(
		$button_color_cssclass, 
		$button_scale_cssclass
	);
	
	/* OUTPUT RECURSIVE FUNCTION
	---------------------------------------------------------------------------------------------------- */
	
	$commentidssharingsameparent = nxs_getcommentidswithparent($allcomments, $parentcommentid);
	foreach ($commentidssharingsameparent as $currentcommentidsharingsameparent) {
		$currentcomment = nxs_getcommentwithid($allcomments, $currentcommentidsharingsameparent);
		
		if ($currentcomment->comment_approved == 1) {
			$avatar = get_avatar($currentcomment);
			
			$currentdate = $currentcomment->comment_date;
			
			$avatar = str_replace("class='avatar", "style='height: auto;' class='avatar nxs-border", $avatar);
			$dayhtml = mysql2date('j', $currentdate);
			$monthhtml = nxs_getlocalizedmonth(mysql2date('m', $currentdate));
			$yearhtml = mysql2date('Y', $currentdate);
			
			$datehtml = $dayhtml . " " . $monthhtml . " " . $yearhtml;
			
			echo '
			<div class="reply-instance ' . $concatenated_comments_css . '">
		    	
				<!-- AVATAR  -->
				<div class="nxs-image-wrapper ' . $avatar_shadow. ' '. $avatar_size_cssclass . '">
					<div class="avatar-wrapper ' . $avatar_border_width_cssclass . '">' . $avatar . '</div>
		    	</div>
				
				<!-- METADATA -->
				<div class="metadata nxs-applylinkvarcolor">
					<h4>' . $currentcomment->comment_author . '</h4>
					<div class="nxs-margin-top5"></div>
					<span class="nxs-default-p nxs-padding-bottom0">' . $datehtml . '</span>';

				if ($formfields == "" || $formfields == "name|email|website")
				{
					echo '					
						<div class="nxs-margin-top5"></div>
						<span class="nxs-default-p nxs-padding-bottom0"><a target="_blank" rel="no-follow" href="' . $currentcomment->comment_author_url . '">' . $currentcomment->comment_author_url . '</a></span>
						<div class="nxs-clear nxs-filler"></div>';
				}
				
				echo '
				</div>
		    
				<!-- COMMENT -->
				<div class="nxs-applylinkvarcolor">
					<p class="nxs-default-p nxs-padding-bottom0"><span>' . $currentcomment->comment_content . '</span></p>	
				</div>
	
				<div class="nxs-clear nxs-filler"></div>
	
				<!-- REPLY BUTTON -->
				<a class="nxs-button ' . $concatenated_button_css . '" href="#" onclick="preparecomment_' . $placeholderid . '(' . $currentcomment->comment_ID . ', true); return false;">';
					nxs_l18n_e("Reply[nxs:button]", "nxs_td"); echo '
				</a>
				
				<!-- DELETE BUTTON -->';
				if (nxs_has_adminpermissions()) {
					echo '
					<a class="nxs-button ' . $concatenated_button_css . '" href="#" onclick="deletecomment_' . $placeholderid . '(' . $postid . ', ' . $currentcomment->comment_ID . ', this); return false;">';
						nxs_l18n_e("Delete[nxs:button]", "nxs_td"); echo '
					</a>'; 
				}

			    echo '
		    	<div class="nxs-clear"></div>
	 			<div class="nxs-reply-container" id="nxs_replycontainer_' . $currentcomment->comment_ID . '" style="display: none;">
				</div>
			</div>';
			
			// recursion!
			nxs_widgets_comments_helper_render_comment_recursive(
				$postid, 
				$placeholderid, 
				$allcomments, 
				$currentcomment->comment_ID, 
				$depth + 1, 
				$button_color, 
				$button_scale, 
				$comment_color, 
				$padding, 
				$border_radius, 
				$border_width,
				$avatar_border_width,
				$avatar_shadow,
				$avatar_size,
				$formfields
			);
			
		} else if ($currentcomment->comment_approved == 0){
			echo "<div class='tobemoderated'>" . nxs_l18n__("Comment awaiting moderation[nxs:span]", "nxs_td") . "</div>";
		} else if ($currentcomment->comment_approved == "spam") {
			echo "<div class='spam'>" . nxs_l18n__("Removed[nxs:span]", "nxs_td") . "</div>";
		} else {
			// not yet supported?
		}
	}
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_comments_render_webpart_render_htmlvisualization_native()
{
	if ( comments_open() || get_comments_number() ) 
	{
		comments_template();
	}
}

function nxs_widgets_comments_render_webpart_render_htmlvisualization($args)
{
	// Importing variables
	extract($args);
	
	global $nxs_global_row_render_statebag;
	global $nxs_global_current_containerpostid_being_rendered;
	global $nxs_global_current_postid_being_rendered;
	
	// Every widget needs it's own unique id for all sorts of purposes
	// The $postid and $placeholderid are used when building the HTML later on
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	//
	
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "")
	{
		// blend unistyle properties
		$unistyleproperties = nxs_unistyle_getunistyleproperties(nxs_widgets_comments_getunifiedstylinggroup(), $unistyle);
		$temp_array = array_merge($temp_array, $unistyleproperties);	
	}
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	$mixedattributes = array_merge($temp_array, $args);	
	
	// The $mixedattributes is an array which will be used to set various widget specific variables (and non-specific).
	// $mixedattributes = array_merge($temp_array, $args);
	
	// Output the result array and setting the "result" position to "OK"
	$result = array();
	$result["result"] = "OK";
	
	$title = $mixedattributes['title'];
	$initialcommentstate = $mixedattributes['initialcommentstate'];
	$formfields = $mixedattributes['formfields'];

	global $nxs_global_placeholder_render_statebag;

	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;	
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);
	
	// Turn on output buffering
	nxs_ob_start();
	nxs_widgets_comments_render_webpart_render_htmlvisualization_native();
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	if ($html == "")
	{
		nxs_ob_start();
		
		/* EXPRESSIONS
		---------------------------------------------------------------------------------------------------- */
		
		$args = array(
			"post_id" => $nxs_global_current_containerpostid_being_rendered, 
			"status" => 'approve',
			
		);
		
		if ($comment_order == "past to present")
		{
			$args["order"] = "ASC";
		}
		else if ($comment_order == "present to past")
		{
			$args["order"] = "DESC";
		}
		else
		{
			$args["order"] = "ASC";
		}
		
		$comments = get_comments($args);
		
		$class = "class='nxs-comments'";
		
		// Default variables
		$button_color_cssclass 	= nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
		$button_scale_cssclass 	= nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
		$comment_color_cssclass = nxs_getcssclassesforlookup("nxs-colorzen-", $comment_color);
		$padding_cssclass 		= nxs_getcssclassesforlookup("nxs-padding-", $padding);
		$border_radius_cssclass = nxs_getcssclassesforlookup("nxs-border-radius-", $border_radius);
		$border_width_cssclass 	= nxs_getcssclassesforlookup("nxs-border-width-", $border_width);
		
		// Concatenations
		$concatenated_comments_css = nxs_concatenateargswithspaces(
			$comment_color_cssclass, 
			$padding_cssclass, 
			$border_radius_cssclass, 
			$border_width_cssclass, 
			$debthmarginclass
		);
		$concatenated_button_css = nxs_concatenateargswithspaces(
			$button_color_cssclass, 
			$button_scale_cssclass
		);
		
		/* OUTPUT
		---------------------------------------------------------------------------------------------------- */
		
		echo'
		
		<div ' . $class . '>
			
			<div class="reply nxs-clear">
		    	<a style="display: none;" class="nxsbutton nxs-float-left" href="#" onclick="preparecomment_' . $placeholderid .'(0, true); return false;">Reageer</a>
		    	<div class="nxs-clear padding"></div>
		    	<div class="nxs-reply-container" id="nxs_replycontainer_0"></div>
				<div class="nxs-clear"></div>
			</div>';
			
			$count = nxs_widgets_comments_helper_countapproved_recursive($comments, "0", 0);
			if ($count == 0){
				// nothing yet
			} else if ($count == 1) {
				echo "<div class='nxs-clear nxs-padding-top20'></div>";
				echo '<h3 class="nxs-title">1 ' . nxs_l18n__("comment[nxs:span]", "nxs_td") . "</h3>";
			} else {
				echo "<div class='nxs-clear nxs-padding-top20'></div>";
				echo '<h3 class="nxs-title">' . $count . " " . nxs_l18n__("comments[nxs:span]", "nxs_td") . "</h3>";
			}
			
			echo '<div class="nxs-clear"></div>';
			
			nxs_widgets_comments_helper_render_comment_recursive(
				$nxs_global_current_containerpostid_being_rendered, 
				$placeholderid, 
				$comments, 0, 0, 
				$button_color, 
				$button_scale, 
				$comment_color, 
				$padding, 
				$border_radius, 
				$border_width,
				$avatar_border_width,
				$avatar_shadow,
				$avatar_size,
				$formfields
			);
			
			$title = $mixedattributes['title'];
			
			echo'
			<div class="template" style="display: none;">
				<div class="nxs-form ' . $concatenated_comments_css . '" id="nxs_commentform_' . $placeholderid . '">';
				
					if (isset($title)) {
						echo '<h3 class="nxs-title">' . $title . '</h3>';
					}
				
					echo '
			
					<input id="postid" name="postid" type="hidden" value="' . $nxs_global_current_containerpostid_being_rendered . '" />
					<input id="replytocommentid" type="hidden" name="replytocommentid" value="0" />
			
					<!-- NAME -->
			    	<div class="nxs-float-left nxs-width20"><label>'; nxs_l18n_e("Name[nxs:tooltip]", "nxs_td"); echo ' *:</label></div>
			    	<div class="nxs-float-right nxs-width80"><input id="naam" name="naam" type="text"></div>
			    	<div class="nxs-clear padding"></div>
					
					<!-- EMAIL -->
					<div class="nxs-float-left nxs-width20"><label>' . nxs_l18n__("Email address[nxs:tooltip]", "nxs_td") . ' *:</label></div>
					<div class="nxs-float-right nxs-width80"><input id="email" name="email" type="text"></div>
					<div class="nxs-clear padding"></div>
					';
					
					if ($formfields == "" || $formfields == "name|email|website")
					{
						echo '
						<!-- WEBSITE -->
						<div class="nxs-float-left nxs-width20"><label>'; nxs_l18n_e("Website[nxs:tooltip]", "nxs_td"); echo ':</label></div>
						<div class="nxs-float-right nxs-width80"><input id="website" name="website" type="text"></div>
						<div class="nxs-clear padding"></div>';
					}
	
					echo '
					<!-- COMMENT -->
					<div class="nxs-float-left nxs-width20"><label>'; nxs_l18n_e("Comment[nxs:tooltip]", "nxs_td"); echo ' *:</label></div>
					<div class="nxs-float-right nxs-width80"><textarea id="comment" name="comment"></textarea></div>
					<div class="nxs-clear padding"></div>
					
					<!-- BUTTONS -->
					<a class="nxs-button ' . $concatenated_button_css . ' nxs-margin-right15" href="#" onclick="postcomment_' . $placeholderid .'(); return false;">'; nxs_l18n_e("Send[nxs:tooltip]", "nxs_td"); echo '</a>
					<a class="nxs-button ' . $concatenated_button_css . '" href="#" onclick="cancelcomment_' . $placeholderid .'(); return false;">'; nxs_l18n_e("Cancel[nxs:tooltip]", "nxs_td"); echo '</a>
					<div class="nxs-clear"></div>
					
			  </div>
			  
			</div>
		
		</div>';
		
		?>
	    
		<script type='text/javascript'>
			function preparecomment_<?php echo $placeholderid; ?>(replytocommentid, shouldscrollandfocus)
			{
				// alles inklappen
				jQuery(".nxs-reply-container").slideUp(400, function() {
					// ensure there's one visible
					jQuery("#nxs_replycontainer_" + replytocommentid).show();
					
					nxs_js_reenable_all_window_events();
				});
				
				jQuery('#nxs_commentform_<?php echo $placeholderid; ?>').show();
				
				// behalve degene waar het om gaat
				jQuery("#nxs_replycontainer_" + replytocommentid).show();
				
				// get template
				var template = jQuery("#nxs_commentform_<?php echo $placeholderid;?>").parent().html();
				// wipe previous location
				jQuery("#nxs_commentform_<?php echo $placeholderid;?>").parent().html("");
				// add contact form on the right place
				jQuery("#nxs_replycontainer_" + replytocommentid).html(template);
	
				//nxs_js_log(template);
	
				
				jQuery("#nxs_replycontainer_" + replytocommentid).hide();
				jQuery("#nxs_replycontainer_" + replytocommentid).slideDown(500, function()
				{
					if (shouldscrollandfocus){
						jQuery('#nxs_commentform_<?php echo $placeholderid; ?> #naam').focus();
					}
					jQuery('#nxs_commentform_<?php echo $placeholderid; ?> #replytocommentid').val(replytocommentid);
					
					if (shouldscrollandfocus)
					{
						jQuery('html, body').animate({scrollTop: jQuery("#nxs_replycontainer_" + replytocommentid).offset().top}, 400);
					}
					
					nxs_js_reenable_all_window_events();
				});
			}
	
			jQuery(window).load(function()
			{
				//nxs_js_log('comment; window loaded');
				// eerste reageer block standaard tonen
				preparecomment_<?php echo $placeholderid; ?>(0, false);
			});
					
			function deletecomment_<?php echo $placeholderid; ?>(postid, commentid, element)
			{
				var answer = confirm("<?php nxs_l18n_e("Are you sure you want to remove this comment?[nxs:tooltip]", "nxs_td"); ?>");
				if (!answer)
				{
					return;
				}
				
				nxs_js_removecomment(postid, commentid, function()
				{
					nxs_js_refreshelementscontainerforelement(element, "anonymous", function() 
					{
						nxs_js_alert("<?php nxs_l18n_e("Comment was removed[nxs:growl]", "nxs_td"); ?>");
						nxs_gui_set_runtime_dimensions_enqueuerequest("nxs-widget-comment-commentremoved");
					});
				},
				function()
				{
					nxs_js_alert("<?php nxs_l18n_e("Comment was not removed[nxs:growl]", "nxs_td"); ?>");
				});
			}
			
			function postcomment_<?php echo $placeholderid; ?>()
			{
				var postid = <?php echo $nxs_global_current_postid_being_rendered; ?>;
				var placeholderid = '<?php echo $placeholderid; ?>';
				var replytocommentid = jQuery('#nxs_commentform_<?php echo $placeholderid; ?> #replytocommentid').val();
				var containerpostid = '<?php echo $nxs_global_current_containerpostid_being_rendered; ?>';
				
				var name = jQuery('#nxs_commentform_<?php echo $placeholderid; ?> #naam').val();
				var email = jQuery('#nxs_commentform_<?php echo $placeholderid; ?> #email').val();
				var website = jQuery('#nxs_commentform_<?php echo $placeholderid; ?> #website').val();
				var comment = jQuery('#nxs_commentform_<?php echo $placeholderid; ?> #comment').val();
	
				if (nxs_js_isemptyorwhitespace(name))
				{
					nxs_js_alert("<?php nxs_l18n_e("Please enter your name[nxs:growl]", "nxs_td"); ?>");
					jQuery('#nxs_commentform_<?php echo $placeholderid; ?> #naam').focus();
					return;
				}
				if (!nxs_js_validateemail(email) || nxs_js_isemptyorwhitespace(email))
				{
					nxs_js_alert("<?php nxs_l18n_e("Please enter a valid email address[nxs:growl]", "nxs_td"); ?>");
					jQuery('#nxs_commentform_<?php echo $placeholderid; ?> #email').focus();
					return;
				}
				if (nxs_js_isemptyorwhitespace(comment))
				{
					nxs_js_alert("<?php nxs_l18n_e("Please enter your comment first[nxs:growl]", "nxs_td"); ?>");
					jQuery('#nxs_commentform_<?php echo $placeholderid; ?> #comment').focus();
					return;
				}
				
				nxs_js_postcomment
				(
					postid, 
					containerpostid,
					placeholderid,
					replytocommentid, 
					name, 
					email, 
					website, 
					comment, 
					function(response)
					{
						jQuery('#nxs_commentform_<?php echo $placeholderid; ?>').slideUp(300, function()
						{
							// wipe comment
							jQuery('#nxs_commentform_<?php echo $placeholderid; ?> #comment').val('');
							
							var element = jQuery('#nxs_commentform_<?php echo $placeholderid; ?>');
							if (response.initialcommentstate == 1)
							{
								//
								// issue 949;
								// in most cases, the comments will be on a pagelet that will also contain
								// social sharing buttons. These social sharing buttons don't like
								// ajax refreshes (our previous implementation). Thus we do a client side 
								// refresh (less good, but the best implementation for now).
								//
								nxs_js_refreshcurrentpage();
							}
							else
							{
								// commentaar wordt pas geplaatst na akkoord, we hoeven de pagina niet te verversen
								nxs_js_alert("<?php nxs_l18n_e("Thanks for your comment. Its awaiting approval.[nxs:growl]", "nxs_td"); ?>");
							}						
						});
					}, 
					function()
					{
						nxs_js_alert("<?php nxs_l18n_e("Your comment was not added because an error occured[nxs:growl]", "nxs_td"); ?>");
					}
				);
			}
			
			function cancelcomment_<?php echo $placeholderid; ?>()
			{
				jQuery('#nxs_commentform_<?php echo $placeholderid; ?>').slideUp(300, function()
				{
					// done
					preparecomment_<?php echo $placeholderid; ?>(0, false);
				});
			}
			
			function nxs_js_execute_after_ajaxrefresh_widget_<?php echo $placeholderid; ?>()
			{
				preparecomment_<?php echo $placeholderid; ?>(0, false);
				
				nxs_js_reenable_all_window_events();
			}
			
		</script>
		<?php		
		/* ------------------------------------------------------------------------------------------------- */
		
		$html = nxs_ob_get_contents();
		nxs_ob_end_clean();
	}
	else
	{
		// a plugin (like for example Disqus) already handled the output
	}
	
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	// outbound statebag
	// $nxs_global_row_render_statebag["foo"] = "bar";

	return $result;
}

function nxs_widgets_comments_initplaceholderdata($args)
{
	extract($args);

	$args['button_color'] = "base2";
	$args['button_scale'] = "1-2";
	$args['comment_color'] = "base1";
	$args['padding'] = "1-0";
	$args['border_radius'] = "1-0";
	$args['border_width'] = "1-0";
	$args['avatar_size'] = "c@1-0";
	
	// current values as defined by unistyle prefail over the above "default" props
	$unistylegroup = nxs_widgets_comments_getunifiedstylinggroup();
	$args = nxs_unistyle_blendinitialunistyleproperties($args, $unistylegroup);
				
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);
	
	$result = array();
	$result["result"] = "OK";
	
	
	return $result;
}

?>
