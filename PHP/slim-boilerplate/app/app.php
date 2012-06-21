<?php



/**
* Any libraries or includes load here
*/


require_once(APPATH . 'auth.php');
require_once(APPATH . 'site.php');

SITE::init();



/**
 *  If a ROUTE is declared later, the previous matches will run first,
 *  so specific at top, generic later 
 */



/**
* Setting up custom 404
*/

$app->notFound('not_found');

function not_found() {
    $app = Slim::getInstance();
    $app->render('404.html');
}

$app->get('/', 'index');

function index(){
	global $app;
	
	//print_r(SITE::$options);
	$view_data = array(
		'page_title' => 'cats', 
		'base_url' => 'test',
		'articles' => array(
			'id'=>1,
			'title' => 'title',
			'author' => 'me',
			'timestamp' =>'33'
		) 
	);
	return $app->render('home.html', $view_data);
}

/**
* Require routes from route directory
*/

foreach (glob(APPATH."routes/*.php") as $filename){
	require_once($filename);
}




$app->run();