<?php

require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class APIHello extends APICommand
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public final function run( $inputs )
    {
        $message = "Hello " . $inputs['name'];
        $output = array ('message' => $message );
        return APIOutput::success( $output );
    }
    
    public final function description()
    {
        return "Returns a message";
    }
    
    public final function inputs()
    {
        $inputs = array( new APIParameter( 'name', 'Name to be returned', true ) );
        return $inputs;
    }    
}

?>
