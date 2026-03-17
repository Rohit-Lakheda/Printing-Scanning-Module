<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: {{ $category->badge_width }}mm {{ $category->badge_height }}mm;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Comfortaa', sans-serif;
        }
        .badge-print {
            width: {{ $category->badge_width }}mm;
            height: {{ $category->badge_height }}mm;
            margin: 0;
            padding: 0;
            background: white;
            display: flex;
            flex-direction: column;
            page-break-inside: avoid;
        }
        .badge-field {
            width: 100%;
        }
    </style>
</head>
<body>
@php
    $mmToPx = 3.779527559;

    // Prepare sorted fields same way as bulk PDF
    $sortedFields = [];
    foreach($visibleFields as $field) {
        $layout = $layoutSettings->get($field);
        if ($layout) {
            $sortedFields[] = [
                'field' => $field,
                'layout' => $layout,
                'sequence' => $layout->sequence ?? 999,
            ];
        }
    }
    usort($sortedFields, function($a, $b) {
        return $a['sequence'] <=> $b['sequence'];
    });
@endphp

<div class="badge-print">
    @foreach($sortedFields as $fieldIndex => $item)
        @php
            $field = $item['field'];
            $layout = $item['layout'];

            // Determine value based on field type
            $value = '';
            if ($field === 'QRcode') {
                $value = '';
            } elseif ($field === 'Category') {
                $value = $userDetail->Category ?? '';
            } elseif ($field && str_starts_with($field, 'Instruction')) {
                // Static text label - get from layout settings
                $value = $layout->static_text_value ?? '';
            } else {
                $value = $userDetail->$field ?? '';
            }

            $marginTop = $layout->margin_top ?? ($fieldIndex > 0 ? 2 : 0);
            $marginTopPx = $marginTop * $mmToPx;
            $fontSizePx = null;
            if ($field !== 'QRcode' && isset($layout->font_size)) {
                $fontSizePx = $layout->font_size * $mmToPx;
            }
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
                // For dompdf, use text-align with inline-block instead of flexbox
                $imageAlign = $textAlign;
            @endphp
            <div class="badge-field" style="margin-top: {{ $marginTopPx }}px; text-align: {{ $imageAlign }};">
                <img src="{{ $qrCode }}"
                     alt="QR"
                     style="width: {{ $qrWidth }}px; height: {{ $qrHeight }}px; max-width: {{ $qrWidth }}px; max-height: {{ $qrHeight }}px; display: inline-block; object-fit: contain;"
                     width="{{ round($qrWidth) }}"
                     height="{{ round($qrHeight) }}">
            </div>
        @elseif($layout && $value !== '')
            @php
                $fontFamily = $layout->font_family ?? 'Comfortaa';
                $dompdfFontMap = [
                    'Arial' => 'Helvetica', 'Helvetica' => 'Helvetica', 'Verdana' => 'Helvetica',
                    'Tahoma' => 'Helvetica', 'Trebuchet MS' => 'Helvetica', 'Impact' => 'Helvetica',
                    'Lucida Sans' => 'Helvetica', 'Comic Sans MS' => 'Helvetica',
                    'Georgia' => 'Times-Roman', 'Times New Roman' => 'Times-Roman', 'Palatino' => 'Times-Roman',
                    'Garamond' => 'Times-Roman', 'Bookman' => 'Times-Roman',
                    'Courier New' => 'Courier',
                ];
                $fontFamily = $dompdfFontMap[$fontFamily] ?? $fontFamily;
                $fontFamilyCss = "'" . addslashes($fontFamily) . "'";
                $fontWeight = $layout->font_weight ?? 'normal';
                if ($fontWeight === 'bold') {
                    $fontWeight = 'bold';
                } elseif ($fontWeight === 'normal') {
                    $fontWeight = 'normal';
                }
            @endphp
            <div class="badge-field" style="
                margin-top: {{ $marginTopPx }}px;
                font-size: {{ $fontSizePx }}px;
                font-family: {{ $fontFamilyCss }}, sans-serif;
                font-weight: {{ $fontWeight }};
                text-align: {{ $layout->text_align ?? 'left' }};
                color: {{ $layout->color ?? '#000000' }};
            ">
                {{ $value }}
            </div>
        @endif
    @endforeach
</div>
</body>
</html>

