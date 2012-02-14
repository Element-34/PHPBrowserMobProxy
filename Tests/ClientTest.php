<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'PHPBrowserMobProxy' . DIRECTORY_SEPARATOR . 'Client.php';

class ServerTest extends PHPUnit_Framework_TestCase {
  public function testProxyExists() {
    $c = new PHPBrowserMobProxy_Client("http://localhost:8080");
    $r = $c->close();
  }

  public function testNamedHar() {
    $c = new PHPBrowserMobProxy_Client("http://localhost:8080");
    $c->new_har("google");
    $c->close();
  }
  
  public function testUnNamedHar() {
    $c = new PHPBrowserMobProxy_Client("http://localhost:8080");
    $c->new_har();
    $c->close();
  }

  public function testNamedPage() {
    $c = new PHPBrowserMobProxy_Client("http://localhost:8080");
    $c->new_har();
    $c->new_page("foo");
    $c->close();
  }

  public function testUnNamedPage() {
    $c = new PHPBrowserMobProxy_Client("http://localhost:8080");
    $c->new_har("Aa");
    $c->new_page("Bb");
    $c->close();
  }

  public function testGetHar() {
    $c = new PHPBrowserMobProxy_Client("http://localhost:8080");
    $c->new_har("Aa");
    $c->new_page("Bb");
    $h = $c->har;
    $c->close();
  }
  
  public function testWhiteList() {
    $c = new PHPBrowserMobProxy_Client("http://localhost:8080");
    $c->whitelist('.*\.doubleclick\.net', 200);
    $c->new_har("noads");
    $c->close();
  }

  public function testBlackList() {
    $c = new PHPBrowserMobProxy_Client("http://localhost:8080");
    $c->whitelist('.*\.doubleclick\.net', 200);
    $c->new_har("noads");
    $c->close();
  }

  /**
  * @group foo
  */
  public function testUpstreamLimists() {
    $c = new PHPBrowserMobProxy_Client("http://localhost:8080");
    $c->limits(array("upstreamKbps" => 12));
    $c->new_har("throttle up");
    $c->close();
  }
}

?>