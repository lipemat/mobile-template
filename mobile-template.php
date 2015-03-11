<?php
/*
Plugin Name: Mobile Template
Plugin URI: http://matlipe.com
Description: Template Overrides for Mobile Devices
Author: Mat Lipe
Version: 1.3.0
Author URI: http://matlipe.com
*/

/** USE $GLOBALS['gMobileTemplate']['device'] for current mobile theme directory **/

require('functions.php');
require('Mobile_Template/Mobile_Detect.php');
require('lib/MobileTemplate.php');

$MobileTemplate = new MobileTemplate;
