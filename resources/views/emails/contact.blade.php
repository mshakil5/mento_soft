@component('mail::message')
# New Contact Message

**Name:** {{ $first_name }} {{ $last_name }}  
**Email:** {{ $email }}  
**Phone:** {{ $phone }}  
**Subject:** {{ $subject }}  

@if($product)
**Product Requested:** {{ $product->title }}
@endif

**Message:**  
{{ $message }}

Thanks,  
{{ config('app.name') }}
@endcomponent