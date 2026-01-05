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
    
    // Mobile Menu Toggle
    initMobileMenu();
});

// Mobile Menu Functions
function initMobileMenu() {
    // Create mobile menu button if not exists
    const pageHeader = document.querySelector('.page-header');
    if (!pageHeader) return;
    
    // Check if button already exists
    if (document.querySelector('.mobile-menu-btn')) return;
    
    const menuBtn = document.createElement('button');
    menuBtn.className = 'mobile-menu-btn';
    menuBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';
    menuBtn.style.cssText = 'display: none; position: fixed; left: 16px; top: 16px; z-index: 1001; background: white; border: 1px solid #e2e8f0; width: 44px; height: 44px; border-radius: 12px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
    
    // Show button on mobile
    const mediaQuery = window.matchMedia('(max-width: 768px)');
    function handleMediaQuery(e) {
        menuBtn.style.display = e.matches ? 'flex' : 'none';
        menuBtn.style.alignItems = 'center';
        menuBtn.style.justifyContent = 'center';
    }
    mediaQuery.addListener(handleMediaQuery);
    handleMediaQuery(mediaQuery);
    
    document.body.appendChild(menuBtn);
    
    // Toggle sidebar
    menuBtn.addEventListener('click', function() {
        const sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            sidebar.classList.toggle('active');
            
            // Create overlay if doesn't exist
            let overlay = document.querySelector('.sidebar-overlay');
            if (sidebar.classList.contains('active')) {
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.className = 'sidebar-overlay';
                    overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999;';
                    document.body.appendChild(overlay);
                    
                    overlay.addEventListener('click', function() {
                        sidebar.classList.remove('active');
                        overlay.remove();
                    });
                }
            } else {
                if (overlay) overlay.remove();
            }
        }
    });
}
