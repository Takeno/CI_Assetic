<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

spl_autoload_register(function($className) {
	if(strpos($className, 'Assetic') === 0)
		require APPPATH .'third_party/' . str_replace('\\', '/', $className.'.php');
});

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\HttpAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\LessFilter;
use Assetic\Filter\Yui;
use Assetic\AssetWriter;
use Assetic\AssetManager;

class CI_Assetic {
	var $CI;
	var $config 	= array();
	var $js;
	var $css;
	var $writer;

	function __construct() {
		$this->CI =& get_instance();
		// Loads the assetic config (assetic.php under ./system/application/config/)
		$this->CI->load->config('assetic');
		$tmp_config =& get_config();

		if (count($tmp_config['assetic']) > 0) {
			$this->config = $tmp_config['assetic'];
			unset ($tmp_config);
		} else
			$this->_error('jquery_ext_configuration_error');

		$this->CI->load->helper('url');

		$this->collections['js'] = array();
		$this->collections['css'] = array();

		foreach($this->config['js']['autoload'] as $filename)
			$this->addJs($filename);

		foreach($this->config['css']['autoload'] as $filename)
			$this->addCss($filename);

	}

	public function addJs($filename, $group = 'common') {
		$group .= '.js';
		if(!isset($this->collections['js'][$group]))
			$this->collections['js'][$group] = new AssetCollection();

		if(parse_url($filename, PHP_URL_SCHEME) === null && strpos($filename, '//:') !== 0)
			$asset = new FileAsset($filename);
		else
			$asset = new HttpAsset($filename);

		$this->collections['js'][$group]->add( $asset );
	}

	public function addJsDir($path, $group = 'common') {
		$group .= '.js';
		if(!isset($this->collections['js'][$group]))
			$this->collections['js'][$group] = new AssetCollection();
		$this->collections['js'][$group]->add( new GlobAsset($path) );
	}

	public function getJs() {
		return $this->collections['js']->dump();
	}

	public function writeJsScripts() {
		$urls = array();
		foreach ($this->collections['js'] as $ac)
			$this->recursiveAssets($ac, $urls);
		$urls = array_unique($urls);

		foreach($urls as $url)
			echo '<script src="'.$url.'"></script>'."\n";
	}

	public function writeStaticJsScripts() {
		if(!isset($this->writer) || $this->writer !== null)
			$this->writer = new AssetWriter($this->config['static']['dir']);

		$urls = array();

		foreach ($this->collections['js'] as $filename => $ac) {
			$ac->setTargetPath($filename);
			$this->writer->writeAsset($ac);
			$urls[] = base_url($this->config['static']['dir'].$filename);
		}

		foreach($urls as $url)
			echo '<script src="'.$url.'"></script>'."\n";
	}

	public function addCss($filename, $group = 'style') {
		$group .= '.css';
		if(!isset($this->collections['css'][$group]))
			$this->collections['css'][$group] = new AssetCollection();
		$this->collections['css'][$group]->add( new FileAsset($filename) );
	}

	public function addCssDir($path, $group = 'style') {
		$group .= '.css';
		if(!isset($this->collections['css'][$group]))
			$this->collections['css'][$group] = new AssetCollection();
		$this->collections['css'][$group]->add( new GlobAsset($path) );
	}

	public function getCss() {
		return $this->js->dump();
	}

	public function writeCssLinks() {
		$urls = array();
		foreach ($this->collections['css'] as $ac)
			$this->recursiveAssets($ac, $urls);
		$urls = array_unique($urls);

		foreach($urls as $url)
			echo '<link rel="stylesheet" type="text/css" href="'.$url.'" />'."\n";
	}

	public function writeStaticCssLinks() {
		if(!isset($this->writer) || $this->writer !== null)
			$this->writer = new AssetWriter($this->config['static']['dir']);

		$urls = array();

		foreach ($this->collections['css'] as $filename => $ac) {
			$ac->setTargetPath($filename);
			$this->writer->writeAsset($ac);
			$urls[] = base_url($this->config['static']['dir'].$filename);
		}

		foreach($urls as $url)
			echo '<link rel="stylesheet" type="text/css" href="'.$url.'" />'."\n";
	}


	private function recursiveAssets(AssetCollection $ac, &$urls) {
		foreach($ac->all() as $el) {
			if($el instanceof AssetCollection)
				$this->recursiveAssets($el, $urls);
			else {
				$filename = $el->getSourceRoot().'/'.$el->getSourcePath();
				if(parse_url($filename, PHP_URL_SCHEME) === null && strpos($filename, '//:') !== 0)
					$filename = base_url($filename);
				$urls[] = $filename;
			}
		}
	}
}