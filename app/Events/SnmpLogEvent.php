<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SnmpLogEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param  string  $oltId     OLT id (channel key)
     * @param  string  $type      info | success | error | progress | done
     * @param  string  $message   Human readable log line
     * @param  mixed   $data      Optional payload (array/string)
     */
    public function __construct(
        public string $oltId,
        public string $type,
        public string $message,
        public mixed $data = null
    ) {}

    // In the Event
public function broadcastOn(): array
{
    return [new Channel('olt-cli.' . $this->sessionId)];   // public
}
    public function broadcastAs(): string
    {
        return 'snmp.log';`
    }

    public function broadcastWith(): array
    {
        return [
            'olt_id'  => $this->oltId,
            'type'    => $this->type,
            'message' => $this->message,
            'data'    => $this->data,
            'time'    => now()->toDateTimeString(),
        ];
    }
}