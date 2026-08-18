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
$cls = "model_award" . date("Y");
$awards = $cls::get_all_entries();
try{
foreach ($awards as $award) {
	echo $award->filename . "\n";
    
    file_put_contents($award->filename, stripslashes($award->file));

    echo "sclaing...\n";
    $png = imagecreatefrompng($award->filename);
    $png = getImageResized($png, 150, 150);
    ob_start();
    imagepng($png);
    $image_data = ob_get_contents();
    ob_end_clean();
    echo "blobbing...\n";
    $blob = addslashes($image_data);
    $award->file = $blob;
    $award->save();
    echo "saving award.\n";
}
}catch(Exception $e){
    echo "EXception!\n";
    die(var_export($e,true));
}
/****************************/

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


function getImageResized($image, int $newWidth, int $newHeight) {
    $newImg = imagecreatetruecolor($newWidth, $newHeight);
    imagealphablending($newImg, false);
    imagesavealpha($newImg, true);
    $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
    imagefilledrectangle($newImg, 0, 0, $newWidth, $newHeight, $transparent);
    $src_w = imagesx($image);
    $src_h = imagesy($image);
    imagecopyresampled($newImg, $image, 0, 0, 0, 0, $newWidth, $newHeight, $src_w, $src_h);
    return $newImg;
}