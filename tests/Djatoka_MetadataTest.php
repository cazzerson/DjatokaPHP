<?php

require_once('lib/metadata.php');
require_once('Djatoka_Test.php');

class Djatoka_MetadataTest extends Djatoka_Test
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
        $this->assertInstanceOf('Djatoka_Metadata', $this->_djMetadataObj);
        $this->assertInstanceOf('Djatoka_Resolver', $this->_djMetadataObj->resolver());
        $this->assertEquals('0000002', $this->_djMetadataObj->rftId());
    }

    public function testGetFields()
    {
        $fields = $this->_djMetadataObj->fieldsArray();
        //assertTrue(is_array($fields));
        $this->assertInternalType('array', $fields);
        $this->assertGreaterThan(0, count($fields));
        $this->assertArrayHasKey('identifier', $fields);
    }

    public function testSetGetRftId()
    {
        $this->assertEquals($this->_testRftId2, $this->_djMetadataObj->rftId($this->_testRftId2)->rftId());
    }

    // TODO
    public function testSetGetResolver()
    {
        return false;
    }

    /**
     * @depends testSetGetRftId
     */
    public function testRefreshMetadata()
    {
        $fields = $this->_djMetadataObj->rftId($this->_testRftId2)->refreshMetadata()->fieldsArray();
        $this->assertInternalType('array', $fields);
        $this->assertGreaterThan(0, count($fields));
        $this->assertArrayHasKey('identifier', $fields);
        $this->assertEquals($this->_testRftId2, $fields['identifier']);
    }

    // TODO
    public function testAllLevelsCalc()
    {
        $this->assertInternalType('array', $this->_djMetadataObj->levelsArray());
    }

}
