<?php
error_reporting(E_ERROR | E_PARSE);
?>function nxs_js_supporthandlequestion()
{
	var q = jQuery("#nxs_chat_q").val();
	
	// clear result box
	jQuery("#nxs_chat_hints").empty();

	// invoke local webmethod			
	nxs_js_chat_supportinvokeasyncwebmethod(q);
}

function nxs_js_supporthandlequestion_direct(q)
{
	nxs_js_supportstartchatstage2();
	jQuery("#nxs_chat_q").val(q);
	nxs_js_chat_supportinvokeasyncwebmethod(q);
}
		
function nxs_js_chat_supportinvokeasyncwebmethod(q)
{
	var ajaxurl = nxs_js_get_adminurladminajax();
	jQuery.ajax
	(
		{
			type: 'POST',
			data: 
			{
				"action": "nxs_ajax_webmethods",
				"webmethod": "getframeworksupport",
				"q": q,
			},
			cache: false,
			dataType: 'JSON',
			url: ajaxurl, 
			success: function(response) 
			{
				nxs_js_log(response);
				if (response.result == "OK")
				{
					nxs_js_log("got some positive feedback :)");
					
					var htmltoadd;
					
					if (response.autolivechat == "show")
					{
						var hint = new Object();
						hint.type = "livechat";
						response.hints.push(hint);
					}
					else if (response.autolivechat == "directlaunch")
					{
						nxs_js_log("directlaunch");
					}
					
					jQuery(".nxs-hints").addClass("nxs-display-none");
					
					if (response.hints)
					{
						var resultcount = response.hints.length;
						nxs_js_log("num of results;" + resultcount);
						
						if (response.hints.length > 0)
						{
							jQuery(".nxs-hints").removeClass("nxs-display-none");
							
							// populate the suggestions
							jQuery("#nxs_chat_hints").empty();
							jQuery.each
							(
								response.hints, 
								function(index, hint)
								{
									if (hint.type == "youtube") {
										
										<?php ob_start(); ?>
																						
										<li>
											<!-- Title -->
											<a href="https://www.youtube.com/watch?v={hint.youtubeid}" target="_blank"><h3>{hint.title}</h3></a>
											
											<!-- Image  -->
											<a href="https://www.youtube.com/watch?v={hint.youtubeid}" target="_blank"><img src="{hint.thumbimgurl}"></a>
											
											<!-- Cite -->
											<a href="https://www.youtube.com/watch?v={hint.youtubeid}" target="_blank"><cite>www.youtube.com/watch?v={hint.youtubeid}</cite></a>
											<br />
											
											<!-- Meta -->
											<span class="meta">{hint.meta}</span>
											
											<div class="nxs-clear nxs-padding"></div>
										</li>
										
										<?php
													
										$result = ob_get_contents();
										
										$result = preg_replace( "/\r|\n/", " ", $result );
										
										ob_end_clean();
										
										?> 
										var html = '<?php echo $result; ?>';
										
										html = nxs_js_replaceall('{hint.title}', hint.title, html);
										html = nxs_js_replaceall('{hint.thumbimgurl}', hint.thumbimgurl, html);
										html = nxs_js_replaceall('{hint.youtubeid}', hint.youtubeid, html);
										html = nxs_js_replaceall('{hint.meta}', hint.meta, html);
										
										jQuery("#nxs_chat_hints").append(html);
										
									} else if (hint.type == 'text') {
										
										<?php ob_start(); ?>
																				
										<li>
											
											<!-- Title -->
											<h3>{hint.title}</h3>
											
											<!-- Meta -->
											<span class="meta">{hint.meta}</span>
											
											<div class="nxs-clear padding"></div>		

										</li>
										
										
										<?php
													
										$result = ob_get_contents();
										
										$result = preg_replace( "/\r|\n/", " ", $result );
										
										ob_end_clean();
										
										?> 
										var html = '<?php echo $result; ?>';
										
										html = nxs_js_replaceall('{hint.title}', hint.title, html);
										html = nxs_js_replaceall('{hint.meta}', hint.meta, html);
										
										jQuery("#nxs_chat_hints").append(html);
										
										
									}
									else
									{
										var uberdiv = "Unsupported hint type;" + hint.type;
										nxs_js_log(uberdiv);
										//htmltoadd = "<li>" + uberdiv + "</li>";
										//jQuery("#nxs_chat_hints").append(htmltoadd);
									}
								}
							);
						}
					}
					
					jQuery("#nxs_chat_q").select();
					//jQuery("#nxs_chat_q").focus();
				}
				else
				{
					nxs_js_popup_notifyservererror_v2(response);
				}
			},
			error: function(xhr, ajaxOptions, thrownError)
			{
				// an error could occur if the user redirects before this operation is completed
				nxs_js_popup_notifyservererror_v2(thrownError);
			}
		}
	);
}

