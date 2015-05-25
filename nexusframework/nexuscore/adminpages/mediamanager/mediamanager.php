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
			var checkedRijen = jQ_nxs('.multiselector.page:checked');
			var count = checkedRijen.length;
			if (count == 0)
			{
				nxs_js_alert("<?php nxs_l18n_e("First select one or more rows[nxs:button]", "nxs_td"); ?>");
			}
			else
			{
				if (actionValue == 'delete')
				{
					var answer = confirm("<?php nxs_l18n_e("Are you sure you want to delete all selected media items?[nxs:confirm]", "nxs_td"); ?>");
					if (answer)
					{
						jQ_nxs(checkedRijen).each(function(i)
						{
							var postid = this.id.split("_")[1];
							nxs_js_delete_article_no_question(postid);
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

	// add custom filters	
  $filters = array();
  

	//		
	$publishedargs = array();
  $publishedargs["numberposts"] = -1;	// allemaal!
	$publishedargs["post_type"] = "attachment";
	$publishedargs["orderby"] = $order_by;
	$publishedargs["order"] = $order;

  $publisheditems = get_posts($publishedargs);
  
  // apply runtime filters (outside SQL)
  nxs_getfilteredposts($publisheditems, $filters);  
  
  $publisheditemscount = count($publisheditems);
  
	if ($post_status == "publish")
	{
		$showitems = $publisheditems;
	}
	else
	{
		$showitems = $publisheditems;
	}
	
	if ($pagingrowsperpage == "")
	{
		$pagingrowsperpage = 999;
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
            <h2><span class="nxs-icon-article"></span><?php echo nxs_l18n__('Media items[nxs:heading]','nxs_td'); ?></h2>
            <div class="nxs-clear padding"></div>
            <ul class="nxs-float-left meta">
                <li><a href="#" onclick="jQ_nxs('#post_status').val('publish'); jQ_nxs('#theform').submit(); return false;"><span><?php nxs_l18n_e("All[nxs:button]", "nxs_td"); ?>&nbsp;(<?php echo $publisheditemscount; ?>)</span></a></li> 
              <!--
	            	media manager kent geen status (published/trashed)
                <li><a href="#" onclick="jQ_nxs('#post_status').val('trash'); jQ_nxs('#theform').submit(); return false;"><span><?php nxs_l18n_e("Recycle bin[nxs:button]", "nxs_td"); ?>&nbsp;(<?php echo $vcount; ?>)</span></a></li>           	
              -->
            </ul>
            <div class="nxs-clear padding"></div>
            <ul class="nxs-float-left meta">
                <li>
                	<div class="nxs-float-left actions">
                    	<select id='multiaction' name="multiaction">
                            <option value="-1" selected="selected"><?php nxs_l18n_e("Bulk actions[nxs:button]", "nxs_td"); ?></option>
                            <?php if ($post_status == "publish") { ?>
                            <option value="delete"><?php nxs_l18n_e("Delete permanently[nxs:button]", "nxs_td"); ?></option>
                            <?php } ?>
                    	</select>
                        <a class="nxsbutton1" href="#" onclick="var selectedValue = jQ_nxs('#multiaction option:selected').val(); handleMultiAction(selectedValue); return false;"><?php nxs_l18n_e("Apply[nxs:button]", "nxs_td"); ?></a>
                    </div>
                </li>
                <li>
                	<a href="#" title="Nieuw" onclick="nxs_js_popup_site_neweditsession('mediamanagerupload'); return false;" class="nxsbutton1"><?php nxs_l18n_e("Add media[nxs:button]", "nxs_td"); ?></a>
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
                <a class="current" href="#" onclick="jQ_nxs('#pagingcurrentpage').val('1'); jQ_nxs('#theform').submit(); return false;">&lt;&lt;</a>
                <?php } ?>
                <?php if ($pagingcurrentpage > 1) { ?>
                <a class="current" href="#" onclick="jQ_nxs('#pagingcurrentpage').val('<?php echo $pagingcurrentpage - 1; ?>'); jQ_nxs('#theform').submit(); return false;">&lt;</a>
                <?php } ?>
                <span class="">
                        <input type="text" name="manualpagingnr" id="manualpagingnr" value="<?php echo $pagingcurrentpage; ?>" size="2" onkeydown="if (event.keyCode == 13) { jQ_nxs('#pagingcurrentpage').val(jQ_nxs('#manualpagingnr').text()); jQ_nxs('#theform').submit(); }" class="small2"> van <?php echo $pagingtotalpages;?>
                    </span>
                    <?php if ($pagingcurrentpage < $pagingtotalpages) { ?>
                <a class="current" href="#" onclick="jQ_nxs('#pagingcurrentpage').val('<?php echo $pagingcurrentpage + 1; ?>'); jQ_nxs('#theform').submit(); return false;">&gt;</a>
                <?php } ?>
                    <?php if ($pagingcurrentpage < $pagingtotalpages) { ?>
                    <a class="current" href="#" onclick="jQ_nxs('#pagingcurrentpage').val('<?php echo $pagingtotalpages;?>'); jQ_nxs('#theform').submit(); return false;">&gt;&gt;</a>
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
                        <input type="checkbox" onchange="jQ_nxs('input[type=\'checkbox\']').prop('checked', this.checked);">
                    </th>
                    <th scope="col" class="preview head100">
                    	<span>Preview</span>&nbsp;
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
                        <input type="checkbox" onchange="jQ_nxs('input[type=\'checkbox\']').prop('checked', this.checked);">
                    </th>
										<th scope="col" class="preview">
                    	<span></span>&nbsp;
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
                        foreach ($showitems as $currentitem)
                        {
                            $currentrow = $currentrow + 1;
                            if ($currentrow < $pagingrowstart || $currentrow > $pagingrowend)
                            {
                                // skip rows that are outside the current paging scope
                            }
                            else
                            {
                                $postid = $currentitem->ID;
                                $postname = $currentitem->post_name;
                                $posttitle = $currentitem->post_title;
                                
                                $pagemeta = nxs_get_postmeta($postid);
                                
                                $postdatetime = $currentitem->post_date;
                                $postdatetimepieces = explode(" ", $postdatetime);
                                $postdate = $postdatetimepieces[0];
                                
                                $mimetype = $currentitem->post_mime_type;
                                
                                $authorname = "";
                                $authorid = $currentitem->post_author;
                                if (!array_key_exists($authorid, $authorslookup))
                                { 
                                    $authorname = get_userdata($authorid)->display_name;
                                    $authorslookup[$authorid] = $authorname;
                                }
                                else
                                {
                                    $authorname = $authorslookup[$authorid];
                                }
                                
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
                            		<?php 
                            		if (nxs_stringstartswith($mimetype, "image")) 
                            		{
                            			// preview plaatje
      			                      $lookup = wp_get_attachment_image_src($currentitem->ID, 'thumbnail', true);
						                      $url = $lookup[0];
						                      $url = nxs_img_getimageurlthemeversion($url);
						                      ?>
                                  <img src='<?php echo $url;?>' class="nxs-preview-thumbnail" />
																	<?php 
																} 
																else if (nxs_stringstartswith($mimetype, "application/pdf")) 
																{ 
																		?>
																		<img src="<?php echo nxs_getframeworkurl(); ?>/images/previewpdf.png" class="nxs-preview-thumbnail" />
																		<?php 
																} 
																else if (nxs_stringstartswith($mimetype, "audio")) 
																{ 
																	?>
																	<img src="<?php echo nxs_getframeworkurl(); ?>/images/previewaudio.png" class="nxs-preview-thumbnail" />
																	<?php
                            		} 
                            		else 
                            		{ 
                            			?>
                            			<span class="nxs-title"></span>
                            			<?php 
                            			} 
                            		?>
                            	</td>
                            	<td>   
                                <?php if ($post_status == "publish") { ?>
                                	<span>
                                		<a href="#" title="<?php nxs_l18n_e("Delete (move to recycle bin)[nxs:button]", "nxs_td"); ?>" class='nxs-float-right nxs-margin-right10' onclick='nxs_js_trash_article("<?php echo $postid; ?>"); nxs_js_refreshcurrentpage(); return false;'>
                                			<span class='nxsiconbutton nxs-icon-trash'></span>
                                		</a>
                                	</span>
                                <?php } ?>	
                                                                
                                <?php 
                                if (nxs_stringstartswith($mimetype, "image")) 
                                {
                                	$lookup = wp_get_attachment_image_src($currentitem->ID, 'full', true);
						                      $url = $lookup[0];
						                      $url = nxs_img_getimageurlthemeversion($url);
                                }
                                else if (nxs_stringstartswith($mimetype, "application/pdf")) 
																{ 
																	$url = wp_get_attachment_url($currentitem->ID);
																}
																else
																{
																	$url = wp_get_attachment_url($currentitem->ID);
																}
																
																$titel = $posttitle;
																if ($titel == "")
																{
																	$titel = "(geen titel)";
																}
																
																?>

                                <strong><a target="_blank" title='Open in nieuw venster' href="<?php echo $url; ?>"><?php echo $titel;?></a></strong><br />
                                <span class="nxs-title"><?php echo $mimetype;?></span><br />																
																
																<div class='nxs-clear'></div>
																
																<a href='#' class='nxsbutton1 nxs-float-left' onclick='nxs_js_copytoclipboard("<?php echo $url; ?>")'>Url</a>
                                
                               
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
		
		<div class='padding'></div>
		
		<div style="background-color: white;">
	  	<div class='nxs-aligncenter960 nxs-admin-wrap' style='position:static;'>
			  <div>
			  	<a href='<?php echo nxs_geturl_home(); ?>' class='nxsbutton nxs-float-right'>OK</a>
				</div>
			</div>
			<div class='padding'></div>
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