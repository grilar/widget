<?php

namespace Grilar\Widget\Facades;

use Grilar\Widget\WidgetGroup;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Grilar\Widget\Factories\WidgetFactory registerWidget(string $widget)
 * @method static array getWidgets()
 * @method static \Illuminate\Support\HtmlString|string|null run()
 *
 * @see \Grilar\Widget\Factories\WidgetFactory
 */
class Widget extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'grilar.widget';
    }

    public static function group(string $name): WidgetGroup
    {
        return app('grilar.widget-group-collection')->group($name);
    }
}
