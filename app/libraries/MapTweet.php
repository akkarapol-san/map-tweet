<?php

class MapTweet
{
    public function search($q='', $lat='', $long='')
    {
        $settings = array(
                    'oauth_access_token' => Config::get('constants.OAUTH_ACCESS_TOKEN'),
                    'oauth_access_token_secret' => Config::get('constants.OAUTH_ACCESS_TOKEN_SECRET'),
                    'consumer_key' => Config::get('constants.CONSUMER_KEY'),
                    'consumer_secret' => Config::get('constants.CONSUMER_SECRET')
                );
        $twitter = new TwitterAPIExchange($settings);
        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $getfield = '?q='.$q.'&geocode='.$lat.','.$long.','.Config::get('constants.RADIUS').'&count=20';
        
        $requestMethod = 'GET';
        $result_json = $twitter->setGetfield($getfield)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest(); 
        return $result_json;
    }
}
