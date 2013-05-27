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
  
  function close() {
    $response = Requests::delete("http://" . $this->browsermob_url . "/" . $this->port);
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

  function __get($property) {
    switch($property) {
      case "har":
        $proxy_handle = curl_init();
        curl_setopt($proxy_handle, CURLOPT_URL, "http://" . $this->browsermob_url . "/proxy/" . $this->port . "/har");
        curl_setopt($proxy_handle, CURLOPT_RETURNTRANSFER, True);
        $result = curl_exec($proxy_handle);
        $decoded = json_decode($result, true);
        curl_close($proxy_handle);
        return $decoded;
      default:
        return $this->$property;
    }
  }

  function new_har($label = '') {
    $data = "initialPageRef=" . $label;
    $response = Requests::put("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/har",
                              array(),
                              $data);
    return $response;
  }

  function new_page($label = '') {
    $data = "pageRef=" . $label;
    $response = Requests::put("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/har/pageRef",
                              array(),
                              $data);
    return $response;
  }

  function blacklist($regexp, $status_code) {
    $data = $this->encode_array(array("regex" => $regexp, "status" => $status_code));
    $response = Requests::put("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/blacklist",
                              array(),
                              $data);
    return $response;
  }

  function whitelist($regexp, $status_code) {
    $data = $this->encode_array(array("regex" => $regexp, "status" => $status_code));
    $response = Requests::put("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/whitelist",
                              array(),
                              $data);
    return $response;
  }

  function basic_auth($domain, $options) {
    $data = json_encode($options);
    $response = Requests::post("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/auth/basic/" . $domain,
                                array('Content-Type' => 'application/json'),
                                $data);
    return $response;
  }

  function headers($options) {
    $data = json_encode($options);
    $response = Requests::post("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/headers",
                                array('Content-Type' => 'application/json'),
                                $data);
    return $response;
  }

  function response_interceptor($js) {
    $response = Requests::post("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/interceptor/response",
                            array('Content-Type' => 'x-www-form-urlencoded'),
                            $js);
    return $response;
  }

  function request_interceptor($js) {
    $response = Requests::post("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/interceptor/request",
                            array('Content-Type' => 'x-www-form-urlencoded'),
                            $js);
    return $response;
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
    $response = Requests::put("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/limit",
                              array(),
                              $data);
    return $response;
  }
  
  function timeouts($options) {
    $keys = array("request" => "requestTimeout",
                  "read" => "readTimeout",
                  "connection" => "connectionTimeout",
                  "dns" => "dnsCacheTimeout");
    foreach (array_keys($options) as $option_name) {
      if (! array_key_exists($option_name, $keys)) {
        throw new Exception($option_name . " is not a valid 'timeouts' option");
      }
    }
    $data = $this->encode_array($options);
    $response = Requests::put("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/timeout",
                              array(),
                              $data);
    return $response;
  }

  function remap_hosts($address, $ip_address) {
    $data = json_encode(array($address => $address));
    $response = Requests::post("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/hosts",
                                array('Content-Type' => 'application/json'),
                                $data);
    return $response;
  }

  function wait_for_traffic_to_stop($quiet_period, $timeout) {
    $data = $this->encode_array(array('quietPeriodInMs' => (string)($quiet_period * 1000), 'timeoutInMs' => (string)($timeout * 1000)));
    $response = Requests::put("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/wait",
                              array(),
                              $data);
    return $response;
  }

  function clear_dns_cache() {
    $response = Requests::delete("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/dns/cache");
    return $response;
  }

  function rewrite_url($match, $replace) {
    $data = $this->encode_array(array('matchRegex' => $match, 'replace' => $replace));
    $response = Requests::put("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/rewrite",
                              array(),
                              $data);
    return $response;
  }

  function retry($retry_count) {
    $data = $this->encode_array(array('retrycount' => $retry_count));
    $response = Requests::put("http://" . $this->browsermob_url . "/proxy/" . $this->port . "/retry",
                              array(),
                              $data);
    return $response;
  }
}


?>