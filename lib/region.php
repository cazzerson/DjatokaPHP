<?php

require_once('net.php');
require_once('resolver.php');
require_once('metadata.php');

class Djatoka_Region
{
    protected $_resolver;
    // One of rft_id, svc_id, svc.level, svc.rotate, svc.region, svc.scale, svc.format, svc.clayer
    protected $_query = array();
    protected $_djatokaConn;
    protected $_rftId;
    protected $_metadata;
    protected $_djatokaRegionSvcString = 'svc_id=info:lanl-repo/svc/getRegion&svc_val_fmt=info:ofi/fmt:kev:mtx:jpeg2000';


    function __construct($resolver, $rftId)
    {
        $this->resolver($resolver);
        $this->_rftId = $rftId;
        $this->_query['rft_id'] = $rftId;
        $this->_metadata = $this->_resolver->metadata($this->_rftId);
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

    // Found by jaron to improve image quality
    protected function _pickBestLevel($scale, $trimPercent=0)
    {
        $bestLevel = $this->_metadata->fields()->levels;

        if (!empty($scale)) {
            $fullLevels = $this->_metadata->levelsArray();
            krsort($fullLevels);
            // We don't need to check the best level--it's the default
            unset($fullLevels[$bestLevel]);

            foreach ($fullLevels as $level => $levelData) {
                // Make sure the level is plenty big enough, even with zoom/crops
                if ((($levelData['height'] * (1 - ($trimPercent*2))) >= $scale) && (($levelData['width'] * (1 - ($trimPercent*2))) >= $scale)) {
                    $bestLevel = $level;
                }
            }
        }
        return $bestLevel;
    }

    public function setClosestLevelToScale($scale)
    {
        $this->level($this->_pickBestLevel($scale));
        return $this;
    }

    protected function _buildRegionUrl()
    {
        $regionUrl = $this->_resolver->baseUrl() . '&' . $this->_djatokaRegionSvcString . '&' . http_build_query($this->_query);
        return $regionUrl;
    }

    // $trimPercent should be < 1 
    // e.g. .10 to trim 10% off of each edge
    public function square($cropFocus='center', $trimPercent=0)
    {
        // FIXME: Changing the level or the scale (esp. the level) after setting 
        // the square region will mess up the region coords
        $level = $this->_pickBestLevel($this->scale, $trimPercent);
        $this->level($level);
        $levelsArray = $this->_metadata->levelsArray();
        ksort($levelsArray);

        // Weirdly, the x,y coord is is relative to the maximum level
        // While the dimensions are relative to the requested level
        // Be careful here
        $height = $levelsArray[$level]['height'];
        $width = $levelsArray[$level]['width'];
        $maxLevel = array_pop($levelsArray);
        $maxHeight = $maxLevel['height'];
        $maxWidth = $maxLevel['width'];
        $heightTrim = (int) round($height * $trimPercent, 0);
        $maxHeightTrim = (int) round($maxHeight * $trimPercent, 0);
        $widthTrim = (int) round($width * $trimPercent, 0);
        $maxWidthTrim = (int) round($maxWidth * $trimPercent, 0);

        if ($height >= $width) { // Portrait
            $edgeDiff = $maxHeight - $maxWidth;
            $widthOffset = 0 + $maxWidthTrim;
            $trimmedWidth = $width - ($widthTrim * 2);
            $trimmedEdgeDiff = $edgeDiff + $widthOffset;

            if ('center' == $cropFocus) {
                $trim = (int) round($trimmedEdgeDiff / 2, 0);

                $regionStatement = "$trim,$widthOffset,$trimmedWidth,$trimmedWidth";
                $this->region("$trim,$widthOffset,$trimmedWidth,$trimmedWidth");
            } elseif ('top_left' == $cropFocus) {
                $this->region("$widthOffset,$widthOffset,$trimmedWidth,$trimmedWidth");
            } elseif ('bottom_right' == $cropFocus) {
                $this->region("{$trimmedEdgeDiff},$widthOffset,{$trimmedWidth},{$trimmedWidth}");
            } else {
                return false;
            }
        } elseif ($width > $height) { // Landscape
            $edgeDiff = $maxWidth - $maxHeight;
            $heightOffset = 0 + $maxHeightTrim;
            $trimmedHeight = $height - ($heightTrim * 2);
            $trimmedEdgeDiff = $edgeDiff + $heightOffset;

            if ('center' == $cropFocus) {
                $trim = (int) round($trimmedEdgeDiff / 2, 0);
                $this->region("$heightOffset,$trim,$trimmedHeight,$trimmedHeight");
            } elseif ('top_left' == $cropFocus) {
                $this->region("$heightOffset,$heightOffset,{$trimmedHeight},{$trimmedHeight}");
            } elseif ('bottom_right' == $cropFocus) {
                $this->region("$heightOffset,{$trimmedEdgeDiff},{$trimmedHeight},{$trimmedHeight}");
            } else {
                return false;
            }
        } 
       /* else { // This is already a square
            $this->region("0,0,{$height},{$width}");
       }*/
        // End crazymaking
        
        return $this;
    }

    public function queryFields()
    {
        return $this->_parseArrayToObject($this->_query);
    }

    public function queryFieldsArray()
    {
        return $this->_query;
    }

    public function scale($longEdge=false)
    {
        if (false === $longEdge) {
            if (!empty($this->_query['svc.scale'])) {
                return $this->_query['svc.scale'];
            } else {
                return false;
            }
        } else {
            $this->_query['svc.scale'] = (string) $longEdge;
            return $this;
        }
    }

    public function level($level=false)
    {
        if (false === $level) {
            if (isset($this->_query['svc.level'])) {
                return $this->_query['svc.level'];
            } else {
                return false;
            }
        } else {
            $this->_query['svc.level'] = $level;
            return $this;
        }
    }

    public function rotate($rotate=false)
    {
        if (false === $rotate) {
            if (isset($this->_query['svc.rotate'])) {
                return $this->_query['svc.rotate'];
            } else {
                return false;
            }
        } else {
            $this->_query['svc.rotate'] = $rotate;
            return $this;
        }
    }

    public function region($regionCoords=false)
    {
        if (false === $regionCoords) {
            if (isset($this->_query['svc.region'])) {
                return $this->_query['svc.region'];
            } else {
                return false;
            }
        } else {
            $this->_query['svc.region'] = $regionCoords;
            return $this;
        }
    }

    public function format($format=false)
    {
        if (false === $format) {
            if (isset($this->_query['svc.format'])) {
                return $this->_query['svc.format'];
            } else {
                return false;
            }
        } else {
            $this->_query['svc.format'] = $format;
            return $this;
        }
    }

    public function clayer($clayer=false)
    {
        if (false === $clayer) {
            if (isset($this->_query['svc.clayer'])) {
                return $this->_query['svc.clayer'];
            } else {
                return false;
            }
        } else {
            $this->_query['svc.clayer'] = $clayer;
            return $this;
        }
    }

    public function url()
    {
        return $this->_buildRegionUrl();
    }

    public function data()
    {
        // TODO: better error checking?
        $djatokaConn = new Djatoka_Net();
        $djatokaConn->connect($this->url());
        return $djatokaConn->content();
    }

    public function rftId()
    {
        return $this->_rftId;
    }

    public function resolver($resolver = false)
    {
        if (false === $resolver) {
            return $this->_resolver;
        } else {
            if (is_a($resolver, 'Djatoka_Resolver')) {
                $this->_resolver = $resolver;
                return $this;
            } else {
                throw new Exception('Invalid Djatoka Resolver');
            }
        }
    }

    public function reset()
    {
        $this->_query = array('rft_id' => $this->_query['rft_id']);
        return $this;
    }
}
