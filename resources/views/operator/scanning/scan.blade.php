@extends('layouts.app')

@section('title', 'Scanning')

@push('styles')
<style>
    #scanning-container {
        min-height: 75vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
        transition: background-color 0.5s ease;
        position: relative;
    }

    #scan-counter {
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        z-index: 1000;
        font-size: 18px;
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }

    #scan-counter .label {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 5px;
    }

    #scan-counter .count {
        font-size: 32px;
        color: #10b981;
    }

    #scan-counter .stats-row {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid rgba(255,255,255,0.3);
        font-size: 12px;
    }

    #scan-counter .stat-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    #scan-counter .stat-item:last-child {
        margin-bottom: 0;
    }

    #scan-counter .stat-label {
        opacity: 0.9;
    }

    #scan-counter .stat-value {
        font-weight: bold;
    }

    #scan-counter .stat-approved {
        color: #10b981;
    }

    #scan-counter .stat-rejected {
        color: #ef4444;
    }
    #connection-status {
        margin-top: 6px;
        font-size: 12px;
    }
    #connection-status.online {
        color: #10b981;
    }
    #connection-status.offline {
        color: #fbbf24;
    }
    #pending-uploads-text {
        font-size: 12px;
        margin-top: 2px;
    }
    #sync-offline-btn {
        margin-top: 6px;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 4px;
        border: none;
        background-color: #3b82f6;
        color: #ffffff;
        cursor: pointer;
        width: 100%;
    }
    #sync-offline-btn:disabled {
        opacity: 0.6;
        cursor: default;
    }

    #input-container {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1001;
        width: 90%;
        max-width: 600px;
    }
    #input-container.hidden-by-mode {
        display: none !important;
    }

    #regid {
        width: 100%;
        padding: 20px;
        font-size: 24px;
        text-align: center;
        letter-spacing: 3px;
        background: rgba(255, 255, 255, 0.95);
        border: 3px solid #3b82f6;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    #result-container {
        display: none;
        text-align: center;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 999;
        width: 100%;
        padding: 20px;
        pointer-events: none;
    }
    #qr-reader {
        transition: opacity 0.2s ease;
    }
    #qr-reader.hidden-by-mode {
        display: none !important;
    }
    #qr-reader.dimmed-by-result {
        opacity: 0;
        visibility: hidden;
    }

    #result-icon {
        font-size: 150px;
        margin-bottom: 40px;
        font-weight: bold;
        line-height: 1;
    }

    #result-name {
        font-size: 36px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    #result-category {
        font-size: 28px;
        opacity: 0.95;
        margin-bottom: 15px;
    }

    #result-message {
        font-size: 20px;
        opacity: 0.9;
        margin-top: 20px;
    }

    @media screen and (max-width: 768px) {
        #scanning-container {
            padding: 10px;
            min-height: 100vh;
        }

        #scan-counter {
            top: 10px;
            right: 10px;
            padding: 10px 15px;
            font-size: 14px;
        }

        #scan-counter .label {
            font-size: 12px;
        }

        #scan-counter .count {
            font-size: 24px;
        }

        #scan-counter .stats-row {
            font-size: 11px;
            margin-top: 6px;
            padding-top: 6px;
        }

        #input-container {
            bottom: 20px;
            width: 95%;
            left: 50%;
            transform: translateX(-50%);
        }

        #regid {
            padding: 15px;
            font-size: 20px;
            letter-spacing: 2px;
        }

        #result-icon {
            font-size: 100px;
            margin-bottom: 20px;
        }

        #result-name {
            font-size: 24px;
            margin-bottom: 15px;
        }

        #result-category {
            font-size: 20px;
            margin-bottom: 10px;
        }

        #result-message {
            font-size: 16px;
            margin-top: 15px;
        }
    }

    @media screen and (max-width: 480px) {
        #scan-counter {
            top: 5px;
            right: 5px;
            padding: 8px 12px;
            font-size: 12px;
        }

        #scan-counter .count {
            font-size: 20px;
        }

        #scan-counter .stats-row {
            font-size: 10px;
            margin-top: 5px;
            padding-top: 5px;
        }

        #input-container {
            bottom: 15px;
            width: 98%;
        }

        #regid {
            padding: 12px;
            font-size: 18px;
            letter-spacing: 1px;
        }

        #result-icon {
            font-size: 80px;
        }

        #result-name {
            font-size: 20px;
        }

        #result-category {
            font-size: 18px;
        }
    }
</style>
@endpush

@section('content')
<div id="scanning-container">
    <!-- Scan Counter - Always visible at top -->
    <div id="scan-counter">
        <div class="label">Today's Scans</div>
        <div id="scan-count-value" class="count">{{ $todayScanCount }}</div>
        <div class="stats-row">
            <div class="stat-item">
                <span class="stat-label">Approved:</span>
                <span id="approved-count-value" class="stat-value stat-approved">{{ $todayApprovedCount }}</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Rejected:</span>
                <span id="rejected-count-value" class="stat-value stat-rejected">{{ $todayRejectedCount }}</span>
            </div>
        </div>
        <div id="connection-status" class="online">Status: Online</div>
        <div id="pending-uploads-text">Pending Uploads: <span id="pending-uploads-count">0</span></div>
        <button id="sync-offline-btn" type="button">Sync Offline Scans</button>
    </div>

    <!-- Input Field - Always visible and accessible -->
    <div id="input-container" class="{{ ($scanningType ?? 'camera') === 'camera' ? 'hidden-by-mode' : '' }}">
        <input type="text" 
               id="regid" 
               name="regid" 
               autofocus 
               required 
               autocomplete="off"
               class="form-control" 
               placeholder="Scan Badge Here...">
    </div>

    <!-- QR Scanner Region -->
    <div id="qr-reader" class="{{ ($scanningType ?? 'camera') === 'device' ? 'hidden-by-mode' : '' }}" style="width: 100%; max-width: 520px; margin-top: 80px;"></div>

    <!-- Form Card - Hidden when showing result -->
    <div id="form-card" class="card" style="max-width: 600px; width: 100%; text-align: center; position: relative; z-index: 10; transition: opacity 0.3s ease;">
        <div class="card-header">
            <h1 class="card-title">Scanning - {{ $location->name }}</h1>
        </div>

        <div style="padding: 30px;">
            <div style="margin-top: 30px; display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('operator.scanning.clear-location') }}" class="btn btn-secondary" style="flex: 1; min-width: 150px;">Change Location</a>
                <a href="{{ route('operator.home') }}" class="btn btn-secondary" style="flex: 1; min-width: 150px;">Home</a>
            </div>
        </div>
    </div>

    <!-- Result Container - Shown when scanning -->
    <div id="result-container">
        <div id="result-icon"></div>
        <div id="result-name"></div>
        <div id="result-category"></div>
        <div id="result-message"></div>
    </div>
</div>

