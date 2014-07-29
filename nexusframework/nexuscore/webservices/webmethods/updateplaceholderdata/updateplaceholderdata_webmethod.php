<?php
function nxs_webmethod_updateplaceholderdata() 
{
	// TODO: this function should eventually be removed in favor of updategenericpopupdata_webmethod,
	// before removing, ensure "updateplaceholderdata" is not used in the framework, nor plugins
	// "old" plugins, however, don't use "options", and thus cannot be handled in the generic new approach...
	extract($_REQUEST);
 	
 	if ($postid == "") { nxs_webmethod_return_nack("postid empty"); }
 	if ($placeholdertemplate == "") { nxs_webmethod_return_nack("placeholdertemplate empty"); }
 	if ($placeholderid == "") { nxs_webmethod_return_nack("placeholderid empty"); }
 	
 	// inject widget if not already loaded, implements *dsfvjhgsdfkjh*
 	nxs_requirewidget($placeholdertemplate);
		
	$functionnametoinvoke = 'nxs_widgets_' . $placeholdertemplate . '_updateplaceholderdata';
	if (function_exists($functionnametoinvoke))
	{
		$result = call_user_func($functionnametoinvoke, $_REQUEST);
		
		// als de placeholder is bijgewerkt, dan impliceert dit dat de 
		// content op de pagina is aangepast. Het kan zou zijn dat 
		// er hierdoor ook meer moet worden aangepast. Bijvoorbeeld:
		// als een gebruiker de url van een menu item aanpast,
		// dan moet het menu worden bijgewerkt.
		nxs_after_postcontents_updated($postid);
		
		if (!array_key_exists("result", $result))
		{
			nxs_webmethod_return_nack("In het resultaat zit geen 'result' key;");
		}
		
		nxs_webmethod_return_ok($result);
	}
	else
	{
		nxs_webmethod_return_nack("functie niet gevonden;" . $functionnametoinvoke);
	}
}
?>