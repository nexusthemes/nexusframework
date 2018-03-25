<?php 

function nxs_sc_reconstructshortcode_keyvalue($v, $k) 
{ 
	return sprintf("%s='%s'", $k, $v); 
}

function nxs_sc_reconstructshortcode($atts, $content, $name)
{
	$implodedatts = implode
	(
		' ', 
		array_map
		(
    	"nxs_sc_reconstructshortcode_keyvalue",
    	$atts,
    	array_keys($atts)
		)
	);
	
	$reconstructed = "[{$name} {$implodedatts}]";
	if ($content !== null && $content !== "")
	{
		$reconstructed .= "{$content}[/{$name}]";
	}
	
	return $reconstructed;
}

//
// sometimes we want to process certain shortcodes conditionally
// meaning, it should keep the shortcode as-is if the condition is not met,
// and it shsould transform the shortcode when the condition is met
// to facilitate this we use the following function
// sample; sc_scope="list.iterator.filter"
function nxs_sc_handlescope($atts, $content = null, $name='')
{
	extract($atts);
	
	$result = false;
	
	if (isset($sc_scope))
	{
		global $nxs_gl_sc_currentscope;
		if ($nxs_gl_sc_currentscope[$sc_scope] === true)
		{
			// we are inside the scope that should process/apply the shortcode
		}
		else
		{
			// wrong scope, wont execute
			// NOTE; we don't "just" return the value, we  return the entire shortcode as-is
			// such that it can be re-evaluated by this same shortcode when we -are- executing in
			// the right scope
			$result = nxs_sc_reconstructshortcode($atts, $content, $name);
		}
	}
	
	return $result;
}

