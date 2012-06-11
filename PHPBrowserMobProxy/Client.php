<?php

require_once 'Requests.php';
Requests::register_autoloader();

class PHPBrowserMobProxy_Client {
  function __construct($url) {
    $this->browsermob_url = $url;

    $parts = parse_url($this->browsermob_url);
    $this->hostname = $parts["host"];
    
    $response = Requests::post("http://" . $this->browsermob_url . "/proxy/");
    
    $decoded = json_decode($response->body, true);
    if ($decoded) {
        $this->port = $decoded["port"];
    }
    $this->url = $this->hostname . ":" . $this->port;
  }
  
  function __get($property) {
    switch($property) {
      case "har":
        $proxy_handle = curl_init();
        curl_setopt($proxy_handle, CURLOPT_URL, $this->url . "/proxy/" . $this->port . "/har");
        curl_setopt($proxy_handle, CURLOPT_RETURNTRANSFER, True);
        $result = curl_exec($proxy_handle);
        $decoded = json_decode($result, true);
        curl_close($proxy_handle);
        return $decoded;
      default:
        return $this->$property;
    }
  }
  
  private function encode_array($args) {
    if (!is_array($args)) {
      return false;
    }
    $c = 0;
    $out = '';
    foreach($args as $name => $value) {
      if ($c++ != 0){
        $out .= '&';
      }
      $out .= urlencode("$name").'=';
      if (is_array($value)) {
        $out .= urlencode(serialize($value));
      } else {
        $out .= urlencode("$value");
      }
    }
    return $out;
  }

  function new_har($label = '') {
    $data = "initialPageRef=" . $label;
    $response = Requests::put($this->url . "/proxy/" . $this->port . "/har",
                              array(),
                              $data);
  }

  function new_page($label = '') {
    $data = "pageRef=" . $label;
    $response = Requests::put($this->url . "/proxy/" . $this->port . "/har/pageRef",
                              array(),
                              $data);
  }

  function whitelist($regexp, $status_code) {
    $data = $this->encode_array(array("regex" => $regexp, "status" => $status_code));
    $response = Requests::put($this->url . "/proxy/" . $this->port . "/whitelist",
                              array(),
                              $data);
  }
  
  function blacklist($regexp, $status_code) {
    $data = $this->encode_array(array("regex" => $regexp, "status" => $status_code));
    $response = Requests::put($this->url . "/proxy/" . $this->port . "/blacklist",
                              array(),
                              $data);
  }
  
  function limits($options) {
    $keys = array("downstreamKbps" => "downstreamKbps",
                  "upstreamKbps" => "upstreamKbps",
                  "latency" => "latency");
    foreach (array_keys($options) as $option_name) {
      if (! array_key_exists($option_name, $keys)) {
        throw new Exception($option_name . " is not a valid 'limits' option");
      }
    }
    $data = $this->encode_array($options);
    $response = Requests::put($this->url . "/proxy/" . $this->port . "/limit",
                              array(),
                              $data);
  }
  
  function close() {
    $response = Requests::delete("http://" . $this->url . "/" . $this->port);
  }
}


?>