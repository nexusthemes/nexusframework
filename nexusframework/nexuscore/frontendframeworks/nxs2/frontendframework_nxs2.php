<?php

function nxs_frontendframework_nxs2_renderplaceholderwarning($message)
{
	// nothing to do here; all warning are absorbed in the nxs2 framework
}

function nxs_frontendframework_nxs2_footer()
{
	global $nxs_gl_style_footer_cssrules;
	if (count($nxs_gl_style_footer_cssrules) > 0)
	{
		echo "<style>";
		foreach ($nxs_gl_style_footer_cssrules as $id => $rulesbypseudo)
		{
			foreach ($rulesbypseudo as $pseudoid => $rules)
			{
				if($pseudoid == "none")
				{
					echo ".{$id}{" . implode($rules) . "}";
				}
				else if ($pseudoid == "hover")
				{
					echo ".{$id}:hover{" . implode($rules) . "}";
				}
				else
				{
					//
					echo "unsupported pseudoid; $pseudoid";
					die();
				}
			}
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

function nxs_getdarkerhexcolor($hex, $delta)
{
	$result = nxs_getlighterhexcolor($hex, -$delta);
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
	$rulesbypseudo = array();
	
	foreach ($styles as $key => $val)
	{
		// derive pseudoselector
		
		// colorzen or hover:colorzen
		$pseudoselector = "none";
		if (nxs_stringcontains($key, ":"))
		{
			$keypieces = explode(":", $key);
			$key = $keypieces[0];
			$pseudoselector = $keypieces[1];
		}
		
		$val = trim($val);		
		
		if ($key == "align")
		{
			$val = str_replace("nxs-align-", "", $val);
			if ($val == "")
			{
				$rulesbypseudo[$pseudoselector][] = "text-align: center;";
			}
			else if ($val == "left")
			{
				$rulesbypseudo[$pseudoselector][] = "text-align: left;";
			}
			else if ($val == "center")
			{
				$rulesbypseudo[$pseudoselector][] = "text-align: center;";
			}
			else if ($val == "right")
			{
				$rulesbypseudo[$pseudoselector][] = "text-align: right;";
			}
			else
			{
				// unknown?
				$rulesbypseudo[$pseudoselector][] = "text-align: unsupported_{$val};";
			}
		}
		else if ($key == "cursor")
		{
			if ($val == "")
			{
				// default
			}
			else if ($val == "default")
			{
				$rulesbypseudo[$pseudoselector][] = "cursor: default !important;";
			}
			else if ($val == "pointer")
			{
				$rulesbypseudo[$pseudoselector][] = "cursor: pointer !important;";
			}
			else
			{
				// unknown?
				$rulesbypseudo[$pseudoselector][] = "cursor: unsupported_{$val};";
			}
		}
		else if ($key == "white_space")
		{
			if ($val == "")
			{
				// default
			}
			else if ($val == "nowrap")
			{
				$rulesbypseudo[$pseudoselector][] = "white-space: nowrap !important;";
			}
			else if ($val == "pointer")
			{
				$rulesbypseudo[$pseudoselector][] = "cursor: pointer !important;";
			}
			else
			{
				// unknown?
				$rulesbypseudo[$pseudoselector][] = "cursor: unsupported_{$val};";
			}
		}
		else if ($key == "image_shadow")
		{
			if ($val != "")
			{
				$rulesbypseudo[$pseudoselector][] = "box-shadow: 0 2px 6px rgba(10, 10, 10, 0.6);";
			}
		}
		else if ($key == "image_size")
		{
			if ($val == "stretch" )
			{
				$rulesbypseudo[$pseudoselector][] = "width: 100% !important; height: auto !important;";
			}
			else if ($val == "auto-fit")
			{
				$rulesbypseudo[$pseudoselector][] = "max-width: 100% !important; height: auto !important;";
			}
		}
		else if ($key == "display")
		{
			if ($val == "block")
			{
				$rulesbypseudo[$pseudoselector][] = "display: {$val};";
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
				
				$rulesbypseudo[$pseudoselector][] = "max-height: {$value}px !important;";
			}
			else
			{
				// unknown?
				$rulesbypseudo[$pseudoselector][] = "max-height: unsupported_{$val};";
			}
		}
		else if ($key == "width")
		{
			if ($val == "")
			{
				// default
			}
			else if ($val == "inherit")
			{
				$rulesbypseudo[$pseudoselector][] = "width: inherit !important;";
			}
			else
			{
				// unknown?
				$rulesbypseudo[$pseudoselector][] = "{$key}: unsupported_{$val};";
			}
		}
		else if ($key == "height")
		{
			if ($val == "")
			{
				// default
			}
			else if (nxs_stringcontains($val, "-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$value = $whole + ($fraction / 10);
				$factor = 50;	// used by menus; 1.2 = 60px
				$value = $value * $factor;
				
				$rulesbypseudo[$pseudoselector][] = "height: {$value}px !important;";
			}
			else
			{
				// unknown?
				$rulesbypseudo[$pseudoselector][] = "height: unsupported_{$val};";
			}
		}
		else if ($key == "padding_left")
		{
			if ($val == "")
			{
				// default
			}
			else 
			{
				$rulesbypseudo[$pseudoselector][] = "padding-left: {$val}px !important;";
			}
		}
		else if ($key == "padding_right")
		{
			if ($val == "")
			{
				// default
			}
			else 
			{
				$rulesbypseudo[$pseudoselector][] = "padding-right: {$val}px !important;";
			}
		}
		else if ($key == "padding_top")
		{
			$val = str_replace("nxs-padding-top-", "", $val);
			$val = str_replace("nxs-padding-top", "", $val);
			
			if ($val == "")
			{
				// default
			}
			// factor = 30
			else if (nxs_stringcontains($val, "-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$value = $whole + ($fraction / 10);
				$factor = 30;
				$value = $value * $factor;
				$rulesbypseudo[$pseudoselector][] = "padding-top: {$value}px !important;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "padding-top: {$val}px !important;";
			}
		}
		else if ($key == "padding_bottom")
		{
			$val = str_replace("nxs-padding-bottom-", "", $val);
			$val = str_replace("nxs-padding-bottom", "", $val);
			
			if ($val == "")
			{
				// default
			}
			else if (nxs_stringcontains($val, "-"))
			{
				// factor-based
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$base = $whole + ($fraction / 10);
				$factor = 30;
				$value = $base * $factor;
				$rulesbypseudo[$pseudoselector][] = "padding-bottom: {$value}px !important;";
			}
			else 
			{
				// hardcoded
				$rulesbypseudo[$pseudoselector][] = "padding-bottom: {$val}px !important;";
			}
		}
		else if ($key == "padding")
		{
			$val = str_replace("nxs-padding-", "", $val);
			$val = str_replace("nxs-padding", "", $val);
			
			if ($val == "")
			{
				// does not apply
			}
			else if (nxs_stringcontains($val, "-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$base = $whole + ($fraction / 10);
				$factor = 30;
				$value = $base * $factor;
				$rulesbypseudo[$pseudoselector][] = "padding: {$value}px !important;";
			}
			else 
			{
				$rulesbypseudo[$pseudoselector][] = "padding: {$val}px !important;";
			}
		}		
		else if ($key == "margin")
		{
			$val = str_replace("nxs-margin-", "", $val);
			$val = str_replace("nxs-margin", "", $val);
			
			if ($val == "")
			{
				// does not apply
			}
			else if (nxs_stringcontains($val, "-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$base = $whole + ($fraction / 10);
				$factor = 30;
				$value = $base * $factor;
				$rulesbypseudo[$pseudoselector][] = "margin: {$value}px !important;";
			}
			else 
			{
				$rulesbypseudo[$pseudoselector][] = "margin: {$val}px !important;";
			}
		}
		else if ($key == "margin_top")
		{
			$val = str_replace("nxs-margin-top-", "", $val);
			$val = str_replace("nxs-margin-top", "", $val);
			
			if ($val == "")
			{
				// does not apply
			}
			// factor (dynamic)
			else if (nxs_stringcontains($val, "-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$value = $whole + ($fraction / 10);
				$factor = 30;
				$value = $value * $factor;
				
				$rulesbypseudo[$pseudoselector][] = "margin-top: {$value}px !important;";
			}
			else
			{
				// hardcoded?
				$rulesbypseudo[$pseudoselector][] = "margin-top: {$val}px !important;";
			}
		}
		else if ($key == "margin_bottom")
		{
			$val = str_replace("nxs-margin-bottom-", "", $val);
			$val = str_replace("nxs-margin-bottom", "", $val);
			
			if ($val == "")
			{
				// does not apply
			}
			// factor (dynamic)
			else if (nxs_stringcontains($val, "-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$value = $whole + ($fraction / 10);
				$factor = 30;
				$value = $value * $factor;
				
				$rulesbypseudo[$pseudoselector][] = "margin-bottom: {$value}px !important;";
			}
			// hardcoded
			else 
			{
				$rulesbypseudo[$pseudoselector][] = "margin-bottom: {$val}px !important;";
			}
		}
		else if ($key == "colorzen")
		{ 
			// nxs-colorzen nxs-colorzen-c12-dm => // nxs-colorzen-c12-dm
			
			//$rulesbypseudo[$pseudoselector][] = "border-style: solid;";
			
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
					}
					
					if ($coloridentification == "base2")
					{
						// overruled; always 100% black
						$middle = "#000000";
					}
					else if ($coloridentification == "base1")
					{
						// overruled; always 100% white
						$middle = "#FFFFFF";
					}
				}
				
				$transformation = $parts[1];
		
				$delta = 0.2;
		
				if ($transformation == "dm")
				{
					// dark to middle
					$hex_from = nxs_getdarkerhexcolor($middle, $delta);
					$hex_to = $middle;
					$rulesbypseudo[$pseudoselector][] = nxs_getlineairgradientcss($hex_from, $hex_to);
				}
				else if ($transformation == "ml")
				{
					// middle to light
					$hex_from = $middle;
					$hex_to = nxs_getlighterhexcolor($middle, $delta);
					$rulesbypseudo[$pseudoselector][] = nxs_getlineairgradientcss($hex_from, $hex_to);
				}
				else if ($transformation == "")
				{
					// flat
					$hex = $middle;
					$rulesbypseudo[$pseudoselector][] = nxs_getflatbackgroundcolorcss($hex);
				}
				else if ($transformation[0] == "a")
				{
					// background is alpha (flat)
					$hex = $middle;
					$alpha = substr($transformation, 1);
					
					$rulesbypseudo[$pseudoselector][] = nxs_getflatalphabackgroundcolorcss($hex, $alpha);
				}
				else
				{
					$rulesbypseudo[$pseudoselector][] = "unsupportedtransformation;";
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
				
				$rulesbypseudo[$pseudoselector][] = "color: $textcolor;";
				$rulesbypseudo[$pseudoselector][] = "text-shadow: 1px 1px 1px $textshadowcolor;";
				
				if ($_REQUEST["bc"] == "true")
				{
					$delta = 0.2;
					$borderhex = nxs_getdarkerhexcolor($middle, $delta);
					$rulesbypseudo[$pseudoselector][] = "border-color: $borderhex;";
				}
			}
		}
		else if ($key == "button")
		{
			$rulesbypseudo[$pseudoselector][] = "border-width: 1px;";
    	$rulesbypseudo[$pseudoselector][] = "border-radius: 3px;";
    	$rulesbypseudo[$pseudoselector][] = "cursor: pointer;";
		}
		else if ($key == "scale")
		{
			if ($val == "")
			{
				// default
			}
			else if ($val == "nxs-button-scale-1-8")
			{
				$rulesbypseudo[$pseudoselector][] = "font-size: 22px;";
				$rulesbypseudo[$pseudoselector][] = "padding-left: 18px;";
    		$rulesbypseudo[$pseudoselector][] = "padding-right: 18px;";
    		$rulesbypseudo[$pseudoselector][] = "padding-top: 11px;";
    		$rulesbypseudo[$pseudoselector][] = "padding-bottom: 11px;";
			}
			else if ($val == "nxs-button-scale-2-0")
			{
				$rulesbypseudo[$pseudoselector][] = "font-size: 24px;";
				$rulesbypseudo[$pseudoselector][] = "padding-left: 20px;";
    		$rulesbypseudo[$pseudoselector][] = "padding-right: 20px;";
    		$rulesbypseudo[$pseudoselector][] = "padding-top: 12px;";
    		$rulesbypseudo[$pseudoselector][] = "padding-bottom: 12px;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "unsupported_scale_{$key}:{$val}";
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
				
				$rulesbypseudo[$pseudoselector][] = "font-size: {$value}px !important;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "font-size: unsupported__{$val};";
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
				$rulesbypseudo[$pseudoselector][] = "font-family: {$sanitizedfontfamily};";
			}
		}
		else if ($key == "border_radius")
		{
			$val = str_replace("nxs-border-radius-", "", $val);
			$val = str_replace("nxs-border-radius", "", $val);
			
			if ($val == "")
			{
				// 
			}
			else if (nxs_stringcontains($val, "-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$base = $whole + ($fraction / 10);
				$factor = 3;
				$value = $base * $factor;
				$rulesbypseudo[$pseudoselector][] = "border-radius: {$value}px;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "unsupported_border_radius_{$key}:{$val};";
			}
		}
		else if ($key == "box_sizing")
		{
			if ($val == "border-box")
			{
				$rulesbypseudo[$pseudoselector][] = "box-sizing: {$val};";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "unsupported_{$key}:{$val};";
			}
		}
		else if ($key == "border_style")
		{
			if ($val == "solid")
			{
				$rulesbypseudo[$pseudoselector][] = "border-style: {$val};";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "unsupported_{$key}:{$val};";
			} 
		}
		else if ($key == "border_width")
		{
			$val = str_replace("nxs-border-width-", "", $val);
			
			if ($val == "")
			{
				// 
			}
			else if (nxs_stringcontains($val, "-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$base = $whole + ($fraction / 10);
				$factor = 1;
				$value = $base * $factor;
				$rulesbypseudo[$pseudoselector][] = "border-width: {$value}px;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "unsupported__{$key}__{$val};";
			}
		}
		else if ($key == "border_top_width")
		{
			$val = str_replace("nxs-border-top-width-", "", $val);
			
			if ($val == "")
			{
				// 
			}
			else if (nxs_stringcontains($val, "-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$base = $whole + ($fraction / 10);
				$factor = 1;
				$value = $base * $factor;
				$rulesbypseudo[$pseudoselector][] = "border-top-width: {$value}px;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "unsupported__{$key}__{$val};";
			}
		}
		else if ($key == "border_bottom_width")
		{
			$val = str_replace("nxs-border-bottom-width-", "", $val);
			
			if ($val == "")
			{
				// 
			}
			else if (nxs_stringcontains($val, "-"))
			{
				$pieces = explode("-", $val);
				$whole = $pieces[0];
				$fraction = $pieces[1];
				$base = $whole + ($fraction / 10);
				$factor = 1;
				$value = $base * $factor;
				$rulesbypseudo[$pseudoselector][] = "border-bottom-width: {$value}px;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "unsupported__{$key}__{$val};";
			}
		}
		else if ($key == "flex_direction")
		{
			if ($val == "")
			{
				// use default
			}
			else if ($val == "row")
			{
				$rulesbypseudo[$pseudoselector][] = "flex-direction: row;";
			}
			else if ($val == "column")
			{
				$rulesbypseudo[$pseudoselector][] = "flex-direction: column;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "unsupported:{$key}__{$val};";
			}
		}
		else if ($key == "display")
		{
			if ($val == "")
			{
				// use default
			}
			else if ($val == "flex")
			{
				$rulesbypseudo[$pseudoselector][] = "display: flex !important;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "unsupported:{$key}__{$val};";
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
				$rulesbypseudo[$pseudoselector][] = "display: flex !important;";
				$rulesbypseudo[$pseudoselector][] = "justify-content: flex-start;";
			}
			else if ($val == "center")
			{
				$rulesbypseudo[$pseudoselector][] = "display: flex !important;";
				$rulesbypseudo[$pseudoselector][] = "justify-content: center;";
			}
			else if ($val == "end")
			{
				$rulesbypseudo[$pseudoselector][] = "display: flex !important;";
				$rulesbypseudo[$pseudoselector][] = "justify-content: flex-end;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "unsupported:{$key}__{$val};";
			}
		}
		else if ($key == "align_items")
		{
			if ($val == "")
			{
				// use default
			}
			else if ($val == "start")
			{
				$rulesbypseudo[$pseudoselector][] = "align-items: flex-start;";
			}
			else if ($val == "end")
			{
				$rulesbypseudo[$pseudoselector][] = "align-items: flex-end;";
			}
			else if ($val == "center")
			{
				$rulesbypseudo[$pseudoselector][] = "align-items: center;";
			}
			else if ($val == "baseline")
			{
				$rulesbypseudo[$pseudoselector][] = "align-items: baseline;";
			}
			else if ($val == "stretch")
			{
				$rulesbypseudo[$pseudoselector][] = "align-items: stretch;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "unsupported:{$key}__{$val};";
			}
		}
		else if ($key == "texttype")
		{
			if ($val == "quote")
			{
				$rulesbypseudo[$pseudoselector][] = "font-style: italic;";
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
				
				$rulesbypseudo[$pseudoselector][] = "line-height: {$value}em !important;";
			}
			else
			{
				$rulesbypseudo[$pseudoselector][] = "line-height: unsupported_$val;";
			}
		}
		else
		{
			$rulesbypseudo[$pseudoselector][] = "unsupported_KEY_{$key}:{$val};";
		}
	}
	
	// remove duplicates
	if (true)
	{
		foreach ($rulesbypseudo as $pseudoselector => $rules)
		{
			$rules = array_unique($rules);
			$rulesbypseudo[$pseudoselector] = $rules;
			$cnt += count($rules);
		}
	}

	// return if no rules are set
	if (true)
	{
		$cnt = 0;
		foreach ($rulesbypseudo as $pseudoselector => $rules)
		{
			$cnt += count($rules);
		}	
		if ($cnt == 0)
		{
			// no impact, no rules nor id :)
			return;
		}
	}
	
	// create hash
	if (true)
	{
		$hashsource = "";
		foreach ($rulesbypseudo as $pseudoselector => $rules)
		{
			$hashsource .= $pseudoselector;
			foreach ($rules as $rule)
			{
				$hashsource .= $rule;
			}
		}
		$md5 = md5($hashsource);
	}
	
	global $nxs_gl_style_hashtoid;
	if ($nxs_gl_style_hashtoid[$md5] != "")
	{
		// we already have this one, dont make a new one, but return this one instead
		$result["id"] = $nxs_gl_style_hashtoid[$md5];
	}
	else
	{
		// its a new one, create a new id, store it
	
		global $nxs_gl_style_id;
		$nxs_gl_style_id++;
		$id = "nxs-s-" . $nxs_gl_style_id;
		$nxs_gl_style_hashtoid[$md5] = $id;
		$result["id"] = $id;
		
		// ensure we hook to the footer to inject the derived styles
		global $nxs_gl_style_footer_cssrules;
		if (!isset($nxs_gl_style_footer_cssrules))
		{
			// enqueue the styles in the footer (1x only)
			add_action("wp_footer", "nxs_frontendframework_nxs2_footer", 1);
		}
		
		$nxs_gl_style_footer_cssrules[$id] = $rulesbypseudo;
	}

	// the only thing returned is the unique id of the compiled result
	return $result;
}

function nxs_frontendframework_nxs2_gethtmlforbutton($args)
{
	extract($args);
	
	if ($visible == "false")
	{
		return "";
	}
	
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
	$styles["border_radius"] = $border_radius;
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
	$result .= '<button ' . $destination_relation_html . ' ' . $onclick . ' class="' . $unique_style_combination_class_2 . ' ' . $fontzen_cssclass . '">' . $text . '</button>';
	$result .= '</p>';
	
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
	if ($headingelement == "hspan")
	{
		$headingelement = "span";
	}
	
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
	
	$anchorattribute = "";
	if ($anchor != "")
	{
		$anchorattribute = 'id="' . $anchor . '"';
		$anchorattribute = strtolower($anchorattribute);
		$anchorattribute = str_replace(" ", "_", $anchorattribute);
	}
	
	$result = '<' . $headingelement . ' ' . $itemprop . ' ' . $anchorattribute . ' ' . 'class="'.$unique_style_combination_class_0.'">' . $title . '</' . $headingelement . '>';
	
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

function nxs_frontendframework_nxs2_optimizecontent($embedcontent)
{
	// optimize output
	$embedcontent = str_replace("\t", " ", $embedcontent);
	$embedcontent = str_replace("\r\n", "\n", $embedcontent);
	$optimizedembedcontent = "";
	$rows = explode("\n", $embedcontent);
	foreach ($rows as $row)
	{
		$canbeoptimized = true;
		if (nxs_stringcontains("/*", $row))
		{
			$canbeoptimized = false;
		}
		else if (nxs_stringcontains("*/", $row))
		{
			$canbeoptimized = false;
		}
		
		if ($canbeoptimized)
		{
			$rowpieces = explode("//", $row);
			$row = $rowpieces[0] . " ";
		}
		else
		{
		}
		
		// multi space to single space
		//$row = preg_replace("/ {2,}/", " ", $row);
		$optimizedembedcontent .= $row;
	}
	
	return $optimizedembedcontent;
}

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
			$embedcontent = file_get_contents($embed);
			echo nxs_frontendframework_nxs2_optimizecontent($embedcontent);
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
		NXS_FRAMEWORKPATH . "/nexuscore/frontendframeworks/nxs2/css/nxs2.css",
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
			//echo nxs_frontendframework_nxs2_optimizecontent($contents);
			
			if (true) // $_REQUEST["cssoptimize"] == "true")
			{
				// replace multiple spaces (and new lines)
				$contents = preg_replace('/\s+/', ' ',$contents);
				// removed tabs and trims
				$contents = preg_replace('/\t+/', '', $contents);
				// trims
				$contents = trim(preg_replace('/\t+/', '', $contents));
			}
			
			echo $contents;
			?>
		</style>
		<?php
	}
	
	?>
	<script data-cfasync="false"  src="//www.google.com/jsapi"></script>
	<script data-cfasync="false" >
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
			wp_deregister_script('wp-embed');
		}
		add_action('wp_print_scripts', 'nxs_modify_scripts', 100);
		add_action('wp_footer','nxs_frontendframework_nxs2_wp_footer', 10);
  }
  else
  {
  	//add_action('admin_head','nxs_setjQ_nxs');
  }
}

function nxs2_f_optimize_getoptimizedbuffer($result)
{
	if (true) // $_REQUEST["testoptnxs2"] == "true")
	{
		// do something here
		$result = str_replace("<style type=\"text/css\">", "<style>", $result);
		$result = str_replace("<script type=\"text/javascript\">", "<script>", $result);

		// some comments we want to keep; flip flow those
		$flip = "<!-- Nexus";
		$flop = "[[[[ Nexus";
		$result = str_replace($flip, $flop, $result);
		
		$result = preg_replace('/<!--(.|\s)*?-->/', '', $result);

		// reverse the flip flopping of the comments we want to keep
		$result = str_replace($flop, $flip, $result);
		
		// remove comments inside CSS and in JS /**/
		$result = preg_replace('!/\*.*?\*/!s', '', $result);
		$result = preg_replace('/\n\s*\n/', "\n", $result);
		
		// replace multiple spaces (and new lines)
		$result = preg_replace('/\s+/', ' ',$result);
		
		// replace empty spaces between tags
		$result = str_replace("> <", "><", $result);
		
	}
	
	//
	return $result;
}




function nxs_frontendframework_nxs2_init()
{
	disable_wp_emojicons();
	nxs_clearunwantedscripts();
	
	add_filter("nxs_f_optimize_getoptimizedbuffer", "nxs2_f_optimize_getoptimizedbuffer", 10, 1);
	
	add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1);
	add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1);
	add_filter('page_css_class', 'my_css_attributes_filter', 100, 1);
	function my_css_attributes_filter($var) 
	{
		if ($_REQUEST["d"] == "d")
		{
			var_dump($var);
			die();
		}
		
	  return is_array($var) ? array() : '';
	}
}

function nxs_framework_theme_styles()
{
	wp_dequeue_style('farbtastic');
	
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
	$styles["colorzen"] =  $mixedattributes["r_colorzen"];
	$styles["margin_top"] =  $mixedattributes["r_margin_top"];
	$styles["padding_top"] =  $mixedattributes["r_padding_top"];
	$styles["margin_bottom"] =  $mixedattributes["r_margin_bottom"];
	$styles["padding_bottom"] =  $mixedattributes["r_padding_bottom"];
	$styles["border_top_width"] =  $mixedattributes["r_border_top_width"];
	$styles["border_bottom_width"] =  $mixedattributes["r_border_bottom_width"];
	$styles["border_radius"] =  $mixedattributes["r_border_radius"];
	
	
	
	
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
add_shortcode("nxspagerow", "nxs_sc_nxspagerow");

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
		
		$concatenated_css = nxs_concatenateargswithspaces($widthclass, $bottommarginclass, $ph_cssclass, $ph_text_fontsize, $ph_unistyle_cssclass, /* $ph_unistyleindicator_cssclass, */ $ph_unicontent_cssclass, /* $ph_unicontentindicator_cssclass, */ $ph_widgettype_cssclass, $ph_runtimecssclass);
		
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
		
		//
		$styles = array();

		$styles["colorzen"] = $ph_colorzen;
		$styles["border_radius"] = $ph_border_radius;
		$styles["border_width"] = $ph_borderwidth;

		$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
		$unique_style_combination_class_0 = $compiled[0]["id"];
		
		$output .= '<div class="ABC ' . $unique_style_combination_class_0 . ' ' . $concatenated_css . '">';
		
		//
		$styles = array();
		$styles["padding"] = $ph_padding;
		$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
		$unique_style_combination_class_0 = $compiled[0]["id"];
		

		$concatenated_css = nxs_concatenateargswithspaces($ph_padding, $ph_valign);
		$output .= '<div class="XYZ ' . $unique_style_combination_class_0 . ' ' . $concatenated_css . '">';
		
		$output .= "<div class='nxs-placeholder-content-wrap " . $cropwidgetclass . "'>";
		$output .= $content;
		$output .= "</div>";
		
		$output .= "</div>";
		$output .= "</div>";
		
		$output .= "</li>";
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
	$styles["margin_bottom"] = $margin_bottom;
	
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_0 = $compiled[0]["id"];

	
	$image_border = '';
	
	// handle image shadow
	$styles = array();
	$styles["image_shadow"] = $image_shadow;
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_0 = $compiled[0]["id"];
	
	$image_border .= "<div class='{$unique_style_combination_class_0}'>";
	
	$styles = array();
	
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_0 = $compiled[0]["id"];
	
	// $image_border .= '<div style="right: 0; left: 0; top: 0; bottom: 0; border-style: solid;" class="nxs-overflow ' . $image_border_width . ' ' . $unique_style_combination_class_0 . '">';
	// note the display: block is essential/required! else the containing div
	// will have two additional pixels; kudos to http://stackoverflow.com/questions/8828215/css-a-2-pixel-line-appears-below-image-img-element
	
	$styles = array();
	$styles["maxheight"] = $image_maxheight_cssclass;
	$styles["margin_bottom"] = $margin_bottom;
	$styles["image_size"] = $image_size;
	$styles["border_width"] = $image_border_width;
	$styles["border_style"] = "solid";
	$styles["box_sizing"] = "border-box";
	$styles["display"] = "block";
	
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	$unique_style_combination_class_0 = $compiled[0]["id"];
	
	$class = $args["class"];
	if ($class != "") { $class = " {$class}"; }

	$image_border .= '<img class="' . $compiled[0]["id"] . $class . '" ';
	
	if ($loadbehaviour == "lazyload")
	{
		// kudos to https://stackoverflow.com/questions/9126105/blank-image-encoded-as-data-uri
		// smallest transparent image is;
		// "data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=="
		$image_border .= 'src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" ';
		$image_border .= 'data-original="' . $imageurl . '" '; 
	}
	else
	{
		$image_border .= 'src="' . $imageurl . '" ';
	}
	
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
	
	//$image_border .= '</div>';
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
	
	if ($loadbehaviour == "lazyload")
	{
		global $nxs_gl_lazylibloaded;
		if  (!isset($nxs_gl_lazylibloaded))
		{
			$nxs_gl_lazylibloaded = "true";
			add_action("wp_footer", "nxs_frontendframework_nxs2_injectlazylib", 9999);
		}
	}
	
	return $result;	
}

function nxs_frontendframework_nxs2_injectlazylib()
{
	// https://github.com/verlok/lazyload
	?>
	<script>
		window.lazyLoadOptions = 
		{
		    /* your lazyload options */
		};
	</script>

	<?php
	/*
	<!-- Download the script and execute it after lazyLoadOptions is defined -->
	<script async src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-lazyload/9.0.0/lazyload.min.js"></script>
	*/
	?><!-- lazyload --><script>var _extends=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e},_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e};!function(e,t){"object"===("undefined"==typeof exports?"undefined":_typeof(exports))&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):e.LazyLoad=t()}(this,function(){"use strict";var e={elements_selector:"img",container:document,threshold:300,data_src:"original",data_srcset:"originalSet",class_loading:"loading",class_loaded:"loaded",class_error:"error",callback_load:null,callback_error:null,callback_set:null},t=function(e){return e.filter(function(e){return!e.dataset.wasProcessed})},n=function(e,t){var n=new e(t),r=new CustomEvent("LazyLoad::Initialized",{detail:{instance:n}});window.dispatchEvent(r)},r=function(e,t){var n=t.dataSrcSet,r=e.parentElement;if("PICTURE"===r.tagName)for(var s=0;s<r.children.length;s++){var o=r.children[s];if("SOURCE"===o.tagName){var a=o.dataset[n];a&&o.setAttribute("srcset",a)}}},s=function(e,t){var n=t.data_src,s=t.data_srcset,o=e.tagName,a=e.dataset[n];if("IMG"===o){r(e,t);var i=e.dataset[s];return i&&e.setAttribute("srcset",i),void(a&&e.setAttribute("src",a))}"IFRAME"!==o?a&&(e.style.backgroundImage='url("'+a+'")'):a&&e.setAttribute("src",a)},o=function(e,t){e&&e(t)},a=function(e,t,n){e.removeEventListener("load",t),e.removeEventListener("error",n)},i=function(e,t){var n=function n(s){l(s,!0,t),a(e,n,r)},r=function r(s){l(s,!1,t),a(e,n,r)};e.addEventListener("load",n),e.addEventListener("error",r)},l=function(e,t,n){var r=e.target;r.classList.remove(n.class_loading),r.classList.add(t?n.class_loaded:n.class_error),o(t?n.callback_load:n.callback_error,r)},c=function(e,t){["IMG","IFRAME"].indexOf(e.tagName)>-1&&(i(e,t),e.classList.add(t.class_loading)),s(e,t),e.dataset.wasProcessed=!0,o(t.callback_set,e)},d=function(t){this._settings=_extends({},e,t),this._setObserver(),this.update()};d.prototype={_setObserver:function(){var e=this;if("IntersectionObserver"in window){var n=this._settings;this._observer=new IntersectionObserver(function(r){r.forEach(function(t){if(t.isIntersecting){var r=t.target;c(r,n),e._observer.unobserve(r)}}),e._elements=t(e._elements)},{root:n.container===document?null:n.container,rootMargin:n.threshold+"px"})}},update:function(){var e=this,n=this._settings,r=n.container.querySelectorAll(n.elements_selector);this._elements=t(Array.prototype.slice.call(r)),this._observer?this._elements.forEach(function(t){e._observer.observe(t)}):(this._elements.forEach(function(e){c(e,n)}),this._elements=t(this._elements))},destroy:function(){var e=this;this._observer&&(t(this._elements).forEach(function(t){e._observer.unobserve(t)}),this._observer=null),this._elements=null,this._settings=null}};var u=window.lazyLoadOptions;return u&&function(e,t){var r=t.length;if(r)for(var s=0;s<r;s++)n(e,t[s]);else n(e,t)}(d,u),d});</script>	
	<?php
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
	
	if ($class != "")
	{
		$extraclass = " " . $class;
	}
	else
	{
		$extraclass = "";
	}
	
	
	$result = "<div class='{$unique_style_combination_class_0} {$extraclass}'>{$unwrapped_content}</div>";
	
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
	if ($align == "") { $align = "left"; }
	
	$alignment_cssclass = nxs_getcssclassesforlookup("nxs-align-", $align);
	$fontzen_cssclass = nxs_getcssclassesforlookup("nxs-fontzen-", $fontzen);
	$class = $atts["class"];
	
	$cssclasses = nxs_concatenateargswithspaces($class, "nxs-default-p", /* "nxs-applylinkvarcolor", */ "nxs-padding-bottom0", $alignment_cssclass, $showliftnote_cssclass, $showdropcap_cssclass, $fontzen_cssclass);
	
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
	
	return $result;
}

// injecting of styles of menus
function nxs2_nav_menu_link_attributes($result, $item, $args, $depth)
{
	$isactive = in_array('current-menu-item', $item->classes);
	// todo: if not active, re-check if current url is url of item, then also consider isactive?

	global $nxs_gl_currentmenuwidget_mixedattributes;
	
	$menuitem_color = $nxs_gl_currentmenuwidget_mixedattributes["menuitem_color"];
	$menuitem_active_color = $nxs_gl_currentmenuwidget_mixedattributes["menuitem_active_color"];
	$menuitem_hover_color = $nxs_gl_currentmenuwidget_mixedattributes["menuitem_hover_color"];
	
	$menuitem_sub_color = $nxs_gl_currentmenuwidget_mixedattributes["menuitem_sub_color"];
	$menuitem_sub_active_color = $nxs_gl_currentmenuwidget_mixedattributes["menuitem_sub_active_color"];
	$menuitem_sub_hover_color = $nxs_gl_currentmenuwidget_mixedattributes["menuitem_sub_hover_color"];
	
	$menuitem_height = $nxs_gl_currentmenuwidget_mixedattributes["parent_height"];
	$menuitem_height = str_replace(".", "-", $menuitem_height);
	$menuitem_height = str_replace("x", "", $menuitem_height);
	if ($menuitem_height == "1") { $menuitem_height = "1-0"; }
	if ($menuitem_height == "") { $menuitem_height = "1-0"; } 
	
	
	
	$styles = array();
	$styles["content_justify"] = "center";
	$styles["align_items"] = "center";
	$styles["height"] = $menuitem_height;
	$styles["fontzen"] = "1";
	$styles["padding_left"] = "20";
	$styles["padding_right"] = "20";
	
	if ($isactive)
	{
		$styles["cursor"] = "default";	// it should not look clickable although it is...		
	}
	
	if ($depth == 0)
	{
		if ($isactive)
		{
			$styles["colorzen"] = $menuitem_active_color;
		}
		else
		{
			$styles["colorzen"] = $menuitem_color;
		}
		
		$styles["colorzen:hover"] = $menuitem_hover_color;
		
		$menu_fontsize = $nxs_gl_currentmenuwidget_mixedattributes["menu_fontsize"];
		$menu_fontsize = str_replace(".", "-", $menu_fontsize);
		$menu_fontsize = str_replace("x", "", $menu_fontsize);
		if ($menu_fontsize == "1") { $menu_fontsize = "1-0"; }
		if ($menu_fontsize == "") { $menu_fontsize = "1-0"; } 
		$styles["fontsize"] = $menu_fontsize;
		
	}
	else if ($depth > 0)
	{
		if ($isactive)
		{
			$styles["colorzen"] = $menuitem_sub_active_color;
		}
		else
		{
			$styles["colorzen"] = $menuitem_sub_color;
		}
		
		$styles["colorzen:hover"] = $menuitem_sub_hover_color;
		
		// 
		$menu_fontsize = $nxs_gl_currentmenuwidget_mixedattributes["submenu_fontsize"];
		$menu_fontsize = str_replace(".", "-", $menu_fontsize);
		$menu_fontsize = str_replace("x", "", $menu_fontsize);
		if ($menu_fontsize == "1") { $menu_fontsize = "1-0"; }
		if ($menu_fontsize == "") { $menu_fontsize = "1-0"; } 
		$styles["fontsize"] = $menu_fontsize;
	}
	
	$compiled[0] = nxs_frontendframework_nxs2_compilestyle($styles);
	if ($result['class'] != "")
	{
		$result['class'].=" ";
	}
	$result['class'].= $compiled[0]["id"] . " " . "nxs-depth-$depth";
	
	return $result;
}
add_filter( 'nav_menu_link_attributes', 'nxs2_nav_menu_link_attributes', 10, 4);

// requires wp 4.8 ...
function nxs2_nav_menu_submenu_css_class( $result ) 
{
	$styles = array();
	$styles["display"] = "flex";
	$styles["flex_direction"] = "column";
	$styles["white_space"] = "nowrap";
	$styles["width"] = "inherit";
	$compiled = nxs_frontendframework_nxs2_compilestyle($styles);
  $result[] = $compiled["id"];
	
  return $result;
}
add_filter( 'nav_menu_submenu_css_class', 'nxs2_nav_menu_submenu_css_class' );

function nxs2_body_classes($result)
{
	$styles = array();
	$styles["fontzen"] = "1";
	$compiled = nxs_frontendframework_nxs2_compilestyle($styles);
  $result[] = $compiled["id"];
	
  return $result;
}
add_filter('body_class', 'nxs2_body_classes');