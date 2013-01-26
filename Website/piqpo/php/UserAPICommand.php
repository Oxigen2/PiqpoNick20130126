<?php

abstract class UserAPICommand extends APICommand
{
    public function __construct()
    {
        parent::__construct();
    }

    final public function run( $inputs )
    {
        // Find token
        
        // Find user based on token
        
        // Run command
        $this->doWork( $inputs, $userId );
    }
    
    protected abstract function doWork( $inputs, $userId );
}

?>