// for example [nxsstring ops="lo;x_"]plumber_wordpress_theme[/nxsstring]
function nxs_sc_string($atts, $content = null, $name='') 
{
	extract($atts);
	
	if (isset($sc_scope))
	{
		$scoperesult = nxs_sc_handlescope($atts, $content, $name);
		if ($scoperesult !== false)
		{
			// we are outside the scope, exit
			return $scoperesult;
		}
	}
	
	$origcontent = $content;
	
	$content = $content;
	if ($content == "")
	{
		$content = $atts["input"];
	}
	if ($content == "")
	{
		$content = $atts["value"];
	}
	
	$input = $content;
	
	$ops = $atts["ops"];
	
	$ops = str_replace(",","|", $ops);
	$ops = str_replace(";","|", $ops);
	$opslist = explode("|", $ops);
	foreach ($opslist as $op)
	{
		$op = trim($op);
		if (false)
		{
			//
		}
		else if ($op == "form_items_data")
		{
			$result = array();
			
			global $nxs_g_modelmanager;
			
			// generates form items data based upon input criteria
			$item_schema = $atts["item_schema"];
			$form_type = $atts["form_type"];
			$lang = $atts["lang"];
			$cachebehaviour = $atts["cachebehaviour"];
			
			// grab all form items for the schema and form_type
			$modeluris = do_shortcode("[nxsstring ops='listmodeluris' singularschema='{$item_schema}' where_property_1='form_type' where_operator_1='equals' where_value_1='{$form_type}' cachebehaviour='{$cachebehaviour}']");
			$pieces = explode(";", $modeluris);
			foreach ($pieces as $modeluri)
			{
				$resultitem = array();
				
				$modeluriprops = $nxs_g_modelmanager->getmodeltaxonomyproperties(array("modeluri" => $modeluri));
				$type = $modeluriprops["type"];
				
				$parseinstructions = array();
				
				if ($type == "contactitemtext")
				{
					$parseinstructions["replicatelanguagedependentprops"] = array("formlabel", "placeholder", "initialtext");
					$parseinstructions["replicatelanguageindependentprops"] = array("type", "isrequired", "isreadonly", "ishidden", "numofrows");
				}
				else if ($type == "contactitemreplyto")
				{
					$parseinstructions["replicatelanguagedependentprops"] = array("formlabel", "placeholder", "initialtext");
					$parseinstructions["replicatelanguageindependentprops"] = array("type", "isrequired", "isreadonly", "ishidden");
				}
				else if ($type == "contactitemselect")
				{
					// grab options ("selectables") from related table based upon a naming convention
					if (true)
					{
						$formitem_id = $modeluriprops["{$item_schema}_id"];
						$listitem_modeluris = do_shortcode("[nxsstring ops='listmodeluris' singularschema='{$item_schema}.listitem' where_property_1='{$item_schema}_id' where_operator_1='equals' where_value_1='{$formitem_id}' cachebehaviour='{$cachebehaviour}']");
						$listitem_pieces = explode(";", $listitem_modeluris);
						$listitem_texts = array();
						foreach ($listitem_pieces as $listitem_modeluri)
						{
							$listitem_texts[]= $nxs_g_modelmanager->getmodeltaxonomyproperty(array("modeluri" => $listitem_modeluri, "property" => "text_{$lang}"));
						}
						$selectables = implode("|", $listitem_texts);
					}
					
					$parseinstructions["addkeyvalues"] = array("selectables" => $selectables);
					$parseinstructions["replicatelanguagedependentprops"] = array("formlabel");
					$parseinstructions["replicatelanguageindependentprops"] = array("type", "isrequired", "isreadonly", "ishidden");
				}
				else
				{
					$errmsg = "form_items_data; err; unsupported type; type:$type; modeluri: $modeluri; props:" . json_encode($modeluriprops);
					error_log($errmsg);
					return $errmsg;
				}
				
				foreach ($parseinstructions as $instruction => $instructionmeta)
				{
					if ($instruction == "replicatelanguagedependentprops")
					{
						foreach ($instructionmeta as $property)
						{
							$modelval = $modeluriprops["{$property}_{$lang}"];
							$resultitem[$property] = $modelval;
						}
					}
					else if ($instruction == "replicatelanguageindependentprops")
					{
						foreach ($instructionmeta as $property)
						{
							$modelval = $modeluriprops["{$property}"];
							$resultitem[$property] = $modelval;
						}
					}
					else if ($instruction == "addkeyvalues")
					{
						foreach ($instructionmeta as $k => $v)
						{
							$resultitem[$k] = $v;
						}
					}
					else
					{
						$errmsg = "form_items_data; err; unsupported instruction; $instruction";
						error_log($errmsg);
						return $errmsg;
					}
				}
				
				//
				$result[] = $resultitem;
			}
			
			$input = json_encode($result);
		}
		else if ($op == "lo")
		{
			$input = strtolower($input);
		}
		else if ($op == "up")
		{
			$input = strtoupper($input);
		}
		else if ($op == "count")
		{
			$seperator = ";";
			$input = explode($seperator, $input);
			$input = count($input);
		}
		else if ($op == "min")
		{
			$input = str_replace("|", ";", $input);
			$pieces = explode(";", $input);
			$input = PHP_INT_MAX;
			foreach ($pieces as $piece)
			{
				if ($piece < $input)
				{
					$input = $piece;
				}
			}
		}
		else if ($op == "str_replace")
		{
			$search = $atts["search"];
			if ($search != "")
			{
				$replace = $atts["replace"];
				$input = str_replace($search, $replace, $input);
			}
			
			// if the trimmed value is empty
			if (isset($atts["iftrimmedempty_replacement"]))
			{
				if (trim($input) == "")
				{
					$replacement = $atts["iftrimmedempty_replacement"];
					$input = $replacement;
				}	
			}
			///
			if (isset($atts["ifcontainscurlybrackets_replacement"]))
			{
				if (nxs_stringcontains($input, "{") || nxs_stringcontains($input, "}"))
				{
					$replacement = $atts["ifcontainscurlybrackets_replacement"];
					$input = $replacement;
				}
			}
		}
		else if ($op == "esc_attr")
		{
			$input = esc_attr($input);
		}
		else if ($op == "md5")
		{
			$input = md5($input);
		}
		else if ($op == "time")
		{
			$input = time();
		}
		else if ($op == "nxs_getplaceholderwarning")
		{
			$input = nxs_getplaceholderwarning($input);
		}
		else if ($op == "rand")
		{
			$min = 0;
			if (isset($atts["min"]))
			{
				$min = $atts["min"];
			}
			$max = getrandmax();
			if (isset($atts["max"]))
			{
				$max = $atts["max"];
			}
			$input = rand($min, $max);
		}
		else if ($op == "sitemapentry")
		{
			// todo: also support the changefreq and priority
			
			$url = $atts["url"];
			
			$shouldprettyfy = true;
			if (nxs_stringcontains($url, "http://"))
			{
				$shouldprettyfy = false;
			}
			if (nxs_stringcontains($url, "https://"))
			{
				$shouldprettyfy = false;
			}
			if ($shouldprettyfy)
			{
				$url = nxs_url_prettyfy($url);
			}
			$input = "<url><loc>{$url}</loc><changefreq>daily</changefreq><priority>0.8</priority></url>";
			
			if (is_user_logged_in())
			{
				$input = htmlentities($input) . "<br />";
			}
		}
		else if ($op == "randomstring")
		{
			$length = 10;
			if (isset($atts["length"]))
			{
				$length = $atts["length"];
			}
			$input = nxs_generaterandomstring($length);
		}
		else if ($op == "md5stringpicker")
		{
			//error_log("md5stringpicker;" . json_encode($atts));
			
			$options = $atts["options"];
			$pieces = explode("|", $options);
			$max = count($pieces);
			$indexer = $atts["indexer"];
			$md5 = md5($indexer);
			$inthash = intval(substr($md5, 0, 8), 16);
			$index = $inthash % $max;
			$input = $pieces[$index];
		}
		else if ($op == "ucwords")
		{
			$input = ucwords($input);
		}
		else if ($op == "ucfirst" || $op == "ucfirstchar")
		{
			$input = strtoupper(substr($input, 0, 1)) . substr($input, 1);
		}
		// homeurl home_url homepage_url homepageurl gethome get_home site home site_home site_url homepage
		else if ($op == "homeurl")
		{
			$input = nxs_geturl_home();
		}
		else if ($op == "hostname")
		{
			$homeurl = nxs_geturl_home();	// for example https://example.org/
			$pieces = explode("/", $homeurl);
			$input = $pieces[2];
		}
		else if ($op == "mediasource")
		{
			// input = 123rf|27357367
			$pieces = explode("|", $input);
			$provider = $pieces[0];
			$id = $pieces[1];
			if ($provider == "123rf")
			{
				$input = "https://123rf.com/search.php?word={$id}&srch_lang=nl&imgtype=&Submit=+&t_word=&t_lang=nl&orderby=0";
			}
			else if ($provider == "pixabay")
			{
				$input = "https://pixabay.com/en/{$id}/";
			}
			else
			{
				$input = "https://example.org";
			}
		}
		else if ($op == "urlprettyfy" || $op == "urlprettify")
		{
			if ($atts["debug"] == "true")
			{
				return "urlprettyfy;debug;($content);($input);($value)";
			}
			
			$lower = trim(strtolower($input));
			if (nxs_stringstartswith($lower, "http:"))
			{
				// ignore; already handled
			}
			else if (nxs_stringstartswith($lower, "https:"))
			{
				// ignore; already handled
			}
			else if (nxs_stringcontains($input, "{{"))
			{
				// still too early, apparently, evaluate at a later moment in time (ignore the shortcode
				$input = nxs_sc_reconstructshortcode($atts, $origcontent, $name);
				// note; an INSTANT return; don't proceed with any other possible operators
				return $input;
			}
			else
			{
				$homeurl = "";
				if (isset($atts["homeurl"])) { $homeurl = $atts["homeurl"]; }
				$input = nxs_url_prettyfy($input, $homeurl);
			}
		}
		else if ($op == "urlfraction")
		{
			$input = strtolower($input);
			$input = preg_replace('/[^A-Za-z0-9]/', '-', $input); // Replaces any non alpha numeric with -
			for ($cnt = 0; $cnt < 3; $cnt++)
			{
				$input = str_replace("--", "-", $input);
			}
		}
		else if ($op == "youtubeify")
		{
			if (true) // $_REQUEST["yt"] == "yt")
			{
				preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $input, $match);
				if (count($match) >= 1)
				{
					// locate t=XX in the url
					$url = $match[0][0];
					$parts = parse_url($url);
					parse_str($parts['query'], $queryparameters);
					$t = $queryparameters["t"];
					if ($t != "")
		    	{
		    		$start = "?start=$t";
		    	}
		    	else
		    	{
		    		$start = "";
		    	}
		    	//var_dump($input);
		    	//var_dump($parts);
		    	//var_dump($t);
		    	//var_dump($start);
		    }
	    	//die();
	    }
			
			// thanks to https://stackoverflow.com/questions/19050890/find-youtube-link-in-php-string-and-convert-it-into-embed-code
			$input = preg_replace(
        "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
        "<br /><div class=\"video-container\"><iframe class=\"video\" width=\"560\" height=\"315\" src=\"//www.youtube.com/embed/$2{$start}\" allowfullscreen></iframe></div><br />",
        $input
    	);
    	
    	
		}
		else if ($op == "linkify")
		{
			$input = html_entity_decode($input);
			
			if ($atts["excludeyoutube"] == "true")
			{
				$input = str_replace("https://www.youtube/watch", "*NXS*PLACEHOLDER*YOUTUBE*", $input);
			}
			if ($atts["exclude"] != "")
			{
				$exclude = $atts["exclude"];
				$excludeitems = explode(";", $exclude);
				foreach ($excludeitems as $excludeitem)
				{
					$excludeitem = trim($excludeitem);
					if ($excludeitem == "mediamanager")
					{
						$input = str_replace("https://mediamanager", "*NXS*PLACEHOLDER*MEDIA*", $input);
					}
					if ($excludeitem == "nxsmedia")
					{
						$input = str_replace("nxsmedia://", "*NXS*PLACEHOLDER*NXSMEDIA*", $input);
					}
					else if ($excludeitem == "youtube")
					{
						$input = str_replace("https://www.youtube/watch", "*NXS*PLACEHOLDER*YOUTUBE*", $input);
					}
					else if ($excludeitem == "example.org")
					{
						$input = str_replace("http://example.org", "*NXS*PLACEHOLDER*HTTPEXAMPLEORG*", $input);
						$input = str_replace("https://example.org", "*NXS*PLACEHOLDER*HTTPSEXAMPLEORG*", $input);
					}
				}
			}
			
			$input = preg_replace
			(
        "~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~",
        "<a target=\"blank\" class=\"linkified\" href=\"\\0\">\\0</a>", 
        $input
      );

			$input = str_replace("*NXS*PLACEHOLDER*YOUTUBE*", "https://www.youtube/watch", $input);
			$input = str_replace("*NXS*PLACEHOLDER*MEDIA*", "https://mediamanager", $input);
			$input = str_replace("*NXS*PLACEHOLDER*NXSMEDIA*", "nxsmedia://", $input);
			$input = str_replace("*NXS*PLACEHOLDER*HTTPEXAMPLEORG*", "http://example.org", $input);
			$input = str_replace("*NXS*PLACEHOLDER*HTTPSEXAMPLEORG*", "https://example.org", $input);
		}
		else if ($op == "listify")
		{
			$seperator = $atts["seperator"];
			$pieces = explode($seperator, $input);
			$input = "";
			$input .= "<ul>";
			foreach ($pieces as $piece)
			{
				$piece = trim($piece);
				if ($piece == "")
				{
					continue;
				}
				$input .= "<li>{$piece}{$seperator}</li>";
			}
			$input .= "</ul>";
		}
		else if ($op == "smartlinks")
		{
			$dictionary = array
			(
				"woocommerce" => "<a href='https://wordpress.org/plugins/woocommerce/'>WooCommerce</a>",
			);
			//
			foreach ($dictionary as $needle => $replace)
			{
				$pos = stripos($input, $needle);
				if ($pos !== false) 
				{
					$input = substr_replace($input, $replace, $pos, strlen($needle));
				}
			}
		}
		else if ($op == "htmlentities")
		{
			$input = htmlentities($input);
		}
		else if ($op == "html_entity_decode")
		{
			$input = html_entity_decode($input);
		}
		else if ($op == "htmlspecialchars")
		{
			$input = htmlspecialchars($input);
		}
		else if ($op == "xspace")
		{
			$input = str_replace(" ", "", $input);
		}
		else if ($op == "x_")
		{
			$input = str_replace("_", "", $input);
		}
		else if ($op == "trim")
		{
			if (isset($atts["trimchars"]))
			{
				$character_mask = $atts["trimchars"];
				$input = trim($input, $character_mask);
			}
			else
			{
				$input = trim($input);
			}
		}
		else if ($op == "replacemodellookupmismatch")
		{
			// if the value does not have a model lookup value
			// it will still have {{ }} values
			$shouldbereplaced = false;
			if (nxs_stringcontains($input, "{"))
			{
				$shouldbereplaced = true;
			}
			else if (nxs_stringcontains($input, "}"))
			{
				$shouldbereplaced = true;
			}
			if ($shouldbereplaced)
			{
				// if thats true, replace it with whatever is set as the replacement in the shortcode
				$replacement = $atts["modellookupmismatchreplacement"];
				$input = $replacement;
			}
		}
		else if ($op == "getlatlng")
		{
			//error_log("getlatlng for; $input");
			nxs_requirewidget("googlemap");
			$latlng = nxs_widget_googlemap_getlatlng($input); 
			$input = $latlng["lat"] . ";" . $latlng["lng"] . ";" . $latlng["found"];
			//error_log("getlatlng result; $input");
		}
		else if ($op == "replaceempty")
		{
			// if the value is empty
			$shouldbereplaced = false;
			if (trim($input) == "")
			{
				$shouldbereplaced = true;
			}
			if ($shouldbereplaced)
			{
				// if thats true, replace it with whatever is set as the replacement in the shortcode
				$replacement = $atts["emptyreplacement"];
				$input = $replacement;
			}
		}
		else if ($op == "queryparameter" || $op == "usequeryparameter" || $op == "getqueryparameter")
		{
			if (nxs_iswebmethodinvocation())
			{
				$uricurrentpage = $_REQUEST["uricurrentpage"];
				$pieces = explode("?", $uricurrentpage);
				$queryparameters = $pieces[1];
				$pieces = explode("&", $queryparameters);
				foreach ($pieces as $piece)
				{
					$subpieces = explode("=", $piece);
					$key = $subpieces[0];
					$value = $subpieces[1];
					$lookup[$key] = $value;
				}
				// error_log("queryparams;" . json_encode($lookup));
			}
			else
			{
				// normal request
				$lookup = $_REQUEST;
			}
			
			// if thats true, replace it with whatever is set as the replacement in the shortcode
			$queryparameter = $atts["queryparameter"];
			$description = $atts["description"];
			
			// expose to the outside world this queryparameter is used and what it does
			// so code that invokes the page will know what input parameters to use
			$actionargs = array
			(
				"queryparameter" => $queryparameter,
				"description" => $description,
			);
			do_action("nxs_a_usesqueryparameter", $actionargs);
			
			$replacement = $lookup[$queryparameter];
			if (isset($replacement) && $replacement != "")
			{
				$input = $replacement;
			}
			else
			{
				if (isset($atts["fallback"]))
				{
					$fallback = $atts["fallback"];
				}
				else
				{
					$fallback = "";
				}
				
				$input = $fallback;
			}
			
			if ($atts["quotesfix"] == "true")
			{
				$input = str_replace('\"', '"', $input);
			}
		}
		else if ($op == "urldecode")
		{
			// if thats true, replace it with whatever is set as the replacement in the shortcode
			$replacement = urldecode($input);
			$input = $replacement;
		}
		else if ($op == "multiply")
		{
			// if thats true, replace it with whatever is set as the replacement in the shortcode
			$replacement = $input * $atts["multiplyfactor"];
			$input = $replacement;
		}
		else if ($op == "intval")
		{
			if ($atts["strip"] == "allexceptdigits")
			{
				// only keep the digits
				$input = preg_replace('/\D/', '', $input);
				// error_log("stripping; $input");
			}
			
			// if thats true, replace it with whatever is set as the replacement in the shortcode
			$replacement = intval($input);
			$input = $replacement;
		}
		else if ($op == "negate")
		{
			// if thats true, replace it with whatever is set as the replacement in the shortcode
			$replacement = 0-intval($input);
			$input = $replacement;
		}
		else if ($op == "applylookups")
		{
			$metadata = array("value" => $input);
			$multiresponse = nxs_filter_translatelookup($metadata, array("value"));
			$input = $multiresponse["value"];
		}
		else if ($op == "modelproperty")
		{
			if ($atts["errorlog"] == "true")
			{
				error_log("shortcodes errorlog;modeluri:$modeluri;property:$property");
			}
			if ($atts["debug"] == "true")
			{
				return "modelproperty; modeluri:$modeluri;property:$property";
			}
			
			global $nxs_g_modelmanager;
			
			$source = $atts["source"];
			if ($source == "md5indexer")
			{
				// the modeluri is derived based upon the md5 indexer of a specified schema
				$schema = $atts["schema"];
				$property = $atts["property"];
				
				$cachebehaviour = $atts["cachebehaviour"];
				if ($cachebehaviour == "none")
				{
				}
				else if ($cachebehaviour == "refreshfirstphpruntime")
				{
					// refreshes the cache the first time this is requested in the php runtime duration
					global $nxs_g_modelrefreshphpruntime;
					if (!isset($nxs_g_modelrefreshphpruntime[$schema]))
					{
						$nxs_g_modelrefreshphpruntime[$schema] = true;
						$nxs_g_modelmanager->cachebulkmodels($schema);
					}
				}
				else
				{
					return "no, or invalid cachebehaviour (none|refreshfirstphpruntime)";
				}
				
				$a = array("singularschema" => $schema);
				$unfilteredpossibilities = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels($a);

								
				// optionally filter the possibilities
				foreach ($unfilteredpossibilities as $possibility)
				{
					$conditionevaluation = true;
	
					$conditionindexers = array("", "_1", "_2");	// add more conditionindexers here when needed...
					foreach ($conditionindexers as $conditionindexer)
					{
						// operator
						
						$operatorproperty = $atts["where_property{$conditionindexer}"];
						$operator = $atts["where_operator{$conditionindexer}"];
						$operatorvalue = $atts["where_value{$conditionindexer}"];
		
						if ($operator == "")
						{
							// ignore this one
							continue;
						}
						else if ($operator == "caseinsensitivelike")
						{
							$fieldvalue = $possibility[$operatorproperty];
							$conditionevaluation = nxs_stringcontains_v2($fieldvalue, $operatorvalue, true);
						}
						else if ($operator == "equals")
						{
							//echo "<br />found equals operator<br />";
							//echo "<br />operatorproperty is<br />";
							//var_dump($operatorproperty);
							//echo "<br />possibility is<br />";
							//var_dump($possibility);
							
							$fieldvalue = $possibility[$operatorproperty];
							//echo "<br />fieldvalue is<br />";
							//var_dump($fieldvalue);
							
							$conditionevaluation = ($fieldvalue == $operatorvalue);
							//echo "<br />conditionevaluation is<br />";
							//var_dump($conditionevaluation);
						}
						else
						{
							return "$op; unsupported where operator ($operator)";
							// not supported; evaluates to false
						}
						
						//
						if ($conditionevaluation === false)
						{
							// if one condition is false, break all (we use a logical AND operator here)
							break;
						}
						
						// loop; proceed evaluating the next condition
					}
					
					// if condition evaluates to true, add the item to the resulting set
					if ($conditionevaluation)
					{
						$possibilities[] = $possibility;
					}					
				}	

				// grab the indexer
				
				$max = count($possibilities);
				//error_log("modelproperty;md5indexer;max;".$max);

				$indexer = $atts["indexer"];
				//error_log("modelproperty;md5indexer;indexer;".$indexer);
				
				$md5 = md5($indexer);
				$inthash = intval(substr($md5, 0, 8), 16);
				$index = $inthash % $max;
				$modelid = $possibilities[$index]["{$schema}_id"];
				$modeluri = "{$modelid}@{$schema}";
				//error_log("modelproperty;md5indexer;".json_encode($possibilities[$index]));
				//error_log("modelproperty;md5indexer;result;".$input);
			}
			else if ($source == "where")
			{
				$input = "";
				$schema = $atts["schema"];
				$where_property_1 = $atts["where_property_1"];
				$where_operator_1 = $atts["where_operator_1"];
				$where_value_1 = $atts["where_value_1"];
				$cachebehaviour = $atts["cachebehaviour"];
				$instruction = "[nxsstring ops='listmodeluris' singularschema='nxs.nexusthemes.itemmeta' where_property_1='{$where_property_1}' where_operator_1='{$where_operator_1}' where_value_1='{$where_value_1}' cachebehaviour='{$cachebehaviour}']";
				//error_log("modelprop secondarykey; instruction; $instruction");
				
				$modelurisstring = do_shortcode($instruction);
				
				//error_log("modelprop secondarykey; modelurisstring; $modelurisstring");
				$modeluris = explode(";", $modelurisstring);
				$count = count($modeluris);
				if ($count == 1)
				{
					foreach ($modeluris as $modeluri)
					{
						$where_property_1 = $atts["property"];
						$instruction = "[nxsstring ops='modelproperty' modeluri='{$modeluri}' property='{$property}']";
						//error_log("instruction;".$instruction);
						$input = do_shortcode($instruction);
						//error_log("input;".$input);
					}
				}
				else
				{
					if ($atts["fallback"] != "")
					{
						return $atts["fallback"];
					}
					else
					{
						return "ERR; multiple rows match? need only one";
					}
				}
			}
			else 
			{
				if ($atts["errorlog"] == "true")
				{
					error_log("modelproperty; source not set");
				}
				
				$modeluri = $atts["modeluri"];		// the base modeluri for which the property will be retrieved
				if ($modeluri == "")
				{
					global $nxs_global_current_containerpostid_being_rendered;
					$modeluri = "{$nxs_global_current_containerpostid_being_rendered}@wp.post";
				}
			}
			
			$property = $atts["property"];		// the property to be retrieved
			$relations = $atts["relations"];
			
			//
			$ignorewhenlist = array("modeluri" => $modeluri, "property" => $property, "relations" => $relations, "input" => $input);
			
			//$ignorewhenlist = array($input, $modeluri, $property, $relations);
			foreach ($ignorewhenlist as $key => $ignorewhen)
			{
				// special case handling; 
				if (nxs_stringcontains($ignorewhen, "{{"))
				{
					if ($key == "modeluri" && isset($atts["unresolvedmodeluri_fallback"]))
					{
						$input = $atts["unresolvedmodeluri_fallback"];
						return $input;
					}					
					
					// still too early, apparently, evaluate at a later moment in time (ignore the shortcode
					$input = nxs_sc_reconstructshortcode($atts, $origcontent, $name);
					// note; an INSTANT return; don't proceed with any other possible operators
					return $input;
				}
			}
			
			if ($atts["errorlog"] == "true")
			{
				error_log("modelproperty; relations: {$relations}");
			}
			
			if (isset($relations) && $relations != "")
			{
				// update the modeluri to other modeluris based upon the relations specified
				// for example "businesstypeinstance|businesstype_name|businesstype" or 
				// for example "businesstypeinstancealtid@businesstypeinstance|businesstype_name|businesstype" or
				// abstract "{{property}}@{{schema}}"
				$relationpieces = explode(";", $relations);
				foreach ($relationpieces as $relationpiece)
				{
					
					
					$relationpiece = trim($relationpiece);
					if ($relationpiece == "")
					{
						// ignore
						continue;
					}
					
					if (nxs_stringcontains($relationpiece, "@"))
					{
						// format is specified as "relationproperty@relationschema"
						$subpieces = explode("@", $relationpiece, 2);
						$relationproperty = trim($subpieces[0]);
						$relationschema = trim($subpieces[1]);
					}
					else
					{
						// format is specified as "relationschema", the property is derived based on its value
						$relationproperty = "{$relationpiece}_id";
						$relationschema = $relationpiece;
					}
					
					//error_log("relationpiece; fetching property ($relationproperty) for ($modeluri)");
					
					// fetch the value of the property; this will return the humanid of the relation
					$args = array
					(
						"modeluri" => $modeluri,
						"property" => $relationproperty,
					);
					$relationmodelid = $nxs_g_modelmanager->getmodeltaxonomyproperty($args);
					$relationmodelid = trim($relationmodelid);
					
					if ($atts["errorlog"] == "true")
					{
						$url = nxs_geturlcurrentpage();
						error_log("modelproperty; relationwalker; $url; modeluri:$modeluri prop:$relationproperty value:{$relationmodelid}@{$relationschema}");
					}

					// error_log("relationpiece; fetching property ($relationproperty) for ($modeluri) returns ($relationmodelid)");
					
					if ($relationmodelid == "")
					{
						// it doesnt exist... return an error (action of the error is exposed by getmodeltaxonomyproperty

						if (is_user_logged_in())
						{
							if (nxs_stringstartswith($relationproperty, "="))
							{
								$input = "<span style='color:red;'>invalid; referenced property ($relationproperty) for ($modeluri) is empty/not found; likely you used two equal signs in the lookup!</span>";
							}
							else
							{
								$input = "<span style='color:red;'>invalid; referenced property ($relationproperty) for ($modeluri) is empty/not found</span>";
							}
						}
						else
						{
							$input = "invalid.reference";
						}
						
						return $input;
					}
					
					// update the modeluri such that it will point to the related item
					$modeluri = "{$relationmodelid}@{$relationschema}";
				}
			}
			
			// retrieve the property of the specified modeluri	
			$args = array
			(
				"modeluri" => $modeluri,
				"property" => $property,
			);
			$input = $nxs_g_modelmanager->getmodeltaxonomyproperty($args);
			
			if ($atts["encode"] == "")
			{
				$input = htmlentities($input);
				// 2017 07 06; the dollar sign is not properly replaced causing php to evaluate it to empty string
				// if we wouldn't replace it here...
				$input = str_replace('$', "&dollar;", $input);
			}
			else if ($atts["encode"] == "none")
			{
				// 
			}
			else
			{
				// nothing
			}
				
			if ($atts["errorlog"] == "true")
			{
				error_log("modelproperty; result;{$input}");
			}
		}
		else if ($op == "modelidbymd5")
		{
			// returns a (semi random) id of a model based upon the md5 index of an indexer variable

			global $nxs_g_modelmanager;
			
			$schema = $atts["schema"];
			$modeluri = "singleton@listof{$schema}";
			$contentmodel = $nxs_g_modelmanager->getcontentmodel($modeluri);
			$ids = array();
			$instances = $contentmodel[$schema]["instances"];
			
			if (count($instances) == 0)
			{
				nxs_webmethod_return_nack("unable to proceed; no instances found; check if the schema is correct and filled; schema;" . $schema);
			}
			
			foreach ($instances as $instance)
			{
				$itemhumanmodelid = $instance["content"]["humanmodelid"];
				$ids[] = $itemhumanmodelid;
			}
			
			$max = count($ids);
			$indexer = $atts["indexer"];
			
			
			$md5 = md5($indexer);
			$inthash = intval(substr($md5, 0, 8), 16);
			$index = $inthash % $max;
			$input = $ids[$index];
			
			// if the skipindexer is set, we should
			$skipindexer = $atts["skipindexer"];
			if (isset($skipindexer))
			{
				$md5 = md5($skipindexer);
				$inthash = intval(substr($md5, 0, 8), 16);
				$skipindex = $inthash % $max;
				if ($index == $skipindex)
				{
					// skip to the next item
					$newindex = $index + 1;
					$newindex = $newindex % $max;
					$newinput = $ids[$newindex];
					
					$input = $newinput;
				}
			}
			
			if ($input == "")
			{
				//
				nxs_webmethod_return_nack("modelidbymd5; empty result; unable to proceed; " . json_encode($instances) . ";" . $indexer);
			}
			
			// error_log("modelidbymd5;" . count($ids) . ";$index;$input");
		}
		else if ($op == "modelurisbymd5")
		{
			// returns a distinct char seperated string of "n" (semi random) ids of a model based upon 
			// the md5 index of an indexer variable, for example "1@model;5@model;2@model"

			global $nxs_g_modelmanager;
			
			$schema = $atts["schema"];
			if ($schema == "") { return "modelurisbymd5; schema not specified"; }
			
			// handle cache refresh of schema
			if (true)
			{
				$cachebehaviour = $atts["cachebehaviour"];
				if ($cachebehaviour == "")
				{
				}
				else if ($cachebehaviour == "none")
				{
				}
				else if ($cachebehaviour == "refreshfirstphpruntime")
				{
					// refreshes the cache the first time this is requested in the php runtime duration
					global $nxs_g_modelrefreshphpruntime;
					if (!isset($nxs_g_modelrefreshphpruntime[$schema]))
					{
						$nxs_g_modelrefreshphpruntime[$schema] = true;
						//error_log("nxs_g_modelrefreshphpruntime refresh required for $schema");
						// clear it!
						$nxs_g_modelmanager->cachebulkmodels($schema);
					}
				}
				else
				{
					nxs_webmethod_return_nack("unsupported cachebehaviour; $cachebehaviour");
				}
			}
			
			$modeluri = "singleton@listof{$schema}";
			$contentmodel = $nxs_g_modelmanager->getcontentmodel($modeluri);
			$ids = array();
			$instances = $contentmodel[$schema]["instances"];
			
			//
			
			$min = $atts["min"];
			$max = $atts["max"];
			$count = $min;
		
			if ($min != $max) { return "modelurisbymd5; not yet implemented; for now min and max should be equal"; }
			if ($min == "") { return "modelurisbymd5; min not specified"; }
			if ($min <= 0) { return "modelurisbymd5; min should be > 0"; }
			if ($min > $max) { return "modelurisbymd5; min should be < max"; }

			//
					
			$indexer = $atts["indexer"];
			if ($indexer == "") { return "modelurisbymd5; indexer not specified"; }
			
			$result = array();
			
			if (count($instances) == 0)
			{
				nxs_webmethod_return_nack("unable to proceed; no instances found; check if the schema is correct and filled; schema;" . $schema);
			}
			
			foreach ($instances as $instance)
			{
				$itemhumanmodelid = $instance["content"]["humanmodelid"];
				$ids[] = $itemhumanmodelid;
			}
			
			$totalitemsavailable = count($ids);
			
			//
			$succeeded = false;
			$triesleft = 999; // randomly picked, should be enough?
			while ($triesleft > 0)
			{
				$triesleft--;
			
				$currentindexer = $indexer . $triesleft;
				
				//
				$md5 = md5($currentindexer);
				$inthash = intval(substr($md5, 0, 8), 16);
				$index = $inthash % $totalitemsavailable;
				$input = $ids[$index] . '@' . $schema;
				
				if (!in_array($input, $result)) 
				{
					$result[] = $input;
					
					if (count($result) == $count)
					{
						// fully loaded, lets return
						$succeeded = true;	
						break;
					}
				}
				else
				{
					// we dont allow duplicates
				}
			}
			
			if (!$succeeded)
			{
				//
				return "modelurisbymd5; insufficient items returned (is max set too high?)?!";
			}
			
			$glue = ";";
			$input = implode($glue, $result);
			
			// error_log("modelidbymd5;" . count($ids) . ";$index;$input");
		}
		else if ($op == "modeldump")
		{
			if (is_user_logged_in())
			{
				global $nxs_g_modelmanager;
				$modeluri = $atts["modeluri"];
				$contentmodel = $nxs_g_modelmanager->getcontentmodel($modeluri);
				$taxonomy = "properties";
				$props = $contentmodel[$taxonomy]["taxonomy"];
				$input = "json of $modeluri:<br />".json_encode($props)."<br />";
			}
			else
			{
				// hidden for anonymous users
				$input = "";
			}
		}
		else if ($op == "modeluriscount")
		{
			global $nxs_g_modelmanager;
			
			$iterator_datasource = $atts["singularschema"];
			
			$cachebehaviour = $atts["cachebehaviour"];
			if ($cachebehaviour == "")
			{
			}
			else if ($cachebehaviour == "none")
			{
				// keep existing
			}
			else if ($cachebehaviour == "refreshfirstphpruntime")
			{
				// refreshes the cache the first time this is requested in the php runtime duration
				global $nxs_g_modelrefreshphpruntime;
				if (!isset($nxs_g_modelrefreshphpruntime[$iterator_datasource]))
				{
					$nxs_g_modelrefreshphpruntime[$iterator_datasource] = true;
					//error_log("nxs_g_modelrefreshphpruntime refresh required for $iterator_datasource");
					// clear it!
					$nxs_g_modelmanager->cachebulkmodels($iterator_datasource);
				}
			}
			else
			{
				nxs_webmethod_return_nack("unsupported cachebehaviour; $cachebehaviour");
			}
			
			$iteratormodeluri = "singleton@listof{$iterator_datasource}";
			$contentmodel = $nxs_g_modelmanager->getcontentmodel($iteratormodeluri);
			$instances = $contentmodel[$iterator_datasource]["instances"];
						
			$input = count($instances);
		}
		else if ($op == "listmodeluris")
		{
			// todo: add support for ordering
			
			global $nxs_g_modelmanager;
			
			$instanceuris = array();
			
			$datasourceprovidertype = $atts["datasourceprovidertype"];
			if ($datasourceprovidertype == "" || $datasourceprovidertype == "singularschema")
			{
				$iterator_datasource = $atts["singularschema"];
				if ($iterator_datasource == "")
				{
					return "$op; no singularschema specified?";
				}
			}
			else if ($datasourceprovidertype == "segmented")
			{
				// to be used when you want to output a list of modeluris (ex. 1@a;2@a) from 
				// a particular spreadsheet that is segmented (meaning that the entire spreadsheet
				// is cut into parts; so for example instead of having a huge nxs.games.game spreadsheet,
				// we have segmented them in nxs.games.gameboy.game and nxs.games.segasaturn.game etc.).
				// this operator is used when the model to be used is derived through a "lookup" defined
				// in another table (through a lookup)
				
				$instanceuris = array();
				
				global $nxs_g_modelmanager;
				
				// step 1; evaluate the singularschema to be used
				$segmentschemaprovidertype = $atts["segmentschemaprovidertype"];
				if ($segmentschemaprovidertype == "modellookup")
				{
					// the singularschema to use is to be derived through a modellookup
					$segmentschema_modellookupuri = $atts["segmentschema_modellookupuri"];
					$segmentschema_modellookupproperty = $atts["segmentschema_modellookupproperty"];
					
					// get the property
					$subargs = array
					(
						"modeluri" => $segmentschema_modellookupuri,
						"segmentschema_modellookupproperty" => $segmentschema_modellookupproperty,
					);
					$iterator_datasource = $nxs_g_modelmanager->getmodeltaxonomyproperty($subargs);
				}
				else
				{
					return "$op; unsupported segmentschemaprovidertype; $segmentschemaprovidertype";
				}
			}
			else
			{
				return "$op; unsupported sourcetype; $sourcetype";
			}
			
			$cachebehaviour = $atts["cachebehaviour"];
			if ($cachebehaviour == "")
			{
			}
			else if ($cachebehaviour == "none")
			{
				//
			}
			else if ($cachebehaviour == "refreshfirstphpruntime")
			{
				// refreshes the cache the first time this is requested in the php runtime duration
				global $nxs_g_modelrefreshphpruntime;
				if (!isset($nxs_g_modelrefreshphpruntime[$iterator_datasource]))
				{
					$nxs_g_modelrefreshphpruntime[$iterator_datasource] = true;
					//error_log("nxs_g_modelrefreshphpruntime refresh required for $iterator_datasource");
					// clear it!
					$nxs_g_modelmanager->cachebulkmodels($iterator_datasource);
				}
			}
			else
			{
				nxs_webmethod_return_nack("unsupported cachebehaviour; $cachebehaviour");
			}
			
			// todo: rewrite using the new getall function 
			
			$iteratormodeluri = "singleton@listof{$iterator_datasource}";
			$contentmodel = $nxs_g_modelmanager->getcontentmodel($iteratormodeluri);
			$instances = $contentmodel[$iterator_datasource]["instances"];
			
			// return "instances count:" . count($instances);
			foreach ($instances as $instance)
			{
				$itemhumanmodelid = $instance["content"]["humanmodelid"];
				$instanceuri = "{$itemhumanmodelid}@{$iterator_datasource}";
								
				$conditionevaluation = true;

				$conditionindexers = array("", "_1", "_2");	// add more conditionindexers here when needed...
				foreach ($conditionindexers as $conditionindexer)
				{
					// operator
					
					$operatorproperty = $atts["where_property{$conditionindexer}"] . $atts["property"];
					$operator = $atts["where_operator{$conditionindexer}"] . $atts["operator"];
					$operatorvalue = $atts["where_value{$conditionindexer}"] . $atts["value"];
	
					if ($operator == "")
					{
						// ignore this one
						continue;
					}
					else if ($operator == "caseinsensitivelike")
					{
						$fieldvalue = $nxs_g_modelmanager->getmodeltaxonomyproperty(array("modeluri"=>$instanceuri, "property"=>$operatorproperty));
						$conditionevaluation = nxs_stringcontains_v2($fieldvalue, $operatorvalue, true);
					}
					else if ($operator == "equals")
					{
						$fieldvalue = $nxs_g_modelmanager->getmodeltaxonomyproperty(array("modeluri"=>$instanceuri, "property"=>$operatorproperty));
						$conditionevaluation = ($fieldvalue == $operatorvalue);
					}
					else if ($operator == "equalscaseinsensitive")
					{
						$fieldvalue = $nxs_g_modelmanager->getmodeltaxonomyproperty(array("modeluri"=>$instanceuri, "property"=>$operatorproperty));
						$conditionevaluation = strcasecmp($fieldvalue, $operatorvalue) == 0;
					}
					else if ($operator == "equalscaseinsensitivetrimmed")
					{
						$fieldvalue = $nxs_g_modelmanager->getmodeltaxonomyproperty(array("modeluri"=>$instanceuri, "property"=>$operatorproperty));
						$conditionevaluation = strcasecmp(trim($fieldvalue), trim($operatorvalue)) == 0;
					}
					else
					{
						return "$op; unsupported where operator ($operator)";
						// not supported; evaluates to false
					}
					
					//
					if ($conditionevaluation === false)
					{
						// if one condition is false, break all (we use a logical AND operator here)
						break;
					}
					
					// loop; proceed evaluating the next condition
				}
				
				// if condition evaluates to true, add the item to the resulting set
				if ($conditionevaluation)
				{
					$instanceuris[] = $instanceuri;
				}
			}
			
			$input = implode(";", $instanceuris);
		}
		else if ($op == "strtoorder")
		{
			// todo: extend the function to also support 0..9 and a space?
			$max_orders_chars = 3;
			$order_base = $input;
			$order_base = strtolower($order_base);
			$order_base = substr($order_base, 0, $max_orders_chars);
			$alphabet = range('a', 'z');
			$sizeperchar = count($alphabet);
			$order = 0;
			
			$explanation = "";
			for ($order_base_index = 0; $order_base_index < $max_orders_chars; $order_base_index++)
			{
				$char = $order_base[$order_base_index];
				$charorder = array_search($char, $alphabet);
				$powerof = ($max_orders_chars - $order_base_index);
				$factor = pow($sizeperchar, $powerof);	// cannot use ** because of PHP compatibility issues
				$value = $charorder * $factor;
				$order += $value;
			}
			
			$input = $order;
		}
		else if ($op == "file_get_contents" || $op == "filegetcontents")
		{
			$url = $atts["url"];
			$input = nxs_geturlcontents(array("url" => $url));
		}
		else if ($op == "jsonsubvalues")
		{
			$key = $atts["key"];
			$json = json_decode($input, true);
			$json = $json[$key];
			
			if ($atts["encode"] == "JSON_UNESCAPED_SLASHES")
			{
				$input = json_encode($json, JSON_UNESCAPED_SLASHES);
			}
			else 
			{
				$input = $json;
			}
		}
		else if ($op == "ifthenelse")
		{
			//error_log("condition for $input");
			$condition = $atts["condition"];
			if ($condition == "true")
			{
				$input = $atts["then"];
			}
			else
			{
				$input = $atts["else"];
			}
		}
		else if ($op == "map")
		{
			if (isset($atts[$input]))
			{
				$input = $atts[$input];
			}
			else
			{
				$input = $atts["fallback"];
			}
		}
		else if ($op == "strip_tags")
		{
			$orig = $input;
			$input = strip_tags($input);
			//error_log("strip_tags; $orig becomes $input");
		}
		else if ($op == "substract")
		{
			$input = ($initial - $input);
		}
		else if ($op == "year" || $op == "currentyear")
		{
			$input = date("Y");
		}
		else if ($op == "urlencode")
		{
			$input = urlencode($input);
		}
		else if ($op == "currenturi")
		{
			$input = nxs_geturicurrentpage();
			if ($atts["urlencode"] == "true")
			{
				$input = urlencode($input);
			}
		}
		else if ($op == "currenturl")
		{
			$input = nxs_geturlcurrentpage();
			if ($atts["urlencode"] == "true")
			{
				$input = url_encode($input);
			}
		}
		else if ($op == "dumppost")
		{
			$postid = get_the_ID();
			$input = "";
			$input .= "<div class='nxs-text'><div class='nxs-dump nxs-default-p nxs-text'><p><table>";
			//
			$key = "posttype";
			$val = nxs_getwpposttype($postid);
			$input .= "<tr><td>{$key}</td><td>{$val}</td></tr>";
			//
			$key = "status";
			$val = get_post_status($postid);
			$input .= "<tr><td>{$key}</td><td>{$val}</td></tr>";
			//
			$key = "categories";
			$val = "";
			$categories = get_the_category($postid);
			foreach ($categories as $cat)
			{
				$val .= "'" . esc_html( $cat->name ) . "'<br />";
			}
			$input .= "<tr><td>{$key}</td><td>{$val}</td></tr>";
			//
			$input .= "</table></p></div></div>";
		}
		else if ($op == "dumpuser")
		{
			// $postid = get_the_ID();
			$input = "";
			$input .= "<div class='nxs-text'><div class='nxs-dump nxs-default-p nxs-text'><p><table>";
			//
			//
			$key = "capabilities";
			$val = "";
			$data = get_userdata( get_current_user_id() );
			$current_user_caps = $data->allcaps;
			foreach ($current_user_caps as $cap => $enabled)
			{
				if ($enabled)
				{
					$val .= $cap . "<br />";
				}
			}
			
			$input .= "<tr><td>{$key}</td><td>{$val}</td></tr>";
			//
			$input .= "</table></p></div></div>";
		}
		else if ($op == "addqueryparameter")
		{
			$queryparameter = $atts["queryparameter"];
			$queryparametervalue = $atts["queryparametervalue"];
			$input = nxs_addqueryparametertourl_v2($input, $queryparameter, $queryparametervalue, true, true);
		}
		else if ($op == "explode")
		{
			$delimiter = $atts["delimiter"];
			if ($delimiter == "") { $delimiter = "|"; }
			
			$return = $atts["return"];
			if ($return == "valueatindex")
			{
				//var_dump($input);
				//die();
				
				$index = $atts["index"];
				$pieces = explode($delimiter, $input);
				$input = $pieces[$index];
			}
			else if ($return == "concatenateditemsblanksremoved")
			{
				$index = $atts["index"];
				$pieces = explode($delimiter, $input);
				// remove blanks
				$pieces = array_filter($pieces);
				// reconstruct
				$input = implode($delimiter, $pieces);
			}
			else if ($return == "json")
			{
				$index = $atts["index"];
				$name = $atts["name"];
				if ($name == "")
				{
					$name = "value";
				}
				$pieces = explode($delimiter, $input);
				$list = array();
				foreach ($pieces as $piece)
				{
					$object = new stdClass();
	        $object->$name = $piece;
	        $list[] = $object;
				}
				$input = json_encode($list);
			}
			else if ($return == "")
			{
				$property = $atts["property"];
				if ($property == "")
				{
					$property = "fallback";
				}
				$newpieces = array();
				$pieces = explode($delimiter, $input);
				foreach($pieces as $piece)
				{
					
					$piece = '{"' . $property . '":"' . $piece . '"}';
					$newpieces[] = $piece;
				}
				$input = implode(",", $newpieces);
			}
		}
		else if ($op == "get_posts_modeluris")
		{
			$args = $atts;
			$posts = get_posts($args);
			$modeluris = array();
			foreach ($posts as $post) 
			{
   			$modeluris[] = $post->ID . "@wp.post";
			}
			$input = implode(";", $modeluris);
			$input .= ";";
		}
		else if ($op == "archive_modeluris")
		{
			$input = "";
			global $wp_query;
			$posts = $wp_query->get_posts();
			
			foreach ($posts as $post)
			{
				$postids[]= $post->ID . "@wp.post";	
			}
			$input = implode(";", $postids);
			$input .= ";";
			
			//var_dump($input);
			//die();
		}
		else if ($op == "image_src_by_id")
		{
			if ($input != "")
			{
				$imagemetadata = nxs_wp_get_attachment_image_src($input, 'full', true);
				$imageurl = $imagemetadata[0];
				$input = nxs_img_getimageurlthemeversion($imageurl);
			}
		}
		else if ($op == "truncate")
		{
			$length = $atts["length"];
			$input = nxs_truncate_string($input, $length);
		}
	}
	
	$output = $input;
		
	return $output;
}
add_shortcode("nxsstring", "nxs_sc_string");	// deprecated ... use nxs_string instead...
add_shortcode("nxs_string", "nxs_sc_string");

// for example [nxsbool ops="isnotempty" value="aap"]
function nxs_sc_bool($atts, $content = null, $name='') 
{
	extract($atts);
	
	if (isset($sc_scope))
	{
		$scoperesult = nxs_sc_handlescope($atts, $content, $name);
		if ($scoperesult !== false)
		{
			// we are outside the scope, exit
			return $scoperesult;
		}
	}
	
	nxs_ob_start();
	
	$input = $content;
	if ($input == "")
	{
		$input = $atts["input"];
	}
	if ($input == "")
	{
		$input = $atts["value"];
	}
	
	$ops = $atts["ops"];
	$ops = str_replace(",","|", $ops);
	$ops = str_replace(";","|", $ops);
	$opslist = explode("|", $ops);
	foreach ($opslist as $op)
	{
		$op = trim($op);
		
		if (false)
		{
			//
		}
		
		else if ($op == "isempty" || $op == "isnotempty" || $op == "notempty" || $op == "!isempty")
		{
			$evaluation = trim($input) == "";
			if (nxs_stringstartswith($op, "!") || nxs_stringstartswith($op, "not") || nxs_stringstartswith($op, "isnot")) { $evaluation = !$evaluation; }
			$input = $evaluation ? "true": "false";
		}
		else if ($op == "in_array" || $op == "!in_array")
		{
			$array = $atts["array"];
			$pieces = explode(";", $array);
			
			$evaluation = in_array($value, $pieces);
			if (nxs_stringstartswith($op, "!")) { $evaluation = !$evaluation; }
			$input = $evaluation ? "true": "false";
		}
		else if ($op == "is_home" || $op == "!is_home")
		{
			$evaluation = is_home();
			if (nxs_stringstartswith($op, "!")) { $evaluation = !$evaluation; }
			$input = $evaluation ? "true": "false";
		}
		else if ($op == "is_singular" || $op == "!is_singular")
		{
			$post_types = $atts["post_types"];
			$evaluation = is_singular($post_types);
			if (nxs_stringstartswith($op, "!")) { $evaluation = !$evaluation; }
			$input = $evaluation ? "true": "false";
		}
		else if ($op == "in_category" || $op == "!in_category")
		{
			$category = $atts["category"];
			$evaluation = in_category($category);
			if (nxs_stringstartswith($op, "!")) { $evaluation = !$evaluation; }
			$input = $evaluation ? "true": "false";
		}
		else if ($op == "is_user_logged_in" || $op == "!is_user_logged_in")
		{
			$evaluation = is_user_logged_in();
			if (nxs_stringstartswith($op, "!")) { $evaluation = !$evaluation; }
			$input = $evaluation ? "true": "false";
		}
		else if ($op == "is_anonymous" || $op == "!is_anonymous")
		{
			$evaluation = !is_user_logged_in();
			if (nxs_stringstartswith($op, "!")) { $evaluation = !$evaluation; }
			$input = $evaluation ? "true": "false";
		}
		else if ($op == "current_user_can" || $op == "!current_user_can" || $op == "has_capability" || $op == "!has_capability"  || $op == "has_cap" || $op == "!has_cap")
		{
			$capability = $atts["capability"];
			$capability = str_replace(" ","_", $capability);	// sanitize the capability; spaces are not allowed; replace them with _'s
			$evaluation = current_user_can($capability);
			if (nxs_stringstartswith($op, "!")) { $evaluation = !$evaluation; }
			$input = $evaluation ? "true": "false";
		}
		else if ($op == "contains" || $op == "!contains")
		{
			$needle = $atts["containsneedle"];
			if ($needle == "")
			{
				$needle = $atts["needle"];
			}
			$ignorecase = $atts["ignorecase"] === "true";
			
			$evaluation = nxs_stringcontains_v2($input, $needle, $ignorecase);
			if (nxs_stringstartswith($op, "!")) { $evaluation = !$evaluation; }
			$input = $evaluation ? "true": "false";
		}
		else if ($op == "equals" || $op == "!equals" || $op == "notequals")
		{
			$equalsvalue = $atts["equalsvalue"];
			
			$evaluation = ($input == $equalsvalue);
			if (nxs_stringstartswith($op, "!") || nxs_stringstartswith($op, "not")) { $evaluation = !$evaluation; }
			$input = $evaluation ? "true": "false";
		}
		else if ($op == "is_numeric")
		{
			if (is_numeric($input))
			{
				$input = "true";
			}
			else
			{
				$input = "false";
			}
		}
		else if ($op == "httpok")
		{
			$webmethodoverrideresult = $atts["webmethodoverrideresult"];
			if (nxs_iswebmethodinvocation() && $webmethodoverrideresult != "")
			{
				// if the list is very long, its very annoying if the configuration of the list widget
				// will cause the entire list to be reloaded (as this is a very resource heavy operation)
				// to avoid the server from getting messed up, we return a static value here instead
				// error_log("url httpok check for; $url; overriden as $webmethodoverrideresult");
				$input = $webmethodoverrideresult;
			}
			else
			{
				$url = $atts["url"];
				if ($url == "")
				{
					// error_log("url httpok check for; $url; no url specified?");
					return "false";
				}
				
				$isactualretrievalrequired = true;
				
				$cache = $atts["cache"];
				$key = "httpheaderresponse_" . md5($url);
				if ($cache == "")
				{
					// ignore cache
				}
				else if ($cache == "200")
				{
					// check local cache
					
					$statuscode = get_transient($key);
					if ($statuscode == 'HTTP/1.1 200 OK')
					{
						$isactualretrievalrequired = false;
					}
					else
					{
						$isactualretrievalrequired = true;
					}
				}
				else
				{
					// not supported
					error_log("url httpok; cache value has unsupported value; $cache");
				}
				
				
				if ($isactualretrievalrequired)
				{
					// the resource heavy invocation ...
					$headers = get_headers($url, 1);
					$statuscode = $headers[0];
					
					// log this so we can see whats going on on the server
					error_log("url httpok; actual; check for; $url; $statuscode");
					
					// update the cache if the cache is being used
					if ($cache != "")
					{
						set_transient($key, $statuscode);
					}
				}
				else
				{
					error_log("url httpok; cache; $url; $statuscode");
				}
				
				if ($statuscode == 'HTTP/1.1 200 OK') 
				{
					// this indicates it went ok; no httpok
					$input = "true";
				}
				else
				{
					$input = "false";
				}
			}
		}
		else if ($op == "not")
		{
			if ($input == "true")
			{
				$input = "false";
			}
			else if ($input == "false")
			{
				$input = "true";
			}
			else
			{
				if (isset($atts["fallback"]))
				{
					$input = $atts["fallback"];
				}
				else
				{
					$input = "err";
				}
			}
		}
		else if ($op == "modelexists")
		{
			$modeluri = $atts["modeluri"];
			global $nxs_g_modelmanager;
			$r = $nxs_g_modelmanager->getmodel($modeluri);
			if ($r === false)
			{
				$input = "false";
			}
			else
			{
				$input = "true";
			}
		}
		else if ($op == "or")
		{
			// true if any of the item(s) is true, false otherwise
			$pieces = explode(";", $input);
			$input = "false";
		  foreach ($pieces as $piece)
		  {
		  	$piece = trim($piece);
		  	if ($piece == "true")
		  	{
		  		$input = "true";
		  		break;
		  	}
		  }
		}
		else if ($op == "and")
		{
			// true if all of the item(s) are true, false otherwise
			$pieces = explode(";", $input);
			$input = "true";
		  foreach ($pieces as $piece)
		  {
		  	$piece = trim($piece);
		  	if ($piece == "false")
		  	{
		  		$input = "false";
		  		break;
		  	}
		  }
		}
		
		else
		{
			// bool operation to be implemented ...
		}
	}
	
	echo $input;
	
	$output = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	/*
	if (isset($sc_scope))
	{
		echo "prior to evaluation;";
		var_dump($atts);
		var_dump($content);
		var_dump($output);
		echo "----<br />";
	}
	*/
		
	return $output;
}
add_shortcode('nxsbool', 'nxs_sc_bool');
add_shortcode('nxs_bool', 'nxs_sc_bool');


