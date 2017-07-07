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

function nxs_frontendframework_nxs2_compilestyle($styles)
{
	$rules = array();
	foreach ($styles as $key => $val)
	{
		$val = trim($val);		
		$concatenated .= "{$key}{$val}";
		if ($key == "button_alignment")
		{
			if ($val == "nxs-align-left")
			{
				$rules[] = "text-align: left;";
			}
			else if ($val == "nxs-align-center")
			{
				$rules[] = "text-align: center;";
			}
			else if ($val == "nxs-align-right")
			{
				$rules[] = "text-align: right;";
			}
			else
			{
				// unknown?
				$rules[] = "text-align: unsupported_{$val};";
			}
		}
		else if ($key == "padding_bottom")
		{
			if ($val == "nxs-padding-bottom0")
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
		}
		else if ($key == "colorzen")
		{
			$rules[] = "nxs-style: $val;";
			$rules[] = "border-style: solid;";
			
			$parts = explode("-", $val);	// nxs-colorzen-c12-dm
			$coloridentification = $parts[2];		// c12

			$middle = "777777";
			if (nxs_hassitemeta())
			{
				$palettename = nxs_colorization_getactivepalettename();
				$colorizationproperties = nxs_colorization_getpersistedcolorizationproperties($palettename);
				$thekey = "colorvalue_" . $coloridentification;
				if (isset($colorizationproperties[$thekey]))
				{
					$middle = $colorizationproperties[$thekey];	// bijv. "#4054BF"
				}
			}
			
			$transformation = $parts[3];
	
			$delta = 0.2;
	
			if ($transformation == "dm")
			{
				// dark to middle
				$hex_from = nxs_getlighterhexcolor($middle, -$delta);
				$hex_to = $middle;
				$rules[] = nxs_getlineairgradientcss($hex_from, $hex_to);
			}
			else
			{
				$rules[] = "unsupportedtransformation";
				echo "unsupported transformation; $transformation";
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
		else if ($key == "button")
		{
			$rules[] = "border-width: 1px;";
    	$rules[] = "border-radius: 3px;";
    	$rules[] = "cursor: pointer;";
		}
		else if ($key == "button_scale")
		{
			if ($val == "nxs-button-scale-1-8")
			{
				$rules[] = "font-size: 22px;";
				$rules[] = "padding-left: 18px;";
    		$rules[] = "padding-right: 18px;";
    		$rules[] = "padding-top: 11px;";
    		$rules[] = "padding-bottom: 11px;";
			}
			else
			{
				$rules[] = "unsupported_button_scale_{$key}:{$val}";
			}
		}
		else
		{
			$rules[] = "unsupported_{$key}:{$val}";
		}
	}
	
	if (count($rules) == 0)
	{
		// no impact, no rules nor id :)
		return;
	}
	
	if (is_user_logged_in())
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
			add_action("wp_footer", "nxs_frontendframework_nxs2_footer");
		}
		
		$nxs_gl_style_footer_cssrules[$id] = $rules;
	}

	return $result;
}

function nxs_frontendframework_nxs2_gethtmlforbutton($args)
{
	extract($args);
	
	if ($button_text == "")
	{
		return "";
	}
	if ($destination_articleid == "" && $destination_url == "" && $destination_js == "")
	{
		return "";
	}		

	$button_alignment = nxs_getcssclassesforlookup("nxs-align-", $button_alignment);
	$button_color = nxs_getcssclassesforlookup("nxs-colorzen-", $button_color);
	$button_scale_cssclass = nxs_getcssclassesforlookup("nxs-button-scale-", $button_scale);
	$button_fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $button_fontzen);
	
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
	$styles["button_alignment"] = $button_alignment;
	$styles["padding_bottom"] = "nxs-padding-bottom0";
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_1 = $compiled[0]["id"];
	//
	
	$styles = array();
	$styles["colorzen"] = "nxs-colorzen-c12-dm";
	$styles["button"] = "";
	$styles["button_scale"] = $button_scale_cssclass;
	
	$compiled[1] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_2 = $compiled[1]["id"];
	//
	
	
	
	//
	
	$result = '';
	$result .= '<p class="' . $compiled[0]["id"] . '">';
	$result .= '<button target="' . $destination_target . '" ' . $destination_relation_html . ' ' . $onclick . ' class="' . $unique_style_combination_class_2 . ' ' . $button_fontzen_cssclass . '" href="' . $url . '">' . $button_text . '</button>';
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