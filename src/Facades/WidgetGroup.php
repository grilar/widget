<?php

namespace Grilar\Widget\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Grilar\Widget\WidgetGroup group(string $sidebarId)
 * @method static \Grilar\Widget\WidgetGroupCollection setGroup(array $args)
 * @method static \Grilar\Widget\WidgetGroupCollection removeGroup(string $groupId)
 * @method static array getGroups()
 * @method static string render(string $sidebarId)
 * @method static void load(bool $force = false)
 * @method static \Illuminate\Support\Collection getData()
 *
 * @see \Grilar\Widget\WidgetGroupCollection
 */
class WidgetGroup extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'grilar.widget-group-collection';
    }
}
