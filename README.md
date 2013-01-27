#CI_Assetic

This CodeIgniter library provides easy management of static resources like JavaScript and CSS files using [Assetic](https://github.com/kriswallsmith/assetic "Assetic library on Github"). In this way you can set assets for every page and set specific assets from controller or in top of your views (of course you must set them before the output method is invoked).

With Assetic you can also merge all your assets in production and reduce HTTP requests.

###Config - application/config/assetic.php
```php
$config['assetic'] = array(
	'js' => array(
		//For every page
		'autoload' => array(
			'http://code.jquery.com/jquery-1.9.0.js',
			'js/my-fantastic-effects.js'
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
		'enabled'			=> true,
		//Directory where Assetic puts the merged files
		'dir'				=> 'static/',
	)
);
```

###Very basic example
How to use Assetic in Views.

*application/views/header.php*
```php
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Title</title>
	<?php
		if(ENVIRONMENT === 'development')
		 	$this->assetic->writeCssLinks();
		else
			$this->assetic->writeStaticCssLinks();
	 ?>
</head>
<body>
```

*application/views/footer.php*
```php
<?php
	if(ENVIRONMENT === 'development')
		$this->assetic->writeJsScripts();
	else
		$this->assetic->writeStaticJsScripts();
?>
</body>
</html>
```

*application/views/index.php*
```php
<?php
	//The second parameter will be the name of the merged file
	//e.g. index.css
	//The default filename is 'style.css' used by autoload
	$this->assetic->addCss('css/only-index.css', 'index');
	$this->load->view('header');
?>
<p>Hello World</p>
<?php
	//The second parameter will be the name of the merged file
	//e.g. index.js
	//The default filename is 'common.js' used by autoload
	$this->assetic->addJs('js/index/*', 'index');
	$this->load->view('footer'); ?>
```


####Result in development 
```html
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Title</title>
	<link rel="stylesheet" type="text/css" href="http://www.example.com/css/main.css" />
	<link rel="stylesheet" type="text/css" href="http://www.example.com/css/top.css" />
	<link rel="stylesheet" type="text/css" href="http://www.example.com/css/only-index.css" />
</head>
<body>
	<p>Hello World</p>
	<script src="http://code.jquery.com/jquery-1.9.0.js"></script>
	<script src="http://www.example.com/js/index/a.js"></script>
	<script src="http://www.example.com/js/index/b.js"></script>
</body>
</html>
```


####Result in production 
```html
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Title</title>
	<link rel="stylesheet" type="text/css" href="http://www.example.com/static/style.css" />
	<link rel="stylesheet" type="text/css" href="http://www.example.com/static/index.css" />
</head>
<body>
	<p>Hello World</p>
	<script src="http://www.example.com/static/common.js"></script>
	<script src="http://www.example.com/static/index.js"></script>
</body>
</html>
```