@extends('layouts.app')

@section('title', 'Print Badge - ' . $userDetail->RegID)

@push('styles')
@php
    // Conversion factor: 1mm = 3.779527559px at 96 DPI
    $mmToPx = 3.779527559;
    $badgeWidthPx = $category->badge_width * $mmToPx;
    $badgeHeightPx = $category->badge_height * $mmToPx;
@endphp
<style>
    .badge-print {
        width: {{ $badgeWidthPx }}px;
        min-height: {{ $badgeHeightPx }}px;
        margin: 0 auto;
        position: relative;
        background: white;
        border: 0.53mm solid #3b82f6; /* 2px in mm */
        border-radius: 3.18mm; /* 12px in mm */
        padding: 5mm;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
    }

    .badge-field {
        position: relative;
        font-family: 'Comfortaa', sans-serif;
        width: 100%;
        box-sizing: border-box;
    }

    @media print {
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: {{ $category->badge_width }}mm;
            height: {{ $category->badge_height }}mm;
            overflow: hidden;
            font-family: 'Comfortaa', sans-serif;
        }
        .no-print {
            display: none !important;
        }
        .badge-print {
            border: none;
            box-shadow: none;
            width: {{ $category->badge_width }}mm !important;
            height: {{ $category->badge_height }}mm !important;
            min-height: {{ $category->badge_height }}mm !important;
            max-height: {{ $category->badge_height }}mm !important;
            padding: 0 !important;
            margin: 0 !important;
            overflow: hidden;
            position: absolute;
            top: 0;
            left: 0;
        }
        @page {
            size: {{ $category->badge_width }}mm {{ $category->badge_height }}mm;
            margin: 0mm;
        }
    }
</style>
@endpush

@section('content')
<div class="no-print" style="text-align: center; margin-bottom: 5.29mm;">
    <h2>Badge Preview - {{ $userDetail->RegID }}</h2>
    <p>Printing will start automatically...</p>
</div>

<div class="badge-print">
    @php
        // Sort visible fields by sequence
        $sortedFields = [];
        foreach($visibleFields as $field) {
            $layout = $layoutSettings->get($field);
            if ($layout) {
                $sortedFields[] = [
                    'field' => $field,
                    'layout' => $layout,
                    'sequence' => $layout->sequence ?? 999
                ];
            }
        }
        usort($sortedFields, function($a, $b) {
            return $a['sequence'] <=> $b['sequence'];
        });
    @endphp
    
    @foreach($sortedFields as $index => $item)
        @php
            $field = $item['field'];
            $layout = $item['layout'];
            
            $value = '';
            if ($field === 'QRcode') {
                $value = 'QR';
            } elseif ($field === 'Category') {
                $value = $userDetail->Category ?? '';
            } elseif ($field && str_starts_with($field, 'Instruction')) {
                // Static text label - get from layout settings
                $value = $layout->static_text_value ?? '';
            } else {
                $value = $userDetail->$field ?? '';
            }
            
            // Calculate margin top (line spacing) - always apply margin_top from layout
            $marginTop = $layout->margin_top ?? ($index > 0 ? 2 : 0);
            $marginTopPx = $marginTop * $mmToPx;
            
            // Font size for non-QR elements
            $fontSizePx = null;
            if ($field !== 'QRcode' && isset($layout->font_size)) {
                $fontSizePx = $layout->font_size * $mmToPx;
            }
            
            // QR code dimensions
            $qrWidth = null;
            $qrHeight = null;
            if ($field === 'QRcode') {
                $qrWidth = ($layout->width ?? 20) * $mmToPx;
                $qrHeight = ($layout->height ?? 20) * $mmToPx;
            }
        @endphp
        @if($field === 'QRcode' && $layout && $qrCode)
            @php
                $textAlign = $layout->text_align ?? 'left';
                $justifyContent = 'flex-start';
                if ($textAlign === 'center') {
                    $justifyContent = 'center';
                } elseif ($textAlign === 'right') {
                    $justifyContent = 'flex-end';
                }
            @endphp
            <div class="badge-field" style="margin-top: {{ $marginTopPx }}px; display: flex; justify-content: {{ $justifyContent }};">
                @if(($qrCodeType ?? 'svg') === 'svg')
                    @php
                        // Replace SVG dimensions - use px (already converted from mm)
                        $qrCodeSized = preg_replace('/width="[^"]*"/', 'width="' . $qrWidth . '"', $qrCode);
                        $qrCodeSized = preg_replace('/height="[^"]*"/', 'height="' . $qrHeight . '"', $qrCodeSized);
                    @endphp
                    <div style="width: {{ $qrWidth }}px; height: {{ $qrHeight }}px;">
                        {!! $qrCodeSized !!}
                    </div>
                @else
                    <img src="data:image/png;base64,{{ $qrCode }}" alt="QR" style="width: {{ $qrWidth }}px; height: {{ $qrHeight }}px; max-width: {{ $qrWidth }}px; max-height: {{ $qrHeight }}px; display: block; object-fit: contain;" width="{{ round($qrWidth) }}" height="{{ round($qrHeight) }}">
                @endif
            </div>
        @elseif($layout && $value)
            <div class="badge-field" style="
                margin-top: {{ $marginTopPx }}px;
                font-size: {{ $fontSizePx }}px;
                font-family: {{ $layout->font_family ?? 'Comfortaa' }};
                font-weight: {{ $layout->font_weight ?? 'normal' }};
                text-align: {{ $layout->text_align ?? 'left' }};
                color: {{ $layout->color ?? '#000000' }};
            ">
                {{ $value }}
            </div>
        @endif
    @endforeach
</div>

<div class="no-print" style="text-align: center; margin-top: 7.94mm;">
    <a href="{{ $redirectUrl ?? route('operator.badge.scan-print') }}" class="btn btn-primary">Back</a>
</div>

@push('scripts')
<script>
    window.onload = function() {
        setTimeout(function() {
            window.print();
            
            // After print dialog closes, redirect back
            setTimeout(function() {
                window.location.href = '{{ $redirectUrl ?? route("operator.badge.scan-print") }}';
            }, 1000);
        }, 500);
    };

    // Handle print cancel
    window.addEventListener('afterprint', function() {
        setTimeout(function() {
            window.location.href = '{{ $redirectUrl ?? route("operator.badge.scan-print") }}';
        }, 500);
    });
</script>
@endpush
@endsection
