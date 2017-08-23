<?php

function nxs_frontendframework_nxs2_footer()
{
	global $nxs_gl_style_footer_cssrules;
	if (count($nxs_gl_style_footer_cssrules) > 0)
	{
		echo "<style>";
		foreach ($nxs_gl_style_footer_cssrules as $id => $rules)
		{
			echo ".{$id}{" . implode($rules) . "}";
		}
		echo "</style>";
	}
}

function nxs_rgbtohsl($rgb)
{
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];
	
	$oldR = $r;
	$oldG = $g;
	$oldB = $b;
	$r /= 255;
	$g /= 255;
	$b /= 255;
  $max = max( $r, $g, $b );
	$min = min( $r, $g, $b );
	$h;
	$s;
	$l = ( $max + $min ) / 2;
	$d = $max - $min;
	if( $d == 0 ){
	  	$h = $s = 0; // achromatic
	} 
	else 
	{
		$s = $d / ( 1 - abs( 2 * $l - 1 ) );
		switch( $max )
		{
	    case $r:
	    	$h = 60 * fmod( ( ( $g - $b ) / $d ), 6 ); 
	      if ($b > $g) 
	      {
	    		$h += 360;
	      }
	      break;
	    case $g: 
	    	$h = 60 * ( ( $b - $r ) / $d + 2 ); 
	    	break;
	    case $b: 
	    	$h = 60 * ( ( $r - $g ) / $d + 4 ); 
	    	break;
		}			        	        
	}
	$result = array
	(
		"h" => $h,
		"s" => $s,
		"l" => $l 
	);
	
	return $result;
}

function nxs_hextorgb($hex)
{
	$hex = str_replace("#", "", $hex);
	$hex_r = substr($hex, 0, 2);
	$rgb_r = hexdec($hex_r);
	$hex_g = substr($hex, 2, 2);
	$rgb_g = hexdec($hex_g);
	$hex_b = substr($hex, 4, 2);
	$rgb_b = hexdec($hex_b);
	$result = array
	(
		"r" => $rgb_r,
		"g" => $rgb_g,
		"b" => $rgb_b
	);
	return $result;
}

function nxs_adjustlightnessforhsl($hsl, $delta)
{
	$result = array
	(
		"h" => $hsl["h"],
		"s" => $hsl["s"],
		"l" => $hsl["l"] + $delta,
	);
	
	if ($result["l"] < 0)
	{
		$result["l"] = 0;
	}
	else if ($result["l"] > 1)
	{
		$result["l"] = 1;
	}
	
	return $result; 
}

function nxs_hsltorgb($hsl)
{
	$h = $hsl["h"];
	$s = $hsl["s"];
	$l = $hsl["l"];
	
	$r; 
  $g; 
  $b;
	$c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
	$x = $c * ( 1 - abs( fmod( ( $h / 60 ), 2 ) - 1 ) );
	$m = $l - ( $c / 2 );
	if ( $h < 60 ) {
		$r = $c;
		$g = $x;
		$b = 0;
	} else if ( $h < 120 ) {
		$r = $x;
		$g = $c;
		$b = 0;			
	} else if ( $h < 180 ) {
		$r = 0;
		$g = $c;
		$b = $x;					
	} else if ( $h < 240 ) {
		$r = 0;
		$g = $x;
		$b = $c;
	} else if ( $h < 300 ) {
		$r = $x;
		$g = 0;
		$b = $c;
	} else {
		$r = $c;
		$g = 0;
		$b = $x;
	}
	$r = ( $r + $m ) * 255;
	$r = floor($r);
	$g = ( $g + $m ) * 255;
	$g = floor($g);
	$b = ( $b + $m  ) * 255;
	$b = floor($b);
	$result = array
	(
		"r" => $r,
		"g" => $g,
		"b" => $b,
	);
	return $result;
}

function nxs_rgbtohex($rgb)
{
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];
	$result = sprintf("#%02x%02x%02x", $r, $g, $b);
	$result = strtoupper($result);
	return $result;
}

function nxs_getlighterhexcolor($hex, $delta)
{
	$rgb = nxs_hextorgb($hex);
	$hsl = nxs_rgbtohsl($rgb);
	$adjustedhsl = nxs_adjustlightnessforhsl($hsl, $delta);
	$adjustedrgb = nxs_hsltorgb($adjustedhsl);
	$adjustedhex = nxs_rgbtohex($adjustedrgb);
	return $adjustedhex;
}

// helper function to get the css output for a lineair gradient between the 2 specified colors
function nxs_getlineairgradientcss($colora, $colorb)
{
	$result = "";
	$result .= "background-color:{$colora};";
	$result .= "fill:{$colora};";
	$result .= "background: -o-linear-gradient({$colorb}, {$colora});";
	$result .= "background: -moz-linear-gradient({$colorb}, {$colora});";
	$result .= "background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({$colorb}), to({$colora}));";
	$result .= "background: -ms-linear-gradient({$colorb}, {$colora});";
	$result .= "filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr={$colorb},EndColorStr={$colora});";
	return $result;
}

//
function nxs_getflatbackgroundcolorcss($color)
{
	$rgb = nxs_hextorgb($color);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];
	
	$result = "";
	$result .= "background-color: rgb({$r}, {$g}, {$b});";
	return $result;
}

function nxs_getflatalphabackgroundcolorcss($color, $alpha)
{
	$result = "";
	$rgb = nxs_hextorgb($color);
	$r = $rgb["r"];
	$g = $rgb["g"];
	$b = $rgb["b"];
	
	$alphavalue = str_replace("-", ".", $alpha);
	if ($alphavalue == "0.0")
	{
		$alphavalue = 0;
	}
	
	$result .= "background-color: rgba({$r}, {$g}, {$b}, {$alphavalue});";
	
	return $result;
}

