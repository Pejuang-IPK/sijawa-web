// Format currency input
function formatRupiah(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value === '') {
        input.value = '';
        // Update hidden field juga
        const rawInput = document.getElementById(input.id + '_raw');
        if (rawInput) {
            rawInput.value = '';
        }
        return;
    }
    
    // Validasi nilai tidak boleh negatif
    value = Math.abs(parseInt(value) || 0).toString();
    let formatted = parseInt(value).toLocaleString('id-ID');
    input.value = formatted;
    
    // Update hidden field untuk value yang akan dikirim
    const rawInput = document.getElementById(input.id + '_raw');
    if (rawInput) {
        rawInput.value = value;
    }
}

function persiapkanKirimForm(event) {
    // Ambil nilai dari display input
    const displayInput = document.getElementById('transaksi');
    const rawInput = document.getElementById('transaksi_raw');
    
    if (displayInput && rawInput) {
        // Ambil angka saja (hilangkan pemisah ribuan)
        const cleanValue = displayInput.value.replace(/[^0-9]/g, '');
        rawInput.value = cleanValue;
        
        // Validasi tidak boleh kosong atau 0
        if (!cleanValue || cleanValue === '0') {
            alert('Jumlah transaksi harus diisi dan lebih dari 0');
            event.preventDefault();
            return false;
        }
    }
    
    // Validasi field lain
    const jenisTransaksi = document.getElementById('jenisTransaksi');
    const kategoriTransaksi = document.getElementById('kategoriTransaksi');
    const keteranganTransaksi = document.getElementById('keteranganTransaksi');
    
    if (!jenisTransaksi || !jenisTransaksi.value) {
        alert('Pilih jenis transaksi terlebih dahulu');
        event.preventDefault();
        return false;
    }
    
    if (!kategoriTransaksi || !kategoriTransaksi.value) {
        alert('Pilih kategori transaksi terlebih dahulu');
        event.preventDefault();
        return false;
    }
    
    if (!keteranganTransaksi || !keteranganTransaksi.value.trim()) {
        alert('Keterangan transaksi harus diisi');
        event.preventDefault();
        return false;
    }
    
    return true;
}

// Modal functions
function bukaModal() {
    document.getElementById('modalTransaksi').style.display = 'flex';
}

function tutupModal() {
    document.getElementById('modalTransaksi').style.display = 'none';
}

function bukaModalKategori(jenis) {
    document.getElementById('modalKategori').style.display = 'flex';
    document.getElementById('jenisKategoriInput').value = jenis;
    document.getElementById('jenisKategoriText').textContent = jenis;
}

function tutupModalKategori() {
    document.getElementById('modalKategori').style.display = 'none';
    document.getElementById('namaKategori').value = '';
}

function tutupModalDetailKategori() {
    document.getElementById('modalKategoriDetail').style.display = 'none';
}

// Update kategori options based on transaction type
function updateOpsiKategori() {
    const jenisTransaksi = document.getElementById('jenisTransaksi').value;
    const kategoriSelect = document.getElementById('kategoriTransaksi');
    
    kategoriSelect.innerHTML = '<option value="">Pilih kategori</option>';
    kategoriSelect.disabled = false;
    
    if(!jenisTransaksi) {
        kategoriSelect.innerHTML = '<option value="">Pilih jenis terlebih dahulu</option>';
        kategoriSelect.disabled = true;
        return;
    }
    
    let categories = jenisTransaksi === 'Pemasukan' ? (window.kategoriPemasukan || []) : (window.kategoriPengeluaran || []);
    
    if(categories.length === 0) {
        kategoriSelect.innerHTML = '<option value="">Belum ada kategori</option>';
        kategoriSelect.disabled = true;
        
        // Tampilkan pesan dan tombol tambah kategori
        const jenisText = jenisTransaksi;
        const msgElement = document.getElementById('kategoriEmptyMessage');
        if (!msgElement) {
            const message = document.createElement('div');
            message.id = 'kategoriEmptyMessage';
            message.style.cssText = 'margin-top: 8px; padding: 12px; background: #fef3c7; border: 1px solid #fbbf24; border-radius: 8px; font-size: 13px; color: #92400e;';
            message.innerHTML = `
                <i class="fa-solid fa-circle-info"></i> Belum ada kategori ${jenisText}.
                <button type="button" onclick="bukaModalKategoriDariForm('${jenisText}')" style="margin-left: 8px; padding: 4px 12px; background: #f59e0b; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 500;">
                    <i class="fa-solid fa-plus"></i> Tambah Kategori
                </button>
            `;
            kategoriSelect.parentElement.appendChild(message);
        } else {
            msgElement.innerHTML = `
                <i class="fa-solid fa-circle-info"></i> Belum ada kategori ${jenisText}.
                <button type="button" onclick="bukaModalKategoriDariForm('${jenisText}')" style="margin-left: 8px; padding: 4px 12px; background: #f59e0b; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 500;">
                    <i class="fa-solid fa-plus"></i> Tambah Kategori
                </button>
            `;
            msgElement.style.display = 'block';
        }
    } else {
        // Hapus pesan jika ada kategori
        const msgElement = document.getElementById('kategoriEmptyMessage');
        if (msgElement) {
            msgElement.style.display = 'none';
        }
        
        kategoriSelect.disabled = false;
        categories.forEach(kategori => {
            const option = document.createElement('option');
            option.value = kategori;
            option.textContent = kategori;
            kategoriSelect.appendChild(option);
        });
    }
}

// Fungsi untuk buka modal kategori dari form transaksi
function bukaModalKategoriDariForm(jenis) {
    tutupModal(); // Tutup modal transaksi dulu
    setTimeout(() => {
        bukaModalKategori(jenis);
    }, 100);
}

