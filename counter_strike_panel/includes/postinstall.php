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
/*require_once file_exists('maincore.php') ? 'maincore.php' : __DIR__."/../../../maincore.php";
if (file_exists(INFUSIONS."counter_strike_panel/locale/".$settings['locale'].".php")) {
	include INFUSIONS."counter_strike_panel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."counter_strike_panel/locale/English.php";
}
include INFUSIONS."counter_strike_panel/infusion_db.php";
	*/
include_once INCLUDES."infusions_include.php";
include_once CS_INCLUDES."counter.php";

$result = dbquery("SHOW TABLES LIKE '%".DB_SERVER."%'");
$result2 = dbquery("SELECT panel_name FROM ".DB_PANELS." WHERE panel_filename='counter_strike_panel'");

    if ((dbrows($result) == '1' ) && (dbrows($result2) == '0' )) {
        
		$order = dbresult(dbquery("SELECT MAX(panel_order) FROM ".DB_PANELS." WHERE panel_side='2'"),0)+1;
	$result = dbquery("INSERT INTO ".DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction, panel_languages) VALUES('".fusion_get_locale("CS_title",
                                                                                                                                                                                                                            CS_LOCALE)."', 'counter_strike_panel', '', '2', '5', 'file', '0', '1', '1', '', '2', '".fusion_get_settings('enabled_languages')."')");
    }	
?>