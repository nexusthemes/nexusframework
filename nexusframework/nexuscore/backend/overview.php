<?php
	if (true) // nxs_hassitemeta())
	{
		function nxs_backend_addmetaboxesgeneric($allboxes, $where)
		{
			global $nxs_global_overviewservicevalue;
			
			$boxpieces = explode(';', $allboxes);
			foreach ($boxpieces as $currentbox) 
		  {
		  	//echo $currentbox;
		  	if ($currentbox != "")
		  	{
			  	$boxid = $nxs_global_overviewservicevalue[$currentbox . '_htmlid'];
		  		$boxtitle = $nxs_global_overviewservicevalue[$currentbox . '_title'];
		  		$callbackparams = $currentbox;
		  		$html = $nxs_global_overviewservicevalue[$currentbox . '_html'];
		  		
		  		//echo $boxid;
		  		//echo $html;
		  		//echo $boxtitle;
		  		
		    	add_meta_box($boxid, $boxtitle, 'nxs_backend_addmetaboxgeneric', 'nxs_backend_overview', $where, 'core', $callbackparams);
		    }
		  }
		}
	  
		function nxs_backend_addmetaboxgeneric($post, $foo)
		{		
			global $nxs_global_overviewservicevalue;
	
			$metaboxid = $foo["id"];
			echo $nxs_global_overviewservicevalue[$metaboxid . "_html"];
		}

		global $nxs_global_overviewservicevalue;
		$nxs_global_overviewservicevalue = nxs_gettransientnexusservervalue("themespage", "overview", array());

		$allboxes = $nxs_global_overviewservicevalue["leftboxes"];
		nxs_backend_addmetaboxesgeneric($allboxes, 'left');
		
		$allboxes = $nxs_global_overviewservicevalue["rightboxes"];
		nxs_backend_addmetaboxesgeneric($allboxes, 'right');
	  
		?>
		<div class="wrap">
			<h2><?php nxs_l18n_e("Nexus Theme Overview", "nxs_td"); ?></h2>		
			<div id="dashboard-widgets-container">
				<div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" id="main-container" style="width:75%;">
								<?php do_meta_boxes('nxs_backend_overview', 'left', ''); ?>
							</div>
			    		<div class="postbox-container" id="side-container" style="width:24%;">
								<?php do_meta_boxes('nxs_backend_overview', 'right', ''); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
		<?php
	}
	else
	{
		// do nothing
	}
?>