<?php

	require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsfunctions.php');
	require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/frontendediting.php');

	extract($_GET);
?>

	<script type='text/javascript'>
		function handleMultiAction(actionValue)
		{
			if (actionValue == "-1")
			{
				nxs_js_alert("<?php nxs_l18n_e("First select an action to perform[nxs:button]", "nxs_td"); ?>");
				return;
			}
			var checkedRijen = jQuery('.multiselector.page:checked');
			var count = checkedRijen.length;
			if (count == 0)
			{
				nxs_js_alert("<?php nxs_l18n_e("First select one or more rows[nxs:button]", "nxs_td"); ?>");
			}
			else
			{
				if (actionValue == 'trash')
				{
					// geen expliciete bevestiging aangezien het item nog gestored kan worden.
					jQuery(checkedRijen).each(function(i)
					{
						var postid = this.id.split("_")[1];
						nxs_js_trash_article(postid);
					});
					// alle items zijn getrashed, refresh screen...
					nxs_js_refreshcurrentpage();
				}
				else if (actionValue == 'restore')
				{
					jQuery(checkedRijen).each(function(i)
					{
						var postid = this.id.split("_")[1];
						nxs_js_restore_article(postid);
					});
					// alle items zijn getrashed, refresh screen...
					nxs_js_refreshcurrentpage();
				}
				else if (actionValue == 'delete')
				{
					var answer = confirm("<?php nxs_l18n_e("Are you sure you want to delete all selected items?[nxs:confirm]", "nxs_td"); ?>");
					if (answer)
					{
						jQuery(checkedRijen).each(function(i)
						{
							var postid = this.id.split("_")[1];
							nxs_js_delete_article(postid);
						});
						// alle items zijn verwijderd, refresh screen...
						nxs_js_refreshcurrentpage();
					}
					else
					{
						// toch niet
					}
				}
				else
				{
					alert("deze actie is nog niet ondersteund;" + actionValue);
				}
			}
			
		}
	</script>

