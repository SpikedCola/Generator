<?php

require_once(__DIR__.'/classes/Generator.class.php');

if (php_sapi_name() != 'cli') {
	die("\nThis script must be run from the command line\n");
}

if (!$argv || count($argv) == 1) {
	// no arguments passed (just script name from php), show usage
	die(Generator::GetUsageText());
}

// parse out filename and remove current script name. 
// we will grab the first string we find which doesnt 
// start with a hyphen and use that for filename
$filename = null;
foreach ($argv as $key => $arg) {
	if ($arg == $_SERVER['SCRIPT_NAME']) {
		// skip the arg containing our script's name
		unset($argv[$key]);
		continue;
	}
	if (!preg_match('/^-/', $arg)) {
		$filename = $arg; 
		unset($argv[$key]);
	}
}
if (!$filename) {
	die(Generator::GetUsageText());
}

// instantiate with any leftover arguments
$generator = new Generator($filename, $argv);
//$generator->outputDirectory = __DIR__.'/source/public_html/';
$generator->outputDirectory = 'c:/wamp/www/presale.codes/source/public_html/';
$generator->run();