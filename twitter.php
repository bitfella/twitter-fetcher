<?php
  require_once('./lib/TwitterFetcher.php');

  $myTwitterStream = new TwitterFetcher(array(
    'tweet_count' => 10,
    'include_rts' => 1,
    'oauth_access_token' => '<YOUR_OAUTH_ACCESS_TOKEN>',
    'oauth_access_token_secret' => '<YOUR_OAUTH_ACCESS_TOKEN_SECRET>',
    'oauth_consumer_key' => '<YOUR_OAUTH_CONSUMER_KEY>',
    'oauth_consumer_secret' => '<YOUR_OAUTH_CONSUMER_SECRET>',
  ));

  echo $myTwitterStream->get();
?>
