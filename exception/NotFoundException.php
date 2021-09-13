<?php

namespace app\core\exception;

class NotFoundException extends \Exception
{
    public $message = 'Page not found';
    public $code = 404;
}