<?php

require_once dirname(__FILE__) . '/../config.php';

$config['base_url'] = 'https://api.twitter.com/1.1/';

/**
 *  Usage:
 *  Send the url you want to access url encoded in the url paramater, for example (This is with JS):
 *  /twitter-proxy.php?url='+encodeURIComponent('statuses/user_timeline.json?screen_name=MikeRogers0&count=2')
 */

$limit = $config['limit'];
if (isset($_GET['limit']) && $_GET['limit'] > 0 && $_GET['limit'] < 100) {
	$limit = $_GET['limit'];
}

// Only allow certain requests to twitter. Stop randoms using your server as a proxy.
$url = 'statuses/user_timeline.json?screen_name=' . $config['username'] . '&count=' . $limit . '&include_rts=false&exclude_replies=' . (!$config['with_replies']);

/*
* Ok, no more config should really be needed. Yay!
*/

// Figure out the URL parmaters
$url_parts = parse_url($url);
parse_str($url_parts['query'], $url_arguments);

$full_url = $config['base_url'] . $url; // Url with the query on it.
$base_url = $config['base_url'] . $url_parts['path']; // Url without the query.

/**
 * Code below from http://stackoverflow.com/questions/12916539/simplest-php-example-retrieving-user-timeline-with-twitter-api-version-1-1 by Rivers
 * with a few modfications by Mike Rogers to support variables in the URL nicely
 */

function buildBaseString($baseURI, $method, $params) {
	$r = array();
	ksort($params);
	foreach ($params as $key => $value) {
		$r[] = "$key=" . rawurlencode($value);
	}
	return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
}

function buildAuthorizationHeader($oauth) {
	$r = 'Authorization: OAuth ';
	$values = array();
	foreach ($oauth as $key => $value) {
		$values[] = "$key=\"" . rawurlencode($value) . "\"";
	}
	$r .= implode(', ', $values);
	return $r;
}

// Set up the oauth Authorization array
$oauth = array(
		'oauth_consumer_key' => $config['consumer_key'],
		'oauth_nonce' => time(),
		'oauth_signature_method' => 'HMAC-SHA1',
		'oauth_token' => $config['oauth_access_token'],
		'oauth_timestamp' => time(),
		'oauth_version' => '1.0'
);

$base_info = buildBaseString($base_url, 'GET', array_merge($oauth, $url_arguments));
$composite_key = rawurlencode($config['consumer_secret']) . '&' . rawurlencode($config['oauth_access_token_secret']);
$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
$oauth['oauth_signature'] = $oauth_signature;

// Make Requests
$header = array(
		buildAuthorizationHeader($oauth),
		'Expect:'
);
$options = array(
		CURLOPT_HTTPHEADER => $header,
	//CURLOPT_POSTFIELDS => $postfields,
		CURLOPT_HEADER => false,
		CURLOPT_URL => $full_url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false
);

$feed = curl_init();
curl_setopt_array($feed, $options);
$result = curl_exec($feed);
$info = curl_getinfo($feed);
curl_close($feed);

// Send suitable headers to the end user.
if (isset($info['content_type']) && isset($info['size_download'])) {
	header('Content-Type: ' . $info['content_type']);
	header('Content-Length: ' . $info['size_download']);

}

echo($result);
?>