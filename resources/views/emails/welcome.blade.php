<x-mail::message>
# Successful registration

Hello {{$member->name}}!

We are happy that you registered.

Your registered phone number is: **{{$member->phone_number}}**

Az árvíztűrő tükörfúrógép nagyon karakteres!

Your other optional data:

@if($member->city)
City: {{$member->city}}

@endif
@if($member->zipcode)
Zipcode: {{$member->zipcode}}

@endif
@if($member->address)
Address: {{$member->address}}

@endif
@if($member->comment)
Your comment: {{$member->comment}}

@endif
@if($member->is_subscribed_to_mailing_list)
You have chosen to receive our newsletter.

@endif

<x-mail::button :url="'https://www.google.com/'">
Open a url
</x-mail::button>

Thanks,<br>
{{ config('mail.from.name') }}
</x-mail::message>
