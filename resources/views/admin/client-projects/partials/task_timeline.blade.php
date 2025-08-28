@php
    $groupedActivities = $task->activities()->latest()->get()->groupBy(function($activity) {
        return $activity->created_at->format('Y-m-d');
    });

    $eventColors = [
        'created' => 'bg-success',
        'updated' => 'bg-info',
        'deleted' => 'bg-danger',
    ];
    $eventIcons = [
        'created' => 'fas fa-plus',
        'updated' => 'fas fa-edit',
        'deleted' => 'fas fa-trash',
    ];
@endphp

<div class="timeline">
    @foreach($groupedActivities as $date => $activities)
        <div class="time-label">
            <span class="{{ $eventColors[$activities->first()->event] ?? 'bg-gray' }}">
                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
            </span>
        </div>

        @foreach($activities as $activity)
            @php $collapseId = 'activity-'.$activity->id; @endphp

            <div>
                <i class="{{ $eventIcons[$activity->event] ?? 'fas fa-info' }} {{ $eventColors[$activity->event] ?? 'bg-secondary' }}"></i>
                <div class="timeline-item">
                    <span class="time">
                        <i class="fas fa-clock"></i> {{ $activity->created_at->format('H:i') }}

                        @if($activity->event == 'updated' && $activity->properties && $activity->properties->isNotEmpty())
                            <button type="button" class="btn btn-tool btn-sm" data-toggle="collapse" data-target="#{{ $collapseId }}">
                                <i class="fas fa-plus"></i>
                            </button>
                        @endif
                    </span>

                    <h3 class="timeline-header">
                        <a href="#">{{ $activity->causer->name ?? 'System' }}</a>
                        {{ $activity->event }}
                    </h3>

                    @if($activity->event == 'updated' && $activity->properties && $activity->properties->isNotEmpty())
                        <div id="{{ $collapseId }}" class="timeline-body collapse">
                            <ul class="list-unstyled small mb-0">
                                @foreach($activity->properties['attributes'] ?? [] as $key => $val)
                                    @if(($activity->properties['old'][$key] ?? null) != $val)
                                        <li>
                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                            <span class="text-muted">{!! $activity->properties['old'][$key] ?? '—' !!}</span>
                                            → <span class="text-success">{!! $val !!}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endforeach

    <div>
        <i class="fas fa-clock bg-gray"></i>
    </div>
</div>