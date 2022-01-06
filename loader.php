<?php
/**
 * loader.php
 * หน้าเพจสำหรับให้ GLoader เรียกมา
 *
 * @author Goragod Wiriya <admin@goragod.com>
 
 */
// load Kotchasan
include 'load.php';
// Initial Kotchasan Framework
$app = Kotchasan::createWebApplication('Gcms\Config');
$app->defaultController = 'Index\Loader\Controller';
$app->run();
