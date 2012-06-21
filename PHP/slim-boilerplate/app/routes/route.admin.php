<?php

/**
 *	Define routes
 * 
 */
$app->get('/admin', 'Admin::index');
$app->get('/admin/logout', 'Auth::logout');
$app->get('/admin/events', 'Admin::events');

/**
 *	Organize route methods into a class 
 * 
 */
class Admin {

	// must be logged in 
	protected static $is_protected = false;

	
	public static $base_route = '/admin';
	public static $base_layout = 'admin.php';

	// Run before every function call
	public static function before(){
		//Auth::check();
	}
	public static function index(){
		// self::before();
		// echo "<br />Admin::index<br />";
		global $app;
		return $app->render('home.html', array('page_title'=>'cats'));
	}

	public static function logout(){
		// self::before();
		echo "<br />Admin::index<br />";
	}
}



?>