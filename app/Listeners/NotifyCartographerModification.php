<?php

namespace App\Listeners;

use App\Events\CartographerModifiedObject;
use App\Models\Cartographer;
use App\Services\MailerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

// Note: requires QUEUE_CONNECTION != sync in production for async delivery.
class NotifyCartographerModification implements ShouldQueue
{
    use Queueable, InteractsWithQueue;

    public function __construct(private readonly MailerService $mailer) {}

    public function handle(CartographerModifiedObject $event): void
    {
        if (! config('mercator.cartography.modification_enabled', false)) {
            return;
        }

        $name      = $event->object->getAttribute('name');
        $objectKey = $event->object->getKey();
        $class     = get_class($event->object);

        $routesMap  = Cartographer::cartographiableRoutesMap();
        $showRoute  = $routesMap[$class] ?? null;

        $objectUrl = $showRoute
            ? route($showRoute, $objectKey)
            : '#';

        $historyUrl = route('admin.audit-logs.history', [
            'type' => $class,
            'id'   => $objectKey,
        ]);

        // Longer keys before shorter prefixes to avoid partial substitution
        $placeholders = [
            ':object_history_url' => $historyUrl,
            ':object_url'         => $objectUrl,
            ':user'               => (string) $event->user->name,
            ':email'              => (string) $event->user->email,
            ':object'             => $event->objectType,
            ':id'                 => (string) $objectKey,
            ':name'               => $name !== null ? (string) $name : '#' . $objectKey,
            ':fields'             => implode(', ', array_keys($event->dirty)),
            ':date'               => now()->format('d/m/Y H:i'),
        ];

        $subject = str_replace(array_keys($placeholders), array_values($placeholders), (string) config('mercator.cartography.modification_subject', ''));
        $body    = str_replace(array_keys($placeholders), array_values($placeholders), (string) config('mercator.cartography.modification_body', ''));

        $from = (string) config('mercator.cartography.modification_from', '');
        $to   = (string) config('mercator.cartography.modification_to', '');

        if ($to === '') {
            Log::warning('[cartographer] modification_to is empty, skipping mail notification');
            return;
        }

        $this->mailer->send($from, $to, $subject, $body);

        Log::info("[cartographer] notification sent for {$event->user->email} modified {$event->objectType}#{$event->object->getKey()}");
    }
}
