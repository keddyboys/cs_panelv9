<?php

/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: counter.inc
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

class Servers {
    protected static $cs_settings = array();
    private static $instance = NULL;
    private static $locale = array();
	private static $limit = 4;
    private $postLink = '';
    private $data = array(
        'server_id'      => 0,
        'server_ip'      => '',
        'server_port'    => '27015',
        'server_player'  => '',
        'server_cod'     => '',
		'server_modul'   => '',
		'server_type'    => ''
    );
	
   private static $default_params = array(
        'csform_name' => '',
        'cs_db'       => '',
        'cs_limit'    => ''
    );
	public function __construct() {
        require_once INCLUDES."infusions_include.php";
        
        self::$locale = fusion_get_locale("", INFUSIONS."counter_strike_panel/locale/".LANGUAGE.".php");
        $cs_settings = self::get_cs_settings();
        self::$limit = $cs_settings['servers_in_panel'];
        $_GET['s_action'] = isset($_GET['s_action']) ? $_GET['s_action'] : '';
        $this->postLink = FORM_REQUEST;
        $this->postLink = preg_replace("^(&amp;|\?)s_action=(edit|delete)&amp;server_id=\d*^", "", $this->postLink);
        $this->sep = stristr($this->postLink, "?") ? "&amp;" : "?";

        switch ($_GET['s_action']) {
            case 'delete':
                self::delete_select($_GET['server_id']);
                break;
            case 'delete_select':
                if (empty($_POST['rights'])) {
                    \defender::stop();
                    addNotice('danger', self::$locale['CS_010']);
                    redirect(clean_request("", array("section=server", "aid"), TRUE));
                }
                self::delete_select($_POST['rights']);
                break;
            case 'edit':
                $this->data = self::_selectedCS($_GET['server_id']);
                break;
            default:
                break;
        }
    }
    
    public static function code_test($d) {
	
 	    $code = array(
	         '1' => self::$locale['CS_051'], 
	         '2' => self::$locale['CS_052'], 
	         '3' => self::$locale['CS_053'], 
             '4' => self::$locale['CS_054'],
	         '5' => self::$locale['CS_055'],
	         '6' => self::$locale['CS_056']
	    ); 
        return $code[$d];        
	}
	
	public static function mod_test($d) {
	    $mod = array(
            '1' => self::$locale['CS_061'], 
            '2' => self::$locale['CS_062'], 
            '3' => self::$locale['CS_063'], 
            '4' => self::$locale['CS_064'], 
            '5' => self::$locale['CS_065']
        );   
 
        return $mod[$d];  
    }
	
	public static function type_test($d) {
	   
 	    $type = array(
	         '1' => self::$locale['CS_070'], 
	         '2' => self::$locale['CS_071'], 
	         '3' => self::$locale['CS_072'], 
             '4' => self::$locale['CS_073'],
	         '5' => self::$locale['CS_074'],
	         '6' => self::$locale['CS_075']
	    ); 
        return $type[$d];        
	}
	
	public static function getInstance($key = TRUE) {
        if (self::$instance === NULL) {
            self::$instance = new static();
	        self::$instance->set_db();
	
    	}

        return self::$instance;
    }
	
	
	public static function get_cs_settings() {
        if (empty(self::$cs_settings)) {
            self::$cs_settings = get_settings("counter_strike_panel");
        }

        return self::$cs_settings;
    }
	
	protected function set_server() {
        global $aidlink;

        $locale = fusion_get_locale("", INFUSIONS."counter_strike_panel/locale/".LANGUAGE.".php");

        if (isset($_POST['save'])) {

            $data = array(
                'server_id'     => form_sanitizer($_POST['server_id'], 0, 'server_id'),
                'server_ip'     => form_sanitizer($_POST['server_ip'], '', 'server_ip'),
				'server_port'   => form_sanitizer($_POST['server_port'], '', 'server_port'),
				'server_player' => form_sanitizer($_POST['server_player'], '', 'server_player'),
				'server_cod'    => form_sanitizer($_POST['server_cod'], '', 'server_cod'),
				'server_modul'  => form_sanitizer($_POST['server_modul'], '', 'server_modul'),
				'server_type'   => form_sanitizer($_POST['server_type'], '', 'server_type'),                
            );

            if (self::verify_server($data['server_id'])) {

                dbquery_insert(DB_SERVER, $data, 'update');

                if (\defender::safe()) {
                    addNotice('success', $locale['CS_003']);
                    //redirect(FUSION_SELF.$aidlink);
					redirect(clean_request("section=server_form", array("", "aid"), TRUE));
                }

            } else {

                dbquery_insert(DB_SERVER, $data, 'save');

                $data['server_id'] = dblastid();

                if (\defender::safe()) {
                    addNotice('success', $locale['CS_002']);
                    //redirect(FUSION_SELF.$aidlink);
					redirect(clean_request("section=server_form", array("", "aid"), TRUE));
                }
            }
			
		}
    }
	
