<?php

/* --------------------------------------------------
SETTINGS */
												
define('TWITTER_OPTION_OAUTHTOKEN', 'oauth_token');
define('TWITTER_OPTION_OAUTHTOKENSECRET', 'oauth_token_secret');
define('TWITTER_OPTION_ACCESSTOKEN', 'access_token');
define('TWITTER_OPTION_RETURNURL', 'twitter_returnurl');

// APP / "CONSUMENT" SETTINGS
// to get a consumer key and secret
// Create an app on twitter on dev.twitter.com
// when creating the app, ensure the callback url is configured 
// in the settings. If not, you will get a 401 when authenticating		
define('CONSUMER_KEY', 'oyTe3lXbqWzAUyICEkAxAQ');
define('CONSUMER_SECRET', 'oaozLFZGlmh23q2y14zW7KljtcfR0ANHmb679FsZp4');

// OAUTH SETTINGS
$url = nxs_addqueryparametertourl(nxs_geturl_home(), "twitter", "callback");
define('OAUTH_CALLBACK', $url);

// embed oauth api
require_once('twitteroauth/twitteroauth.php');

/* --------------------------------------------------
METHODS */

function nxs_twitter_isconnected()
{
	$result = false;
	
	/* If access tokens are not available redirect to connect page. */
	$at = get_option(TWITTER_OPTION_ACCESSTOKEN);
	$at_ot = $at['oauth_token'];
	$at_ts = $at['oauth_token_secret'];
	if (empty($at) || empty($at_ot) || empty($at_ts))
	{
		$result = false;
	}
	else
	{
		$result = true;
	}
	
	return $result;
}

function nxs_twitter_disconnect()
{
	// Remove tokens
	delete_option(TWITTER_OPTION_OAUTHTOKEN);
	delete_option(TWITTER_OPTION_OAUTHTOKENSECRET);
	delete_option(TWITTER_OPTION_ACCESSTOKEN);
	delete_option(TWITTER_OPTION_STATUS);
	// TODO: remove transients too
}

function nxs_twitter_redirecttotwitterauthentication()
{
	// Build TwitterOAuth object with client credentials.
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
	 
	// Get temporary credentials.
	$request_token = $connection->getRequestToken(OAUTH_CALLBACK);
	
	// Save temporary credentials to session.
	$token = $request_token['oauth_token'];
	update_option(TWITTER_OPTION_OAUTHTOKEN, $token);
	update_option(TWITTER_OPTION_OAUTHTOKENSECRET, $request_token['oauth_token_secret']);
	
	/* If last connection failed don't display authorization link. */
	switch ($connection->http_code) {
	  case 200:
	    /* Build authorize URL and redirect user to Twitter. */
	    $url = $connection->getAuthorizeURL($token);
	    header('Location: ' . $url);
	    exit();
	    break;
	  default:
	    /* Show notification if something went wrong. */
	    echo 'Could not connect to Twitter. Refresh the page or try again later.';
	    die();
	}
	
	exit();
}

function nxs_twitter_gettweets($twitteruser, $count)
{
	if (!isset($count))
	{
		// defaults to 3
		$count = 3;
	}
	
	/* Get user access tokens out of the session. */
	$access_token = get_option(TWITTER_OPTION_ACCESSTOKEN);
	
	/* Create a TwitterOauth object with consumer/user tokens. */
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
	
	$parameters = array();
	if (isset($count))
	{
		$parameters["count"] = $count;
	}
	if (isset($twitteruser))
	{
		$parameters["screen_name"] = $twitteruser;
	}
	$result = $connection->get('statuses/user_timeline', $parameters);
	return $result;
}

function nxs_twitter_handletwittercallback()
{
	// Take the user when they return from Twitter. Get access tokens.
	// Verify credentials and redirect to based on response from Twitter.
	 
	// If the oauth_token is old redirect to the connect page.
	if (isset($_REQUEST['oauth_token']) && get_option(TWITTER_OPTION_OAUTHTOKEN) !== $_REQUEST['oauth_token']) 
	{
	  update_option(TWITTER_OPTION_STATUS, 'oldtoken');
	  nxs_twitter_redirecttotwitterauthentication();
	}
	
	// Create TwitteroAuth object with app key/secret and token key/secret from default phase
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, get_option(TWITTER_OPTION_OAUTHTOKEN), get_option(TWITTER_OPTION_OAUTHTOKENSECRET));
	
	// Request access tokens from twitter
	$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
	
	// Save the access tokens
	update_option(TWITTER_OPTION_ACCESSTOKEN, $access_token);
	
	// Remove no longer needed request tokens
	delete_option(TWITTER_OPTION_OAUTHTOKEN);
	delete_option(TWITTER_OPTION_OAUTHTOKENSECRET);
	
	/* If HTTP response is 200 continue otherwise send to connect page to retry */
	if (200 == $connection->http_code) 
	{
	  /* The user has been verified and the access tokens can be saved for future use */
	  update_option(TWITTER_OPTION_STATUS, 'verified');
	  
	  $returnurl = get_option(TWITTER_OPTION_RETURNURL);
	  if (isset($returnurl))
	  {
	  	$returnurl = nxs_geturl_home();
	  }
	  wp_redirect($returnurl, 301);
	  exit();
	} 
	else 
	{
		nxs_twitter_disconnect();
		
		$returnurl = get_option(TWITTER_OPTION_RETURNURL);
	  if (isset($returnurl))
	  {
	  	$returnurl = nxs_geturl_home();
	  }
	  wp_redirect($returnurl, 301);
	  exit();
	}
}

function twitteroauth_row($method, $response, $http_code, $parameters = '') 
{
  echo '<tr>';
  echo "<td><b>{$method}</b></td>";
  switch ($http_code) {
    case '200':
    case '304':
      $color = 'green';
      break;
    case '400':
    case '401':
    case '403':
    case '404':
    case '406':
      $color = 'red';
      break;
    case '500':
    case '502':
    case '503':
      $color = 'orange';
      break;
    default:
      $color = 'grey';
  }
  echo "<td style='background: {$color};'>{$http_code}</td>";
  if (!is_string($response)) {
    $response = print_r($response, TRUE);
  }
  if (!is_string($parameters)) {
    $parameters = print_r($parameters, TRUE);
  }
  echo '<td>', strlen($response), '</td>';
  echo '<td>', $parameters, '</td>';
  echo '</tr><tr>';
  echo '<td colspan="4">', substr($response, 0, 400), '...</td>';
  echo '</tr>';
}

function twitteroauth_header($header) 
{
  echo '<tr><th colspan="4" style="background: grey;">', $header, '</th></tr>';
}

?>