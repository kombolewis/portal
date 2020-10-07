@component('mail::message')
# Hello {{$data['name']}},


Thank you for choosing to save and invest with us.

This is an automated message to acknowledge receipt of your personal information 
of Identification/Passport No. {{$data['idno']}} and Mobile number {{$data['telno']}}.

You will receive another email with your account details and instructions 
on how to deposit. Your account in the Zimele {{ $data[$data['accTypes']] }}  will be activated 
once your first contribution is received. 
The email will also give instructions on how to fill the beneficiary details.


Best Regards,<br>
Zimele Customer Care Team,
Cell: 0722-207-662/0734-207-662
@endcomponent
