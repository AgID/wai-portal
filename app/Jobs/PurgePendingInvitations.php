<?php

namespace App\Jobs;

use App\Enums\Logs\JobType;
use App\Enums\UserStatus;
use App\Events\Jobs\PurgePendingInvitationsCompleted;
use App\Exceptions\AnalyticsServiceException;
use App\Exceptions\CommandErrorException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PurgePendingInvitations implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle()
    {
        logger()->info(
            'Purging old pending invitations',
            [
                'job' => JobType::PURGE_PENDING_INVITATIONS,
            ]
        );

        $pendingInvitations = User::where('status', UserStatus::INVITED)->get();

        $oldPendingInvitations = $pendingInvitations->mapToGroups(function ($invitedUser) {
            if ($invitedUser->created_at->diffInDays(Carbon::now()) > (int) config('auth.verification.purge')) {
                try {
                    $invitedUser->deleteAnalyticsServiceAccount();
                } catch (BindingResolutionException $exception) {
                    report($exception);

                    return [
                        'failed' => [
                            'user' => $invitedUser->uuid,
                            'reason' => 'Unable to bind to Analytics Service',
                        ],
                    ];
                } catch (AnalyticsServiceException $exception) {
                    report($exception);

                    return [
                        'failed' => [
                            'user' => $invitedUser->uuid,
                            'reason' => 'Unable to contact the Analytics Service',
                        ],
                    ];
                } catch (CommandErrorException $exception) {
                    report($exception);

                    return [
                        'failed' => [
                            'user' => $invitedUser->uuid,
                            'reason' => 'Invalid command for Analytics Service',
                        ],
                    ];
                }
                $invitedUser->forceDelete();

                return [
                    'purged' => [
                        'user' => $invitedUser->uuid,
                    ],
                ];
            } else {
                return [
                    'pending' => [
                        'user' => $invitedUser->uuid,
                    ],
                ];
            }
        });

        event(new PurgePendingInvitationsCompleted(
            optional($oldPendingInvitations->get('purged'))->all() ?? [],
            optional($oldPendingInvitations->get('pending'))->all() ?? [],
            optional($oldPendingInvitations->get('failed'))->all() ?? []
        ));
    }
}
