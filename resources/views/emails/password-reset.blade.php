<x-mail::message>
    <div>
        <center>
            <img src="{{$message->embed(public_path().'/assets/logo/christmass-logo.png')}}" alt="Logo" height="60px">
        </center>
    </div>

# Hello {{$data['name']}},

We have received your password reset request,
please use the link below and the code to reset your password.
Here is your reset code

# {{$data['code']}}
Here is the link to reset the password
    <x-mail::button :url="'http://localhost:5173/reset-password'">
        Reset Password
    </x-mail::button>
If you did not send this request please ignore this email.

Thanks,
{{ config('app.name') }}
</x-mail::message>
