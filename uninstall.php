<?php
/**
 * Uninstall data.
 *
 * @package   Cherry_Real_Estate
 * @author    Template Monster
 * @license   GPL-3.0+
 * @copyright 2002-2016, Template Monster
 */

// Make sure we're actually uninstalling the plugin.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

require_once 'cherry-real-estate.php';

Cherry_Real_Estate::uninstall();
