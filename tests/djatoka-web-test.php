<?php

require_once("../lib/resolver.php");

$resolver = new Djatoka_Resolver('http://YOUR.HOST/adore-djatoka/resolver?');
$region = $resolver->region('0000002');
$metadata = $resolver->metadata('0000002');
//header('Content-Type:image/jpeg');
//echo $region->scale(100)->data();

/*print $resolver->baseUrl();
print "\n";*/
print '<img src="' . $region->scale(100)->url() . '"/>';
print '<img src="' . $region->scale(250)->url() . '"/>';
print '<img src="' . $region->scale(800)->url() . '"/>';
print '<img src="' . $region->scale(500)->square('center')->url() . '"/>';
print '<img src="' . $region->scale(500)->square('top_left')->url() . '"/>';
print '<img src="' . $region->scale(500)->square('bottom_right')->url() . '"/>';
print '<br />';
print_r($metadata->fields());
// id: 0000002
