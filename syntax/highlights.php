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
    $pattern = '/[^[]*\[([^]]*)\][ \t]*\{([^}]*)\}/';
    $doc = '';
    switch ($state) {
      case DOKU_LEXER_ENTER:
        ++$this->_id;
        preg_match($pattern, $match, $matches);
        $url = $this->_handle_link($matches[1]);
        $image = $matches[2];
        $doc .= '<div class="kc-highlights">';
        //$doc .= '<div class="kc-highlights-left">L</div>';
        //$doc .= '<div class="kc-highlights-right">R</div>';
        $doc .= '<div class="kc-highlights-view" id="kc-highlights-view'.$this->_id.'">';
        $doc .= $this->_start_slide($url, $image);
        break;
      case DOKU_LEXER_MATCHED:
        preg_match($pattern, $match, $matches);
        $url = $this->_handle_link($matches[1]);
        $image = $matches[2];
        $doc .= $this->_end_slide();
        $doc .= $this->_start_slide($url, $image);
        break;
      case DOKU_LEXER_SPECIAL:
        break;
      case DOKU_LEXER_UNMATCHED:
        $doc .= $match;
        break;
      case DOKU_LEXER_EXIT:
        $doc .= $this->_end_slide();
        $doc .= '</div></div>';
        $doc .= '<script type="text/javascript" charset="utf-8" ><!--//--><![CDATA[//><!--'.PHP_EOL;
        $doc .= 'jQuery(document).ready(function($){$("#kc-highlights-view'.$this->_id.'")';
        $doc .= '.after("<div class=\"kc-highlights-nav\" id=\"kc-highlights-nav'.$this->_id.'\"></div>")';
        $doc .= '.cycle({';
        $doc .= 'fx: "'.$this->getConf('fx').'",';
        $doc .= 'pause: '.$this->getConf('pause').',';
        $doc .= 'pauseOnPagerHover: '.$this->getConf('pause').',';
        $doc .= 'timeout: '.$this->getConf('timeout').',';
        $doc .= 'speed: '.$this->getConf('speed').',';
        $doc .= 'containerResize: 0,';
        $doc .= 'slideExpr: "p.slide",';
        $doc .= 'pager: "#kc-highlights-nav'.$this->_id.'",';
        $doc .= '});});'.PHP_EOL.'//--><!]]></script>';
        break;
    }
    return $doc;
  }
 
  function render($mode, &$renderer, &$data) {
    if($mode == 'xhtml' && $data){
      $renderer->doc .= $data;
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

  function _start_slide($url, $image) {
    $doc = '';
    $doc .= '<p class="slide">';
    $doc .= '<a href="'.$url.'"><img src="'.ml($image).'"\></a>';
    $doc .= '<a href="'.$url.'">';
    return $doc;
  }

  function _end_slide() {
    return '</a></p>';
  }
}
