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
include_once CS_INCLUDES."postinstall.php";
// Infusion general information
$locale = fusion_get_locale("", CS_LOCALE);

$inf_title = $locale['CS_title'];
$inf_description = $locale['CS_desc'];
$inf_version = "2.3";
$inf_developer = "Keddy <a href='http://www.phpfusion.ro/'>PHP-Fusion Rom&#226;nia</a>";
$inf_email = "kmodsro@gmail.com";
$inf_weburl = "http://dev.kmods.ro";
$inf_folder = "counter_strike_panel"; // The folder in which the infusion resides.
$inf_image = "cs.png";

//Administration panel
$inf_adminpanel[] = array(
    "title" => $locale['CS_admin1'],
    "image" => $inf_image,
    "panel" => "counter_strike_admin.php",
    "rights" => "CS",
    "page" => 5
);
//Multilanguage table for Administration
$inf_mlt[] = array(
    "title" => $inf_title,
    "rights" => "CS"
);

// Delete any items not required below.
$inf_newtable[] = DB_SERVER." (
server_id smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
server_ip varchar(45) NOT NULL DEFAULT '' ,
server_port varchar(5) NOT NULL DEFAULT '' ,
server_player varchar(2) NOT NULL DEFAULT '' ,
server_cod varchar(25) NOT NULL DEFAULT '' ,
server_modul varchar(25) NOT NULL DEFAULT '' ,
server_type varchar(25) NOT NULL DEFAULT '',
server_order SMALLINT(5) UNSIGNED NOT NULL,
PRIMARY KEY (server_ip),
UNIQUE id (server_id)
)ENGINE=MyISAM DEFAULT CHARSET=UTF8 COLLATE=utf8_unicode_ci";


//Infuse insertations
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('servers_in_panel', '5', '".$inf_folder."')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('servers_per_page', '10', '".$inf_folder."')";
$inf_insertdbrow[] = DB_SETTINGS_INF." (settings_name, settings_value, settings_inf) VALUES('show_players', '1', '".$inf_folder."')";
$inf_insertdbrow[] = DB_SITE_LINKS." (link_name, link_url, link_visibility, link_position, link_window, link_order, link_status, link_language) VALUES ('".$locale['CS_006']."', 'infusions/counter_strike_panel/add_server.php', ".USER_LEVEL_MEMBER.", '1', '0', '20', '1', '".LANGUAGE."')";
//Defuse cleaning
$inf_droptable[] = DB_SERVER;
$inf_deldbrow[] = DB_ADMIN." WHERE admin_rights='CS'";
$inf_deldbrow[] = DB_PANELS." WHERE panel_filename='".$inf_folder."'";
$inf_deldbrow[] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";

$inf_deldbrow[] = DB_SITE_LINKS." WHERE link_url='infusions/counter_strike_panel/add_server.php'";

?>