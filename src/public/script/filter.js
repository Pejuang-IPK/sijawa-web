// Filter utility functions shared between pages

const UtilFilter = {
    // Current filter values
    currentPeriod: '',
    currentValue: '',
    
    // Initialize filter with values from PHP
    inisialisasi(period, value) {
        this.currentPeriod = period;
        this.currentValue = value;
    },
    
    // Update value options based on period type
    updateOpsiNilai(periodSelectId, valueSelectId) {
        const periodElement = document.getElementById(periodSelectId);
        const valueSelect = document.getElementById(valueSelectId);
        
        if (!periodElement || !valueSelect) {
            console.error('Element tidak ditemukan:', periodSelectId, valueSelectId);
            return;
        }
        
        const period = periodElement.value || 'bulan';
        
        if (period === 'semua') {
            valueSelect.style.display = 'none';
            if (period !== this.currentPeriod) {
                this.terapkanFilter(periodSelectId, valueSelectId);
            }
            return;
        } else {
            valueSelect.style.display = 'inline-block';
        }
        
        valueSelect.innerHTML = this.buatOpsi(period);
        
        // Set value setelah options di-render
        if (period === this.currentPeriod && this.currentValue) {
            // Gunakan setTimeout untuk memastikan DOM sudah terupdate
            setTimeout(() => {
                valueSelect.value = this.currentValue;
            }, 0);
        }
    },
    
    // Generate options based on period type
    buatOpsi(period) {
        let options = '';
        
        switch(period) {
            case 'hari':
                options = '<option value="0">Hari Ini</option>';
                options += '<option value="1">Kemarin</option>';
                for(let i = 2; i <= 30; i++) {
                    options += `<option value="${i}">${i} Hari Lalu</option>`;
                }
                break;
            
            case 'minggu':
                options = '<option value="0">Minggu Ini</option>';
                options += '<option value="1">Minggu Lalu</option>';
                for(let i = 2; i <= 12; i++) {
                    options += `<option value="${i}">${i} Minggu Lalu</option>`;
                }
                break;
            
            case 'bulan':
                const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const currentDate = new Date();
                for(let i = 0; i < 24; i++) {
                    const d = new Date(currentDate.getFullYear(), currentDate.getMonth() - i, 1);
                    const monthName = months[d.getMonth()];
                    const year = d.getFullYear();
                    options += `<option value="${i}">${monthName} ${year}</option>`;
                }
                break;
            
            case 'tahun':
                options = '<option value="0">Tahun Ini</option>';
                options += '<option value="1">Tahun Lalu</option>';
                const currentYear = new Date().getFullYear();
                for(let i = 2; i <= 10; i++) {
                    const year = currentYear - i;
                    options += `<option value="${i}">${year}</option>`;
                }
                break;
        }
        
        return options;
    },
    
    // Apply filter and reload page
    terapkanFilter(periodSelectId, valueSelectId) {
        const period = document.getElementById(periodSelectId).value;
        const value = document.getElementById(valueSelectId).value || '0';
        
        window.location.href = '?period=' + period + '&value=' + value;
    }
};

// Search functionality for transactions
function cariTransaksi(searchInputId, transactionItemClass, monthGroupClass) {
    const searchTerm = document.getElementById(searchInputId).value.toLowerCase();
    const transactionItems = document.querySelectorAll(`.${transactionItemClass}`);
    const monthGroups = document.querySelectorAll(`.${monthGroupClass}`);
    
    transactionItems.forEach(item => {
        const keterangan = item.querySelector('.transaction-details h4').textContent.toLowerCase();
        const kategori = item.querySelector('.transaction-details p').textContent.toLowerCase();
        const amount = item.querySelector('.transaction-amount').textContent.toLowerCase();
        
        if (keterangan.includes(searchTerm) || kategori.includes(searchTerm) || amount.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
    
    // Hide month groups if all transactions are hidden
    monthGroups.forEach(group => {
        const visibleTransactions = group.querySelectorAll(`.${transactionItemClass}[style*="display: flex"]`);
        if (visibleTransactions.length === 0) {
            group.style.display = 'none';
        } else {
            group.style.display = 'block';
        }
    });
}
