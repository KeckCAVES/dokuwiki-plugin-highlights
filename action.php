<?php
/**
 * highlights plugin
 *
 * @author     Braden Pellett <bpellett@ucdavis.edu>
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

if(plugin_isdisabled('jquery')) msg('Highlights plugin requires jQuery plugin.', -1);

class action_plugin_highlights extends DokuWiki_Action_Plugin {

    /*
     * Register handlers with the DokuWiki's event controller
     */
    function register(&$controller) {
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, '_add_cycle');
    }

    function _add_cycle(&$event, $param) {
        array_unshift($event->data['script'],
            // Load the Cycle jQuery plugin
            array(
                'type' => 'text/javascript',
                'src' => $this->getConf('src'),
                '_data' => ''
            )
        );
    }

}