// for example [nxsint ops="add" p1="1" p2="2"]
function nxs_sc_int($atts, $content = null, $name='') 
{
	extract($atts);
	
	if (isset($sc_scope))
	{
		$scoperesult = nxs_sc_handlescope($atts, $content, $name);
		if ($scoperesult !== false)
		{
			// we are outside the scope, exit
			return $scoperesult;
		}
	}
	
	nxs_ob_start();
	
	$input = $content;
	if ($input == "")
	{
		$input = $atts["input"];
	}
	if ($input == "")
	{
		$input = $atts["value"];
	}
	
	$ops = $atts["ops"];
	$ops = str_replace(",","|", $ops);
	$ops = str_replace(";","|", $ops);
	$opslist = explode("|", $ops);
	foreach ($opslist as $op)
	{
		$op = trim($op);
		
		if (false)
		{
			//
		}
		
		else if ($op == "add")
		{
			$input = intval($atts["p1"]) + intval($atts["p2"]);
		}
	}
}
add_shortcode("nxsint", "nxs_sc_int");
add_shortcode("nxs_int", "nxs_sc_int");

function nxs_sc_command($atts, $content = null, $name='') 
{
	extract($atts);
	
	if (isset($sc_scope))
	{
		$scoperesult = nxs_sc_handlescope($atts, $content, $name);
		if ($scoperesult !== false)
		{
			// we are outside the scope, exit
			return $scoperesult;
		}
	}
	
	$origcontent = $content;
	
	$content = $content;
	if ($content == "")
	{
		$content = $atts["input"];
	}
	if ($content == "")
	{
		$content = $atts["value"];
	}
	
	$input = $content;
	
	$ops = $atts["ops"];
	$ops = str_replace(",","|", $ops);
	$ops = str_replace(";","|", $ops);
	$opslist = explode("|", $ops);
	foreach ($opslist as $op)
	{
		$op = trim($op);
		if ($op == "redirect301")
		{
			if (is_user_logged_in())
			{
				$output = "if you would not be logged in, this would redirect to <a href='$value'>$value</a>";
			}
			else
			{
				// cleanup output that was possibly produced before,
				// if we won't this could cause output to not be json compatible
				$existingoutput = nxs_outputbuffer_popall();
	
				header("HTTP/1.1 301 Moved Permanently"); 
				header("Location: {$value}"); 
				exit();
			}
		}
	}
	
	return $output;
}
add_shortcode('nxscommand', 'nxs_sc_command');

