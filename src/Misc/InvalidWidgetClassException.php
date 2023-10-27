<?php

namespace Grilar\Widget\Misc;

use Grilar\Widget\AbstractWidget;
use Exception;

class InvalidWidgetClassException extends Exception
{
    protected $message = 'Widget class must extend class ' . AbstractWidget::class;
}
