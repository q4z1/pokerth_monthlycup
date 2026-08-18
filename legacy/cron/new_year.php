<?php

/**
 * ermitteln des Wurzelverzeichnisses,
 * Definition der Verzeichnis-Konstanten
 */
define(
	'ROOT_DIR',
	substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR) + 1)
);
define('APP_DIR', ROOT_DIR . "application" . DIRECTORY_SEPARATOR);
define('DATA_DIR', ROOT_DIR . "application" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR);
define('VIEW_DIR', APP_DIR . "view" . DIRECTORY_SEPARATOR);
define('LIB_DIR', ROOT_DIR . "lib" . DIRECTORY_SEPARATOR);
define('TMPL_DIR', ROOT_DIR . "template" . DIRECTORY_SEPARATOR);
define('VAR_DIR', ROOT_DIR . "var" . DIRECTORY_SEPARATOR);
define('INC_DIR', ROOT_DIR . "inc" . DIRECTORY_SEPARATOR);

/**
 * setzen der benötigten Include-Verzeichnisse
 * für den Autoloader
 */
set_include_path(
	get_include_path() . PATH_SEPARATOR . APP_DIR	. PATH_SEPARATOR . LIB_DIR
		. PATH_SEPARATOR . DATA_DIR . PATH_SEPARATOR . VIEW_DIR . PATH_SEPARATOR . TMPL_DIR
		. PATH_SEPARATOR . VAR_DIR . PATH_SEPARATOR . INC_DIR . PATH_SEPARATOR
);

spl_autoload_register('ava_autoloader');

/*
 * Stelle Konfigurations-Variablen bereit
 */
cfg::init();

/****************************/
/**** @XXX: Cron-Ablauf ****/
$year = date('Y'); // das Jahr, für das die Tabellen angelegt werden sollen
$db = database::get_instance();

$tables = [];

