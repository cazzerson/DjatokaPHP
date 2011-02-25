DjatokaPHP
==============

This library supports some interactions with the Djatoka image server, and uses a method-chaining design. It also currently supports the generation some pre-defined image square crops (top_left, center, bottom_right), as well as edge-trimming of these crops. Play around and let me know if you notice any problems or have any ideas for improvements. This documentation should get you started, but please see the code for the full set of methods for now.

Installation
--------------

Just stick the DjatokaPHP directory into your project directory and include DjatokaPHP/lib/resolver.php.

Example usage
--------------

    <?php

    require_once("djatokaPHP/lib/resolver.php");

    // Example resolver and ID
    $resolver = new Djatoka_Resolver('http://african.lanl.gov/adore-djatoka/resolver?');
    $itemId = 'info:lanl-repo/ds/5aa182c2-c092-4596-af6e-e95d2e263de3';
    $region = $resolver->region($itemId);
    $metadata = $resolver->metadata($itemId);
    ?>
    <html>
    <head>
    <title>DjatokaPHP Test</title>
    </head>
    <body>

    <?php
    print '<img src="' . $region->scale(100)->url() . '"/>';
    print '<img src="' . $region->scale(250)->rotate('90')->url() . '"/>';
    print '<img src="' . $region->scale(800)->rotate('180')->url() . '"/>';
    print '<img src="' . $region->scale(500)->rotate('0')->square('center')->url() . '"/>';

    // Trim 10% off of each edge for a nicer thumbnail
    print '<img src="' . $region->scale(250)->rotate('0')->square('center', .10)->url() . '"/>';
    ?>
    <div>
    <?php
    print_r($metadata->fields());
    ?>
    </div>
    </body>
    </html>



Or...


    <?php
    require_once("djatokaPHP/lib/resolver.php");

    // Example resolver and ID
    $resolver = new Djatoka_Resolver('http://african.lanl.gov/adore-djatoka/resolver?');
    $itemId = 'info:lanl-repo/ds/5aa182c2-c092-4596-af6e-e95d2e263de3';
    $region = $resolver->region($itemId);

    header('Content-Type:image/jpeg');
    echo $region->scale(600)->data();
    ?>

Credits
---------

Jason Casden

Based heavily on work in Ruby by Jason Ronallo:

[https://github.com/jronallo/djatoka](https://github.com/jronallo/djatoka)

Copyright
----------

Copyright (c) 2011 North Carolina State University. See LICENSE for details.