	private function delete_select($id) {
        if (!empty($id)) {
            $cnt = count($id);
            $i = 0;
            if (is_array($id)) {
                foreach ($id as $key => $right) {
                    if (self::verify_server($key)) {
                        dbquery("DELETE FROM ".DB_SERVER." WHERE server_id='".intval($key)."'");
                        $i++;
                    }
                }
            } else {
                if (self::verify_server($id)) {
                    dbquery("DELETE FROM ".DB_SERVER." WHERE server_id='".intval($id)."'");
                    $i++;
                }

            }

            addNotice('warning', $cnt." / ".$i.' '.self::$locale['CS_009']);
        }

        defined('ADMIN_PANEL') ?
            redirect(clean_request("section=server", array("", "aid"), TRUE)) :
            redirect($this->postLink);
    }
	
	protected function verify_server($id) {
        if (isnum($id)) {
            return dbcount("(server_id)", DB_SERVER, "server_id='".intval($id)."'");
        }

        return FALSE;
    }
	
	public function _countCS($opt) {
        $DBc = dbcount("(server_id)", DB_SERVER, $opt);

        return $DBc;
    }
		
	public function _selectDB($rows, $min) {
        $result = dbquery("SELECT server_id, server_ip, server_port, server_player, server_cod, server_modul, server_type
            FROM ".DB_SERVER." 
            ORDER BY server_id DESC
            LIMIT ".intval($rows).", ".$min
        );

        return $result;
    }

    public function _selectedCS($ids) {
        if (self::verify_server($ids)) {
            $result = dbquery("SELECT server_id, server_ip, server_port, server_player, server_cod, server_modul, server_type
                FROM ".DB_SERVER."
                WHERE server_id=".intval($ids)
            );

            if (dbrows($result) > 0) {
                return $this->data = dbarray($result);
            }
        }
    }
    
    public function display_server_admin() {
	    self::$locale = fusion_get_locale("", INFUSIONS."counter_strike_panel/locale/".LANGUAGE.".php");
        $aidlink = fusion_get_aidlink();
        
		$allowed_section = array("server", "server_form", "server_settings");
        $_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'server';
        $edit = ((isset($_GET['action']) && $_GET['action'] == 'edit') && isset($_GET['server_id'])) ? TRUE : FALSE;
        $_GET['server_id'] = isset($_GET['server_id']) && isnum($_GET['server_id']) ? $_GET['server_id'] : 0;
        \PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb(['link' => INFUSIONS.'counter_strike_panel/counter_strike_admin.php'.fusion_get_aidlink(), "title" => self::$locale['CS_001']]);
        switch ($_GET['section']) {
            case "server_form":
               // add_to_title(self::$locale['edit']);
				\PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb(['link' => FUSION_REQUEST, "title" => $edit ? self::$locale['edit'] : self::$locale['CS_006']]);
                break;
            case "server_settings":
                //add_to_title(self::$locale['CS_020']);
				\PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb(['link' => FUSION_REQUEST, "title" => self::$locale['CS_020']]);
                break;
            default:
        }

        opentable(self::$locale['CS_admin1']);
        $master_tab_title['title'][] = self::$locale['CS_001'];
        $master_tab_title['id'][] = "server";
        $master_tab_title['icon'][] = "";

        $master_tab_title['title'][] = $edit ? self::$locale['edit'] : self::$locale['CS_006'];
        $master_tab_title['id'][] = "server_form";
        $master_tab_title['icon'][] = "";

        $master_tab_title['title'][] = self::$locale['CS_020'];
        $master_tab_title['id'][] = "server_settings";
        $master_tab_title['icon'][] = "";

        echo opentab($master_tab_title, $_GET['section'], "server", TRUE);
        switch ($_GET['section']) {
            case "server_form":
                add_to_title(self::$locale['edit']);
                $this->Server_AdminForm();
                break;
            case "server_settings":
                add_to_title(self::$locale['CS_020']);
                $this->settings_Form();
                break;
            default:
                add_to_title(self::$locale['CS_001']);
                $this->cs_listing();
                break;
        }
        echo closetab();
        closetable();
    }

