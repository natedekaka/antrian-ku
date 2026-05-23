const API = {
    post: async (endpoint, data) => {
        const res = await fetch('/api/' + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return res.json();
    },
    get: async (endpoint) => {
        const res = await fetch('/api/' + endpoint);
        return res.json();
    }
};

function showToast(message, type) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => { toast.remove(); }, 3000);
}

var audioCtx = null;
function getAudioCtx() {
    if (!audioCtx) {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    if (audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

function playBeep(freq, duration, type) {
    try {
        var ctx = getAudioCtx();
        var osc = ctx.createOscillator();
        var gain = ctx.createGain();
        osc.type = type || 'sine';
        osc.frequency.value = freq || 800;
        gain.gain.setValueAtTime(0.25, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + (duration || 0.15));
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + (duration || 0.15));
    } catch (e) {}
}

function playCallSound() {
    playBeep(880, 0.12, 'sine');
    setTimeout(function() { playBeep(1100, 0.18, 'sine'); }, 130);
}

function speakQueue(queueCode, categoryName, counterName) {
    if (!window.speechSynthesis) return;
    if (window.speechSynthesis.speaking) {
        window.speechSynthesis.cancel();
    }
    var label = counterName || 'Loket';
    var text = 'Nomor antrian ' + queueCode + ', ' + categoryName + ', silakan menuju ' + label;
    var utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'id-ID';
    utterance.rate = 0.85;
    utterance.pitch = 1.1;
    window.speechSynthesis.speak(utterance);
}

function speakWithBeep(queueCode, categoryName, counterName) {
    playCallSound();
    setTimeout(function() { speakQueue(queueCode, categoryName, counterName); }, 350);
}

function printTicket(queueCode, categoryName) {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const dateStr = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    const printArea = document.createElement('div');
    printArea.id = 'ticket-print-area';
    printArea.innerHTML =
        '<div class="ticket-title">SISTEM ANTRIAN</div>' +
        '<div class="ticket-divider"></div>' +
        '<div class="ticket-number">' + queueCode + '</div>' +
        '<div class="ticket-category">' + categoryName + '</div>' +
        '<div class="ticket-divider"></div>' +
        '<div class="ticket-time">' + dateStr + '</div>' +
        '<div class="ticket-time">' + timeStr + '</div>' +
        '<div class="ticket-divider"></div>' +
        '<div class="ticket-time">Silakan menunggu dipanggil</div>';
    document.body.appendChild(printArea);
    window.print();
    setTimeout(function() { printArea.remove(); }, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;

    // === HALAMAN AMBIL NOMOR ===
    if (body.classList.contains('page-ambil')) {
        const categoryBtns = document.querySelectorAll('.category-btn');
        const modal = document.getElementById('modal-antrian');
        const numberDisplay = document.getElementById('queue-number-display');
        const categoryNameDisplay = document.getElementById('queue-category-name');
        const modalTime = document.getElementById('modal-time');
        const estimasiEl = document.getElementById('estimasi-waktu');

        function updateModalTime() {
            if (modalTime) {
                const now = new Date();
                modalTime.textContent = now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
        }

        async function loadEstimasi(categoryId) {
            if (!estimasiEl) return;
            estimasiEl.classList.remove('hidden');
            estimasiEl.className = 'estimasi-badge loading';
            estimasiEl.innerHTML =
                '<svg class="estimasi-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Menghitung estimasi...';
            try {
                const result = await API.get('estimasi?category_id=' + categoryId);
                if (result.success && result.data) {
                    estimasiEl.className = 'estimasi-badge';
                    estimasiEl.innerHTML =
                        '<svg class="estimasi-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> ' + result.data.estimasi_label;
                }
            } catch (e) {
                estimasiEl.classList.add('hidden');
            }
        }

        categoryBtns.forEach(btn => {
            btn.addEventListener('click', async () => {
                const categoryId = btn.dataset.id;
                loadEstimasi(categoryId);
                try {
                    const result = await API.post('ambil', { category_id: parseInt(categoryId) });
                    if (result.success) {
                        numberDisplay.textContent = result.data.queue_code;
                        categoryNameDisplay.textContent = result.data.category_name;
                        updateModalTime();
                        modal.classList.remove('hidden');
                        numberDisplay.classList.remove('number-pop');
                        void numberDisplay.offsetWidth;
                        numberDisplay.classList.add('number-pop');
                        document.getElementById('btn-cetak-tiket')?.setAttribute('data-code', result.data.queue_code);
                        document.getElementById('btn-cetak-tiket')?.setAttribute('data-category', result.data.category_name);
                    } else {
                        alert('Gagal: ' + (result.error || result.message));
                    }
                } catch (e) {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            });
        });

        document.querySelector('.btn-ambil-lagi')?.addEventListener('click', () => {
            modal.classList.add('hidden');
            if (estimasiEl) estimasiEl.classList.add('hidden');
        });

        document.getElementById('btn-cetak-tiket')?.addEventListener('click', function() {
            const code = this.getAttribute('data-code');
            const cat = this.getAttribute('data-category');
            if (code) printTicket(code, cat);
        });
    }

    // === HALAMAN LAYAR ===
    if (body.classList.contains('page-layar')) {
        const numberDisplay = document.getElementById('layar-number');
        const statusDisplay = document.getElementById('layar-status');
        const nextList = document.getElementById('layar-next-list');
        const clockDisplay = document.getElementById('layar-clock');

        function updateClock() {
            const now = new Date();
            clockDisplay.textContent = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
        setInterval(updateClock, 1000);
        updateClock();

        let lastCallKey = '';

        async function pollData() {
            try {
                const result = await API.get('data-antrian');
                if (result.success) {
                    const data = result.data;
                    if (data.current_calls && data.current_calls.length > 0) {
                        const current = data.current_calls[0];
                        var callKey = current.queue_code + '|' + (current.called_at || '');
                        if (callKey !== lastCallKey) {
                            numberDisplay.textContent = current.queue_code;
                            numberDisplay.classList.remove('number-pop');
                            void numberDisplay.offsetWidth;
                            numberDisplay.classList.add('number-pop');
                            lastCallKey = callKey;
                            speakWithBeep(current.queue_code, current.category_name, 'Loket ' + current.counter_id);
                        }
                        statusDisplay.textContent = 'LOKET ' + (current.counter_id || '1') + ' \u2014 SEDANG DIPANGGIL';
                    } else {
                        numberDisplay.textContent = '---';
                        statusDisplay.textContent = 'MENUNGGU ANTRIAN';
                        lastCallKey = '';
                    }

                    if (data.waiting_list) {
                        nextList.innerHTML = data.waiting_list.slice(0, 5).map(q =>
                            '<div class="layar-next-item status-waiting">' + q.queue_code + '</div>'
                        ).join('');
                    }
                }
            } catch (e) {}
        }
        pollData();
        setInterval(pollData, 3000);
    }

    // === HALAMAN ADMIN ===
    if (body.classList.contains('page-admin')) {
        let selectedCounter = null;
        let lastCalledCode = '';
        const counterBtns = document.querySelectorAll('.counter-btn');
        const activeNumber = document.getElementById('admin-active-number');
        const activeStatus = document.getElementById('admin-active-status');
        const queueBody = document.getElementById('admin-queue-body');
        const activeCard = activeNumber ? activeNumber.parentElement : null;

        counterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                counterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                selectedCounter = parseInt(btn.dataset.id);
                pollData();
            });
        });

        if (counterBtns.length > 0) counterBtns[0].click();

        document.getElementById('btn-panggil')?.addEventListener('click', async () => {
            if (!selectedCounter) return alert('Pilih loket terlebih dahulu');
            const result = await API.post('panggil', { counter_id: selectedCounter });
            if (result.success) {
                playCallSound();
                showToast('Memanggil ' + result.data.queue_code, 'success');
                var counterName = 'Loket ' + selectedCounter;
                setTimeout(function() { speakQueue(result.data.queue_code, result.data.category_name, counterName); }, 400);
                pollData();
            } else {
                showToast(result.message || 'Gagal memanggil', 'error');
            }
        });

        document.getElementById('btn-ulang')?.addEventListener('click', async () => {
            const activeId = activeCard ? activeCard.dataset.activeId : null;
            if (!activeId) return showToast('Tidak ada antrian aktif', 'error');
            playCallSound();
            await API.post('panggil-ulang', { queue_id: parseInt(activeId) });
            showToast('Memanggil ulang', 'success');
            if (activeNumber.textContent !== '---') {
                var code = activeNumber.textContent;
                var cat = document.querySelector('#admin-active-status')?.textContent || 'Loket ' + selectedCounter;
                setTimeout(function() { speakQueue(code, cat, 'Loket ' + selectedCounter); }, 400);
            }
            pollData();
        });

        document.getElementById('btn-skip')?.addEventListener('click', async () => {
            const activeId = activeCard ? activeCard.dataset.activeId : null;
            if (!activeId) return showToast('Tidak ada antrian aktif', 'error');
            await API.post('skip', { queue_id: parseInt(activeId) });
            showToast('Antrian dilewati', 'success');
            pollData();
        });

        document.getElementById('btn-selesai')?.addEventListener('click', async () => {
            const activeId = activeCard ? activeCard.dataset.activeId : null;
            if (!activeId) return showToast('Tidak ada antrian aktif', 'error');
            await API.post('selesai', { queue_id: parseInt(activeId) });
            showToast('Antrian selesai', 'success');
            pollData();
        });

        async function pollData() {
            try {
                const result = await API.get('data-antrian');
                if (result.success) {
                    const data = result.data;

                    const currentForCounter = data.current_calls ?
                        data.current_calls.filter(c => c.counter_id === selectedCounter) : [];

                    if (currentForCounter.length > 0) {
                        const current = currentForCounter[0];
                        activeNumber.textContent = current.queue_code;
                        activeStatus.textContent = 'DIPANGGIL DI ' + (current.counter_name || 'Loket ' + current.counter_id);
                        if (activeCard) activeCard.dataset.activeId = current.id;
                    } else {
                        activeNumber.textContent = '---';
                        activeStatus.textContent = 'Tidak ada antrian aktif';
                        if (activeCard) delete activeCard.dataset.activeId;
                    }

                    if (queueBody) {
                        const statusLabels = {
                            waiting: 'Menunggu',
                            called: 'Dipanggil',
                            skipped: 'Dilewati',
                            completed: 'Selesai'
                        };
                        const statusClasses = {
                            waiting: 'badge-waiting',
                            called: 'badge-called',
                            skipped: 'badge-skipped',
                            completed: 'badge-completed'
                        };
                        const allQueues = [
                            ...(data.current_calls || []),
                            ...(data.waiting_list || [])
                        ];
                        queueBody.innerHTML = allQueues.map((q, i) => {
                            const label = statusLabels[q.status] || q.status;
                            const cls = statusClasses[q.status] || '';
                            const time = q.called_at ? new Date(q.called_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
                            return '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
                                '<td><strong>' + q.queue_code + '</strong></td>' +
                                '<td>' + (q.category_name || '-') + '</td>' +
                                '<td><span class="badge ' + cls + '">' + label + '</span></td>' +
                                '<td>' + time + '</td>' +
                                '</tr>';
                        }).join('');
                    }
                }
            } catch (e) {}
        }

        pollData();
        setInterval(pollData, 5000);
    }
});
