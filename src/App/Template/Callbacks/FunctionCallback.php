<?php

namespace Plex\Template\Callbacks;

use Plex\Template\Functions\Functions;

Class FunctionCallback
{
    public const FUNCTION_CALLBACK = '|{{function=([a-zA-Z_]+)\|?(.*)?}}|i';

    public function callback_parse_function($matches)
    {
        $helper = new Functions();
        $method = $matches[1];
        // $value = Helper::$method();
        // if(method_exists($helper,$method)){
        return $helper->{$method}($matches);
        // }
    }
}
