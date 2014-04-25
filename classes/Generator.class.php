<?php

/**
 * Generator class.
 * 
 * This class will create files within an output directory. It currently
 * supports creting JS, PHP, CSS and TPL files. 
 * 
 * By default (only filename specified) it will create all types. Fewer
 * types may be selected by specifying any number of arguments.
 * 
 * @author Jordan Skoblenick <parkinglotlust@gmail.com> 2013-08-22
 */

class Generator {
	
	protected $arguments = [];
	
	/**
	 * An array of (extension => path) types that can be created.
	 * 
	 * The base path will be appended onto each path fragment.
	 * 
	 * @var array 
	 */
	public $supportedTypes = [
	    'js' => 'js/',
	    'php' => '',
	    'css' => 'css/',
	    'tpl' => 'templates/'
	];
	
	/**
	 * An array of types that will be generated.
	 * Populated by parsing the arguments passed in.
	 *
	 * @var array 
	 */
	public $typesToGenerate = [];
	
	public $filename = null;
	
	public $outputDirectory = '';
	
	public function __construct($filename, array $arguments = []) {
		if (!$filename) {
			throw new InvalidArgumentException(Generator::GetUsageText());
		}
		$this->filename = $filename;
		if ($arguments) {
			$this->arguments = $arguments;
		}
	}
	
	
	public function run() {
		$this->parseArguments();
		
		if (!$this->typesToGenerate) {
			// if the args were parsed and no types found,
			// enable the creation of all by default
			$this->typesToGenerate = array_keys($this->supportedTypes);
		}
		
		foreach ($this->typesToGenerate as $type) {
			$this->generateFile($type);
		}

	}
	
	protected function generateFile($type) {
		$path = $this->outputDirectory.$this->supportedTypes[$type];
		$dirStr = '';
		$parts = explode('/', $path);
		foreach ($parts as $part) {
			$dirStr .= $part.'/';
			if (!is_dir($dirStr)) {
				mkdir($dirStr); // ensure dir exists
			}
			
		}
		$content = '';
		switch ($type) {
			case 'php':
				$content = <<<EOL
<?php

/**
 * 
 * @author Jordan Skoblenick <parkinglotlust@gmail.com> 2014-04-
 */

require_once(__DIR__.'/ui.php');

\$template->display('{$this->filename}');
EOL;
		}
		file_put_contents($path.$this->filename.'.'.$type, $content); // write some sample data
	}
	
	public function parseArguments() {
		foreach ($this->arguments as $arg) {
			switch (strtolower($arg)) {
				case '-j':
				case '--js':
					$this->typesToGenerate[] = 'js';
					break;
				case '-c':
				case '--css':
					$this->typesToGenerate[] = 'css';
					break;
				case '-p':
				case '--php':
					$this->typesToGenerate[] = 'php';
					break;
				case '-t':
				case '--tpl':
					$this->typesToGenerate[] = 'tpl';
					break;
			}
		}
	}
	
	public static function GetUsageText() {
		return <<<EOL
Generator Usage:
  <filename>    the name of the file(s) to generate (Required)
  -p | --php    create PHP file (Default: Enabled)
  -j | --js     create JS file (Default: Enabled)
  -c | --css    create CSS file (Default: Enabled)
  -t | --tpl    create TPL file (Default: Enabled)
EOL;
	}
}