// spinner shortcodes
function nxs_sc_spin($atts, $content = null, $name='') 
{
	extract($atts);
	
	nxs_ob_start();
	
	if ($modeluri == "")
	{
		$modeluri = "{$humanid}@{$schema}";
	}
	
	$modeluri = "spinner:{$modeluri}";
	
	global $nxs_g_modelmanager;
	
	$lookupargs = array
	(
		"modeluris" => $modeluri,
	);
	$lookups = $nxs_g_modelmanager->getlookups_v2($lookupargs);
	
	$text = $lookups["spinner:text.textvalue"];
	
	//
	$text = str_replace("[", "{{", $text);
	$text = str_replace("]", "}}", $text);
	
	if (true)
	{
		$lookup = array();
		foreach ($atts as $key=>$val)
		{
			$shoulddecorate = false;
			
			if (is_user_logged_in())
			{
				if ($_REQUEST["spindecorate"] == "true")
				{
					$shoulddecorate = true;
				}
			}
			
			if ($shoulddecorate)
			{
				$lookup[$key] = "<b class='ph' style='color: white; text-shadow: none; background-color: #000; border-style: dotted; border-width: 1px; border-color: red; '>{$val}</b>";
			}
			else
			{
				$lookup[$key] = "{$val}";
			}
		}
	
		// use the atts passed in to this shortcode as a lookup table
		$translateargs = array
		(
			"lookup" => $lookup,
			"item" => $text,
		);
		$text = nxs_filter_translate_v2($translateargs);
	}
	
	echo $text;
	
	$output = nxs_ob_get_contents();
	nxs_ob_end_clean();
		
	return $output;
}
add_shortcode('nxsspin', 'nxs_sc_spin');

