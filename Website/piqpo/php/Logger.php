<?php

class Logger
{
    static function slideLogger()
    {
        return self::getLogger( "slide" );
    }    
    
    static function adminLogger()
    {
        return self::getLogger( "admin" );
    }    

    static function userLogger()
    {
        return self::getLogger( "user" );
    }    

    static function logDir( $relative = false )
    {
        return $relative ? self::$ms_baseDir : getenv("DOCUMENT_ROOT") . self::$ms_baseDir; 
    }
        
    private static function getLogger( $name )
    {
        if ( !isset(self::$ms_loggers) )
        {
            self::$ms_loggers = array();
        }
        if (!array_key_exists( $name, self::$ms_loggers ) )
        {    
            $filename = self::logDir() . $name . "_log_".date( "Ymd" ).".txt";            
            self::$ms_loggers[ $name ] = new KLogger( $filename, KLogger::DEBUG );
        }
        
        return self::$ms_loggers[ $name ];
    }    
       
    private static $ms_baseDir = "/log/";
    
    private static $ms_loggers;
    
    private static $ms_slideLogger;
    private static $ms_feedLogger;
}

?>
