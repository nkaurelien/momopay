<?php

namespace Nkaurelien\Momopay\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Nkaurelien\Momopay\Fluent\MomoRequestToPayResultDto;

class PaymentAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $operator;

    /**
     * @var string
     */
    private $referenceId;

    /**
     * @var array
     */
    public $responseData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(string $operator, string $referenceId, MomoRequestToPayResultDto $responseData)
    {

        $this->operator = $operator;
        $this->referenceId = $referenceId;
        $this->responseData = $responseData;
    }

//    /**
//     * Get the channels the event should broadcast on.
//     *
//     * @return \Illuminate\Broadcasting\Channel|array
//     */
//    public function broadcastOn()
//    {
//        return new PrivateChannel('payement');
//    }
}
