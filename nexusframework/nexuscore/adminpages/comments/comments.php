<?php

	require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsfunctions.php');
	do_action("nxs_render_frontendeditor");

	extract($_GET);
?>

	<script>
		function handleMultiAction(actionValue)
		{
			if (actionValue == "-1")
			{
				nxs_js_alert('<?php nxs_l18n_e("Please select an action first[nxs:column,heading]", "nxs_td");?>');
				return;
			}
			var checkedRijen = jQ_nxs('.multiselector.page:checked');
			var count = checkedRijen.length;
			if (count == 0)
			{
				nxs_js_alert('<?php nxs_l18n_e("First select one or more rows[nxs:column,heading]", "nxs_td");?>');
			}
			else
			{
				if (actionValue == 'delete')
				{
					// geen expliciete bevestiging aangezien het item nog gestored kan worden.
					jQ_nxs(checkedRijen).each(function(i)
					{
						var postid = this.id.split("_")[1];
						var commentid = this.id.split("_")[2];
						nxs_js_removecomment(postid, commentid, function() { nxs_js_alert('<?php nxs_l18n_e("Comment was removed[nxs:column,heading]", "nxs_td");?> (' + commentid + ')'); }, function () {});
					});
					// alle items zijn verwijderd, refresh screen...
					nxs_js_refreshcurrentpage();
				}
				else if (actionValue == 'restore')
				{
					jQ_nxs(checkedRijen).each(function(i)
					{
							var postid = this.id.split("_")[1];
							var commentid = this.id.split("_")[2];
							nxs_js_approvecomment(postid, commentid, function() { nxs_js_alert('<?php nxs_l18n_e("Comment restored[nxs:column,heading]", "nxs_td");?> (' + commentid + ')'); }, function () {});
					});
					nxs_js_refreshcurrentpage();
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
		$post_status = "hold";
	}
	if ($order_by == "")
	{
		$order_by = "post_date";
	}
	if ($order == "")
	{
		$order = "DESC";
	}

	// published pages
	$args = array
	(
		"status" => 'hold',
	);
  $posts = get_comments($args);
	
	// published combined
	$holdpages = $posts;
  $holdpagescount = count($holdpages);
  
  	// all pages
	$args = array
	(
	);
  $posts = get_comments($args);
	
	// published combined
	$allpages = $posts;
  $allpagescount = count($allpages);
  
  //
  //
  //
  
	if ($post_status == "any")
	{
		$showcomments = $allpages;
	}
	else if ($post_status == "hold")
	{
		$showcomments = $holdpages;
	}
	else
	{
		$showcomments = $holdpages;
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
            <h2><span class="nxs-icon-comments"></span><?php nxs_l18n_e("Blog comments[nxs:heading]", "nxs_td"); ?></h2>
            <div class="nxs-clear padding"></div>
            <ul class="nxs-float-left meta">
                <li><a href="#" onclick="jQ_nxs('#post_status').val('any'); jQ_nxs('#theform').submit(); return false;"><span><?php nxs_l18n_e("All[nxs:linkbutton]", "nxs_td"); ?>&nbsp;(<?php echo $allpagescount; ?>)</span></a>|</li> 
                <li><a href="#" onclick="jQ_nxs('#post_status').val('hold'); jQ_nxs('#theform').submit(); return false;"><span><?php nxs_l18n_e("Awaiting moderation[nxs:linkbutton]", "nxs_td"); ?>&nbsp;(<?php echo $holdpagescount; ?>)</span></a></li> 
            </ul>
            <div class="nxs-clear padding"></div>
            <ul class="nxs-float-left meta">
                <li>
                	<div class="nxs-float-left actions">
                    	<select id='multiaction' name="multiaction">
                            <option value="-1" selected="selected"><?php nxs_l18n_e("Bulk actions[nxs:ddl]", "nxs_td"); ?></option>
                            <?php if ($post_status == "hold") { ?>
														<option value="restore"><?php nxs_l18n_e("Allow[nxs:heading]", "nxs_td"); ?></option>
                            <option value="delete"><?php nxs_l18n_e("Remove permanently[nxs:ddl]", "nxs_td"); ?></option>                            
                            <?php } else if ($post_status == "any") { ?>
                            <option value="delete"><?php nxs_l18n_e("Remove permanently[nxs:ddl]", "nxs_td"); ?></option>
                            <?php } ?>
                    	</select>
                        <a class="nxsbutton1" href="#" onclick="var selectedValue = jQ_nxs('#multiaction option:selected').val(); handleMultiAction(selectedValue); return false;"><?php nxs_l18n_e("Apply[nxs:button]", "nxs_td"); ?></a>
                    </div>
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
                    <th scope="col">
                        <span><?php nxs_l18n_e("Comment[nxs:column,heading]", "nxs_td"); ?></span>&nbsp;
                    </th>
                    <th scope="col">
                        <span><?php nxs_l18n_e("Author[nxs:column,heading]", "nxs_td"); ?></span>
                    </th>
                    <th scope="col">
                        <span><?php nxs_l18n_e("Date[nxs:column,heading]", "nxs_td"); ?></span>
                    </th>
                    <th scope="col">
                       <span><?php nxs_l18n_e("Article[nxs:column,heading]", "nxs_td"); ?></span>
                    </th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th scope="col" class="check">
                        <input type="checkbox" onchange="jQ_nxs('input[type=\'checkbox\']').prop('checked', this.checked);">
                    </th>
                    <th scope="col">
                        <span><?php nxs_l18n_e("Comment[nxs:column,heading]", "nxs_td"); ?></span>&nbsp;
                    </th>
                    <th scope="col">
                        <span><?php nxs_l18n_e("Author[nxs:column,heading]", "nxs_td"); ?></span>
                    </th>
                    <th scope="col">
                        <span><?php nxs_l18n_e("Date[nxs:column,heading]", "nxs_td"); ?></span>
                    </th>
                    <th scope="col">
                       <span><?php nxs_l18n_e("Article[nxs:column,heading]", "nxs_td"); ?></span>
                    </th>
                </tr>
                </tfoot>
                <tbody>
                    
                        <?php
                        
                        $authorslookup = array();
                        $currentrow = 0;
                        
                        // loop over available pages
                        foreach ($showcomments as $currentcomment)
                        {
                            $currentrow = $currentrow + 1;
                            if ($currentrow < $pagingrowstart || $currentrow > $pagingrowend)
                            {
                                // skip rows that are outside the current paging scope
                            }
                            else
                            {
                            	$comment = $currentcomment->comment_content;
                            	$commentid = $currentcomment->comment_ID;
                              $postid = $currentcomment->comment_post_ID;
                              $url = nxs_geturl_for_postid($postid);
                              
                              $postname = nxs_getslug_for_postid($postid);
                              $posttitle = nxs_gettitle_for_postid($postid);
                              if ($posttitle == "")
                              {
                              	$posttitle = "(leeg, id:" . $postid . ")";
                              }
                              
                              $pagemeta = nxs_get_corepostmeta($postid);
                              
                              $auteur = $currentcomment->comment_author . " / " . $currentcomment->comment_author_email . " / " . $currentcomment->comment_author_url;
                              
                              $postdatetime = $currentcomment->comment_date;
                              $postdatetimepieces = explode(" ", $postdatetime);
                              $postdate = $postdatetimepieces[0];
                              
                              $current_state = $currentcomment->comment_approved;
                              
                              $authorname = "";
                              $authorid = $currentcomment->post_author;
                              if (!array_key_exists($authorid, $authorslookup))
                              { 
                                  $authorname = get_userdata($authorid)->display_name;
                                  $authorslookup[$authorid] = $authorname;
                              }
                              else
                              {
                                  $authorname = $authorslookup[$authorid];
                              }
                              
															// add custom filters	
														  $categoriesfilters = array();
														  $categoriesfilters["uncategorized"] = "skip";
                              
                              $categories = get_the_category($postid);
                              nxs_getfilteredcategories($categories, $categoriesfilters);
                              
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
			                                <input type="checkbox" class="multiselector page" id="comment_<?php echo $postid;?>_<?php echo $commentid;?>">
			                            </td>
			                            <td>
																		<?php if ($current_state == 0) { ?>
																			<!-- on hold -->
			                                <span>
			                                	<a href="#" title="<?php nxs_l18n_e("Delete permanently[nxs:tooltip]", "nxs_td");?>" class='nxs-float-right nxs-margin-right10' onclick='nxs_js_removecomment(<?php echo $postid; ?>, <?php echo $commentid; ?>, function() { nxs_js_refreshcurrentpage(); }, function () {}); return false;'>
			                                		<span class='nxsiconbutton nxs-icon-lightning'></span>
			                                	</a>
			                                </span>
			                                <span>
			                                	<a href="#" title="<?php nxs_l18n_e("Allow[nxs:tooltip]", "nxs_td");?>" class='nxs-float-right nxs-margin-right10' onclick='nxs_js_approvecomment(<?php echo $postid; ?>, <?php echo $commentid; ?>, function() { nxs_js_refreshcurrentpage(); }, function () {}); return false;'>
			                                		<span class='nxsiconbutton nxs-icon-checkmark'></span>
			                                	</a>
			                                </span>
		                                <?php } else if ($current_state == 1) { ?> 
																			<!-- approved -->                
			                                <span>
			                                	<a href="#" title="<?php nxs_l18n_e("Delete permanently[nxs:tooltip]", "nxs_td");?>" class='nxs-float-right nxs-margin-right10' onclick='nxs_js_removecomment(<?php echo $postid; ?>, <?php echo $commentid; ?>, function() { nxs_js_refreshcurrentpage(); }, function () {}); return false;'>
			                                		<span class='nxsiconbutton nxs-icon-lightning'></span>
			                                	</a>
			                                </span>
		                                <?php } else if ($current_state == 'spam') { ?> 
																			<!-- spam -->                
			                                <span>
			                                	<a href="#" title="<?php nxs_l18n_e("Delete permanently[nxs:tooltip]", "nxs_td");?>" class='nxs-float-right nxs-margin-right10' onclick='nxs_js_removecomment(<?php echo $postid; ?>, <?php echo $commentid; ?>, function() { nxs_js_refreshcurrentpage(); }, function () {}); return false;'>
			                                		<span class='nxsiconbutton nxs-icon-lightning'></span>
			                                	</a>
			                                </span>
		                                <?php } ?>				                            	
			                            	
			                              <?php echo nxs_htmlescape($comment);?>
			                            </td>
			                            <td>
			                                <span><?php echo $auteur;?></span>
			                            </td>
			                            <td>
			                                <span><?php echo $postdatetime;?></span>
			                            </td>		                            
			                            <td>                                    
			                                <strong><a href="<?php echo $url; ?>"><?php echo $posttitle;?></a></strong>
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