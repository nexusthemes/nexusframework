<?php
	function nxs_failover_widgets_undefined_getvalue($args)
	{
		$result = array();
		
		$expirationtimeinseconds = 60;
		
		ob_start();
		?>
		
		<?php

		$html = ob_get_contents();
		ob_end_clean();

		$result["html"] = $html;
		$result["transientduration"] = $expirationtimeinseconds;
		
		return $result;
	}
?>