<!-- Sounds will be generated via JavaScript -->

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure html5-qrcode is loaded
    const html5QrcodeReady = new Promise((resolve) => {
        if (window.Html5Qrcode) {
            resolve();
            return;
        }
        const checkInterval = setInterval(() => {
            if (window.Html5Qrcode) {
                clearInterval(checkInterval);
                resolve();
            }
        }, 50);
    });

    const regidInput = document.getElementById('regid');
    const formCard = document.getElementById('form-card');
    const resultContainer = document.getElementById('result-container');
    const resultIcon = document.getElementById('result-icon');
    const resultName = document.getElementById('result-name');
    const resultCategory = document.getElementById('result-category');
    const resultMessage = document.getElementById('result-message');
    const scanningContainer = document.getElementById('scanning-container');
    const scanCountValue = document.getElementById('scan-count-value');
    const approvedCountValue = document.getElementById('approved-count-value');
    const rejectedCountValue = document.getElementById('rejected-count-value');
    const connectionStatusEl = document.getElementById('connection-status');
    const pendingUploadsTextEl = document.getElementById('pending-uploads-text');
    const pendingUploadsCountEl = document.getElementById('pending-uploads-count');
    const syncOfflineBtn = document.getElementById('sync-offline-btn');
    const qrRegionId = 'qr-reader';
    const scanningType = @json($scanningType ?? 'camera');
    const inputContainer = document.getElementById('input-container');
    const qrReaderEl = document.getElementById('qr-reader');
    
    // Queue system for handling rapid scans
    let scanQueue = [];
    let isProcessing = false;
    let resetTimeout = null;
    let lastProcessedRegid = null; // Prevent duplicate immediate scans
    let lastProcessedTime = 0; // Track time of last processed scan
    let lastScannedCode = null;
    let lastScannedTime = 0;
    const DUPLICATE_WINDOW_MS = 1200; // Prevent same regid within 1.2 seconds

    // IndexedDB setup for offline scans
    let dbInstance = null;

    function openIndexedDb() {
        return new Promise((resolve, reject) => {
            if (dbInstance) {
                resolve(dbInstance);
                return;
            }
            const request = window.indexedDB.open('lead_scanner_db', 1);
            request.onerror = () => {
                console.error('IndexedDB open error');
                reject(request.error);
            };
            request.onsuccess = () => {
                dbInstance = request.result;
                resolve(dbInstance);
            };
            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                if (!db.objectStoreNames.contains('offline_scans')) {
                    const store = db.createObjectStore('offline_scans', {
                        keyPath: 'id',
                        autoIncrement: true
                    });
                    store.createIndex('synced', 'synced', { unique: false });
                }
            };
        });
    }

    function getDeviceId() {
        try {
            const key = 'lead_scanner_device_id';
            let id = localStorage.getItem(key);
            if (!id) {
                id = 'dev-' + Math.random().toString(36).substring(2) + Date.now().toString(36);
                localStorage.setItem(key, id);
            }
            return id;
        } catch (e) {
            return null;
        }
    }

    async function saveOfflineScan(regid) {
        try {
            const db = await openIndexedDb();
            return await new Promise((resolve, reject) => {
                const tx = db.transaction('offline_scans', 'readwrite');
                const store = tx.objectStore('offline_scans');
                const record = {
                    regid: regid,
                    scan_time: new Date().toISOString(),
                    synced: false,
                    device_id: getDeviceId()
                };
                const request = store.add(record);
                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(request.error);
            });
        } catch (e) {
            console.error('Failed to save offline scan', e);
        }
    }

    async function getOfflineScans(onlyUnsynced = false) {
        try {
            const db = await openIndexedDb();
            return await new Promise((resolve, reject) => {
                const tx = db.transaction('offline_scans', 'readonly');
                const store = tx.objectStore('offline_scans');
                const result = [];
                let request;
                if (onlyUnsynced) {
                    try {
                        const index = store.index('synced');
                        request = index.openCursor(IDBKeyRange.only(false));
                    } catch (e) {
                        // Fallback for browsers/old DB where synced index is missing.
                        request = store.openCursor();
                    }
                } else {
                    request = store.openCursor();
                }
                request.onerror = () => reject(request.error);
                request.onsuccess = (event) => {
                    const cursor = event.target.result;
                    if (cursor) {
                        if (!onlyUnsynced || !cursor.value.synced) {
                            result.push(cursor.value);
                        }
                        cursor.continue();
                    } else {
                        resolve(result);
                    }
                };
            });
        } catch (e) {
            console.error('Failed to get offline scans', e);
            return [];
        }
    }

    async function markScanSynced(id) {
        try {
            const db = await openIndexedDb();
            return await new Promise((resolve, reject) => {
                const tx = db.transaction('offline_scans', 'readwrite');
                const store = tx.objectStore('offline_scans');
                const getReq = store.get(id);
                getReq.onerror = () => reject(getReq.error);
                getReq.onsuccess = () => {
                    const record = getReq.result;
                    if (!record) {
                        resolve(false);
                        return;
                    }
                    record.synced = true;
                    const putReq = store.put(record);
                    putReq.onsuccess = () => resolve(true);
                    putReq.onerror = () => reject(putReq.error);
                };
            });
        } catch (e) {
            console.error('Failed to mark scan synced', e);
            return false;
        }
    }

    async function updatePendingUploadsCount() {
        const unsynced = await getOfflineScans(true);
        if (pendingUploadsCountEl) {
            pendingUploadsCountEl.textContent = unsynced.length.toString();
        }
        if (syncOfflineBtn) {
            syncOfflineBtn.disabled = unsynced.length === 0 || !navigator.onLine;
        }
    }

    function updateConnectionStatus() {
        if (!connectionStatusEl) return;
        if (navigator.onLine) {
            connectionStatusEl.textContent = 'Status: Online';
            connectionStatusEl.classList.remove('offline');
            connectionStatusEl.classList.add('online');
        } else {
            connectionStatusEl.textContent = 'Status: Offline';
            connectionStatusEl.classList.remove('online');
            connectionStatusEl.classList.add('offline');
        }
        updatePendingUploadsCount();
    }

    // Function to play success sound - Pleasant ascending chime (shorter for fast scanning)
    function playSuccessSound() {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const now = audioContext.currentTime;
        
        // Shorter chime for fast scanning
        const osc1 = audioContext.createOscillator();
        const gain1 = audioContext.createGain();
        osc1.type = 'sine';
        osc1.frequency.value = 659.25; // E5 - higher pitch for quick feedback
        gain1.gain.setValueAtTime(0, now);
        gain1.gain.linearRampToValueAtTime(0.5, now + 0.02);
        gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.2);
        osc1.connect(gain1);
        gain1.connect(audioContext.destination);
        osc1.start(now);
        osc1.stop(now + 0.2);
    }

    // Function to play error sound - Harsh descending buzzer (shorter for fast scanning)
    function playErrorSound() {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const now = audioContext.currentTime;
        
        // Shorter buzzer for fast scanning
        const osc1 = audioContext.createOscillator();
        const gain1 = audioContext.createGain();
        osc1.type = 'square';
        osc1.frequency.value = 200;
        gain1.gain.setValueAtTime(0, now);
        gain1.gain.linearRampToValueAtTime(0.5, now + 0.02);
        gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.25);
        osc1.connect(gain1);
        gain1.connect(audioContext.destination);
        osc1.start(now);
        osc1.stop(now + 0.25);
    }

    // Function to reset form and prepare for next scan
    function setResultOverlayState(showOverlay) {
        if (!qrReaderEl) return;
        if (showOverlay) {
            qrReaderEl.classList.add('dimmed-by-result');
        } else {
            qrReaderEl.classList.remove('dimmed-by-result');
        }
    }

    function applyScanningTypeUi() {
        if (scanningType === 'camera') {
            if (inputContainer) inputContainer.classList.add('hidden-by-mode');
            if (qrReaderEl) qrReaderEl.classList.remove('hidden-by-mode');
            return;
        }

        if (inputContainer) inputContainer.classList.remove('hidden-by-mode');
        if (qrReaderEl) qrReaderEl.classList.add('hidden-by-mode');
    }

    function resetForm() {
        if (resetTimeout) {
            clearTimeout(resetTimeout);
            resetTimeout = null;
        }
        
        scanningContainer.style.backgroundColor = '#ffffff';
        resultContainer.style.display = 'none';
        setResultOverlayState(false);
        formCard.style.display = 'block';
        formCard.style.visibility = 'visible';
        formCard.style.opacity = '1';
        
        // Process next item in queue if available
        processNextInQueue();
    }

    // Function to add online scan to queue
    function queueOnlineScan(regid) {
        if (!regid || regid.trim() === '') return;
        
        const trimmedRegid = regid.trim();
        
        // Prevent duplicate immediate scans (same regid within 100ms)
        if (trimmedRegid === lastProcessedRegid) {
            const now = Date.now();
            if (now - lastProcessedTime < 100) {
                return; // Skip duplicate
            }
        }
        
        // Add to queue
        scanQueue.push(trimmedRegid);
        
        // Clear input immediately to accept next scan
        regidInput.value = '';
        
        // Start processing if not already processing
        if (!isProcessing) {
            processNextInQueue();
        }
    }

    // Function to process next item in queue
    async function processNextInQueue() {
        // If already processing or queue is empty, return
        if (isProcessing || scanQueue.length === 0) {
            isProcessing = false;
            return;
        }
        
        // Get next regid from queue
        const regid = scanQueue.shift();
        if (!regid) {
            isProcessing = false;
            processNextInQueue(); // Try next item
            return;
        }
        
        isProcessing = true;
        lastProcessedRegid = regid;
        lastProcessedTime = Date.now();
        
        // Clear any pending reset
        if (resetTimeout) {
            clearTimeout(resetTimeout);
            resetTimeout = null;
        }

        // Hide form and show result container immediately
        formCard.style.display = 'none';
        formCard.style.visibility = 'hidden';
        formCard.style.opacity = '0';
        resultContainer.style.display = 'block';
        setResultOverlayState(true);
        scanningContainer.style.backgroundColor = '#ffffff';

        try {
            const response = await fetch('{{ route("operator.scanning.check-user") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ regid: regid })
            });

            const data = await response.json();

            if (data.success) {
                // Update scan counters
                if (data.today_scan_count !== undefined) {
                    scanCountValue.textContent = data.today_scan_count;
                }
                if (data.today_approved_count !== undefined) {
                    approvedCountValue.textContent = data.today_approved_count;
                }
                if (data.today_rejected_count !== undefined) {
                    rejectedCountValue.textContent = data.today_rejected_count;
                }

                // Show result - icon, name, category, and message
                resultName.textContent = data.name || 'Unknown';
                resultCategory.textContent = data.category || '';
                resultMessage.textContent = '';

                if (data.allowed) {
                    // Success - Green background
                    scanningContainer.style.backgroundColor = '#10b981';
                    resultIcon.textContent = '✓';
                    resultIcon.style.color = '#ffffff';
                    resultName.style.color = '#ffffff';
                    resultCategory.style.color = '#ffffff';
                    resultMessage.style.color = '#ffffff';
                    
                    // Play success sound
                    playSuccessSound();
                } else {
                    // Error - Red background
                    scanningContainer.style.backgroundColor = '#ef4444';
                    resultIcon.textContent = '✗';
                    resultIcon.style.color = '#ffffff';
                    resultName.style.color = '#ffffff';
                    resultCategory.style.color = '#ffffff';
                    resultMessage.style.color = '#ffffff';
                    
                    // Show "Already Scanned" message if applicable
                    if (data.already_scanned && data.previous_scan_time) {
                        resultMessage.textContent = 'Already Scanned\n' + data.previous_scan_time;
                    }
                    
                    // Play error sound
                    playErrorSound();
                }

                // Reset after very short time (200ms) for ultra-fast scanning
                resetTimeout = setTimeout(() => {
                    resetForm();
                }, 200);
            } else {
                // User not found - show error
                scanningContainer.style.backgroundColor = '#ef4444';
                resultIcon.textContent = '✗';
                resultIcon.style.color = '#ffffff';
                resultName.textContent = 'User Not Found';
                resultCategory.textContent = '';
                resultMessage.textContent = '';
                
                playErrorSound();
                
                // Reset after very short time
                resetTimeout = setTimeout(() => {
                    resetForm();
                }, 200);
            }
        } catch (error) {
            console.error('Error:', error);
            // Continue processing queue even on error
            resetForm();
        }
    }

    function extractRegidFromText(text) {
        if (!text) return '';
        const trimmed = text.trim();
        try {
            const url = new URL(trimmed);
            const param = url.searchParams.get('regid') || url.searchParams.get('RegID');
            if (param) return param;
        } catch (e) {
            // Not a URL, fall back to raw text
        }
        return trimmed;
    }

    async function handleDetectedCode(rawText) {
        const regid = extractRegidFromText(rawText);
        if (!regid) return;

        const now = Date.now();
        if (regid === lastScannedCode && now - lastScannedTime < DUPLICATE_WINDOW_MS) {
            return;
        }
        lastScannedCode = regid;
        lastScannedTime = now;

        if (navigator.onLine) {
            queueOnlineScan(regid);
        } else {
            await saveOfflineScan(regid);
            if (pendingUploadsTextEl) {
                pendingUploadsTextEl.classList.add('text-warning');
            }
            // Show brief offline saved feedback (amber background)
            formCard.style.display = 'none';
            formCard.style.visibility = 'hidden';
            formCard.style.opacity = '0';
            resultContainer.style.display = 'block';
            setResultOverlayState(true);
            scanningContainer.style.backgroundColor = '#fbbf24';
            resultIcon.textContent = '⏺';
            resultIcon.style.color = '#000000';
            resultName.textContent = regid;
            resultName.style.color = '#000000';
            resultCategory.textContent = 'Saved Offline';
            resultCategory.style.color = '#000000';
            resultMessage.textContent = 'Will sync when online';
            resultMessage.style.color = '#000000';
            await updatePendingUploadsCount();
            resetTimeout = setTimeout(() => {
                resetForm();
            }, 400);
        }
    }

    async function syncOfflineScans() {
        if (!navigator.onLine) {
            updateConnectionStatus();
            return;
        }
        if (syncOfflineBtn) {
            syncOfflineBtn.disabled = true;
        }
        const unsynced = await getOfflineScans(true);
        for (const record of unsynced) {
            try {
                const response = await fetch('{{ route("operator.scanning.check-user") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ regid: record.regid })
                });
                if (!response.ok) {
                    continue;
                }
                const data = await response.json();
                if (data) {
                    await markScanSynced(record.id);
                }
            } catch (e) {
                console.error('Failed to sync offline scan', e);
            }
        }
        await updatePendingUploadsCount();
        if (syncOfflineBtn) {
            syncOfflineBtn.disabled = !navigator.onLine || (pendingUploadsCountEl && pendingUploadsCountEl.textContent !== '0');
        }
    }

    if (regidInput) {
        // Auto-submit on Enter key (barcode scanners send Enter after code)
        regidInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const regid = regidInput.value;
                if (regid) {
                    handleDetectedCode(regid);
                }
            }
        });

        // Auto-submit when input is complete (for barcode scanners that don't send Enter)
        let inputTimeout = null;
        regidInput.addEventListener('input', function() {
            if (inputTimeout) {
                clearTimeout(inputTimeout);
            }
            if (regidInput.value.length >= 3) {
                inputTimeout = setTimeout(() => {
                    const regid = regidInput.value;
                    if (regid) {
                        handleDetectedCode(regid);
                    }
                }, 50);
            }
        });

        // Keep input always focused - only when device mode is active
        if (scanningType === 'device') {
            regidInput.focus();
            setInterval(() => {
                if (document.activeElement !== regidInput) {
                    regidInput.focus();
                }
            }, 80);
        }

        // Re-focus when clicking anywhere on the page (except links)
        document.addEventListener('click', function(e) {
            if (scanningType !== 'device') return;
            if (!e.target.closest('a') && !e.target.closest('#regid')) {
                setTimeout(() => {
                    regidInput.focus();
                }, 10);
            }
        });

        regidInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        regidInput.addEventListener('paste', function() {
            setTimeout(() => {
                const regid = regidInput.value;
                if (regid && regid.length >= 3) {
                    handleDetectedCode(regid);
                }
            }, 10);
        });
    }

    if (syncOfflineBtn) {
        syncOfflineBtn.addEventListener('click', function() {
            syncOfflineScans();
        });
    }

    window.addEventListener('online', function() {
        updateConnectionStatus();
        setTimeout(() => {
            syncOfflineScans();
        }, 700);
    });
    window.addEventListener('offline', function() {
        updateConnectionStatus();
    });

    updateConnectionStatus();
    applyScanningTypeUi();
    if (scanningType === 'camera') {
    html5QrcodeReady.then(() => {
        try {
            const html5QrCode = new Html5Qrcode(qrRegionId);
            Html5Qrcode.getCameras().then((devices) => {
                if (!devices || devices.length === 0) {
                    return;
                }
                let cameraId = devices[0].id;
                const backCam = devices.find(d => /back|rear|environment/i.test(d.label));
                if (backCam) {
                    cameraId = backCam.id;
                }
                html5QrCode.start(
                    cameraId,
                    {
                        fps: 18,
                        disableFlip: true,
                        experimentalFeatures: {
                            useBarCodeDetectorIfSupported: true,
                        },
                        qrbox: function(viewportWidth, viewportHeight) {
                            const minEdge = Math.min(viewportWidth, viewportHeight);
                            const boxSize = Math.floor(minEdge * 0.86);
                            return { width: boxSize, height: boxSize };
                        }
                    },
                    (decodedText, decodedResult) => {
                        // Do not drop reads while processing; queue system handles sequencing.
                        handleDetectedCode(decodedText);
                    },
                    (errorMessage) => {
                        // Ignore continuous scan errors to keep it lightweight
                    }
                );
            }).catch(err => {
                console.error('Camera access error', err);
            });

            window.addEventListener('beforeunload', function() {
                try {
                    html5QrCode.stop().catch(() => {});
                    html5QrCode.clear().catch(() => {});
                } catch (e) {
                    // ignore
                }
            });
        } catch (e) {
            console.error('Failed to initialize QR scanner', e);
        }
    });
    }
});
</script>
@endpush
@endsection
