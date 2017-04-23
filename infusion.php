<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
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
if (!defined("IN_FUSION")) {
    die("Access Denied");
}

// Infusion general information
$locale = fusion_get_locale("", CS_LOCALE);

$inf_title = $locale['CS_title'];
$inf_description = $locale['CS_desc'];
$inf_version = "2.3";
$inf_developer = "Keddy<br /><a href='http://www.phpfusion.ro/'>PHP-Fusion Rom&#226;nia</a>";
$inf_email = "kmodsro@gmail.com";
$inf_weburl = "http://dev.kmods.ro";
$inf_folder = "cs_panel"; // The folder in which the infusion resides.
$inf_image = "cs.png";

//Administration panel
$inf_adminpanel[] = array(
    "title" => $locale['CS_admin1'],
    "image" => $inf_image,
    "panel" => "cs_panel_admin.php",
    "rights" => "S",
    "page" => 5
);

//Multilanguage table for Administration
$inf_mlt[] = array(
    "title" => $inf_title,
    "rights" => "CS"
);
// Delete any items not required below.
$inf_newtable[] = DB_SERVER." (
server_id smallint(5) unsigned NOT NULL auto_increment,
server_ip varchar(45) NOT NULL DEFAULT '' ,
server_port varchar(5) NOT NULL DEFAULT '' ,
server_player varchar(2) NOT NULL DEFAULT '' ,
server_cod varchar(25) NOT NULL DEFAULT '' ,
server_modul varchar(25) NOT NULL DEFAULT '' ,
server_type varchar(25) NOT NULL DEFAULT '',
server_language varchar(25) NOT NULL DEFAULT '',
PRIMARY KEY (server_ip),
UNIQUE id (server_id)
)ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";

// shoutbox deletion of MLT shouts
$enabled_languages = makefilelist(LOCALE, ".|..", TRUE, "folders");
if (!empty($enabled_languages)) {
    foreach ($enabled_languages as $language) {
        $locale = fusion_get_locale('', LOCALE.$language."/setup.php");
        $mlt_deldbrow[$language][] = DB_SERVER." WHERE server_language='".$language."'";
    }
}

//Infuse insertations
$inf_insertdbrow[] = DB_PANELS." (panel_name, panel_filename, panel_content, panel_side, panel_order, panel_type, panel_access, panel_display, panel_status, panel_url_list, panel_restriction, panel_languages) VALUES('".fusion_get_locale("CS_title",
                                                                                                                                                                                                                            CS_LOCALE)."', 'cs_panel', '', '2', '3', 'file', '0', '1', '1', '', '3', '".fusion_get_settings('enabled_languages')."')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('servers_in_panel', '10', '".$inf_folder."')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('servers_per_page', '10', '".$inf_folder."')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('show_players', '0', '".$inf_folder."')";

//Defuse cleaning
$inf_droptable[] = DB_SHOUTBOX;
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='S'";
$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='".$inf_folder."'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";
$inf_deldbrow[] = DB_LANGUAGE_TABLES." WHERE mlt_rights='CS'";
?>