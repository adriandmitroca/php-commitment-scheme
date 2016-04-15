<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SecondCommitment extends Event implements ShouldBroadcast
{

    public $id;

    public $message;

    public $r1;

    public $r2;

    public $client_id;


    /**
     * Create a new event instance.
     *
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->id        = $message->id;
        $this->r1        = $message->R1;
        $this->r2        = $message->R2;
        $this->message   = $message->content;
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
