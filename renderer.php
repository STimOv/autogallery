<?php
/**
 * DokuWiki Plugin autogallery (Renderer Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Tim Siebentaler <tsvamp333@googlemail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

require_once DOKU_INC.'inc/parser/renderer.php';

class renderer_plugin_autogallery extends Doku_Renderer {

    /**
     * The format this renderer produces
     */
    public function getFormat(){
        return 'autogallery';
    }

    // FIXME implement all methods of Doku_Renderer here
}

