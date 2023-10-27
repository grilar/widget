<?php

namespace Grilar\Widget\Repositories\Interfaces;

use Grilar\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface WidgetInterface extends RepositoryInterface
{
    public function getByTheme(string $theme): Collection;
}
