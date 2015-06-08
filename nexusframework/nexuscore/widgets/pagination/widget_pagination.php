<?php

function nxs_widgets_pagination_geticonid()
{
    $widget_name = basename(dirname(__FILE__));
    return "nxs-icon-" . $widget_name;
}

function nxs_widgets_pagination_gettitle()
{
    return nxs_l18n__("pagination", "nxs_td");
}

// rendert de placeholder zoals deze uiteindelijk door een gebruiker zichtbaar is,
// hierbij worden afhankelijk van de rechten ook knoppen gerenderd waarmee de gebruiker
// het bewerken van de placeholder kan opstarten
function nxs_widgets_pagination_render_webpart_render_htmlvisualization($args)
{
    //
    extract($args);

    $result = array();
    $result["result"] = "OK";

    $temp_array = nxs_getwidgetmetadata($postid, $placeholderid);

    $mixedattributes = array_merge($temp_array, $args);

    // Localize atts
    $mixedattributes = nxs_localization_localize($mixedattributes);

    // Lookup atts
    $mixedattributes = nxs_filter_translatelookup($mixedattributes, array("htmlcustom"));

    $hovermenuargs = array();
    $hovermenuargs["postid"] = $postid;
    $hovermenuargs["placeholderid"] = $placeholderid;
    $hovermenuargs["placeholdertemplate"] = $placeholdertemplate;
    $hovermenuargs["metadata"] = $mixedattributes;
    nxs_widgets_setgenericwidgethovermenu_v2($hovermenuargs);

    nxs_ob_start();

    $shouldrenderalternative = false;

    the_posts_pagination( array(
        'prev_text'          => __( 'Previous page', 'nxs_td' ),
        'next_text'          => __( 'Next page', 'nxs_td' ),
        'mid-size'           => 2,
    ) );


    $html = nxs_ob_get_contents();

    nxs_ob_end_clean();

    $result["html"] = $html;
    $result["replacedomid"] = 'nxs-widget-' . $placeholderid;

// outbound statebag

    return $result;
}


/* WIDGET STRUCTURE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

// Define the properties of this widget
function nxs_widgets_pagination_home_getoptions($args)
{
    // CORE WIDGET OPTIONS

    $options = array
    (
        "sheettitle" => nxs_widgets_pagination_gettitle(),
        "sheeticonid" => nxs_widgets_pagination_geticonid(),
        "sheethelp" => nxs_l18n__("http://nexusthemes.com/html-widget/"),
        "fields" => array
        (
            // -------------------------------------------------------

            array(
                "id" 					=> "wrapper_input_begin",
                "type" 				=> "wrapperbegin",
                "label" 			=> nxs_l18n__("HTML properties", "nxs_td"),
            ),

            array(
                "id" 				=> "wrapper_input_end",
                "type" 				=> "wrapperend"
            ),
            // -------------------------------------------------------

        ),
    );

    nxs_extend_widgetoptionfields($options, array("backgroundstyle"));

    return $options;
}

function nxs_widgets_pagination_initplaceholderdata($args)
{
    extract($args);

//    $args["htmlcustom"] = nxs_l18n__("Sample htmlcustom[nxs:default]", "nxs_td");
//    $args['ph_margin_bottom'] = "0-0";

    nxs_mergewidgetmetadata_internal($postid, $placeholderid, $args);

    $result = array();
    $result["result"] = "OK";

    return $result;
}

?>