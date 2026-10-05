<div class="msm">
    @include('moonshine-monitoring::partials.styles')
    @if($pageUrl)
        <div class="msm-card-head">
            <h3>{{ __('moonshine-monitoring::ui.monitoring') }}</h3>
            <a class="msm-widget-link" href="{{ $pageUrl }}">{{ __('moonshine-monitoring::ui.details') }} →</a>
        </div>
    @endif
    @include('moonshine-monitoring::partials.tiles')
</div>
