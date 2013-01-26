<?php

class APIOutput
{
    public static function success( $outputs )
    {
        return new APIOutput( APIErrorCode::success, $outputs );        
    }
    
    public static function error( $errorCode, $errorMessage )
    {
        return new APIOutput( $errorCode, array( 'error' => $errorMessage) );
    }
    
    private function __construct($errorCode, $outputs)
    {
        $this->errorCode = $errorCode;
        $this->outputs = $outputs;
    }    
    
    public function jsonEncode()
    {
        $result = $this->outputs;
        $result['status'] = $this->errorCode;
        
        return json_encode( $result );
    }
    
    public function errorCode()
    {
        return $this->errorCode;
    }
    
    public function outputs()
    {
        return $this->outputs;
    }
    
    public function errorMessage()
    {
        return $this->outputs['error'];
    }
    
    private $errorCode;
    private $outputs;
}

?>
