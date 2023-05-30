@unless ($breadcrumbs->isEmpty())
    <ol class="breadcrumb float-sm-right">
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($breadcrumb->url && !$loop->last)
                <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">
                    @php
                    if(isset($breadcrumb->icon) && !empty($breadcrumb->icon)){
                    @endphp
                    <i class="{{ $breadcrumb->icon }}"></i>
                    @php
                        }
                    @endphp
                    {{ $breadcrumb->title }}</a></li>
            @else
                <li class="breadcrumb-item active">
                    @php
                    if(isset($breadcrumb->icon) && !empty($breadcrumb->icon)){
                    @endphp
                    <i class="{{ $breadcrumb->icon }}"></i>
                   @php
                        }
                    @endphp
                    {{ $breadcrumb->title }}
                </li>
            @endif

        @endforeach
    </ol>
@endunless


