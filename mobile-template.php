<?php
/*
Plugin Name: Mobile Template
Plugin URI: http://lipeimagination.info 
Description: Template Overrides for Mobile Devices
Author: Mat Lipe
Version: 1.0
Author URI: http://lipeimagination.info/author/mat
*/

define('MOBILE_TEMPLATE_THEME', 'mintpress' );

require('functions.php');
require('lib/MobileTemplateMobileDetect.php');
require('lib/MobileTemplate.php');

$MobileTemplate = new MobileTemplate;
