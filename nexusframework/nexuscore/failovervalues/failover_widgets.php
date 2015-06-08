<?php
	function nxs_failover_widgets_undefined_getvalue($args)
	{
		$result = array();
		
		$expirationtimeinseconds = 60;
		
		nxs_ob_start();
		?>
		
		<?php

		$html = nxs_ob_get_contents();
		nxs_ob_end_clean();

		$result["html"] = $html;
		$result["transientduration"] = $expirationtimeinseconds;
		
		return $result;
	}
?>