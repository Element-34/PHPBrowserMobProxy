## PHPBrowserMobProxy

This is a PHP wrapper for the [BrowserMob Proxy](https://github.com/lightbody/browsermob-proxy). It is currently just the client-side of things as I haven't found a cross-platform solution for controlling the server yet (in PHP) that I like. It is strongly suggested that you use something like [Puppet](http://puppetlabs.com/) to make sure that it is installed and running.

##  GETTING STARTED

*   This driver has been packaged for distribution via PEAR. So...

        pear channel-discover element-34.github.com/pear
        pear install -f element-34/PHPBrowserMobProxy

##  DOCUMENTATION

See [The tests](https://github.com/Element-34/PHPBrowserMobProxy/blob/master/Tests/ClientTest.php)

