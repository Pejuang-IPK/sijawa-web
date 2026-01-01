// Format currency input
function formatRupiah(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    if (value === '') {
        input.value = '';
        return;
    }
    let formatted = parseInt(value).toLocaleString('id-ID');
    input.value = formatted;
    
    const rawInput = document.getElementById(input.id + '_raw');
    if (rawInput) {
        rawInput.value = value;
    }
}

function persiapkanKirimForm(event) {
    const displayInput = document.querySelector('input[name="transaksi_display"]');
    const rawInput = document.querySelector('input[name="transaksi"]');
    if (displayInput && rawInput) {
        rawInput.value = displayInput.value.replace(/[^0-9]/g, '');
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
    
    if(!jenisTransaksi) {
        kategoriSelect.innerHTML = '<option value="">Pilih jenis terlebih dahulu</option>';
        return;
    }
    
    let categories = jenisTransaksi === 'Pemasukan' ? window.kategoriPemasukan : window.kategoriPengeluaran;
    
    if(categories.length === 0) {
        kategoriSelect.innerHTML = '<option value="">Belum ada kategori, ketik manual</option>';
        const input = document.createElement('option');
        input.value = 'custom';
        input.textContent = '+ Tambah kategori baru';
        kategoriSelect.appendChild(input);
    } else {
        categories.forEach(kategori => {
            const option = document.createElement('option');
            option.value = kategori;
            option.textContent = kategori;
            kategoriSelect.appendChild(option);
        });
    }
}

// Filter functions using UtilFilter
function updateOpsiNilai() {
    const periodIncome = document.getElementById('periodTypeIncome');
    const periodExpense = document.getElementById('periodTypeExpense');
    const valueSelectIncome = document.getElementById('valueSelectIncome');
    const valueSelectExpense = document.getElementById('valueSelectExpense');
    
    // Sinkronkan kedua select period
    const period = periodIncome.value || periodExpense.value;
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
    if(period === window.currentPeriod) {
        valueSelectIncome.value = window.currentValue;
        valueSelectExpense.value = window.currentValue;
    }
}

function terapkanFilter() {
    const period = document.getElementById('periodTypeIncome').value || document.getElementById('periodTypeExpense').value;
    const value = document.getElementById('valueSelectIncome').value || document.getElementById('valueSelectExpense').value || '0';
    
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
    updateOpsiNilai();
    muatLangganan();
    
    // Auto hide alert after 3 seconds
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    }
});
