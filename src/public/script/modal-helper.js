/**
 * MODAL HELPER - Fungsi bantu untuk membuat modal (popup window)
 * 
 * Tujuan: Supaya tidak perlu menulis kode yang sama berulang-ulang
 * setiap kali membuat modal. Tinggal pakai fungsi-fungsi ini.
 * 
 * Contoh tanpa Modal Helper (panjang & ribet):
 * ----------------------------------------
 * const overlay = document.createElement('div');
 * overlay.className = 'modal-overlay';
 * overlay.style.cssText = 'position: fixed; top: 0; ...banyak banget';
 * 
 * const header = document.createElement('div');
 * header.style.cssText = 'background: linear-gradient...banyak lagi';
 * // ... dan seterusnya puluhan baris
 * 
 * Contoh dengan Modal Helper (singkat & gampang):
 * ----------------------------------------
 * const overlay = PembantuModal.buatOverlay();
 * const header = PembantuModal.buatHeader('Judul Modal', () => modal.remove());
 * // Selesai! Cuma 2 baris
 */

const PembantuModal = {
    /**
     * Buat background gelap untuk modal (overlay)
     * @param {number} zIndex - Urutan layer (default: 1000)
     * @returns {HTMLElement} Element overlay yang sudah siap pakai
     * 
     * Contoh: const overlay = PembantuModal.buatOverlay();
     */
    buatOverlay(zIndex = 1000) {
        const overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.style.cssText = `position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: ${zIndex};`;
        return overlay;
    },
    
    /**
     * Buat tombol X untuk tutup modal
     * @param {Function} onClick - Fungsi yang dijalankan saat diklik (biasanya tutup modal)
     * @returns {HTMLElement} Tombol close yang sudah siap pakai
     * 
     * Contoh: const closeBtn = PembantuModal.buatTombolTutup(() => modal.remove());
     */
    buatTombolTutup(onClick) {
        const btn = document.createElement('button');
        btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        btn.style.cssText = 'background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 18px;';
        btn.onclick = onClick;
        return btn;
    },
    
    /**
     * Buat header modal (bagian atas dengan judul & tombol close)
     * @param {string} title - Judul yang ditampilkan di modal
     * @param {Function} onClose - Fungsi untuk tutup modal
     * @returns {HTMLElement} Header modal lengkap dengan judul & tombol X
     * 
     * Contoh: const header = PembantuModal.buatHeader('Edit Transaksi', () => overlay.remove());
     */
    buatHeader(title, onClose) {
        const header = document.createElement('div');
        header.style.cssText = 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;';
        
        const titleEl = document.createElement('h3');
        titleEl.textContent = title;
        titleEl.style.margin = '0';
        
        const closeBtn = this.buatTombolTutup(onClose);
        
        header.appendChild(titleEl);
        header.appendChild(closeBtn);
        return header;
    },
    
    /**
     * Buat input form (text box, number, select, dll)
     * @param {string} label - Label yang ditampilkan di atas input
     * @param {string} id - ID untuk element input
     * @param {*} value - Nilai awal input (bisa kosong)
     * @param {string} type - Tipe input: 'text', 'number', 'select', dll
     * @param {boolean} required - Apakah wajib diisi?
     * @returns {HTMLElement} Container dengan label + input
     * 
     * Contoh: 
     * const inputJumlah = PembantuModal.buatInputForm('Jumlah', 'editJumlah', 50000, 'number', true);
     */
    buatInputForm(label, id, value, type = 'text', required = true) {
        const container = document.createElement('div');
        container.style.marginBottom = '20px';
        
        const labelEl = document.createElement('label');
        labelEl.textContent = label;
        labelEl.style.cssText = 'display: block; margin-bottom: 8px; font-weight: 500; color: #334155;';
        
        const input = type === 'select' 
            ? document.createElement('select')
            : document.createElement('input');
        
        if (type !== 'select') input.type = type;
        if (value !== undefined) input.value = value;
        if (required) input.required = true;
        input.style.cssText = 'width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;';
        
        container.appendChild(labelEl);
        container.appendChild(input);
        return container;
    },
    
    /**
     * Buat grup tombol (misal: Batal & Simpan)
     * @param {Array} buttons - Array berisi objek tombol: [{text: 'Batal', onClick: fn, style: '...'}, ...]
     * @returns {HTMLElement} Container berisi semua tombol
     * 
     * Contoh:
     * const buttons = PembantuModal.buatGrupTombol([
     *     {text: 'Batal', onClick: () => modal.remove()},
     *     {text: 'Simpan', onClick: () => simpanData(), style: 'background: #3b82f6; color: white;'}
     * ]);
     */
    buatGrupTombol(buttons) {
        const group = document.createElement('div');
        group.style.cssText = 'display: flex; gap: 12px; margin-top: 24px;';
        
        buttons.forEach(btn => {
            const button = document.createElement('button');
            button.textContent = btn.text;
            button.onclick = btn.onClick;
            button.style.cssText = btn.style || 'padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; background: #e2e8f0;';
            group.appendChild(button);
        });
        
        return group;
    },
    
    /**
     * Format tanggal ke bahasa Indonesia
     * @param {string} dateString - Tanggal dalam format string (2024-12-31)
     * @returns {string} Tanggal dalam format: 31 Desember 2024
     * 
     * Contoh: PembantuModal.formatTanggal('2024-12-31') → '31 Desember 2024'
     */
    formatTanggal(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
    },
    
    /**
     * Format angka ke format Rupiah
     * @param {number} amount - Jumlah uang
     * @returns {string} Format Rupiah: Rp 50.000
     * 
     * Contoh: PembantuModal.formatRupiah(50000) → 'Rp 50.000'
     */
    formatRupiah(amount) {
        return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    }
};

/* ==========================================
 * CONTOH PENGGUNAAN LENGKAP
 * ==========================================
 * 
 * // 1. Buat overlay (background gelap)
 * const overlay = PembantuModal.buatOverlay();
 * 
 * // 2. Buat container modal
 * const modalContent = document.createElement('div');
 * modalContent.style.cssText = 'background: white; border-radius: 12px; padding: 0; width: 500px;';
 * 
 * // 3. Buat header
 * const header = PembantuModal.buatHeader('Edit Transaksi', () => overlay.remove());
 * 
 * // 4. Buat body modal
 * const body = document.createElement('div');
 * body.style.padding = '24px';
 * body.innerHTML = '<p>Isi modal di sini...</p>';
 * 
 * // 5. Gabungkan semua
 * modalContent.appendChild(header);
 * modalContent.appendChild(body);
 * overlay.appendChild(modalContent);
 * document.body.appendChild(overlay);
 * 
 * // Selesai! Modal sudah muncul
 * 
 * ==========================================
 * TANPA Modal Helper butuh 50+ baris kode
 * DENGAN Modal Helper cuma butuh 10 baris
 * ==========================================
 */