// widget specific shortcodes

function nxs_sc_title($atts, $content = null)
{
	if ($content == "" && $atts["title"] != "")
	{
		$content = $atts["title"];
	}
	
	$args_overriden = array
	(
		"title" => $content,
	);
	$args = array_merge($atts, $args_overriden);
	
	//
	//
	//
	
	// delegate rendering to the frontendframework
	$frontendframework = nxs_frontendframework_getfrontendframework();
	$filetoinclude = NXS_FRAMEWORKPATH . "/nexuscore/frontendframeworks/{$frontendframework}/frontendframework_{$frontendframework}.php";
	require_once($filetoinclude);
	
	$functionnametoinvoke ="nxs_frontendframework_{$frontendframework}_gethtmlfortitle";
	$result = call_user_func_array($functionnametoinvoke, array($args));
	
	return $result;
}
add_shortcode("nxstitle", "nxs_sc_title");
add_shortcode("nxs_title", "nxs_sc_title");

function nxs_sc_button($atts, $content = null)
{
	if ($atts["output"] == "shortcode")
	{
		// unset($atts["output"]);
		$result = nxs_sc_reconstructshortcode($atts, $content, "nxs&#95;button");
		$result = str_replace("[ ", "&#91;", $result);
		$result = str_replace("]", "&#93;", $result);
		$result = str_replace("=", "&#61;", $result);
		
		return $result;
	}
	return nxs_gethtmlforbutton_v2($atts);
}
add_shortcode("nxsbutton", "nxs_sc_button");
add_shortcode("nxs_button", "nxs_sc_button");

