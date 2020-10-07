@component('mail::message')
# Hello Customer Service,


Please open a Zimele {{$data[$data['accTypes']]}} account with the following details:
<b>Name:</b> {{$data['name']}} <br>
<b>PP/ID Number:</b> {{$data['idno']}} <br>
<b>Mobile Phone Number:</b> {{$data['telno']}} <br>
<b>Email Address:</b> {{$data['email']}} <br>
<b>Category:</b> {{$data['detailFunds']}}<br>


Attached is the passport size photo and national ID.

Get back to the client for them to activate the account by sending their contribution.


Best Regards,<br>
Zimele Online Team
Cell: 0722-207-662/0734-207-662 
@endcomponent




