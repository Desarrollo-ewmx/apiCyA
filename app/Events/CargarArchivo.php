<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CargarArchivo
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $blob_archivo, $ruta, $nom, $original_archivo;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($blob_archivo, $ruta, $nom, $original_archivo) {
        $this->blob_archivo = $blob_archivo;
        $this->ruta = $ruta;
        $this->nom = $nom;
        $this->original_archivo = $original_archivo;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
