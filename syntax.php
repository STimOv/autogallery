<?php
/**
 * DokuWiki Plugin autogallery (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Tim Siebentaler <tsvamp333@googlemail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_autogallery extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }
    /**
     * @return string Paragraph type 
     */
    public function getPType() {
        return 'block';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 200;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{NEWGALLERY[^\}]*\}\}',$mode,'plugin_autogallery');

    }

    /**
     * Handle matches of the autogallery syntax
     *
     * @param string          $match   The match of the syntax
     * @param int             $state   The state of the handler
     * @param int             $pos     The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler) {
		/**
		* Erkannten String parsen
		* Erste 12 Zeichen für aktivierung plugin, danach Namespace
		* Trennzeichen = '>'
		* wurde ein Template angegeben? egal nutzen wir ohnehin nicht
		*/
        $options = substr($match, 12, -2); //
        $options = explode('#', $options, 2);

        $namespace = trim(ltrim($options[0], '>'));
        $templates = explode(',', $options[1]);
        $templates = array_map('trim', $templates);
        $arr='';
        $arr=array('namespace' => $namespace,'newpagetemplates' => $templates);
        return $arr;
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        global $lang, $INFO;
        if ($mode == 'xhtml') {
            $disablecache = null;
            $namespaceinput ='<input type="text" id="np_cat" name="np_cat" value="'.$INFO['namespace'].':bilder:'.time().'" disabled>';            
            if ($disablecache) $renderer->info['cache'] = false;

            $newpagetemplateinput = $this->_htmlTemplateInput($data['newpagetemplates']);
            $tstamp=time();
            if ($data['namespace']) {
                $tstamp=$data['namespace'];
            }

            $arr=array('tstamp'=>$tstamp,'mydata'=>$data);
			/**
			* EVENT 'NON_SENSE' triggern, damit Action-Teil aktiv werden kann
			* dort prüfung der Verzeichnisstruktur + initiale Erstellung wenn nicht vorhanden
			* dort Erstellung von (Sub-)Namespaces, Index dieser Gallerie,, Aktualisierung Gallerie-Hauptindex(Übersicht der Gallerien)
			*/
			
            trigger_event('NON_SENSE',$arr);
            /**
			* Upload-Button und Hinweis erzeugen
			*/
            $form = '<div class="panel filelist">'. DOKU_LF
                    .'<h2 class="a11y">Dateiauswahl</h2>'. DOKU_LF
                    .'<ul class="tabs">'. DOKU_LF
                    .'<li><a href="/dokuwiki/doku.php/wiki;bilder;'.$tstamp.';index?tab_files=upload&amp;do=media&amp;ns=wiki%3Abilder%3A'.$tstamp.'%3A'.$tstamp.'">Hochladen</a></li>'. DOKU_LF

                    .'</ul>'. DOKU_LF
                    .'<h3>Bitte Dateien in <strong>wiki:bilder:'.$tstamp.':'.$tstamp.' hochladen!<br>(muss vor erstem Anzeigen der Gallerie geschehen)</strong></h3>'. DOKU_LF
                    .'</div>'. DOKU_LF
                    .'</div>'. DOKU_LF;
            $renderer->doc .= $form;
            $nul=array();




            return true;
        }
        return false;
    }
    public function _htmlTemplateInput($newpagetemplates) {
		/**
		* NUR EINE DUMMY-FUNKTION
		* IN ZUKUNFT VIELLEICHT MEHR
		*/
        return '';
    }

}

// vim:ts=4:sw=4:et:
