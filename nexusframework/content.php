<?php
	// Start the Loop.
	while ( have_posts() ) 
	{
		the_post();

		/*
		 * Include the post format-specific template for the content. If you want to
		 * use this in a child theme, then include a file called called content-___.php
		 * (where ___ is the post format) and that will be used instead.
		 */
		the_content();
	}
?>