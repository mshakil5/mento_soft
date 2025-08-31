@component('mail::message')
New Quotation Request

**Name:** {{ $first_name }} {{ $last_name }}  
**Email:** {{ $email }}  
**Phone:** {{ $phone }}  
**Subject:** {{ $subject }}  


**Message:**  
{{ $message }}

Thanks,  
{{ config('app.name') }}
@endcomponent