<?php

require_once('lib/metadata.php');
require_once('Djatoka_Test.php');

class Djatoka_NetTest extends Djatoka_Test
{
    protected $_djMetadataObj;
    protected $_testResolver;

    public function setUp()
    {
        $this->_testResolver = new Djatoka_Resolver($this->_testBaseUrl);
        $this->_djMetadataObj = new Djatoka_Metadata($this->_testResolver, $this->_testRftId1);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Djatoka_Resolver', $this->_djMetadataObj->resolver());
        $this->assertEquals('0000002', $this->_djMetadataObj->rftId());
    }

}
