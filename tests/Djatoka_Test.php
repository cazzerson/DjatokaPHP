<?php

require_once('lib/metadata.php');

abstract class Djatoka_Test extends PHPUnit_Framework_TestCase
{
    protected $_testBaseUrl = 'http://YOUR.HOST/adore-djatoka/resolver?';
    protected $_testRftId1 = '0000002';
    protected $_testRftId2 = '0000003';
    protected $_testBaseUrlVers = 'url_ver=Z39.88-2004';
    protected $_testBadBaseUrl = 'http://bad.url/adore-djatoka/resolver?';
    protected $_testBadRftId = 'CASDENYEAH';
}
