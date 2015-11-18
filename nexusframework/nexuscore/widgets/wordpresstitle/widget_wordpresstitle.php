<?php

// Setting the widget name and icon
function nxs_widgets_wordpresstitle_geticonid() {
	$widget_name = basename(dirname(__FILE__));
	return "nxs-icon-" . $widget_name;
}

// Setting the widget title
function nxs_widgets_wordpresstitle_gettitle() {
	return nxs_l18n__("Title[nxs:widgettitle]", "nxs_td");
}

// Unistyle
function nxs_widgets_wordpresstitle_getunifiedstylinggroup() {
	return "wordpresstitlewidget";
}

/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_wordpresstitle_home_getoptions($args) 
{
	// CORE WIDGET OPTIONS

	$options = array
	(
		"sheettitle" => nxs_widgets_wordpresstitle_gettitle(),
		"sheeticonid" => nxs_widgets_wordpresstitle_geticonid(),
		"unifiedstyling" => array
		(
			"group" => nxs_widgets_wordpresstitle_getunifiedstylinggroup(),
		),
		"fields" => array
		(
			// TITLE
			
			array( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"initial_toggle_state"	=> "closed",
			),
			
			array
			( 
				"id" 				=> "gototitle",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_widgets_wordpresstitle_popupoptioncontent",
				"label" 			=> nxs_l18n__("Title", "nxs_td"),
				"placeholder" 		=> "Title goes here",
			),
			array(
				"id" 				=> "title_heading",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Title importance", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("title_heading"),
				"unistylablefield"	=> true
			),
			
			array(
				"id" 				=> "title_alignment",
				"type" 				=> "radiobuttons",
				"subtype" 			=> "halign",
				"label" 			=> nxs_l18n__("Title alignment", "nxs_td"),
				"unistylablefield"	=> true
			),
						
			array(
				"id" 				=> "title_fontsize",
				"type" 				=> "select",
				"label" 			=> nxs_l18n__("Override title fontsize", "nxs_td"),
				"dropdown" 			=> nxs_style_getdropdownitems("fontsize"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
			),
			
			// METADATA AND SOCIAL SHARING
			
			array( 
				"id" 				=> "wrapper_items_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Metadata and social sharing", "nxs_td"),	
				"unistylablefield"	=> true	
			),
			
			array( 
				"id" 				=> "showauthor",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show author", "nxs_td"),
				"unistylablefield"	=> true
			),
			
			array( 
				"id" 				=> "showdate",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show date", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "showcategories",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show categories", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "showcommentcount", 
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Show commentcount", "nxs_td"),
				"tooltip"			=> nxs_l18n__("The commentcount will be displayed by the configured comments provider (see site settings)", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "twitter",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Twitter", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "facebook",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Facebook", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "linkedin",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("LinkedIn", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "googleplus",
				"type" 				=> "checkbox",
				"label" 			=> nxs_l18n__("Google Plus", "nxs_td"),
				"unistylablefield"	=> true
			),
			array( 
				"id" 				=> "wrapper_items_end",
				"type" 				=> "wrapperend",
				"unistylablefield"	=> true
			),
		),
	);
	
	nxs_extend_widgetoptionfields($options, array("backgroundstyle"));
	
	return $options;
}

function nxs_widgets_wordpresstitle_popupoptioncontent($optionvalues, $args, $runtimeblendeddata) 
{
	nxs_ob_start();
	$containerpostid = $args["clientpopupsessioncontext"]["containerpostid"];
	$title = nxs_gettitle_for_postid($containerpostid);
	
	?>
	<a href="#" class='nxsbutton1 nxs-float-right' onclick="nxs_js_popup_pagetemplate_neweditsession('home'); return false;"><?php nxs_l18n_e('Edit', 'nxs_td'); ?></a>
	<p><?php echo $title;?></p>
	<?php
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

/* WIDGET HTML
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

function nxs_widgets_wordpresstitle_render_webpart_render_htmlvisualization($args)
{	
	extract($args);
	
	global $nxs_global_row_render_statebag;	
	global $nxs_global_current_containerpostid_being_rendered;
	global $nxs_global_current_postid_being_rendered;
	
	$result = array();
	$result["result"] = "OK";
	
	$temp_array = nxs_getwidgetmetadata($nxs_global_current_postid_being_rendered, $placeholderid);
	$mixedattributes = array_merge($temp_array, $args);
	
	// Widget specific variables
	extract($mixedattributes);
	
	if (is_tax())
	{
		// $title = "Taxonomy";
		$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$title = $term->name;
	}
	else if (is_archive())
	{
		if (is_category())
		{
			$cat = get_query_var('cat');
			$yourcat = get_category($cat);
			$title = $yourcat->name; 
		}
		else
		{
			$title = "Archive";//get_the_title(); // $term->name;
		}
	}
	else
	{
		$currentpost = get_post($nxs_global_current_containerpostid_being_rendered);
		$title = nxs_gettitle_for_postid($nxs_global_current_containerpostid_being_rendered);
		$currentpostdate = $currentpost->post_date;
	}
	
	$hovermenuargs = array();
	$hovermenuargs["postid"] = $postid;
	$hovermenuargs["placeholderid"] = $placeholderid;
	$hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
	$hovermenuargs["metadata"] = $mixedattributes;
	nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs); 

	global $nxs_global_placeholder_render_statebag;
	$nxs_global_placeholder_render_statebag["widgetclass"] = "nxs-wordpress-title";
	
	nxs_ob_start();
	
	/* EXPRESSIONS
	---------------------------------------------------------------------------------------------------- */
	
	if ($showcommentcount != "") {
		$commentsprovider = nxs_commentsprovider_getcurrent();
		if ($commentsprovider == "") {
			$shouldrenderalternative = true;
			$alternativehint = nxs_l18n__("No comments provider is configured while commentcount is active", "nxs_td");
		}
	}
	
	// Title
	$microdata = apply_filters("nxs_wptitle_microdata");
	$htmltitle = nxs_gethtmlfortitle_v2($title, $title_heading, $title_alignment, $title_fontsize, $title_heightiq, "", "", $microdata);

	// Date
	if ($showdate != "") 
	{
		$get_wordpress_date_format = get_option('date_format');
		$formatteddate = mysql2date($get_wordpress_date_format, $currentpostdate);
    	$datehtml = $formatteddate;
	}
	
	// Categories
	if ($showcategories != "") {
	
		$categoriesfilters = array();
		$categoriesfilters["uncategorized"] = "skip";
		$categories = get_the_category($nxs_global_current_containerpostid_being_rendered);
		nxs_getfilteredcategories($categories, $categoriesfilters);
			
		if (count($categories) > 0) {
			foreach ($categories as $currentcategory) {
				$url = get_category_link($currentcategory->cat_ID);
				$categorieshtml .= '
					<span class="nxs-categories">
						<a class="" href="' . $url . '">' . $currentcategory->name . '</a>
					</span>';
			}
		}
	}
	
	// Author
	if ($showauthor != "") {
		
		$authorurl = get_author_posts_url($currentpost->post_author);
		$authorname = get_the_author_meta("display_name", $currentpost->post_author);
		$authorhtml .= '
			<span class="nxs-author">
				<a href="' . $authorurl . '">' . $authorname . '</a>
			</span>';
	}
	
	/*
	// Comments count
	if ($showcommentcount != "" && $commentsprovider != "") {
		nxs_requirecommentsprovider($commentsprovider);
		$postcommentcounthtml = nxs_commentsprovider_getpostcommentcounthtml($nxs_global_current_containerpostid_being_rendered);
		$icon_font_list_comments ='	
			<ul class="icon-font-list nxs-float-right nxs-relative comments">
				<li>'
					. $postcommentcounthtml
					. '
				</li>
			</ul>';
	}
	*/
	
	// SHARING BUTTONS
	
	global $nxs_global_current_containerpostid_being_rendered;
	$currentposturl = nxs_geturl_for_postid($nxs_global_current_containerpostid_being_rendered);
	$currentencodedposturl = urlencode($currentposturl);
	$currenttitle = nxs_gettitle_for_postid($nxs_global_current_containerpostid_being_rendered);
	$currentencodedtitle = urlencode($currenttitle);
	
	// Twitter
	if ($twitter != "") {
		$twitter = '
		<li>
			<a target="_blank" href="https://twitter.com/share?url=' . $currentencodedposturl . '&text=' . $currentencodedtitle . '">
				<span class="nxs-icon-twitter-2"></span>
			</a>
		</li>
	';
	} 
		
	// Facebook
	if ($facebook != "") {
		$facebook = '
		<li>
			<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . $currentencodedposturl . '">
				<span class="nxs-icon-facebook"></span>
			</a>
		</li>
		';
	} 
	
	// LinkedIn
	if ($linkedin != "") { 
		$linkedin = 		'
		<li>
			<a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&url=' . $currentencodedposturl . '&title=' . $currentencodedtitle . '">
				<span class="nxs-icon-linkedin"></span>
			</a>
		</li>
		'; 
	}
	
	// Google Plus
	if ($googleplus != "") {
		$googleplus = '
		<li>
			<a target="_blank" href="https://plus.google.com/share?url=' . $currentencodedposturl . '" onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\'); return false;">
				<span class="nxs-icon-google-plus"></span>
			</a>
		</li>
		';
	}

	// Sharing list		
	if ($twitter != "" || $facebook != "" || $linkedin!= "" || $googleplus != "") {
		$icon_font_list_sharing ='	
			<ul class="icon-font-list nxs-float-right">'
				. $twitter
				. $facebook
				. $linkedin
				. $googleplus
				. '
			</ul>';
	}

	/* OUTPUT
	---------------------------------------------------------------------------------------------------- */
    
	if ($shouldrenderalternative) {
		if ($alternativehint == "") {
			$alternativehint = nxs_l18n__("Missing input", "nxs_td");
		} 
		nxs_renderplaceholderwarning($alternativehint); 
	} else {		
		
		// Title
		echo $htmltitle;
				
		// Meta data

		if ( $datehtml || $categorieshtml || $authorhtml ) 
		{
			echo '<div class="nxs-blog-meta nxs-applylinkvarcolor">'; 

				if ( $datehtml ) {
					echo $datehtml;
					if ( $categorieshtml || $authorhtml) {
						echo '<span class="nxs-seperator"> | </span>';
					}
				}

				if ( $categorieshtml ) {
					echo $categorieshtml;
					if ( $authorhtml ) { 
						echo '<span class="nxs-seperator"> | </span>';
					}
				}

				echo $authorhtml;
			echo '</div>';
		}
		
		// (Sharing) Buttons
		if ( $icon_font_list_comments != "" || $icon_font_list_sharing != "" ) {
			echo '<div class="nxs-blog-sharing nxs-applylinkvarcolor">';
				echo $icon_font_list_sharing;
				if ( $icon_font_list_comments != "" && $icon_font_list_sharing != "" ) { echo '<span class="nxs-seperator nxs-float-right"> | </span>'; }
				echo $icon_font_list_comments;
			echo '</div>';
		}
		
		echo '<div class="nxs-clear"></div>';
	}
	
	/* ------------------------------------------------------------------------------------------------- */

	// Setting the contents of the output buffer into a variable and cleaning up te buffer
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	// Setting the contents of the variable to the appropriate array position
	// The framework uses this array with its accompanying values to render the page
	$result["html"] = $html;	
	$result["replacedomid"] = 'nxs-widget-' . $placeholderid;

	return $result;
}

function nxs_widgets_wordpresstitle_initplaceholderdata($args)
{
	extract($args);

	$args["showdate"] = "true";
	$args["showauthor"] = "true";
	$args["showcategories"] = "true";
	$args['title_heightiq'] = "true";	
	
	nxs_widgets_wordpresstitle_updateplaceholderdata($args);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_widgets_wordpresstitle_updateplaceholderdata($args)
{
	extract($args);

	// let op, dit is dus geen meta data van de placeholder, maar meta data van de pagina waarop de title wordt geplaatst
	// update title of post
	
	// we staan niet toe dat de titel wordt aangepast vanuit deze methode
	
	$temp_array = array();
	$temp_array['type'] = 'wordpresstitle';		
	$temp_array['showdate'] = $showdate;
	$temp_array['showauthor'] = $showauthor;
	$temp_array['showcategories'] = $showcategories;
	
	nxs_mergewidgetmetadata_internal($postid, $placeholderid, $temp_array);

	$result = array();
	$result["result"] = "OK";
	
	return $result;
}
?>