<?php

// see https://gist.github.com/samikeijonen/8692631
// see http://gh.codehum.com/shoestrap/shoestrap-updater
// see http://pastebin.com/VRn6rrCt
// see https://github.com/sumobi/shop-front/tree/master/includes
// see https://github.com/sumobi/shop-front/blob/master/includes/EDD_SL_Theme_Updater.php
// see https://github.com/sumobi/Easy-Digital-Downloads
// see https://github.com/easydigitaldownloads/Easy-Digital-Downloads
// see also https://github.com/churchthemes/church-theme-framework/blob/master/includes/admin/edd-license.php

define('NXS_UPDATESERVER_URL', 'http://license.nexusthemes.com');

$sitemeta = nxs_getsitemeta();

define('NXS_LICENSE_THEME', $catitem_themeid);
define('NXS_LICENSE_OPTION_KEY', 'NXS_LICENSE');
define('NXS_LICENSE_STATUS_OPTION_KEY', 'NXS_LICENSE_STATUS');
 
if( !class_exists( 'NXS_Theme_Updater' ) ) 
{
  class NXS_Theme_Updater 
  {
    private $remote_api_url;
    private $request_data;
    private $response_key;
    private $theme_slug;
    private $license_key;
    private $version;
    private $author;

    function __construct( $args = array() ) 
    {
      $request_data = array();
      $theme_slug = get_template();
      $theme = wp_get_theme( sanitize_key( $theme_slug ) );
			$version = $theme->get( 'Version' );
      $author = "Nexus Themes";

      $this->license        = trim(get_option(NXS_LICENSE_OPTION_KEY));
      $this->item_name      = NXS_LICENSE_THEME;
      $this->version        = $theme->get('Version');
      $this->theme_slug     = sanitize_key( $theme_slug );
      $this->author         = $author;
      $this->remote_api_url = NXS_UPDATESERVER_URL;
      $this->response_key   = $this->theme_slug . '-update-response';

      add_filter( 'site_transient_update_themes', array( &$this, 'theme_update_transient' ) );
      add_filter( 'delete_site_transient_update_themes', array( &$this, 'delete_theme_update_transient' ) );
      add_action( 'load-update-core.php', array( &$this, 'delete_theme_update_transient' ) );
      add_action( 'load-themes.php', array( &$this, 'delete_theme_update_transient' ) );
      add_action( 'load-themes.php', array( &$this, 'load_themes_screen' ) );
    }

    function load_themes_screen() 
    {
	    add_thickbox();
	    add_action( 'admin_notices', array( &$this, 'update_nag' ) );
    }

    function update_nag() 
    {
      $theme = wp_get_theme( $this->theme_slug );

      $api_response = get_transient( $this->response_key );

      if( false === $api_response )
              return;

      $update_url = wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( $this->theme_slug ), 'upgrade-theme_' . $this->theme_slug );
      $update_onclick = ' onclick="if ( confirm(\'' . esc_js( __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update." ) ) . '\') ) {return true;}return false;"';

      if ( version_compare( $this->version, $api_response->new_version, '<' ) ) {

              echo '<div id="update-nag">';
                      printf( '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.',
                              $theme->get( 'Name' ),
                              $api_response->new_version,
                              '#TB_inline?width=640&amp;inlineId=' . $this->theme_slug . '_changelog',
                              $theme->get( 'Name' ),
                              $update_url,
                              $update_onclick
                      );
              echo '</div>';
              echo '<div id="' . $this->theme_slug . '_' . 'changelog" style="display:none;">';
                      echo wpautop( $api_response->sections['changelog'] );
              echo '</div>';
      }
    }

    function theme_update_transient( $value ) 
    {
      $update_data = $this->check_for_update();
      if ( $update_data ) {
              $value->response[ $this->theme_slug ] = $update_data;
      }
      return $value;
    }

    function delete_theme_update_transient() 
    {
    	delete_transient( $this->response_key );
    }

    function check_for_update() 
    {
      $theme = wp_get_theme( $this->theme_slug );

      $update_data = get_transient( $this->response_key );
      if ( false === $update_data ) {
              $failed = false;

              if( empty( $this->license ) )
                      return false;

              $api_params = array(
                      'nxs_license_action'    => 'get_version',
                      'license'               => $this->license,
                      'name'                  => $this->item_name,
                      'slug'                  => $this->theme_slug,
                      'author'                => $this->author,
                      'url'           => home_url()
              );

              $response = wp_remote_post( $this->remote_api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

              // make sure the response was successful
              if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
                      $failed = true;
              }

              $update_data = json_decode( wp_remote_retrieve_body( $response ) );

              if ( ! is_object( $update_data ) ) {
                      $failed = true;
              }

              // if the response failed, try again in 30 minutes
              if ( $failed ) {
                      $data = new stdClass;
                      $data->new_version = $this->version;
                      set_transient( $this->response_key, $data, strtotime( '+30 minutes' ) );
                      return false;
              }

              // if the status is 'ok', return the update arguments
              if ( ! $failed ) {
                      $update_data->sections = maybe_unserialize( $update_data->sections );
                      set_transient( $this->response_key, $update_data, strtotime( '+12 hours' ) );
              }
      }

      if ( version_compare( $this->version, $update_data->new_version, '>=' ) ) {
              return false;
      }

      return (array) $update_data;
    }
  } // end Theme_Updater
}
 
// setup the updater
new NXS_Theme_Updater();
 
add_action( 'admin_init', 'NXS_Theme_Updater' );
 
// add License menu
add_action( 'admin_menu', 'nxs_license_theme_license_page', 11 );
 
