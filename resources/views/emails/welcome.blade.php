@component('mail::message')
# Welcome to Zimele Portal

Use this password <b>{{$otp}}</b> to Log in to your account.



Thanks,<br>
{{ config('app.name') }}
@endcomponent
