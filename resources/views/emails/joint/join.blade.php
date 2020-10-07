@component('mail::message')
# Dear Customer Service,

<p>Please open a Zimele {{ $data[$data['accTypes']] }} {{$data['accTypes'] == 'MM' ? ($data['detailFunds']): ''}} account with 
following details:</p>

<table style="font-size: 14px">
    <tr>
        <td>{{$data['firstApplicantName']}}</td>
        <td>{{$data['firstApplicantEmail']}}</td>
        <td>{{$data['firstApplicantPhone']}}</td>
    </tr>
    <tr>
        <td>{{$data['secondApplicantName']}}</td>
        <td>{{$data['secondApplicantEmail']}}</td>
        <td>{{$data['secondApplicantPhone']}}</td>
    </tr>
</table>

<p>Attached are their passport-sized photos and their national IDs</p>
<p>Get back to the client for them to activate their account by sending their contribution</p>




Best Regards,<br>
Zimele Online Team
Cell: 0722-207-662/0734-207-662 
@endcomponent
