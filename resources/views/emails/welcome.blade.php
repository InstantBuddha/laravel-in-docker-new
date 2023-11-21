<x-mail::message>
# Successful registration

Hello {{$member->name}}!

We are happy that you registered.

Your registered phone number is: **{{$member->phone_number}}**

Az árvíztűrő tükörfúrógép nagyon karakteres!


<x-mail::button :url="'https://www.google.com/'">
Open a url
</x-mail::button>

Thanks,<br>
{{ config('mail.from.name') }}
</x-mail::message>
