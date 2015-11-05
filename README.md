This is a simple set of tests for the [php-vcr](https://github.com/php-vcr/php-vcr) library to demonstrate an issue it apparently has with redirects and `file_get_contents`, as described here: https://github.com/php-vcr/php-vcr/issues/138.

#### Environment

* PHP 5.5.27
* PHPUnit 4.8.5
* `php-vcr` 1.2.6

#### The Tests

There are three tests:

1. [`NoPhpVcrTest::testNoVcrNoFollowLocation`](https://github.com/abeger/php-vcr-issue-138/blob/master/test/NoPhpVcrTest.php#L14) just uses a straight `file_get_contents` to retrieve a URL with a 302 redirect. No cassette is used.
2. [`WithPhpVcrTest::testWithVcrNoFollowLocation`](https://github.com/abeger/php-vcr-issue-138/blob/master/test/WithPhpVcrTest.php#L25) duplicates test 1 but loads a cassette. 
3. [`WithPhpVcrTest::testWithVcrFollowLocation`](https://github.com/abeger/php-vcr-issue-138/blob/master/test/WithPhpVcrTest.php#L47) duplicates test 2 but also explicitly tells the call to `file_get_contents` to follow redirects.

#### The Results

````bash
$ phpunit test
PHPUnit 4.8.5 by Sebastian Bergmann and contributors.

.FF

Time: 1.44 seconds, Memory: 13.25Mb

There were 2 failures:

1) WithPhpVcrTest::testWithVcrNoFollowLocation
Failed asserting that a string is not empty.

/path/to/php-vcr-issue/test/WithPhpVcrTest.php:31

2) WithPhpVcrTest::testWithVcrFollowLocation
Source starts with "HTTP/1.1 302 Fo"
Failed asserting that 781 is identical to 0.

/path/to/php-vcr-issue/test/WithPhpVcrTest.php:60

FAILURES!
Tests: 3, Assertions: 6, Failures: 2.
````

#### Why the Failures?

Looking at the VCR cassettes, we see two different reasons for the failures:

[Test 2's cassette](https://github.com/abeger/php-vcr-issue-138/blob/master/test/vcr_cassettes/no_follow_location) reveals that simply throwing a cassette around the call to `file_get_contents` doesn't follow the redirect at all. It just records the 302 response and calls it a day.

[Test 3's cassette](https://github.com/abeger/php-vcr-issue-138/blob/master/test/vcr_cassettes/with_follow_location) shows that making the `follow_location` command explicit with cause `php-vcr` to follow the redirect. But, oddly, the contents returned contain all the returned headers in addition to the content of the site.
