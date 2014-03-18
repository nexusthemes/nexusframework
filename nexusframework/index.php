<?php
	if (!defined('NXS_FRAMEWORKPATH'))
	{
		// outside context of index.php
		echo "Index.php (nexus framework raw)";
		return;
	}
	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found Index");
?>
<html>
	<head>
		<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	</head>
	<body>
		Generic 404 (Index.php / Nexus framework / Not Indexed)
	</body>
</html>

