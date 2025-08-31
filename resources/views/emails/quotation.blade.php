@component('mail::message')
New Quotation Request

**Name:** {{ $first_name }} {{ $last_name }}  
**Email:** {{ $email }}  
**Phone:** {{ $phone }}  
**Subject:** {{ $subject }}  
**Additional Info:** {{ $additional_info }}  


**Message:**  
{{ $dream_description }}

Thanks,  
{{ config('app.name') }}
@endcomponent