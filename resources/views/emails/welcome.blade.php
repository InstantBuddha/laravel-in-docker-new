<x-mail::message>
# Successful registration

Hello {{$name}}!

We are happy that you registered.

Your registered phone number is: **{{$phone_number}}**


<x-mail::button :url="'https://www.google.com/'">
Open a url
</x-mail::button>

Thanks,<br>
{{ config('mail.from.name') }}
</x-mail::message>