    public function settings_Form() {
        $cs_settings = self::get_cs_settings();
        
        openside('');
        echo openform('server_settings', 'post', $this->postLink);
        $opts = array('1' => self::$locale['yes'], '0' => self::$locale['no'],);
		
        echo form_text('servers_in_panel', self::$locale['CS_011'], $cs_settings['servers_in_panel'], array('inline' => TRUE, 'inner_width' => '100px', "type" => "number"));
        echo form_text('servers_per_page', self::$locale['CS_012'], $cs_settings['servers_per_page'], array('inline' => TRUE, 'inner_width' => '100px', "type" => "number"));
		echo form_select('show_players', self::$locale['CS_013'], $cs_settings['show_players'], array('inline' => TRUE, 'inner_width' => '100px', 'options' => $opts));
		
        echo form_button('server_settings', self::$locale['save'], self::$locale['save'], array('class' => 'btn-success'));
        echo closeform();
        closeside();
    }	
	
	public function server_form() {
        defined('ADMIN_PANEL') ? fusion_confirm_exit() : "";
		$play = array('20' => "20", '10' => "10", '12' => "12", '14' => "14", '16' =>"16", '18' =>"18",'20' =>"20",'22' =>"22",'24' =>"24",'26' =>"26",'28' =>"28",'30' =>"30",'32' =>"32");
		$code = array('1' => self::$locale['CS_051'], '2' => self::$locale['CS_052'], '3' => self::$locale['CS_053'], '4' => self::$locale['CS_054'], '5' =>self::$locale['CS_055'], '6' => self::$locale['CS_056']);
		
		$mod = array('1' => self::$locale['CS_061'], '2' => self::$locale['CS_062'], '3' => self::$locale['CS_063'], '4' => self::$locale['CS_064'], '5' => self::$locale['CS_065'],);
		
		$typ = array('1' => self::$locale['CS_070'], '2' => self::$locale['CS_071'], '3' => self::$locale['CS_072'], '4' => self::$locale['CS_073'], '5' => self::$locale['CS_074']);
		openside(self::$locale['CS_title']);
                
				echo openform(self::$default_params['csform_name'], 'post', $this->postLink);
                echo form_hidden('server_id', '', $this->data['server_id']);
                
                echo form_text('server_ip', self::$locale['CS_133'], $this->data['server_ip'], array('inline' => FALSE, 'inner_width' => '300px', 'required' => 1));
     		    echo form_text('server_port', self::$locale['CS_134'], $this->data['server_port'], array('inline' => FALSE, 'inner_width' => '300px', 'required' => 1));
		        echo form_select('server_player', self::$locale['CS_135'], $this->data['server_player'], array('inline' => FALSE, 'inner_width' => '300px', 'options' => $play, 'required' => 0));
			    echo form_select('server_cod', self::$locale['CS_136'], $this->data['server_cod'], array('inline' => FALSE, 'inner_width' => '300px', 'options' => $code));
                echo form_select('server_modul', self::$locale['CS_137'], $this->data['server_modul'], array('inline' => FALSE, 'inner_width' => '300px', 'options' => $mod, 'required' => 0));
	    	    echo form_select('server_type', self::$locale['CS_138'], $this->data['server_type'], array('inline' => FALSE, 'inner_width' => '300px', 'options' => $typ, 'required' => 0));
        

                echo "</ul>\n";
                echo "</div>\n";
                echo form_button('add_server', empty($_GET['server_id']) ? self::$locale['CS_022'] : self::$locale['CS_023'], empty($_GET['server_id']) ? self::$locale['CS_022'] : self::$locale['CS_023'], array('class' => 'btn-primary btn-block'));
            

                echo closeform();
    }
    
