<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OltCliOutput implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $sessionId,
        public string $type,   // output | error | status | connected | disconnected
        public string $data
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('olt-cli.' . $this->sessionId)];
    }

    public function broadcastAs(): string
    {
        return 'cli.output';
    }

    public function broadcastWith(): array
    {
        $data = $this->data ?? '';

        // Remove control characters (keep \n \r \t)
        $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $data);

        // Force valid UTF-8
        if (!mb_check_encoding($data, 'UTF-8')) {
            $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }
        $data = iconv('UTF-8', 'UTF-8//IGNORE', $data) ?: '';

        return [
            'type' => $this->type,
            'data' => $data,
        ];
    }
}