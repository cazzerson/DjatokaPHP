<?php

class Djatoka_Net
{
    protected $_currentUrl;
    protected $_currentResponse = false;
    protected $_curlInfo;
    protected $_curlErrNo = 0;
    protected $_curlErrorStr = '';

    protected function _updateConnection()
    {
        // TODO: Make sure we have a valid URL
        //
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_USERAGENT, "DjatokaPHP Library");
        curl_setopt($ch, CURLOPT_URL, $this->_currentUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $this->_currentResponse = curl_exec($ch);
        $this->_curlInfo = curl_getinfo($ch);
        $this->_curlErrNo = curl_errno($ch);
        $this->_curlErrorStr = curl_error($ch);

        if (!$this->_currentResponse) {
            throw new Exception('Failed to connect to Djatoka');
        }

        curl_close($ch);
    }

    public function connect($url)
    {
        if (!isset($this->_currentUrl) || ($url != $this->_currentUrl)) {
            $this->_currentUrl = $url;
        }

        $this->_updateConnection();

        return $this;
    }

    public function OK()
    {
        // TODO: Make sure that Djatoka makes good use of HTTP error codes
        if (!empty($this->_currentResponse))
        {
            return true;
        }

        return false;
    }

    public function content()
    {
        return $this->_currentResponse;
    }

    public function contentType()
    {
        if (is_array($this->_curlInfo) && isset($this->_curlInfo['content_type'])) {
            return $this->_curlInfo['content_type'];
        } else {
            return false;
        }
    }

    public function httpCode()
    {
        if (is_array($this->_curlInfo) && isset($this->_curlInfo['http_code'])) {
            return $this->_curlInfo['http_code'];
        } else {
            return false;
        }
    }

/*    public function curlResponse()
    {
        if (!empty($this->_currentResponse)) {
            return $this->_currentResponse;
        }

        return false;
    }*/

}
