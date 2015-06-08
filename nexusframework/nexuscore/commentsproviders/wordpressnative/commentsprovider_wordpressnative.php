<?php

function nxs_commentsprovider_wordpressnative_gettitle()
{
	return nxs_l18n__("WordPressNative[nxs:commentsprovidertitle]", "nxs_td");
}

function nxs_commentsprovider_wordpressnative_geticonid()
{
	return "nxs-icon-nativecomments";
}


function nxs_commentsprovider_wordpressnative_getflyoutmenuhtml()
{
		// Turn on output buffering
	nxs_ob_start();
	?>
			<li class="nxs-hidewheneditorinactive">
				<a href="<?php echo home_url('/'); ?>?nxs_admin=admin&backendpagetype=comments" title="<?php nxs_l18n_e("Comments[nxs:adminmenu,tooltip]", "nxs_td"); ?>" class="site">
					<span class='nxs-icon-comments'></span>
				</a>
			</li>
	<?php
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	return $result;
}

// invoked by for example the wordpresstitle widget
function nxs_commentsprovider_wordpressnative_getpostcommentcounthtml($args)
{
	// preconditions
	if (!isset($args["postid"])){ nxs_webmethod_return_nack("postid not set"); }
	
	//
	$postid = $args["postid"];
	$count = get_comments_number($postid);

	// Turn on output buffering
	nxs_ob_start();
	?>
	<span class="nxs-icon-comments-2">
		<span class="nxs-comments-count"><?php echo $count; ?></span>
    </span>
	<?php
	$result = nxs_ob_get_contents();
	nxs_ob_end_clean();
	
	return $result;	
}
?>
