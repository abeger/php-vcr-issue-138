<?php

require_once __DIR__ . '/../vendor/autoload.php';

class WithPhpVcrTest extends \PHPUnit_Framework_TestCase
{

    # URL 302 redirects to http://en.gravatar.com/abeger
    const URL="http://www.gravatar.com/2fa8e97737b15bdb51a9b4ddbfa624ae";

    public function setUp() {
        \VCR\VCR::configure()->setCassettePath('test/vcr_cassettes');
        \VCR\VCR::turnOn();
    }

    public function tearDown() {
        \VCR\VCR::turnOff();
    }


    /**
     * Gets the URL with file_get_contents and no special context
     * Solution suggested here: https://github.com/php-vcr/php-vcr/issues/138#issuecomment-143422131
     */
    public function testWithVcrNoFollowLocation() {
        \VCR\VCR::insertCassette('no_follow_location');

        $source = file_get_contents(self::URL);

        # Make sure contents were got
        $this->assertNotEmpty($source);

        # Make sure there's nothing before the contents
        $this->assertSame(0, strpos($source, '<!doctype html>'), 'Source starts with "'.substr($source,0,15).'"');

        # Make sure we grabbed the right thing
        $this->assertContains('<title>abeger - Gravatar Profile</title>', $source);

        \VCR\VCR::eject();
    }


    /**
     * Gets the URL with file_get_contents, explicitly setting follow_location 
     * in the context
     */
    public function testWithVcrFollowLocation() {

        \VCR\VCR::insertCassette('with_follow_location');

        $opts = array('http' => array('follow_location' => 1));
        $context = stream_context_create($opts);

        $source = file_get_contents(self::URL, false, $context);

        # Make sure contents were got
        $this->assertNotEmpty($source);

        # Make sure there's nothing before the contents
        $this->assertSame(0, strpos($source, '<!doctype html>'), 'Source starts with "'.substr($source,0,15).'"');

        # Make sure we grabbed the right thing
        $this->assertContains('<title>abeger - Gravatar Profile</title>', $source);

        \VCR\VCR::eject();
    }


}
