<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) die();
if(!defined('ABBR_CONF')) define('ABBR_CONF',DOKU_INC.'/conf/acronyms.local.conf');

require_once(DOKU_PLUGIN.'admin.php');
 
/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_acronymedit extends DokuWiki_Admin_Plugin {

    /**
     * Constructor
     */
    function admin_plugin_acronymedit() {
        $this->setupLocale();
    }

    /**
     * return some info
     */
    function getInfo() {
        $parts = explode('_',get_class($this));
        $file  = DOKU_PLUGIN.'/'.$parts[2].'/plugin.info.txt';
        $info  = array();
        if(!@file_exists($file)) {
            trigger_error('getInfo() not implemented in '.get_class($this).' and '.$info.' not found', E_USER_WARNING);
        }
        else {
            $info= confToHash($file);
            $lang= explode(',', $info['lang']);
            if (in_array('desc', $lang) && ''!==$this->getLang('plugininfo_desc')) $info['desc']= $this->getLang('plugininfo_desc');
        }
        return $info;
    }
 
    /**
     * return prompt for admin menu
     */
    function getMenuText($language) {
      if (!$this->disabled)
        return parent::getMenuText($language);
      return '';
    }
                                                    
    /**
     * return sort order for position in admin menu
     */
    function getMenuSort() {
        return 4129;
    }
 
    /**
     * handle user request
     */
    function handle() {
    }
 
    /**
     * output appropriate html
     */
    function html() {
      global $lang;
      global $conf;
                                    
      print "<h1>".$this->getLang('abbr_title')."</h1>";
      print $this->getLang('abbr_text');
      print "<br /><br />";

      if (isset($_POST['saver']) && $_POST['saver']==1) {
        $ok=true;
        $ok = $ok & $this->writeFile(ABBR_CONF, $_POST['abbr']);
        
        if ($ok) {
          print "<form name=\"message_form\" method=\"POST\"><input type=\"hidden\" name=\"saver\" value=\"0\" /></form>";
          print "<script>document.forms['message_form'].submit();</script>";
        }
        else {
          msg("<b>".$this->getLang('abbr_error')."</b>",-1);
          print "<br />";
        }
      }

      print "<form method=\"POST\">";
      print "<input type=\"hidden\" name=\"saver\" value=\"1\" />";

      print "<h3>".$this->getLang('abbr_list')."</h3>";
      print "<textarea name=\"abbr\" style=\"width:100%; height:20em;\">";
      print @file_get_contents(ABBR_CONF);
      print "</textarea>";
      print "<br /><br />";

      print "<input type=\"submit\" value=\"".$this->getLang('abbr_save')."\" title=\"".$this->getLang('abbr_save')." [S]\" accesskey=\"s\" />";
      print "</form>";

    }

    function writeFile($filename,$chaine) {
      //if (is_writable($filename)) {
      if (is_writable( $filename ) || (is_writable(dirname( $filename ).'/.') && !file_exists( $filename ))) {
        if (!$handle = fopen($filename, 'w')) {
          msg($this->getLang('abbr_err_open')." ($filename).",-1);
          return false;
        }

        if (fwrite($handle, $chaine) === FALSE) {
          msg($this->getLang('abbr_err_write')." ($filename).",-1);
          return false;
        }

        fclose($handle);
        return true;

    } else {
      msg($this->getLang('abbr_err_secure')." ($filename).",-1);
      return false;
    }
  }
}                                                                                                                                                                  
// vim:ts=4:sw=4:et:enc=utf-8:
