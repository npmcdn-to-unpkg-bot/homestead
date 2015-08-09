<?php

// set the twitter keys per subdomain by retrieving them from the .env file and setting them here
//
$subdomain = config('app.subdomain');

$consumerKey = '';
$consumerSecret = '';
$accessToken = '';
$accessTokenSecret = '';
    
if ($subdomain == 'nba') {
    $consumerKey = env('NBA_CONSUMER_KEY');
    $consumerSecret = env('NBA_CONSUMER_SECRET');
    $accessToken = env('NBA_ACCESS_TOKEN');
    $accessTokenSecret = env('NBA_ACCESS_TOKEN_SECRET');
} else if ($subdomain == 'abbotkinneyblvd') {
    $consumerKey = env('AK_CONSUMER_KEY');
    $consumerSecret = env('AK_CONSUMER_SECRET');
    $accessToken = env('AK_ACCESS_TOKEN');
    $accessTokenSecret = env('AK_ACCESS_TOKEN_SECRET');  
} else {
    /*
    print_r(get_included_files());
    var_dump($subdomain);
    var_dump(Site::getInstance()->getSubdomainArr());
    //dd("subdomain not set");
    */
}

// You can find the keys here : https://apps.twitter.com/

return [
	'debug'               => false,

	'API_URL'             => 'api.twitter.com',
	'UPLOAD_URL'          => 'upload.twitter.com',
	'API_VERSION'         => '1.1',
	'AUTHENTICATE_URL'    => 'https://api.twitter.com/oauth/authenticate',
	'AUTHORIZE_URL'       => 'https://api.twitter.com/oauth/authorize',
	'ACCESS_TOKEN_URL'    => 'https://api.twitter.com/oauth/access_token',
	'REQUEST_TOKEN_URL'   => 'https://api.twitter.com/oauth/request_token',
	'USE_SSL'             => true,
    
	'CONSUMER_KEY'        => $consumerKey,
	'CONSUMER_SECRET'     => $consumerSecret,
	'ACCESS_TOKEN'        => $accessToken,
	'ACCESS_TOKEN_SECRET' => $accessTokenSecret,
];