<?php

require_once('lib/resolver.php');
require_once('Djatoka_Test.php');

class Djatoka_ResolverTest extends Djatoka_Test
{
    protected $_resolver;

    public function setUp()
    {
        $this->_resolver = new Djatoka_Resolver($this->_testBaseUrl);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Djatoka_Resolver', $this->_resolver);
        $this->assertEquals($this->_testBaseUrl . $this->_testBaseUrlVers, $this->_resolver->baseUrl());
    }

    public function testPingGoodRftId()
    {
        $this->assertTrue($this->_resolver->pingRftId($this->_testRftId1));
    }

    public function testPingBadRftId()
    {
        $this->setExpectedException('Exception');
        $this->_resolver->pingRftId($this->_testBadRftId);
        //$this->assertFalse($this->_resolver->baseUrl($this->_testBadBaseUrl)->pingRftId($this->_testRftId1));
    }

    public function testPingBadResolver()
    {
        $this->setExpectedException('Exception');
        $this->_resolver->baseUrl($this->_testBadBaseUrl)->pingRftId($this->_testRftId1);
    }

    public function testSetGetBaseUrl()
    {
        $this->assertEquals($this->_testBadBaseUrl . $this->_testBaseUrlVers, $this->_resolver->baseUrl($this->_testBadBaseUrl)->baseUrl());
    }

    public function testSetGetRegion()
    {
        $region = $this->_resolver->region($this->_testRftId1);
        $this->assertInstanceOf('Djatoka_Region', $region);
        $this->assertEquals($this->_testRftId1, $region->rftId());
    }

    public function testSetGetMetadata()
    {
        $metadata = $this->_resolver->metadata($this->_testRftId1);
        $this->assertInstanceOf('Djatoka_Metadata', $metadata);
        $this->assertEquals($this->_testRftId1, $metadata->rftId());
    }

    public function testRftIdUrl()
    {
        $this->assertEquals($this->_testBaseUrl . $this->_testBaseUrlVers . "&rft_id={$this->_testRftId1}", $this->_resolver->rftIdUrl($this->_testRftId1));
    }

}