	public function cs_listing() {
	
        $total_rows = $this->_countCS("");
        $rowstart = isset($_GET['rowstart']) && ($_GET['rowstart'] <= $total_rows) ? $_GET['rowstart'] : 0;
        $result = $this->_selectDB($rowstart, self::$limit);
        $rows = dbrows($result);
        
                echo "<div class='clearfix'>\n";
                echo "<span class='pull-right m-t-10'>".sprintf(self::$locale['CS_007'], $rows, $total_rows)."</span>\n";
                echo "</div>\n";
                echo openform('cs_form', 'post', $this->postLink."&amp;section=server&amp;s_action=delete_select");
				echo "<table class='table table-responsive table-hover'>\n";    
        if ($rows > 0) {
        
        		
                echo "<tr>\n";
                echo "<th>#</th>\n";
                echo "<th>".self::$locale['CS_131']."</th>\n";
				echo "<th>".self::$locale['CS_133']."</th>\n";
                echo "<th>".self::$locale['CS_134']."</th>\n";
                echo "<th>".self::$locale['CS_135']."</th>\n";
                echo "<th>".self::$locale['CS_136']."</th>\n";
                echo "<th>".self::$locale['CS_137']."</th>\n";
		        echo "<th>".self::$locale['CS_138']."</th>\n";
		        echo "<th>".self::$locale['CS_021']."</th>\n";
				echo "</tr>\n";
            
        		$ii = 1;	
            while ($cdata = dbarray($result)) {
                echo "<tr class='list-result pointer'>\n";
				echo "<td class='text-center'>".form_checkbox("rights[".$cdata['server_id']."]", '', '')."</td>\n";
                echo "<td class='text-center'>".$ii."</td>\n";
                echo "<td class='col-sm-4'>".$cdata['server_ip']."\n</td>\n";
                echo "<td class='text-center'>".$cdata['server_port']."\n</td>\n";
                echo "<td class='text-center'>".$cdata['server_player']."\n</td>\n";
                echo "<td class='text-center'>".$this->code_test($cdata['server_cod'])."\n</td>\n";
                echo "<td class='text-center'>".$this->mod_test($cdata['server_modul'])."\n</td>\n";
                echo "<td class='text-center'>".$this->type_test($cdata['server_type'])."\n</td>\n";
                echo "<td class='col-sm-5'>\n";
				echo "<a class='btn btn-default' href='".FORM_REQUEST."&amp;section=server_form&amp;s_action=edit&amp;server_id=".$cdata['server_id']."'>".self::$locale['edit']."</a>\n";
                echo "<a class='btn btn-danger' href='".FORM_REQUEST."&amp;section=server_form&amp;s_action=delete&amp;server_id=".$cdata['server_id']."' onclick=\"return confirm('".self::$locale['CS_014']."');\">".self::$locale['delete']."</a>\n</td>\n";
				echo "</tr>\n";
				$ii++;
            }
			echo "<tr>\n<td colspan='9' class='text-left'>\n";
			echo form_button('cs_admins', self::$locale['CS_025'], self::$locale['CS_025'], array('class' => 'btn-danger', 'ico' => 'fa fa-trash'));
            echo "</td>\n</tr>\n</tbody>\n";            
            echo closeform();
            echo ($total_rows > $rows) ? makepagenav($rowstart, self::$limit, $total_rows, self::$limit, clean_request("", array("aid", "section"), TRUE)."&amp;") : "";
        } else {
            echo "<tr>\n";
            echo "<td colspan='8' class='text-center'>\n<div class='well'>\n".self::$locale['CS_008']."</div>\n</td>\n";
            echo "</tr>\n";
			
        }
		    echo "</table>\n";
            echo "</div>\n";
    }
	
    private function set_db() {
        if (isset($_POST['add_server'])) { 
            $this->data = array(
                'server_id'     => form_sanitizer($_POST['server_id'], 0, 'server_id'),
				'server_ip'     => form_sanitizer($_POST['server_ip'], '', 'server_ip'),
				'server_port'   => form_sanitizer($_POST['server_port'], '', 'server_port'),
				'server_player' => form_sanitizer($_POST['server_player'], '', 'server_player'),
				'server_cod'    => form_sanitizer($_POST['server_cod'], '', 'server_cod'),
				'server_modul'  => form_sanitizer($_POST['server_modul'], '', 'server_modul'),
				'server_type'   => form_sanitizer($_POST['server_type'], '', 'server_type')
            );
                if (\defender::safe()) {
                    dbquery_insert(DB_SERVER, $this->data, empty($this->data['server_id']) ? "save" : "update");
                    addNotice("success", empty($this->data['server_id']) ? self::$locale['CS_002'] : self::$locale['CS_003']);
					
                }

            var_dump($this->data);
            defined('ADMIN_PANEL') ?
                redirect(clean_request("section=server", array("", "aid"), TRUE)) :
                redirect($this->postLink);
        }

        if (isset($_POST['cs_settings'])) {
            $inputArray = array(
                'servers_in_panel'   => form_sanitizer($_POST['servers_in_panel'], 0, "servers_in_panel"),
				'servers_per_page'   => form_sanitizer($_POST['servers_per_page'], 0, "servers_per_page"),
                'show_players'       => form_sanitizer($_POST['show_players'], 0, "show_players"),
            );

            if (\defender::safe()) {
                foreach ($inputArray as $settings_name => $settings_value) {
                    $inputSettings = array(
                        "settings_name" => $settings_name, "settings_value" => $settings_value, "settings_inf" => "counter_strike_panel",
                    );
                    dbquery_insert(DB_SETTINGS_INF, $inputSettings, "update", array("primary_key" => "settings_name"));
                }
                addNotice("success", self::$locale['CS_024']);
                redirect(clean_request("section=server_settings", array("", "aid"), TRUE));
            }
        }
    }	
	
