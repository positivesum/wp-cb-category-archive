<?php
/*
Plugin Name: WPCB Category Archive
Plugin URI: http://www.amiworks.org/
Description: WP plugin to show yearly category archive.
Version: 0.1
Author: Aman Kumar Jain
Author URI: http://amanjain.com
*/

function WPCBCategoryArchiveLoad()
{
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."WPCBCategoryArchive.php");
}
add_action('cfct-modules-loaded', 'WPCBCategoryArchiveLoad');