<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'PHPBrowserMobProxy' . DIRECTORY_SEPARATOR . 'Client.php';

class ServerTest extends PHPUnit_Framework_TestCase {
  protected function setUp() {
    $this->client = new PHPBrowserMobProxy_Client("localhost:8080");
  }

  protected function tearDown() {
    $this->client->close();
  }

  public function testProxyExists() {
    $r = $this->client->close();
  }

  public function testNamedHar() {
    $this->client->new_har("google");
  }
  
  public function testUnNamedHar() {
    $this->client->new_har();
  }

  public function testNamedPage() {
    $this->client->new_har();
    $this->client->new_page("foo");
  }

  public function testUnNamedPage() {
    $this->client->new_har("Aa");
    $this->client->new_page("Bb");
  }

  public function testGetHar() {
    $this->client->new_har("Aa");
    $this->client->new_page("Bb");
    $h = $this->client->har;
  }

  public function testBlackList() {
    $this->client->whitelist('.*\.doubleclick\.net', 200);
    $this->client->new_har("noads");
  }

  public function testWhiteList() {
    $this->client->whitelist('.*\.doubleclick\.net', 200);
    $this->client->new_har("noads");
  }

  public function testBasicAuth() {
    $response = $this->client->basic_auth('yoyo.org', array('username' => 'foo', 'password' => 'bar'));
    $this->assertEquals($response->status_code, 200);
  }

  public function testHeaders() {
    $response = $this->client->headers(array('ribbit' => 'rabbit'));
    $this->assertEquals($response->status_code, 200);
  }

  public function testResponseInterceptor() {
    $response = $this->client->response_interceptor('ffdskl');
    $this->assertEquals($response->status_code, 200);
  }
  
  public function testRequestInterceptor() {
    $response = $this->client->request_interceptor('ffdskl');
    $this->assertEquals($response->status_code, 200);
  }

  public function testUpstreamLimists() {
    $limits = array (
      "downstreamKbps" => 12,
      "upstreamKbps" => 34,
      "latency" => 3
    );
    $response = $this->client->limits($limits);
    $this->assertEquals($response->status_code, 200);
  }

  public function testTimeouts() {
    $timeouts = array (
      "request" => 12,
      "read" => 34,
      "connection" => 3,
      "dns" => 2
    );
    $response = $this->client->timeouts($timeouts);
    $this->assertEquals($response->status_code, 200);
  }

  public function testRemapHosts() {
    $response = $this->client->remap_hosts("a.b.c", "d.e.f");
    $this->assertEquals($response->status_code, 200);
  }

  public function testWaitForTrafficToStop() {
    $response = $this->client->wait_for_traffic_to_stop(5, 30);
    $this->assertEquals($response->status_code, 200);
  }

  public function testClearDNSCache() {
    $response = $this->client->clear_dns_cache();
    $this->assertEquals($response->status_code, 200);
  }

  public function testRewriteURL() {
    $response = $this->client->rewrite_url('foo', 'bar');
    $this->assertEquals($response->status_code, 200);
  }

  public function testRetry() {
    $response = $this->client->retry(3);
    $this->assertEquals($response->status_code, 200);
  }

}

?>