// Filter functions using UtilFilter
function updateOpsiNilai() {
    const periodIncome = document.getElementById('periodTypeIncome');
    const periodExpense = document.getElementById('periodTypeExpense');
    const valueSelectIncome = document.getElementById('valueSelectIncome');
    const valueSelectExpense = document.getElementById('valueSelectExpense');
    
    // Validasi element ada
    if (!periodIncome || !periodExpense || !valueSelectIncome || !valueSelectExpense) {
        console.error('Filter elements tidak ditemukan');
        return;
    }
    
    // Sinkronkan kedua select period
    const period = periodIncome.value || periodExpense.value || 'bulan';
    periodIncome.value = period;
    periodExpense.value = period;
    
    if(period === 'semua') {
        valueSelectIncome.style.display = 'none';
        valueSelectExpense.style.display = 'none';
        if(period !== window.currentPeriod) {
            terapkanFilter();
        }
        return;
    } else {
        valueSelectIncome.style.display = 'inline-block';
        valueSelectExpense.style.display = 'inline-block';
    }
    
    // Generate dan set options untuk kedua select
    const options = UtilFilter.buatOpsi(period);
    valueSelectIncome.innerHTML = options;
    valueSelectExpense.innerHTML = options;
    
    // Set value jika period sama dengan current period
    if(period === window.currentPeriod && window.currentValue) {
        setTimeout(() => {
            valueSelectIncome.value = window.currentValue;
            valueSelectExpense.value = window.currentValue;
        }, 0);
    }
}

function terapkanFilter() {
    const periodIncome = document.getElementById('periodTypeIncome');
    const periodExpense = document.getElementById('periodTypeExpense');
    const valueSelectIncome = document.getElementById('valueSelectIncome');
    const valueSelectExpense = document.getElementById('valueSelectExpense');
    
    if (!periodIncome || !periodExpense) {
        console.error('Period select tidak ditemukan');
        return;
    }
    
    const period = periodIncome.value || periodExpense.value || 'bulan';
    const value = (valueSelectIncome && valueSelectIncome.value) || (valueSelectExpense && valueSelectExpense.value) || '0';
    
    window.location.href = '?period=' + period + '&value=' + value;
}

// Show kategori detail modal
function tampilkanDetailKategori(kategori, jenis) {
    const modal = document.getElementById('modalKategoriDetail');
    const title = document.getElementById('kategoriDetailTitle');
    const content = document.getElementById('kategoriDetailContent');
    
    title.textContent = 'Transaksi - ' + kategori;
    content.innerHTML = '<p style="text-align: center; color: #94a3b8; padding: 20px;">Memuat data...</p>';
    modal.style.display = 'flex';
    
    const period = window.currentPeriod || 'bulan';
    const value = window.currentValue || '0';
    
    fetch('get_kategori_detail.php?kategori=' + encodeURIComponent(kategori) + '&period=' + period + '&value=' + value)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                content.innerHTML = '<p style="text-align: center; color: #94a3b8; padding: 20px;">Belum ada transaksi</p>';
                return;
            }
            
            let html = '<div class="transaction-list" style="display: flex; flex-direction: column; gap: 12px;">';
            data.forEach(trans => {
                const isIncome = trans.jenisTransaksi === 'Pemasukan';
                const iconClass = isIncome ? 'success' : 'danger';
                const icon = isIncome ? 'fa-arrow-down-long' : 'fa-arrow-up-long';
                const iconRotation = 'transform: rotate(45deg);';
                const prefix = isIncome ? '+' : '-';
                
                html += `
                    <div class="transaction-item" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8fafc; border-radius: 8px;">
                        <div class="transaction-icon ${iconClass}" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fa-solid ${icon}" style="${iconRotation}"></i>
                        </div>
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 4px 0; font-size: 14px;">${trans.keteranganTransaksi}</h4>
                            <p style="margin: 0; font-size: 12px; color: #64748b;">${new Date(trans.tanggalKeuangan).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})}</p>
                        </div>
                        <div style="text-align: right; display: flex; align-items: center; gap: 8px;">
                            <p class="transaction-amount ${iconClass}" style="margin: 0; font-weight: 600; margin-right: 12px;">${prefix}Rp ${Number(trans.transaksi).toLocaleString('id-ID')}</p>
                            <button onclick="bukaModalEditTransaksi('${trans.id_keuangan}')" class="btn-edit-transaksi" style="background: #3b82f6; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button onclick="hapusTransaksi('${trans.id_keuangan}')" class="btn-delete-transaksi" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            content.innerHTML = html;
        })
        .catch(error => {
            content.innerHTML = '<p style="text-align: center; color: #ef4444; padding: 20px;">Gagal memuat data</p>';
        });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modalTransaksi = document.getElementById('modalTransaksi');
    const modalKategori = document.getElementById('modalKategori');
    if (event.target == modalTransaksi) {
        tutupModal();
    } else if (event.target == modalKategori) {
        tutupModalKategori();
    }
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', function() {
    console.log('Keuangan.js loaded');
    
    // Initialize filter dengan delay untuk memastikan DOM ready
    setTimeout(() => {
        try {
            updateOpsiNilai();
        } catch(e) {
            console.error('Error initializing filter:', e);
        }
    }, 100);
    
    // Load subscriptions
    try {
        if (typeof muatLangganan === 'function') {
            muatLangganan();
        }
    } catch(e) {
        console.error('Error loading subscriptions:', e);
    }
    
    // Auto hide alert after 3 seconds
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    }
});
