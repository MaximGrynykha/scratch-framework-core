<?php

namespace Ismaxim\ScratchFrameworkCore\exception;

class ForbiddenException extends \Exception 
{
    protected $message = 'Use don\'t have permission to access this page';
    protected $code = 403;
}