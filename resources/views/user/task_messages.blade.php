@foreach($messages as $msg)
<div class="d-flex mb-3 {{ $msg->user_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
    @if($msg->user_id != auth()->id())
        <div class="me-2 text-center">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                 style="width:40px; height:40px; font-weight:bold;">
                {{ substr($msg->sender->name ?? '', 0, 1) }}
            </div>
            <small class="d-block text-muted">{{ $msg->sender->name ?? '' }}</small>
        </div>
    @endif

    <div>
        <div class="p-2 rounded {{ $msg->user_id == auth()->id() ? 'bg-success text-white' : 'bg-light text-dark' }}">
            {{ $msg->message }}
        </div>
        <small class="text-muted d-block mt-1 text-end" style="font-size:0.75rem;">
            {{ $msg->created_at->format('d M, h:i a') }}
        </small>
    </div>

    @if($msg->user_id == auth()->id())
        <div class="ms-2 text-center">
            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                 style="width:40px; height:40px; font-weight:bold;">
                {{ substr($msg->sender->name ?? '', 0, 1) }}
            </div>
            <small class="d-block text-muted">{{ $msg->sender->name ?? '' }}</small>
        </div>
    @endif
</div>
@endforeach