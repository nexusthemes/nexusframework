<?php
	extract($_GET);
	
	$formsurl = home_url('/') . "?nxs_admin=admin&backendpagetype=forms";

	$formidentifier = $formname;
	
	// 
	$requirewidgetresult = nxs_requirewidget("formbox");
	$storageabsfolder = nxs_widgets_formbox_getstorageabsfolder($metadata);
	$storageabspath = $storageabsfolder . "/" . $formname;
	
	if ($_REQUEST["action"] == "deleteform")
	{
		if (!isset($_GET['nxs_nonce']) || !wp_verify_nonce($_GET['nxs_nonce'], 'deleteform')) 
		{
			nxs_webmethod_return_nack("not allowed");
		}
		
		// file_put_contents($storageabspath, ""); // empty existing CSV
		if (file_exists($storageabspath))
		{
			$r = unlink($storageabspath);
			if (!$r)
			{
				nxs_webmethod_return_nack("failed. no write access?");
			}
		}
		else
		{
			// file no longer exists, skip
		}

		$extentionstart = strpos($formidentifier, ".php");
		$formidentifier = substr($formidentifier, 0, $extentionstart);
		$metadata = array($formidentifier);
		$fileuploadstorageabsfolder = nxs_widgets_formbox_getfileuploadstorageabsfolder($metadata);

		if (file_exists($fileuploadstorageabsfolder))
		{
			$files = glob($fileuploadstorageabsfolder . '*', GLOB_MARK);
		    foreach ($files as $file) {
		        unlink($file);
		    }
			$r = rmdir($fileuploadstorageabsfolder);
			if (!$r)
			{
				nxs_webmethod_return_nack("failed. no write access?");
			}
		}
		else
		{
			// file no longer exists, skip
		}

		// no matter the result, we will navigate to the Forms list page
		?>
		<script>
			nxs_js_redirect("<?php echo $formsurl; ?>");
		</script>
		<?php
		die();
	}
	
	$currenturl = nxs_geturlcurrentpage();
	$downloadlink = $currenturl;
	$downloadlink = nxs_addqueryparametertourl_v2($downloadlink, "action", "downloadcsv", true, true);
	$downloadlink = nxs_addqueryparametertourl_v2($downloadlink, "adminheader", "off", true, true);
	
	$deletelink = $currenturl;
	$deletelink = nxs_addqueryparametertourl_v2($deletelink, "action", "deleteform", true, true);
	$deletelink = wp_nonce_url($deletelink, "deleteform", "nxs_nonce");
	
	
	
	function nxs_forms_parseheaders($contents)
	{
		$result = array();
		
		foreach (explode("\r\n", $contents) as $row) 
		{
			if ($row != "")
			{
				$rowparts = explode(";", $row);
				$isheader = count($rowparts) == 1;
  			if ($isheader)
  			{
  				$colspan = "99";
  			}			
  			else
  			{
  				$colspan = "1";
  			}
				foreach (explode(";", $row) as $colinrow) 
    		{
    			$parts = explode(":", $colinrow, 2);
    			if (count($parts) == 2)
    			{
    				$header = $parts[0];
    				if (!in_array($header, $result))
    				{
    					$result[] = $header;
    				}
    			}
    		}
			}
		}
		
		return $result;
	}
	
	function nxs_forms_parserow($row, $headers)
	{
		$result = array();
		foreach (explode(";", $row) as $colinrow) 
		{
			$parts = explode(":", $colinrow, 2);
			if (count($parts) == 2)
			{
				$result[$parts[0]] = $parts[1];
			}
		}
		
		return $result;
	}
	
	if ($_REQUEST["action"] == "downloadcsv")
	{	
		nxs_saveobclean();
		header('Content-type: text/plain');
		header('Content-disposition: attachment; filename="' . $formname .'"');
		
		$delimiter = ",";
		
		if (file_exists($storageabspath))
 		{
 			$contents = file_get_contents($storageabspath);
 			$headers = nxs_forms_parseheaders($contents);
 			// output headers
	 		foreach ($headers as $currentheader)
	 		{
	 			echo $currentheader . $delimiter;
	 		}
	 		echo "\r\n";
	 		// line indicating end of header
	 		echo "\r\n";
			// output rows 			
 			foreach (explode("\r\n", $contents) as $row) 
  		{
  			if ($row != "")
  			{
  				$rowparts = explode(";", $row);
					$isheader = count($rowparts) == 1;
    			$valuepercolumn = nxs_forms_parserow($row, $headers);
    			foreach ($headers as $currentheader)
        	{
        		echo trim($valuepercolumn[$currentheader]);
        		echo $delimiter;
        	}
        	echo "\r\n";
        }
      }
 		}
 		else
 		{
 			echo "not found";
 		} 	
 		
		// stop further handling
		die();
	}
	
	?>
	<?php
	if (file_exists($storageabspath))
 		{
 			$contents = file_get_contents($storageabspath);
 			$headers = nxs_forms_parseheaders($contents);
 			?>		
		 	<form id='theform' method="get">
				<div id="wrap-header">
		     	<h2><span class="nxs-icon-pencil2"></span><a href='<?php echo $formsurl; ?>'><?php nxs_l18n_e("Forms", "nxs_td"); ?></a> &gt; <?php echo $formname;?></h2>
		      <div class="nxs-clear padding"></div>
		     	<a href="<?php echo $downloadlink; ?>" target="_blank" download class="nxsbutton">Download</a>
		     	<a href="<?php echo $deletelink; ?>" onclick="var answer = confirm('Sure?'); return answer;" class="nxsbutton2">Delete</a>
		      <div class="nxs-clear padding"></div>
		      <div class="nxs-admin-wrap">
		        <table>
		          <thead>
		            <tr>
		            	<th scope="col" class="nxs-title">
	                	<span class="nxs-margin-left15">#</span>
	              	</th>
		            	<?php
		            	$isfirst = false;
		            	foreach ($headers as $currentheader)
		            	{
		            		if ($isfirst)
		            		{
		            			$isfirst = false;
		            			$spanclass = "nxs-margin-left15";
		            		}
		            		else
		            		{
		            			$spanclass = "";
		            		}
		            		?>
		            		<th scope="col" class="nxs-title">
		                	<span class="<?php echo $spanclass; ?>"><?php echO $currentheader; ?></span>
		              	</th>
		              	<?php
		            	}
		            	?>
		            </tr>
		          </thead>
		          <tfoot>
		            <tr>
		            	<th scope="col" class="nxs-title">
	                	<span class="nxs-margin-left15">#</span>
	              	</th>
									<?php
									$isfirst = false;
		            	foreach ($headers as $currentheader)
		            	{
		            		if ($isfirst)
		            		{
		            			$isfirst = false;
		            			$spanclass = "nxs-margin-left15";
		            		}
		            		else
		            		{
		            			$spanclass = "";
		            		}
		            		?>
		            		<th scope="col" class="nxs-title">
		                	<span class="<?php echo $spanclass; ?>"><?php echO $currentheader; ?></span>
		              	</th>
		              	<?php
		            	}
		            	?>
		            </tr>
		          </tfoot>
		          <tbody>
		            <?php
		            $index = 0;
		            ?>
		            <?php
								foreach (explode("\r\n", $contents) as $row) 
			      		{
			      			if ($row != "")
			      			{
				      			$index++;		      				
			      				$rowparts = explode(";", $row);
										$isheader = count($rowparts) == 1;
		      					?>
				      			<tr style='background-color: green; border-color: black; border-style: solid;'>
				      			<?php
			      				?>
				      			<tr style='border-color: black; border-style: solid;'>
				      			<?php  		
				      			if ($isheader)
				      			{
				      				$colspan = "99";
				      			}			
				      			else
				      			{
				      				$colspan = "1";
				      			}
				      			$isfirst = false;
				      			$valuepercolumn = nxs_forms_parserow($row, $headers);
				      			?>
				      			<td>
		                	<span class="nxs-margin-left15"><?php echo $index; ?></span>
		              	</td>
				      			<?php
				      			foreach ($headers as $currentheader)
			            	{
			            		if ($isfirst)
			            		{
			            			$isfirst = false;
			            			$spanclass = "nxs-margin-left15";
			            		}
			            		else
			            		{
			            			$spanclass = "";
			            		}
			            		$currentvalue = $valuepercolumn[$currentheader];
			            		?>
			            		<td>
			                	<span class="<?php echo $spanclass; ?>"><?php echo $currentvalue; ?></span>
			              	</td>
			              	<?php
			            	}
			      			}
			      		}
								?>
							</tbody>
						</table>
					</div>
 				</div>
 			</form>
 			<?php
 		}
 		else
 		{
 			?>
 			No output file found (check permissions?)
 			<?php
 		}
?>