<?php
/**
 * index.php
 *
 * @author Pachari Phaentong <pachari.pha@tkschemical.co.th>

 */

 
    // load Kotchasan
    include 'load.php';
    // Initial Kotchasan Framework
    $app = Kotchasan::createWebApplication('Gcms\Config');
    $app->run();