function nxs_license_theme_license_page()
{
	//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
	
	add_submenu_page("nxs_backend_overview", 'License', 'License', 'manage_options', 'license_admin', 'nxs_license_theme_license_page_content', '', 81 );
}
 
function nxs_license_theme_license_page_content() 
{
  $license = get_option(NXS_LICENSE_OPTION_KEY);
  $status = get_option(NXS_LICENSE_STATUS_OPTION_KEY);
  ?>
  <div class="wrap">
    <h2>License key</h2>
    <form method="post" action="options.php">
      <?php settings_fields('nxs_license_theme_license'); ?>

      <table class="form-table">
              <tbody>
                      <tr valign="top">
                        <th scope="row" valign="top">
                        	License key
                        </th>
                        <td>
                          <input id="<?php echo NXS_LICENSE_OPTION_KEY; ?>" name="<?php echo NXS_LICENSE_OPTION_KEY; ?>" type="text" class="regular-text" value="<?php esc_attr( $license ); ?>" />
                          <label class="description" for="<?php echo NXS_LICENSE_OPTION_KEY; ?>">Enter your license key</label>
                        </td>
                      </tr>
                      <?php if( false !== $license ) { ?>
                              <tr valign="top">
                                      <th scope="row" valign="top">
                                              ????? ?????? ??????
                                      </th>
                                      <td>
                                              <?php if( $status !== false && $status == 'valid' ) { ?>
                                                      <span style="color:green;font-weight:bold;">Activated</span>
                                                      <?php wp_nonce_field( 'nxs_license_nonce', 'nxs_license_nonce' ); ?>
                                                      &nbsp;&nbsp;<input type="submit" class="button-secondary" name="nxs_license_theme_license_deactivate" value="Remove License"/>
                                              <?php } else {
                                                      wp_nonce_field( 'nxs_license_nonce', 'nxs_license_nonce' ); ?>
                                                      <input type="submit" class="button-secondary" name="nxs_license_theme_license_activate" value="Activate License"/>
                                              <?php } ?>
                                      </td>
                              </tr>
                      <?php } ?>
              </tbody>
      </table>
      <?php //submit_button(); ?>

    </form>
  </div>
  <?php
}
 
function nxs_license_register_option() 
{
  // creates our settings in the options table
  register_setting('nxs_license_theme_license', NXS_LICENSE_OPTION_KEY, 'nxs_license_reloadlocalstatus');
}
add_action('admin_init', 'nxs_license_register_option');

function nxs_license_reloadlocalstatus( $new ) 
{
	$old = get_option(NXS_LICENSE_OPTION_KEY);
	if( $old && $old != $new ) 
	{
	 	delete_option(NXS_LICENSE_STATUS_OPTION_KEY); // new license has been entered, so must reactivate
	}
	return $new;
}

/*
// Illustrates how to activate a license key.
function nxs_license_activate() 
{
  if( isset( $_POST['nxs_license_activate'] ) ) 
  {
    if( ! check_admin_referer( 'nxs_license_nonce', 'nxs_license_nonce' ) )
    {
    	return; // get out if we didn't click the Activate button
    }

    global $wp_version;

    $license = trim( get_option(NXS_LICENSE_OPTION_KEY) );

    $api_params = array(
            'nxs_license_action' => 'activate_license',
            'license' => $license,
            'item_name' => urlencode(NXS_LICENSE_THEME)
    );

    $response = wp_remote_get( add_query_arg( $api_params, NXS_UPDATESERVER_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

    if ( is_wp_error( $response ) )
            return false;

    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

    // $license_data->license will be either "active" or "inactive"

    update_option(NXS_LICENSE_STATUS_OPTION_KEY, $license_data->license );
  }
}
add_action('admin_init', 'nxs_license_activate');
*/

/*
// This will descrease the site count
function nxs_license_deactivate_license() 
{ 
  // listen for our activate button to be clicked
  if( isset( $_POST['nxs_license_theme_license_deactivate'] ) ) {

          // run a quick security check
          if( ! check_admin_referer( 'nxs_license_nonce', 'nxs_license_nonce' ) )
                  return; // get out if we didn't click the Activate button

          // retrieve the license from the database
          $license = trim( get_option(NXS_LICENSE_OPTION_KEY) );


          // data to send in our API request
          $api_params = array(
                  'nxs_license_action'=> 'deactivate_license',
                  'license'       => $license,
                  'item_name' => urlencode( NXS_LICENSE_THEME ) // the name of our product in EDD
          );

          // Call the custom API.
          $response = wp_remote_get( add_query_arg( $api_params, NXS_UPDATESERVER_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

          // make sure the response came back okay
          if ( is_wp_error( $response ) )
                  return false;

          // decode the license data
          $license_data = json_decode( wp_remote_retrieve_body( $response ) );

          // $license_data->license will be either "deactivated" or "failed"
          if( $license_data->license == 'deactivated' )
          {
          	delete_option(NXS_LICENSE_STATUS_OPTION_KEY);
          }

  }
}
add_action('admin_init', 'nxs_license_deactivate_license');
*/

/*
// LICENSE CHECK
functionnxs_theme_check_license() {
 
        global $wp_version;
 
        $license = trim( get_option(NXS_LICENSE_OPTION_KEY) );
 
        $api_params = array(
                'nxs_license_action' => 'check_license',
                'license' => $license,
                'item_name' => urlencode(NXS_LICENSE_THEME)
        );
 
        $response = wp_remote_get( add_query_arg( $api_params, NXS_UPDATESERVER_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
 
        if ( is_wp_error( $response ) )
                return false;
 
        $license_data = json_decode( wp_remote_retrieve_body( $response ) );
 
        if( $license_data->license == 'valid' ) {
                echo "valid";
        }
}
*/
       
?>