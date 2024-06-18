<x-mail::message>
# Hello {{$data['name']}}

<p>
    This is to notify you that,
</p> 
<p>
    there is a fault reported as per details below.
</p>
<p>
    Equipment Name: {{$data['equipment_name']}}
</p>
<p>
    Equipment Serial Number: {{$data['equipment_serial_no']}}
</p>
<p>
    Department: {{$data['department']}}
</p>

<p>
    Description
</p>
<p>
    {{$data['issue']}}
</p>
Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
