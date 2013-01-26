<?php

abstract class APICommand
{
    function __construct()
    {
        
    }
    
    // Perform the work of this command.
    // Input is an array, output is APIOutput
    abstract function run( $inputs );   
    
    // Returns a description of the method
    abstract function description();
    
    // Returns an array of name => APIParameters forming the input
    abstract function inputs();
}

?>
