<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Http\View\Composers\MetadataComposer;
use App\Http\View\Composers\ModalComposer;
use App\Http\View\Composers\NotificationComposer;
use App\Http\View\Composers\PrimaryMenuComposer;
use App\Http\View\Composers\PublicAdministrationSelectorComposer;
use App\Traits\GetsLocalizedYamlContent;
use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Yaml\Exception\ParseException;

class ViewServiceProvider extends ServiceProvider
{
    use GetsLocalizedYamlContent;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeViewConfig();

        Blade::if('env', function ($environment) {
            return app()->environment($environment);
        });

        Blade::directive('markdown', function ($markdown) {
            return "<?php echo (new Markdown())->convertToHtml($markdown); ?>";
        });

        Blade::directive('excerpt', function ($markdown) {
            return "<?php echo (new Markdown())->excerpt($markdown); ?>";
        });

        Blade::directive('remainder', function ($markdown) {
            return "<?php echo (new Markdown())->remainder($markdown); ?>";
        });

        View::composer([
            'auth.*',
            'pages.*',
        ], PublicAdministrationSelectorComposer::class);
        View::composer('*', PrimaryMenuComposer::class);
        View::composer('layouts.includes.head', MetadataComposer::class);
        View::composer('layouts.includes.modal', ModalComposer::class);
        View::composer('layouts.includes.notification', NotificationComposer::class);
        View::composer('layouts.includes.highlight_bar', function ($view) {
            $hasResetCountdown = 'public-playground' === config('app.env');
            $view->with('hasResetCountdown', $hasResetCountdown);
            if ($hasResetCountdown) {
                $nextDayResetPublicPlayground = 'next ' . config('wai.reset_public_playground_day_verbose', 'sunday');

                try {
                    $parsedNextDayReset = Carbon::parse($nextDayResetPublicPlayground);
                } catch (InvalidFormatException $ife) {
                    $parsedNextDayReset = Carbon::parse('next sunday');
                }

                $view->with('countdown', $parsedNextDayReset->setHour(config('wai.reset_public_playground_hour', 23))->setMinutes(config('wai.reset_public_playground_minute', 30))
                    ->diffForHumans(
                        Carbon::now(),
                        [
                            'syntax' => CarbonInterface::DIFF_ABSOLUTE,
                            'options' => Carbon::JUST_NOW | Carbon::ONE_DAY_WORDS | Carbon::TWO_DAY_WORDS,
                            'parts' => 2,
                            'join' => true,
                            'aUnit' => true,
                        ]
                    )
                );
            }
        });
        View::composer('*', function ($view) {
            $authUser = auth()->user();
            $view->with('authUser', $authUser);
            $view->with('spidAuthUser', app()->make('SPIDAuth')->getSPIDUser());
            $view->with('showPublicAdministration', session()->has('tenant_id') && (1 === $authUser->publicAdministrations->count()));
            $view->with('isSuperAdmin', isset($authUser) && $authUser->isA(UserRole::SUPER_ADMIN));

            $hasHighlightBar = config('site.highlight')[config('app.env')] ?? false;
            $view->with('hasHighlightBar', $hasHighlightBar);

            $view->with('bodyClasses', implode(' ', [
                'wai',
                $hasHighlightBar ? 'has-highlight-bar' : '',
            ]));
        });
    }

    /**
     * Merge the view configuration into the app configuration array.
     */
    protected function mergeViewConfig()
    {
        try {
            $localizedViewConfig = $this->getLocalizedYamlContent('config');

            config($localizedViewConfig);
        } catch (ParseException $exception) {
            abort(500, $exception->getMessage());
        }
    }
}
