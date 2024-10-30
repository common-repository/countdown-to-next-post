<?php
/*
Countdown Timer Uninstall Module
Version 2.2.9.2 (kept in step with fergcorp_countdownTimer.php)
Copyright (c) 2008 Andrew Ferguson
---------------------------------------------------------------------------------
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/


if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();

//Remove options from wp_options table
delete_option('afdn_countdowntracker');
delete_option('afdn_countdownOptions');
delete_option('scott_timer_version');
delete_option('widget_scott_timer');

//Remove metadata for all users from wp_usermeta table
global $wpdb;
$wpdb->query("DELETE FROM $wpdb->usermeta WHERE `meta_key` LIKE '%scott'");
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name = 'scott_values'");
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name = 'scott_database'");

?>