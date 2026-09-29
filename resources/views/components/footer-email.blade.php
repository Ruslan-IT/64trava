@if(filled($footerSetting->contact_title) || filled($footerSetting->contact_label))
    <div class="footer-email">
        @if(filled($footerSetting->contact_title))
            <h3>{{ $footerSetting->contact_title }}</h3>
        @endif

        @if(filled($footerSetting->contact_label))
            <a href="{{ filled($footerSetting->contact_url) ? $footerSetting->contact_url : '#' }}">
                {{ $footerSetting->contact_label }}
            </a>
        @endif
    </div>
@endif
