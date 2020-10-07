<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountOpeningClient extends Mailable
{
    use Queueable, SerializesModels;

    private $_data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->_data = $data;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $id = collect(collect($this->_data)->get('nationalId'))->pluck('value')->first();
        $photo = collect(collect($this->_data)->get('passportPhoto'))->pluck('value')->first();

        return $this->markdown('emails.individual.ut.client')->with('data',$this->_data)
                    ->attachData(base64_decode($id),'nationalID.jpg',['mime' => 'image/jpeg'])                                
                    ->attachData(base64_decode($photo),'passportPhoto.jpg',['mime' => 'image/jpeg']);                              

 
    }
}
