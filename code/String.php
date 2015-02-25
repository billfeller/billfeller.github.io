<?php

class String {
    private $_string;
    public function __construct($string) {
        $this->_string = $string;
    }

    public function __call($method, $arguments) {
        $this->_string = call_user_func($method, $this->_string);
        return $this;
    }

    public function getValue() {
        return $this->_string;
    }
}

$test = new String('  test, test2 ');
$test->trim();
var_dump($test->getValue());
$test->strlen();
var_dump($test->getValue());
