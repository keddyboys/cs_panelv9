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
error_reporting(E_ALL); 
class Servers {
    protected static $cs_settings = array();
    private static $instance = NULL;
    private static $locale = array();
	private static $limit = 4;
	private static $plimit = 4;
    private $postLink = '';
    private $data = array(
        'server_id'       => 0,
        'server_name'       => '',
        'server_port'     => '27015',
        'server_player'   => '',
        'server_cod'      => '',
		'server_modul'    => '',
		'server_type'     => '',
		'server_order'    => '',
		'server_language' => ''
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
		self::$plimit = $cs_settings['servers_per_page'];
        $_GET['s_action'] = isset($_GET['s_action']) ? $_GET['s_action'] : '';
        $this->postLink = FORM_REQUEST;
        $this->postLink = preg_replace("^(&amp;|\?)s_action=(edit|delete)&amp;server_id=\d*^", "", $this->postLink);
        $this->sep = stristr($this->postLink, "?") ? "&amp;" : "?";

        switch ($_GET['s_action']) {
			case 'mu':
                self::move_up();
                break;
            case 'md':
                self::move_down();
                break;
            case 'delete':
                self::delete_select($_GET['server_id']);
                break;
            case 'delete_select':
                if (empty($_POST['rights'])) {
                    \defender::stop();
                    addNotice('danger', self::$locale['counter_010']);
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

            addNotice('warning', $cnt." / ".$i.' '.self::$locale['counter_009']);
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
	
	public static function _countCS($opt) {
        $DBc = dbcount("(server_id)", DB_SERVER, $opt);

        return $DBc;
    }
		
	public static function _selectDB($rows, $min) {
        $result = dbquery("SELECT server_id, server_name, server_port, server_player, server_cod, server_modul, server_type, server_order
            FROM ".DB_SERVER." 
            ORDER BY server_order ASC
            LIMIT ".intval($rows).", ".$min
        );

        return $result;
    }

    public function _selectedCS($ids) {
        if (self::verify_server($ids)) {
            $result = dbquery("SELECT server_id, server_name, server_port, server_player, server_cod, server_modul, server_type, server_order
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
        $edit = ((isset($_GET['s_action']) && $_GET['s_action'] == 'edit') && isset($_GET['server_id'])) ? TRUE : FALSE;
        $_GET['server_id'] = isset($_GET['server_id']) && isnum($_GET['server_id']) ? $_GET['server_id'] : 0;
        \PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb(['link' => INFUSIONS.'counter_strike_panel/counter_strike_admin.php'.fusion_get_aidlink(), "title" => self::$locale['counter_001']]);
        switch ($_GET['section']) {
            case "server_form":
               \PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb(['link' => FUSION_REQUEST, "title" => $edit ? self::$locale['counter_005'] : self::$locale['counter_006']]);
                break;
            case "server_settings":
                \PHPFusion\BreadCrumbs::getInstance()->addBreadCrumb(['link' => FUSION_REQUEST, "title" => self::$locale['counter_020']]);
                break;
            default:
        }

        opentable(self::$locale['counter_admin1']);
        $master_tab_title['title'][] = self::$locale['counter_001'];
        $master_tab_title['id'][] = "server";
        $master_tab_title['icon'][] = "";

        $master_tab_title['title'][] = $edit ? self::$locale['counter_005'] : self::$locale['counter_006'];
        $master_tab_title['id'][] = "server_form";
        $master_tab_title['icon'][] = "";

        $master_tab_title['title'][] = self::$locale['counter_020'];
        $master_tab_title['id'][] = "server_settings";
        $master_tab_title['icon'][] = "";

        echo opentab($master_tab_title, $_GET['section'], "server", TRUE);
        switch ($_GET['section']) {
            case "server_form":
                add_to_title(self::$locale['edit']);
                $this->Server_AdminForm();
                break;
            case "server_settings":
                add_to_title(self::$locale['counter_020']);
                $this->settings_Form();
                break;
            default:
                add_to_title(self::$locale['counter_001']);
                $this->server_listing();
                break;
        }
        echo closetab();
        closetable();
    }

    public function settings_Form() {
        $cs_settings = self::get_cs_settings();
        
        openside('');
        echo openform('counter', 'post', $this->postLink);
        $opts = array('1' => self::$locale['yes'], '0' => self::$locale['no'],);
		
        echo form_text('servers_in_panel', self::$locale['counter_011'], $cs_settings['servers_in_panel'], array('inline' => TRUE, 'inner_width' => '100px', "type" => "number"));
        echo form_text('servers_per_page', self::$locale['counter_012'], $cs_settings['servers_per_page'], array('inline' => TRUE, 'inner_width' => '100px', "type" => "number"));
		echo form_select('show_players', self::$locale['counter_013'], $cs_settings['show_players'], array('inline' => TRUE, 'inner_width' => '100px', 'options' => $opts));
		
        echo form_button('counter_settings', self::$locale['save'], self::$locale['save'], array('class' => 'btn-success'));
        echo closeform();
        closeside();
    }	
	
	public function server_form() {
        defined('ADMIN_PANEL') ? fusion_confirm_exit() : "";
		$play = array('20' => "20", '10' => "10", '12' => "12", '14' => "14", '16' =>"16", '18' =>"18",'20' =>"20",'22' =>"22",'24' =>"24",'26' =>"26",'28' =>"28",'30' =>"30",'32' =>"32");
		$code = array('1' => self::$locale['counter_051'], '2' => self::$locale['counter_052'], '3' => self::$locale['counter_053'], '4' => self::$locale['counter_054'], '5' =>self::$locale['counter_055'], '6' => self::$locale['counter_056']);
		
		$mod = array('1' => self::$locale['counter_061'], '2' => self::$locale['counter_062'], '3' => self::$locale['counter_063'], '4' => self::$locale['counter_064'], '5' => self::$locale['counter_065'],);
		
		$typ = array('1' => self::$locale['counter_071'], '2' => self::$locale['counter_072'], '3' => self::$locale['counter_073'], '4' => self::$locale['counter_074'], '5' => self::$locale['counter_075']);
		openside(self::$locale['counter_title']);
                
				echo openform(self::$default_params['csform_name'], 'post', $this->postLink);
                echo form_hidden('server_id', '', $this->data['server_id']);
                
                echo form_text('server_name', self::$locale['counter_133'], $this->data['server_name'], array('inline' => TRUE, 'inner_width' => '300px', 'required' => 1));
     		    echo form_text('server_port', self::$locale['counter_134'], $this->data['server_port'], array('inline' => TRUE, 'inner_width' => '300px', 'required' => 1));
		        echo form_select('server_player', self::$locale['counter_135'], $this->data['server_player'], array('inline' => TRUE, 'inner_width' => '300px', 'options' => $play, 'required' => 0));
			    echo form_select('server_cod', self::$locale['counter_136'], $this->data['server_cod'], array('inline' => TRUE, 'inner_width' => '300px', 'options' => $code, 'required' => 0));
                echo form_select('server_modul', self::$locale['counter_137'], $this->data['server_modul'], array('inline' => TRUE, 'inner_width' => '300px', 'options' => $mod, 'required' => 0));
	    	    echo form_select('server_type', self::$locale['counter_138'], $this->data['server_type'], array('inline' => TRUE, 'inner_width' => '300px', 'options' => $typ, 'required' => 0));
                echo form_text('server_order', self::$locale['counter_026'], $this->data['server_order'], array('inline' => TRUE, 'inner_width' => '100px', 'required' => 0));
                echo "</ul>\n";
                echo "</div>\n";
                echo form_button('save_server', empty($_GET['server_id']) ? self::$locale['counter_006'] : self::$locale['counter_023'], empty($_GET['server_id']) ? self::$locale['counter_006'] : self::$locale['counter_023'], array('class' => 'btn-primary btn-block'));
            

                echo closeform();
    }
    
	public function cs_listing($info) {
	    
		$aidlink = fusion_get_aidlink();
        $total_rows = $this->_countCS("");
        $rowstart = isset($_GET['rowstart']) && ($_GET['rowstart'] <= $total_rows) ? $_GET['rowstart'] : 0;
        $result = $this->_selectDB($rowstart, $info['cs_limit']);
		
        $rows = dbrows($result);
        
                echo "<div class='clearfix'>\n";
                echo "<span class='pull-right m-t-10'>".sprintf(self::$locale['counter_007'], $rows, $total_rows)."</span>\n";
                echo "</div>\n";
                echo openform('cs_form', 'post', $this->postLink."&amp;section=server&amp;s_action=delete_select");
				echo "<table class='table table-responsive table-hover'>\n";    
			$has_entypo = fusion_get_settings("entypo") ? TRUE : FALSE;
            $has_fa = fusion_get_settings("fontawesome") ? TRUE : FALSE;
            $ui_label = array(
                "move_up"         => $has_entypo ? "<i class='entypo up-bold m-r-10'></i>" : $has_fa ? "<i class='fa fa-angle-up fa-lg m-r-10'></i>" : self::$locale['counter_029'],
                "move_down"       => $has_entypo ? "<i class='entypo down-bold m-r-10'></i>" : $has_fa ? "<i class='fa fa-angle-down fa-lg m-r-10'></i>" : self::$locale['counter_030'],
            );
        if ($rows > 0) {
        
        		echo "<tr>\n";
                echo "<th>#</th>\n";
                echo "<th>".self::$locale['counter_131']."</th>\n";
				echo "<th>".self::$locale['counter_133']."</th>\n";
                echo "<th>".self::$locale['counter_134']."</th>\n";
                echo "<th>".self::$locale['counter_135']."</th>\n";
                echo "<th>".self::$locale['counter_136']."</th>\n";
                echo "<th>".self::$locale['counter_137']."</th>\n";
		        echo "<th>".self::$locale['counter_138']."</th>\n";
		        echo "<th>".self::$locale['counter_026']."</th>\n";
				echo "<th>".self::$locale['counter_021']."</th>\n";
				echo "</tr>\n";
            
        		$ii = 0;	
            while ($data = dbarray($result)) {
				$ii++;
                echo "<tr class='list-result pointer'>\n";
				echo "<td class='text-center'>".form_checkbox("rights[".$data['server_id']."]", '', '')."</td>\n";
                echo "<td class='text-center'>".($ii+$rowstart)."</td>\n";
                echo "<td>".$data['server_name']."\n</td>\n";
                echo "<td class='text-center'>".$data['server_port']."\n</td>\n";
                echo "<td class='text-center'>".$data['server_player']."\n</td>\n";
                echo "<td class='text-center'>".self::$locale['counter_05'.$data['server_cod']]."\n</td>\n";
				echo "<td class='text-center'>".self::$locale['counter_06'.$data['server_modul']]."\n</td>\n";
                echo "<td class='text-center'>".self::$locale['counter_07'.$data['server_type']]."\n</td>\n";
				$up = $data['server_order'] - 1;
                $down = $data['server_order'] + 1;
				$upLink = FUSION_SELF.$aidlink."&amp;s_action=mu&amp;order=$up&amp;server_id=".$data['server_id'];
                $downLink = FUSION_SELF.$aidlink."&amp;s_action=md&amp;order=$down&amp;server_id=".$data['server_id'];
				echo "<td class='text-center'>\n".$data['server_order']."\n";
				echo ($ii == 1) ? '' : "<a title='".self::$locale['counter_029']."' href='".$upLink."'>".$ui_label['move_up']."</a>\n";
                echo ($ii == $rows) ? '' : "<a title='".self::$locale['counter_029']."' href='".$downLink."'>".$ui_label['move_down']."</a>\n</td>\n";
                echo "<td class='col-sm-5'>\n";
				echo "<a class='btn btn-default' href='".FORM_REQUEST."&amp;section=server_form&amp;s_action=edit&amp;server_id=".$data['server_id']."'>".self::$locale['edit']."</a>\n";
                echo "<a class='btn btn-danger' href='".FORM_REQUEST."&amp;section=server_form&amp;s_action=delete&amp;server_id=".$data['server_id']."' onclick=\"return confirm('".self::$locale['counter_014']."');\">".self::$locale['delete']."</a>\n</td>\n";
				echo "</tr>\n";
				
            }
			    echo "<tr>\n<td colspan='9' class='text-left'>\n";
			    echo form_button('cs_admins', self::$locale['counter_025'], self::$locale['counter_025'], array('class' => 'btn-danger', 'ico' => 'fa fa-trash'));
                echo "</td>\n</tr>\n</tbody>\n";            
                echo closeform();
                echo ($total_rows > $rows) ? makepagenav($rowstart, self::$limit2, $total_rows, self::$limit2, clean_request("", array("aid", "section"), TRUE)."&amp;") : "";
        } else {
                echo "<tr>\n";
                echo "<td colspan='8' class='text-center'>\n<div class='well'>\n".self::$locale['counter_008']."</div>\n</td>\n";
                echo "</tr>\n";
			
        }
		        echo "</table>\n";
                echo "</div>\n";
    }
	
    private function set_db() {
		
        if (isset($_POST['save_server'])) { 
            $this->data = array(
                'server_id'     => form_sanitizer($_POST['server_id'], 0, 'server_id'),
				'server_name'   => form_sanitizer($_POST['server_name'], '', 'server_name'),
				'server_port'   => isset($_POST['server_port']) ? form_sanitizer($_POST['server_port'], '', 'server_port') : 27015,
				'server_player' => isset($_POST['server_player']) ? form_sanitizer($_POST['server_player'], '', 'server_player') : 0,
				'server_cod'    => isset($_POST['server_cod']) ? form_sanitizer($_POST['server_cod'], '', 'server_cod') : 0,
				'server_modul'  => isset($_POST['server_modul']) ? form_sanitizer($_POST['server_modul'], '', 'server_modul') : 0,
				'server_type'   => isset($_POST['server_type']) ? form_sanitizer($_POST['server_type'], '', 'server_type') : 0,                
				'server_order'  => isset($_POST['server_order']) ? form_sanitizer($_POST['server_order']) : '',
            );
            
			if (!$this->data['server_order']) {
                $this->data['server_order'] = dbresult(dbquery("SELECT MAX(server_order) FROM ".DB_SERVER." "),0) + 1;
            }
			
			if (self::verify_server($this->data['server_id'])) {
                
				dbquery_order(DB_SERVER, $this->data['server_order'], 'server_order', $this->data['server_id'], 'server_id', '',  '', '', '', 'update');
				
                if (\defender::safe()) {
                    dbquery_insert(DB_SERVER, $this->data, 'update');
					
					addNotice('success', self::$locale['counter_003']);
                    redirect(clean_request("section=server", array("", "aid"), TRUE));
                }

            } else {

                dbquery_order(DB_SERVER, $this->data['server_order'], 'server_order', $this->data['server_id'], 'link_id', '', '', '', '', 'save');
				
				if (\defender::safe()) {
                    dbquery_insert(DB_SERVER, $this->data, 'save');
					addNotice('success', self::$locale['counter_002']);
                    redirect(clean_request("section=server", array("", "aid"), TRUE));
                }
            }
            
            defined('ADMIN_PANEL') ? redirect(clean_request("section=server", array("", "aid"), TRUE)) : redirect($this->postLink);
        }

        if (isset($_POST['counter_settings'])) {
            $inputArray = array(
                'servers_in_panel'   => form_sanitizer($_POST['servers_in_panel'], 0, 'servers_in_panel'),
				'servers_per_page'   => form_sanitizer($_POST['servers_per_page'], 0, 'servers_per_page'),
                'show_players'       => form_sanitizer($_POST['show_players'], 0, 'show_players'),
            );

            if (\defender::safe()) {
                foreach ($inputArray as $settings_name => $settings_value) {
                    $inputSettings = array(
                        "settings_name" => $settings_name, "settings_value" => $settings_value, "settings_inf" => "counter_strike_panel",
                    );
                    dbquery_insert(DB_SETTINGS_INF, $inputSettings, "update", array("primary_key" => "settings_name"));
                }
                addNotice("success", self::$locale['counter_024']);
                redirect(clean_request("section=server_settings", array("", "aid"), TRUE));
            }
        }
    }
	
    public function get_server_list() {
        self::$default_params = array(
            'csform_name' => 'cspage',
            'cs_db'       => '?rowstart',
            'cs_limit'    => self::$plimit,
        );
    
        self::server_list(self::$default_params);
    }	
	
    public function get_server() {
        self::$default_params = array(
            'csform_name' => 'cspanel',
            'cs_db'       => '?rowstart',
            'cs_limit'    => self::$limit,
        );
    
        self::server_list(self::$default_params);
    }
	
    public function server_listing() {
        self::$default_params = array(
            'csform_name' => 'csadmin',
            'cs_db'       => '?rowstart',
            'cs_limit'    => self::$plimit,
        );
    
        self::cs_listing(self::$default_params);
    }
	
	public function Server_AdminForm() {
        self::$default_params = array(
            'csform_name' => 'cs_admin',
            'cs_db'       => '?rowstart',
            'cs_limit'    => self::$plimit,
        );

        self::server_form(self::$default_params);
    }
	
	public static function server_list($info) {
        global $aidlink;
		$locale = fusion_get_locale("", INFUSIONS."counter_strike_panel/locale/".LANGUAGE.".php");
		$total_rows = self::_countCS("");
		//$limit = ($info['csform_name'] == 'cspanel') ? self::$limit : self::$limit2;
        $rowstart = isset($_GET['rowstart']) && ($_GET['rowstart'] <= $total_rows) ? $_GET['rowstart'] : 0;
        $result = self::_selectDB($rowstart, $info['cs_limit']);
        $rows = dbrows($result);
		
        if ($rows > 0) {
        
            		echo "<div>\n";
                    echo "<table class='table'>\n";
                    echo "<tr>\n";
                    echo "<th>".$locale['counter_131']."</th>\n";
                    echo "<th>".$locale['counter_132']."</th>\n";
	    			echo "<th>".$locale['counter_133']."</th>\n";
                    echo "<th>".$locale['counter_134']."</th>\n";
                    echo "<th>".$locale['counter_135']."</th>\n";
                    echo "<th>".$locale['counter_136']."</th>\n";
                    echo "<th>".$locale['counter_137']."</th>\n";
		            echo "<th>".$locale['counter_138']."</th>\n";
		        if (iADMIN && checkrights("CS")) {
				    echo "<th>".$locale['counter_021']."</th>\n";
			    }
				    echo "</tr>\n";
                    $i = 0;
    		while ($cdata = dbarray($result)) {
      			    $i++;
				    echo "<tr>\n";
				    echo "<td>".($i+$rowstart)."</td>\n";
				    echo "<td><a href='#' onclick=window.open('".INFUSIONS."counter_strike_panel/stats.php?id=".$cdata['server_id']."','','scrollbars=yes,width=600,height=600')>\n";
                    echo "<img src='".INFUSIONS."counter_strike_panel/img/verifica.gif' alt=''/></a></td>\n";
                    echo "<td>".$cdata['server_name']."\n</td>\n";
                    echo "<td>".$cdata['server_port']."\n</td>\n";
                    echo "<td>".$cdata['server_player']."\n</td>\n";
                    echo "<td>".self::$locale['counter_05'.$cdata['server_cod']]."\n</td>\n";
                    echo "<td>".self::$locale['counter_06'.$cdata['server_modul']]."\n</td>\n";
                    echo "<td>".self::$locale['counter_07'.$cdata['server_type']]."\n</td>\n";
			    if (iADMIN && checkrights("CS")) {
                    echo "<td>\n";
				    echo "<a class='btn btn-default' href='".INFUSIONS."counter_strike_panel/counter_strike_admin.php".$aidlink."&amp;section=server_form&amp;s_action=edit&amp;server_id=".$cdata['server_id']."'>";
                    echo "<i class='fa fa-edit fa-fw'></i> ".self::$locale['edit']."</a>\n";
                    echo "<a class='btn btn-danger' href='".INFUSIONS."counter_strike_panel/counter_strike_admin.php".$aidlink."&amp;section=server_form&amp;s_action=delete&amp;server_id=".$cdata['server_id']."' onclick=\"return confirm('".self::$locale['counter_014']."');\">";
                    echo "<i class='fa fa-trash fa-fw'></i> ".self::$locale['delete']."</a>\n";
			     	echo "</td>\n";
			    }
				    echo "</tr>\n";
				
            }
			        echo ($total_rows > $rows) ? makepagenav($rowstart, $limit, $total_rows, $limit, clean_request("", array("aid", "section"), TRUE)."?") : "";
                    echo "</tbody>\n";
        } else {
                
				    echo "<tr>\n";
                    echo "<td colspan='7' class='text-center'>\n<div class='well text-center'>\n".$locale['counter_008']."</div>\n</td>\n";
                    echo "</tr>\n";
        }
                    echo "</table>\n";
                    echo "</div>\n";
    }
	
	private function move_down() {
        global $aidlink;
        if (isset($_GET['server_id']) && isnum($_GET['server_id']) && isset($_GET['order']) && isnum($_GET['order'])) {
            
            $data = dbarray(dbquery("SELECT server_id FROM ".DB_SERVER." WHERE server_order='".$_GET['order']."'"));
            $result = dbquery("UPDATE ".DB_SERVER." SET server_order=server_order-1 WHERE server_id='".$data['server_id']."'");
            if ($result) {
                $result = dbquery("UPDATE ".DB_SERVER." SET server_order=server_order+1 WHERE server_id='".$_GET['server_id']."'");
            }
            if ($result) {
                addNotice('success', self::$locale['counter_028']);
                redirect(FUSION_SELF.$aidlink);
            }
        }
    }
	
	private function move_up() {
        global $aidlink;

        if (isset($_GET['server_id']) && isnum($_GET['server_id']) && isset($_GET['order']) && isnum($_GET['order'])) {

            $data = dbarray(dbquery("SELECT server_id FROM ".DB_SERVER." WHERE server_order='".intval($_GET['order'])."'"));
            $result = dbquery("UPDATE ".DB_SERVER." SET server_order=server_order+1 WHERE server_id='".intval($data['server_id'])."'");
            if ($result) {
			$result = dbquery("UPDATE ".DB_SERVER." SET server_order=server_order-1 WHERE server_id='".intval($_GET['server_id'])."'");
			}
			if ($result) {
            addNotice('success', self::$locale['counter_027']);
            redirect(FUSION_SELF.$aidlink);
			}
        }
    }
}	
 ?>