<?php
  
  class TwitterFetcher {
    private $api_endpoint_url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    private $tweet_count;
    private $include_rts;
    private $oauth;
    private $oauth_access_token;
    private $oauth_access_token_secret;
    private $oauth_consumer_key;
    private $oauth_consumer_secret;

    private function composeOauth() {
      $oauth = array(
        'count' => $this->tweet_count,
        'include_rts' => $this->include_rts,
        'oauth_consumer_key' => $this->oauth_consumer_key,
        'oauth_nonce' => time(),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp' => time(),
        'oauth_token' => $this->oauth_access_token,
        'oauth_version' => '1.0',
      );

      $oauthString = array();      
      foreach($oauth as $key => $val) {
        $oauthString[] = $key . '=' . rawurlencode($val);
      }

      $base_string = 'GET&' . rawurlencode($this->api_endpoint_url) . '&' . rawurlencode(implode('&', $oauthString));
      $sign_key = rawurlencode($this->oauth_consumer_secret) . '&' . rawurlencode($this->oauth_access_token_secret);
      $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $base_string, $sign_key, true));
      $this->oauth = $oauth;

      return $this;
    }

    private function composeCurlHttpHeader() {
      $values = array();

      foreach($this->oauth as $key => $value) {
        if (in_array($key, array('count', 'include_rts', 'oauth_consumer_key', 'oauth_nonce', 'oauth_signature', 'oauth_signature_method', 'oauth_token', 'oauth_timestamp', 'oauth_version'))) {
          $values[] = "$key=\"" . rawurlencode($value) . "\"";
        }
      }

      $curlHttpHeader = array('Authorization: OAuth '.implode(', ', $values), 'Expect:');

      return $curlHttpHeader;
    }

    public function get() { 
      $tweets = curl_init();

      curl_setopt_array($tweets, array(
        CURLOPT_HTTPHEADER => $this->composeCurlHttpHeader(),
        CURLOPT_HEADER => false,
        CURLOPT_URL => $this->api_endpoint_url . '?count=' . $this->tweet_count . '&include_rts=' . $this->include_rts,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
      ));
      
      $json = curl_exec($tweets);
      curl_close($tweets);

      return $json;
    }

    public function __construct(array $options) {
      if (!function_exists('curl_init')) {
        throw new RuntimeException('TwitterFetcher class requires cURL extension!');
      }

      if (!isset($options['oauth_access_token']) 
       || !isset($options['oauth_access_token_secret']) 
       || !isset($options['oauth_consumer_key']) 
       || !isset($options['oauth_consumer_secret'])) {
        throw new InvalidArgumentException('One or more arguments are missing in the TwitterFetcher class instance!');
      }

      $this->oauth_access_token = $options['oauth_access_token'];
      $this->oauth_access_token_secret = $options['oauth_access_token_secret'];
      $this->oauth_consumer_key = $options['oauth_consumer_key'];
      $this->oauth_consumer_secret = $options['oauth_consumer_secret'];
      $this->tweet_count = $options['tweet_count'] !== NULL ? $options['tweet_count'] : 1;
      $this->include_rts = $options['include_rts'] !== NULL ? $options['include_rts'] : 1;

      $this->composeOauth();
    }    
  }

?>