<?php


	
	if ($post_status == "")
	{
		$post_status = "publish";
	}
	if ($order_by == "")
	{
		$order_by = "post_date";
	}
	if ($order == "")
	{
		$order = "DESC";
	}

	
	// published / nxs_sidebar
	$publishedargs = array();
	$publishedargs["post_status"] = "publish";
	$publishedargs["post_type"] = "nxs_sidebar";
	$publishedargs["orderby"] = "post_date";//$order_by;
	$publishedargs["order"] = "DESC"; //$order;
	$publishedargs["numberposts"] = -1;	// allemaal!
  $pages = get_posts($publishedargs);
	
	// combined
	$publishedpages = array_merge($pages);
  $publishedpagescount = count($publishedpages);
  
  //
  //
  //
  
  // trashed nxs_sidebar
	$publishedargs = array();
	$publishedargs["post_status"] = "trash";
	$publishedargs["post_type"] = "nxs_sidebar";
	$publishedargs["orderby"] = "post_date";//$order_by;
	$publishedargs["order"] = "DESC"; //$order;
	$publishedargs["numberposts"] = -1;	// allemaal!
  $pages = get_posts($publishedargs);
	
	// combined
	$trashedpages = array_merge($pages);
  $trashedpagescount = count($trashedpages);

	if ($post_status == "publish")
	{
		$showpages = $publishedpages;
	}
	else if ($post_status == "trash")
	{
		$showpages = $trashedpages;
	}
	else
	{
		$showpages = $publishedpages;
	}
	
	if ($pagingrowsperpage == "")
	{
		$pagingrowsperpage = 20;
	}
	if ($pagingcurrentpage == "")
	{
		$pagingcurrentpage = 1;
	}
		
	// apply any additional filters
	
	// apply paging logic
	$totalrows = count($showpages);
	$pagingtotalpages = ceil($totalrows / $pagingrowsperpage);

	if ($pagingcurrentpage > $pagingtotalpages)
	{
		$pagingcurrentpage = 1;
	}
	
	$pagingrowstart = (($pagingcurrentpage - 1) * $pagingrowsperpage) + 1;	// bijv. 1
	$pagingrowend = $pagingcurrentpage * $pagingrowsperpage;								// bijv. 10
	
	?>
	<form id='theform' method="get">
		<div id="wrap-header">
            <h2><span class="nxs-icon-sidebar"></span><?php echo nxs_l18n__('Sidebar[nxs:heading]','nxs_td'); ?></h2>
            <div class="nxs-clear padding"></div>
            <ul class="nxs-float-left meta">
                <li><a href="#" onclick="jQuery('#post_status').val('publish'); jQuery('#theform').submit(); return false;"><span><?php nxs_l18n_e("Published[nxs:button]", "nxs_td"); ?>&nbsp;(<?php echo $publishedpagescount; ?>)</span></a>|</li> 
                <li><a href="#" onclick="jQuery('#post_status').val('trash'); jQuery('#theform').submit(); return false;"><span><?php nxs_l18n_e("Recycle bin[nxs:button]", "nxs_td"); ?>&nbsp;(<?php echo $trashedpagescount; ?>)</span></a></li>           	
            </ul>
            <div class="nxs-clear padding"></div>
            <ul class="nxs-float-left meta">
                <li>
                    <div class="nxs-float-left actions">
                        <select id='multiaction' name="multiaction">
                            <option value="-1" selected="selected"><?php nxs_l18n_e("Bulk actions[nxs:button]", "nxs_td"); ?></option>
                            <?php if ($post_status == "publish") { ?>
                            <option value="trash"><?php nxs_l18n_e("Remove to recycle bin[nxs:button]", "nxs_td"); ?></option>
                            <?php } else if ($post_status == "trash") { ?>
                            <option value="restore"><?php nxs_l18n_e("Restore[nxs:button]", "nxs_td"); ?></option>
                            <option value="delete"><?php nxs_l18n_e("Delete permanently[nxs:button]", "nxs_td"); ?></option>
                            <?php } else if ($post_status == "trash") { ?>
                            <option value="none"><?php nxs_l18n_e("None[nxs:button]", "nxs_td"); ?></option>
                            <?php } ?>
                            
                        </select>
                        <a class="nxsbutton1" href="#" onclick="var selectedValue = jQuery('#multiaction option:selected').val(); handleMultiAction(selectedValue); return false;"><?php nxs_l18n_e("Apply[nxs:button]", "nxs_td"); ?></a>
                    </div>
                </li>  
                <li>
                	<a href="#" title="Nieuwe sidebar" onclick="nxs_js_popup_site_neweditsession('nieuwsidebarhome'); return false;" class="nxsbutton1"><?php nxs_l18n_e("New sidebar[nxs:button]", "nxs_td"); ?></a>
                </li>
                </ul>
                
                <?php
                if ($pagingtotalpages > 1)
                {
                ?>
                
                <div class="nxs-pagination nxs-float-right">
                        <!--
                <span class="total">Totaal <?php echo $totalrows; ?> rijen (<?php echo $pagingtotalpages; ?> pages), we zien hier rij <?php echo $pagingrowstart; ?> t/m <?php echo $pagingrowend; ?>. Huidige paging page: <?php echo $pagingcurrentpage; ?></span>
                -->
                
                <span class="">
                    <?php if ($pagingcurrentpage > 1) { ?>
                <a class="current" href="#" onclick="jQuery('#pagingcurrentpage').val('1'); jQuery('#theform').submit(); return false;">&lt;&lt;</a>
                <?php } ?>
                <?php if ($pagingcurrentpage > 1) { ?>
                <a class="current" href="#" onclick="jQuery('#pagingcurrentpage').val('<?php echo $pagingcurrentpage - 1; ?>'); jQuery('#theform').submit(); return false;">&lt;</a>
                <?php } ?>
                <span class="">
                        <input type="text" name="manualpagingnr" id="manualpagingnr" value="<?php echo $pagingcurrentpage; ?>" size="2" onkeydown="if (event.keyCode == 13) { jQuery('#pagingcurrentpage').val(jQuery('#manualpagingnr').text()); jQuery('#theform').submit(); }" class="small2"> van <?php echo $pagingtotalpages;?>
                    </span>
                    <?php if ($pagingcurrentpage < $pagingtotalpages) { ?>
                <a class="current" href="#" onclick="jQuery('#pagingcurrentpage').val('<?php echo $pagingcurrentpage + 1; ?>'); jQuery('#theform').submit(); return false;">&gt;</a>
                <?php } ?>
                    <?php if ($pagingcurrentpage < $pagingtotalpages) { ?>
                    <a class="current" href="#" onclick="jQuery('#pagingcurrentpage').val('<?php echo $pagingtotalpages;?>'); jQuery('#theform').submit(); return false;">&gt;&gt;</a>
                  <?php } ?>
                </span>
                
            </div>
            
                <?php
                }
                ?>
                
            <div class="nxs-clear"></div>
	  </div>
	  
     	<div class="nxs-admin-wrap">
			<table>
		    <thead>
	        <tr>
	            <th scope="col" class="check">
	            	<input type="checkbox" onchange="jQuery('input[type=\'checkbox\']').attr('checked', this.checked);">
	            </th>
	            <th scope="col" class="nxs-title">
	            	<span><?php nxs_l18n_e("Title[nxs:button]", "nxs_td"); ?></span>&nbsp;
	            </th>
	            <th scope="col">
	               <span><?php nxs_l18n_e("Author[nxs:button]", "nxs_td"); ?></span>
	            </th>
	            <th scope="col">
	            	<span><?php nxs_l18n_e("Date[nxs:button]", "nxs_td"); ?></span>
	            </th>
	        </tr>
		    </thead>
		    <tfoot>
	        <tr>
	            <th scope="col" class="check">
	            	<input type="checkbox" onchange="jQuery('input[type=\'checkbox\']').attr('checked', this.checked);">
	            </th>
	            <th scope="col" class="nxs-title">
	            	<span><?php nxs_l18n_e("Title[nxs:button]", "nxs_td"); ?></span>&nbsp;
	            </th>
	            <th scope="col">
	               <span><?php nxs_l18n_e("Author[nxs:button]", "nxs_td"); ?></span>
	            </th>
	            <th scope="col">
	            	<span><?php nxs_l18n_e("Date[nxs:button]", "nxs_td"); ?></span>
	            </th>
	        </tr>
		    </tfoot>
		    <tbody>
		    	
		    		<?php
		    		
		    		$authorslookup = array();
		    		$currentrow = 0;
		    		
		    		// loop over available pages
		    		foreach ($showpages as $currentpost)
		    		{
		    			$currentrow = $currentrow + 1;
		    			if ($currentrow < $pagingrowstart || $currentrow > $pagingrowend)
		    			{
		    				// skip rows that are outside the current paging scope
		    			}
		    			else
		    			{
			    			$postid = $currentpost->ID;
			    			$postname = $currentpost->post_name;
			    			$posttitle = $currentpost->post_title;
			    			
			    			$pagemeta = nxs_get_postmeta($postid);
			    			
			    			$postdatetime = $currentpost->post_date;
		    				$postdatetimepieces = explode(" ", $postdatetime);
		    				$postdate = $postdatetimepieces[0];
		    				
		    				$authorname = "";
		    				$authorid = $currentpost->post_author;
		    				if (!array_key_exists($authorid, $authorslookup))
		    				{ 
		    					$authorname = get_userdata($authorid)->display_name;
		    					$authorslookup[$authorid] = $authorname;
		    				}
		    				else
		    				{
		    					$authorname = $authorslookup[$authorid];
		    				}
		    				
		    				$categories = get_the_category($postid);
			    			
			    			if ($currentrow % 2 == 0)
			    			{
			    				$rowalt = "class='alt'";
			    			}
			    			else
			    			{
			    				$rowalt = "";
			    			}
			    			
			    			?>
			    	
				        <tr <?php echo $rowalt;?>>
				        	<td class="check">
			            	<input type="checkbox" class="multiselector page" id="page_<?php echo $postid;?>">
			            </td>
                        <td>
														<?php if ($post_status == "publish") { ?>
                         		<span>
                         			<a href="#" title="<?php nxs_l18n_e("Delete (move to recycle bin)[nxs:button]", "nxs_td"); ?>" class='nxs-float-right nxs-margin-right10' onclick='nxs_js_trash_article("<?php echo $postid; ?>"); nxs_js_refreshcurrentpage(); return false;'>
                         				<span class='nxsiconbutton nxs-icon-trash'></span>
                         			</a>
                         		</span>
                            <?php } else if ($post_status == "trash") { ?>
                                <span>
                                	<a href="#" class='nxs-float-right nxs-margin-right10' onclick='nxs_js_delete_article("<?php echo $postid; ?>"); nxs_js_refreshcurrentpage(); return false;'>
                                		<span class='nxsiconbutton nxs-icon-lightning'></span>
                                	</a>
                                </span>
                                <span>
                                	<a href="#" class='nxs-float-right nxs-margin-right10' onclick='nxs_js_restore_article("<?php echo $postid; ?>"); nxs_js_refreshcurrentpage(); return false;'>
                                		<span class='nxsiconbutton small-restore'></span>
                                	</a>
                                </span>
                            <?php } ?>
                        
                            <?php
                            	$nxsrefurlspecial = urlencode(base64_encode(home_url('/') . "?nxs_admin=admin&backendpagetype=sidebars"));
                            	$refurl = get_home_url() . "/?nxs_sidebar=" . urlencode($postname) . "&nxsrefurlspecial=" . $nxsrefurlspecial;
                            ?>
                            <strong><a href="<?php echo $refurl; ?>"><?php echo $posttitle;?></a></strong>    
                        </td>
			            <td>
			            	<?php echo $authorname;?>
			            </td>
                        
			            <td>
			            	<span><?php echo $postdatetime;?></span>
			            </td>
				    		</tr>
		
							<?php
							}
						}
						?>
	
		    </tbody>
			</table>  	
	  	
	    <?php
	     
	    ?>
		</div>
				
		<input type="hidden" name="post_status" id="post_status" value="<?php echo $post_status; ?>" />
		<input type="hidden" name="order" id="order" value="<?php echo $order; ?>" />
		<input type="hidden" name="order_by" id="order_by" value="<?php echo $order_by; ?>" />
		<input type="hidden" name="pagingrowsperpage" id="pagingrowsperpage" value="<?php echo $pagingrowsperpage; ?>" />
		<input type="hidden" name="pagingcurrentpage" id="pagingcurrentpage" value="<?php echo $pagingcurrentpage; ?>" />
		
		<!-- page is nodig voor admin deel (anders komen we niet in de theme admin pagina uit -->
		<input type="hidden" name="page" id="page" value="<?php echo $page; ?>" />
		<input type="hidden" name="backendpagetype" value="<?php echo $backendpagetype; ?>" />
		<input type="hidden" name="nxs_admin" value="<?php echo $nxs_admin; ?>" />
				
	</form>
	<?php
?>