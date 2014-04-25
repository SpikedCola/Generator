<?php
/**
 * Test class for Generator
 * 
 * @author Jordan Skoblenick <parkinglotlust@gmail.com> 2013-08-22
 */

require_once(__DIR__.'/../classes/Generator.class.php');

class GeneratorTest extends PHPUnit_Framework_TestCase {
	public $outputDirectory = '/source/public_html/';
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	function testShowsUsageTextWhenNoFilenameProvided() {
		$generator = new Generator(null); 
		$generator->run();
		
		// expect some kind of output containing the word 'usage'
		$this->expectOutputRegex('/usage/i');
	}
	
	function testHasListOfSupportedTypes() {
		$generator = new Generator('filename');
		
		$this->assertObjectHasAttribute('supportedTypes', $generator);		
	}
	
	function testFilenameIsPassedInAndSetCorrectly() {
		$generator = new Generator('filename');
		
		$this->assertTrue($generator->filename == 'filename');	
	}
	
	/**
	 * @dataProvider typeProvider
	 */
	function testSupportForVariousTypes($type, $arg) {
		$filename = 'index';
		
		$generator = new Generator($filename, [$arg]); 
		$generator->outputDirectory = $this->outputDirectory;
		$generator->run();
		
		$path = $this->outputDirectory.$generator->supportedTypes[$type].$filename.'.'.$type;
		
		$this->assertFileExists($path); 
		
		// clean up files created for the test
		unlink($path);
	}
	
	function typeProvider() {
		// currently supports testing 4 options
		return array(
		    array('js', '-j'),
		    array('css', '-c'),
		    array('php', '-p'),
		    array('tpl', '-t'),
		);
	}
	
	function testGeneratesCorrectListOfEnabledTypes() {
		// enable 2 options & ensure only those 2 are set in array
		$generator = new Generator('filename', ['-j', '-c']);
		$generator->outputDirectory = $this->outputDirectory;
		$generator->parseArguments();
		
		$this->assertObjectHasAttribute('typesToGenerate', $generator);
	
		$this->assertContains('js', $generator->typesToGenerate);
		$this->assertContains('css', $generator->typesToGenerate);
	}
	
	function testSingleArgumentProducesAllFilesByDefault() {
		$filename = 'index';
		
		$generator = new Generator($filename); // one argument only
		$generator->outputDirectory = $this->outputDirectory;
		$generator->run();
		
		// ensure we set the typesToGenerate array equal to supportedTypes (all types) by default
		$diff = array_diff(array_keys($generator->supportedTypes), $generator->typesToGenerate);
		$this->assertTrue(empty($diff));
		
		// we expect all file types (php/js/css/tpl) to be created by default
		foreach ($generator->supportedTypes as $type => $path) {		
			$this->assertFileExists($this->outputDirectory.$path.$filename.'.'.$type); 

			// clean up files created for the test
			unlink($this->outputDirectory.$path.$filename.'.'.$type);
		}
	}
}