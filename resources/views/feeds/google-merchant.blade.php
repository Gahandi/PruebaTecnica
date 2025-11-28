<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>{{ config('app.name') }} - Eventos</title>
        <link>{{ config('app.url') }}</link>
        <description>Feed de eventos disponibles para Google Merchant</description>
        <language>es-MX</language>
        
        @foreach($events as $event)
            @php
                $minPrice = $event->ticketTypes->min('pivot.price') ?? 0;
                $maxPrice = $event->ticketTypes->max('pivot.price') ?? 0;
                $eventUrl = route('events.show', $event);
                if ($event->space) {
                    $eventUrl = config('app.url') . '/' . $event->space->subdomain . '/' . $event->slug;
                }
                $imageUrl = \App\Helpers\ImageHelper::getImageUrl($event->image);
                $availability = $event->ticketTypes->sum(function($tt) use ($event) {
                    $total = $tt->pivot->quantity ?? 0;
                    $sold = \App\Models\Ticket::where('event_id', $event->id)
                        ->where('ticket_types_id', $tt->id)
                        ->count();
                    return max(0, $total - $sold);
                }) > 0 ? 'in stock' : 'out of stock';
            @endphp
            <item>
                <g:id>{{ $event->id }}</g:id>
                <title><![CDATA[{{ $event->name }}]]></title>
                <description><![CDATA[{{ strip_tags($event->description) }}]]></description>
                <link>{{ $eventUrl }}</link>
                <g:image_link>{{ $imageUrl }}</g:image_link>
                <g:availability>{{ $availability }}</g:availability>
                <g:price>{{ number_format($minPrice, 2, '.', '') }} MXN</g:price>
                @if($maxPrice > $minPrice)
                    <g:sale_price>{{ number_format($maxPrice, 2, '.', '') }} MXN</g:sale_price>
                @endif
                <g:condition>new</g:condition>
                <g:brand><![CDATA[{{ $event->space->name ?? config('app.name') }}]]></g:brand>
                <g:product_type><![CDATA[{{ $event->type_event->name ?? 'Evento' }}]]></g:product_type>
                <g:custom_label_0><![CDATA[{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d H:i') }}]]></g:custom_label_0>
                <g:custom_label_1><![CDATA[{{ $event->address }}]]></g:custom_label_1>
                @if($event->keywords)
                    <g:custom_label_2><![CDATA[{{ $event->keywords }}]]></g:custom_label_2>
                @endif
                @if($event->tags && $event->tags->count() > 0)
                    <g:custom_label_3><![CDATA[{{ $event->tags->pluck('name')->implode(', ') }}]]></g:custom_label_3>
                @endif
                <g:identifier_exists>no</g:identifier_exists>
            </item>
        @endforeach
    </channel>
</rss>

