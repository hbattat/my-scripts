<?php

class SITE {
    public static $options = array();
    

    public static function init(){
        self::auto_load_options();
    }
    public static function auto_load_options(){
        $options = ORM::for_table('options')->where('autoload', 1)->find_many();
        self::handle_options($options);
       
    }

    public static function handle_options($data){
       
        foreach($data as $option){
            

            if(preg_match('/^({|\[)/', $option->value)){
                $option->value = json_decode($option->value);
            } 

            self::$options[$option->option] = $option->value;
        }
    }
    public static function get_option($key){
        if(!array_key_exists($key, self::$site)){
            handle_options(ORM::for_table('options')->where('option', $key)->find_many());
        }
        
       
        return $value;
    }

    public static function set_option($key, $value, $autoload = 0){
       
        
        self::$options[$key] = $value; 
        

        // if is object or array change to JSON
        if(is_object($value) || is_array($value)){
            $value = ''+json_encode($value);
        }
        
        $option = ORM::for_table('options')->create();
        $option->option = $key;
        $option->value = $value;
        $option->autoload = $autoload;
        $option->save();

        
   }

}