    public function get_server() {
        self::$default_params = array(
            'csform_name' => 'cspanel',
            'cs_db'       => '?rowstart',
            'cs_limit'    => self::$limit,
        );
    
        self::server_list(self::$default_params);
    }
    
	public function Server_AdminForm() {
        self::$default_params = array(
            'csform_name' => 'cs_admin',
            'cs_db'       => '?rowstart',
            'cs_limit'    => self::$limit,
        );

        self::server_form(self::$default_params);
    }
	
	public static function server_list() {
        global $aidlink;
		$locale = fusion_get_locale("", INFUSIONS."counter_strike_panel/locale/".LANGUAGE.".php");
	    $result = dbquery("SELECT server_id, server_ip, server_port, server_player, server_cod, server_modul, server_type FROM ".DB_SERVER." ORDER BY server_id ASC");
        if (dbrows($result) > 0) {
                echo "<div class='m-t-20'>\n";
                echo "<table class='table table-responsive table-hover'>\n";
                echo "<tr>\n";
                echo "<th>".$locale['CS_131']."</th>\n";
                echo "<th>".$locale['CS_132']."</th>\n";
				echo "<th>".$locale['CS_133']."</th>\n";
                echo "<th>".$locale['CS_134']."</th>\n";
                echo "<th>".$locale['CS_135']."</th>\n";
                echo "<th>".$locale['CS_136']."</th>\n";
                echo "<th>".$locale['CS_137']."</th>\n";
		        echo "<th>".$locale['CS_138']."</th>\n";
		        //if (iADMIN && checkrights("CS")) {
				//echo "<th>".$locale['CS_022']."</th>\n";
				//}
				echo "</tr>\n";
                $i = 1;
    		while ($cdata = dbarray($result)) {
      			
				echo "<tr class='list-result pointer'>\n";
				echo "<td class='col-sm-4'>".$i."</td>\n";
				echo "<td class='col-sm-4'><a href='#' onclick=window.open('".INFUSIONS."counter_strike_panel/stats.php?id=".$cdata['server_id']."','','scrollbars=yes,width=600,height=600')>\n";
                echo "<img src='".INFUSIONS."counter_strike_panel/img/verifica.gif' alt=''/></a></td>\n";
                echo "<td class='col-sm-4'>".$cdata['server_ip']."\n</td>\n";
                echo "<td class='col-sm-4'>".$cdata['server_port']."\n</td>\n";
                echo "<td class='col-sm-4'>".$cdata['server_player']."\n</td>\n";
                echo "<td class='col-sm-4'>".self::code_test($cdata['server_cod'])."\n</td>\n";
                echo "<td class='col-sm-4'>".self::mod_test($cdata['server_modul'])."\n</td>\n";
                echo "<td class='col-sm-4'>".self::type_test($cdata['server_type'])."\n</td>\n";
				//if (iADMIN && checkrights("CS")) {
                //echo "<td class='col-sm-4'>\n";
				//echo "<a class='btn btn-default' href='".INFUSIONS."counter_strike_panel/counter_strike_admin.php&amp;section=server_form&amp;s_action=edit&amp;server_id=".$cdata['server_id']."'>";
                //echo "<i class='fa fa-edit fa-fw'></i> ".self::$locale['edit'];
                //echo "</a>";
                //echo "<a class='btn btn-danger' href='".FORM_REQUEST."&amp;section=server_form&amp;s_action=delete&amp;server_id=".$cdata['server_id']."' onclick=\"return confirm('".self::$locale['CS_014']."');\">";
                //echo "<i class='fa fa-trash fa-fw'></i> ".self::$locale['delete'];
                //echo "</a>";
				//echo "</td>\n";
				//}
				echo "</tr>\n";
				
				$i++;
            }
            echo "</tbody>\n";
        } else {
            echo "<tr>\n";
            echo "<td colspan='7' class='text-center'>\n<div class='well'>\n".$locale['CS_008']."</div>\n</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
        echo "</div>\n";
    }
	
	
}	
	
 ?>