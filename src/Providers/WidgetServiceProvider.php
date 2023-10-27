<?php

namespace Grilar\Widget\Providers;

use Grilar\Base\Facades\BaseHelper;
use Grilar\Base\Facades\DashboardMenu;
use Grilar\Base\Facades\Html;
use Grilar\Base\Supports\ServiceProvider;
use Grilar\Base\Traits\LoadAndPublishDataTrait;
use Grilar\Theme\Facades\AdminBar;
use Grilar\Theme\Facades\Theme;
use Grilar\Theme\Supports\ThemeSupport;
use Grilar\Widget\AbstractWidget;
use Grilar\Widget\Facades\WidgetGroup;
use Grilar\Widget\Factories\WidgetFactory;
use Grilar\Widget\Models\Widget;
use Grilar\Widget\Repositories\Eloquent\WidgetRepository;
use Grilar\Widget\Repositories\Interfaces\WidgetInterface;
use Grilar\Widget\WidgetGroupCollection;
use Grilar\Widget\Widgets\Text;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Auth;

class WidgetServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(WidgetInterface::class, function () {
            return new WidgetRepository(new Widget());
        });

        $this->app->bind('grilar.widget', function (Application $app) {
            return new WidgetFactory($app);
        });

        $this->app->singleton('grilar.widget-group-collection', function (Application $app) {
            return new WidgetGroupCollection($app);
        });

        $this->setNamespace('packages/widget')
            ->loadHelpers();
    }

    public function boot(): void
    {
        $this
            ->loadAndPublishConfigurations(['permissions'])
            ->loadRoutes()
            ->loadMigrations()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssets();

        $this->app->booted(function () {
            WidgetGroup::setGroup([
                'id' => 'primary_sidebar',
                'name' => trans('packages/widget::widget.primary_sidebar_name'),
                'description' => trans('packages/widget::widget.primary_sidebar_description'),
            ]);

            register_widget(Text::class);

            $widgetPath = theme_path(Theme::getThemeName() . '/widgets');
            $widgets = BaseHelper::scanFolder($widgetPath);
            if (! empty($widgets) && is_array($widgets)) {
                foreach ($widgets as $widget) {
                    $registration = $widgetPath . '/' . $widget . '/registration.php';
                    if ($this->app['files']->exists($registration)) {
                        $this->app['files']->requireOnce($registration);
                    }
                }
            }

            add_filter('widget_rendered', function (string|null $html, AbstractWidget $widget) {
                if (! setting('show_theme_guideline_link', false) || ! Auth::check() || ! Auth::user()->hasPermission('widgets.index')) {
                    return $html;
                }

                $editLink = route('widgets.index') . '?widget=' . $widget->getId();
                $link = view('packages/theme::guideline-link', [
                    'html' => $html,
                    'editLink' => $editLink,
                    'editLabel' => __('Edit this widget'),
                ])->render();

                return ThemeSupport::insertBlockAfterTopHtmlTags($link, $html);
            }, 9999, 2);

            add_filter(THEME_FRONT_HEADER, function ($html) {
                if (! setting('show_theme_guideline_link', false) || ! Auth::check() || ! Auth::user()->hasPermission('widgets.index')) {
                    return $html;
                }

                return $html . Html::style('vendor/core/packages/theme/css/guideline.css');
            }, 16);
        });

        $this->app['events']->listen(RouteMatched::class, function () {
            DashboardMenu::registerItem([
                'id' => 'cms-core-widget',
                'priority' => 3,
                'parent_id' => 'cms-core-appearance',
                'name' => 'packages/widget::widget.name',
                'icon' => null,
                'url' => route('widgets.index'),
                'permissions' => ['widgets.index'],
            ]);

            if (function_exists('admin_bar')) {
                AdminBar::registerLink(
                    trans('packages/widget::widget.name'),
                    route('widgets.index'),
                    'appearance',
                    'widgets.index'
                );
            }
        });
    }
}
