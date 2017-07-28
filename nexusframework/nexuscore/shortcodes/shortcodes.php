<?php 

function nxs_sc_reconstructshortcode_keyvalue($v, $k) 
{ 
	return sprintf("%s='%s'", $k, $v); 
}

function nxs_sc_reconstructshortcode($attributes, $content, $name)
{
	$implodedattributes = implode
	(
		' ', 
		array_map
		(
    	"nxs_sc_reconstructshortcode_keyvalue",
    	$attributes,
    	array_keys($attributes)
		)
	);
	
	$reconstructed = "[{$name} {$implodedattributes}]";
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
function nxs_sc_handlescope($attributes, $content = null, $name='')
{
	extract($attributes);
	
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
			$result = nxs_sc_reconstructshortcode($attributes, $content, $name);
		}
	}
	
	return $result;
}

// for example [nxsstring ops="lo;x_"]plumber_wordpress_theme[/nxsstring]
function nxs_sc_string($attributes, $content = null, $name='') 
{
	extract($attributes);
	
	if (isset($sc_scope))
	{
		$scoperesult = nxs_sc_handlescope($attributes, $content, $name);
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
		$content = $attributes["input"];
	}
	if ($content == "")
	{
		$content = $attributes["value"];
	}
	
	$input = $content;
	
	$ops = $attributes["ops"];
	$ops = str_replace(",","|", $ops);
	$ops = str_replace(";","|", $ops);
	$opslist = explode("|", $ops);
	foreach ($opslist as $op)
	{
		$op = trim($op);
		if ($op == "lo")
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
			$search = $attributes["search"];
			$replace = $attributes["replace"];
			
			$input = str_replace($search, $replace, $input);
		}
		else if ($op == "md5")
		{
			$input = md5($input);
		}
		else if ($op == "time")
		{
			$input = time();
		}
		else if ($op == "rand")
		{
			$min = 0;
			if (isset($attributes["min"]))
			{
				$min = $attributes["min"];
			}
			$max = getrandmax();
			if (isset($attributes["max"]))
			{
				$max = $attributes["max"];
			}
			$input = rand($min, $max);
		}
		else if ($op == "sitemapentry")
		{
			// todo: also support the changefreq and priority
			$url = nxs_url_prettyfy($attributes["url"]);
			$input = "<url><loc>{$url}</loc><changefreq>daily</changefreq><priority>0.8</priority></url>";
			
			if (is_user_logged_in())
			{
				$input = htmlentities($input) . "<br />";
			}
		}
		else if ($op == "randomstring")
		{
			$length = 10;
			if (isset($attributes["length"]))
			{
				$length = $attributes["length"];
			}
			$input = nxs_generaterandomstring($length);
		}
		else if ($op == "md5stringpicker")
		{
			//error_log("md5stringpicker;" . json_encode($attributes));
			
			$options = $attributes["options"];
			$pieces = explode("|", $options);
			$max = count($pieces);
			$indexer = $attributes["indexer"];
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
		else if ($op == "urlprettyfy" || $op == "urlprettify")
		{
			if ($attributes["debug"] == "true")
			{
				return "urlprettyfy;debug;($content);($input);($value)";
			}
			
			if (nxs_stringcontains($input, "http"))
			{
				// ignore; already handled
			}
			else if (nxs_stringcontains($input, "{{"))
			{
				// still too early, apparently, evaluate at a later moment in time (ignore the shortcode
				$input = nxs_sc_reconstructshortcode($attributes, $origcontent, $name);
				// note; an INSTANT return; don't proceed with any other possible operators
				return $input;
			}
			else
			{
				$input = nxs_url_prettyfy($input);
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
			// thanks to https://stackoverflow.com/questions/19050890/find-youtube-link-in-php-string-and-convert-it-into-embed-code
			$input = preg_replace(
        "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
        "<br /><div class=\"video-container\"><iframe class=\"video\" width=\"560\" height=\"315\" src=\"//www.youtube.com/embed/$2\" allowfullscreen></iframe></div><br />",
        $input
    	);
		}
		else if ($op == "linkify")
		{
			if ($attributes["excludeyoutube"] == "true")
			{
				$input = str_replace("https://www.youtube", "*NXS*PLACEHOLDER*YOUTUBE*", $input);
			}
			
			$input = preg_replace
			(
        "~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~",
        "<a target=\"blank\" class=\"linkified\" href=\"\\0\">\\0</a>", 
        $input
      );

			if ($attributes["excludeyoutube"] == "true")
			{
				$input = str_replace("*NXS*PLACEHOLDER*YOUTUBE*", "https://www.youtube", $input);
			}
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
			if (isset($attributes["trimchars"]))
			{
				$character_mask = $attributes["trimchars"];
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
				$replacement = $attributes["modellookupmismatchreplacement"];
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
				$replacement = $attributes["emptyreplacement"];
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
			$queryparameter = $attributes["queryparameter"];
			$description = $attributes["description"];
			
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
				if (isset($attributes["fallback"]))
				{
					$fallback = $attributes["fallback"];
				}
				else
				{
					$fallback = "";
				}
				
				$input = $fallback;
			}
			
			if ($attributes["quotesfix"] == "true")
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
			$replacement = $input * $attributes["multiplyfactor"];
			$input = $replacement;
		}
		else if ($op == "intval")
		{
			if ($attributes["strip"] == "allexceptdigits")
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
			if ($attributes["errorlog"] == "true")
			{
				error_log("shortcodes errorlog;modeluri:$modeluri;property:$property");
			}
			if ($attributes["debug"] == "true")
			{
				return "modelproperty; modeluri:$modeluri;property:$property";
			}
			
			global $nxs_g_modelmanager;
			
			$source = $attributes["source"];
			if ($source == "md5indexer")
			{
				// the modeluri is derived based upon the md5 indexer of a specified schema
				$schema = $attributes["schema"];
				$property = $attributes["property"];
				
				$cachebehaviour = $attributes["cachebehaviour"];
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
				$possibilities = $nxs_g_modelmanager->gettaxonomypropertiesofallmodels($a);

				// grab the indexer
				
				$max = count($possibilities);
				//error_log("modelproperty;md5indexer;max;".$max);

				$indexer = $attributes["indexer"];
				//error_log("modelproperty;md5indexer;indexer;".$indexer);
				
				$md5 = md5($indexer);
				$inthash = intval(substr($md5, 0, 8), 16);
				$index = $inthash % $max;
				$modelid = $possibilities[$index]["{$schema}_id"];
				$modeluri = "{$modelid}@{$schema}";
				//error_log("modelproperty;md5indexer;".json_encode($possibilities[$index]));
				//error_log("modelproperty;md5indexer;result;".$input);
			}
			else 
			{
				$modeluri = $attributes["modeluri"];		// the base modeluri for which the property will be retrieved
				if ($modeluri == "")
				{
					global $nxs_global_current_containerpostid_being_rendered;
					$modeluri = "{$nxs_global_current_containerpostid_being_rendered}@wp.post";
				}
			}
			
			$property = $attributes["property"];		// the property to be retrieved
			$relations = $attributes["relations"];
			
			//
			$ignorewhenlist = array($modeluri, $property, $relations, $input);
			
			//$ignorewhenlist = array($input, $modeluri, $property, $relations);
			foreach ($ignorewhenlist as $ignorewhen)
			{
				// special case handling; 
				if (nxs_stringcontains($ignorewhen, "{{"))
				{
					// still too early, apparently, evaluate at a later moment in time (ignore the shortcode
					$input = nxs_sc_reconstructshortcode($attributes, $origcontent, $name);
					// note; an INSTANT return; don't proceed with any other possible operators
					return $input;
				}
			}
			
			
			
			if ($attributes["errorlog"] == "true")
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
					
					if ($attributes["errorlog"] == "true")
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
			
			$input = htmlentities($input);
			// 2017 07 06; the dollar sign is not properly replaced causing php to evaluate it to empty string
			// if we wouldn't replace it here...
			$input = str_replace('$', "&dollar;", $input);
				
			if ($attributes["errorlog"] == "true")
			{
				error_log("modelproperty; result;{$input}");
			}
		}
		else if ($op == "modelidbymd5")
		{
			// returns a (semi random) id of a model based upon the md5 index of an indexer variable

			global $nxs_g_modelmanager;
			
			$schema = $attributes["schema"];
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
			$indexer = $attributes["indexer"];
			
			
			$md5 = md5($indexer);
			$inthash = intval(substr($md5, 0, 8), 16);
			$index = $inthash % $max;
			$input = $ids[$index];
			
			// if the skipindexer is set, we should
			$skipindexer = $attributes["skipindexer"];
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
		else if ($op == "modeldump")
		{
			if (is_user_logged_in())
			{
				global $nxs_g_modelmanager;
				$modeluri = $attributes["modeluri"];
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
		else if ($op == "listmodeluris")
		{
			// todo: add support for ordering
			
			global $nxs_g_modelmanager;
			
			$instanceuris = array();
			
			$datasourceprovidertype = $attributes["datasourceprovidertype"];
			if ($datasourceprovidertype == "")
			{
				$iterator_datasource = $attributes["singularschema"];
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
				$segmentschemaprovidertype = $attributes["segmentschemaprovidertype"];
				if ($segmentschemaprovidertype == "modellookup")
				{
					// the singularschema to use is to be derived through a modellookup
					$segmentschema_modellookupuri = $attributes["segmentschema_modellookupuri"];
					$segmentschema_modellookupproperty = $attributes["segmentschema_modellookupproperty"];
					
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
			
			$cachebehaviour = $attributes["cachebehaviour"];
			if ($cachebehaviour == "")
			{
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
					
					$operatorproperty = $attributes["where_property{$conditionindexer}"] . $attributes["property"];
					$operator = $attributes["where_operator{$conditionindexer}"] . $attributes["operator"];
					$operatorvalue = $attributes["where_value{$conditionindexer}"] . $attributes["value"];
	
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
		else if ($op == "filegetcontents")
		{
			$url = $attributes["url"];
			$input = nxs_geturlcontents(array("url" => $url));
		}
		else if ($op == "jsonsubvalues")
		{
			$key = $attributes["key"];
			$json = json_decode($input, true);
			$json = $json[$key];
			$input = json_encode($json);
		}
		else if ($op == "ifthenelse")
		{
			//error_log("condition for $input");
			$condition = $attributes["condition"];
			if ($condition == "true")
			{
				$input = $attributes["then"];
			}
			else
			{
				$input = $attributes["else"];
			}
		}
		else if ($op == "strip_tags")
		{
			$orig = $input;
			$input = strip_tags($input);
			//error_log("strip_tags; $orig becomes $input");
		}
		else if ($op == "year" || $op == "currentyear")
		{
			$input = date("Y");
		}
		else if ($op == "currenturl")
		{
			$input = nxs_geturlcurrentpage();
			if ($attributes["urlencode"] == "true")
			{
				$input = url_encode($input);
			}
		}
		else if ($op == "addqueryparameter")
		{
			$queryparameter = $attributes["queryparameter"];
			$queryparametervalue = $attributes["queryparametervalue"];
			$input = nxs_addqueryparametertourl_v2($input, $queryparameter, $queryparametervalue, true, true);
		}
		else if ($op == "explode")
		{
			$delimiter = $attributes["delimiter"];
			if ($delimiter == "") { $delimiter = "|"; }
			
			$return = $attributes["return"];
			if ($return == "valueatindex")
			{
				//var_dump($input);
				//die();
				
				$index = $attributes["index"];
				$pieces = explode($delimiter, $input);
				$input = $pieces[$index];
			}
			else if ($return == "concatenateditemsblanksremoved")
			{
				$index = $attributes["index"];
				$pieces = explode($delimiter, $input);
				// remove blanks
				$pieces = array_filter($pieces);
				// reconstruct
				$input = implode($delimiter, $pieces);
			}
			else if ($return == "json")
			{
				$index = $attributes["index"];
				$name = $attributes["name"];
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
				$property = $attributes["property"];
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
			$args = $attributes;
			$posts = get_posts($args);
			$modeluris = array();
			foreach ($posts as $post) 
			{
   			$modeluris[] = $post->ID . "@wp.post";
			}
			$input = implode(";", $modeluris);
			$input .= ";";
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
	}
	
	$output = $input;
		
	return $output;
}
add_shortcode('nxsstring', 'nxs_sc_string');

// for example [nxsbool ops="isnotempty" value="aap"]
function nxs_sc_bool($attributes, $content = null, $name='') 
{
	extract($attributes);
	
	if (isset($sc_scope))
	{
		$scoperesult = nxs_sc_handlescope($attributes, $content, $name);
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
		$input = $attributes["input"];
	}
	if ($input == "")
	{
		$input = $attributes["value"];
	}
	
	$ops = $attributes["ops"];
	$ops = str_replace(",","|", $ops);
	$ops = str_replace(";","|", $ops);
	$opslist = explode("|", $ops);
	foreach ($opslist as $op)
	{
		$op = trim($op);
		if ($op == "isnotempty" || $op == "!isempty")
		{
			$orig = $input;
			
			if (trim($input) == "")
			{
				$input = "false";
			}
			else
			{
				$input = "true";
			}
			/*			
			if ($orig != "")
			{
				error_log("shortcode condition $op [$orig] becomes [$input]");			
			}
			*/
		}
		else if ($op == "isempty")
		{
			if (trim($input) == "")
			{
				$input = "true";
			}
			else
			{
				$input = "false";
			}
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
		else if ($op == "contains")
		{
			$needle = $attributes["containsneedle"];
			$ignorecase = $attributes["ignorecase"] === "true";
			
			if (nxs_stringcontains_v2($input, $needle, $ignorecase))
			{
				$input = "true";
			}
			else
			{
				$input = "false";
			}
			
			if ($_REQUEST["scdebug"] == "true")
			{
				echo "shortcode condition [$input][$needle] evaluates to [$input]";
				die();
			}
		}
		else if ($op == "!contains")
		{
			$needle = $attributes["containsneedle"];
			$ignorecase = $attributes["ignorecase"] === "true";
			
			if (nxs_stringcontains_v2($input, $needle, $ignorecase))
			{
				$input = "false";
			}
			else
			{
				$input = "true";
			}
		}
		else if ($op == "equals")
		{
			$equalsvalue = $attributes["equalsvalue"];
			
			if ($input == $equalsvalue)
			{
				$input = "true";
			}
			else
			{
				//echo "shortcode condition [$input][$equalsvalue] evaluates to [$input]";
				// error_log("shortcode condition [$input][$equalsvalue] evaluates to [$input]");
		
				$input = "false";
			}

		}
		else if ($op == "!equals" || $op == "notequals")
		{
			$equalsvalue = $attributes["equalsvalue"];
			$orig = $input;
			if ($input == $equalsvalue)
			{
				$input = "false";
			}
			else
			{
				$input = "true";
			}
			// error_log("shortcode condition [$op][$orig][$equalsvalue] evaluates to [$input]");
		}
		else if ($op == "httpok")
		{
			$webmethodoverrideresult = $attributes["webmethodoverrideresult"];
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
				$url = $attributes["url"];
				if ($url == "")
				{
					// error_log("url httpok check for; $url; no url specified?");
					return "false";
				}
				
				$isactualretrievalrequired = true;
				
				$cache = $attributes["cache"];
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
				$input = "err";
			}
		}
		else if ($op == "is_user_logged_in")
		{
			if (is_user_logged_in())
			{
				$input = "true";
			}
			else
			{
				$input = "false";
			}
		}
		else if ($op == "modelexists")
		{
			$modeluri = $attributes["modeluri"];
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
		else if ($op == "in_array")
		{
			$array = $attributes["array"];
			$pieces = explode(";", $array);
			if (in_array($value, $pieces))
			{
				$input = "true";
			}
			else
			{
				$input = "false";
			}
		}
		else if ($op == "!in_array")
		{
			$array = $attributes["array"];
			$pieces = explode(";", $array);
			if (in_array($value, $pieces))
			{
				$input = "false";
			}
			else
			{
				$input = "true";
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
		var_dump($attributes);
		var_dump($content);
		var_dump($output);
		echo "----<br />";
	}
	*/
		
	return $output;
}
add_shortcode('nxsbool', 'nxs_sc_bool');

function nxs_sc_command($attributes, $content = null, $name='') 
{
	extract($attributes);
	
	if (isset($sc_scope))
	{
		$scoperesult = nxs_sc_handlescope($attributes, $content, $name);
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
		$content = $attributes["input"];
	}
	if ($content == "")
	{
		$content = $attributes["value"];
	}
	
	$input = $content;
	
	$ops = $attributes["ops"];
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
function nxs_sc_spin($attributes, $content = null, $name='') 
{
	extract($attributes);
	
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
		foreach ($attributes as $key=>$val)
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
	
		// use the attributes passed in to this shortcode as a lookup table
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

function nxs_sc_googlemap($attributes, $content = null, $name='') 
{
	extract($attributes);
	
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

// layout specific shortcodes

function nxs_nxspagerow($rowattributes, $content = null, $name='') 
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

	if ($nxs_global_current_nxsposttype_being_rendered == null)
	{
		nxs_webmethod_return_nack("nxs_global_current_nxsposttype_being_rendered is NIET gezet");
	}

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
			$cssclass .= " nxs-row-enabled-false"; 
			
			if (is_user_logged_in())
			{
				// do show it (otherwise we wont be able to edit it)
				// but tag it so we can visualize it in a spacial way
				$should_render_row = true;
				$cssclass .= " nxs-hidewheneditorinactive ";
			}
			else
			{
				$should_render_row = false;
			}
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
	
	if (isset($grs_upgradetofullwidth) && $grs_upgradetofullwidth) 
	{
		
		$output .= "<div class='nxs-row {$cssclass} {$cssclassrowtemplate}' {$rowidattribute}>";
		$output .= "<div class='nxs-row-container nxs-row2'>";
		$output .= "<div class='nxs-fullwidth nxs-containsimmediatehovermenu " . $additionalrowclasses . " '>";
	}
	else
	{
		$output .= "<div class='nxs-row {$cssclass} {$cssclassrowtemplate} " . $additionalrowclasses . " ' {$rowidattribute}>";
		$output .= "<div class='nxs-row-container nxs-containsimmediatehovermenu nxs-row1'>";
	}
	
	if ($nxs_global_current_render_mode == "default")
	{
		if (nxs_has_adminpermissions()) 
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
	      		
	      		/*
	      		$output .= '<style>';
	      		$output .= '.character:hover { fill: #E9F1F9 !important; }';
	      		$output .= '</style>';
	      		*/
	      		
	      		/*
	      		$output .= '<div class="conditional-indicator" style="position: absolute; top: -10px; left: 40px;">';
	      		$output .= '<a onclick="nxs_js_alert(\'This indicates the row is enabled or disabled based upon a condition\'); return false;">';
	      		$output .= '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 100 100">';
	      		$output .= '<g transform="translate(50,50)">';
    				$output .= '<circle style="fill:'.$circle_color.'; stroke-width: 0px;" r="50" stroke="black" class="character" />';
    				$output .= '<text text-anchor="middle" dominant-baseline="central" class="" style="fill: '.$text_color.'; stroke-width: 0px; font-size: 50px;">C</text>';
  					$output .= '</g>';
						$output .= '</svg>';
						$output .= '</a>';
						$output .= '</div>';
						*/
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
add_shortcode('nxspagerow', 'nxs_nxspagerow');

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
	foreach ($data_atts as $key => $val)
	{
		$phdataattributeshtml .= "data-{$key}='{$val}' ";
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
		
		$output .= "<li class='nxs-placeholder nxs-containshovermenu1 nxs-runtime-autocellsize " . $concatenated_css . "' {$phdataattributeshtml}>";
		$output .= $menutopleft;	// will be empty if not allowed, or not needed
		$output .= $menutopright;	// will be empty if not allowed, or not needed
		$output .= $placeholdercursors;	// will be empty if not allowed, or not needed
		
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
		
		/*
		if (nxs_has_adminpermissions() && $_REQUEST["debugmeta"] == "true")
		{
			$output .= "<div class='nxs-debug-meta'>";
			$output .= "<ul>";
			foreach ($widgetmetadata as $currentkey => $currentval)
			{
				$output .= "<li>";
				$output .= nxs_render_html_escape_gtlt("$currentkey:$currentval");
				$output .= "</li>";
			}
			$output .= "</ul>";
			//$output .= $widgetmetadata;
			$output .= "</div>";	
		}
		*/

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

function nxs_sc_embed($attributes, $content = null, $name='') 
{
	extract($attributes);
	
	nxs_requirewidget("embed");

	$args = array
	(
		"render_behaviour" => "code",
	);
	// blend the parameters given
	$args = array_merge($args, $attributes);
	
	$renderresult = nxs_widgets_embed_render_webpart_render_htmlvisualization($args);
	return $renderresult["html"];
}
add_shortcode('nxsembed', 'nxs_sc_embed');