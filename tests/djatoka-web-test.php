<?php

require_once("../lib/resolver.php");

$resolver = new Djatoka_Resolver('http://YOUR.HOST/adore-djatoka/resolver?');
$resolver = new Djatoka_Resolver('http://african.lanl.gov/adore-djatoka/resolver?');
$itemId = 'info:lanl-repo/ds/5aa182c2-c092-4596-af6e-e95d2e263de3';
$region = $resolver->region($itemId);
$metadata = $resolver->metadata($itemId);
//header('Content-Type:image/jpeg');
//echo $region->scale(100)->data();

/*print $resolver->baseUrl();
print "\n";*/
print '<img src="' . $region->scale(100)->url() . '"/>';
print '250 scale: <img src="' . $region->scale(250)->url() . '"/>';
print 'Best level for 250 scale, with browser scaling: <img width="250" src="' . $region->reset()->setClosestLevelToScale(250)->url() . '"/>';
print '<img src="' . $region->reset()->scale(800)->url() . '"/>';
print '<img src="' . $region->reset()->setClosestLevelToScale(500)->square('center')->url() . '"/>';
print '<img src="' . $region->reset()->scale(500)->square('top_left')->url() . '"/>';
print '<img src="' . $region->reset()->scale(500)->square('bottom_right')->url() . '"/>';
print '<br />';
print_r($metadata->fields());