// award table
$tables['award'] = "
CREATE TABLE IF NOT EXISTS `award{$year}` (
  `award{$year}_id` int(8) NOT NULL AUTO_INCREMENT,
  `month` int(2) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `file` longblob DEFAULT NULL,
  `filename` varchar(64) DEFAULT NULL,
  `mime` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`award{$year}_id`),
  UNIQUE KEY `month_2` (`month`,`type`),
  KEY `month` (`month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
";

// player table
$tables['player'] = "
CREATE TABLE IF NOT EXISTS `player{$year}` (
  `player{$year}_id` int(11) NOT NULL AUTO_INCREMENT,
  `playername` varchar(64) DEFAULT NULL,
  `awards` text DEFAULT NULL,
  `avatar` longblob DEFAULT NULL,
  `avatar_mime` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`player{$year}_id`),
  KEY `playername` (`playername`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
";

// settings table
$tables['settings'] = "
CREATE TABLE IF NOT EXISTS `settings{$year}` (
  `settings{$year}_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(64) DEFAULT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`settings{$year}_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

// signup table
$tables['signup'] = "
CREATE TABLE IF NOT EXISTS `signup{$year}` (
  `signup{$year}_id` int(8) NOT NULL AUTO_INCREMENT,
  `month` int(2) DEFAULT NULL,
  `playername` varchar(64) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `fp` varchar(64) DEFAULT NULL,
  `fpnew` varchar(64) DEFAULT NULL,
  `valid` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`signup{$year}_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
";

// upload table
$tables['upload'] = "
CREATE TABLE IF NOT EXISTS `upload{$year}` (
  `uploads{$year}_id` int(8) NOT NULL AUTO_INCREMENT,
  `type` enum('firstround','final') NOT NULL,
  `table_` varchar(16) NOT NULL,
  `month` int(2) NOT NULL,
  `playername` varchar(32) NOT NULL,
  `position` int(2) NOT NULL,
  `points` int(2) NOT NULL,
  PRIMARY KEY (`uploads{$year}_id`),
  KEY `table_month` (`table_`,`month`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
";

foreach ($tables as $key => $sql) {
    try {
        $db->query($sql);
        echo "Tabelle {$key}{$year} erstellt/überprüft" . PHP_EOL;
    } catch (Exception $e) {
        echo "Fehler beim Erstellen der Tabelle {$key}{$year}: " . $e->getMessage() . PHP_EOL;
    }
}



/****************************/

// ab hier: Models für das neue Jahr anlegen (kopiere Vorjahres-Dateien und ersetze Jahreszahl)
$tplYear = $year - 1;
$modelDir = DATA_DIR . "model" . DIRECTORY_SEPARATOR;
$mixedDir = DATA_DIR . "mixed" . DIRECTORY_SEPARATOR;

// Modelle (application/data/model)
create_year_files_from_template($modelDir, $tplYear, $year);

// gemischte Dateien (application/data/mixed)
create_year_files_from_template($mixedDir, $tplYear, $year);


/****************************/

function create_year_files_from_template($dir, $tplYear, $newYear)
{
    if (!is_dir($dir)) {
        echo "Verzeichnis nicht gefunden: {$dir}" . PHP_EOL;
        return;
    }

    $pattern = $dir . "*" . $tplYear . ".php";
    $files = glob($pattern);
    if (!$files) {
        echo "Keine Template-Dateien für Jahr {$tplYear} in {$dir} gefunden." . PHP_EOL;
        return;
    }

    foreach ($files as $src) {
        $basename = basename($src);
        // ersetze nur die Jahreszahl im Dateinamen
        $dstName = str_replace((string)$tplYear, (string)$newYear, $basename);
        $dst = $dir . $dstName;

        if (file_exists($dst)) {
            echo "Datei existiert bereits: {$dstName}" . PHP_EOL;
            continue;
        }

        $content = file_get_contents($src);
        if ($content === false) {
            echo "Fehler beim Lesen: {$basename}" . PHP_EOL;
            continue;
        }

        // ersetze Vorkommen der Jahreszahl innerhalb der Datei (Klassen/Kommentare/Tabellenreferenzen)
        $newContent = str_replace((string)$tplYear, (string)$newYear, $content);

        if (file_put_contents($dst, $newContent) === false) {
            echo "Fehler beim Schreiben: {$dstName}" . PHP_EOL;
            continue;
        }

        // Standard-Dateirechte setzen
        @chmod($dst, 0644);
        echo "Erstellt: {$dstName}" . PHP_EOL;
    }
}


/**
 * __autoload($class_name)
 *
 * autmatisches Laden von Klassen
 *
 * Konzept:
 *
 * Verzeichnisse können durch Unterstriche getrennt vorangestellt werden -
 * nach dem letzten Unterstrich folgt der Dateiname der Klasse ohne ".php"
 *
 * Ein folgender require_once Aufruf sucht in allen Verzeichnissen
 * des Include-Paths
 */
function ava_autoloader($class)
{
    $class = str_replace("_", "/", $class) . ".php";
    /*
     * stelle fest, ob die Klassendatei existiert
     */
    if (
        (file_exists(APP_DIR . $class) === false) &&
        (file_exists(LIB_DIR . $class) === false) &&
        (file_exists(DATA_DIR . $class) === false) &&
        (file_exists(VIEW_DIR . $class) === false) &&
        (file_exists(TMPL_DIR . $class) === false)
    ) {
        $exception = new Exception("Klassen-Datei $class wurde nicht gefunden!");
        /*
         * lade exception controller
         */
        $e = new controller_exceptionhandler();
        $e->run($exception);
        exit();
    }
    /*
     * lade die Klassen-Datei
     */
    try {
        require_once($class);
    } catch (Exception $e) {
        throw new Exception(
                "Das Laden der Klassen-Datei $class schlug fehl! : " . $e->getMessage()
            );
    }
    return;
}
