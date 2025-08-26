@foreach($messages as $msg)
<div class="direct-chat-msg {{ $msg->user_id == auth()->id() ? 'right' : '' }}">
    <div class="direct-chat-infos clearfix">
        <span class="direct-chat-name {{ $msg->user_id == auth()->id() ? 'float-right' : 'float-left' }}">
            {{ $msg->sender->name ?? 'N/A' }}
        </span>
        <span class="direct-chat-timestamp {{ $msg->user_id == auth()->id() ? 'float-left' : 'float-right' }}">
            {{ $msg->created_at->format('d M, h:i a') }}
        </span>
    </div>
    <div class="direct-chat-img d-flex align-items-center justify-content-center bg-primary text-white" 
         style="border-radius:50%; width:40px; height:40px; font-weight:bold;">
        {{ substr($msg->sender->name ?? 'N/A', 0, 1) }}
    </div>
    <div class="direct-chat-text">
        {{ $msg->message }}
    </div>
</div>
@endforeach