function nxs_sc_img($atts, $content = null)
{
	return nxs_gethtmlforimage_v3($atts);
}
add_shortcode("nxs_img", "nxs_sc_img");

function nxs_sc_googlemap($atts, $content = null, $name='') 
{
	extract($atts);
	
	//$debug = json_encode($atts);
	//return "map :) $debug";
	
	global $nxs_sc_googlemap_cnt;
	$nxs_sc_googlemap_cnt++;
	
	$height = intval($height);
	if ($height == 0)
	{
		$height = 200;	// fallback
	}

	$zoom = intval($zoom);
	if ($zoom == 0)
	{
		$zoom = 17;	// fallback
	}
	
	if ($maptype == "")
	{
		$maptype = "";	// fallback
	}
	
	if ($id == "")
	{
		global $nxs_global_row_render_statebag;
		global $nxs_global_current_containerpostid_being_rendered;
		global $nxs_global_current_postid_being_rendered;
		global $nxs_global_placeholder_render_statebag;
		
		$widgetmetadata = $nxs_global_placeholder_render_statebag["widgetmetadata"];
		$postid = $widgetmetadata["postid"];
		$placeholderid = $widgetmetadata["placeholderid"];
		
		$id = "scmap_{$nxs_sc_googlemap_cnt}_{$postid}_{$placeholderid}";
	}
	
	nxs_requirewidget("googlemap");
	nxs_ob_start();
	
	?>
	<style>
		.template-widgettype-googlemap 
		{
			display: block;
			position: relative;
		}
		
	</style>
	<div class='nice'>
		<?php
			$args = array
			(
				"render_behaviour" => "code",
				"map_canvas_class" => "mapsheightofcontainer",
				"placeholderid" => $id,
				"address" => $address,
				"zoom" => $zoom,
				"maptypeid" => $maptypeid,
				"renderstyle" => "v2",
			);
			
			$renderresult = nxs_widgets_googlemap_render_webpart_render_htmlvisualization($args);
			echo $renderresult["html"];
		?>
	</div>
	
	<script>
		jQuery(".nice").parent().parent().css("position", "absolute").css("width", "100%").css("height", "100%");
		jQuery(".nice").parent().css("position", "absolute").css("width", "100%").css("height", "100%");
		jQuery(".nice").css("position", "absolute").css("width", "100%").css("height", "100%");
		jQuery("#map_canvas_<?php echo $id; ?>").css("position", "absolute").css("width", "100%").css("height", "100%");
	</script>
	
	
	<?php
	
	if (false)
	{
		?>
		<style>.mapsheightofcontainer{height:100%;}</style>
		<div id='mapcontainer_<?php echo $id; ?>' style='<?php echo $heightattribute; ?>;width: 100%; overflow: hidden;'>
			<?php
			$args = array
			(
				"render_behaviour" => "code",
				"map_canvas_class" => "mapsheightofcontainer",
				"placeholderid" => $id,
				"address" => $address,
				"zoom" => $zoom,
				"maptypeid" => $maptypeid,
			);
			
			$renderresult = nxs_widgets_googlemap_render_webpart_render_htmlvisualization($args);
			echo $renderresult["html"];
			?>
		</div>
		<?php
	}
	$output = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	return $output;
}
add_shortcode('nxsgooglemap', 'nxs_sc_googlemap');


