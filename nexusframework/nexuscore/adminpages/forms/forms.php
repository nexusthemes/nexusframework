<?php

	require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/nxsfunctions.php');
	require_once(NXS_FRAMEWORKPATH . '/nexuscore/includes/frontendediting.php');
	
	extract($_GET);
	
	// 
	
?>
	<form id='theform' method="get">
		<div id="wrap-header">
     	<h2><span class="nxs-icon-pencil2"></span><?php nxs_l18n_e("Forms", "nxs_td"); ?></h2>
      <div class="nxs-clear padding"></div>
      <div class="nxs-admin-wrap">
        <table>
          <thead>
            <tr>
              <th scope="col" class="check">
                <input type="checkbox" onchange="jQ_nxs('input[type=\'checkbox\']').prop('checked', this.checked);">
              </th>
              <th scope="col" class="nxs-title">
                <span><?php nxs_l18n_e("Title[nxs:adminpage,columnhead]", "nxs_td"); ?></span>&nbsp;
              </th>
              <!--
              <th scope="col">
                 <span><?php nxs_l18n_e("Author[nxs:adminpage,columnhead]", "nxs_td"); ?></span>
              </th>
              <th scope="col">
                  <span><?php nxs_l18n_e("Categories[nxs:adminpage,columnhead]", "nxs_td"); ?></span>
              </th>
              <th scope="col">
                  <span><?php nxs_l18n_e("Date[nxs:adminpage,columnhead]", "nxs_td"); ?></span>
              </th>
              -->
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th scope="col" class="check">
                  <input type="checkbox" onchange="jQ_nxs('input[type=\'checkbox\']').prop('checked', this.checked);">
              </th>
              <th scope="col" class="nxs-title">
                  <span><?php nxs_l18n_e("Title[nxs:adminpage,columnhead]", "nxs_td"); ?></span>&nbsp;
              </th>
            </tr>
          </tfoot>
          <tbody>
            <?php
            $requirewidgetresult = nxs_requirewidget("formbox");
            $metadata = array();
            $storageabsfolder = nxs_widgets_formbox_getstorageabsfolder($metadata);
            $fileextension = nxs_widgets_formbox_getstoragefileextension($metadata);
            $items = glob("{$storageabsfolder}/*.{$fileextension}",GLOB_BRACE);
            
            $authorslookup = array();
            $currentrow = 0;
            
            // loop over available pages
            foreach ($items as $currentitem)
            {
            	$itemtitle = basename($currentitem);
            	$itemdetailurl = home_url('/') . "?nxs_admin=admin&backendpagetype=formdetail&formname=" . $itemtitle;
            	
              $currentrow = $currentrow + 1;
              if (true)
              {
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
                    <!-- <input type="checkbox" class="multiselector page" id="page_<?php echo $postid;?>"> -->
                  </td>
                  <td>                                    
                    <strong><a href="<?php echo $itemdetailurl; ?>"><?php echo $itemtitle;?></a></strong>
                  </td>
                </tr>
                <?php
              }
            }
            ?>
          </tbody>
        </table>  	
			</div>
			<div class='padding'></div>
		
		<div style="background-color: white;">
	  	<div class='nxs-admin-wrap' style='position:static;'>
			  <div>
			  	<a href='<?php echo nxs_geturl_home(); ?>' class='nxsbutton nxs-float-right'>OK</a>
				</div>
			</div>
			<div class='padding'></div>
		</div>
		
		<!-- page is nodig voor admin deel (anders komen we niet in de theme admin pagina uit -->
		<input type="hidden" name="page" id="page" value="<?php echo $page; ?>" />
		<input type="hidden" name="backendpagetype" value="<?php echo $backendpagetype; ?>" />
		<input type="hidden" name="nxs_admin" value="<?php echo $nxs_admin; ?>" />
		
	</form>
	<?php
?>