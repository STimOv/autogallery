<?php
/**
 * DokuWiki Plugin autogallery (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Tim Siebentaler <tsvamp333@googlemail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class action_plugin_autogallery extends DokuWiki_Action_Plugin {

    /* Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void 
     */
    public function register(Doku_Event_Handler $controller) {

        $controller->register_hook('NON_SENSE', 'AFTER', $this, 'do_it');//event registrieren

    }
    /* [Custom event handler which performs action]
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */

    public function do_it(Doku_Event &$event, $param) {
        global $ID,$INFO;
        $tstamp='';
        foreach($event->data as $name=>$X) {
            if ($name=='tstamp') {
                $tstamp=$X;
            }
            if ($name=='mydata') {
                $mydata=$X;

            }
        }
        if ($mydata['namespace']) {
            $tstamp=$mydata['namespace'];
        }
		
		/**
		* Initilisierung beim ersten start
		*  Prüfe ob NS wiki existiert, wenn nicht erstellen
		*  Prüfe ob NS wiki:bilder existiert, wenn nicht erstellen
		*  Nutzer über Erstellung informieren
		*  Erstelle zentralen Index (dort werden die Gallerien künftig zentral verlinkt)
		*/
		if (!is_dir(dirname(wikiFN("wiki:dummy.txt")))) {
			$inidir= dirname(wikiFN("wiki:dummy.txt"));
			$inidir=str_replace("/","\\", $inidir);
			io_makeFileDir($inidir);
			msg("First time initialisation 1/3: created Namespache wiki");
		}
		if (!is_dir(dirname(wikiFN("wiki:bilder:dummy.txt")))) {
			$inidir=dirname(wikiFN("wiki:bilder:dummy.txt"));
			$inidir=str_replace("/","\\", $inidir);
			io_makeFileDir($inidir);
			msg("First time initialisation 2/3: created (sub-)namespache wiki:bilder");
		}
		if (!is_file(str_replace("/","\\",dirname(wikiFN("wiki:bilder:dummy"))."\\index.txt"))) {
			$inidir=dirname(wikiFN("wiki:bilder:dummy.txt"));
			$inidir=str_replace("/","\\", $inidir);
			io_saveFile($inidir."\\index.txt","");
			msg(str_replace("/","\\",dirname(wikiFN("wiki:bilder:dummy"))."\\index.txt"));
			msg("First time initialisation 3/3: Created base-index for your future galleries");
		}			
		
        /**
		* Gallerie-Index-Verzeichnis 1/2
		* ermittle künftiges Verzeichnis aus NS
		*/
        $imggal = dirname(wikiFN("wiki:bilder:" . $tstamp . ":123"));
        
		/**
		* Gallerie-Bilder-Verzeichnis 1/2
		* ermittle künftiges Verzeichnis aus NS
		*/
        $imgbase = dirname(wikiFN("wiki:bilder:" . $tstamp . ":" . $tstamp . ":123"));
		
		/**
		* Gallerie-Index-Verzeichnis 2/2
		* erstellen wenn Verzeichnis nicht existiert
		* Benutzer informieren, dass erstellt
		*/
        if (!is_dir($imggal)) {
            io_makeFileDir($imggal . '/dummy.txt');
            msg("made an own NS for gallery!", 1);
        }
		
		/**
		* Gallerie-Bilder-Verzeichnis 2/2
		* erstellen wenn Verzeichnis nicht existiert
		* Benutzer informieren, dass erstellt
		*/
        if (!is_dir($imgbase)) {
            io_makeFileDir($imgbase . '/dummy.txt');
            msg("made a Sub-NS for images of gallery!", 1);
        }
		
		/**
		* Index der zu erstellenden Gallerie
		* Wenn Datei noch nicht existiert erstellen sie
		* Schreibe Code für Plugin Gallery hinein um Gallerie zu erzeugen
		* Informiere den benutzer darüber
		* teile Benutzer mit, wohin Bilder hochgeleden werden müssen
		*/
		
        $imgpage = $imggal . '/index.txt';
        if (!is_file($imgpage)) {

            $page = "{{gallery>'" . "wiki:bilder:" . $tstamp . ":" . $tstamp  . "'}}";//Nutze Gallery Plugin
            $page = str_replace("\\", ":", $page);
            $page =str_replace("/", ":", $page);
            io_saveFile($imgpage, $page);
            msg("made gallery file. Please Upload your images to: " . "wiki:bilder:" . $tstamp . ":" . $tstamp);
			
			/**
			* Gallerie verlinken
			* Zentral-Index einlesen
			* String mit Link zur neuen Gallerie erstellen
			* alten Inhalt der Datei an neuen Link anhängen
			* speichere neuen Index ab
			* informiere Nutzer über Verlinkung
			*/
            $indexbase = dirname(wikiFN("wiki:bilder:index"));
			$indexbase=str_replace("/","\\", $indexbase);
            $page=io_readFile($indexbase."\\index.txt", $clean=false);
            $page_fin="[[wiki:bilder:".$tstamp.":index|".$tstamp."]]\n\n".$page;
            io_saveFile($indexbase."\\index.txt", $page_fin);
            msg("Link to new gallery created in wiki:bilder:index",1);
			
			/**
			* Aktuelle Datei leeren
			* schreibe leeren String in aktuelle Datei und lösche damit den Content
			* ist erforderlich, da sonst sinnlose Uploadpage zurück bleibt, editieren auf konventionellem Wege wäre nicht möglich
			* informiere Nutzer über Vorgang
			*/
            io_saveFile($INFO["filepath"],"");//leere aktuelle Datei
            msg("Deleted content of this actual file...otherwise it would not be editable in future!",-1);

        }
    }

}

// vim:ts=4:sw=4:et:
