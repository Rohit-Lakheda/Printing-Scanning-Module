@extends('layouts.app')

@section('title', 'Edit Badge Layout - ' . $categoryModel->Category)

@push('styles')
<style>
    .preview-container {
        display: flex !important;
        flex-direction: row !important;
        gap: 0;
        margin: 20px 0;
        align-items: flex-start;
        width: 100%;
        justify-content: space-between;
    }

    #badgePreview {
        position: relative;
        background: white;
        border: 2px solid #3b82f6;
        border-radius: 12px;
        margin: 0 auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        box-sizing: border-box;
        padding: 0;
        margin-top: 0;
        margin-bottom: 0;
    }

    .preview-element {
        position: relative;
        margin: 0;
        margin-bottom: 0;
        padding: 0;
        border-radius: 0;
        background-color: rgba(59, 130, 246, 0.1);
        border: 1px dashed #3b82f6;
        user-select: none;
        width: 100%;
        box-sizing: border-box;
    }

    .preview-element:hover {
        background-color: rgba(59, 130, 246, 0.2);
        border-color: #2563eb;
    }

    .preview-element.selected {
        background-color: rgba(59, 130, 246, 0.3);
        border-color: #1e40af;
        border-style: solid;
    }

    .preview-element.qr-code {
        background: transparent;
        border: 1px dashed #3b82f6;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .element-controls {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 350px;
        min-width: 350px;
        max-width: 350px;
        max-height: calc(100vh - 200px);
        overflow-y: auto;
        position: sticky;
        top: 20px;
        flex-shrink: 0;
        order: 2;
        margin-left: auto;
        margin-right: 0;
    }

    .element-controls h3 {
        margin-bottom: 15px;
        color: #1e40af;
        font-size: 18px;
    }

    .element-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e5e7eb;
    }

    .element-btn {
        padding: 10px 16px;
        border: 2px solid #3b82f6;
        border-radius: 10px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        color: #1e40af;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Comfortaa', sans-serif;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        position: relative;
        overflow: hidden;
    }

    .element-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .element-btn:hover::before {
        left: 100%;
    }

    .element-btn:hover {
        border-color: #2563eb;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #1e3a8a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    }

    .element-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(59, 130, 246, 0.2);
    }

    .element-btn.active {
        border-color: #1e40af;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
        transform: translateY(-1px);
    }

    .element-btn.active:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
    }

    .control-group {
        margin-bottom: 20px;
    }

    .control-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        font-family: 'Comfortaa', sans-serif;
    }

    .control-group input,
    .control-group select {
        width: 100%;
        padding: 12px 14px;
        border: 2px solid #d1d5db;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        font-family: 'Comfortaa', sans-serif;
        background: #ffffff;
        color: #1f2937;
    }

    .control-group input:hover,
    .control-group select:hover {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.1);
    }

    .control-group input:focus,
    .control-group select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        background: #ffffff;
    }

    .control-group input[type="color"] {
        height: 50px;
        cursor: pointer;
        padding: 4px;
        border-radius: 10px;
        border: 2px solid #d1d5db;
    }

    .control-group input[type="color"]:hover {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.1);
    }

    .control-group input[type="color"]:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    }

    .control-group input[disabled] {
        background: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
        border-color: #e5e7eb;
    }

    .control-group select {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%233b82f6' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 12px;
        padding-right: 40px;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .preview-section {
        flex: 0 0 auto;
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        order: 1;
        margin-right: auto;
    }
</style>
@endpush

@section('content')
@php
    // Conversion factor: 1mm = 3.779527559px at 96 DPI
    $mmToPx = 3.779527559;
    $badgeWidthPx = $categoryModel->badge_width * $mmToPx;
    $badgeHeightPx = $categoryModel->badge_height * $mmToPx;
    
    // Generate sample QR code for preview (use SVG to avoid imagick requirement)
    $sampleRegID = 'PREVIEW123';
    $sampleQrCode = null;
    $sampleQrCodeType = 'svg';
    try {
        // Try SVG first (no extension required)
        $sampleQrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->generate($sampleRegID);
        // Convert SVG to base64 data URI for easier handling
        $sampleQrCode = 'data:image/svg+xml;base64,' . base64_encode($sampleQrCode);
        $sampleQrCodeType = 'svg-data';
    } catch (\Exception $e) {
        // Fallback: create a simple placeholder SVG
        $placeholderSvg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg"><rect width="200" height="200" fill="#f3f4f6" stroke="#3b82f6" stroke-width="2"/><text x="100" y="100" text-anchor="middle" dominant-baseline="middle" fill="#6b7280" font-family="Arial" font-size="14">QR Code</text></svg>';
        $sampleQrCode = 'data:image/svg+xml;base64,' . base64_encode($placeholderSvg);
        $sampleQrCodeType = 'svg-data';
    }
@endphp

<div class="card" style="overflow: visible;">
    <div class="card-header">
        <h1 class="card-title">Edit Badge Layout</h1>
        <p style="margin-top: 10px; color: #6b7280; font-size: 14px;">
            Select a category and configure which fields are visible and how they appear on the badge.
        </p>
    </div>

    <div style="display:flex; gap: 10px; margin-top: 10px; margin-bottom: 10px; flex-wrap: wrap;">
        @php
            $currentType = $layoutType ?? (request('type') ?: 'normal');
        @endphp
        <a
            href="{{ route('admin.badge-layout.edit', $categoryModel->Category) }}?type=normal"
            class="btn {{ $currentType === 'normal' ? 'btn-primary' : 'btn-secondary' }}"
        >
            Normal Printing
        </a>
        <a
            href="{{ route('admin.badge-layout.edit', $categoryModel->Category) }}?type=bulk"
            class="btn {{ $currentType === 'bulk' ? 'btn-primary' : 'btn-secondary' }}"
        >
            Bulk Printing
        </a>
    </div>

    <form id="layoutForm" action="{{ route('admin.badge-layout.update', $categoryModel->Category) }}?type={{ $currentType }}" method="POST" style="margin-top: 10px;">
        @csrf
        @method('PUT')
        <input type="hidden" name="layouts" id="layoutsInput" value="">
        <input type="hidden" name="layout_type" value="{{ $currentType }}">

        <div class="preview-container" style="display: flex; flex-direction: row; width: 100%; justify-content: space-between; gap: 0;">
            <div class="preview-section">
                <div style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
                    <label for="categorySelect" style="font-size: 14px; font-weight: 600; color: #374151;">Category:</label>
                    <select id="categorySelect" onchange="onCategoryChange(this.value)" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #d1d5db; font-size: 13px;">
                        @foreach(\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->Category }}" {{ $cat->Category === $categoryModel->Category ? 'selected' : '' }}>
                                {{ $cat->Category }} ({{ number_format($cat->badge_width, 2) }} x {{ number_format($cat->badge_height, 2) }} mm)
                            </option>
                        @endforeach
                    </select>
                </div>
                <h3 style="margin-bottom: 15px; color: #1e40af;">Badge Preview</h3>
                <div class="badge-dimension-display" style="margin-bottom: 10px; font-size: 14px; color: #6b7280;">
                    Dimensions: {{ number_format($categoryModel->badge_width, 2) }}mm × {{ number_format($categoryModel->badge_height, 2) }}mm (fixed size - content exceeding height will be clipped)
                </div>
                <div id="badgePreview" style="width: {{ $badgeWidthPx }}px; height: {{ $badgeHeightPx }}px; overflow: hidden; padding: 0; margin: 0 auto; display: flex; flex-direction: column; box-sizing: border-box;">
                    <!-- Elements will be dynamically added here -->
                </div>

                <div style="margin-top: 20px; text-align: center;">
                    <button type="submit" class="btn btn-primary">Save Layout & Display Settings</button>
                    <a href="{{ route('admin.badge-layout.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>

            <div class="element-controls">
                <h3>Element Properties</h3>

                <!-- Display Settings (what to show on badge) -->
                <div class="elements-list" style="margin-bottom: 20px;">
                    <h4 style="margin-bottom: 10px; font-size: 14px; color: #1e40af;">Fields to Show on Badge</h4>
                    @php
                        $display = $displaySettings ?? null;
                    @endphp
                    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px 16px; font-size: 12px;">
                        @foreach (['ShowCategory' => 'Category', 'RegID' => 'Reg. ID', 'Name' => 'Name', 'Email' => 'Email', 'Mobile' => 'Mobile', 'Designation' => 'Designation', 'Company' => 'Company', 'Country' => 'Country', 'State' => 'State', 'City' => 'City', 'Additional1' => 'Additional 1', 'Additional2' => 'Additional 2', 'Additional3' => 'Additional 3', 'Additional4' => 'Additional 4', 'Additional5' => 'Additional 5', 'QRcode' => 'QR Code'] as $fieldKey => $fieldLabel)
                            <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                                <input
                                    type="checkbox"
                                    name="display_settings[{{ $fieldKey }}]"
                                    value="1"
                                    {{ $display && isset($display->$fieldKey) && $display->$fieldKey ? 'checked' : '' }}
                                    style="width: 14px; height: 14px;"
                                    onchange="onDisplayFieldToggle('{{ $fieldKey }}', this.checked)"
                                >
                                <span>{{ $fieldLabel }}</span>
                            </label>
                        @endforeach
                    </div>
                    
                    <!-- Static Text Labels Section -->
                    <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
                        <h4 style="margin-bottom: 15px; font-size: 14px; color: #1e40af;">Static Text Labels (Same for all badges)</h4>
                        @for($i = 1; $i <= 5; $i++)
                            @php
                                $staticKey = 'Instruction' . $i;
                                $staticLayout = $layoutSettings->get($staticKey);
                                $staticValue = $staticLayout ? $staticLayout->static_text_value : '';
                            @endphp
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; margin-bottom: 5px; font-size: 12px; font-weight: 600; color: #374151;">
                                    Instruction {{ $i }}:
                                </label>
                                <input
                                    type="text"
                                    id="static_text_{{ $i }}"
                                    placeholder="Enter static text for Instruction {{ $i }}"
                                    value="{{ $staticValue }}"
                                    style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px;"
                                    onchange="updateStaticText('{{ $staticKey }}', this.value)"
                                >
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Element Selection Buttons -->
                <div class="element-buttons" id="elementButtons">
                    <!-- Buttons will be populated here -->
                </div>

                <!-- Properties Form -->
                <div id="controlsContainer">
                    <p style="color: #6b7280; font-size: 14px; text-align: center; padding: 20px;">Select an element to edit</p>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Conversion factor: 1mm = 3.779527559px at 96 DPI
    const mmToPx = 3.779527559;
    const badgeWidthPx = {{ $badgeWidthPx }};
    const badgeHeightPx = {{ $badgeHeightPx }};
    const sampleQrCode = @json($sampleQrCode ?? '');
    const sampleQrCodeType = '{{ $sampleQrCodeType ?? 'svg-data' }}';

    let elements = [];
    let selectedElement = null;

    function onCategoryChange(category) {
        if (category) {
            const currentType = @json($currentType);
            window.location.href = "{{ url('admin/badge-layout') }}/" + encodeURIComponent(category) + "/edit?type=" + encodeURIComponent(currentType);
        }
    }

    function onDisplayFieldToggle(fieldName, isChecked) {
        // Handle ShowCategory specially - it uses Category field name
        if (fieldName === 'ShowCategory') {
            fieldName = 'Category';
        }
        
        // Update visibleFields array and re-init elements
        if (isChecked) {
            if (!visibleFields.includes(fieldName)) {
                visibleFields.push(fieldName);
            }
        } else {
            visibleFields = visibleFields.filter(f => f !== fieldName);
        }
        // Rebuild elements and re-render preview/buttons
        rebuildElementsFromVisibleFields();
        renderPreview();
        renderElementButtons();
    }

    // Handle static text updates
    function updateStaticText(staticKey, value) {
        // Find or create element for static text
        let element = elements.find(el => el.field_name === staticKey);
        if (!element && value.trim() !== '') {
            // Create new static text element
            element = {
                field_name: staticKey,
                margin_top: 2,
                sequence: elements.length,
                text_align: 'left',
                font_family: 'Tw Cen MT',
                font_weight: 'normal',
                color: '#000000',
                font_size: 3.70,
                static_text_value: value,
                preview_text: value,
            };
            elements.push(element);
            elements.sort((a, b) => (a.sequence || 0) - (b.sequence || 0));
        } else if (element) {
            element.static_text_value = value;
            element.preview_text = value;
        }
        
        // Remove element if value is empty
        if (value.trim() === '' && element) {
            elements = elements.filter(el => el.field_name !== staticKey);
        }
        
        renderPreview();
        renderElementButtons();
    }

    // Helper function to get default preview text
    function getDefaultPreviewText(fieldName) {
        const defaults = {
            'RegID': 'REG123',
            'Name': 'John Doe',
            'Email': 'john@example.com',
            'Mobile': '+1234567890',
            'Designation': 'Senior Manager',
            'Company': 'ABC Corporation',
            'Country': 'United States',
            'State': 'California',
            'City': 'Los Angeles',
            'Additional1': 'Additional Info 1',
            'Additional2': 'Additional Info 2',
            'Additional3': 'Additional Info 3',
            'Additional4': 'Additional Info 4',
            'Additional5': 'Additional Info 5',
        };
        return defaults[fieldName] || fieldName;
    }

    // Initialize elements from visible fields
    let visibleFields = @json($visibleFields ?? []);
    @php
        // Convert collection to array properly
        $layoutSettingsArray = $layoutSettings ? $layoutSettings->values()->toArray() : [];
    @endphp
    let layoutSettings = @json($layoutSettingsArray);

    console.log('Visible Fields:', visibleFields);
    console.log('Layout Settings:', layoutSettings);
    console.log('Elements array before init:', elements);

    // If no visible fields, show default
    if (!visibleFields || visibleFields.length === 0) {
        console.warn('No visible fields found, using defaults');
        visibleFields = ['RegID', 'Name'];
    }

    function rebuildElementsFromVisibleFields() {
        elements = []; // Clear any existing elements
        
        // First, add visible fields
        visibleFields.forEach((field, index) => {
            // Skip static text labels - they're handled separately
            if (field.startsWith('Instruction')) {
                return;
            }
            
            const savedLayout = layoutSettings.find(l => l.field_name === field);
            if (savedLayout) {
                // Use saved layout data
                const layout = {
                    field_name: savedLayout.field_name,
                    margin_top: parseFloat(savedLayout.margin_top || 0),
                    sequence: parseInt(savedLayout.sequence || index),
                    text_align: savedLayout.text_align || 'left',
                    font_family: savedLayout.font_family || 'Tw Cen MT',
                    font_weight: savedLayout.font_weight || 'normal',
                    color: savedLayout.color || '#000000',
                    font_size: savedLayout.font_size ? parseFloat(savedLayout.font_size) : (field === 'QRcode' ? null : 3.70),
                    width: savedLayout.width ? parseFloat(savedLayout.width) : (field === 'QRcode' ? 20 : null),
                    height: savedLayout.height ? parseFloat(savedLayout.height) : (field === 'QRcode' ? 20 : null),
                    preview_text: savedLayout.preview_text || getDefaultPreviewText(field), // Store preview text
                };
                elements.push(layout);
            } else {
                // Use default values for new fields
                const layout = {
                    field_name: field,
                    margin_top: index === 0 ? 0 : 2, // Default 2mm spacing
                    sequence: index,
                    text_align: 'left',
                    font_family: 'Tw Cen MT',
                    font_weight: 'normal',
                    color: '#000000',
                    font_size: field === 'QRcode' ? null : 3.70, // 3.70mm default
                    width: field === 'QRcode' ? 20 : null,
                    height: field === 'QRcode' ? 20 : null,
                    preview_text: getDefaultPreviewText(field), // Default preview text
                };
                elements.push(layout);
            }
        });
        
        // Then add static text labels from layout settings
        for (let i = 1; i <= 5; i++) {
            const staticKey = 'Instruction' + i;
            const savedLayout = layoutSettings.find(l => l.field_name === staticKey && l.static_text_value);
            if (savedLayout) {
                const layout = {
                    field_name: staticKey,
                    margin_top: parseFloat(savedLayout.margin_top || 2),
                    sequence: parseInt(savedLayout.sequence || elements.length),
                    text_align: savedLayout.text_align || 'left',
                    font_family: savedLayout.font_family || 'Tw Cen MT',
                    font_weight: savedLayout.font_weight || 'normal',
                    color: savedLayout.color || '#000000',
                    font_size: savedLayout.font_size ? parseFloat(savedLayout.font_size) : 3.70,
                    static_text_key: staticKey,
                    static_text_value: savedLayout.static_text_value || '',
                    preview_text: savedLayout.static_text_value || '',
                };
                elements.push(layout);
            }
        }
        
        // Sort by sequence
        elements.sort((a, b) => (a.sequence || 0) - (b.sequence || 0));
        console.log('Elements after rebuild:', elements);
    }

    // Initial build
    rebuildElementsFromVisibleFields();

    function renderPreview() {
        const preview = document.getElementById('badgePreview');
        preview.innerHTML = '';
        
        // Set fixed dimensions - always use configured badge size (exactly like PDF print - no padding)
        preview.style.width = badgeWidthPx + 'px';
        preview.style.height = badgeHeightPx + 'px';
        preview.style.minHeight = badgeHeightPx + 'px';
        preview.style.maxHeight = badgeHeightPx + 'px';
        preview.style.overflow = 'hidden'; // Clip content that exceeds badge boundaries
        preview.style.padding = '0'; // No padding - matches PDF print output exactly
        preview.style.margin = '0 auto'; // Center horizontally only
        preview.style.display = 'flex';
        preview.style.flexDirection = 'column';
        preview.style.boxSizing = 'border-box';

        elements.forEach((element, index) => {
            const div = document.createElement('div');
            div.className = 'preview-element' + (element.field_name === 'QRcode' ? ' qr-code' : '');
            div.dataset.index = index;
            div.dataset.field = element.field_name;

            // Add margin top (line spacing) - apply to all elements including the first
            const marginTop = (element.margin_top || 0) * mmToPx;
            div.style.marginTop = marginTop + 'px';
            
            div.style.textAlign = element.text_align;
            div.style.fontFamily = element.font_family;
            div.style.fontWeight = element.font_weight;
            div.style.color = element.color;
            div.style.boxSizing = 'border-box';
            div.style.padding = '0'; // No padding - matches PDF print output
            div.style.marginLeft = '0';
            div.style.marginRight = '0';
            div.style.marginBottom = '0';

            if (element.field_name === 'QRcode') {
                const qrWidth = (element.width || 20) * mmToPx;
                const qrHeight = (element.height || 20) * mmToPx;
                
                div.style.width = '100%';
                div.style.display = 'flex';
                div.style.justifyContent = element.text_align === 'center' ? 'center' : 
                                          element.text_align === 'right' ? 'flex-end' : 'flex-start';
                
                if (sampleQrCode && sampleQrCodeType === 'svg-data') {
                    const img = document.createElement('img');
                    img.src = sampleQrCode;
                    img.style.width = qrWidth + 'px';
                    img.style.height = qrHeight + 'px';
                    img.style.objectFit = 'contain';
                    img.style.display = 'block';
                    img.alt = 'QR Code';
                    div.appendChild(img);
                } else {
                    const placeholder = document.createElement('div');
                    placeholder.style.width = qrWidth + 'px';
                    placeholder.style.height = qrHeight + 'px';
                    placeholder.style.background = '#f3f4f6';
                    placeholder.style.border = '1px dashed #3b82f6';
                    placeholder.style.borderRadius = '4px';
                    placeholder.style.display = 'flex';
                    placeholder.style.alignItems = 'center';
                    placeholder.style.justifyContent = 'center';
                    placeholder.style.fontSize = '10px';
                    placeholder.style.color = '#6b7280';
                    placeholder.textContent = 'QR Code';
                    div.appendChild(placeholder);
                }
            } else {
                // Text elements - use preview_text if available, otherwise use default
                div.style.fontSize = (element.font_size || 3.70) * mmToPx + 'px';
                
                // Handle static text labels and Category
                let textContent;
                if (element.field_name && element.field_name.startsWith('Instruction')) {
                    textContent = element.static_text_value || element.preview_text || '';
                } else if (element.field_name === 'Category') {
                    textContent = element.preview_text || '{{ $categoryModel->Category ?? "VISITOR" }}';
                } else {
                    textContent = element.preview_text || getDefaultPreviewText(element.field_name);
                }
                
                div.textContent = textContent;
                // Allow wrapping similar to print, but clip at badge boundary
                div.style.whiteSpace = 'normal';
                div.style.wordBreak = 'break-word';
                div.style.overflow = 'hidden';
                div.style.maxWidth = '100%';
            }

            div.addEventListener('click', () => selectElementByIndex(index));
            preview.appendChild(div);
        });

        renderElementButtons();
    }

    function renderElementButtons() {
        const buttonsContainer = document.getElementById('elementButtons');
        buttonsContainer.innerHTML = '';

        // Sort by sequence for button display
        const sortedElements = [...elements].sort((a, b) => (a.sequence || 0) - (b.sequence || 0));

        sortedElements.forEach((element) => {
            const index = elements.findIndex(el => el.field_name === element.field_name);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'element-btn' + (selectedElement && selectedElement.index === index ? ' active' : '');
            btn.textContent = element.field_name;
            btn.dataset.index = index;
            btn.addEventListener('click', () => selectElementByIndex(index));
            buttonsContainer.appendChild(btn);
        });
    }

    function selectElementByIndex(index) {
        selectedElement = { element: elements[index], index: index };
        showControls(elements[index], index);
        renderElementButtons();
        
        // Highlight in preview
        document.querySelectorAll('.preview-element').forEach(el => el.classList.remove('selected'));
        const previewElement = document.querySelector(`.preview-element[data-index="${index}"]`);
        if (previewElement) {
            previewElement.classList.add('selected');
        }
    }

    function showControls(element, index) {
        const isQR = element.field_name === 'QRcode';
        
        const controlsHTML = `
            <div class="control-group">
                <label>Field Name</label>
                <input type="text" value="${element.field_name}" disabled style="background: #f3f4f6;">
            </div>
            <div class="control-group">
                <label>Line Spacing (Margin Top in mm)</label>
                <input type="number" id="marginTop" value="${parseFloat(element.margin_top || 0).toFixed(2)}" step="0.1" min="0">
            </div>
            <div class="control-group">
                <label>Sequence (Print Order)</label>
                <input type="number" id="sequence" value="${element.sequence || index}" min="0" step="1">
            </div>
            <div class="control-group">
                <label>Alignment</label>
                <select id="textAlign">
                    <option value="left" ${element.text_align === 'left' ? 'selected' : ''}>Left</option>
                    <option value="center" ${element.text_align === 'center' ? 'selected' : ''}>Center</option>
                    <option value="right" ${element.text_align === 'right' ? 'selected' : ''}>Right</option>
                </select>
            </div>
            <div class="control-group">
                <label>Font Family</label>
                <select id="fontFamily">
                    <option value="Tw Cen MT" ${element.font_family === 'Tw Cen MT' ? 'selected' : ''}>Tw Cen MT</option>
                    <option value="Roboto" ${element.font_family === 'Roboto' ? 'selected' : ''}>Roboto (PDF-safe, install via font:load-roboto)</option>
                    <option value="Comfortaa" ${element.font_family === 'Comfortaa' || !element.font_family ? 'selected' : ''}>Comfortaa</option>
                    <option value="Arial" ${element.font_family === 'Arial' ? 'selected' : ''}>Arial</option>
                    <option value="Helvetica" ${element.font_family === 'Helvetica' ? 'selected' : ''}>Helvetica</option>
                    <option value="Times New Roman" ${element.font_family === 'Times New Roman' ? 'selected' : ''}>Times New Roman</option>
                    <option value="Georgia" ${element.font_family === 'Georgia' ? 'selected' : ''}>Georgia</option>
                    <option value="Verdana" ${element.font_family === 'Verdana' ? 'selected' : ''}>Verdana</option>
                    <option value="Courier New" ${element.font_family === 'Courier New' ? 'selected' : ''}>Courier New</option>
                    <option value="Trebuchet MS" ${element.font_family === 'Trebuchet MS' ? 'selected' : ''}>Trebuchet MS</option>
                    <option value="Impact" ${element.font_family === 'Impact' ? 'selected' : ''}>Impact</option>
                    <option value="Tahoma" ${element.font_family === 'Tahoma' ? 'selected' : ''}>Tahoma</option>
                    <option value="Lucida Sans" ${element.font_family === 'Lucida Sans' ? 'selected' : ''}>Lucida Sans</option>
                    <option value="Palatino" ${element.font_family === 'Palatino' ? 'selected' : ''}>Palatino</option>
                    <option value="Garamond" ${element.font_family === 'Garamond' ? 'selected' : ''}>Garamond</option>
                    <option value="Bookman" ${element.font_family === 'Bookman' ? 'selected' : ''}>Bookman</option>
                    <option value="Comic Sans MS" ${element.font_family === 'Comic Sans MS' ? 'selected' : ''}>Comic Sans MS</option>
                </select>
            </div>
            ${!isQR ? `
            <div class="control-group">
                <label>Preview Text (for testing)</label>
                <input type="text" id="previewText" value="${element.preview_text || getDefaultPreviewText(element.field_name)}" placeholder="Enter text to preview">
            </div>
            ` : ''}
            <div class="control-group">
                <label>Font Weight</label>
                <select id="fontWeight">
                    <option value="normal" ${element.font_weight === 'normal' ? 'selected' : ''}>Normal</option>
                    <option value="bold" ${element.font_weight === 'bold' ? 'selected' : ''}>Bold</option>
                </select>
            </div>
            <div class="control-group">
                <label>Color</label>
                <input type="color" id="color" value="${element.color || '#000000'}">
            </div>
            ${!isQR ? `
            <div class="control-group">
                <label>Font Size (mm)</label>
                <input type="number" id="fontSize" value="${parseFloat(element.font_size || 3.70).toFixed(2)}" step="0.1" min="1" max="50">
            </div>
            ` : `
            <div class="control-group">
                <label>QR Code Width (mm)</label>
                <input type="number" id="qrWidth" value="${parseFloat(element.width || 20).toFixed(2)}" step="0.1" min="5" max="100">
            </div>
            <div class="control-group">
                <label>QR Code Height (mm)</label>
                <input type="number" id="qrHeight" value="${parseFloat(element.height || 20).toFixed(2)}" step="0.1" min="5" max="100">
            </div>
            `}
        `;

        // Store current input state before recreating
        let activeInputId = null;
        let activeInputValue = null;
        let activeInputSelectionStart = null;
        let activeInputSelectionEnd = null;
        
        const currentActive = document.activeElement;
        if (currentActive && currentActive.id && currentActive.tagName === 'INPUT') {
            activeInputId = currentActive.id;
            activeInputValue = currentActive.value;
            if (currentActive.setSelectionRange) {
                activeInputSelectionStart = currentActive.selectionStart;
                activeInputSelectionEnd = currentActive.selectionEnd;
            }
        }

        document.getElementById('controlsContainer').innerHTML = controlsHTML;

        // Add event listeners for instant preview update (without losing focus)
        document.getElementById('marginTop').addEventListener('input', (e) => {
            elements[index].margin_top = parseFloat(e.target.value) || 0;
            renderPreview();
            renderElementButtons();
        });

        document.getElementById('sequence').addEventListener('input', (e) => {
            elements[index].sequence = parseInt(e.target.value) || 0;
            elements.sort((a, b) => (a.sequence || 0) - (b.sequence || 0));
            renderPreview();
            const newIndex = elements.findIndex(el => el.field_name === element.field_name);
            if (newIndex !== -1) {
                selectedElement = { element: elements[newIndex], index: newIndex };
                renderElementButtons();
            }
        });

        document.getElementById('textAlign').addEventListener('change', (e) => {
            elements[index].text_align = e.target.value;
            renderPreview();
            renderElementButtons();
        });

        document.getElementById('fontFamily').addEventListener('change', (e) => {
            elements[index].font_family = e.target.value;
            renderPreview();
            renderElementButtons();
        });

        if (!isQR) {
            document.getElementById('previewText').addEventListener('input', (e) => {
                elements[index].preview_text = e.target.value;
                renderPreview();
                renderElementButtons();
            });
        }

        document.getElementById('fontWeight').addEventListener('change', (e) => {
            elements[index].font_weight = e.target.value;
            renderPreview();
            renderElementButtons();
        });

        document.getElementById('color').addEventListener('input', (e) => {
            elements[index].color = e.target.value;
            renderPreview();
            renderElementButtons();
        });

        if (!isQR) {
            document.getElementById('fontSize').addEventListener('input', (e) => {
                elements[index].font_size = parseFloat(e.target.value) || 3.70;
                renderPreview();
                renderElementButtons();
            });
        } else {
            document.getElementById('qrWidth').addEventListener('input', (e) => {
                elements[index].width = parseFloat(e.target.value) || 20;
                renderPreview();
                renderElementButtons();
            });

            document.getElementById('qrHeight').addEventListener('input', (e) => {
                elements[index].height = parseFloat(e.target.value) || 20;
                renderPreview();
                renderElementButtons();
            });
        }

        // Restore focus and cursor position if there was an active input
        if (activeInputId) {
            setTimeout(() => {
                const restoredInput = document.getElementById(activeInputId);
                if (restoredInput) {
                    // Restore value if it changed
                    if (restoredInput.value !== activeInputValue) {
                        restoredInput.value = activeInputValue;
                    }
                    restoredInput.focus();
                    // Restore cursor position for text/number inputs
                    if (restoredInput.setSelectionRange && activeInputSelectionStart !== null) {
                        try {
                            restoredInput.setSelectionRange(activeInputSelectionStart, activeInputSelectionEnd);
                        } catch (e) {
                            // Some inputs don't support setSelectionRange
                            restoredInput.setSelectionRange(restoredInput.value.length, restoredInput.value.length);
                        }
                    }
                }
            }, 10);
        }
    }

    // Save layout
    document.getElementById('layoutForm').addEventListener('submit', (e) => {
        e.preventDefault();
        
        const layouts = elements.map((element, index) => {
            const layout = {
                field_name: element.field_name,
                margin_top: parseFloat(element.margin_top || 0),
                sequence: element.sequence !== undefined ? element.sequence : index,
                text_align: element.text_align,
                font_family: element.font_family,
                font_weight: element.font_weight,
                color: element.color,
            };
            
            // Add static text fields if this is a static text element
            if (element.field_name && element.field_name.startsWith('Instruction')) {
                layout.static_text_key = element.field_name;
                layout.static_text_value = element.static_text_value || element.preview_text || '';
            }
            
            if (element.field_name === 'QRcode') {
                layout.width = parseFloat(element.width || 20);
                layout.height = parseFloat(element.height || 20);
            } else {
                layout.font_size = parseFloat(element.font_size || 3.70);
            }
            
            return layout;
        });

        document.getElementById('layoutsInput').value = JSON.stringify(layouts);
        e.target.submit();
    });

    // Initial render
    console.log('Calling renderPreview with', elements.length, 'elements');
    if (elements.length > 0) {
        renderPreview();
    } else {
        console.error('No elements to render!');
        document.getElementById('badgePreview').innerHTML = '<p style="color: red; padding: 20px;">No elements found. Please configure display settings first.</p>';
        document.getElementById('elementsList').innerHTML = '<p style="color: #6b7280; padding: 10px;">No elements available. Please configure badge display settings first.</p>';
    }
</script>
@endpush
@endsection