function nxs_js_supportstartchatstage2()
{
	// get rid of existing content
	jQuery("#nxs_frameworkchat_wrap").empty();
	
	<?php 
	
	ob_start();
	
	?>
	
	<div id="nxs_chat_innerwrap" class="nxs-admin-wrap">
		
		<div id="nxs_chat_topquestionwrap">
		
			<div class="nxs-question-wrapper">
				
				<!-- Arrow button -->
				<div class="nxs-float-left">
					<ul class="question">
						<li><a href="#" onclick="nxs_js_supportshowchatstage1(); return false;"><span class="nxs-icon-arrow-down" /></a></li>
					</ul>
				</div>
				
				<!-- Label -->
				<span class="nxs-admin-font nxs-float-left nxs-margin-right15">Please, enter your question</span>
				
				<!-- Input -->
				<input id="nxs_chat_q" type="text" name="nxs_chat_q" placeholder="Type here and hit &lt;ENTER&gt;" />
				
				<!-- Close button -->
				<a class="nxsbutton nxs-margin-right10" href="#" id="nxs_chat_submit" onclick="nxs_js_supporthandlequestion(); return false;">Go</a>
			
			</div>	
			
			<div class="nxs-hints nxs-padding10 nxs-display-none">
				<div class="block">
					<div class="content2">
						<ul id="nxs_chat_hints" ></ul>
					</div>
				</div>
			</div>			
		
		</div>
	
	</div>

	<?php
				
	$result = ob_get_contents();
	
	$result = preg_replace( "/\r|\n/", " ", $result );
	
	ob_end_clean();
	
	?> 
	
	jQuery("#nxs_frameworkchat_wrap").append('<?php echo $result; ?>');
			
	// set focus
	jQuery("#nxs_chat_q").focus();
	
	jQuery("#nxs_chat_q").keypress
	(
		function(e) 
		{
      if(e.which == 13) 
      {
      	nxs_js_log("chat q received enter");
      	
        jQuery(this).blur();
        jQuery('#nxs_chat_submit').focus().click();
        return false;
      }
  	}
  );
  
}

function nxs_js_supportchathide()
{
	nxs_js_supporthidechat();
	nxs_js_setcookie('nxs_disable_chat', true);
}

function nxs_js_supportchatshow()
{
	nxs_js_supportshowchatstage1();
	nxs_js_setcookie('nxs_disable_chat', false);
}
		
var nxs_supportchatprequestionloaded = false;

jQuery(window).load
(
	function()
	{
		nxs_js_log("SUPPORT window on load!");	
		
		if (window!=window.top)
		{
			nxs_js_log("window on load in iframe, ignoring :)");		
			return;
		}
		else
		{
			if (nxs_supportchatprequestionloaded) 
			{
				nxs_js_log("been there before :)");		
			}
			else
			{
				nxs_js_log("initializing chat!");
				
				// only load this 1x
				nxs_supportchatprequestionloaded = true;
				
				// wrap 
				jQuery("body").append("<div id='nxs_frameworkchat_wrap' class='display720 nxs-hidewheneditorinactive' style='display: none;'></div>");
				
				nxs_js_process_updated_editor_state_internal(false);

				var cookieval = nxs_js_getcookie('nxs_disable_chat');

				if (cookieval === 'true')
				{
					nxs_js_supporthidechat();
				}

				else {
					nxs_js_supportshowchatstage1();
				}

				nxs_js_log("Support chat was loaded!");
			}
		}
	}
);
		
function nxs_js_supportshowchatstage1()
{
	// empty the wrap
	jQuery("#nxs_frameworkchat_wrap").empty();
	
	<?php ob_start(); ?>
	
	<div id="nxs_chat_innerwrap" class="nxs-admin-wrap">
		
		<div id="nxs_chat_topquestionwrap">
		
			<div class="nxs-question-wrapper">
			
				<!-- Arrow button -->
				<div class="nxs-float-left">
					<ul class="question">
						<li><a href="#" onclick="nxs_js_supportstartchatstage2(); return false;"><span class="nxs-icon-arrow-up" /></a></li>
					</ul>
				</div>
			
				<!-- Text link -->
				<a href="#" onclick="nxs_js_supportstartchatstage2(); return false;" class="nxs-admin-font nxs-float-left nxs-width90">Need help? Ask questions here!</a>
				
				<!-- Close buttton -->
				<div>
					<a href="#" onclick="nxs_js_supportchathide(); return false;"><span class="nxs-popup-closer nxs-icon-remove-sign" /></a>
				</div>
				
			</div>
		
		</div>
	
	</div>
	
	<?php
				
	$result = ob_get_contents();
	
	$result = preg_replace( "/\r|\n/", " ", $result );
	
	ob_end_clean();
	
	?> 
	
	jQuery("#nxs_frameworkchat_wrap").append('<?php echo $result; ?>');
}

function nxs_js_supporthidechat()
{
	// empty the wrap
	jQuery("#nxs_frameworkchat_wrap").empty();
	
	<?php ob_start(); ?>
	
	<div id="nxs_chat_showchatwrap" class="nxs-admin-wrap">
		<a href="#" onclick="nxs_js_supportchatshow(); return false;"><span class="nxs-icon-support" /></a>
	</div>
	
	<?php
				
	$result = ob_get_contents();
	
	$result = preg_replace( "/\r|\n/", " ", $result );
	
	ob_end_clean();
	
	?> 
	
	jQuery("#nxs_frameworkchat_wrap").append('<?php echo $result; ?>');
}

