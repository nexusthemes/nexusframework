<?php

//
// wordt aangeroepen bij het opslaan van data
//
function nxs_ws_site_updatesitedata($args)
{
	extract($args);
	
	if ($updatesectionid == "dashboarduser")
	{
		require_once( ABSPATH . WPINC . '/registration.php');
		
		$current_user = wp_get_current_user();
    
    
		$user_id = $current_user->ID;
		$user_email_old = $current_user->user_email;
		
		$userdatatoupdate = array
		(
			'ID' => $user_id, 
			'user_email' => $nxs_user_email,
		);
		
		$response = wp_update_user($userdatatoupdate);
		
		if ($response == false)
		{
			nxs_webmethod_return_nack("not updated");
		}
	}	
	else if ($updatesectionid == "dashboardfavicon")
	{
		$modifiedmetadata["faviconid"] = $faviconid;

		nxs_mergesitemeta($modifiedmetadata);
	}
	else if ($updatesectionid == "menuvormgevingkleuren")
	{		
		//
		//
		//
		
		$colortypes = nxs_getcolorsinpalette();
		foreach($colortypes as $currentcolortype)
		{
			$subtypes = array("1", "2");
			foreach($subtypes as $currentsubtype)
			{
				$identification = $currentcolortype . $currentsubtype;
				
				$variable = "vg_color_" . $identification . "_m";
				$modifiedmetadata[$variable] = $$variable;
			}
		}
		
		nxs_mergesitemeta($modifiedmetadata);
	}
	else if ($updatesectionid == "menuvormgevinglettertypen")
	{
		$fontidentifiers = nxs_font_getfontidentifiers();
		foreach ($fontidentifiers as $currentfontidentifier)
		{
			$variablename = "vg_fontfam_" . $currentfontidentifier;
			$variablevalue = $$variablename;
			$modifiedmetadata["vg_fontfam_" . $currentfontidentifier] = $variablevalue;
		}
		
		nxs_mergesitemeta($modifiedmetadata);
	}
	else if ($updatesectionid == "menuvormgevingmanualcss")
	{
		$modifiedmetadata["vg_manualcss"] = $vg_manualcss;
			
		nxs_mergesitemeta($modifiedmetadata);
	}
	else if ($updatesectionid == "menuvormgevinginjecthead")
	{
		$modifiedmetadata["vg_injecthead"] = $vg_injecthead;
		nxs_mergesitemeta($modifiedmetadata);
	}
	else
	{
		nxs_webmethod_return_nack("Unsupported updatesectionid;" . $updatesectionid);
	}
	
	$result = array();
	$result["result"] = "OK";
	
	return $result;
}

function nxs_site_loginhome_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div id="login">
			<?php do_action('nxs_authentication_popup_top'); ?>
			<div class="block">
       	<?php nxs_render_popup_header(nxs_l18n__("Log in[nxs:popup]", "nxs_td")); ?>
				<div class="nxs-popup-content-canvas-cropper">
					<div class="nxs-popup-content-canvas">
		        <div class="content2">
	            <div class="box">
	                <div class="box-title"><h4><?php nxs_l18n_e("Username[nxs:popup,button]", "nxs_td"); ?></h4></div>
	                <div class="box-content"><input id='gebruikersnaam' name='gebruikersnaam' type="textarea" class="nxs_defaultenter"></div>
	            </div>
	            <div class="nxs-clear margin"></div>
		        </div> <!--END content-->
		        <div class="content2">
		            <div class="box">
		                <div class="box-title"><h4><?php nxs_l18n_e("Password[nxs:popup,button]", "nxs_td"); ?></h4></div>
		                <div class="box-content"><input id='wachtwoord' name='wachtwoord' type="password" class="nxs_defaultenter"></div>
		            </div>
		            <div class="nxs-clear margin"></div>
		        </div> <!--END content-->
		      </div>
		    </div>
        <div class="content2">
          <div class="box">
            <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='login_js(); return false;'><?php nxs_l18n_e("Login[nxs:popup,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
            <a id='nxs_popup_forgot' href='<?php echo wp_lostpassword_url(); ?>' class="nxs-float-right"><?php nxs_l18n_e("Lost your password?", "nxs_td"); ?></a>
          </div>
          <div class="nxs-clear"></div>
        </div> <!--END content-->
			</div> <!--END block-->
		</div> <!--END wrap-->
	</div>
	
	<script>

		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function nxs_js_popup_get_minwidth()
		{
			return 380;
		}

		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#gebruikersnaam').focus();
		}
		
		function login_js()
		{
			if (jQuery("#gebruikersnaam").val() == "")
			{
				nxs_js_popup_negativebounce('<?php nxs_l18n_e("Enter a username first[nxs:negativebounce]", "nxs_td"); ?>');
				//
				jQuery('#gebruikersnaam').focus();
				
				return;
			}
			if (jQuery("#wachtwoord").val() == "")
			{
				nxs_js_popup_negativebounce('<?php nxs_l18n_e("Enter a password first[nxs:negativebounce]", "nxs_td"); ?>');
				//
				jQuery('#wachtwoord').focus();

				return;
			}
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "login",
						"gebruikersnaam": jQuery("#gebruikersnaam").val(),
						"wachtwoord": jQuery('#wachtwoord').val()
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							if (response.logonsuccesful)
							{
								// close the pop up
								nxs_js_closepopup_unconditionally();
								
								nxs_js_refreshcurrentpage();
							}
							else
							{
								nxs_js_popup_negativebounce(response.message);
								//
								jQuery('#wachtwoord').focus();
							}
						}
						else
						{
							nxs_js_popup_notifyservererror();
							nxs_js_log(response);
						}
					},
					error: function(response)
					{
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
					}										
				}
			);
		}
		
		// overriden
		function nxs_js_showwarning_when_trying_to_close_dirty_popup()
		{
			return false;
		}
		
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_nieuwfooterhome_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("New footer[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		      
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
		                <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<input type='text' placeholder='<?php nxs_l18n_e("Title of the new footer[nxs:placeholder]", "nxs_td"); ?>' id='pagetitle' value='' class="nxs_defaultenter" />
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		    </div>
		  </div>
      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='toevoegen(); return false;'><?php nxs_l18n_e("Add[nxs:popup,button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear">
        </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function toevoegen()
		{
			var titel = jQuery("#pagetitle").val();
			var slug = jQuery("#pagetitle").val();
			
			nxs_js_addnewarticle(titel, slug, 'footer');
		}

		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#pagetitle').focus();
		}
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_nieuwheaderhome_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("New header[nxs:popup]", "nxs_td")); ?>
      
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		 
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
		                <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<input type='text' placeholder='<?php nxs_l18n_e("Title of the new header[nxs:placeholder]", "nxs_td"); ?>'' id='pagetitle' value='' class="nxs_defaultenter" />
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		    </div>
		  </div>
		      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savepopupdata(); return false;'><?php nxs_l18n_e("Add[nxs:popup,button]", "nxs_td"); ?></a>
        	<a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear">
        </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script>

		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function nxs_js_savepopupdata()
		{
			var titel = jQuery("#pagetitle").val();
			var slug = jQuery("#pagetitle").val();
			nxs_js_addnewarticle(titel, slug, 'header');
		}		
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#pagetitle').focus();
		}
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_nieuwsubheaderhome_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("New subheader[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">

					<div class="content2">
		        <div class="box">
		          <div class="box-title">
		              <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
		           </div>
		          <div class="box-content">
		          	<input type='text' placeholder='<?php nxs_l18n_e("Title of the new subheader[nxs:placeholder]", "nxs_td"); ?>'' id='pagetitle' value='' class="nxs_defaultenter" />
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		    </div>
		  </div>
      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savepopupdata(); return false;'><?php nxs_l18n_e("Add[nxs:popup,button]", "nxs_td"); ?></a>
        	<a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear"></div>
      </div> <!--END content-->
      <div class="nxs-clear"></div>
		</div>
		<div class="nxs-clear"></div>
	</div>
	
	<script>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}

		function nxs_js_savepopupdata()
		{
			var titel = jQuery("#pagetitle").val();
			var slug = jQuery("#pagetitle").val();
			nxs_js_addnewarticle(titel, slug, 'subheader');
		}		
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#pagetitle').focus();
		}
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_nieuwsubfooterhome_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("New subfooter[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">

					<div class="content2">
		        <div class="box">
		          <div class="box-title">
		              <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
		           </div>
		          <div class="box-content">
		          	<input type='text' placeholder='<?php nxs_l18n_e("Title of the new subfooter[nxs:placeholder]", "nxs_td"); ?>'' id='pagetitle' value='' class="nxs_defaultenter" />
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		    </div>
		  </div>
      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savepopupdata(); return false;'><?php nxs_l18n_e("Add[nxs:popup,button]", "nxs_td"); ?></a>
        	<a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear"></div>
      </div> <!--END content-->
      <div class="nxs-clear"></div>
		</div>
		<div class="nxs-clear"></div>
	</div>
	
	<script>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}

		function nxs_js_savepopupdata()
		{
			var titel = jQuery("#pagetitle").val();
			var slug = jQuery("#pagetitle").val();
			nxs_js_addnewarticle(titel, slug, 'subfooter');
		}		
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#pagetitle').focus();
		}
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_newcontentparthome_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("New contentpart", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">

					<div class="content2">
		        <div class="box">
		          <div class="box-title">
		              <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
		           </div>
		          <div class="box-content">
		          	<input type='text' placeholder='<?php nxs_l18n_e("Title", "nxs_td"); ?>'' id='pagetitle' value='' class="nxs_defaultenter" />
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		    </div>
		  </div>
      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savepopupdata(); return false;'><?php nxs_l18n_e("Add[nxs:popup,button]", "nxs_td"); ?></a>
        	<a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear"></div>
      </div> <!--END content-->
      <div class="nxs-clear"></div>
		</div>
		<div class="nxs-clear"></div>
	</div>
	
	<script>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}

		function nxs_js_savepopupdata()
		{
			var titel = jQuery("#pagetitle").val();
			var slug = jQuery("#pagetitle").val();
			nxs_js_addnewarticle_v2(titel, slug, 'templatepart', 'content');
		}		
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#pagetitle').focus();
		}
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_newpagedecoratorhome_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("New pagedecorator[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">

					<div class="content2">
		        <div class="box">
		          <div class="box-title">
		              <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
		           </div>
		          <div class="box-content">
		          	<input type='text' placeholder='<?php nxs_l18n_e("Title of the new pagedecorator[nxs:placeholder]", "nxs_td"); ?>'' id='pagetitle' value='' class="nxs_defaultenter" />
		          </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		    </div>
		  </div>
      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savepopupdata(); return false;'><?php nxs_l18n_e("Add[nxs:popup,button]", "nxs_td"); ?></a>
        	<a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear"></div>
      </div> <!--END content-->
      <div class="nxs-clear"></div>
		</div>
		<div class="nxs-clear"></div>
	</div>
	
	<script>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}

		function nxs_js_savepopupdata()
		{
			var titel = jQuery("#pagetitle").val();
			var slug = jQuery("#pagetitle").val();
			nxs_js_addnewarticle_v2(titel, slug, 'genericlist', 'pagedecorator');
		}		
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#pagetitle').focus();
		}
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_nieuwsidebarhome_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("New sidebar[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
		                <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<input type='text' placeholder='<?php nxs_l18n_e("Title of the new sidebar[nxs:placeholder]", "nxs_td"); ?>'' id='pagetitle' value='' class="nxs_defaultenter" />
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		    
		  	</div>
		  </div>
      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savepopupdata(); return false;'><?php nxs_l18n_e("Add[nxs:popup,button]", "nxs_td"); ?></a>
        	<a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear">
        </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}

		function nxs_js_savepopupdata()
		{
			var titel = jQuery("#pagetitle").val();
			var slug = jQuery("#pagetitle").val();
			nxs_js_addnewarticle(titel, slug, 'sidebar');
		}		
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#pagetitle').focus();
		}
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_nieuwmenuhome_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("New menu[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
		                <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<input type='text' placeholder='<?php nxs_l18n_e("Title of the new menu[nxs:placeholder]", "nxs_td"); ?>'' id='pagetitle' value='' class="nxs_defaultenter" />
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		    
		  	</div>
		  </div>
      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savepopupdata(); return false;'><?php nxs_l18n_e("Add[nxs:popup,button]", "nxs_td"); ?></a>
        	<a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear">
        </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}

		function nxs_js_savepopupdata()
		{
			var titel = jQuery("#pagetitle").val();
			var slug = jQuery("#pagetitle").val();
			nxs_js_addnewarticle(titel, slug, 'menu');
		}		
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#pagetitle').focus();
		}
		
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_nieuwlisthome_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("New list[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
		                <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<input type='text' placeholder='<?php nxs_l18n_e("Title of the new list[nxs:placeholder]", "nxs_td"); ?>'' id='pagetitle' value='' class="nxs_defaultenter" />
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		    
		  	</div>
		  </div>
      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savepopupdata(); return false;'><?php nxs_l18n_e("Add[nxs:popup,button]", "nxs_td"); ?></a>
        	<a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear">
        </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}

		function nxs_js_savepopupdata()
		{
			var titel = jQuery("#pagetitle").val();
			var slug = jQuery("#pagetitle").val();
			nxs_js_addnewarticle(titel, slug, 'list');
		}		
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#pagetitle').focus();
		}
		
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_nieuwslidesethome_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
     	<?php nxs_render_popup_header(nxs_l18n__("New slideset[nxs:popup]", "nxs_td")); ?>
      
			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		      
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
		                <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<input type='text' placeholder='<?php nxs_l18n_e("Title of the new slideset[nxs:placeholder]", "nxs_td"); ?>'' id='pagetitle' value='' class="nxs_defaultenter" />
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->
		      
		    </div>
		  </div>
      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savepopupdata(); return false;'><?php nxs_l18n_e("Add[nxs:popup,button]", "nxs_td"); ?></a>
        	<a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear">
        </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
	
		function nxs_js_savepopupdata()
		{
			var titel = jQuery("#pagetitle").val();
			var slug = jQuery("#pagetitle").val();
			nxs_js_addnewarticle(titel, slug, 'slideset');
		}		
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#pagetitle').focus();
		}
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_dashboarduserhome_rendersheet($args)
{
	//
	extract($args);
	
	$current_user = wp_get_current_user();
  $nxs_user_email = $current_user->user_email;
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	
      
    	<?php nxs_render_popup_header(nxs_l18n__("User settings[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		      
		      <!-- email -->
					<div class="content2">
		        <div class="box">
		            <div class="box-title">
		                <h4><?php nxs_l18n_e("Email[nxs:popup,label]", "nxs_td"); ?></h4>
		             </div>
		            <div class="box-content">
		            	<input id='nxs_user_email' placeholder='<?php nxs_l18n_e("name@example.org[nxs:placeholder]", "nxs_td"); ?>'' name='nxs_user_email' type='text' value='<?php echo $nxs_user_email;?>' />
		              <span class="nxs-title"><?php nxs_l18n_e("Email help[nxs:tip]", "nxs_td"); ?></span>
		            </div>
		        </div>
		        <div class="nxs-clear"></div>
		      </div> <!--END content-->

		    </div>
		  </div>
      
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savepopupdata(); return false;'><?php nxs_l18n_e("Save[nxs:popup,button]", "nxs_td"); ?></a>
        	<a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear">
        </div>
      </div> <!--END content-->
		</div>
	</div>
	
	<script>
		function nxs_js_savepopupdata()
		{
			var valuestobeupdated = {};
			valuestobeupdated["nxs_user_email"] = jQuery('#nxs_user_email').val();
			
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatesitedata",
						"updatesectionid": "dashboarduser",
						"data": nxs_js_getescapeddictionary(valuestobeupdated)
					},
					async: false,
					cache: false,					
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// close the pop up
							nxs_js_closepopup_unconditionally();
							
							// refresh current page 
							nxs_js_refreshcurrentpage();
						}
						else
						{
							nxs_js_popup_notifyservererror();
							nxs_js_log(response);
						}
					},
					error: function(response)
					{
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
					}										
				}
			);
		}		
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_dashboardfaviconhome_rendersheet($args)
{
	//
	//
	//
	extract($args);
	
	//
	// clientpopupsessiondata bevat key values van de client side
	// deze overschrijft met opzet (tijdelijk) mogelijk waarden die via $args
	// zijn meegegeven; hierdoor kan namelijk een 'gevoel' worden gecreeerd
	// van een 'state' die client side leeft, die helpt om meerdere (popup) 
	// pagina's state te laten delen. De inhoud van clientpopupsessiondata is een
	// array die wordt gevoed door de clientside variabele "popupsessiondata",
	// die gedefinieerd is in de file 'frontendediting.php'
	//
	extract($clientpopupsessiondata);	
	extract($clientshortscopedata);
	
	$fileuploadurl = admin_url( 'admin-ajax.php');
		
	$result = array();
	$result["result"] = "OK";
	
	if ($medialist_pagenr == "")
	{
		$medialist_pagenr = 1;
	}
	
	$itemsperpage = 8;
	$firstrownrtoshow = $itemsperpage*($medialist_pagenr - 1);
		
	$args = array
	(
		'numberposts' => -1,	//$itemsperpage,
		'offset' => 0,
		'post_type' => 'attachment',
		'post_mime_type' => array('image/x-icon',	'image/vnd.microsoft.icon'), // only fetch ico images
		'post_parent' => null, // no parent
	);
	
	//
	$images = get_posts($args);

	$totalrows = count($images);
	$totalpages = (int) ceil($totalrows / $itemsperpage);

	if ($medialist_pagenr < 1 || $medialist_pagenr > $totalpages) 
	{
		// out of bounds
		$medialist_pagenr = 1;
	}

	nxs_ob_start();
	?>

  <div class="nxs-admin-wrap">
    <div class="block">
    	
      <?php nxs_render_popup_header(nxs_l18n__("FavIcon[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
		      
		      <div class="content2">
		      
		          <form id='nxsuploadform' action="<?php echo $fileuploadurl;?>" method="post" enctype="multipart/form-data">
		              <input type="file" name="file" id="file" class="nxs-float-left" onchange="storefile();" />
		              
		              <!-- <a class="button" href="#" onclick="storefile(); return false;"><?php nxs_l18n_e("Save[nxs:popup,button]", "nxs_td"); ?></a> -->
		          </form>		
		
		          <?php if ($totalpages > 1) { ?>
		          <div class="nxs-pagination nxs-float-right">
		              <span class="">
		              		<?php if ($medialist_pagenr > 1) { ?>
		                  <a class="current" href="#" onclick="nxs_js_setpagenr('1'); return false;">&lt;&lt;</a>
		                  <a class="current" href="#" onclick="nxs_js_setpagenr('<?php echo $medialist_pagenr - 1; ?>'); return false;">&lt;</a>
		                	<?php } ?>
		                  <span class="">
		                      <input id="pagechanger" type="text" value="<?php echo $medialist_pagenr;?>" size="3" class="small2"> van <?php echo $totalpages; ?>
		                  </span>
		                  <?php if ($medialist_pagenr < $totalpages) { ?>
		                  <a class="current" href="#" onclick="nxs_js_setpagenr('<?php echo $medialist_pagenr + 1; ?>'); return false;">&gt;</a>
		                  <a class="current" href="#" onclick="nxs_js_setpagenr('<?php echo $totalpages; ?>'); return false;">&gt;&gt;</a>
		                  <?php } ?>
		              </span>
		          </div>
		        <?php } ?>
		        
		          <div class="nxs-clear padding"></div>
		          
		          <table>
		              <thead>
		                  <tr>
		                      <th class="file">
		                          <span><?php nxs_l18n_e("Image[nxs:column,heading]", "nxs_td"); ?></span>
		                          <span class="sorting-indicator"></span>
		                      </th>
		                      <th></th>
		                  </tr>
		              </thead>
		              <tfoot>
		                  <tr>
		                      <th class="file">
		                          <span><?php nxs_l18n_e("Image[nxs:column,heading]", "nxs_td"); ?></span>
		                          <span class="sorting-indicator"></span>
		                      </th>
		                      <th></th>
		                  </tr>
		              </tfoot>
		             
		              <tbody>
		                  <?php 
		                  if ($totalrows == 0)
		                  {
		                  	?>
		                  	<tr>
			                    <td class="file">
			                    	<?php nxs_l18n_e("No files found with mime type 'image/x-icon'[nxs:column,heading]", "nxs_td"); ?>
			                   	</td>
			                  </tr>
			                 	<?php
		                  }
		                  else
		                  {
			                  foreach($images as $currentimage) 
			                  {
			                      $rownr = $rownr + 1;
			                      if ($rownr < $firstrownrtoshow)
			                      {
			                          // continue looping
			                          continue;
			                      }
			                      $visiblenr = $visiblenr + 1;
			                      if ($visiblenr > $itemsperpage)
			                      {
			                          // break the loop
			                          break;
			                      }		
			                      
			                      // wp_get_attachment_url($attachmentID);
			                      $imglookup = nxs_wp_get_attachment_image_src($currentimage->ID, 'thumbnail', true);
			                      $url = $imglookup[0];
			                      $url = nxs_img_getimageurlthemeversion($url);
			                      $rowclass = "";
			                      if ($rownr % 2 == 0)
			                      {
			                          $rowclass = "class='alt'";
			                      }
			                      ?>
			                      
			                      
			                      <tr <?php echo $rowclass; ?>>
			                          <td class="file">
			                              <a href='#' onclick='nxs_js_selectattachment("<?php echo $currentimage->ID; ?>"); return false;'>
			                                  <img src='<?php echo $url;?>' class="nxs-preview-thumbnail icon" />
			                              </a>
			                          </td>
			                          <td>
			                              <p><?php echo $currentimage->post_title; ?></p>
			                              <p><?php echo $currentimage->post_mime_type; ?></p>
			                          </td>
			                      </tr>
			                      
			                      <?php
			                  }
			                }
			                ?>
			                <tr>
			                	<td>
			                		<a href='#' class='nxsbutton1' onclick="nxs_js_selectattachment(''); nxs_js_savegenericpopup(); return false;">None</a>
			                	</td>
			                </tr>
			                <?php
		                  ?>
		              </tbody>
		          </table>
		          
		      </div> <!--END content-->
		     
		    </div>
		  </div>
		      		      
      <div class="content2">
        <div class="box">
        	
		      <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save[nxs:popup,button]", "nxs_td"); ?></a>
		    	<a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
		      <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
       	<div class="nxs-clear margin"></div>
      </div> <!--END content-->
    </div> <!--END block-->
  	
  </div>

  <script>
  	
  	function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
  	
		function setupfiletransfer()
		{
			//alert("setting up...");
			var filename = jQuery('#file').val().split(/\\|\//).pop();
			var options = 
      { 
        data:
        {
            action: "nxs_ajax_webmethods",
            webmethod: "savefileupload",
            uploadtitel: filename
        },
        dataType: 'json',
        iframe: true,
        success: processResponse,
    	};
        
			jQuery('#nxsuploadform').ajaxForm(options);
		}
	
    function storefile()
    {                    
    	nxs_js_log('storing file 1a');
    	
      // 
      // setup form to support ajax submission (file transfer using html5 features)
      //
      setupfiletransfer();
      
      nxs_js_log('storing file 1b');

			if (!verifyFileSelected())
      {
      	nxs_js_log('storing file 1c');
        return;
      }
      
      nxs_js_log('storing file 1d');
      
      // submit form
      jQuery("#nxsuploadform").submit(); 
  	}
  	
  	function endsWith(str, suffix) {
    	return str.indexOf(suffix, str.length - suffix.length) !== -1;
		}
    
    function verifyFileSelected()
    {
	    var f = document.getElementById("file");
	    if (f.value == "")
	    {
	        nxs_js_alert("<?php nxs_l18n_e("First select a file[nxs:growl]","nxs_td"); ?>");
	        return false;
	    }
	    else
      {
        return true;
      }
    }

    function processResponse(data, statusText, xhr, $form)  
    {
      if (data.result == "OK")
      {
          // file upload was succesful
          
          nxs_js_popup_setsessiondata("imageid", data.imageid);
          nxs_js_popup_sessiondata_make_dirty();
          
          // toon eerste scherm in de popup
					nxs_js_popup_navigateto("dashboardfaviconhome");
      }
      else
      {
      	nxs_js_alert("<?php nxs_l18n_e("Upload failed (hints: check upload (filesize) restrictions, available diskspace and file permissions)[nxs:growl]","nxs_td"); ?>");
        //Er is een fout opgetreden bij het uploaden van het document (wellicht is het bestand te groot, is er onvoldoende ruimte, of is er een rechten probleem?");
        nxs_js_log("error output:");
        nxs_js_log(data);
      }
    }
    
    function nxs_js_setpagenr(pagenr)
    {
    	nxs_js_popup_setsessiondata("medialist_pagenr", pagenr);
    	nxs_js_popup_refresh();
  	}
  	
  	function nxs_js_selectattachment(attachmentid)
		{
			nxs_js_popup_setsessiondata("imageid", attachmentid);
			nxs_js_popup_sessiondata_make_dirty();

			// toon eerste scherm in de popup
			nxs_js_popup_navigateto("dashboardfaviconhome");
		}
  	
  	//
  	// 
  	//
  	function nxs_js_overrule_topmargin()
  	{
  		return 40;
  	}
  	
  	jQuery("#pagechanger").unbind("keyup.defaultenter");
		jQuery("#pagechanger").bind("keyup.defaultenter", function(e)
		{
			if (e.keyCode == 13)
			{
				var nieuwepagenr = parseInt(jQuery("#pagechanger").val());
				if (isNaN(nieuwepagenr))
				{
					//ignore
				}
				else
				{
					nxs_js_setpagenr(nieuwepagenr);
				}
			}
		});
		
		function nxs_js_savegenericpopup()
		{
			var valuestobeupdated = {};
			valuestobeupdated["faviconid"] = nxs_js_popup_getsessiondata("imageid");

			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: 
					{
						"action": "nxs_ajax_webmethods",
						"webmethod": "updatesitedata",
						"updatesectionid": "dashboardfavicon",
						"data": nxs_js_getescapeddictionary(valuestobeupdated)
					},
					async: false,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							// close the pop up
							nxs_js_closepopup_unconditionally();
							
							// refresh current page (if the footer is updated we could decide to
							// update only the footer, but this is needless; an update of the page is ok too)
							nxs_js_refreshcurrentpage();
						}
						else
						{
							nxs_js_popup_notifyservererror();
							nxs_js_log(response);
						}
					},
					error: function(response)
					{
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
					}										
				}
			);
		}
    	
  </script>    
  
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_logouthome_rendersheet($args)
{
	//
	extract($args);
	
	//
		
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	nxs_ob_start();

	?>
	
	<div class="nxs-admin-wrap">
		<?php do_action('nxs_authentication_popup_top'); ?>
		<div class="block">	
			
      <?php nxs_render_popup_header(nxs_l18n__("Logout?[nxs:popup]", "nxs_td")); ?>      

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					<!-- empty :) -->
				</div>
			</div>
    	
    	<div class="content2">
  		  <div class="box">
  		  	<div class="box-title">
  		  		&nbsp;
  		  	</div>
          <div class="box-content">
					  <a class="nxsbutton nxs-float-right" href="#" title="<?php nxs_l18n_e("Log out[nxs:popup,button,tooltip]", "nxs_td"); ?>" onclick="nxs_js_logout(); return false;" class="site small-switch"><?php nxs_l18n_e("Log out[nxs:popup,button]", "nxs_td"); ?></a>
						<a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
					</div>
				</div>
				<div class="nxs-clear"></div>
		</div>
	</div>
	
	<script>
			
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function nxs_js_popup_get_minwidth()
		{
			return 380;
		}
			
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_mediamanagerupload_rendersheet($args)
{
	//
	//
	//
	extract($args);
	
	//
	// clientpopupsessiondata bevat key values van de client side
	// deze overschrijft met opzet (tijdelijk) mogelijk waarden die via $args
	// zijn meegegeven; hierdoor kan namelijk een 'gevoel' worden gecreeerd
	// van een 'state' die client side leeft, die helpt om meerdere (popup) 
	// pagina's state te laten delen. De inhoud van clientpopupsessiondata is een
	// array die wordt gevoed door de clientside variabele "popupsessiondata",
	// die gedefinieerd is in de file 'frontendediting.php'
	//
	extract($clientpopupsessiondata);	
	extract($clientshortscopedata);
	
	$fileuploadurl = admin_url( 'admin-ajax.php');
		
	$result = array();
	$result["result"] = "OK";
	
	if ($medialist_pagenr == "")
	{
		$medialist_pagenr = 1;
	}
	
	nxs_ob_start();
	?>

  <div class="nxs-admin-wrap">
    <div class="block">
      <form id='nxsuploadform' action="<?php echo $fileuploadurl;?>" method="post" enctype="multipart/form-data">

	      <?php nxs_render_popup_header(nxs_l18n__("New media item[nxs:popup]", "nxs_td")); ?>

				<div class="nxs-popup-content-canvas-cropper">
					<div class="nxs-popup-content-canvas">
			      
						<div class="content2">
			        <div class="box">
			            <div class="box-title">
			                <h4><?php nxs_l18n_e("Title[nxs:popup,label]", "nxs_td"); ?></h4>
			             </div>
			            <div class="box-content">
			            	<input id='nxs_titel' placeholder='Titel van het bestand' name='nxs_titel' type='text' value='<?php echo $nxs_titel;?>' />
			              <span class="nxs-title"><?php nxs_l18n_e("Title of the file[nxs:placeholder]", "nxs_td"); ?>'</span>
			            </div>
			        </div>
			        <div class="nxs-clear"></div>
			      </div> <!--END content-->      
			      
			      <div class="content2">
		            <input type="file" name="file" id="file" class="nxs-float-left" onchange="nxs_js_filechanged();" />
			          <div class="nxs-clear padding"></div>   
			      </div> <!--END content-->
		      </form>		
		      
		    </div>
		  </div>
 
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_savegenericpopup(); return false;'><?php nxs_l18n_e("Save[nxs:popup,button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
       	</div>
        <div class="nxs-clear margin"></div>
      </div> <!--END content-->
    </div> <!--END block-->
  	
  </div>

  <script>
  	
  	function nxs_js_filechanged()
  	{
  		nxs_js_popup_sessiondata_make_dirty();
  		if (jQuery("#nxs_titel").val() == "")
  		{
  			// set filename
  			var filename = jQuery('#file').val().split(/\\|\//).pop();
  			jQuery("#nxs_titel").val(filename);
  		}
  		else
			{
			}
  	}
  	
  	function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
  	
		function setupfiletransfer()
		{
			//alert("setting up...");
			
			var options = 
      { 
        data:
        {
            action: "nxs_ajax_webmethods",
            webmethod: "savefileupload",
            uploadtitel: jQuery("#nxs_titel").val()
        },
        dataType: 'json',
        iframe: true,
        success: processResponse,
    	};
        
			jQuery('#nxsuploadform').ajaxForm(options);
		}
	
    function storefile()
    {
    	nxs_js_log('storing file 2');         
      // 
      // setup form to support ajax submission (file transfer using html5 features)
      //
      setupfiletransfer();
      
      nxs_js_log('after setupfiletransfer2');

			if (!verifyFileSelected())
      {
          return;
      }
      
      nxs_js_log('verified, submitting...');
      
      // submit form
      jQuery("#nxsuploadform").submit(); 
  	}
  	
  	function endsWith(str, suffix) {
    	return str.indexOf(suffix, str.length - suffix.length) !== -1;
		}
    
    function verifyFileSelected()
    {
	    var f = document.getElementById("file");
	    if (f.value == "")
	    {
	      nxs_js_alert("<?php nxs_l18n_e("Select a file first[nxs:growl]","nxs_td"); ?>");
	      return false;
	    }
	    else
      {
        return true;
      }
    }

    function processResponse(data, statusText, xhr, $form)  
    {
      if (data.result == "OK")
      {
          // file upload was succesful
          
          // close the pop up
					nxs_js_closepopup_unconditionally();
					
					// refresh current page (if the footer is updated we could decide to
					// update only the footer, but this is needless; an update of the page is ok too)
					nxs_js_refreshcurrentpage();
      }
      else
      {
        nxs_js_alert("<?php nxs_l18n_e("Upload failed (hints: check upload (filesize) restrictions, available diskspace and file permissions)[nxs:growl]","nxs_td"); ?>");
      }
    }
    
    function nxs_js_setpagenr(pagenr)
    {
    	nxs_js_popup_setsessiondata("medialist_pagenr", pagenr);
    	nxs_js_popup_refresh();
  	}
  	
  	function nxs_js_selectattachment(attachmentid)
		{
			nxs_js_popup_setsessiondata("imageid", attachmentid);
			nxs_js_popup_sessiondata_make_dirty();

			// toon eerste scherm in de popup
			nxs_js_popup_navigateto("dashboardfaviconhome");
		}
  	
  	//
  	// 
  	//
  	function nxs_js_overrule_topmargin()
  	{
  		return "auto";
  	}
  	
  	jQuery("#pagechanger").unbind("keyup.defaultenter");
		jQuery("#pagechanger").bind("keyup.defaultenter", function(e)
		{
			if (e.keyCode == 13)
			{
				var nieuwepagenr = parseInt(jQuery("#pagechanger").val());
				if (isNaN(nieuwepagenr))
				{
					//ignore
				}
				else
				{
					nxs_js_setpagenr(nieuwepagenr);
				}
			}
		});
		
		function nxs_js_savegenericpopup()
		{
			storefile();
		}
    	
  </script>    
  
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_help_rendersheet($args)
{
	nxs_site_render_popup_supportoptions($args);
}

function nxs_site_newposthome_rendersheet($args)
{
	//
	extract($args);

	$meta = nxs_getsitemeta();
	
	extract($clientpopupsessiondata);
	extract($clientpopupsessioncontext);
	extract($clientshortscopedata);
	
	$result = array();
	
  $pwargs = array();
  $pwargs["invoker"] = "newinteractive";
  $postwizards = nxs_getpostwizards($pwargs);
	
	nxs_ob_start();
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("New[nxs:popup]", "nxs_td")); ?>
            
      <!--  -->
      
			<div class="nxs-popup-content-canvas-cropper" style="width: 700px;">
				<div class="nxs-popup-content-canvas">
	
					<div class="content2">
						<div class="box-title">
	            <h4><?php nxs_l18n_e("Wizard[nxs:popup,label]", "nxs_td"); ?></h4>
	         	</div>
						<div class="box-content">
	          	<select id='postwizard' onchange="nxs_js_savepopupdata(); nxs_js_popup_refresh_keep_focus(this);">
	          		<option <?php if ($postwizard=='') echo "selected='selected'"; ?> value=''><?php nxs_l18n_e("Select a wizard[nxs:popup,ddl]", "nxs_td"); ?></option>
	          		<?php
	          		foreach ($postwizards as $currentpostwizard)
	          		{
	          			$currenttitel = $currentpostwizard["titel"];
	          			$currentpostwizard = $currentpostwizard["postwizard"];
	          			?>
	          			<option <?php if ($postwizard==$currentpostwizard) echo "selected='selected'"; ?> value='<?php echo $currentpostwizard; ?>'><?php echo $currenttitel; ?></option>
	          			<?php
	          		}
	          		?>
	          	</select>
	          </div>
	        <div class="nxs-clear"></div>
	      </div> <!--END content-->
	  
	      <!-- preview -->
	      
	      <?php 
	      if ($postwizard!= "")
	      {
	      	// show the preview
	      	nxs_renderpostwizardpreview($postwizard, $args);
	    	}
	    	?>
	    
	  	</div>
	  </div>
		    	
    <div class="content2">
      <div class="box">
      	<?php
      	$behaviour = "";
      	if ($postwizard=="pdt2")
      	{
      		// default behaviour for posts is to create those in the WP backend,
      		// plugins can change that behaviour
      		$behaviour = apply_filters("nxs_postwizard_newblogbehaviour", "backend");
      		if (nxs_hassitemeta())
      		{
      			$lookup = nxs_lookuptable_getlookup();
      			if ($lookup["nxs_postwizard_newblogbehaviour"] != "")
      			{
      				$behaviour = $lookup["nxs_postwizard_newblogbehaviour"];
      			}
      		}
      		else
      		{
      			
      		}
	      }
      	if ($behaviour == "backend")
      	{
      		// a post, to be created in the backend
      		?>
      		<a id='nxs_popup_genericsavebutton' href='<?php echo admin_url('post-new.php'); ?>' class="nxsbutton nxs-float-right"><?php nxs_l18n_e("Next[nxs:popup,button]", "nxs_td"); ?></a>
      		<?php
      	}
      	else
      	{
      		?>
        	<a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='showpostwizard(); return false;'><?php nxs_l18n_e("Next[nxs:popup,button]", "nxs_td"); ?></a>
        	<?php
        }
      	?>
        <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>            
        <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
     	</div>
      <div class="nxs-clear">
      </div>
    </div> <!--END content-->
	</div>
	
	<script>
		
		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showcancel'; 
		}
		
		function nxs_js_savepopupdata()
		{
			nxs_js_popup_setsessioncontext('postwizard', jQuery('#postwizard').val());
		}
		
		function showpostwizard()
		{
			nxs_js_savepopupdata();
			
			var postwizard = nxs_js_popup_getsessioncontext('postwizard');
			
			if (postwizard == '')
			{      
				nxs_js_popup_negativebounce('<?php nxs_l18n_e("Select a wizard first[nxs:negativebounce]", "nxs_td"); ?>');

      	jQuery('#postwizard').focus();
				return;
			}
			
			// 
			nxs_js_popup_sessiondata_clear_dirty();	// don't annoy user with warnings when switching context
			nxs_js_popup_postwizard_neweditsession(postwizard, 'home');
		}
		
		function nxs_js_execute_after_popup_shows()
		{
			jQuery('#postwizard').focus();
		}
		
		// overriden
		function nxs_js_showwarning_when_trying_to_close_dirty_popup()
		{
			return false;
		}
		
	</script>
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();

	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

function nxs_site_exportsite_rendersheet($args)
{
	//
	//
	//
	extract($args);
	
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
	
	$filedownloadurl = admin_url('admin-ajax.php?action=nxs_ajax_webmethods&webmethod=exportcontent&export=siteallpoststructuresandwidgets');
	
	nxs_ob_start();

	?>

  <div class="nxs-admin-wrap">
    <div class="block">
     
     	<?php nxs_render_popup_header(nxs_l18n__("Export site post structure and widget data[nxs:popup]", "nxs_td")); ?>
      
      <div class="content2">
      	<a href='<?php echo $filedownloadurl;?>'>Download</a>
      </div> <!--END content-->
      <div class="content2">
          <div class="box">
            <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
         </div>
          <div class="nxs-clear margin"></div>
      </div> <!--END content-->
    </div> <!--END block-->
  </div>
    
  <script>
		function nxs_js_execute_after_popup_shows()
		{
			
		}
	</script>
    
	<?php
	
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

/* DASHBOARD POPUP
---------------------------------------------------------------------------------------------------- */
function nxs_site_dashboardhome_rendersheet($args)
{
	$clientshortscopedata = array(); // will/can be overriden bij extract line below
	$collectanonymousdata = "";
	$faviconurl = "";
	
	extract($args);
	
	$sitemeta = nxs_getsitemeta();

	if (isset($sitemeta["faviconid"]))
	{
		$faviconid = $sitemeta["faviconid"];
		$favicondata = get_post($faviconid);
		$faviconlookup = nxs_wp_get_attachment_image_src($faviconid, 'thumbnail', true);
		$faviconurl = $faviconlookup[0];
		$faviconurl = nxs_img_getimageurlthemeversion($faviconurl);
	}
		
	$current_user = wp_get_current_user();
  $nxs_user_email = $current_user->user_email;
  	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);

	$sitemeta = nxs_getsitemeta();
	$collectanonymousdata = $sitemeta["collectanonymousdata"];
	
	if ($toggledatacollection == "true")
	{
		if ($collectanonymousdata == "" || $collectanonymousdata == "true")
		{
			$collectanonymousdata = "false";
		}
		else
		{
			// true
			$collectanonymousdata = "";
		}
		$sitemeta["collectanonymousdata"] = $collectanonymousdata;		
		nxs_mergesitemeta($sitemeta);
	}
	
	if ($togglewidescreen == "triggered")
	{
		$currentwidescreenvalue = nxs_iswidescreen($sitewideelement);
		$newwidescreenvalue = !$currentwidescreenvalue;
		nxs_setwidescreensetting($sitewideelement, $newwidescreenvalue);
		$currentwidescreenvalue = nxs_iswidescreen($sitewideelement);
		// refresh page
		$shouldrefresh = true;
	}
	
	$result = array();
	
	$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("Site dashboard[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					
					<!-- background styling -->
					<div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Generic styling", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href='#' onclick='nxs_js_popup_navigateto("sitestyling"); return false;' class='nxsbutton1 nxs-float-right'><?php echo nxs_l18n__("Change", "nxs_td"); ?></a>
            	</div>
            </div>
            <div class="nxs-clear margin"></div>
          </div>
          
          <!-- maintenance -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Maintenance mode", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('maintenancehome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Change", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->
					
					<!-- header footer -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Header footer", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('headerfooter'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Change", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->
					
					<!-- access restrictions -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Access restrictions", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('accessrestrictionhome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Change", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->

					<!-- wp management -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("WP management", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('wpmanagementhome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Manage", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->

					<!-- marketing management -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Marketing management", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('marketingmanagementhome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Manage", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->

					<!-- integrations -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Integrations", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('integrationshome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Manage", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->
	        
	        <!-- data protection -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Data Protection (GDPR/Privacy)", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('dataprotectionhome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Manage", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->
	        
					<!-- cache management -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Cache management (performance/speed)", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('cachemanagementhome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Manage", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->

					<!-- uni styling management -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Unistyle management", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('unistylemanagementhome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Manage", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->
	        
	        <!-- uni content management -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Unicontent management", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('unicontentmanagementhome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Manage", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->
	        
	        <!-- lookup table management -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Lookup table management", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('lookuptablemanagementhome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Manage", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->
	        
	       	<!-- favicon -->
	       	<div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("FavIcon[nxs:popup,label]", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('dashboardfaviconhome'); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Change[nxs:popup,button]", "nxs_td"); ?></a>
              	<div>
              		<?php 
              		if ($faviconid == "" || $faviconid == 0)
              		{
              			?>
              			-
              			<?php
              		}
              		else
              		{
              			?>
			                <div>
			                	<p><?php echo $faviconurl; ?></p>
			               	</div>
			              	<a href="#" onclick="nxs_js_popup_site_neweditsession('dashboardfaviconhome'); return false;" class="nxs-float-left">
			              		
			                  <img src="<?php echo $faviconurl; ?>">
			                </a>
              			<?php
              		}
              		?>
	              </div>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->
	       
	        <!-- e-mail -->
	        <div class="content2">
            <div class="box">
              <div class="box-title"><h4><?php nxs_l18n_e("E-mail[nxs:popup,label]", "nxs_td"); ?></h4></div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('dashboarduserhome'); return false;" class="nxsbutton1 nxs-float-right nxs-margin-top5"><?php nxs_l18n_e("Change[nxs:popup,button]", "nxs_td"); ?></a> 
                <span class='title'><?php echo $nxs_user_email; ?></span>
                <div class="nxs-clear margin"></div>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div> <!--END content-->
	        
	        <?php
	        $widescreenfields = array();
	        $widescreenfields[] = "header";
	        $widescreenfields[] = "content";
	        $widescreenfields[] = "footer";
	        
	        foreach ($widescreenfields as $currentwidescreenfield)
	        {
	        	$iswidescreen = nxs_iswidescreen($currentwidescreenfield);
		        ?>
		        <!-- header widescreen or none widescreen -->
		        <div class="content2">
	            <div class="box">
	              <div class="box-title"><h4><?php echo $currentwidescreenfield; ?> <?php nxs_l18n_e("Widescreen[nxs:popup,label]", "nxs_td"); ?></h4>
	              </div>
	              <div class="box-content">
	              	<a href="#" onclick="nxs_js_popup_setshortscopedata('togglewidescreen', 'triggered'); nxs_js_popup_setshortscopedata('sitewideelement', '<?php echo $currentwidescreenfield; ?>'); nxs_js_popup_refresh(); return false;" class="nxsbutton1 nxs-float-right"><?php nxs_l18n_e("Change[nxs:popup,button]", "nxs_td"); ?></a>
	              	<?php if ($iswidescreen == true) { ?>
	              		<span class='title'><?php nxs_l18n_e("Active[nxs:popup,button]", "nxs_td"); ?></span>
	              	<?php } else { ?>
	              		<span class='title'><?php nxs_l18n_e("Inactive[nxs:popup,button]", "nxs_td"); ?></span>
	              	<?php } ?>	
	              	
	                <div class="nxs-clear margin"></div>
	              </div>
	            </div>
	            <div class="nxs-clear margin"></div>
		        </div> <!--END content-->
	        	<?php
	        }
	        ?>
	        
	        <!-- export -->
	        <!--
	        <div class="content2">
            <div class="box">
              <div class="box-title"><h4><?php nxs_l18n_e("Site data[nxs:popup,label]", "nxs_td"); ?></h4></div>
              <div class="box-content">
              	<a href="#" onclick="nxs_js_popup_site_neweditsession('exportsite'); return false;" class="nxsbutton1 nxs-float-right">Export site</a>
                <div class="nxs-clear margin"></div>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div>
	        -->
	      </div>
	    </div>
		        
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
        </div>
        <div class="nxs-clear"></div>
      </div> <!--END content-->
		            
		</div>
	</div>
	
	<script>

		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showokifnotdirty'; 
		}

		function nxs_js_execute_after_popup_shows()
		{
			//jQuery('#gebruikersnaam').focus();
			<?php
			if ($shouldrefresh)
			{
				?>
				nxs_js_refreshcurrentpage();
				<?php
			}
			?>
		}
		
		// overriden
		function nxs_js_showwarning_when_trying_to_close_dirty_popup()
		{
			return false;
		}
		
	</script>	
	
	<?php
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

/* NEXUS SUPPORT OPTIONS
---------------------------------------------------------------------------------------------------- */
function nxs_site_supportoptions_rendersheet($args)
{
	//
	extract($args);
	
	extract($clientpopupsessiondata);
	extract($clientshortscopedata);
		
	$result = array();
	
	//$meta = nxs_getsitemeta();
	//$someproperty = $meta["someproperty"];
	
	nxs_ob_start();
	?>
	
	<div class="nxs-admin-wrap">
		<div class="block">	

     	<?php nxs_render_popup_header(nxs_l18n__("Nexus support options[nxs:popup]", "nxs_td")); ?>

			<div class="nxs-popup-content-canvas-cropper">
				<div class="nxs-popup-content-canvas">
					
			    <!-- Skype chat -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Skype[nxs:popup,label]", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a id='nxstryskype' href="skype:barkgj?chat"><?php nxs_l18n_e("Start skype chat[nxs:label]", "nxs_td"); ?></a>
              	<a href='http://www.skype.com' target='_blank'><?php nxs_l18n_e("Requires Skype[nxs:link]", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div>

					<!-- Twitter -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Twitter[nxs:popup,label]", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="http://twitter.com/vanseijen" target="_blank">@vanseijen</a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div>
					
					<!-- Video tutorial -->
	        <div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Video tutorials[nxs:popup,label]", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href='http://nexusthemes.com/video-tutorials/' target='_blank'><?php nxs_l18n_e("Video tutorials[nxs:link]", "nxs_td"); ?></a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div>
					
					<!-- web help -->
					<div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Website[nxs:label]", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="http://nexusthemes.com" target="_blank">http://nexusthemes.com</a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div>	   	        
					
	        
	        <!-- email -->
					<div class="content2">
            <div class="box">
              <div class="box-title">
              	<h4><?php nxs_l18n_e("Email[nxs:label]", "nxs_td"); ?></h4>
              </div>
              <div class="box-content">
              	<a href="mailto:info@nexusthemes.com">info@nexusthemes.com</a>
              </div>
            </div>
            <div class="nxs-clear margin"></div>
	        </div>
	      	        
	      </div>
	    </div>
		        
      <div class="content2">
        <div class="box">
          <a id='nxs_popup_genericsavebutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericokbutton' href='#' class="nxsbutton nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("OK[nxs:popup,button]", "nxs_td"); ?></a>
          <a id='nxs_popup_genericcancelbutton' href='#' class="nxsbutton2 nxs-float-right" onclick='nxs_js_closepopup_unconditionally_if_not_dirty(); return false;'><?php nxs_l18n_e("Cancel[nxs:popup,button]", "nxs_td"); ?></a>
        </div>
        <div class="nxs-clear"></div>
      </div> <!--END content-->
		            
		</div>
	</div>
	
	<script>

		function nxs_js_tryskype()
		{
			try 
			{
				jQuery('#nxstryskype').click();
				nxs_js_alert('Launching Skype chat support');
			} 
			catch (exc)
			{
				// fails
				nxs_js_alert('Looks like Skype is not yet installed');
			}
		}

		function nxs_js_popup_get_initialbuttonstate() 
		{ 
			return 'showokifnotdirty'; 
		}

		function nxs_js_execute_after_popup_shows()
		{
			//jQuery('#gebruikersnaam').focus();
		}
		
		// overriden
		function nxs_js_showwarning_when_trying_to_close_dirty_popup()
		{
			return false;
		}
		
	</script>	
	
	<?php
	$html = nxs_ob_get_contents();
	nxs_ob_end_clean();
	$result["html"] = $html;
	nxs_webmethod_return_ok($result);
}

/* COMMENT PROVIDER
---------------------------------------------------------------------------------------------------- */
function nxs_site_selectcommentprovider_getoptions($args)
{	
	$options = array
	(
		"sheettitle" => nxs_l18n__("Comment provider", "nxs_td"),
		"footerfiller" => true,
		"fields" => array
		(
			array( 
				"id" 			=> "active_commentsprovider",
				"type" 			=> "select",
				"dropdown" 		=> nxs_getcommentsproviders_idtonames(),
			),
		)
	);
	return $options;
}

/* MARKETING MANAGEMENT
---------------------------------------------------------------------------------------------------- */
function nxs_site_marketingmanagementhome_getoptions($args)
{	
	$options = array
	(
		"sheettitle" => nxs_l18n__("Marketing management", "nxs_td"),
		"fields" => array
		(
			array( 
				"id" 			=> "googletagmanagerid",
				"label"			=> nxs_l18n__("Google Tag Manager ID", "nxs_td") . "<br />GTM-XXXXXX",
				"type" 			=> "input",
			),
			array(
				"id" 			=> "analyticsUA",
				"label"			=> nxs_l18n__("Google Analytics UA", "nxs_td"),
				"type" 			=> "input",
			),
			array(
				"id" 			=> "pagecaching",
				"label"			=> nxs_l18n__("Page caching", "nxs_td"),
				"type" 			=> "checkbox",
			),
		)
	);
	return $options;
}

/* INTEGRATIONS
---------------------------------------------------------------------------------------------------- */
function nxs_site_integrationshome_getoptions($args)
{	
	$options = array
	(
		"sheettitle" => nxs_l18n__("Integrations", "nxs_td"),
		"fields" => array
		(
			array( 
				"id" 			=> "googletagmanagerid",
				"label"			=> nxs_l18n__("Google Tag Manager ID", "nxs_td") . "<br />GTM-XXXXXX",
				"type" 			=> "input",
			),
			array(
				"id" 			=> "analyticsUA",
				"label"			=> nxs_l18n__("Google Analytics UA", "nxs_td"),
				"type" 			=> "input",
			),
			array(
				"id" 			=> "googlemapsapikey",
				"label"			=> nxs_l18n__("Google Maps API key", "nxs_td"),
				"type" 			=> "input",
			),
			array(
				"id" 			=> "googlemapsapikeyhint",
				"label"			=> "",
				"type" 				=> "custom",
				"customcontent" => "<a target='_blank' style='backgroundcolor: white; color: blue; text-decoration: underline;' href='https://www.wpsupporthelp.com/answer/i-rsquo-m-having-issues-with-the-google-maps-api-plugin-i-have-follow-1257/'>Click here to learn how to configure the Google Maps API key</a>",
			),
			
		)
	);
	return $options;
}

/* MARKETING MANAGEMENT
---------------------------------------------------------------------------------------------------- */
function nxs_site_webfontshome_getoptions($args)
{	
	$options = array
	(
		"sheettitle" => nxs_l18n__("Google webfonts management", "nxs_td"),
		"fields" => array
		(
			array( 
				"id" 			=> "googlewebfonts",
				"label"			=> nxs_l18n__("Google Webfonts", "nxs_td"),
				"type" 			=> "textarea",
			),
		)
	);
	return $options;
}

/* CACHE MANAGEMENT
---------------------------------------------------------------------------------------------------- */

function nxs_site_cachemanagementhome_clearcache_popupcontent($optionvalues, $args, $runtimeblendeddata) 
{
	nxs_ob_start();
	if ($runtimeblendeddata["cacheaction"] == "clear")
	{
		$path = nxs_cache_getcachefolder();
		nxs_cache_clear();
		?>
		Cache cleared <?php echo $path; ?>
		<?php
	}
	?>
	<a href="#" class='nxsbutton1 nxs-float-right' onclick='nxs_js_triggerclearcache(); return false;'><?php nxs_l18n_e('Clear', 'nxs_td'); ?></a>
	<script>
		function nxs_js_triggerclearcache()
		{
			nxs_js_popup_setshortscopedata("cacheaction","clear");
			nxs_js_popup_refresh();
		}
	</script>
	<?php
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}


function nxs_site_dataprotectionhome_getoptions($args)
{
	$prefix = nxs_dataprotection_getprefix();
	$usecookiewallactivity = "nexusframework:usecookiewall";
	$usecookiewallactivity = nxs_dataprotection_getcanonicalactivity($usecookiewallactivity);
	
	$dataprotectiontype_usecookiewall = nxs_dataprotection_getdataprotectiontype($usecookiewallactivity);
	if (isset($args["clientpopupsessiondata"]["{$prefix}{$usecookiewallactivity}"]))
	{
		$dataprotectiontype_usecookiewall = $args["clientpopupsessiondata"]["{$prefix}{$usecookiewallactivity}"];
	}

	$fields = array();

	// time stamp or version number of the privavy policy and/or terms and conditions
	if (true)
	{
		$fields[] = array
		(
			"id" 					=> "{$prefix}controllerlatestversion",
			"type" 					=> "input",
			"label"					=> nxs_l18n__("Version", "nxs_td"),
		);
	}
	
	// duration of the cookies
	$fields[] = array
	(
		"id" 					=> "{$prefix}cookieretention",
		"type" 					=> "select",
		"label"					=> nxs_l18n__("Retention period", "nxs_td"),
		"dropdown" 				=> array
		(
			"" => "30 days (default)",
			"30" => "30 days",
			"60" => "60 days",
			"365" => "365 days",
		),
	);
	
	
	// each configurable component that deals with user data that can be controlled
	if (true)
	{		
		$fields[] = array
		( 
			"id" 				=> "wrapper_title_begin",
			"type" 				=> "wrapperbegin",
			"label" 			=> nxs_l18n__("Components Impacting User Data (You Control How)", "nxs_td"),
		);
		
		$a = array
		(
			"rootactivity" => "nexusframework:process_request",
		);
		$controllable_activities = nxs_dataprotection_get_controllable_activities($a);
		$controllable_activities = array_reverse($controllable_activities);
		
		foreach ($controllable_activities as $controllable_activity => $control_options)
		{
			$controllable_activity = nxs_dataprotection_getcanonicalactivity($controllable_activity);
			
			$popuprefreshonchange = "";
			if ($controllable_activity == "nexusframework_usecookiewall")
			{
				$popuprefreshonchange = "true";
			}
			
			$fields[] = array
			(
				"id" 					=> "{$prefix}{$controllable_activity}",
				"type" 					=> "select",
				"label"					=> nxs_l18n__("{$controllable_activity}", "nxs_td"),
				"dropdown" 				=> $control_options,
				"popuprefreshonchange" => $popuprefreshonchange,
			);
		}
		
		$fields[] = array
		( 
			"id" 				=> "wrapper_end",
			"type" 				=> "wrapperend",
		);
		
	}

	// EXPLICIT CONSENT OPTIONS
	
	if ($dataprotectiontype_usecookiewall == "enabled_disabled_for_robots")
	{
		// GENERAL
		
		$fields[] = array
		( 
			"id" 				=> "wrapper_title_begin",
			"type" 				=> "wrapperbegin",
			"label" 			=> nxs_l18n__("General", "nxs_td"),
			"initial_toggle_state"	=> "closed-if-empty",
			"initial_toggle_state_id" => "title",
		);
		
		$fields[] = array
		( 
			"id" 				=> "text",
			"type" 				=> "input",
			"label" 			=> nxs_l18n__("Title", "nxs_td"),
			"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
			"unicontentablefield" => true,
			"localizablefield"	=> true
		);
		
		$fields[] = array
		( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
		);
		
		// IMAGES
		
		$fields[] = array
		( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Images", "nxs_td"),
				"initial_toggle_state"	=> "closed-if-empty",
				"initial_toggle_state_id" => "title",
			);
			
		$fields[] = array
		(
			"id" 			=> "gdpr_imageid",
			"label"			=> nxs_l18n__("Trust Icon", "nxs_td"),
			"type" 			=> "image",
		);
		$fields[] = array
		(
			"id" 			=> "cookie_wall_image_imageid",
			"label"			=> nxs_l18n__("Background Image", "nxs_td"),
			"type" 			=> "image",
		);
		
		$fields[] = array
		( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
		);
		
		// TERMS AND CONDITIONS
		
		$fields[] = array
		( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Terms and Conditions", "nxs_td"),
				"initial_toggle_state"	=> "closed-if-empty",
				"initial_toggle_state_id" => "title",
			);
		
		$fields[] = array
		( 
			"id" 				=> "terms_and_conditions_title",
			"type" 				=> "input",
			"label" 			=> nxs_l18n__("Title", "nxs_td"),
			"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
			"unicontentablefield" => true,
			"localizablefield"	=> true
		);
		$fields[] = array
		(
			"id" 				=> "terms_and_conditions_text",
			"type" 				=> "tinymce",
			"label" 			=> nxs_l18n__("Text", "nxs_td"),
			"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
			"unicontentablefield" => true,
			"localizablefield"	=> true
		);
		
		$fields[] = array
		( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
		);
		
		// PRIVACY POLICY
		
		$fields[] = array
		( 
				"id" 				=> "wrapper_title_begin",
				"type" 				=> "wrapperbegin",
				"label" 			=> nxs_l18n__("Privacy Policy", "nxs_td"),
				"initial_toggle_state"	=> "closed-if-empty",
				"initial_toggle_state_id" => "title",
			);
		
		$fields[] = array
		( 
			"id" 				=> "privacy_policy_title",
			"type" 				=> "input",
			"label" 			=> nxs_l18n__("Title", "nxs_td"),
			"placeholder" 		=> nxs_l18n__("Title goes here", "nxs_td"),
			"unicontentablefield" => true,
			"localizablefield"	=> true
		);
		$fields[] = array
		(
			"id" 				=> "privacy_policy_text",
			"type" 				=> "tinymce",
			"label" 			=> nxs_l18n__("Text", "nxs_td"),
			"placeholder" 		=> nxs_l18n__("Text goes here", "nxs_td"),
			"unicontentablefield" => true,
			"localizablefield"	=> true
		);
		
		$fields[] = array
		( 
				"id" 				=> "wrapper_title_end",
				"type" 				=> "wrapperend"
		);
	}
	
	// 
	
	$options = array
	(
		"sheettitle" => nxs_l18n__("Data Protection (GDPR/privacy)", "nxs_td"),
		"footerfiller" => true,
		"fields" => $fields,
	);
	
	return $options;	
}

function nxs_site_cachemanagementhome_getoptions($args)
{	
	$options = array
	(
		"sheettitle" => nxs_l18n__("Cache management", "nxs_td"),
		"footerfiller" => true,
		"fields" => array
		(
			array
			(
				"id" 			=> "pagecaching_enabled",
				"label"			=> nxs_l18n__("Page caching", "nxs_td"),
				"type" 			=> "checkbox",
			),
			array
			(
				"id" 			=> "pagecaching_expirationinsecs",
				"label"			=> nxs_l18n__("Expiration period", "nxs_td"),
				"type" 			=> "select",
				"dropdown" 		=> array
				(
					"@@@nxsempty@@@"	=>nxs_l18n__("Default", "nxs_td"), 
					"3600"	=>nxs_l18n__("1 hour", "nxs_td"), 
					"14400"	=>nxs_l18n__("4 hours", "nxs_td"), 
					"86400"	=>nxs_l18n__("1 day", "nxs_td"), 
					"604800"	=>nxs_l18n__("1 week", "nxs_td"), 
					"never"	=>nxs_l18n__("Never", "nxs_td"), 
				)
			),
			array
			( 
				"id" 				=> "clearcache",
				"type" 				=> "custom",
				"customcontenthandler"	=> "nxs_site_cachemanagementhome_clearcache_popupcontent",
				"label" 			=> nxs_l18n__("Clear cache", "nxs_td"),
				"placeholder" 		=> "Clears any existing items in the cache",
			),
		)
	);
	return $options;
}

/* GENERIC STYLING
---------------------------------------------------------------------------------------------------- */
function nxs_site_sitestyling_getoptions($args)
{	
	$options = array(
		"sheettitle" => nxs_l18n__("Generic styling", "nxs_td"),
		"footerfiller" => true,		
		"fields" => array(
			
			// SITE STYLING
		
			array( 
				"id" 			=> "wrapper_body_begin",
				"type" 			=> "wrapperbegin",
				"label" 		=> nxs_l18n__("Site styling", "nxs_td"),
			),
			
			array( 
				"id"			=> "site_colorzen",
				"type" 			=> "colorzen",
				"label" 		=> nxs_l18n__("Color", "nxs_td"),
				"focus"			=> "true",
				"tooltip" 		=> nxs_l18n__("The background color", "nxs_td")
			),
			array( 
				"id" 			=> "site_linkcolorvar",
				"type" 			=> "colorvariation",
				"scope" 		=> "link",
				"label" 		=> nxs_l18n__("Link color", "nxs_td"),
			),
			array( 
				"id"			=> "site_text_fontsize",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Text fontsize", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("fontsize")
			),
			
			array( 
				"id" 			=> "wrapper_begin",
				"type" 			=> "wrapperend"
			),

			// PAGE STYLING
			
			array( 
				"id" 			=> "wrapper_begin",
				"type" 			=> "wrapperbegin",
				"label" 		=> nxs_l18n__("Page styling", "nxs_td"),
			),
			
			array
			( 
				"id"			=> "site_page_colorzen",
				"type" 			=> "colorzen",
				"label" 		=> nxs_l18n__("Color", "nxs_td"),
				"focus"			=> "true",
				"tooltip" 		=> nxs_l18n__("The background color", "nxs_td")
			),
			array( 
				"id" 			=> "site_page_linkcolorvar",
				"type" 			=> "colorvariation",
				"scope" 		=> "link",
				"label" 		=> nxs_l18n__("Link color", "nxs_td"),
			),
			array(
				"id"			=> "site_page_margin_top",
				"type" 			=> "select",
				"label"			=> nxs_l18n__("Margin top", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("margin")
			),
			array(
				"id" 			=> "site_page_padding_top",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Padding top", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("padding")
			),
			array(
				"id" 			=> "site_page_padding_bottom",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Padding bottom", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("padding")
			),
			array(
				"id" 			=> "site_page_margin_bottom",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Margin bottom", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("margin")
			),
			array(
				"id" 			=> "site_page_border_top_width",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Border top width", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("border_width")
			),
			array(
				"id" 			=> "site_page_border_bottom_width",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Border bottom width", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("border_width")
			),
			array(
				"id" 			=> "site_page_border_radius",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Border radius", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("border_radius")
			),
			
			array( 
				"id" 			=> "wrapper_begin",
				"type" 			=> "wrapperend"
			),			
		),
	);
	
	return $options;
}

/* WIDGETS MANAGEMENT
---------------------------------------------------------------------------------------------------- */

function nxs_site_wpmanagementhome_getoptions($args)
{	
	$options = array
	(
		"sheettitle" => nxs_l18n__("WP management", "nxs_td"),
		"footerfiller" => true,
		"fields" => array(
			
			array( 
				"id" 		=> "wrapper_widgetsmanagement_begin",
				"type" 		=> "wrapperbegin",
				"label" 	=> nxs_l18n__("Advanced", "nxs_td"),
			),
			array( 
				"id"		=> "widgetsmanagement_enableconceptual",
				"type" 		=> "select",
				"label" 	=> nxs_l18n__("Conceptual widgets", "nxs_td"),
				"dropdown" 	=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Default (hide)", "nxs_td"), 
					"hide" => nxs_l18n__("Hide", "nxs_td"), 
					"show" => nxs_l18n__("Show", "nxs_td"), 
				)
			),
			array( 
				"id"		=> "wpmanagement_showadminbar",
				"type" 		=> "select",
				"label" 	=> nxs_l18n__("Show admin bar of WP", "nxs_td"),
				"dropdown" 	=> array
				(
					"@@@nxsempty@@@" => nxs_l18n__("Default (hide)", "nxs_td"), 
					"hide" => nxs_l18n__("Hide", "nxs_td"), 
					"show" => nxs_l18n__("Show", "nxs_td"), 
				)
			),
			array( 
				"id" 		=> "wrapper_accessrestrictions_end",
				"type" 		=> "wrapperend"
			),
		)
	);
	
	return $options;
}

/* ACCESS RESTRICTIONS
---------------------------------------------------------------------------------------------------- */
function nxs_site_accessrestrictionhome_getoptions($args)
{	
	$options = array
	(
		"sheettitle" => nxs_l18n__("Site access restrictions", "nxs_td"),
		"footerfiller" => true,
		"fields" => array(
			
			array( 
				"id" 		=> "wrapper_accessrestrictions_begin",
				"type" 		=> "wrapperbegin",
				"label" 	=> nxs_l18n__("Site access restrictions", "nxs_td"),
			),
			array( 
				"id"		=> "accessrestrictions_anonymousaccess",
				"type" 		=> "select",
				"label" 	=> nxs_l18n__("Anonymous access", "nxs_td"),
				"dropdown" 	=> array
				(
					"allow" => nxs_l18n__("Allow", "nxs_td"), 
					"block" => nxs_l18n__("Block", "nxs_td"), 
				)
			),
			array( 
				"id" 		=> "wrapper_accessrestrictions_end",
				"type" 		=> "wrapperend"
			),
		)
	);
	
	return $options;
}

/* LOOKUP TABLE MANAGEMENT
---------------------------------------------------------------------------------------------------- */
function nxs_site_lookuptablemanagementhome_customhtml($optionvalues, $args, $runtimeblendeddata) {
	nxs_ob_start();
	
	$clientshortscopedata = $args["clientshortscopedata"];
	if (isset($clientshortscopedata)) {

		if ($clientshortscopedata["action"] == "deletelookuptableitem") {
			$name = $clientshortscopedata["name"];
			nxs_lookuptable_deletekey($name);
			//echo "done";
		}
		else if ($clientshortscopedata["action"] == "storelookuptableitem")
		{
			$name = $clientshortscopedata["name"];
			$val = $clientshortscopedata["val"];
			nxs_lookuptable_setlookupvalueforkey($name, $val);
		}
		else if ($clientshortscopedata["action"] == "changelookuptableitem")
		{
			$name = $clientshortscopedata["name"];
			$newvalue = $clientshortscopedata["newvalue"];
			nxs_lookuptable_setlookupvalueforkey($name, $newvalue);
		}
	}
	
	$includeruntimeitems = false;
	$lookup = nxs_lookuptable_getlookup_v2($includeruntimeitems);
	
	if (count($lookup) > 0) {
		$foundatleastone = true;
	}

	if (true)
	{			
		echo '
		<table>';
			
			// Table head and foot
			echo '
			<thead>
				<tr>
					<th scope="col" class="nxs-title">
						<span class="nxs-margin-left15">Key</span>
					</th>
					<th scope="col">
						<span>Value</span>
					</th>
					<th scope="col">
						<span></span>
					</th>
					<th scope="col">
						<span></span>
					</th>
					
				</tr>
			</thead>';
			
			// Table body
			echo '
			<tbody>';
		
			foreach ($lookup as $currentname => $currentvalue) {
				echo '
				<tr>';
				
					// name
					echo '
					<td class="lookuptable-item">
						<span class="nxs-margin-left15">' . nxs_htmlescape($currentname) . '<span>						
					</td>';

					// value
					echo '
					<td class="lookuptable-item">
						<span class="nxs-margin-left15">' . nxs_htmlescape($currentvalue) . '<span>
					</td>';
					
					// Edit button
					echo'
					<td class="nxs-width5">
						<a href="#" title="Edit" onclick="nxs_js_editlookuptableitem(' . nxs_htmlescape(json_encode($currentname)) . ',' . nxs_htmlescape(json_encode($currentvalue)) . '); return false;">
							<span class="nxs-icon nxs-icon-plug"></span>
						</a>
					</td>';

					// Remove button
					echo'
					<td class="nxs-width5">
						<a href="#" title="Remove" onclick="nxs_js_deletelookuptableitem(' . nxs_htmlescape(json_encode($currentname)) . '); return false;">
							<span class="nxs-icon nxs-icon-trash"></span>
						</a>
					</td>
				
				</tr>';
			}
			
			?>
			<tr>
				<td>
					<input class="nxs-margin-left15" type='text' value='' id='lookuptable_new_name' name='lookuptable_new_name' />
				</td>
				<td>
					<input type='text' value='' id='lookuptable_new_val' name='lookuptable_new_val' />
				</td>
                <td>
					<a href="#" onclick="nxs_js_store_lookuptable(); return false;" class='nxsbutton1'>
						Add
					</a>
				</td>
				<td>
                </td>
			</tr>
			<?php
			
			echo '
			</tbody>
		
		</table>
		<div class="padding"></div>
		';
	}
	?>
	<script>
		function nxs_js_store_lookuptable()
		{
			var name = jQuery("#lookuptable_new_name").val();
			var val = jQuery("#lookuptable_new_val").val();
			
			nxs_js_popup_setshortscopedata('action', 'storelookuptableitem');
			nxs_js_popup_setshortscopedata('name', name);
			nxs_js_popup_setshortscopedata('val', val);
			nxs_js_popup_refresh();
		}
		
		function nxs_js_editlookuptableitem(name, oldvalue)
		{
			var newvalue = prompt('<?php nxs_l18n_e("Enter the new value", "nxs_td"); ?>', oldvalue);
			if (newvalue)
			{
				if (newvalue != oldvalue)
				{
					nxs_js_popup_setshortscopedata('action', 'changelookuptableitem');
					nxs_js_popup_setshortscopedata('name', name);
					nxs_js_popup_setshortscopedata('newvalue', newvalue);
					
					// ensure page is refresh when user hits save
					nxs_js_popup_setsessioncontext("onsaverefreshpage", true);
					
					nxs_js_popup_refresh();
				}
				else
				{
					// nothing changed
					//nxs_js_alert('same');
				}
			}
			else
			{
				// cancelled
				//nxs_js_alert('cancelled1');
			}
		}
		
		function nxs_js_deletelookuptableitem(name)
		{
			var conf = confirm("<?php nxs_l18n_e("This will remove the lookup table item from your site, continue?", "nxs_td"); ?>");
    	if(conf == true)
    	{
				nxs_js_popup_setshortscopedata('action', 'deletelookuptableitem');
				nxs_js_popup_setshortscopedata('name', name);
				
					// ensure page is refresh when user hits save
					nxs_js_popup_setsessioncontext("onsaverefreshpage", true);
				
				nxs_js_popup_refresh();
			}
		}
	</script>
	<?php
	
	if (!$foundatleastone)
	{
		nxs_l18n_e("No lookup items found", "nxs_td");
	}
	
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

/* LOOKUP TABLE POPUP
---------------------------------------------------------------------------------------------------- */
function nxs_site_lookuptablemanagementhome_getoptions($args)
{
	$result = array(
		"sheettitle" 			=> nxs_l18n__("Lookup table management", "nxs_td"),
		"fields" 				=> array()
	);
	
	$result["fields"][] = array(
		"id" 					=> "lookuptablemanagementcustom",
		"type" 					=> "custom",
		"customcontenthandler"	=> "nxs_site_lookuptablemanagementhome_customhtml",
		"label" 				=> nxs_l18n__("Lookup table", "nxs_td"),
	);
	
	return $result;
}




/* UNISTYLE / UNICONTENT
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

/* UNISTYLE MANAGEMENT
---------------------------------------------------------------------------------------------------- */
	
function nxs_site_unistylemanagementhome_customhtml($optionvalues, $args, $runtimeblendeddata) {
	nxs_ob_start();
	
	$clientshortscopedata = $args["clientshortscopedata"];
	if (isset($clientshortscopedata)) {
		if ($clientshortscopedata["action"] == "renameunistyle") {
			$group = $clientshortscopedata["group"];
			$oldname = $clientshortscopedata["oldname"];
			$newname = $clientshortscopedata["newname"];
			nxs_unistyle_renameunistyle($group, $oldname, $newname);
			//echo "done";
		} else if ($clientshortscopedata["action"] == "deleteunistyle") {
			$group = $clientshortscopedata["group"];
			$name = $clientshortscopedata["name"];
			nxs_unistyle_deleteunistyle($group, $name);
			//echo "done";
		}
	}
	
	$groups = nxs_unistyle_getgroups();
	
	$foundatleastone = false;

	foreach ($groups as $currentgroup) {
		
		$dropdown = nxs_unistyle_getunistylenames($currentgroup);
		$unistylenames = array();
		
		foreach ($dropdown as $currentkey => $currentvalue) {
			if ($currentkey == "@@@nxsempty@@@") {
				// skip
				$currentkey = "";
			} else {
				$unistylenames[] = $currentkey;
			}			
		}
	
		if (count($unistylenames) > 0) {
			
			$foundatleastone = true;
				
			echo '
			<table>';
				
				// Table head and foot
				echo '
				<thead>
					<tr>
						<th scope="col" class="nxs-title">
							<span class="nxs-margin-left15">' . $currentgroup . '</span>
						</th>
						<th scope="col"><span></span></th>
						<th scope="col"><span></span></th>
					</tr>
				</thead>';
				
				// Table body
				echo '
				<tbody>';
			
				foreach ($unistylenames as $currentunistylename) {
					echo '
					<tr>';
					
						// Unistyle name
						echo '
						<td class="unistyle-item">
							<span class="nxs-margin-left15">' . $currentunistylename . '<span>
						</td>';
						
						// Rename button
						echo '
						<td class="nxs-width5">
							<a href="#" title="Rename" onclick="nxs_js_renameunistyle(\'' . $currentgroup . '\', \'' . $currentunistylename . '\'); return false;">
								<span class="nxs-icon nxs-icon-pencil"></span>
							</a>
						</td>';
						
						// Remove button
						echo'
						<td class="nxs-width5">
							<a href="#" title="Remove" onclick="nxs_js_deleteunistyle(\''  . $currentgroup . '\', \'' . $currentunistylename . '\'); return false;">
								<span class="nxs-icon nxs-icon-trash"></span>
							</a>
						</td>
					
					</tr>';
				}
				
				echo '
				</tbody>
			
			</table>
			<div class="padding"></div>
			';
		} 
	}
	
	?>
	<script>
		function nxs_js_renameunistyle(group, oldname)
		{
			var newname = prompt('<?php nxs_l18n_e("Enter the new unistyle name", "nxs_td"); ?>', oldname);
			if (newname)
			{
				if (newname != oldname)
				{
					nxs_js_popup_setshortscopedata('action', 'renameunistyle');
					nxs_js_popup_setshortscopedata('group', group);
					nxs_js_popup_setshortscopedata('oldname', oldname);
					nxs_js_popup_setshortscopedata('newname', newname);
					nxs_js_popup_refresh();
				}
				else
				{
					// nothing changed
					//nxs_js_alert('same');
				}
			}
			else
			{
				// cancelled
				//nxs_js_alert('cancelled1');
			}
		}
		
		function nxs_js_deleteunistyle(group, name)
		{
			var conf = confirm("<?php nxs_l18n_e("This will remove the unistyle from your site, continue?", "nxs_td"); ?>");
    	if(conf == true)
    	{
				nxs_js_popup_setshortscopedata('action', 'deleteunistyle');
				nxs_js_popup_setshortscopedata('group', group);
				nxs_js_popup_setshortscopedata('name', name);
				nxs_js_popup_refresh();
			}
		}
	</script>
	<?php
	
	if (!$foundatleastone)
	{
		nxs_l18n_e("No unistyles found", "nxs_td");
	}
	
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}



function nxs_site_unistylemanagementhome_getoptions($args)
{
	$result = array
	(
		"sheettitle" => nxs_l18n__("Unistyle management", "nxs_td"),
		"fields" => array()
	);
	
	$result["fields"][] = array(
		"id" 					=> "clipboardselectorcustom",
		"type" 				=> "custom",
		"customcontenthandler"	=> "nxs_site_unistylemanagementhome_customhtml",
		"label" 			=> nxs_l18n__("Unistyles", "nxs_td"),
	);
	
	return $result;
}

/* UNICONTENT
---------------------------------------------------------------------------------------------------- */

function nxs_site_unicontentmanagementhome_customhtml($optionvalues, $args, $runtimeblendeddata) {
	nxs_ob_start();
	
	$clientshortscopedata = $args["clientshortscopedata"];
	if (isset($clientshortscopedata)) {
		
		if ($clientshortscopedata["action"] == "renameunicontent") {
			$group = $clientshortscopedata["group"];
			$oldname = $clientshortscopedata["oldname"];
			$newname = $clientshortscopedata["newname"];
			nxs_unicontent_renameunicontent($group, $oldname, $newname);
			//echo "done";
		} else if ($clientshortscopedata["action"] == "deleteunicontent") {
			$group = $clientshortscopedata["group"];
			$name = $clientshortscopedata["name"];
			nxs_unicontent_deleteunicontent($group, $name);
			//echo "done";
		}
	}
	
	$groups = nxs_unicontent_getgroups();
	
	$foundatleastone = false;

	foreach ($groups as $currentgroup) {
		$dropdown = nxs_unicontent_getunicontentnames($currentgroup);
		$unicontentnames = array();
		foreach ($dropdown as $currentkey => $currentvalue) {

			if ($currentkey == "@@@nxsempty@@@") {
				// skip
				$currentkey = "";
			} else {
				$unicontentnames[] = $currentkey;
			}			
		}
		
	
		if (count($unicontentnames) > 0) {
			
			$foundatleastone = true;
			
			echo '
			<table>';
				
				// Table head and foot
				echo '
				<thead>
					<tr>
						<th scope="col" class="nxs-title">
							<span class="nxs-margin-left15">' . $currentgroup . '</span>
						</th>
						<th scope="col"><span></span></th>
						<th scope="col"><span></span></th>
					</tr>
				</thead>';
				
				// Table body
				echo '
				<tbody>';
			
				foreach ($unicontentnames as $currentunicontentname) {
					echo '
					<tr>';
					
						// Unistyle name
						echo '
						<td class="unistyle-item">
							<span class="nxs-margin-left15">' . $currentunicontentname . '<span>
						</td>';
						
						// Rename button
						echo '
						<td class="nxs-width5">
							<a href="#" title="Rename" onclick="nxs_js_renameunicontent(\'' . $currentgroup . '\', \'' . $currentunicontentname . '\'); return false;">
								<span class="nxs-icon nxs-icon-pencil"></span>
							</a>
						</td>';
						
						// Remove button
						echo'
						<td class="nxs-width5">
							<a href="#" title="Remove" onclick="nxs_js_deleteunicontent(\''  . $currentgroup . '\', \'' . $currentunicontentname . '\'); return false;">
								<span class="nxs-icon nxs-icon-trash"></span>
							</a>
						</td>
					
					</tr>';
				}
				
				echo '
				</tbody>
			
			</table>
			<div class="padding"></div>
			';
		}
	}
	
	?>
	<script>
		function nxs_js_renameunicontent(group, oldname)
		{
			var newname = prompt('<?php nxs_l18n_e("Enter the new unicontent name", "nxs_td"); ?>', oldname);
			if (newname)
			{
				if (newname != oldname)
				{
					nxs_js_popup_setshortscopedata('action', 'renameunicontent');
					nxs_js_popup_setshortscopedata('group', group);
					nxs_js_popup_setshortscopedata('oldname', oldname);
					nxs_js_popup_setshortscopedata('newname', newname);
					nxs_js_popup_refresh();
				}
				else
				{
					// nothing changed
					//nxs_js_alert('same');
				}
			}
			else
			{
				// cancelled
				//nxs_js_alert('cancelled1');
			}
		}
		function nxs_js_deleteunicontent(group, name)
		{
			var conf = confirm("<?php nxs_l18n_e("This will remove the unicontent from your site, continue?", "nxs_td"); ?>");
    	if(conf == true)
    	{
				nxs_js_popup_setshortscopedata('action', 'deleteunicontent');
				nxs_js_popup_setshortscopedata('group', group);
				nxs_js_popup_setshortscopedata('name', name);
				nxs_js_popup_refresh();
			}
		}
	</script>
	<?php
	
	if (!$foundatleastone)
	{
		nxs_l18n_e("No unicontents found", "nxs_td");
	}
	
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

/* UNICONTENT POPUP
---------------------------------------------------------------------------------------------------- */

function nxs_site_unicontentmanagementhome_getoptions($args) {

	$result = array(
		"sheettitle"			=> nxs_l18n__("unicontent management", "nxs_td"),
		"fields" 				=> array()
	);
	
	$result["fields"][] = array(
		"id" 					=> "clipboardselectorcustom",
		"type" 					=> "custom",
		"customcontenthandler"	=> "nxs_site_unicontentmanagementhome_customhtml",
		"label" 				=> nxs_l18n__("unicontents", "nxs_td"),
	);
	
	return $result;
}






/* MAINTENANCE MODE
---------------------------------------------------------------------------------------------------- */

function nxs_site_maintenancehome_getoptions($args)
{	
	$options = array (
		"sheettitle" => nxs_l18n__("Site maintenance mode", "nxs_td"),
		"footerfiller" => true,	// fills footer since the ddl at bottom wont be accessible otherwise
		"fields" => array(
			
			array( 
				"id"			=> "wrapper_maintenance_begin",
				"type" 			=> "wrapperbegin",
				"label" 		=> nxs_l18n__("Site maintenance mode", "nxs_td"),
			),
			array( 
				"id"			=> "maintenance_duration",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Maintenance mode", "nxs_td"),
				"dropdown" 		=> array(
					"-"			=>nxs_l18n__("Online; no maintenance", "nxs_td"), 
					"3600"		=>nxs_l18n__("Offline (approx. 1 hour)", "nxs_td"), 
					"14400"		=>nxs_l18n__("Offline (approx. 4 hours)", "nxs_td"), 
					"86400"		=>nxs_l18n__("Offline (approx. 1 day)", "nxs_td"), 
					"172800"	=>nxs_l18n__("Offline (approx. 2 days)", "nxs_td"), 
					"259200"	=>nxs_l18n__("Offline (approx. 3 days)", "nxs_td"), 
					"345600"	=>nxs_l18n__("Offline (approx. 4 days)", "nxs_td"), 
					"604800"	=>nxs_l18n__("Offline (approx. 1 week)", "nxs_td"), 
					"1209600"	=>nxs_l18n__("Offline (approx. 2 weeks)", "nxs_td"), 
				)
			),
			array( 
				"id" 			=> "wrapper_maintenance_end",
				"type" 			=> "wrapperend"
			),
		)
	);
	
	return $options;
}

/* MAINTENANCE MODE
---------------------------------------------------------------------------------------------------- */

function nxs_site_headerfooter_getoptions($args)
{	
	$options = array (
		"sheettitle" => nxs_l18n__("Header and footer options", "nxs_td"),
		"fields" => array(
			
			array( 
				"id"			=> "wrapper_header_begin",
				"type" 			=> "wrapperbegin",
				"label" 		=> nxs_l18n__("Header", "nxs_td"),
			),
			array( 
				"id" 			=> "vg_injecthead",
				"label"			=> nxs_l18n__("Head script", "nxs_td"),
				"type" 			=> "textarea",
				"placeholder" 		=> nxs_l18n__("Script to insert within the &gt;head&lt; tag", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("Script to insert within the &gt;head&lt; tag", "nxs_td"),
				"localizablefield"	=> true
			),
			array( 
				"id" 			=> "footerhtmltemplate",
				"label"			=> nxs_l18n__("Footer html template", "nxs_td"),
				"type" 			=> "textarea",
				"valueadapters" => array("" => "{{{themelink}}} | {{{authenticatelink}}}"),
				"placeholder" 		=> nxs_l18n__("{{{themelink}}} | {{{authenticatelink}}}", "nxs_td"),
				"tooltip" 			=> nxs_l18n__("The html to show in the footer. You can use {{{nexuslink}}} {{{themelink}}} and {{{authenticatelink}}} placeholders.", "nxs_td"),
				"localizablefield"	=> true
			),
			array( 
				"id" 			=> "wrapper_header_end",
				"type" 			=> "wrapperend"
			),
		)
	);
	
	return $options;
}

/* PAGE STYLING
---------------------------------------------------------------------------------------------------- */

function nxs_site_sitepagestyling_getoptions($args){	
	$options = array(
		"sheettitle" => nxs_l18n__("Page styling", "nxs_td"),
		"fields" => array(

			array( 
				"id" 			=> "wrapper_begin",
				"type" 			=> "wrapperbegin",
				"label" 		=> nxs_l18n__("Page styling", "nxs_td"),
			),
			
			array( 
				"id"			=> "site_page_colorzen",
				"type" 			=> "colorzen",
				"label" 		=> nxs_l18n__("Color", "nxs_td"),
				"focus"			=> "true",
				"tooltip" 		=> nxs_l18n__("The background color", "nxs_td")
			),
			array( 
				"id" 			=> "site_page_linkcolorvar",
				"type" 			=> "colorvariation",
				"scope" 		=> "link",
				"label" 		=> nxs_l18n__("Link color", "nxs_td"),
			),
			array(
				"id" 			=> "site_page_margin_top",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Margin top", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("margin")
			),
			array(
				"id" 			=> "site_page_padding_top",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Padding top", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("padding")
			),
			array(
				"id" 			=> "site_page_padding_bottom",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Padding bottom", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("padding")
			),
			array(
				"id"			=> "site_page_margin_bottom",
				"type"			=> "select",
				"label"			=> nxs_l18n__("Margin bottom", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("margin")
			),
			array(
				"id"			=> "site_page_border_top_width",
				"type"			=> "select",
				"label"			=> nxs_l18n__("Border top width", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("border_width")
			),
			array(
				"id" 			=> "site_page_border_bottom_width",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Border bottom width", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("border_width")
			),
			array(
				"id" 			=> "site_page_border_radius",
				"type" 			=> "select",
				"label" 		=> nxs_l18n__("Border radius", "nxs_td"),
				"dropdown" 		=> nxs_style_getdropdownitems("border_radius")
			),
			
			array( 
				"id" 			=> "wrapper_begin",
				"type" 			=> "wrapperend"
			),			
		),
	);
	
	return $options;
}




/* COPY / PASTE PAGE
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

/* COPY PAGE
---------------------------------------------------------------------------------------------------- */
function nxs_site_clipboardcopyselector_customhtml()
{
	?>
	<a href="#" class="nxsbutton nxs-float-left" onclick="nxs_js_clipboard_copycontent('maincontent:contentbuilder'); return false;"><?php nxs_l18n_e("Copy main content of page", "nxs_td"); ?></a>
	<script>
		function nxs_js_clipboard_copycontent(context)
		{
			var d = {
						"action": "nxs_ajax_webmethods",
						"webmethod": nxs_js_getclipboardhandler() + "copy",
						"clipboardcontext" : context,
						"containerpostid": nxs_js_getcontainerpostid(),
					};
			
			if (context == "maincontent:contentbuilder")
			{
				//var dom = jQuery(".nxs-article-container");
				//var prefix = "nxs-post-";
				//var articlecontainerpostid = nxs_js_findclassidentificationwithprefix(dom, prefix);
				d.postid = nxs_js_getcontainerpostid();
			}
			else
			{
				// add more contexts here
			}
			
			// invoke ajax call
			var ajaxurl = nxs_js_get_adminurladminajax();
			jQ_nxs.ajax
			(
				{
					type: 'POST',
					data: d,
					cache: false,
					dataType: 'JSON',
					url: ajaxurl, 
					success: function(response) 
					{
						nxs_js_log(response);
						if (response.result == "OK")
						{
							nxs_js_alert(response.growl);
							// close popup, we're done :)
							nxs_js_closepopup_unconditionally_if_not_dirty();
						}
						else
						{
							nxs_js_alert('<?php nxs_l18n_e("Clipboard failed", "nxs_td"); ?>');
						}
					},
					error: function(response)
					{
						nxs_js_popup_notifyservererror();
						nxs_js_log(response);
						nxs_js_alert('<?php nxs_l18n_e("Clipboard failed", "nxs_td"); ?>');
					}
				}
			);
		}
	</script>
	<?php
}

/* COPY PAGE POPUP
---------------------------------------------------------------------------------------------------- */
function nxs_site_clipboardcopyselector_getoptions($args){	
	$options = array(
		"sheettitle" => nxs_l18n__("Clipboard - Copy context selector", "nxs_td"),
		"fields" => array(
			
			array(
				"id" 					=> "clipboardselectorcustom",
				"type" 					=> "custom",
				"customcontenthandler"	=> "nxs_site_clipboardcopyselector_customhtml",
				"label" 				=> nxs_l18n__("Clipboard context", "nxs_td"),
			),
		),
	);
	
	return $options;
}

/* PASTE PAGE
---------------------------------------------------------------------------------------------------- */
function nxs_site_clipboardpasteselector_customhtml()
{
 	if (true)
 	{
 		?>
		<a href="#" class="nxsbutton nxs-float-left" onclick="nxs_js_clipboard_pastecontent(); return false;"><?php nxs_l18n_e("Paste main content (content builder) of page", "nxs_td"); ?></a>
		<script>
			function nxs_js_clipboard_pastecontent()
			{
				var articlecontainerpostid = nxs_js_getcontainerpostid();
      	var dom = jQuery(".nxs-post-" + articlecontainerpostid);
      	jQuery(dom).addClass("blink");

				var conf = confirm("<?php nxs_l18n_e("This will override the (blinking) main content of this page. Continu?", "nxs_td"); ?>");
				jQuery(dom).removeClass("blink");
				
    		if(conf == true)
    		{
    			// invoke ajax call
					var ajaxurl = nxs_js_get_adminurladminajax();
					jQ_nxs.ajax
					(
						{
							type: 'POST',
							data: 
							{
								"action": "nxs_ajax_webmethods",
								"webmethod": nxs_js_getclipboardhandler() + "paste",
								"clipboardcontext" : "maincontent:contentbuilder",
								"destinationpostid": articlecontainerpostid,
								"clientpopupsessioncontext": nxs_js_getescaped_popupsession_context(),
								"clientqueryparameters": nxs_js_escaped_getqueryparametervalues()
							},
							cache: false,
							dataType: 'JSON',
							url: ajaxurl, 
							success: function(response) 
							{
								nxs_js_log(response);
								if (response.result == "OK")
								{
									nxs_js_refreshcurrentpage();
								}
								else
								{
									nxs_js_alert('<?php nxs_l18n_e("Clipboard failed", "nxs_td"); ?>');
								}
							},
							error: function(response)
							{
								nxs_js_popup_notifyservererror();
								nxs_js_log(response);
								nxs_js_alert('<?php nxs_l18n_e("Clipboard failed", "nxs_td"); ?>');
							}										
						}
					);
				}
				else
				{
					// cancelled
				}
			}
		</script>
		<?php
 	}
 	else
 	{
 		nxs_l18n_e("Unable to paste, no, or unsupported data in the clipboard", "nxs_td");
 	}
}

/* PASTE PAGE POPUP
---------------------------------------------------------------------------------------------------- */
function nxs_site_clipboardpasteselector_getoptions($args)
{	
	$options = array
	(
		"sheettitle" => nxs_l18n__("Clipboard - paste context selector", "nxs_td"),
		"fields" => array(
			
			array(
				"id" 					=> "clipboardselectorcustom",
				"type" 					=> "custom",
				"customcontenthandler"	=> "nxs_site_clipboardpasteselector_customhtml",
				"label" 				=> nxs_l18n__("Clipboard context", "nxs_td"),
			),
		),
	);
	
	return $options;
}




/* COLOR PALETTES
----------------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------- */

/* COLOR PALETTES POPUP
---------------------------------------------------------------------------------------------------- */
function nxs_site_managecolorization_getoptions($args)
{	
	$result = array(
		"sheettitle" 			=> nxs_l18n__("Manage colorization", "nxs_td"),
		"fields" 				=> array(),
	);
	
	$result["fields"][] = array(
		"id" 					=> "managecolorization",
		"type" 					=> "custom",
		"customcontenthandler"	=> "nxs_site_managecolorization_customhtml",
		"label" 				=> nxs_l18n__("Palettes", "nxs_td"),
	);
	
	return $result;
}

/* COLOR PALETTES MANAGEMENT
---------------------------------------------------------------------------------------------------- */
function nxs_site_managecolorization_customhtml($optionvalues, $args, $runtimeblendeddata) 
{
	nxs_ob_start();
	
	$clientshortscopedata = $args["clientshortscopedata"];
	if (isset($clientshortscopedata))
	{
		if ($clientshortscopedata["action"] == "activatepalette")
		{
			$palettename = $clientshortscopedata["palettename"];
			nxs_colorization_setactivepalettename($palettename);
			// refresh page
			?>
			<script>
				nxs_js_closepopup_unconditionally();
				nxs_js_refreshcurrentpage();			
			</script>
			<?php
		}
		else if ($clientshortscopedata["action"] == "deletepalette")
		{
			$palettename = $clientshortscopedata["palettename"];
			$activepalettename = nxs_colorization_getactivepalettename();
			if ($activepalettename == $palettename)
			{
				// 
				nxs_l18n_e("Failed; cannot delete the active palette", "nxs_td");
			}
			else
			{
				nxs_colorization_deletepalettename($palettename);
			}
		}
	}
	
	$palettenames = nxs_colorization_getpalettenames(true);
	
	$foundatleastone = false;
	
	echo '
	<table>';
		
		// Table head
		echo '
		<thead>
			<tr>
				<th scope="col" class="nxs-title">
					<span class="nxs-margin-left15">Color palette</span>
				</th>
				<th scope="col"><span></span></th>
				<th scope="col"><span></span></th>
				<th scope="col"><span></span></th>
			</tr>
		</thead>';
		
		// Table body
		echo '
		<tbody>';
	
		foreach ($palettenames as $key=>$currentpalettename) {
			if ($key == "@@@nxsempty@@@") {
				// skip
				$currentpalettename = "";
			} else {
				$foundatleastone = true;
				$activepalettename = nxs_colorization_getactivepalettename();
					
				echo '
				<tr>';
				
					// Unistyle name
					echo '
					<td class="unistyle-item nxs-padding-left10">';
						nxs_colorization_renderpalette($currentpalettename);
					echo'
					</td>';
					
					
					// Activate button
					if ($currentpalettename != $activepalettename) {
						echo '
						<td class="nxs-width5">
							<a href="#" title="Activate" onclick="nxs_js_activatepalette(\'' . $currentpalettename . '\'); return false;">
								<span class="nxs-icon nxs-icon-plug"></span>
							</a>
						</td>';
					} else {
						echo'<td></td>';	
					}
					
					// Remove button
					if ($currentpalettename != $activepalettename) {
						echo'
						<td class="nxs-width5">
							<a href="#" title="Remove" onclick="nxs_js_deletepalette(\'' .  $currentpalettename . '\'); return false;">
								<span class="nxs-icon nxs-icon-trash"></span>
							</a>
						</td>';
					} else {
						echo'<td></td>';	
					}
				
				echo'
				</tr>';
			}
		}
			
		echo '
		</tbody>
	
	</table>
	<div class="padding"></div>
	';

	if (!$foundatleastone)
	{
		nxs_l18n_e("No palettes found", "nxs_td");
	}
	?>
	<script>
		function nxs_js_activatepalette(palettename)
		{
			nxs_js_popup_setshortscopedata('action', 'activatepalette');
			nxs_js_popup_setshortscopedata('palettename', palettename);
			nxs_js_popup_refresh();
		}
		
		function nxs_js_deletepalette(palettename)
		{
			nxs_js_popup_setshortscopedata('action', 'deletepalette');
			nxs_js_popup_setshortscopedata('palettename', palettename);
			nxs_js_popup_refresh();
		}
	</script>
	<?php
	
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

/* COLOR PALETTES ACTIVATE POPUP
---------------------------------------------------------------------------------------------------- */
function nxs_site_doactivatepalette_getoptions($args){
	$result = array(
		"sheettitle" 			=> nxs_l18n__("Activate palette", "nxs_td"),
		"fields" 				=> array(),
	);
	
	$result["fields"][] = array(
		"id" 					=> "doactivatepalette",
		"type" 					=> "custom",
		"customcontenthandler"	=> "nxs_site_doactivatepalette_customhtml",
		"label" 				=> nxs_l18n__("Palette", "nxs_td"),
	);
	
	return $result;
}

function nxs_site_doactivatepalette_customhtml($optionvalues, $args, $runtimeblendeddata){
	$palettename = $args["clientpopupsessioncontext"]["palettename"];
	nxs_colorization_setactivepalettename($palettename);

	?>
	<script>
		nxs_js_refreshcurrentpage();
	</script>
	<?php
}

/* COLOR PALETTES SAVE POPUP
---------------------------------------------------------------------------------------------------- */
function nxs_site_dosavepalette_getoptions($args){
	$result = array(
		"sheettitle" 			=> nxs_l18n__("Save palette", "nxs_td"),
		"fields" 				=> array(),
	);
	
	$result["fields"][] = array(
		"id" 					=> "dosavepalette",
		"type" 					=> "custom",
		"customcontenthandler"	=> "nxs_site_dosavepalette_customhtml",
		"label" 				=> nxs_l18n__("Palette", "nxs_td"),
	);
	
	return $result;
}

function nxs_site_dosavepalette_customhtml($optionvalues, $args, $runtimeblendeddata) 
{
	$ishandled = false;
	$clientshortscopedata = $args["clientshortscopedata"];
	if (isset($clientshortscopedata))
	{
		if ($clientshortscopedata["action"] == "savepalettephase2")
		{
			$ishandled = true;
			
			$how = $args["clientpopupsessioncontext"]["how"];
			
			if ($how == "override")
			{			
				$palettename = nxs_colorization_getactivepalettename();
				if (!isset($palettename) || $palettename == "")
				{
					$palettename = nxs_colorization_getunallocatedpalettename();
				}
			}
			else if ($how == "new")
			{
				$palettename = nxs_colorization_getunallocatedpalettename();
			}
			
			if ($palettename == "")
			{
				nxs_webmethod_return_nack("palettename not set?");
			}
			
			$colorizationproperties = array();
			
			$colortypes = nxs_getcolorsinpalette();
			foreach($colortypes as $currentcolortype)
			{
				$subtypes = array("1", "2");
				foreach($subtypes as $currentsubtype)
				{
					$identification = $currentcolortype . $currentsubtype;
					$key = "colorvalue_" . $identification;
					$colorizationproperties[$key] = $clientshortscopedata[$key];
				}
			}			
			nxs_colorization_persistcolorizationproperties($palettename, $colorizationproperties);
			// and activate this particular color too
			nxs_colorization_setactivepalettename($palettename);
			
			?>
			<script>
				nxs_js_refreshcurrentpage();
			</script>
			<?php
		}
	}
	if (!$ishandled)
	{
		?>
		<script>
			nxs_js_popup_setshortscopedata('action', 'savepalettephase2');
			<?php
			$colortypes = nxs_getcolorsinpalette();
			foreach($colortypes as $currentcolortype)
			{
				$subtypes = array("1", "2");
				foreach($subtypes as $currentsubtype)
				{
					$identification = $currentcolortype . $currentsubtype;
					?>
					nxs_js_popup_setshortscopedata('colorvalue_<?php echo $identification;?>', jQuery('#vg_color_<?php echo $identification;?>_m').val());
					<?php
				}
			}
			?>
			nxs_js_popup_refresh();
		</script>
		<?php
	}
}
?>
