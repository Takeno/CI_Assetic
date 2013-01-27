<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['assetic'] = array(
	'js' => array(
		//For every page
		'autoload' => array(
			'http://code.jquery.com/jquery-1.9.0.js'
		)
	),
	'css' => array(
		//For every page
		'autoload' => array(
			'css/main.css',
			'css/top.css'
		)
	),
	'static' => array(
		//Directory where Assetic puts the merged files
		'dir'					=> 'static/',
	)
);