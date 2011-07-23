<?php
/**
 * Highlights syntax plugin
 *
 * @author     Braden Pellett <bpellett@ucdavis.edu>
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_highlights_highlights extends DokuWiki_Syntax_Plugin {

    var $_id = 0;

    function getType() { return 'baseonly'; }
    function getAllowedTypes() { return array('formatting', 'substition', 'disabled'); }
    function getPType() { return 'block'; }

    function getSort() { return 0; }
 
    function connectTo($mode) {
        $pattern = '\n {2,}\+[ \t]*\[[^]]*\][ \t]*\{[^}]*\}';
        $this->Lexer->addEntryPattern('[ \t]*'.($pattern),$mode,'plugin_highlights_highlights');
        $this->Lexer->addPattern($pattern,'plugin_highlights_highlights');
    }

    function postConnect() {
        $this->Lexer->addExitPattern('\n','plugin_highlights_highlights');
    }
  
    function handle($match, $state, $pos, &$handler) {
        $text='';
        $pattern = '/[^[]*\[([^]]*)\][ \t]*\{([^}]*)\}/';
        switch ($state) {
            case DOKU_LEXER_ENTER: // a pattern set by addEntryPattern()
                ++$this->_id;
                preg_match($pattern, $match, $matches);
                return array($state,$this->_id,$this->_handle_link($matches[1]),$matches[2]);
            case DOKU_LEXER_MATCHED: // a pattern set by addPattern()
                preg_match($pattern, $match, $matches);
                return array($state,$this->_handle_link($matches[1]),$matches[2]);
            case DOKU_LEXER_SPECIAL: // a pattern set by addSpecialPattern()
                return array($state);
            case DOKU_LEXER_UNMATCHED: // ordinary text encountered within the plugin's syntax mode which doesn't match any pattern
                return array($state,$match);
            case DOKU_LEXER_EXIT: // a pattern set by addExitPattern()
                return array($state,$this->_id);
        }
        return array($state,'');
    }
 
    function render($mode, &$renderer, $data) {
        if($mode == 'xhtml'){
            switch ($data[0]) {
                case DOKU_LEXER_ENTER:
                    list(,$id,$url,$image) = $data;
                    $renderer->doc .= '<div class="kc-highlights">';
//                    $renderer->doc .= '<div class="kc-highlights-left">L</div>';
//                    $renderer->doc .= '<div class="kc-highlights-right">R</div>';
                    $renderer->doc .= '<div class="kc-highlights-view" id="kc-highlights-view'.$id.'">';
                    $renderer->doc .= '<p class="slide">';
                    $renderer->doc .= '<a href="'.$url.'"><img src="'.ml($image).'"></img></a>';
                    $renderer->doc .= '<a href="'.$url.'">';
                    break;
                case DOKU_LEXER_MATCHED:
                    list(,$url,$image) = $data;
                    $renderer->doc .= '</a></p>';
                    $renderer->doc .= '<p class="slide">';
                    $renderer->doc .= '<a href="'.$url.'"><img src="'.ml($image).'"></img></a>';
                    $renderer->doc .= '<a href="'.$url.'">';
                    break;
                case DOKU_LEXER_SPECIAL:
                    break;
                case DOKU_LEXER_UNMATCHED:
                    list(,$text) = $data;
                    $renderer->doc .= $text;
                    break;
                case DOKU_LEXER_EXIT:
                    list(,$id) = $data;
                    $renderer->doc .= '</a></p>';
                    $renderer->doc .= '</div></div>';
                    $renderer->doc .= '<script type="text/javascript" charset="utf-8" ><!--//--><![CDATA[//><!--'.PHP_EOL;
                    $renderer->doc .= 'jQuery(document).ready(function($){$("#kc-highlights-view'.$id.'")';
                    $renderer->doc .= '.after("<div class=\"kc-highlights-nav\" id=\"kc-highlights-nav'.$id.'\"></div>")';
                    $renderer->doc .= '.cycle({';
                    $renderer->doc .= 'fx: "'.$this->getConf('fx').'",';
                    $renderer->doc .= 'pause: '.$this->getConf('pause').',';
                    $renderer->doc .= 'pauseOnPagerHover: '.$this->getConf('pause').',';
                    $renderer->doc .= 'timeout: '.$this->getConf('timeout').',';
                    $renderer->doc .= 'speed: '.$this->getConf('speed').',';
                    $renderer->doc .= 'containerResize: 0,';
                    $renderer->doc .= 'slideExpr: "p.slide",';
                    $renderer->doc .= 'pager: "#kc-highlights-nav'.$id.'",';
                    $renderer->doc .= '});});'.PHP_EOL.'//--><!]]></script>';
                    break;
            }
            return true;
        }
        return false;
    }

    function _handle_link($link) {
        if(substr($link,0,7)=='http://') {
          $url = $link;
        } else {
          $url = wl($link);
        }
        return $url;
    }

}
