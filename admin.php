<?php
/**
 * DokuWiki Plugin safehtmlsnippets (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  nefercheprure <nefercheprure <hidden@>>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'admin.php';

class admin_plugin_safehtmlsnippets extends DokuWiki_Admin_Plugin {

    public function getMenuSort() { return 12452; }
    public function forAdminOnly() { return true; }

    public function handle() {
        //FIXME
    }

    public function html() {
        ptln('<h1>' . $this->getLang('menu') . '</h1>');
        //ptln('FIXME here should be form for editing');
        //echo $this->locale_xhtml('intro');

        $form = new Doku_Form(array('class'=>'safehtmlsnippets'));
        $form->startFieldset('HTML Snippets');
        $form->addHidden('id',$ID);
        $form->addHidden('do','admin');
        $form->addHidden('page','safehtmlsnippets');
        $form->addHidden('sectok', getSecurityToken());
        $form->addElement('<label for="key">'.hsc($this->getLang('key')).' </label><input name="key" type="text" value="'.hsc($_REQUEST['key']).'" /><br/>');
        $form->addElement('<label for="snippet">'.hsc($this->getLang('snippet')).'</label><br/><textarea name="snippet" class="edit">'.hsc($_REQUEST['snippet']).'</textarea>');
        $form->addElement('<button name="button" type="submit" class="button" value="add">'.hsc($this->getLang('button_add')).'</button>');
        $form->addElement('<button name="button" type="submit" class="button" value="remove">'.hsc($this->getLang('button_remove')).'</button>');
        $form->endFieldset();
        $form->printForm();

        /*
        ptln('id<br/>'.hsc($_REQUEST['id']).'<br/>');
        ptln('do<br/>'.hsc($_REQUEST['do']).'<br/>');
        ptln('page<br/>'.hsc($_REQUEST['page']).'<br/>');
        ptln('key<br/>'.hsc($_REQUEST['key']).'<br/>');
        ptln('snippet<br/>'.hsc($_REQUEST['snippet']).'<br/>');
        ptln('button<br/>'.hsc($_REQUEST['button']).'<br/>');
        //*/

        $sqlite = plugin_load('helper', 'sqlite');
        if(!$sqlite){
            msg('This plugin requires the sqlite plugin. Please install it');
            return $data;
        }
        // initialize the database connection
        if(!$sqlite->init('safehtmlsnippets',DOKU_PLUGIN.'safehtmlsnippets/db/')){
            return $data;
        }



        if($_REQUEST['button'] && checkSecurityToken()){
            $key = $_REQUEST['key'];
            if (!preg_match('/^[a-zA-Z0-9_.-]{1,10}$/i',$key)) {
                msg(sprintf($this->getLang('invalidkey'),hsc($key)));
                return;
            }

            if ($_REQUEST['button'] == 'add') {
                $res = $sqlite->query("DELETE FROM safesnippets WHERE key = ?;",$key);
                $res = $sqlite->query("INSERT INTO safesnippets (key,val) values (?,?);",$key,$_REQUEST['snippet']);
            } else if ($_REQUEST['button'] == 'remove') {
                $res = $sqlite->query("DELETE FROM safesnippets WHERE key = ?;",$key);
            }
        }

        $res = $sqlite->query("SELECT key,val FROM safesnippets;");
        //if ($res === false) continue;

        //msg(sqlite_num_rows($res).' affected rows',1);
        $result = $sqlite->res2arr($res);
        if(count($result)) {
            echo '<p>';
            $ths = array_keys($result[0]);
            echo '<table class="inline">';
            echo '<tr>';
            foreach($ths as $th){
                echo '<th>'.hsc($th).'</th>';
            }
            echo '</tr>';
            foreach($result as $row){
                echo '<tr>';
                $tds = array_values($row);
                foreach($tds as $td){
                    echo '<td>'.hsc($td).'</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
            echo '</p>';
        }
        
    }
}

// vim:ts=4:sw=4:et:
