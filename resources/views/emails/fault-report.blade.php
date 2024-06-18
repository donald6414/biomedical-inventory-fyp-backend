<x-mail::message>
# Hello {{$data['name']}}

This is to notify you that, 
there is a fault reported as per details below.
Equipment Name: {{$data['equipment_name']}}
Equipment Serial Number: {{$data['equipment_serial_no']}}
Department: {{$data['department']}}

Description:
{{$data['issue']}}
Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
