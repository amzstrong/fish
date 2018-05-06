<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 2018/4/19
 * Time: 下午10:40
 */


ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Shanghai');
defined('APP_PATH') or define('APP_PATH', __DIR__);
defined('debug_mod') or define('debug_mod', true);
require_once(APP_PATH . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/lib/AutoLoader.php');
