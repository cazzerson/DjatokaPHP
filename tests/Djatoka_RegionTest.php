<?php

require_once('lib/region.php');
require_once('Djatoka_Test.php');

class Djatoka_RegionTest extends Djatoka_Test
{
    protected $_region;
    protected $_testResolver;

    public function setUp()
    {
        $this->_testResolver = new Djatoka_Resolver($this->_testBaseUrl);
        $this->_region = new Djatoka_Region($this->_testResolver, $this->_testRftId1);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('Djatoka_Region', $this->_region);
        $this->assertInstanceOf('Djatoka_Resolver', $this->_region->resolver());
        $this->assertEquals($this->_testRftId1, $this->_region->rftId());
    }

    public function testSetGetScale()
    {
        $this->assertEquals('75',$this->_region->scale(75)->scale());
    }

    public function testSetGetLevel()
    {
        $this->assertEquals('5',$this->_region->level(5)->level());
    }

    public function testSetGetRotate()
    {
        $this->assertEquals('180',$this->_region->rotate(180)->rotate());
    }

    public function testSetGetRegion()
    {
        $this->assertEquals('5,10,30,40',$this->_region->region('5,10,30,40')->region());
    }

    public function testSetGetClayer()
    {
        $this->assertEquals('2',$this->_region->clayer(2)->clayer());
    }

    // TODO
    public function testSetGetResolver()
    {
        return true;
    }

    // TODO: How to test the Url without just rewriting the method?
    //public function testUrl;

    public function testReset()
    {
        $this->_region->scale(75)->level(4)->rotate(180);
        $this->assertEquals(4, count($this->_region->queryFieldsArray()));
        $this->_region->reset();
        $query = $this->_region->queryFieldsArray();
        $this->assertEquals(1, count($query));
    }

    // TODO
    public function testSquare()
    {
        $this->_region->square('center');
        // TODO: check region query value
        // See if it is a square
        // See if it starts in the right place for the crop focus
        return true;
    }

    // TODO: How to do this? Just check to see if there is something there?
    //public function testData;

}