function nxs_frontendframework_nxs2_compilestyle($styles)
{
	$rules = array();
	foreach ($styles as $key => $val)
	{
		$val = trim($val);		
		
		
		$concatenated .= "{$key}{$val}";
		if ($key == "align")
		{
			$val = str_replace("nxs-align-", "", $val);
			if ($val == "")
			{
				$rules[] = "text-align: center;";
			}
			else if ($val == "left")
			{
				$rules[] = "text-align: left;";
			}
			else if ($val == "center")
			{
				$rules[] = "text-align: center;";
			}
			else if ($val == "right")
			{
				$rules[] = "text-align: right;";
			}
			else
			{
				// unknown?
				$rules[] = "text-align: unsupported_{$val};";
			}
		}
		else if ($key == "maxheight")
		{
			if ($val == "")
			{
				// default
			}
			else if (nxs_stringstartswith($val, "nxs-maxheight-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[2];
				$fraction = $pieces[3];
				$value = $whole + ($fraction / 10);
				$factor = 100;
				$value = $value * $factor;
				
				$rules[] = "max-height: {$value}px !important;";
			}
			else
			{
				// unknown?
				$rules[] = "max-height: unsupported_{$val};";
			}
		}
		else if ($key == "padding_top")
		{
			if ($val == "")
			{
				// default
			}
			// factor = 30
			else if (nxs_stringstartswith($val, "nxs-padding-top-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[3];
				$fraction = $pieces[4];
				$value = $whole + ($fraction / 10);
				$factor = 30;
				$value = $value * $factor;
				$rules[] = "padding-top: {$value}px !important;";
			}
			else
			{
				// unknown?
				$rules[] = "padding-top: unsupported_{$val};";
			}
		}
		else if ($key == "padding_bottom")
		{
			if ($val == "")
			{
				// absorb
			}
			else if ($val == "nxs-padding-bottom0")
			{
				$rules[] = "padding-bottom: 0px !important;";
			}
			else if ($val == "nxs-padding-bottom10")
			{
				$rules[] = "padding-bottom: 10px !important;";
			}
			else if ($val == "nxs-padding-bottom20")
			{
				$rules[] = "padding-bottom: 20px !important;";
			}
			else if ($val == "nxs-padding-bottom30")
			{
				$rules[] = "padding-bottom: 30px !important;";
			}
			else if ($val == "nxs-padding-bottom40")
			{
				$rules[] = "padding-bottom: 40px !important;";
			}
			else if ($val == "nxs-padding-bottom50")
			{
				$rules[] = "padding-bottom: 50px !important;";
			}
			else
			{
				// unknown?
				$rules[] = "padding-bottom: unsupported_{$val};";
			}
		}
		else if ($key == "padding")
		{
			if ($val == "")
			{
				// does not apply
			}
			else if (nxs_stringstartswith($val, "nxs-padding-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[2];
				$fraction = $pieces[3];
				$base = $whole + ($fraction / 10);
				$factor = 30;
				$value = $base * $factor;
				$rules[] = "padding: {$value}px !important;";
			}
			else
			{
				// unknown?
				$rules[] = "margin: unsupported_{$val};";
			}
		}		
		else if ($key == "margin")
		{
			if ($val == "")
			{
				// does not apply
			}
			else if ($val == "nxs-margin5")
			{
				$rules[] = "margin: 5px !important;";
			}
			else if ($val == "nxs-margin10")
			{
				$rules[] = "margin: 10px !important;";
			}
			else if (nxs_stringstartswith($val, "nxs-margin-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[2];
				$fraction = $pieces[3];
				$base = $whole + ($fraction / 10);
				$factor = 30;
				$value = $base * $factor;
				$rules[] = "margin: {$value}px !important;";
			}
			else
			{
				// unknown?
				$rules[] = "margin: unsupported_{$val};";
			}
		}
		else if ($key == "margin_top")
		{
			if ($val == "")
			{
				// does not apply
			}
			/* static valued */
			else if ($val == "nxs-margin-top0")
			{
				$rules[] = "margin-top: 0px !important;";
			}
			else if ($val == "nxs-margin-top10")
			{
				$rules[] = "margin-top: 10px !important;";
			}
			// factor (dynamic)
			else if (nxs_stringstartswith($val, "nxs-margin-top-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[3];
				$fraction = $pieces[4];
				$value = $whole + ($fraction / 10);
				$factor = 30;
				$value = $value * $factor;
				
				$rules[] = "margin-top: {$value}px !important;";
			}
			else
			{
				// unknown?
				$rules[] = "margin-bottom: unsupported_{$val};";
			}
		}
		else if ($key == "margin_bottom")
		{
			if ($val == "")
			{
				// does not apply
			}
			// factor (dynamic)
			else if (nxs_stringstartswith($val, "nxs-margin-bottom-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[3];
				$fraction = $pieces[4];
				$value = $whole + ($fraction / 10);
				$factor = 30;
				$value = $value * $factor;
				
				$rules[] = "margin-bottom: {$value}px !important;";
			}
			// hardcoded
			else if (nxs_stringstartswith($val, "nxs-margin-bottom"))
			{
				$value = str_replace("nxs-margin-bottom", "", $val);
				$rules[] = "margin-bottom: {$value}px !important;";
			}
			else
			{
				// unknown?
				$rules[] = "margin-bottom: unsupported_{$val};";
			}
		}
		else if ($key == "colorzen")
		{
			// nxs-colorzen nxs-colorzen-c12-dm => // nxs-colorzen-c12-dm
			$rules[] = "border-style: solid;";
			
			$val = str_replace("nxs-colorzen ", "", $val);
			$val = str_replace("nxs-colorzen-", "", $val);
			
			if ($val != "")
			{
				// nxs-colorzen-c12-dm
				$parts = explode("-", $val, 2);	
				
				$coloridentification = $parts[0];		// c12
				
				$middle = "777777";
				if (nxs_hassitemeta())
				{
					$palettename = nxs_colorization_getactivepalettename();
					$colorizationproperties = nxs_colorization_getpersistedcolorizationproperties($palettename);
					$thekey = "colorvalue_" . $coloridentification;
					if (isset($colorizationproperties[$thekey]))
					{
						$middle = $colorizationproperties[$thekey];	// bijv. "#4054BF"
						/*
						echo "key:";
						var_dump($thekey);
						echo "middle: $middle <br />";
						var_dump($colorizationproperties);
						echo "<br />";
						var_dump($coloridentification);
						die();
						*/
					}
					
					if ($coloridentification == "base2")
					{
						// overruled; always 100% black
						$middle = "000000";
					}
					else if ($coloridentification == "base1")
					{
						// overruled; always 100% white
						$middle = "FFFFFF";
					}
				}
				
				$transformation = $parts[1];
		
				$delta = 0.2;
		
				if ($transformation == "dm")
				{
					// dark to middle
					$hex_from = nxs_getlighterhexcolor($middle, -$delta);
					$hex_to = $middle;
					$rules[] = nxs_getlineairgradientcss($hex_from, $hex_to);
				}
				else if ($transformation == "ml")
				{
					// middle to light
					$hex_from = $middle;
					$hex_to = nxs_getlighterhexcolor($middle, $delta);
					$rules[] = nxs_getlineairgradientcss($hex_from, $hex_to);
				}
				else if ($transformation == "")
				{
					// flat
					$hex = $middle;
					$rules[] = nxs_getflatbackgroundcolorcss($hex);
				}
				else if ($transformation[0] == "a")
				{
					// background is alpha (flat)
					$hex = $middle;
					$alpha = substr($transformation, 1);
					
					$rules[] = nxs_getflatalphabackgroundcolorcss($hex, $alpha);
				}
				else
				{
					$rules[] = "unsupportedtransformation;";
					echo "unsupported transformation; $transformation; parts; val: $val <br />";
					var_dump($parts);
					die();
				}
				
				//
				
				$comparecolorhex = $middle;
				$comparecolorrgb = nxs_hextorgb($comparecolorhex);
				$comparecolorhsl = nxs_rgbtohsl($comparecolorrgb);	
				$lighttreshhold = 0.7;
				$isbackgroundcolorrelativelydark = $comparecolorhsl["l"] < $lighttreshhold;
				 
				if ($isbackgroundcolorrelativelydark)
				{
					//var textcolor = csslookup["color_" + identification + "1_ll"];
					//var textshadowcolor = csslookup["color_" + identification + "2_dd"];
					$textcolor = "white";
					$textshadowcolor = "black";
				}
				else
				{
					$textcolor = "black";
					$textshadowcolor = "white";
				}
				
				$rules[] = "color: $textcolor;";
				$rules[] = "text-shadow: 1px 1px 1px $textshadowcolor;";
			}
		}
		else if ($key == "button")
		{
			$rules[] = "border-width: 1px;";
    	$rules[] = "border-radius: 3px;";
    	$rules[] = "cursor: pointer;";
		}
		else if ($key == "scale")
		{
			if ($val == "")
			{
				// default
			}
			else if ($val == "nxs-button-scale-1-8")
			{
				$rules[] = "font-size: 22px;";
				$rules[] = "padding-left: 18px;";
    		$rules[] = "padding-right: 18px;";
    		$rules[] = "padding-top: 11px;";
    		$rules[] = "padding-bottom: 11px;";
			}
			else if ($val == "nxs-button-scale-2-0")
			{
				$rules[] = "font-size: 24px;";
				$rules[] = "padding-left: 20px;";
    		$rules[] = "padding-right: 20px;";
    		$rules[] = "padding-top: 12px;";
    		$rules[] = "padding-bottom: 12px;";
			}
			else
			{
				$rules[] = "unsupported_scale_{$key}:{$val}";
			}
		}
		else if ($key == "fontsize" || false)
		{
			$val = str_replace("nxs-fontsize-", "", $val);
			
			if ($val == "")
			{
				// leave as-is (default)
			}
			else if (nxs_stringcontains($val, "-"))
			{
				// format; 1-2
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$value = $whole + ($fraction / 10);
				$factor = 15;
				$value = $value * $factor;
				
				if ($value == 0)
				{
					echo "huh? $val";
				}
				
				$rules[] = "font-size: {$value}px !important;";
			}
			else
			{
				$rules[] = "font-size: unsupported__{$val};";
			}
		}
		else if ($key == "fontzen" || false)
		{
			$val = str_replace("nxs-fontzen-", "", $val);
			
			if ($val == "")
			{
				// leave as-is (default)
			}
			else 
			{
				$sitemeta = nxs_getsitemeta();
				$fontzenid = $val;
				$sanitizedfontfamily = str_replace("\'", "'", nxs_font_getcleanfontfam($sitemeta["vg_fontfam_{$fontzenid}"]));
				$rules[] = "font-family: {$sanitizedfontfamily};";
			}
		}
		else if ($key == "border_radius")
		{
			$val = str_replace("nxs-border-radius-", "", $val);
			
			if ($val == "")
			{
				// 
			}
			else if (nxs_stringcontains($val, "-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[2];
				$fraction = $pieces[3];
				$base = $whole + ($fraction / 10);
				$factor = 3;
				$value = $base * $factor;
				$rules[] = "border-radius: {$value}px;";
			}
			else
			{
				$rules[] = "unsupported_border_radius_{$key}:{$val};";
			}
		}
		else if ($key == "content_justify")
		{
			if ($val == "")
			{
				// use default
			}
			else if ($val == "start")
			{
				$rules[] = "display: flex;";
				$rules[] = "flex-direction: row;";
				$rules[] = "justify-content: flex-start;";
			}
			else if ($val == "center")
			{
				$rules[] = "display: flex;";
				$rules[] = "flex-direction: row;";
				$rules[] = "justify-content: center;";
			}
			else if ($val == "end")
			{
				$rules[] = "display: flex;";
				$rules[] = "flex-direction: row;";
				$rules[] = "justify-content: flex-end;";
			}
			else
			{
				$rules[] = "unsupported:{$key}__{$val};";
			}
		}
		else if ($key == "texttype")
		{
			if ($val == "quote")
			{
				$rules[] = "font-style: italic;";
			}
		}
		else if ($key == "line_height")
		{
			$val = str_replace("nxs-line-height-", "", $val);
				
			if ($val == "")
			{
				// default
			}
			else if (nxs_stringcontains($val, "-"))
			{
				// format; 1-0
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$value = $whole + ($fraction / 10);
				$factor = 1.625;
				$value = $value * $factor;
				
				$rules[] = "line-height: {$value}em !important;";
			}
			else
			{
				$rules[] = "line-height: unsupported_$val;";
			}
		}
		else
		{
			$rules[] = "unsupported_{$key}:{$val};";
		}
	}
	
	// remove duplicates
	$rules = array_unique($rules);
	
	if (count($rules) == 0)
	{
		// no impact, no rules nor id :)
		return;
	}
	
	// there's 2 ways how the styles can be rendered; either inline, or in the footer
	if (false) // is_user_logged_in())
	{
		// use inline styling, and unique class ids (less optimized)
		
		$result["id"] = "nxs-s-" . md5($concatenated);
		$result["rules"] = $rules;
	}
	else
	{
		global $nxs_gl_style_id;
		$nxs_gl_style_id++;
		$id = "nxs-s-" . $nxs_gl_style_id;
		$result["id"] = $id;
		
		// ensure we hook to the footer to inject the derived styles
		global $nxs_gl_style_footer_cssrules;
		if (!isset($nxs_gl_style_footer_cssrules))
		{
			// enqueue the styles in the footer (1x only)
			add_action("wp_footer", "nxs_frontendframework_nxs2_footer");
		}
		
		$nxs_gl_style_footer_cssrules[$id] = $rules;
	}

	return $result;
}

function nxs_frontendframework_nxs2_gethtmlforbutton($args)
{
	extract($args);
	
	if ($text == "")
	{
		return "";
	}
	if ($destination_articleid == "" && $destination_url == "" && $destination_js == "")
	{
		return "";
	}
	
	$margin = nxs_getcssclassesforlookup("nxs-margin", $margin);
	$scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $scale);
	$fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $fontzen);
	$border_radius_cssclass = nxs_getcssclassesforlookup("nxs-border-radius-", $border_radius);
	
	if ($destination_articleid != "")
	{
		$posttype = get_post_type($destination_articleid);
		if ($posttype == "attachment")
		{
			$url = wp_get_attachment_url($destination_articleid);
		}
		else
		{
			$url = nxs_geturl_for_postid($destination_articleid);
		}
		$onclick = "";
	}
	else if ($destination_url != "")
	{
		if (nxs_stringstartswith($destination_url, "tel:"))
		{
			// a phone link; if parenthesis or spaces are used; absorb them
			$url = $destination_url;
			$url = str_replace(" ", "", $url);
			$url = str_replace("(", "", $url);
			$url = str_replace(")", "", $url);
		}
		else
		{
			// regular link
			$url = $destination_url;
		}
		$onclick = "";
	}
	else if ($destination_js != "")
	{
		$url = "#";
		$onclick = "onclick='" . nxs_render_html_escape_singlequote($destination_js) . "' ";
	}
	else
	{
		// unsupported
		$url = "nxsunsupporteddestination";
		$onclick = "";
	}
	
	if ($onclick != "")
	{
		$onclick = " " . $onclick . " ";
 	}
 
 	if ($destination_target == "@@@empty@@@" || $destination_target == "")
 	{
 		// auto
 		if ($destination_articleid != "")
 		{
 			// local link = self
 			$destination_target = "_self";
 		}
 		else
 		{
 			$homeurl = nxs_geturl_home();
 			if (nxs_stringstartswith($url, $homeurl))
 			{
 				$destination_target = "_self";
 			}
 			else
 			{
 				$destination_target = "_blank";
 			}
 		}
 	}
 	if ($destination_target == "_self")
 	{
 		$destination_target = "_self";
 	}
 	else if ($destination_target == "_blank")
 	{
 		$destination_target = "_blank";
 	}
 	else
 	{
 		$destination_target = "_self";
	}

	$destination_relation_html = '';
	if ($destination_relation == "nofollow") 
	{
		$destination_relation_html = 'rel="nofollow"';
	}

	$styles = array();
	$styles["align"] = $align;
	$styles["padding_bottom"] = "nxs-padding-bottom0";
	$styles["margin"] = $margin;
	$styles["line_height"] = "0-8";
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_1 = $compiled[0]["id"];
	//
	
	$styles = array();
	$styles["colorzen"] = $colorzen;
	$styles["button"] = "";	// ?
	$styles["scale"] = $scale_cssclass;
	$styles["border_radius"] = $border_radius_cssclass;
	
	if ($_REQUEST["debugbutton"] == "true")
	{
		var_dump($styles);
		die();
	}
	
	$compiled[1] = nxs_frontendframework_nxs2_compilestyle($styles);
	
	
	
	$unique_style_combination_class_2 = $compiled[1]["id"];
	//
	
	if ($url != "")
	{
		if ($destination_target_html == 'target="_self"')
		{
			$onclick = " onClick=\"window.location.href='{$url}';\" ";
		}
		else
		{
			$onclick = " onClick=\"window.open('{$url}');\" ";
		}
	}
	
	//
	
	$result = '';
	$result .= '<p class="' . $compiled[0]["id"] . '">';
	$result .= '<button target="' . $destination_target . '" ' . $destination_relation_html . ' ' . $onclick . ' class="' . $unique_style_combination_class_2 . ' ' . $fontzen_cssclass . '">' . $text . '</button>';
	$result .= '</p>';
	
	
	
	foreach ($compiled as $id => $compiledresult)
	{
		$class = $compiledresult["id"];
		$rules = $compiledresult["rules"];
		if (count($rules) > 0)
		{
			$result .= '<style>';
			$result .= ".{$class} { " . implode($rules) . " }";
			$result .= '</style>';
		}
	}
		
	return $result;
}

function nxs_frontendframework_nxs2_gethtmlfortitle($args)
{
	extract($args);
	
	if ($title == "")
	{
		return "";
	}
	
	if ($destination_target == "_self") 
	{
		$destination_target_html = 'target="_self"';
	} 
	else if ($destination_target == "_blank") 
	{
		$destination_target_html = 'target="_blank"';
	} 
	else 
	{
		if ($destination_articleid != "") 
		{
			$destination_target_html = 'target="_self"';
		} 
		else 
		{
			$homeurl = nxs_geturl_home();
 			if (nxs_stringstartswith($destination_url, $homeurl)) {
 				$destination_target_html = 'target="_self"';
 			} else {
 				$destination_target_html = 'target="_blank"';
 			}
		}
	}

	$destination_relation_html = '';
	if ($destination_relation == "nofollow") {
		$destination_relation_html = 'rel="nofollow"';
	}
	
	// Title importance (H1 - H6)
	if ($heading == "")
	{
		$heading = "1";
	}
	$heading = str_replace("h", "", $heading);
	$headingelement = "h" . $heading;
	
	// Title alignment
	if ($fontsize == "")
	{
		// derive the fontsize based upon the heading type (h1, h2, ...)
		if ($heading == "2")
		{
			$fontsize = "1-8";
		}
		else if ($heading == "3")
		{
			$fontsize = "1-6";
		}
		else if ($heading == "4")
		{
			$fontsize = "1-4";
		}
		else if ($heading == "5")
		{
			$fontsize = "1-2";
		}
		else
		{
			echo "unsupported heading; $heading";
		}
	}
	$margin_cssclass = nxs_getcssclassesforlookup("nxs-margin", $margin);
	
	if ($heightiq != "")
	{
		$heightiqprio = "p1";
		$heightiqgroup = "title";
		$cssclasses = nxs_concatenateargswithspaces($cssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$heightiqgroup}");
	}
	
	if ($microdata != "")
	{
		$itemprop = "itemprop='name'";
	}
	else
	{
		$itemprop = "";
	}

	if ($fontzen == "")
	{
		$fontzen = "2";
	}
	
	//
	$styles = array();
	$styles["fontsize"] = $fontsize;
	$styles["content_justify"] = $content_justify;
	$styles["colorzen"] = $colorzen;
	$styles["margin"] = $margin;
	$styles["margin_bottom"] = $margin_bottom;
	$styles["fontzen"] = $fontzen;
	$styles["line_height"] = "0-8";
	$styles["align"] = $align;
	
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_0 = $compiled[0]["id"];
	
	$result = '<' . $headingelement . ' ' . $itemprop . ' class="'.$unique_style_combination_class_0.'">' . $title . '</' . $headingelement . '>';
	
	// link
	if ($destination_articleid != "") 
	{
		$destination_url = nxs_geturl_for_postid($destination_articleid);
		$result = '<a href="' . $destination_url .'" '.$destination_target_html.' '.$destination_relation_html.'>' . $result . '</a>';
	}
	else if ($destination_url != "") 
	{
		$result = '<a href="' . $destination_url .'" '.$destination_target_html.' '.$destination_relation_html.'>' . $result . '</a>';
	}
	
	// inject the generated css inline
	foreach ($compiled as $id => $compiledresult)
	{
		$class = $compiledresult["id"];
		$rules = $compiledresult["rules"];
		if (count($rules) > 0)
		{
			$result .= '<style>';
			$result .= ".{$class} { " . implode($rules) . " }";
			$result .= '</style>';
		}
	}

	return $result;
}

// disable emojis, thanks
function disable_wp_emojicons() {

  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}
add_action( 'init', 'disable_wp_emojicons' );

function nxs_frontendframework_nxs2_wp_footer()
{
	$frameworkurl = nxs_getframeworkurl();
	
	// js
	$embeds = array
	(
		NXS_FRAMEWORKPATH . "/nexuscore/frontendframeworks/nxs2/js/nxs2.js",
	);
	foreach ($embeds as $embed)
	{
		?>
		<script>
			<?php
			echo file_get_contents($embed);
			?>
		</script>
		<?php
	}
	
	// css
	$css_embeds = array
	(
		NXS_FRAMEWORKPATH . "/css/css-reset.css",
		NXS_FRAMEWORKPATH . "/css/framework.css",
		NXS_FRAMEWORKPATH . "/css/framework-responsive.css",
	);
	foreach ($css_embeds as $embed)
	{
		?>
		<style>
			<?php
			$contents = file_get_contents($embed);
			// pimp the contents
			// * fonts should be referenced by an absolute url, not relative
			$contents = str_replace("../fonts/", "{$frameworkurl}/fonts/", $contents);
			echo $contents;
			?>
		</style>
		<?php
	}
	
	?>
	<script type="text/javascript" data-cfasync="false"  src="//www.google.com/jsapi"></script>
	<script type="text/javascript" data-cfasync="false" >
		google.load('webfont','1');
	</script>
	<?php
		/* FONT HANDLING v2 START */
		// fonts not used will not be loaded here, as this is nxs2 (optimized output, not editing features)
		$sitemeta = nxs_getsitemeta();
		$allfontfams = array();
		$allfonts = nxs_getfonts();	// font fam => text
		$fontidentifiers = nxs_font_getfontidentifiers();
		
		foreach ($allfonts as $currentfontid=>$meta)
		{
			$currentfontfams = nxs_getmappedfontfams($currentfontid);	// for example Oswald:400,300
			foreach ($currentfontfams as $currentfontfam)
			{
				// for example Oswald:400,300
				$fontfam_a = $currentfontfam;
				$pieces = explode(":", $fontfam_a);
				$fontfam_a = strtolower(trim($pieces[0]));	// for example Oswald
				
				// only include the font, if its actually being used
				$isfontused = false;
				foreach ($fontidentifiers as $fontzenid)
				{
					$fontfam_b = nxs_font_getcleanfontfam($sitemeta["vg_fontfam_{$fontzenid}"]);	// for example Oswald, sans-serif
					$pieces = explode(",", $fontfam_b);
					$fontfam_b = strtolower(trim($pieces[0]));
					
					if ($fontfam_a == $fontfam_b)
					{
						//echo "MATCH :); $fontfam_a loading $currentfontfam <br />";
						$isfontused = true;
						break;
					}
					else
					{
						//echo "mismatch; $currentfontfam != $sanitizedfontfamily <br />";
					}
				}
				
				if ($isfontused)
				{
					if (!in_array($currentfontfam, $allfontfams))
					{
						$allfontfams[] = $currentfontfam;
					}
					else
					{
						// ignore, dont load the same font multiple times
					}
				}
			}
		}
		?>	
	<script> 
		
		WebFont.load
		(
			{
				google: 
				{ 
	      	families: 
	      	[
	      		<?php
	      		// only load the fonts that are actually used
	      		
	      		// some fonts produce a 403 or 400, we skip these	
	      		$skipfonts = nxs_font_getskipfonts();
	      		foreach ($skipfonts as $skipfont)
	      		{
	      			if(($key = array_search($skipfont, $allfontfams)) !== false) 
	      			{
					   	 unset($allfontfams[$key]);
							}
						}
	      		
	      		$isfirstfont = true;
	      		foreach ($allfontfams as $currentfont)
	      		{
	      			if ($isfirstfont == false)
	      			{
	      				echo ",";
	      			}
	      			else
	      			{
	      				$isfirstfont = false;
	      			}
	      			
	      			if (nxs_stringcontains($currentfont, "'"))
	      			{
	      				echo "{$currentfont}";
	      			}
	      			else
	      			{
	      				// als het font al quotes bevat, dan niet wrappen in single QUOTES!!!!!
	      				echo "'{$currentfont}'";
	      			}
	      		}
	      		?>
	      	] 
	      }
			}
		); 
	</script>
	<?php
	/* FONT HANDLING v2 END */

	// 	
}

function nxs_clearunwantedscripts()
{
	// if we are in the frontend ...
	if (!is_admin())
	{
		// the theme could break if pointing to an incompatible version
		// therefore we remove jquery scripts added by third party plugins, such as NGG
  	//wp_deregister_script('jquery');
  	
  	
  	// 25 aug 2014; removed; woocommerce adds various scripts that are dependent upon
  	// jquery, and we ignore those too when using the approach below...
  	function nxs_modify_scripts() 
  	{
  		wp_deregister_script('jquery');
			wp_deregister_script('jquery-ui');
			wp_deregister_script('farbtastic');
			wp_deregister_style('farbtastic');
			wp_dequeue_style('farbtastic');
		}
		add_action('wp_print_scripts', 'nxs_modify_scripts', 100);
		add_action('wp_footer','nxs_frontendframework_nxs2_wp_footer');
  }
  else
  {
  	//add_action('admin_head','nxs_setjQ_nxs');
  }
}
add_action('init', 'nxs_clearunwantedscripts');

function nxs_framework_theme_styles()
{
	wp_dequeue_style('farbtastic');
	
	/*
	wp_register_style('nxs-framework-style-css-reset', 
    nxs_getframeworkurl() . '/css/css-reset.css', 
    array(), 
    nxs_getthemeversion(),    
    'all' );
  
  wp_register_style('nxs-framework-style', 
    nxs_getframeworkurl() . '/css/framework.css', 
    array(), 
    nxs_getthemeversion(), 
    'all' );
  
	wp_enqueue_style('nxs-framework-style-css-reset');
	wp_enqueue_style('nxs-framework-style');
    
	wp_register_style('nxs-framework-style-responsive', 
	    nxs_getframeworkurl() . '/css/framework-responsive.css', 
	    array(), 
	    nxs_getthemeversion(),
	    'all' );
	    
	wp_enqueue_style('nxs-framework-style-responsive');
	*/
  do_action('nxs_action_after_enqueue_baseframeworkstyles');
}
add_action('wp_enqueue_scripts', 'nxs_framework_theme_styles');



//
//
//

// layout specific shortcodes

function nxs_sc_nxspagerow($rowattributes, $content = null, $name='') 
{
	extract
	(
		shortcode_atts
		(
			array
			(
				"id" => '',
				"class" => ''
			)
			, 
			$rowattributes
		)
	);
	
	global $nxs_global_current_nxsposttype_being_rendered;
	global $nxs_global_current_postid_being_rendered;
	global $nxs_global_current_postmeta_being_rendered;
	global $nxs_global_current_rowindex_being_rendered;	
	global $nxs_global_current_render_mode;
	global $nxs_global_row_render_statebag;

	if ($nxs_global_current_nxsposttype_being_rendered == null) { nxs_webmethod_return_nack("nxs_global_current_nxsposttype_being_rendered is NOT set"); }
	if ($nxs_global_current_postid_being_rendered == null) { nxs_webmethod_return_nack("nxs_global_current_postid_being_rendered not set");}
	if ($nxs_global_current_render_mode == null) { nxs_webmethod_return_nack("nxs_global_current_render_mode not set"); }
	if ($nxs_global_current_postmeta_being_rendered === null) { nxs_webmethod_return_nack("nxs_global_current_postmeta_being_rendered  not set"); }	
	if ($nxs_global_current_rowindex_being_rendered == null) { nxs_webmethod_return_nack("nxs_global_current_rowindex_being_rendered  not set"); }	
	if ($nxs_global_row_render_statebag != null) { nxs_webmethod_return_nack("expected nxs_global_row_render_statebag to be null, but it isn't?"); }	

	$nxs_global_row_render_statebag = array();
	$nxs_global_row_render_statebag["pagerowtemplate"] = $rowattributes["pagerowtemplate"];
	$nxs_global_row_render_statebag["pagerowid"] = $rowattributes["pagerowid"];
	$nxs_global_row_render_statebag["rowindex"] = $nxs_global_current_rowindex_being_rendered;
	
	// render inner html
	$content = nxs_applyshortcodes($content);
	
	// note; the statebag could have been updated / populated by placeholders for outbound data / information
		
	extract($nxs_global_row_render_statebag, EXTR_PREFIX_ALL, "grs_");
	
	$pagerowtemplate = $rowattributes["pagerowtemplate"];
	$hidewheneditorinactive = $nxs_global_row_render_statebag["hidewheneditorinactive"];

	$additionalrowclasses = "";
	
	$upgradetofullwidth = $nxs_global_row_render_statebag["upgradetowidescreen"];	
	
	if (isset($nxs_global_row_render_statebag["rrs_cssclass"]))
	{
		$additionalrowclasses .= $nxs_global_row_render_statebag["rrs_cssclass"];
	}
	
	if ($pagerowtemplate == "141214")
	{
		// promote this row to exceptional responsive row
		$grs_upgradetoexceptionalresponsiverow = "true";
	}
	else if (
		$pagerowtemplate == "121414" ||
		$pagerowtemplate == "141412"
	)
	{
		// promote this row to exceptional responsive row
		$grs_upgradetoexceptionalresponsiverow2 = "true";
	}
	else if 
	(
		$pagerowtemplate == "1third2third" || 
		$pagerowtemplate == "1212" || 
		$pagerowtemplate == "131313" || 
		$pagerowtemplate == "14141414" || 
		$pagerowtemplate == "one" || 
		$pagerowtemplate == "twothirdonethird")
	{
		// no upgrade to exceptional responsive row
	}
	else
	{
		// echo "Unsupported pagerowtemplate; [$pagerowtemplate]";
		$pagerowtemplate = "one";
	}
	
	if (isset($grs_upgradetoexceptionalresponsiverow) && $grs_upgradetoexceptionalresponsiverow == "true")
	{
		$additionalrowclasses .= "nxs-exceptional-responsive-row ";
	}
	if (isset($grs_upgradetoexceptionalresponsiverow2) && $grs_upgradetoexceptionalresponsiverow2 == "true")
	{
		$additionalrowclasses .= "nxs-exceptional-responsive-row2 ";
	}
	
	$output = "";
	$cssclass = "";

	if ($rowattributes["pagerowid"] == "")
	{
		// indien de pagerowid niet gezet is...
		$rowidattribute = "";
	}
	else
	{
		$pagerowid = $rowattributes["pagerowid"];
		$rowidattribute = "id='nxs-pagerow-{$pagerowid}' ";
		
		$mixedattributes = array();
		$mixedattributes = array_merge($mixedattributes, nxs_getpagerowmetadata($nxs_global_current_postid_being_rendered, $pagerowid));
		
		//
		$combined_lookups = nxs_lookups_getcombinedlookups_for_currenturl();
		$combined_lookups = array_merge($combined_lookups, nxs_parse_keyvalues($mixedattributes["r_lookups"]));

		$combined_lookups = nxs_lookups_evaluate_linebyline($combined_lookups);
		
		// replace values in mixedattributes with the lookup dictionary
		$magicfields = array("r_enabled");
		$translateargs = array
		(
			"lookup" => $combined_lookups,
			"items" => $mixedattributes,
			"fields" => $magicfields,
		);
		$mixedattributes = nxs_filter_translate_v2($translateargs);		
		
		$cssclass = nxs_getcssclassesforrow($mixedattributes);
		
		$should_render_row = true;
		$r_enabled = strtolower(trim($mixedattributes["r_enabled"]));
		if ($r_enabled == "")
		{
			// its enabled in all its glory :) (default)
			$should_render_row = true;
		}
		else if ($r_enabled == "true")
		{
			// its enabled after evaluation
			$should_render_row = true;
			$cssclass .= " nxs-row-enabled-true"; 
		}
		else
		{
			$should_render_row = false;
		}
		
		if ($mixedattributes["r_widescreen"] != "")
		{
			$upgradetofullwidth = "yes";
		}
	}
	
	if ($upgradetofullwidth == "yes")
	{
		if ($pagerowtemplate == "one")
		{
			$additionalrowclasses .= " widescreen-row ";
		}
		else
		{
			// not allowed
		}
	}
	
	$cssclassrowtemplate = "nxs-rowtemplate-" . $nxs_global_row_render_statebag["pagerowtemplate"];
	
	if ($hidewheneditorinactive === true)
	{
		$cssclass .= " nxs-hidewheneditorinactive ";
	}
	
	$r_colorzen = nxs_getcssclassesforlookup("nxs-colorzen-", $mixedattributes["r_colorzen"]);
	$r_margin_top = nxs_getcssclassesforlookup("nxs-margin-top-", $mixedattributes["r_margin_top"]);
	$r_padding_top = nxs_getcssclassesforlookup("nxs-padding-top-", $mixedattributes["r_padding_top"]);
	
	// 
	$styles = array();
	$styles["colorzen"] =  $r_colorzen;
	$styles["margin_top"] =  $r_margin_top;
	$styles["padding_top"] =  $r_padding_top;
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_0 = $compiled[0]["id"];
	
	if (isset($grs_upgradetofullwidth) && $grs_upgradetofullwidth) 
	{
		
		$output .= "<div class='nxs-row " . $compiled[0]["id"] . " {$cssclass} {$cssclassrowtemplate}' {$rowidattribute}>";
		$output .= "<div class='nxs-row-container nxs-row2'>";
		$output .= "<div class='nxs-fullwidth nxs-containsimmediatehovermenu " . $additionalrowclasses . " '>";
	}
	else
	{
		//$output .= "<div class='nxs-row {$cssclass} {$cssclassrowtemplate} " . $additionalrowclasses . " ' {$rowidattribute}>";
		$output .= "<div class='nxs-row " . $compiled[0]["id"] . " {$cssclass} {$cssclassrowtemplate} " . $additionalrowclasses . " ' {$rowidattribute}>";
		$output .= "<div class='nxs-row-container nxs-containsimmediatehovermenu nxs-row1'>";
	}
	
	if ($nxs_global_current_render_mode == "default")
	{
		if (false) 
		{
			if ($nxs_global_current_nxsposttype_being_rendered == "menu")
			{
				
			}
			else if ($nxs_global_current_nxsposttype_being_rendered == "slideset")
			{
				
			}
			else if ($nxs_global_current_nxsposttype_being_rendered == "list")
			{
				
			}
			else if ($nxs_global_current_nxsposttype_being_rendered == "genericlist")
			{
				
			}
			else if ($nxs_global_current_nxsposttype_being_rendered == "busrulesset")
			{
				
			}
			else
			{
				$shouldrenderrowhover = false;
				
				if (nxs_cap_hasdesigncapabilities())
				{
					$shouldrenderrowhover = true;
				}
			
				if ($shouldrenderrowhover)
				{
					// pop up menu
					$output .= "<div class='nxs-hover-menu nxs-row-hover-menu nxs-admin-wrap outside-left-top'>";
					
					$output .= '<ul>';
	      	$output .= '<li>';
	      	
	      	$onclick = 'onclick="nxs_js_edit_row(this); return false;"';
	      	$title = nxs_l18n__("Click to configure this row", "nxs_td");
	      	if (!isset($nxs_global_row_render_statebag["pagerowid"]) || $nxs_global_row_render_statebag["pagerowid"] == "")
					{
						// downwards compatibility, to be removed eventually
						$onclick = "";
						$title = nxs_l18n__("This row is not configurable (#34568793875)", "nxs_td");
					}

	      	if ($r_enabled != "")
	      	{
	      		
	      		
	      		
	      		$circle_color = "#DFDFDF";
	      		$text_color = "#000000";
	      		if ($r_enabled == "true")
	      		{
	      			$circle_color = "#00EE00";
	      			$text_color = "#FFFFFF";
	      		}
	      		else
	      		{
	      			$circle_color = "#FF0000";
	      			$text_color = "#FFFFFF";
	      		}
	      		
	      		$notificationargs = array
	      		(
	      			"link_growl" => "This indicates the row is enabled or disabled based upon a condition",
	      			"circle_color" => $circle_color,
	      			"text_color" => $text_color,
	      			"text" => "C",
	      		);
	      		$notificationhtml = nxs_gethtmlfornotification($notificationargs);
	      		
	      		$output .= $notificationhtml;
	      	}
					
	      	$output .= '<a href="#" ' . $onclick . ' title="' . $title . '">';
	      	$output .= '<span class="nxs-icon-arrow-right"></span>';
	      	
	        $output .= '</a>';
					
					//
					// submenu start
					//
					
					$output .= '<ul>';

					// move row
					$output .= "<li class='nxs-dragrow-handler' style='cursor:move;' title='" . nxs_l18n__("Move row", "nxs_td") ."'><span class='nxs-icon-move'></span></li>";
					
					// delete row					
					$output .= "<a class='nxs-no-event-bubbling nxs-defaultwidgetdeletehandler' href='#' onclick='nxs_js_row_remove(this); return false;'><li title='" . nxs_l18n__("Remove row[nxs:hovermenu,tooltip]", "nxs_td") ."'><span class='nxs-icon-trash'></span></li></a>";


					$output .= "</ul> <!-- nxs-sub-menu -->";
	
					//
					// submenu end
					//
	
	      	$output .= '</li>';      	
					
					$output .= '</ul> <!-- nxs-menu -->';
					
					$output .= "</div>";
				}
			}
		}
	}
	else if ($nxs_global_current_render_mode == "anonymous")
	{
		//
	}
	else
	{
		nxs_webmethod_return_nack("nxs_global_current_render_mode (nog?) niet ondersteund: {$nxs_global_current_render_mode}");
	}
	
	$output .= "<ul class='nxs-placeholder-list'>";
	$output .= $content;
	$output .= "</ul>";
	$output .= "<div class='nxs-clear'></div>";

	if (isset($grs_upgradetofullwidth) && $grs_upgradetofullwidth) 
	{
		$output .= "</div> <!-- nxs-fullwidth -->";
		$output .= "</div> <!-- nxs-row-container -->";
		$output .= "</div>";
	}
	else
	{
		$output .= "</div> <!-- nxs-row-container -->";		
		$output .= "</div>";
	}

	foreach ($compiled as $id => $compiledresult)
	{
		$class = $compiledresult["id"];
		$rules = $compiledresult["rules"];
		if (count($rules) > 0)
		{
			$output .= '<style>';
			$output .= ".{$class} { " . implode($rules) . " }";
			$output .= '</style>';
		}
	}

	// widgets have the capability to tell the row to etch itself
	// (for example entities widgets)
	if ($nxs_global_row_render_statebag["etchrow"] === true)
	{
		if (!is_user_logged_in())
		{
			$output = ""; // "<!-- and its gone -->";
		}
	}
	
	// if you require any capability, this means you have to be logged in,
	// and thus we hide it here (this frontendframework optimized output, noise should
	// not be generated)
	if ($nxs_global_row_render_statebag["requiredcapabilities"] != "")
	{
		$output = ""; // "<!-- and its gone (2) -->";
	}
	
	// global variable no longer needed
	$nxs_global_row_render_statebag = null;
	
	if ($pagerowtemplate == "")
	{
		//
		$output = "";
	}
	if ($should_render_row === false)
	{
		$output = "";
	}
	
	return $output;
}
add_shortcode('nxspagerow', 'nxs_sc_nxspagerow');

function nxs_nxsphcontainer($atts, $content = null, $name='') 
{
	extract(shortcode_atts(array(
		"id" => '',
		"class" => ''
	), $atts));
	
	global $nxs_global_row_render_statebag;
	if ($nxs_global_row_render_statebag == null)
	{
		nxs_webmethod_return_nack("expected nxs_global_row_render_statebag to be set, but it isn't?");
	}
	$nxs_global_row_render_statebag["width"] = $atts["width"];
	
	// statebag for rendering this placeholder
	global $nxs_global_current_postid_being_rendered;
	global $nxs_global_current_postmeta_being_rendered;
	global $nxs_global_placeholder_render_statebag;
	global $nxs_global_current_render_mode;
		
	$nxs_global_placeholder_render_statebag = array();
	
	// perform actual render of the placeholder (delegates to widget)
	$content = nxs_applyshortcodes($content);
	
	extract($nxs_global_placeholder_render_statebag, EXTR_PREFIX_ALL, "gphs");	// underscore is added automatically
	
	$widgetmetadata = $nxs_global_placeholder_render_statebag["widgetmetadata"];
	
	$phdataattributeshtml = "";
	$data_atts = $nxs_global_placeholder_render_statebag["data_atts"];
	if (isset($data_atts))
	{
		foreach ($data_atts as $key => $val)
		{
			$phdataattributeshtml .= "data-{$key}='{$val}' ";
		}
	}
	
	
	// hover menu's
	$menutopleft = "";
	$menutopright = "";
	$menutypecontainer = "";
	
	$cropwidgetclass = "nxs-crop ";
	if (isset($gphs_widgetcropping) && $gphs_widgetcropping == "no")
	{
		// no cropping, this is needed, for example, in the slider, which exceeds the regular boundaries of the widget
		$cropwidgetclass = "";
	}
	
	$bottommarginclass = nxs_getcssclassesforlookup("nxs-margin-bottom-", $widgetmetadata["ph_margin_bottom"]);
	
	// ----------------------
	
	if ($nxs_global_current_render_mode == "default")
	{
		if (nxs_has_adminpermissions()) 
		{
			if (isset($gphs_placeholderrenderresult) && $gphs_placeholderrenderresult == "OK")
			{
				// er zijn geen fouten opgetreden bij het renderen van de widget
				
				$placeholdertemplate = $gphs_placeholdertemplate;
				$placeholdertitle = nxs_getplaceholdertitle($placeholdertemplate);
				
				if (isset($gphs_menutopleft) && $gphs_menutopleft != "")
				{
					$menutopleft .= "<div class='nxs-hover-menu-positioner'>";
					$menutopleft .= "<div class='nxs-hover-menu nxs-widget-hover-menu nxs-admin-wrap inside-left-top'>";
					$menutopleft .= $gphs_menutopleft;
					$menutopleft .= "</div>";
					$menutopleft .= "</div>";
				}
				else
				{
					// no top left menu is needed
					
				}
				
				if (false)
				{
					// nxs2 is optimized; all fluff is not rendered
					if ($gphs_menutopright != "")
					{
						$menutopright .= "
						<div class='nxs-hover-menu-positioner'>
						<div class='nxs-hover-menu nxs-widget-hover-menu nxs-admin-wrap inside-right-top'>
						" . $gphs_menutopright . "
						</div>
						</div>
						";
					}
					else
					{
						// no top right menu is needed
					}
				}
			}
			else
			{
				// an errror occured when rendering the widget,
				// if this is the case we allow the user to move the widget (as no specific logic is required)
				// and to delete the item

				if (nxs_shoulddebugmeta())
				{
					nxs_ob_start();
					?>
					<a class='nxs-no-event-bubbling' href='#' onclick="nxs_js_edit_widget_v2(this, 'debug'); return false; return false;">
	         	<li title='<?php nxs_l18n_e("Debug[tooltip]", "nxs_td"); ?>'>
	         		<span class='nxs-icon-search'></span>
	         	</li>
	      	</a>
	      	<?php
	      	$debughtml = nxs_ob_get_contents();
					nxs_ob_end_clean();
				}
				else
				{
					$debughtml = "";
				}
				
				$menutopright .= "
				<div class='nxs-hover-menu-positioner'>
				<div class='nxs-hover-menu nxs-widget-hover-menu nxs-admin-wrap inside-right-top'>
				<ul>

				<a class='nxs-no-event-bubbling' href='#' onclick='nxs_js_popup_placeholder_wipe(\"" . $nxs_global_current_postid_being_rendered . "\", \"" . $gphs_placeholderid . "\"); return false;'>
				<li title='" . nxs_l18n__("Remove widget[nxs:hovermenu,tooltip]", "nxs_td") ."'><span class='nxs-icon-trash'></span></li>
				</a>
				
				" . $debughtml . "
				
				</ul>
				</div>
				</div>";
			}
		}
		else
		{
			// no access
		}
	}
	else
	{
		// not needed
	}
	
	// ------------------------------------------ cursors
	
	if (false)
	{
		if (nxs_has_adminpermissions())
		{
			// het 'hover' element; als de muis boven de placeholder hangt, zien we dit element
			$droplayerhtml = "<div class='nxs-runtime-autocellsize nxs-cursor nxs-drop-cursor'><span class='nxs-runtime-autocellsize'></span></div>";
			$cursorlayerhtml = "<div title='" . nxs_l18n__("Edit[nxs:hovermenu,tooltip]", "nxs_td") ."' class='nxs-runtime-autocellsize nxs-cursor nxs-cell-cursor'><span class='nxs-runtime-autocellsize'></span></div>";
		}
		else
		{
			$droplayerhtml = "";
			$cursorlayerhtml = "";
		}
	}

	if ($nxs_global_current_render_mode == "default")
	{
		$placeholdercursors = $droplayerhtml . $cursorlayerhtml;
	}
	else if ($nxs_global_current_render_mode == "anonymous")
	{
		$placeholdercursors = "";
	}
	
	// ------------------------------------------
	
	$ph_colorzen = nxs_getcssclassesforlookup("nxs-colorzen-", $widgetmetadata["ph_colorzen"]);
	$ph_linkcolorvar = nxs_getcssclassesforlookup("nxs-linkcolorvar-", $widgetmetadata["ph_linkcolorvar"]);
	
	$ph_padding = nxs_getcssclassesforlookup("nxs-padding-", $widgetmetadata["ph_padding"]);
	$ph_valign = $widgetmetadata["ph_valign"];
	
	$ph_text_fontsize = nxs_getcssclassesforlookup("nxs-text-fontsize-", $widgetmetadata["ph_text_fontsize"]);
		
	$ph_border_radius = nxs_getcssclassesforlookup("nxs-border-radius-", $widgetmetadata["ph_border_radius"]);
	$ph_borderwidth = nxs_getcssclassesforlookup("nxs-border-width-", $widgetmetadata["ph_border_width"]);
	$ph_cssclass = $widgetmetadata["ph_cssclass"];
	
	// css classes that were added while rendering the widget at runtime
	$ph_runtimecssclass = $nxs_global_placeholder_render_statebag["ph_runtimecssclass"];

	// unistyle css classes	
	if (isset($widgetmetadata["unistyle"]) && $widgetmetadata["unistyle"] != "")
	{
		$ph_unistyleindicator_cssclass = "nxs-unistyled";
		$ph_unistyle_cssclass = "nxs-unistyle-" . nxs_stripspecialchars($widgetmetadata["unistyle"]);
	}
	else
	{
		$ph_unistyle_cssclass = "";
		$ph_unistyleindicator_cssclass = "nxs-not-unistyled";
	}
	
	// unicontent css classes	
	if (isset($widgetmetadata["unicontent"]) && $widgetmetadata["unicontent"] != "")
	{
		$ph_unicontentindicator_cssclass = "nxs-unicontented";
		$ph_unicontent_cssclass = "nxs-unicontent-" . nxs_stripspecialchars($widgetmetadata["unicontent"]);
	}
	else
	{
		$ph_unicontentindicator_cssclass = "nxs-not-unicontented";
		$ph_unicontent_cssclass = "";
	}

	// widgettype css classes	
	if (isset($widgetmetadata["type"]) && $widgetmetadata["type"] != "")
	{
		$ph_widgettype_cssclass = "nxs-widgettype-" . nxs_stripspecialchars($widgetmetadata["type"]);
	}
	else
	{
		$ph_widgettype_cssclass = "";
	}

	// clear the statebag for rendering this placeholder	
	$nxs_global_placeholder_render_statebag = null;

	$widthsupported = false;
	$widthclass = "";

	if ($atts["width"] == "1")
	{
		$widthsupported = true;
		$widthclass = "nxs-one-whole";
	}
	else if ($atts["width"] == "2/3")
	{
		$widthsupported = true;
		$widthclass = "nxs-two-third";		
	}
	else if ($atts["width"] == "1/2")
	{
		$widthsupported = true;
		$widthclass = "nxs-one-half";
	}
	else if ($atts["width"] == "1/3")
	{
		$widthsupported = true;
		$widthclass = "nxs-one-third";
	}	
	else if ($atts["width"] == "1/4")
	{
		$widthsupported = true;
		$widthclass = "nxs-one-fourth";
	}	
	else
	{
		$output = "<li>{$content} (BREEDTE (NOG?) NIET VOLLEDIG ONDERSTEUND)</li>";
	}
		
	if ($widthsupported)
	{
		$output = "";
		
		$concatenated_css = nxs_concatenateargswithspaces($widthclass, $bottommarginclass, $ph_cssclass, $ph_text_fontsize, $ph_unistyle_cssclass, $ph_unistyleindicator_cssclass, $ph_unicontent_cssclass, $ph_unicontentindicator_cssclass, $ph_widgettype_cssclass, $ph_runtimecssclass);
		
		$styles = array();
		$styles["margin_bottom"] = $bottommarginclass;
		$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
		
		$output .= "<li class='" . $compiled[0]["id"] . " nxs-placeholder nxs-containshovermenu1 nxs-runtime-autocellsize " . $concatenated_css . "' {$phdataattributeshtml}>";
		//$output .= $menutopleft;	// will be empty if not allowed, or not needed
		//$output .= $menutopright;	// will be empty if not allowed, or not needed
		//$output .= $placeholdercursors;	// will be empty if not allowed, or not needed
		
		$concatenated_css = nxs_concatenateargswithspaces($ph_colorzen, $ph_linkcolorvar, $ph_border_radius, $ph_borderwidth);
		
		$heightclass = "";
		if ($widgetmetadata["ph_valign"] == "nxs-valign-top" || $widgetmetadata["ph_valign"] == "")
		{
			$heightclass = "nxs-height100";
		}
		
		$output .= "<div class='ABC $heightclass $concatenated_css'>";

		$concatenated_css = nxs_concatenateargswithspaces($ph_padding, $ph_valign);
		$output .= '<div class="XYZ ' . $concatenated_css . '">';
		
		$output .= "<div class='nxs-placeholder-content-wrap " . $cropwidgetclass . "'>";
		$output .= $content;
		$output .= "</div>";
		
		$output .= "</div>";
		$output .= "</div>";
		
		$output .= "</li>";
		
		foreach ($compiled as $id => $compiledresult)
		{
			$class = $compiledresult["id"];
			$rules = $compiledresult["rules"];
			if (count($rules) > 0)
			{
				$output .= '<style>';
				$output .= ".{$class} { " . implode($rules) . " }";
				$output .= '</style>';
			}
		}
	}
	
	return $output;
}
add_shortcode('nxsphcontainer', 'nxs_nxsphcontainer');

function nxs_nxsplaceholder($inlinepageattributes, $content = null, $name='') 
{
	extract(shortcode_atts(array(
		"id" => '',
		"class" => ''
	), $inlinepageattributes));
	
	//
	global $nxs_global_current_nxsposttype_being_rendered;
	global $nxs_global_current_postid_being_rendered;
	global $nxs_global_current_postmeta_being_rendered;
	global $nxs_global_current_rowindex_being_rendered;
	global $nxs_global_current_render_mode;	
	global $nxs_global_row_render_statebag;	
	global $nxs_global_placeholder_render_statebag;
	
	if ($nxs_global_current_nxsposttype_being_rendered == null)
	{
		echo "nxs_global_current_nxsposttype_being_rendered == null (2)";
	}
	
	if ($nxs_global_current_rowindex_being_rendered == null)
	{
		echo "nxs_global_current_rowindex_being_rendered == null";
	}
	
	if ($nxs_global_current_postid_being_rendered == null || $nxs_global_current_render_mode == null)
	{
		nxs_webmethod_return_nack("nxs_global_current_postid_being_rendered ($nxs_global_current_postid_being_rendered) en/of nxs_global_current_render_mode ($nxs_global_current_render_mode) is NIET gezet (B)");
	}
	
	if ($nxs_global_current_postmeta_being_rendered === null)
	{
		echo "nxs_global_current_postmeta_being_rendered is NIET gezet b";
	}
	
	if ($nxs_global_current_rowindex_being_rendered == null)
	{
		nxs_webmethod_return_nack("nxs_global_current_rowindex_being_rendered is niet gezet (2)");
	}
	if ($nxs_global_row_render_statebag == null)
	{
		nxs_webmethod_return_nack("expected nxs_global_row_render_statebag to be set, but it isn't?");
	}
	
	//
	$postid = $nxs_global_current_postid_being_rendered;	
	$placeholderid = $inlinepageattributes["placeholderid"];	
	if ($placeholderid == null || $placeholderid == '')
	{
		// incorrectly configured
		return "<div>incorrectly configured; placeholderid attribute not found on page $postid</div>";
	}
	$placeholdertemplate = nxs_getplaceholdertemplate($postid, $placeholderid);
	
	$temp_array = nxs_getwidgetmetadata($postid, $placeholderid);
	
	// blend unistyle properties
	$unistyle = $temp_array["unistyle"];
	if (isset($unistyle) && $unistyle != "")
	{
		// blend unistyle properties with the metadata
		$unistyleprefix = nxs_getunifiedstylinggroup($placeholdertemplate);
		if (isset($unistyleprefix) && $unistyleprefix != "")
		{
			$unistyleproperties = nxs_unistyle_getunistyleproperties($unistyleprefix, $unistyle);
			$temp_array = array_merge($temp_array, $unistyleproperties);	
		}
		else
		{
			// strange; unistyle is set, but widget doesn't support unistyling?
		}
	}
	
	// store the widgetmetadata; its used in the phcontainer "later on"
	$nxs_global_placeholder_render_statebag["widgetmetadata"] = $temp_array;
	
	$mixedattributes = array_merge($inlinepageattributes, $temp_array);
	$mixedattributes["postid"] = $postid;
	$mixedattributes["rendermode"] = $nxs_global_current_render_mode;
	$mixedattributes["contenttype"] = "webpart";
	$mixedattributes["webparttemplate"] = "render_htmlvisualization";
	$mixedattributes["placeholderid"] = $placeholderid;
	$mixedattributes["placeholdertemplate"] = $placeholdertemplate;
	
	// prefetch metadata 
	$widgetmetadata = nxs_getwidgetmetadata($postid, $placeholderid);
	$mixedattributes["widgetmetadata"] = $widgetmetadata;
	
	//
	$placeholderrenderresult = nxs_getrenderedwidget($mixedattributes);
	
	$nxs_global_placeholder_render_statebag["placeholderrenderresult"] = $placeholderrenderresult["result"];	// bijv. "OK"
	$nxs_global_placeholder_render_statebag["placeholdertemplate"] = $placeholdertemplate;
	$nxs_global_placeholder_render_statebag["placeholderid"] = $placeholderid;
	
	if (false)
	{
		if (nxs_has_adminpermissions())
		{
			// het 'hover' element; als de muis boven de placeholder hangt, zien we dit element
			$droplayerhtml = "<div class='nxs-runtime-autocellsize nxs-cursor nxs-drop-cursor'><span class='nxs-runtime-autocellsize'></span></div>";
			$cursorlayerhtml = "<div title='" . nxs_l18n__("Edit[nxs:hovermenu,tooltip]", "nxs_td") ."' class='nxs-runtime-autocellsize nxs-cursor nxs-cell-cursor'><span class='nxs-runtime-autocellsize'></span></div>";
		}
		else
		{
			$droplayerhtml = "";
			$cursorlayerhtml = "";
		}
	}
	
	$widgetclass = "";
	if (isset($nxs_global_placeholder_render_statebag["widgetclass"]) && $nxs_global_placeholder_render_statebag["widgetclass"] != null)
	{
		$widgetclass = $nxs_global_placeholder_render_statebag["widgetclass"];
	}
	
	$healthclass = "";
	if ($nxs_global_placeholder_render_statebag["placeholderrenderresult"] != "OK")
	{
		// a problem occured (for example; widget not found)
		$healthclass = "nxs-render-error";
	}
	
	$inlinehtml = "";		
	$inlinehtml .= "<div id='nxs-widget-" . $placeholderid . "' class='nxs-widget nxs-widget-" . $placeholderid . " " . $healthclass . " " . $widgetclass . "'>";
	
	if ($placeholderrenderresult["result"] == "OK")
	{
		$inlinehtml .= $placeholderrenderresult["html"];
	}
	else
	{
		// output error message
		$inlinehtml .= nxs_getplaceholderwarning($placeholderrenderresult["message"] . " [" . $placeholdertemplate . "]");
	}
	
	$inlinehtml .= "</div>";
	
	if ($nxs_global_current_render_mode == "default")
	{
		$result = $inlinehtml;
	}
	else if ($nxs_global_current_render_mode == "anonymous")
	{
		$result = $inlinehtml;
	}
	else
	{
		nxs_webmethod_return_nack("nxs_global_current_render_mode (nog?) niet ondersteund:" . $nxs_global_current_render_mode);
	}
	
	return $result;	
}
add_shortcode('nxsplaceholder', 'nxs_nxsplaceholder');

function nxs_frontendframework_nxs2_setgenericwidgethovermenu($args)
{
	// do nothing (on purpose)
}

function nxs_frontendframework_nxs2_gethtmlforimage($args)
{
	extract($args);
	
	$image_alt = trim($image_alt);
	$image_title = trim($image_title);
	$image_maxheight_cssclass = nxs_getcssclassesforlookup("nxs-maxheight-", $image_maxheight);

	if ($image_size == "")
	{
		$image_size = "auto-fit";
	}
	
	// Image metadata
	if ($image_imageid == "" && $image_src == "") 
	{
		return "";
	}
	if (!nxs_isimagesizevisible($image_size))
	{
		return "";
	}
	
	// Image shadow
	if ($image_shadow != "") {
		$image_shadow = 'nxs-shadow';
	}
	
	// Hover effects
	if ($enlarge != "") { $enlarge = 'nxs-enlarge'; }
	if ($grayscale != "") {	$grayscale = 'nxs-grayscale'; }
	
	// escape quotes used in title and alt, preventing malformed html
	$image_title = str_replace("\"", "&quote;", $image_title);
	$image_alt = str_replace("\"", "&quote;", $image_alt);
	
	$wpsize = nxs_getwpimagesize($image_size);
	
	if ($image_imageid != "")
	{
		$imagemetadata= nxs_wp_get_attachment_image_src($image_imageid, $wpsize, true);
	
		// Returns an array with $imagemetadata: [0] => url, [1] => width, [2] => height
		$imageurl 		= $imagemetadata[0];
		$imageurl = nxs_img_getimageurlthemeversion($imageurl);
		$imagewidth 	= $imagemetadata[1] . "px";
		$imageheight 	= $imagemetadata[2] . "px";	
	}
	else if ($image_src != "")
	{
		$imageurl = $image_src;
	}
	
	$image_size_cssclass = nxs_getimagecsssizeclass($image_size);
	$image_alignment_cssclass = nxs_getimagecssalignmentclass($image_alignment); // "nxs-icon-left";
	
	// Border size
	$image_border_width = nxs_getcssclassesforlookup("nxs-border-width-", $image_border_width);
	
	$image_margin_cssclass = nxs_getcssclassesforlookup("nxs-margin-", $image_margin);
	$border_radius_cssclass = nxs_getcssclassesforlookup("nxs-border-radius-", $border_radius);
	
	$styles = array();
	$styles["maxheight"] = $image_maxheight_cssclass;
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_0 = $compiled[0]["id"];

	
	// Image border
	$image_border = '';
	$image_border .= '<div class="nxs-image-wrapper ' . $image_shadow . ' ' . $image_size_cssclass . ' ' . $image_alignment_cssclass . ' ' . '">';
	$image_border .= '<div style="right: 0; left: 0; top: 0; bottom: 0; border-style: solid;" class="nxs-overflow ' . $image_border_width . '">';
	// note the display: block is essential/required! else the containing div
	// will have two additional pixels; kudos to http://stackoverflow.com/questions/8828215/css-a-2-pixel-line-appears-below-image-img-element
	
	$styles = array();
	$styles["maxheight"] = $image_maxheight_cssclass;
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_0 = $compiled[0]["id"];

	$image_border .= '<img class="' . $compiled[0]["id"] . '" ';
	
	$image_border .= 'src="' . $imageurl . '" ';
	if ($image_alt != "")
	{
		$image_border .= 'alt="' . $image_alt . '" ';
	}
	if ($image_title != "")
	{
		$image_border .= 'title="' . $image_title . '" ';
	}
	$image_border .= '/>';
	
	$image_border .= $htmlforimage;
	
	foreach ($compiled as $id => $compiledresult)
	{
		$class = $compiledresult["id"];
		$rules = $compiledresult["rules"];
		if (count($rules) > 0)
		{
			$image_border .= '<style>';
			$image_border .= ".{$class} { " . implode($rules) . " }";
			$image_border .= '</style>';
		}
	}



	
	$image_border .= '</div>';
	$image_border .= '</div>';
	
	// Image shadow
	// TODO: make ddl too
	if ($image_shadow != "") 				{ $image_shadow = 'nxs-shadow'; }
	
	// Image link
	if ($destination_articleid != "") 
	{
		$destination_articleid = nxs_geturl_for_postid($destination_articleid);
		$image_border = '<a href="' . $destination_articleid .'">' . $image_border . '</a>';
	} else if ($destination_url != "") {
		$image_border = '<a href="' . $destination_url .'" target="_blank">' . $image_border . '</a>';
	}
	
	// Image
	$result = '';
	if ($image_imageid != "" || $image_src != "")
	{
		$result .= '<div class="nxs-relative">';
		$result .= $image_border;
		$result .= '</div>';
	}
	
	return $result;	
}

function nxs_sc_wrap($atts, $content = null, $name='') 
{
	extract($atts);
	
	$unwrapped_content = do_shortcode($content);
	
	//
	
	$styles = array();
	$styles["colorzen"] = $colorzen;
	$styles["padding"] = $padding;
	$styles["margin"] = $margin;
	$styles["border_radius"] = $border_radius;
	
	//
	
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_0 = $compiled[0]["id"];
	
	$result = "<div class='{$unique_style_combination_class_0}'>{$unwrapped_content}</div>";
	
	// render style
	foreach ($compiled as $id => $compiledresult)
	{
		$class = $compiledresult["id"];
		$rules = $compiledresult["rules"];
		if (count($rules) > 0)
		{
			$result .= '<style>';
			$result .= ".{$class} { " . implode($rules) . " }";
			$result .= '</style>';
		}
	}
	
	return $result;
}
add_shortcode('nxs_wrap', 'nxs_sc_wrap');

function nxs_frontendframework_nxs2_gethtmlfortext($args)
{
	extract($args);
	
	if ( $text == "")
	{
		return "";
	}
	
	if ($wrappingelement == "") {
	$wrappingelement = 'p';
	}
	
	// Text styling
	if ($showliftnote != "") { $showliftnote_cssclass = 'nxs-liftnote'; }
	if ($showdropcap != "") { $showdropcap_cssclass = 'nxs-dropcap'; }
	
	
	$alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $align);
	$fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $fontzen);
	
	$cssclasses = nxs_concatenateargswithspaces("nxs-default-p", "nxs-applylinkvarcolor", "nxs-padding-bottom0", $alignment_cssclass, $showliftnote_cssclass, $showdropcap_cssclass, $fontzen_cssclass);
	
	if ($heightiq != "") 
	{
		$heightiqprio = "p1";
		$heightiqgroup = "text";
		$cssclasses = nxs_concatenateargswithspaces($cssclasses, "nxs-heightiq", "nxs-heightiq-{$heightiqprio}-{$heightiqgroup}");
	}
	
	// apply shortcode on text widget
	$text = do_shortcode($text);
	
	if ($fontzen == "")
	{
		$fontzen = "1";
	}
	$fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $fontzen);
	
	$styles = array();
	$styles["fontsize"] = $fontsize;
	$styles["colorzen"] = $colorzen;
	$styles["fontzen"] = $fontzen_cssclass;
	$styles["align"] = $align;
	$styles["texttype"] = $texttype;
	$styles["line_height"] = $line_height;
	
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_0 = $compiled[0]["id"];
		
	$result .= '<'. $wrappingelement . ' class="' . $unique_style_combination_class_0 . ' ' . $cssclasses . '">' . $text . '</'. $wrappingelement . '>';
	
	foreach ($compiled as $id => $compiledresult)
	{
		$class = $compiledresult["id"];
		$rules = $compiledresult["rules"];
		if (count($rules) > 0)
		{
			$result .= '<style>';
			$result .= ".{$class} { " . implode($rules) . " }";
			$result .= '</style>';
		}
	}
	
	return $result;
}
