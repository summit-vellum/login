<?php 

namespace Quill\Login\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Quill\Login\Models\Login;

class LoginUpdating
{
    // use Dispatchable, InteractsWithSockets, 
    use SerializesModels;
 
    public $data;

    /**
     * Create a new event instance.
     *
     * @param  \Quill\Login\Models\Login  $data
     * @return void
     */
    public function __construct(Login $data) 
    {
        $this->data = $data;  
    }
}
