## PHPBrowserMobProxy

This is a PHP wrapper for the [BrowserMob Proxy](http://opensource.webmetrics.com/browsermob-proxy/). It is currently just the client-side of things as I haven't found a cross-platform solution for controlling the server yet (in PHP) that I like. It is strongly suggested that you use something like [Puppet](http://puppetlabs.com/) to make sure that it is installed and running.

##  GETTING STARTED

*   This driver has been packaged for distribution via PEAR. So...

        pear channel-discover element-34.github.com/pear
        pear install -f element-34/PHPBrowserMobProxy

*   It also makes use of the awesome [Requests for PHP](http://requests.ryanmccue.info/) library which is not (yet) available through Pear. This means you will have to somehow install it on your machine (there are instructions on his site) and then change the path on line 3 of Client.php

## TODO

*   Make the tests actually have asserts
*   Return a Selenium Proxy object which can be used by browser
*   Custom headers
*   Documentation