<?php
function nxs_webmethod_exportmedia() 
{	
	extract($_REQUEST);
	
	if ($postid == "")
	{
		// 
		nxs_webmethod_return_nack("postid not set #247");
	}
	
	$filepath = get_attached_file($postid, true);	
	if (!$filepath)
	{
		echo "path not found";
		exit;
	}
	
	if ( ! function_exists('readfile_chunked')) 
	{
    function readfile_chunked($file, $retbytes=TRUE) {
    
		$chunksize = 1 * (1024 * 1024);
		$buffer = '';
		$cnt = 0;
		
		$handle = fopen($file, 'r');
		if ($handle === FALSE) return FALSE;
				
		while (!feof($handle)) :
		   $buffer = fread($handle, $chunksize);
		   echo $buffer;
		   ob_flush();
		   flush();
		
		   if ($retbytes) $cnt += strlen($buffer);
		endwhile;
		
		$status = fclose($handle);
		
		if ($retbytes AND $status) return $cnt;
		
		return $status;
    }
	}
	
	header('Content-type: application/force-download');	// force popup
	header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
	header("Content-Transfer-Encoding: binary");
	
	if ($size = @filesize($filepath)) 
	{
		header("Content-Length: ".$size);
	}
	
	@readfile_chunked("$filepath") or wp_die("Whoops, unable to download attachment");
	
	die();
}
?>