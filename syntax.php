<?php
/**
 * DokuWiki Plugin safehtmlsnippets (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  nefercheprure <nefercheprure <hidden@>>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once DOKU_PLUGIN.'syntax.php';

class syntax_plugin_safehtmlsnippets extends DokuWiki_Syntax_Plugin {
    public function getType() {
        return 'substition';
    }

    public function getPType() {
        return 'normal';
    }

    public function getSort() {
        return 12452;
    }


    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~safesnippet:(?:[a-zA-Z0-9._-]{1,10})~~',$mode,'plugin_safehtmlsnippets');
//        $this->Lexer->addEntryPattern('<FIXME>',$mode,'plugin_safehtmlsnippets');
    }

//    public function postConnect() {
//        $this->Lexer->addExitPattern('</FIXME>','plugin_safehtmlsnippets');
//    }

    public function handle($match, $state, $pos, &$handler){
        $data = array('');
        $page = substr($match,14,-2);
 
        // load the helper plugin
        $sqlite = plugin_load('helper', 'sqlite');
        if(!$sqlite){
            msg('This plugin requires the sqlite plugin. Please install it');
            return $data;
        }
        // initialize the database connection
        if(!$sqlite->init('safehtmlsnippets',DOKU_PLUGIN.'safehtmlsnippets/db/')){
            return $data;
        }
        // use the plugin
        $res = $sqlite->query("SELECT key,val FROM safesnippets WHERE key = ?;",$page);
        $arr = $sqlite->res2row($res);
        $data = array($arr['val']);
        return $data;
    }

    public function render($mode, &$renderer, $data) {
        if($mode == 'xhtml') {
            $renderer->doc .= $data[0];
            return true;
        }

        return false;
    }
}

// vim:ts=4:sw=4:et:
