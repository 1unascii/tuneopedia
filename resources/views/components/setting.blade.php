@props(['setting', 'showTuneInfo' => false])

<div class="card bg-base-200 shadow-sm">
    <div class="card-body">
        @if($showTuneInfo)
            <h2 class="card-title">{{ $setting->tune->name }}</h2>
            <p class="text-sm text-base-content/60">{{ $setting->tune->tuneType->name }}</p>
        @endif

        <div class="flex items-center justify-between">
            <h3 class="font-semibold">{{ $setting->name }}</h3>
            <div class="flex items-center gap-4 text-sm text-base-content/60">
                @if($setting->key_signature)
                    <span>Key: {{ $setting->key_signature }}</span>
                @endif
                @if($setting->time_signature)
                    <span>Time: {{ $setting->time_signature }}</span>
                @endif
                @if($setting->votes_sum_vote_value !== null)
                    <span>
                        <i class="fa-solid fa-thumbs-up"></i>
                        {{ (int) $setting->votes_sum_vote_value }}
                    </span>
                @endif
            </div>
        </div>

        <div class="abc-notation mt-4" data-abc="{{ $setting->toAbc() }}"></div>

        @if($setting->source || $setting->book || $setting->transcription_credit)
            <div class="mt-2 text-xs text-base-content/50 space-y-1">
                @if($setting->source)
                    <p>Source: {{ $setting->source }}</p>
                @endif
                @if($setting->book)
                    <p>Book: {{ $setting->book }}</p>
                @endif
                @if($setting->transcription_credit)
                    <p>Transcription: {{ $setting->transcription_credit }}</p>
                @endif
            </div>
        @endif
    </div>
</div>
