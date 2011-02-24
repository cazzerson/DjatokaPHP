<?php

require_once('resolver.php');
require_once('net.php');

class Djatoka_Metadata
{

    protected $_resolver;
    protected $_rftId;
    protected $_metadataFields;
    protected $_allLevels;
    protected $_djatokaConn;
    protected $_djatokaMetadataSvcString = 'svc_id=info:lanl-repo/svc/getMetadata';
    protected $_needsUpdate;

    function __construct($resolver, $rftId=false)
    {
        $this->resolver($resolver);
        $this->_djatokaConn = new Djatoka_Net();

        if (false !== $rftId) {
            $this->rftId($rftId);
            $this->_retrieveMetadata();
        }
    }

    // Stolen from:
    // http://www.webdevlabs.net/2010/08/php-convert-array-to-object-and-vice.html
    protected function _parseArrayToObject($array) {
        $object = new stdClass();
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $name=>$value) {
                $name = strtolower(trim($name));
                if (!empty($name)) {
                    $object->$name = $value;
                }
            }
        }
        return $object;
    }

    protected function _retrieveMetadata()
    {
        $metadataUrl = $this->_resolver->baseUrl() . '&' . $this->_djatokaMetadataSvcString . "&rft_id={$this->_rftId}";

        $metadata_return = $this->_djatokaConn->connect($metadataUrl)->content();

        $this->_metadataFields = json_decode($metadata_return, $assoc=true);

        if (empty($this->_metadataFields)) {
            throw new Exception('Invalid JSON Metadata from Djatoka server');
        }

        $this->_calcAllLevels();

        return $this;
    }

    public function rftId ($rftId = false)
    {
        if (false === $rftId) {
            return $this->_rftId;
        } else {
            $this->_rftId = $rftId;
            $this->_needsUpdate = true;
            //$this->_retrieveMetadata();
            return $this;
        }
    }

    public function resolver($resolver = false)
    {
        if (false === $resolver) {
            return $this->_resolver;
        } else {
            if (is_a($resolver, 'Djatoka_Resolver')) {
                $this->_resolver = $resolver;
                $this->_needsUpdate = true;
                return $this;
            } else {
                throw new Exception('Invalid Djatoka Resolver');
            }
        }
    }

    public function fields()
    {
        if ($this->_needsUpdate) {
            $this->refreshMetadata();
            $this->_needsUpdate = false;
        }

        if (!empty($this->_metadataFields)) {
            return $this->_parseArrayToObject($this->_metadataFields);
        } else {
            return false;
        }
    }

    public function fieldsArray()
    {
        if ($this->_needsUpdate) {
            $this->refreshMetadata();
            $this->_needsUpdate = false;
        }

        if (!empty($this->_metadataFields)) {
            return $this->_metadataFields;
        } else {
            return false;
        }
    }

    protected function _calcAllLevels()
    {
        if (!empty($this->_metadataFields['levels'])) {
            // Contrary to the djatoka docs, this number is the maximum level (0-indexed)
            // NOT, the number of levels
            $highestLevel = (int) $this->_metadataFields['levels'];
            $allLevels = array();

            for ($currentLevel = 0; $currentLevel <= $highestLevel; $currentLevel++) {
                // Differences in level represent halving the dimensions of the image
                // from the next highest level
                $dimensionsDividend = pow(2, ($highestLevel - $currentLevel));


                // To copy Djatoka's level calcs...
                $allLevels[$currentLevel]['height'] = (int) round(floatval($this->_metadataFields['height']) / $dimensionsDividend, 0);
                $allLevels[$currentLevel]['width'] = (int) round(floatval($this->_metadataFields['width']) / $dimensionsDividend, 0);
            }
            $this->_allLevels = $allLevels;
        } else {
            return false;
        }

        return true;
    }

    // Currently broken because of numeric property names
/*    public function levels()
    {
        if ($this->_needsUpdate) {
            $this->refreshMetadata();
            $this->_needsUpdate = false;
        }

        if (!empty($this->_allLevels)) {
            return $this->_parseArrayToObject($this->_allLevels);
        } else {
            return false;
        }
}*/

    public function levelsArray()
    {
        if ($this->_needsUpdate) {
            $this->refreshMetadata();
            $this->_needsUpdate = false;
        }

        if (!empty($this->_allLevels)) {
            return $this->_allLevels;
        } else {
            return false;
        }
    }

    public function refreshMetadata()
    {
        if (isset($this->_rftId)) {
            $this->_retrieveMetadata();
        } else {
            return false;
        }

        return $this;
    }
}
