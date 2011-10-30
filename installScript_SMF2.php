<?php 
/**
 * Sticky any Post (SaP)
 *
 * @package SaP
 * @author emanuele
 * @copyright 2011 emanuele, Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 0.1.4
 */

// If we have found SSI.php and we are outside of SMF, then we are running standalone.
if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF')) // If we are outside SMF and can't find SSI.php, then throw an error
	die('<b>Error:</b> Cannot install - please verify you put this file in the same place as SMF\'s SSI.php.');

db_extend('packages');
$smcFunc['db_add_column'] (
			'{db_prefix}topics', 
			array(
						'name' => 'sticky_message',
						'type' => 'VARCHAR',
						'size' => 150,
						'default' => ''
			),
			array(),
			'ignore'
		);

?>