<?php
/**
 * highlights plugin
 *
 * @author     Braden Pellett <bpellett@ucdavis.edu>
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_highlights extends DokuWiki_Action_Plugin {

    /*
     * Register handlers with the DokuWiki's event controller
     */
    function register(&$controller) {
        // Nothing to do
    }

}
