<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Http\View\Composers\ModalComposer;
use App\Http\View\Composers\NotificationComposer;
use App\Http\View\Composers\PrimaryMenuComposer;
use App\Http\View\Composers\PublicAdministrationButtonsComposer;
use App\Http\View\Composers\PublicAdministrationSelectorComposer;
use App\Traits\GetsLocalizedYamlContent;
use Carbon\CarbonInterface;
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
        View::composer('pages.pa.partials.buttons', PublicAdministrationButtonsComposer::class);
        View::composer('layouts.includes.modal', ModalComposer::class);
        View::composer('layouts.includes.notification', NotificationComposer::class);
        View::composer('layouts.includes.highlight_bar', function ($view) {
            $view->with('hasResetCountdown', 'public-playground' === config('app.env'));
            $nextDayResetPublicPlayground = 'next ' . config('wai.reset_public_playground_day_verbose', 'sunday');
            $view->with('countdown', Carbon::parse($nextDayResetPublicPlayground)->setHour(config('wai.reset_public_playground_hour', 23))->setMinutes(config('wai.reset_public_playground_minute', 30))
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
        });
        View::composer('*', function ($view) {
            $authUser = auth()->user();
            $view->with('authUser', $authUser);
            $view->with('spidAuthUser', app()->make('SPIDAuth')->getSPIDUser());
            $view->with('showPublicAdministration', session()->has('tenant_id') && (1 === $authUser->publicAdministrations->count()));
            $view->with('isSuperAdmin', isset($authUser) && $authUser->isA(UserRole::SUPER_ADMIN));
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