//
//
//

// Whitelist the TEDTalks oEmbed URL
wp_oembed_add_provider( 'http://www.ted.com/talks/*', 'http://www.ted.com/talks/oembed.json' );

// kudos to http://wordpress.stackexchange.com/questions/67740/ted-talks-shortcode-not-working
function nxs_ted_shortcode( $atts ) {
    // We need to use the WP_Embed class instance
    global $wp_embed;

    // The "id" parameter is required
    if ( empty($atts['id']) )
        return '';

    // Construct the TEDTalk URL
    $url = 'http://www.ted.com/talks/view/lang/eng/id/' . $atts['id'];

    // Run the URL through the  handler.
    // This handler handles calling the oEmbed class
    // and more importantly will also do the caching!
    return $wp_embed->shortcode( $atts, $url );
}
add_shortcode('ted', 'nxs_ted_shortcode');

function nxs_vimeo_shortcode( $atts ) 
{
	if (count($atts) == 1)
	{
		$videoid = $atts[0];
		if (nxs_stringstartswith($videoid, "http://vimeo.com/"))
		{
			$videoid = str_replace("http://vimeo.com/", "", $videoid);
		}
		$result = '<iframe class="nxs-inline-vimeo" src="http://player.vimeo.com/video/'.$videoid.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	}
	else
	{
		$result = "(Unsupported vimeo)";
	}
  return $result;
}
add_shortcode('vimeo', 'nxs_vimeo_shortcode');

function nxs_sc_embed($atts, $content = null, $name='') 
{
	extract($atts);
	
	nxs_requirewidget("embed");

	$args = array
	(
		"render_behaviour" => "code",
	);
	// blend the parameters given
	$args = array_merge($args, $atts);
	
	$renderresult = nxs_widgets_embed_render_webpart_render_htmlvisualization($args);
	return $renderresult["html"];
}
add_shortcode('nxsembed', 'nxs_sc_embed');
add_shortcode('nxs_embed', 'nxs_sc_embed');

function nxs_sc_widget($atts, $content = null, $name='') 
{
	extract($atts);
	
	$type = $atts["type"];
	nxs_requirewidget($type);

	$args = array
	(
		"render_behaviour" => "code",
	);
	// blend the parameters given
	$args = array_merge($args, $atts);
	
	//$renderresult = nxs_widgets_embed_render_webpart_render_htmlvisualization($args);
	
	$functionnametoinvoke = "nxs_widgets_{$type}_render_webpart_render_htmlvisualization";
	$renderresult = call_user_func_array($functionnametoinvoke, array($args));
	
	return $renderresult["html"];
}
add_shortcode('nxs_widget', 'nxs_sc_widget');


function nxs_sc_video($atts, $content = null, $name='') 
{
	extract($atts);
	
	if ($id == "")
	{
		global $nxs_sc_video_cnt;
		$nxs_sc_video_cnt++;
	
		global $nxs_global_row_render_statebag;
		global $nxs_global_current_containerpostid_being_rendered;
		global $nxs_global_current_postid_being_rendered;
		global $nxs_global_placeholder_render_statebag;
		
		$widgetmetadata = $nxs_global_placeholder_render_statebag["widgetmetadata"];
		$postid = $widgetmetadata["postid"];
		$placeholderid = $widgetmetadata["placeholderid"];
		
		$id = "scvid__{$postid}_{$placeholderid}_{$nxs_sc_video_cnt}";
	}
	
	nxs_requirewidget("youtube");
	nxs_ob_start();
	
	?>
	<div class='ytwrap'>
		<?php
			
			$overriden_args = array
			(
				"rendermode" => "anonymous",
				"render_behaviour" => "code",
				"placeholderid" => $id,
			);
			
			$args = array_merge($atts, $overriden_args);
			
			$renderresult = nxs_widgets_youtube_render_webpart_render_htmlvisualization($args);
			echo $renderresult["html"];
		?>
	</div>
	<?php
	
	$output = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	return $output;
}
add_shortcode('nxs_video', 'nxs_sc_video');

function nxs_sc_text($atts, $content = null, $name='') 
{
	if ($content == "" && $atts["text"] != "")
	{
		$content = $atts["text"];
	}
	
	$args_overriden = array
	(
		"text" => $content,
	);
	$args = array_merge($atts, $args_overriden);
	
	//
	//
	//
	
	// delegate rendering to the frontendframework
	$frontendframework = nxs_frontendframework_getfrontendframework();
	$filetoinclude = NXS_FRAMEWORKPATH . "/nexuscore/frontendframeworks/{$frontendframework}/frontendframework_{$frontendframework}.php";
	require_once($filetoinclude);
	
	$functionnametoinvoke = "nxs_frontendframework_{$frontendframework}_gethtmlfortext";
	$result = call_user_func_array($functionnametoinvoke, array($args));
	
	return $result;
}
add_shortcode('nxs_text', 'nxs_sc_text');

function nxs_sc_icon($atts, $content = null, $name='') 
{
	if ($atts["output"] == "shortcode")
	{
		// unset($atts["output"]);
		$result = nxs_sc_reconstructshortcode($atts, $content, "nxs&#95;icon");
		$result = str_replace("[ ", "&#91;", $result);
		$result = str_replace("]", "&#93;", $result);
		$result = str_replace("=", "&#61;", $result);
		
		return $result;
	}
	
	extract($atts);
	
	
	
	$result = "<span class='nxs-icon-{$icon}'></span>";
	return $result;
	
	return $renderresult["html"];
}
add_shortcode('nxs_icon', 'nxs_sc_icon');