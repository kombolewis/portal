<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JointAccountOpeningClient extends Mailable
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

        $data = collect($this->_data);
        $idFirst = collect(collect($data->get('nationalId'))->get(0))->pluck('value')->first();
        $idSecond = collect(collect($data->get('nationalId'))->get(1))->pluck('value')->first();
        $photoFirst = collect(collect($data->get('passportPhoto'))->get(0))->pluck('value')->first();
        $photoSecond = collect(collect($data->get('passportPhoto'))->get(1))->pluck('value')->first();

        return $this->markdown('emails.joint.client')->with('data', $this->_data)
                    ->attachData(base64_decode($idFirst),$data->get('firstApplicantName').'jpg',['mime' => 'image/jpeg'])                                
                    ->attachData(base64_decode($idSecond),$data->get('secondApplicantName').'jpg',['mime' => 'image/jpeg'])                                
                    ->attachData(base64_decode($photoFirst),$data->get('firstApplicantName').'jpg',['mime' => 'image/jpeg'])                                
                    ->attachData(base64_decode($photoSecond),$data->get('secondApplicantName').'jpg',['mime' => 'image/jpeg']);                              
              
    }
}
