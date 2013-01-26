<?php

class TagTransformCustom extends TagTransform
{
	function __construct($target, $function)
	{
		$this->_target = $target;
		$this->_function = $function;
	}
	
	function doTransform($existingTags)
	{
		return $this->{$this->_function}($existingTags);
	}
    
    function outputTags() 
    {
        // Assume not mandatory for now.
        return array( $this->_target => false );
    }   
    
    function runBeforeGuidCheck()
    {
        return false;
    }

    function atp_fixtures($existingTags)
    {
        $fixtures = array();
        foreach( $existingTags['candidate'] as $candidate )
        {
            $date = strtotime( $candidate['date'] );
            $live = (($candidate['live'] == 'Live' ) ? true : false );
            if ( ($date > time()) || $live )
            {
                $venue = $candidate['venue'];
                $tournament = $candidate['tournament'];
                $fixtures[] = array( 'date' => $date, 'live' => $live, 'tournament' => $tournament, 'venue' => $venue );
            }
        }
        return array( $this->_target => $fixtures );
    }
    
	private $_function;
	private $_target;
}

?>