<?php

require_once('net.php');
require_once('metadata.php');
require_once('region.php');

class Djatoka_Resolver
{

    protected $_baseUrl;
    protected $_djatokaUrlVersion = 'url_ver=Z39.88-2004';
    protected $_djatokaRftIdPrefix = 'rft_id=';
    protected $_pingSvcString = 'svc_id=info:lanl-repo/svc/ping';
    protected $_djatokaConn;

    function __construct($baseUrl)
    {
        $this->baseUrl($baseUrl);
        $this->_djatokaConn = new Djatoka_Net();
    }

    public function pingRftId($rftId)
    {
        $pingStatus = false;

        $pingUrl = $this->rftIdUrl($rftId) . '&' . $this->_pingSvcString; 

        // TODO: Is this necessary with the net.php Exception?
        if ($this->_djatokaConn->connect($pingUrl)->OK()) {
            // TODO: get the content
            // Parse the json
            // Check the status
            $pingStatus = true;
        } else {
            $pingStatus = false;
        }

        return $pingStatus;
    }

    public function baseUrl($baseUrl=false)
    {
        if (false !== $baseUrl) {
            $this->_baseUrl = $baseUrl . $this->_djatokaUrlVersion;
            return $this;
        } else {
            return $this->_baseUrl;
        }
    }

    public function rftIdUrl($rftId)
    {
        return $this->_baseUrl . "&{$this->_djatokaRftIdPrefix}" . urlencode($rftId);
    }

    public function region($rftId)
    {
        return new Djatoka_Region($this, $rftId);
    }

    public function metadata($rftId)
    {
        return new Djatoka_Metadata($this, $rftId);
    }
}
