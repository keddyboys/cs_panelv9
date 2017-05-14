<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: postinstall.php
| Author: Keddy
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
include_once INCLUDES."infusions_include.php";
include_once CS_INCLUDES."counter_inc.php";
$locale = fusion_get_locale("", INFUSIONS."counter_strike_panel/locale/".LANGUAGE.".php");
$inf_folder = "counter_strike_panel";

$panel = dbquery("SELECT panel_name FROM ".DB_PANELS." WHERE panel_filename='".$inf_folder."'");
$link = dbquery("SELECT link_name FROM ".DB_SITE_LINKS." WHERE link_url='infusions/counter_strike_panel/add_server.php'");

    if (db_exists(DB_SERVER) && (dbrows($panel) == '0' )) {
        
		$panel_order = dbresult(dbquery("SELECT MAX(panel_order) FROM ".DB_PANELS." WHERE panel_side='2'"),0)+1;
	$result = dbquery("INSERT INTO ".DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction, panel_languages) VALUES('".$locale['counter_title']."', 'counter_strike_panel', '', '2', '".$panel_order."', 'file', '0', '1', '1', '".fusion_get_settings('opening_page')."', '2', '".LANGUAGE."')");
    
	} 
	if (db_exists(DB_SERVER) && (dbrows($link) == '0' )) {
        
		$site_order = dbresult(dbquery("SELECT MAX(link_order) FROM ".DB_SITE_LINKS." WHERE link_position='1'"),0)+1;
	$result = dbquery("INSERT INTO ".DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_status, link_language) VALUES('".$locale['counter_006']."', 'infusions/counter_strike_panel/add_server.php', ".USER_LEVEL_MEMBER.", '1', '0', '".$site_order."', '1', '".LANGUAGE."')");
	}
?>