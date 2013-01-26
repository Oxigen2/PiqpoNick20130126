<?php

class APIParameter
{
    public function __construct($name, $description, $required)
    {
        $this->name = $name;
        $this->required = $required;
        $this->description = $description;
    }
    
    public function name()
    {
        return $this->name;
    }
    public function description()
    {
        return $this->description;
    }
    public function required()
    {
        return $this->required;
    }
    
    private $name;
    private $required;
    private $description;
}

?>
