<?php

require_once __DIR__ . '/../vendor/autoload.php';

class NoPhpVcrTest extends \PHPUnit_Framework_TestCase
{

    # URL 302 redirects to http://en.gravatar.com/abeger
    const URL="http://www.gravatar.com/2fa8e97737b15bdb51a9b4ddbfa624ae";

    /**
     * Gets the URL with file_get_contents with no php-vcr
     */
    public function testNoVcrNoFollowLocation() {
        $source = file_get_contents(self::URL);

        # Make sure contents were got
        $this->assertNotEmpty($source);

        # Make sure there's nothing before the contents
        $this->assertSame(0, strpos($source, '<!doctype html>'), 'Source starts with "'.substr($source,0,15).'"');

        # Make sure we grabbed the right thing
        $this->assertContains('<title>abeger - Gravatar Profile</title>', $source);
    }

}
