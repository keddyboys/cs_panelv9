<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: stats.php
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
require_once "../../maincore.php";
if (file_exists(INFUSIONS."counter_strike_panel/locale/".$settings['locale'].".php")) {
	include INFUSIONS."counter_strike_panel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."counter_strike_panel/locale/English.php";
}
include INFUSIONS."counter_strike_panel/infusion_db.php";
	
include_once INCLUDES."infusions_include.php";
include_once CS_INCLUDES."counter.php";

require_once THEMES."templates/header.php";


$default_opts = array(
  'http'=>array(
    'method'=>"GET",
    'user_agent'=>'Mozilla/5.0 (Linux; U; Android 0.5; en-us) AppleWebKit/522+ (KHTML, like Gecko) Safari/419.3'
  )
);
stream_context_set_default($default_opts);

require_once CS_INCLUDES."Game3/Autoloader.php"; 
 
$cs_settings = get_settings("counter_strike_panel");

error_reporting(E_ALL); 
$id = isset($_GET['id']) && isNum($_GET['id']) ? $_GET['id'] : "0";
$data = dbarray(dbquery("SELECT server_ip, server_port, server_type FROM ".DB_SERVER." WHERE server_id='".$id."'"));
header("Refresh: 30; url=".FUSION_SELF."?id=$id");
if ($data !=0) {
$server_ip = $data['server_ip'];
$server_port = $data['server_port'];
$server_type = $typo[$data['server_type']];


function gameTime($time, $units) {
    if ($time >= 86400) {
        return intval($time / 86400) . $units['days'] . gameTime($time % 86400, $units);
    } elseif ($time >= 3600) {
        return intval($time / 3600) . $units['hours'] . gameTime($time % 3600, $units);
    } elseif ($time >= 60) {
        return intval($time / 60) . $units['minutes'] . gameTime($time % 60, $units);
    } else {
        return intval($time) . $units['seconds'];
    }
}
$servers = [
    [
    'type'    => $server_type,
    'host'    => $server_ip.':'.$server_port,
    ]
];

$GameQ = new \GameQ\GameQ(); // or $GameQ = \GameQ\GameQ::factory();
$GameQ->addServers($servers);
$GameQ->setOption('timeout', 5); // seconds

$results = $GameQ->process();
$server = $results[$server_ip.':'.$server_port];


        	echo "<table border='0' class='margins' cellspacing='1' cellpadding='0' align='center'>\n";
            echo "<tr><td valign='top'>\n";
            echo "<table class='tbl1'>\n";
            echo "<tr><td valign='top'>\n";
            echo "<table border='0' class='margins' cellspacing='1' cellpadding='0' align='center'>\n<tr>\n";
            echo "<td align=center>\n";
            echo "<table border='0' width='100%'>\n<tr>\n";
	        echo "<td align='center'>\n<font size='3'>".$locale['CS_151']."</td>\n</tr>\n";
            echo "</table>";
	
if (!$server['gq_online']) {
        	echo "<center>".$locale['CS_161']."</center>";
} else {
            echo "<table width='100%' cellspacing=1 cellpadding=0 align='center'>\n<tr>\n";
		    echo "<td class='tbl1'>\n".$locale['CS_140']."</td>\n";
		    echo "<td class='tbl1'>\n". $server['hostname']."</td>\n";
		    echo "<td rowspan='10' align='center' class='tbl1'>\n";
		$tbl = "tbl".($i % 2 == 0 ? 2 : 1);
		
		$fileUrl = "https://image.gametracker.com/images/maps/160x120/cs/".$server['map'].".jpg";
        $AgetHeaders = @get_headers($fileUrl);
        if (preg_match("|200|", $AgetHeaders[0])) {
            echo "<img src='https://image.gametracker.com/images/maps/160x120/cs/".$server['map'].".jpg' width=160 height=120>";
        } else {
	        echo "<img src=img/no.gif width=160 height=120>"; 
	    }
		    echo "</td>\n</tr>\n<tr>\n";
		    echo "<td class='tbl2'>\n".$locale['CS_141']."</td>\n";
		    echo "<td class='tbl2'>\n".(isNum($server_ip) ? $server_ip : gethostbyname($server_ip))."</td>\n"; 
            echo "</tr>\n<tr>\n";
		    echo "<td class='tbl1'>\n".$locale['CS_142']."</td>\n";
		    echo "<td class='tbl1'>\n".$typ[$data['server_type']]."</td>\n";
	        echo "</tr>\n<tr>\n";
		    echo "<td class='tbl2'>\n".$locale['CS_143']."</td>\n";
		    echo "<td class='tbl2'>\n".$server['map']."</td>\n";
	        echo "</tr>\n<tr>\n";
		    echo "<td class='tbl1'>\n".$locale['CS_144']."</td>\n";
		    echo "<td class='tbl1'>\n".$server['amx_nextmap']."</td>\n";
	        echo "</tr>\n<tr>\n";
	        echo "<td class='tbl2'>\n".$locale['CS_145']."</td>\n";
	        echo "<td class='tbl2'>\n".$server['num_players']." / ".$server['max_players']."</td>\n";
	        echo "</tr>\n<tr>\n";
		    echo "<td class='tbl1'>\n".$locale['CS_146']."</td>\n";
		    echo "<td class='tbl1'>\n".(($server['secure'] == "1") ? $locale['CS_155'] : $locale['CS_156'])."</td>\n";
	        echo "</tr>\n<tr>\n";
		    echo "<td class='tbl2'>\n".$locale['CS_147']."</td>\n";
		    echo "<td class='tbl2'>\n".(($server['os'] == "w") ? $locale['CS_157'] : $locale['CS_158'])."</td>\n";
	        echo "</tr>\n<tr>\n";
		    echo "<td class='tbl1'>\n".$locale['CS_148']."</td>\n";
		    echo "<td class='tbl1'>\n".(($server['dedicated'] == "d") ? $locale['CS_138'] : $locale['CS_139'])."</td>\n";
	        echo "</tr>\n<tr>\n";
	        echo "<td class='tbl2'>\n".$locale['CS_149']."</td>\n";
	        echo "<td class='tbl2'>\n".(($server['gq_password'] == "false") ? $locale['CS_155'] : $locale['CS_156'])."</td>\n";
	        echo "</tr>\n<tr>\n";
	        echo "<td class='tbl1'>\n".$locale['CS_150']."</td>\n";
	        echo "<td class='tbl1'>\n".$server['protocol']."</td>\n";
	        echo "</tr>\n</table>\n";
            echo "</td>\n</tr>\n</table>\n";
            echo "<table cellpadding='0' cellaspacing='0' align='center'>\n<tr>\n<td>\n";

	if ($cs_settings['show_players'] == "1") {
		
           echo "<table border='0' width='458' align='center'>\n<tr>\n";
           echo "<td class='tbl2' align='center'>\n<strong>".$locale['CS_145']."</strong></td>\n";
           echo "</tr>\n</table>\n";

           echo "<table cellpadding=0 cellspacing=0 width='458' align='center'>\n";
           echo "<tr>\n<td align=center valign=top>\n";
           echo "<table width='100%' cellspacing=1 cellpadding=1>\n<tr>\n";
		
	       echo "<th class='tbl2'>\n<strong>".$locale['CS_131']."</strong></td>\n";	
           echo "<th class='tbl2'>\n<strong>".$locale['CS_135']."</strong></td>\n";
           echo "<th class='tbl2'>\n<strong>".$locale['CS_152']."</strong></td>\n";
           echo "<th class='tbl2'>\n<strong>".$locale['CS_153']."</strong></td>\n";
		   echo "<th class='tbl2'>\n<strong>".$locale['CS_154']."</strong></td>\n";
           echo "</tr>\n";

    
	    $ii=1;
        foreach( $server['players'] as $player ) {
		    
            $tbl = "tbl".($ii % 2 == 0 ? 2 : 1);
            echo "<tr>\n";
			echo "<td class='$tbl'>".($ii++)."</td>\n";
			echo "<td class='$tbl'>".htmlspecialchars($player['gq_name'])."</td>\n";
			echo "<td class='$tbl' align='right'>".$player['gq_score']."</td>\n";
			echo "<td class='$tbl' align='right'>".gameTime($player['time'], $locale['CS_timeUnits'])."</td>\n";
			echo "<td class='$tbl' align='right'>".rand(10,50)."</td>\n";
			echo "</tr>\n";
        }
    }		
		    echo "</table>\n";
            echo "</td>\n</tr>\n</table>\n";
            echo "<br /><center>\nCopyright &copy; 2016 <a href='http://dev.kmods.ro' target='_black'>Keddy</a>";
        
} 


            echo "<br /><a href='#'onclick='javascript:self.close()'>".$locale['CS_162']."</a>\n";
            echo "</body></html>";
} else {
            echo "<center>".$locale['CS_163'],"<br />\n";
            echo "<a href='#'onclick='javascript:self.close()'>".$locale['CS_162']."</a></center>\n";
}	
?>