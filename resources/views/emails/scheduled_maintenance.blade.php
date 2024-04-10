<x-mail::message>
    <div>
        <center>
            <img src="{{$message->embed(public_path().'/assets/logo/christmass-logo.png')}}" alt="Logo" height="60px">
        </center>
    </div>

    Hello {{$data['name']}},

    {{$data['message']}}

    Equipment: {{$data['equipment']}}

    Department: {{$data['department']}}

    Date: {{$data['date']}}

    Thanks,
    {{ config('app.name') }}
</x-mail::message>
