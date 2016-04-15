<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FirstCommitment extends Event implements ShouldBroadcast
{

    public $id;

    public $r1;

    public $hash;

    public $client_id;


    /**
     * Create a new event instance.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->id        = $message->id;
        $this->r1        = $message->r1;
        $this->hash      = $message->hash;
        $this->client_id = $message->client_id;
    }


    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [ 'chat' ];
    }
}
