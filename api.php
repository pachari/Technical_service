<?php
/**
 * api.php
 * หน้าเพจสำหรับให้ API เรียกมา
 *
 * @author Goragod Wiriya <admin@goragod.com>
 
 */
// load Kotchasan
include 'load.php';
// Initial Kotchasan Framework
$app = Kotchasan::createWebApplication('Gcms\Config');
$app->defaultRouter = 'Gcms\Router';
$app->defaultController = 'Index\Api\Controller';
$app->run();
