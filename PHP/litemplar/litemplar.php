<?php 
/**
 * Litemplar 
 * @version 0.1
 * @author Andres Hermosilla andres@ahermosilla.com
 * -----------------------------------------------
 * A simple lite templating system. Built for quick
 * smallish projects that don't need anything complicated
 *
 * 
 * 
 * 
 * 
 * 
 * 
 */

class Litemplar {
	// Delimiters
	public static $del_r = '}}';
	public static $del_l = '{{';

	// Template file to be parsed
	public static $tmpl_file = '';
	// Directory of template(s) - Can be different the loaded template
	public static $tmpl_dir  = '';
	
	// Holds HTML parsed
	public static $tmpl_html = '';
	
	/**
	 * @tag e.g.
	 *      HTML {{%users}}{%}{{%/users}} 
	 *      array('users' => array('Bill','John','Pete'));
	 * 
	 * @tag e.g. 
	 *      {{%users}}{name}{dob}{{%/users}} 
	 *      array('users' => array(array('name'=>'Bill','dob'=>'12/12/1950'),array('name'=>'John','dob'=>'1/12/1976')));
	 * 
	 * @param  string $key Template tag
	 * @param  array $array Array of data passed into template
	 * 
	 */
	private static function _parseArray($key, $array)
	{
		$tags_remove = '/'.preg_quote(self::$del_l).'\%\/?'.$key.preg_quote(self::$del_r).'(\s?)+/';
		$needle = '|'.preg_quote(self::$del_l).'\%'.$key.preg_quote(self::$del_r).'(.+?)'.preg_quote(self::$del_l).'\%/'.$key.preg_quote(self::$del_r).'|s';

		preg_match_all($needle, self::$tmpl_html, $matches);

		foreach($matches[0] as $match){
			$html = '';

			foreach($array as $key => $value){
				$i = $key + 1;
				$tmpl = preg_replace($tags_remove, '', $match);

				if(!is_array($value)){
					$temp_html = str_replace(array("{%}",'{$}'), array($value, $i), $tmpl);
				} else {
					$temp_html = self::_parseArrayAsc($value, $i, $tmpl);
				}

				$html .= $temp_html;
			}

			self::$tmpl_html = str_replace($match, $html, self::$tmpl_html);
		}
	}
	/**
	 * 
	 * @param array $array Associative array to be parse
	 * @param integer $i Iterator count, can be used in template
	 * @param string $tmpl Micro template from looped passed in
	 * @return string Sends back parse template
	 */
	private static function _parseArrayAsc($array, $i, $tmpl)
	{
		foreach($array as $key => $value){
			$tmpl = str_replace(array("{".$key."}",'{$}'), array($value,$i), $tmpl);
		}
		return $tmpl;
	}
	/**
	 * @tag e.g.
	 *      HTML {{title}} 
	 *      array('title' => 'Hellow World!');
	 * 
	 * @param  string $key Template tag
	 * @param  string $str String to replace template tag
	 * 
	 */
	private static function _parseString($key, $str)
	{
		// @template_usage : {{title}}
		$needle = self::$del_l.$key.self::$del_r;
		self::$tmpl_html = str_replace($needle, $str, self::$tmpl_html);
	}
	/**
	 * @tag e.g.
	 *      HTML {{=hascats}}{{=/hascats}}
	 *      array('hascats' => false);
	 * 
	 * @param  string $key Template tag
	 * @param  boolean $bool Boolean value, to show or not to show content
	 * 
	 */
	private static function _parseBoolean($key, $bool)
	{
		$tags_remove = '/'.preg_quote(self::$del_l).'\=\/?'.$key.preg_quote(self::$del_r).'(\s?)+/';
		$needle = '|'.preg_quote(self::$del_l).'\='.$key.preg_quote(self::$del_r).'(.+?)'.preg_quote(self::$del_l).'\=/'.$key.preg_quote(self::$del_r).'|s';
		if($bool){
			self::$tmpl_html = preg_replace($tags_remove, '', self::$tmpl_html);
		} else {
			self::$tmpl_html = preg_replace($needle, '', self::$tmpl_html);
		}
	}
	/**
	 * Planned but not developed
	 * 
	 */
	private static function _parseObject($key,$value)
	{
		
	}
	/**
	 * @tag e.g.
	 *      HTML {{-inc=text.html}}
	 */
	private static function _parseIncludes(){
		$tags_remove = array(self::$del_l.'-inc=',self::$del_r);
		$needle = '/'.preg_quote(self::$del_l).'-inc=(.+)'.preg_quote(self::$del_r).'/';

		preg_match_all($needle, self::$tmpl_html, $matches);
		
		foreach($matches[0] as $match){
			// strip tags and get only file name - pass directory
			$file = self::$tmpl_dir .'/'. str_replace($tags_remove, '', $match);
			// Verify files exists
			if (file_exists($file)) {
				$html = file_get_contents($file);
				self::$tmpl_html = str_replace($match, $html, self::$tmpl_html);
			} else {
				throw new Exception("File $file doesn't exist - Check $match");
			}
			
		}
	}
	/**
	 *
	 *  Runs on parse to verify template file exists
	 * 
	 */
	private static function checkTemplate()
	{
		if (!file_exists(self::$tmpl_file)) {
			throw new Exception("Cant seem to find ".self::$tmpl_file);
		}
		// If template directory not set, assume base on template file
		if(self::$tmpl_dir === ''){
			self::$tmpl_dir = dirname(self::$tmpl_file);
		}

		self::$tmpl_html = file_get_contents(self::$tmpl_file);
		 
	}
	/**
	 * The function that ties it all togther
	 * 
	 * @param  array $data Data in associate array format that is passed into the template
	 * @param  string $tmpl_file Template file parser loads
	 * 
	 */
	public static function parse($data = array(), $tmpl_file = '')
	{
		// If template is specified, set
		if($tmpl_file !== ''){
			self::$tmpl_file = $tmpl_file;
		}

		// Verify template has been defined
		self::checkTemplate();

		// Load includes files at beggining, that way template tags can be parsed
		self::_parseIncludes();

		// Loop through $data, passing into actions based on type of each array value
		foreach($data as $key => $value){
			$type =  ucfirst(gettype($value));
			$action = '_parse'.$type;
			self::$action($key, $value);
		}

		return self::$tmpl_html;
	}
}

$values = array(
			'title' => 'Hello World',
			'firstname' => 'test',
			'lastname' => 'more',
			'users' => array(
				array('name'=>'joe','dob'=>'now'),
				array('name'=>'john','dob'=>'now'),
				array('name'=>'bill','dob'=>'now')
			),
			'hascats' => true
		);
Litemplar::$tmpl_dir = 'tmpl';
echo Litemplar::parse($values,'email.html');

