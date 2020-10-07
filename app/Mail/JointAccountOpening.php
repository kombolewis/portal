<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JointAccountOpening extends Mailable
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
        
        $idFirst = collect(collect($data->get('nationalId'))->get(0))->get('value');
        $idSecond = collect(collect($data->get('nationalId'))->get(1))->get('value');
        $photoFirst = collect(collect($data->get('passportPhoto'))->get(0))->get('value');
        $photoSecond = collect(collect($data->get('passportPhoto'))->get(1))->get('value');

        return $this->markdown('emails.joint.join')->with('data', $this->_data)
                    ->attachData(base64_decode($idFirst),$data->get('firstApplicantName').'id.jpg',['mime' => 'image/jpeg'])                                
                    ->attachData(base64_decode($idSecond),$data->get('secondApplicantName').'id.jpg',['mime' => 'image/jpeg'])                                
                    ->attachData(base64_decode($photoFirst),$data->get('firstApplicantName').'pass.jpg',['mime' => 'image/jpeg'])                                
                    ->attachData(base64_decode($photoSecond),$data->get('secondApplicantName').'pass.jpg',['mime' => 'image/jpeg']);                              
                              

      
    }
}
