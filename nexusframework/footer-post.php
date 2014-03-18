<?php
	global $nxs_global_current_containerpostid_being_rendered;
	$postid = $nxs_global_current_containerpostid_being_rendered;

	$meta = nxs_get_postmeta($postid);
	
	if (nxs_hastemplateproperties())
	{
		// derive the layout
		$templateproperties = nxs_gettemplateproperties();
		if ($templateproperties["result"] == "OK")
		{
			$existingfooterid = $templateproperties["footer_postid"];
		}
		else
		{
			$existingfooterid = 0;
		}
	}
	else
	{
		$existingfooterid = $meta["footer_postid"];
	}
	
	
	$iswidescreen = nxs_iswidescreen("footer");
	if ($iswidescreen)
	{
		$widescreenclass = "nxs-widescreen";
	}
	else
	{
		$widescreenclass = "";
	}

	if ($existingfooterid != "")
	{
		$cssclass = nxs_getcssclassesforrowcontainer($existingfooterid);
	}
	else
	{
		$cssclass = "";
	}

	if ($existingfooterid != "")
	{
		?>
		<div id="nxs-footer" class="nxs-containsimmediatehovermenu nxs-sitewide-element <?php echo $widescreenclass; ?>">
	    <div id="nxs-footer-container" class="nxs-sitewide-container nxs-footer-container nxs-elements-container nxs-layout-editable nxs-widgets-editable nxs-post-<?php echo $existingfooterid . " " . $cssclass; ?>">
				<?php 
				if ($existingfooterid != "")
				{
					echo nxs_getrenderedhtmlincontainer($postid, $existingfooterid, "default");
				}
				do_action("nxs_action_postfooterlink");
	      ?>
	    </div>
		</div> <!-- end #nxs-footer -->	
		<?php
	}	
	?>			
	</div> <!-- end #nxs-container -->
	<?php get_template_part('includes/scripts'); ?>
	<?php wp_footer(); ?>
	</body>
</html>