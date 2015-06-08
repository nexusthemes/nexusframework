<?php
	function nxs_failover_help_start_getvalue($args)
	{
		$result = array();
		
		$expirationtimeinseconds = 60;
		
		nxs_ob_start();
		?>
		<div class='content2'>
			<div class='box'>
				<div class='box-title'>
					<h4>Nexus Themes</h4>
				</div>
				<div class='box-content'>
					<a href='http://nexusthemes.com/?camp=failoverhelp' target="_blank">Online help</a>
					<div class='nxs-clear'></div>
				</div>
			</div>
			<div class='nxs-clear margin'></div>
		</div>
		<div class='nxs-clear'></div>					

		<?php

		$html = nxs_ob_get_contents();
		nxs_ob_end_clean();

		$result["html"] = $html;
		$result["transientduration"] = $expirationtimeinseconds;
		
		return $result;
	}
?>