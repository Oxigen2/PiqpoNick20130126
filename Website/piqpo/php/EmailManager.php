<?php

require_once('Mail.php');

class EmailManager
{
	function __construct()
	{
		$this->_params = $GLOBALS['MAIL_PARAMS'];
	}
	    
	function send( $to, $from, $subject, $body )
	{
        $headers = array();
        $headers['To'] = $to;
        $headers['From'] = $from;
        $headers['Subject'] = $subject;

        $message = null;
        if ( isset( $this->_params ) )
        {
            $message =& Mail::factory('smtp', $this->_params);            
        }
        else
        {
            $message =& Mail::factory('mail');
        }
        $res = $message->send($to, $headers, $body);

        return $res;
	}

	private $_params;
}

?>