<div id="lead-header">
    <div id="lead-status-bar">
        <div>
            <span id="lead-connection-status" class="online">Status: Online</span>
            <span id="lead-pending-count"> | Pending: <span id="lead-pending-count-value">0</span></span>
        </div>
        <button id="lead-sync-btn" type="button">Sync</button>
    </div>
    <div id="lead-sync-progress" style="margin-top:6px;font-size:12px;color:#475569;"></div>
</div>

<div id="qr-reader" style="margin-top:10px;"></div>

<div id="lead-result-card" style="margin-top:10px;">
    <div id="lead-result-title">Waiting for scan…</div>
    <div id="lead-result-body"></div>
</div>

<div id="fallback-input-container" style="margin-top:10px;">
    <div style="display:flex;gap:8px;align-items:center;">
        <input type="text"
               id="lead-regid-input"
               class="form-control"
               autocomplete="off"
               placeholder="Type / paste RegID here">
        <button type="button" id="manual-submit-btn" class="btn btn-primary">Submit</button>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const qrRegionId = 'qr-reader';
    const connectionStatusEl = document.getElementById('lead-connection-status');
    const pendingCountValueEl = document.getElementById('lead-pending-count-value');
    const syncBtn = document.getElementById('lead-sync-btn');
    const syncProgressEl = document.getElementById('lead-sync-progress');
    const resultCardEl = document.getElementById('lead-result-card');
    const resultTitleEl = document.getElementById('lead-result-title');
    const resultBodyEl = document.getElementById('lead-result-body');
    const regidInput = document.getElementById('lead-regid-input');
    const metaModal = document.getElementById('lead-meta-modal');
    const metaSaveBtn = document.getElementById('lead-meta-save-btn');
    const metaSkipBtn = document.getElementById('lead-meta-skip-btn');
    const metaComments = document.getElementById('lead-meta-comments');
    const manualSubmitBtn = document.getElementById('manual-submit-btn');
    const recentNoData = document.getElementById('recent-no-data');
    const recentTableWrap = document.getElementById('recent-table-wrap');
    const recentTbody = document.getElementById('recent-scans-tbody');
    const totalLeadsCountEl = document.getElementById('total-leads-count');

    let dbInstance = null;
    let lastScannedCode = null;
    let lastScannedTime = 0;
    const DUPLICATE_WINDOW_MS = 1200;
    let pendingScan = null;
    let scannerInstance = null;
    let selectedCameraId = null;
    let scannerPausedByModal = false;
    let cameraInitInProgress = false;
    let cameraInitAttempts = 0;
    const MAX_CAMERA_INIT_RETRIES = 6;

    const html5QrcodeReady = new Promise((resolve) => {
        if (window.Html5Qrcode) {
            resolve();
            return;
        }
        const interval = setInterval(() => {
            if (window.Html5Qrcode) {
                clearInterval(interval);
                resolve();
            }
        }, 50);
    });

    function openIndexedDb() {
        return new Promise((resolve, reject) => {
            if (dbInstance) {
                resolve(dbInstance);
                return;
            }
            const request = window.indexedDB.open('lead_scanner_db', 1);
            request.onerror = () => reject(request.error);
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

    async function saveOfflineScan(regid, scanTimeIso, leadType = null, comments = null) {
        try {
            const db = await openIndexedDb();
            return await new Promise((resolve, reject) => {
                const tx = db.transaction('offline_scans', 'readwrite');
                const store = tx.objectStore('offline_scans');
                const record = {
                    regid: regid,
                    scan_time: scanTimeIso || new Date().toISOString(),
                    synced: false,
                    device_id: getDeviceId(),
                    lead_type: leadType,
                    comments: comments
                };
                const req = store.add(record);
                req.onsuccess = () => resolve(req.result);
                req.onerror = () => reject(req.error);
            });
        } catch (e) {
            console.error('saveOfflineScan error', e);
        }
    }

    async function getOfflineScans(onlyUnsynced = false) {
        try {
            const db = await openIndexedDb();
            return await new Promise((resolve, reject) => {
                const tx = db.transaction('offline_scans', 'readonly');
                const store = tx.objectStore('offline_scans');
                const result = [];
                let req;
                if (onlyUnsynced) {
                    try {
                        const idx = store.index('synced');
                        req = idx.openCursor(IDBKeyRange.only(false));
                    } catch (e) {
                        // Fallback for older DB schema that may not have the index
                        req = store.openCursor();
                    }
                } else {
                    req = store.openCursor();
                }
                req.onerror = () => reject(req.error);
                req.onsuccess = (e) => {
                    const cursor = e.target.result;
                    if (cursor) {
                        if (!onlyUnsynced || cursor.value.synced === false) {
                            result.push(cursor.value);
                        }
                        cursor.continue();
                    } else {
                        resolve(result);
                    }
                };
            });
        } catch (e) {
            console.error('getOfflineScans error', e);
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
            console.error('markScanSynced error', e);
            return false;
        }
    }

    async function updatePendingCount() {
        const unsynced = await getOfflineScans(true);
        if (pendingCountValueEl) {
            pendingCountValueEl.textContent = unsynced.length.toString();
        }
        if (syncBtn) {
            syncBtn.disabled = !navigator.onLine || unsynced.length === 0;
        }
    }

    function setSyncProgress(text, tone) {
        if (!syncProgressEl) return;
        syncProgressEl.textContent = text || '';
        syncProgressEl.style.color = '#475569';
        if (tone === 'success') syncProgressEl.style.color = '#065f46';
        if (tone === 'warning') syncProgressEl.style.color = '#b45309';
        if (tone === 'error') syncProgressEl.style.color = '#b91c1c';
    }

    function formatIstDateTime(dateInput) {
        try {
            const dt = new Date(dateInput);
            if (Number.isNaN(dt.getTime())) return '-';
            return new Intl.DateTimeFormat('en-CA', {
                timeZone: 'Asia/Kolkata',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
            }).format(dt).replace(',', '');
        } catch (e) {
            return '-';
        }
    }

    function maskSensitiveValue(field, value) {
        if (value === null || value === undefined || value === '') return '-';
        const str = String(value);

        if (field === 'Email') {
            if (!str.includes('@')) return str;
            const parts = str.split('@');
            const local = parts[0] || '';
            const domain = parts[1] || '';
            const maskedLocal = local.length <= 2
                ? '*'.repeat(local.length)
                : local.slice(0, 2) + '*'.repeat(Math.max(local.length - 2, 0));
            return maskedLocal + '@' + domain;
        }

        if (field === 'Mobile') {
            const digits = str.replace(/\D+/g, '');
            if (digits.length > 4) {
                return digits.slice(0, 2) + '*'.repeat(Math.max(digits.length - 4, 0)) + digits.slice(-2);
            }
            if (digits.length > 0) {
                return '*'.repeat(digits.length);
            }
            return str;
        }

        return str;
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
        updatePendingCount();
    }

    function extractRegidFromText(text) {
        if (!text) return '';
        const trimmed = text.trim();
        try {
            const url = new URL(trimmed);
            const param = url.searchParams.get('regid') || url.searchParams.get('RegID');
            if (param) return param;
        } catch (e) {
            // not URL, use trimmed text as fallback
        }
        return trimmed;
    }

    function setResultState(state) {
        if (!resultCardEl) return;
        resultCardEl.classList.remove('state-success', 'state-warning', 'state-error');
        if (state === 'success') resultCardEl.classList.add('state-success');
        if (state === 'warning') resultCardEl.classList.add('state-warning');
        if (state === 'error') resultCardEl.classList.add('state-error');
    }

    function showLeadResult(status, message, user, state) {
        let icon = '';
        if (state === 'success') icon = '✓ ';
        if (state === 'warning') icon = '! ';
        if (state === 'error') icon = '✗ ';
        resultTitleEl.textContent = icon + status;
        setResultState(state);
        if (user) {
            resultBodyEl.innerHTML =
                '<div><span class="lead-label">Name:</span> ' + (user.Name || '-') + '</div>' +
                '<div><span class="lead-label">Company:</span> ' + (user.Company || '-') + '</div>' +
                '<div><span class="lead-label">Category:</span> ' + (user.Category || '-') + '</div>' +
                '<div><span class="lead-label">Email:</span> ' + (user.Email || '-') + '</div>' +
                '<div style="margin-top:4px;">' + message + '</div>';
        } else {
            resultBodyEl.textContent = message;
        }
    }

    async function sendLeadToServer(regid, scanTimeIso, leadType = null, comments = null, silent = false) {
        try {
            const response = await fetch('{{ route('lead.scan.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    regid: regid,
                    device_id: getDeviceId(),
                    scan_time: scanTimeIso || new Date().toISOString(),
                    lead_type: leadType,
                    comments: comments
                }),
            });

            const data = await response.json();
            if (!response.ok || !data.success) {
                if (data && data.code === 'USER_NOT_FOUND') {
                    if (!silent) {
                        showLeadResult('Not Found', 'Data with this RegID is not found. Please check the scanned QR is a badge QR.', null, 'error');
                    }
                } else if (data && data.code === 'ALREADY_SCANNED') {
                    if (!silent) {
                        showLeadResult('Already Scanned', data.message || 'This user is already scanned.', null, 'warning');
                    }
                } else {
                    if (!silent) {
                        showLeadResult('Online Error', (data && data.message) ? data.message : 'Could not save lead online.', null, 'error');
                    }
                }
                return {
                    ok: false,
                    code: data && data.code ? data.code : 'ERROR',
                    message: data && data.message ? data.message : 'Could not save lead online.',
                };
            }

            if (!silent) {
                showLeadResult('Lead Captured', 'Lead saved successfully.', data.user || null, 'success');
            }
            addRecentScanRow(data.user || {}, leadType, comments, scanTimeIso || new Date().toISOString());
            return {
                ok: true,
                code: 'SUCCESS',
                message: data.message || 'Lead saved successfully.',
                user: data.user || null,
            };
        } catch (e) {
            console.error('sendLeadToServer error', e);
            if (!silent) {
                showLeadResult('Online Error', e.message || 'Could not save lead online.', null, 'error');
            }
            return {
                ok: false,
                code: 'ERROR',
                message: e.message || 'Could not save lead online.',
            };
        }
    }

    async function precheckLeadScan(regid) {
        try {
            const response = await fetch('{{ route('lead.scan.precheck') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ regid: regid }),
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) {
                if (data && data.code === 'USER_NOT_FOUND') {
                    showLeadResult('Not Found', 'Data with this RegID is not found. Please check the scanned QR is a badge QR.', null, 'error');
                } else if (data && data.code === 'ALREADY_SCANNED') {
                    showLeadResult('Already Scanned', data.message || 'This user is already scanned.', null, 'warning');
                } else {
                    showLeadResult('Online Error', (data && data.message) ? data.message : 'Could not validate this scan.', null, 'error');
                }
                return false;
            }

            return true;
        } catch (e) {
            console.error('precheckLeadScan error', e);
            showLeadResult('Online Error', 'Could not validate this scan.', null, 'error');
            return false;
        }
    }

    function incrementTotalLeads() {
        if (!totalLeadsCountEl) return;
        const current = parseInt(totalLeadsCountEl.textContent || '0', 10);
        totalLeadsCountEl.textContent = Number.isNaN(current) ? '1' : String(current + 1);
    }

    function addRecentScanRow(userData, leadType, comments, scanTimeIso) {
        if (!recentTbody) return;
        if (recentNoData) {
            recentNoData.style.display = 'none';
        }

        const sharedFields = Array.isArray(window.leadPortalSharedFields) ? window.leadPortalSharedFields : [];
        const tr = document.createElement('tr');

        sharedFields.forEach((field) => {
            const td = document.createElement('td');
            td.textContent = maskSensitiveValue(field, userData[field]);
            tr.appendChild(td);
        });

        const leadTypeTd = document.createElement('td');
        leadTypeTd.textContent = leadType || '-';
        tr.appendChild(leadTypeTd);

        const commentsTd = document.createElement('td');
        commentsTd.textContent = comments || '-';
        tr.appendChild(commentsTd);

        const scanTimeTd = document.createElement('td');
        scanTimeTd.textContent = formatIstDateTime(scanTimeIso);
        tr.appendChild(scanTimeTd);

        recentTbody.insertBefore(tr, recentTbody.firstChild);

        while (recentTbody.children.length > 5) {
            recentTbody.removeChild(recentTbody.lastChild);
        }

        if (recentTableWrap) {
            recentTableWrap.style.display = 'block';
        }
        incrementTotalLeads();
    }

    function resetMetaModal() {
        document.querySelectorAll('input[name="lead_type"]').forEach((input) => {
            input.checked = false;
        });
        if (metaComments) {
            metaComments.value = '';
        }
    }

    function openMetaModal(scanData) {
        pendingScan = scanData;
        resetMetaModal();
        if (metaModal) {
            metaModal.style.display = 'flex';
        }
    }

    function closeMetaModal() {
        if (metaModal) {
            metaModal.style.display = 'none';
        }
    }

    function isMetaModalOpen() {
        return !!(metaModal && metaModal.style.display === 'flex');
    }

    async function restartScanner() {
        if (!selectedCameraId) return;
        try {
            if (scannerInstance) {
                await scannerInstance.stop().catch(() => {});
                await scannerInstance.clear().catch(() => {});
            }
        } catch (e) {
            // ignore stop/clear errors
        }

        scannerInstance = new Html5Qrcode(qrRegionId);
        await scannerInstance.start(
            selectedCameraId,
            {
                fps: 18,
                disableFlip: true,
                experimentalFeatures: {
                    useBarCodeDetectorIfSupported: true,
                },
                qrbox: function (w, h) {
                    const minEdge = Math.min(w, h);
                    const size = Math.floor(minEdge * 0.86);
                    return { width: size, height: size };
                },
            },
            (decodedText) => {
                handleDetectedCode(decodedText).then((processed) => {
                    if (processed && scannerInstance) {
                        scannerPausedByModal = true;
                        scannerInstance.pause(true);
                    }
                });
            },
            () => {
                // ignore scan errors for performance
            }
        );
        scannerPausedByModal = false;
    }

    async function tryResumeScanner() {
        if (isMetaModalOpen()) return;
        if (!scannerInstance || !selectedCameraId) {
            initializeScanner().catch(() => {});
            return;
        }
        try {
            await scannerInstance.resume();
            scannerPausedByModal = false;
        } catch (e) {
            // In PWA/home-screen mode camera may not resume; restart stream.
            await restartScanner().catch(() => {});
        }
    }

    function scheduleScannerRetry() {
        if (cameraInitAttempts >= MAX_CAMERA_INIT_RETRIES) return;
        const delay = Math.min(4500, 700 * cameraInitAttempts);
        setTimeout(() => {
            initializeScanner().catch(() => {});
        }, delay);
    }

    async function initializeScanner() {
        if (cameraInitInProgress || isMetaModalOpen()) return;
        cameraInitInProgress = true;

        try {
            if (!window.Html5Qrcode) {
                throw new Error('Html5Qrcode not available yet');
            }

            const devices = await Html5Qrcode.getCameras();
            if (!devices || devices.length === 0) {
                throw new Error('No camera devices found');
            }

            let cameraId = devices[0].id;
            const backCam = devices.find(d => /back|rear|environment/i.test(d.label));
            if (backCam) cameraId = backCam.id;
            selectedCameraId = cameraId;

            if (!scannerInstance) {
                scannerInstance = new Html5Qrcode(qrRegionId);
            }

            await scannerInstance.start(
                cameraId,
                {
                    fps: 18,
                    disableFlip: true,
                    experimentalFeatures: {
                        useBarCodeDetectorIfSupported: true,
                    },
                    qrbox: function (w, h) {
                        const minEdge = Math.min(w, h);
                        const size = Math.floor(minEdge * 0.86);
                        return { width: size, height: size };
                    },
                },
                (decodedText) => {
                    handleDetectedCode(decodedText).then((processed) => {
                        if (processed && scannerInstance) {
                            scannerPausedByModal = true;
                            scannerInstance.pause(true);
                        }
                    });
                },
                () => {
                    // Ignore scan errors for performance.
                }
            );

            cameraInitAttempts = 0;
        } catch (err) {
            cameraInitAttempts += 1;
            console.error('Camera init/retry error', err);
            if (!navigator.onLine) {
                showLeadResult(
                    'Offline Mode',
                    'Camera is initializing. You can still scan via manual RegID input; camera will retry automatically.',
                    null,
                    'warning'
                );
            } else {
                showLeadResult('Camera Preparing', 'Retrying camera access...', null, 'warning');
            }
            scheduleScannerRetry();
        } finally {
            cameraInitInProgress = false;
        }
    }

    async function submitPendingScan(withMeta) {
        if (!pendingScan) return;
        const leadTypeInput = document.querySelector('input[name="lead_type"]:checked');
        const leadType = withMeta ? (leadTypeInput ? leadTypeInput.value : null) : null;
        const comments = withMeta ? (metaComments ? metaComments.value : null) : null;

        if (navigator.onLine) {
            await sendLeadToServer(pendingScan.regid, pendingScan.scanTimeIso, leadType, comments);
        } else {
            await saveOfflineScan(pendingScan.regid, pendingScan.scanTimeIso, leadType, comments);
            showLeadResult('Saved Offline', 'Lead will sync when internet is available.', null, 'warning');
            await updatePendingCount();
        }

        pendingScan = null;
        closeMetaModal();
        if (scannerInstance) {
            tryResumeScanner();
        }
    }

    async function handleDetectedCode(rawText) {
        const regid = extractRegidFromText(rawText);
        if (!regid) return false;

        const now = Date.now();
        if (regid === lastScannedCode && now - lastScannedTime < DUPLICATE_WINDOW_MS) {
            return false;
        }
        lastScannedCode = regid;
        lastScannedTime = now;
        const scanTimeIso = new Date().toISOString();

        if (navigator.onLine) {
            const canProceed = await precheckLeadScan(regid);
            if (!canProceed) {
                return false;
            }
        }

        openMetaModal({
            regid: regid,
            scanTimeIso: scanTimeIso
        });
        return true;
    }

    async function syncOfflineScans() {
        if (!navigator.onLine) {
            updateConnectionStatus();
            return;
        }
        if (syncBtn) {
            syncBtn.disabled = true;
        }
        const unsynced = await getOfflineScans(true);
        if (unsynced.length === 0) {
            setSyncProgress('No pending scans to sync.', 'success');
            setTimeout(() => setSyncProgress(''), 2000);
            return;
        }

        let handled = 0;
        setSyncProgress('Syncing 0/' + unsynced.length + '...', 'warning');
        for (const record of unsynced) {
            try {
                setSyncProgress('Syncing ' + handled + '/' + unsynced.length + '...', 'warning');
                const result = await sendLeadToServer(
                    record.regid,
                    record.scan_time,
                    record.lead_type || null,
                    record.comments || null,
                    true
                );

                if (result.ok) {
                    await markScanSynced(record.id);
                    handled++;
                } else if (result.code === 'ALREADY_SCANNED') {
                    await markScanSynced(record.id);
                    showLeadResult('Already Scanned', 'This RegID is already scanned: ' + record.regid + '. Removed from pending queue.', null, 'warning');
                    handled++;
                } else if (result.code === 'USER_NOT_FOUND') {
                    await markScanSynced(record.id);
                    showLeadResult('Not Found', 'RegID not found: ' + record.regid + '. Removed from pending queue.', null, 'error');
                    handled++;
                } else {
                    setSyncProgress('Sync stopped at ' + handled + '/' + unsynced.length + '.', 'error');
                    break;
                }
            } catch (e) {
                // Stop on first failure to avoid hammering
                setSyncProgress('Sync stopped at ' + handled + '/' + unsynced.length + '.', 'error');
                break;
            }
        }
        await updatePendingCount();
        if (handled === unsynced.length) {
            setSyncProgress('Sync complete (' + handled + '/' + unsynced.length + ').', 'success');
            setTimeout(() => setSyncProgress(''), 2500);
        }
    }

    if (syncBtn) {
        syncBtn.addEventListener('click', function () {
            syncOfflineScans();
        });
    }

    if (metaSaveBtn) {
        metaSaveBtn.addEventListener('click', function () {
            submitPendingScan(true);
        });
    }

    if (metaSkipBtn) {
        metaSkipBtn.addEventListener('click', function () {
            submitPendingScan(false);
        });
    }

    window.addEventListener('online', function () {
        updateConnectionStatus();
        setTimeout(() => {
            syncOfflineScans();
        }, 300);
        initializeScanner().catch(() => {});
    });
    window.addEventListener('offline', function () {
        updateConnectionStatus();
    });

    window.addEventListener('pageshow', function () {
        tryResumeScanner();
    });
    window.addEventListener('focus', function () {
        tryResumeScanner();
    });
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            tryResumeScanner();
        }
    });

    // Manual input fallback (explicit submit)
    if (regidInput) {
        regidInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = regidInput.value;
                if (value) {
                    handleDetectedCode(value);
                    regidInput.value = '';
                }
            }
        });
        regidInput.addEventListener('paste', function () {
            // keep paste support but do not auto-submit
        });
    }

    if (manualSubmitBtn) {
        manualSubmitBtn.addEventListener('click', function () {
            if (!regidInput) return;
            const value = regidInput.value;
            if (value) {
                handleDetectedCode(value);
                regidInput.value = '';
            }
        });
    }

    updateConnectionStatus();

    html5QrcodeReady.then(() => {
        initializeScanner().catch(() => {});
        window.addEventListener('beforeunload', function () {
            try {
                if (scannerInstance) {
                    scannerInstance.stop().catch(() => {});
                    scannerInstance.clear().catch(() => {});
                }
            } catch (e) {}
        });
    });
});
</script>
@endpush

