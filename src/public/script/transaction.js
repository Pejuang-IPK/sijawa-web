

function bukaModalEditTransaksi(id_keuangan) {
    fetch('keuangan.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ajax=1&action=get&id_keuangan=' + id_keuangan
    })
    .then(response => response.json())
    .then(result => {
        if (result.sukses) {
            tampilkanModalEdit(result.data);
        } else {
            window.alert('Gagal mengambil data transaksi: ' + result.pesan);
        }
    })
    .catch(error => {
        console.error('Kesalahan:', error);
        window.alert('Terjadi kesalahan saat mengambil data transaksi');
    });
}

function tampilkanModalEdit(transaksi) {
    const modalOverlay = PembantuModal.buatOverlay();
    const modalContent = document.createElement('div');
    modalContent.style.cssText = 'background: white; border-radius: 12px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto;';

    const modalHeader = PembantuModal.buatHeader('Edit Transaksi', () => modalOverlay.remove());
    const modalBody = document.createElement('div');
    modalBody.style.padding = '24px';
    
    modalBody.innerHTML = `
        <form id="formEditTransaksi">
            <input type="hidden" id="editIdKeuangan" value="${transaksi.id_keuangan}">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">Jumlah</label>
                <input type="number" id="editJumlah" value="${transaksi.transaksi}" required
                    style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">Keterangan</label>
                <input type="text" id="editKeterangan" value="${transaksi.keteranganTransaksi}" required
                    style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">Jenis Transaksi</label>
                <select id="editJenisTransaksi" required onchange="updateEditKategoriOptions()"
                    style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    <option value="">Pilih jenis</option>
                    <option value="Pemasukan" ${transaksi.jenisTransaksi === 'Pemasukan' ? 'selected' : ''}>Pemasukan</option>
                    <option value="Pengeluaran" ${transaksi.jenisTransaksi === 'Pengeluaran' ? 'selected' : ''}>Pengeluaran</option>
                </select>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #334155;">Kategori</label>
                <select id="editKategoriTransaksi" required
                    style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    <option value="">Pilih kategori</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="button" onclick="this.closest('.modal-overlay').remove()"
                    style="flex: 1; padding: 12px; background: #e2e8f0; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; color: #334155;">
                    Batal
                </button>
                <button type="button" id="btnUpdateTransaksi"
                    style="flex: 1; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 8px; cursor: pointer; font-weight: 500; color: white;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    `;
    
    modalContent.appendChild(modalHeader);
    modalContent.appendChild(modalBody);
    modalOverlay.appendChild(modalContent);
    document.body.appendChild(modalOverlay);
    
    updateOpsiKategoriEdit();
    document.getElementById('editKategoriTransaksi').value = transaksi.kategoriTransaksi;
    
    document.getElementById('btnUpdateTransaksi').onclick = updateTransaksi;
}

function updateOpsiKategoriEdit() {
    const jenisTransaksi = document.getElementById('editJenisTransaksi').value;
    const kategoriSelect = document.getElementById('editKategoriTransaksi');
    const currentKategori = kategoriSelect.value;
    
    kategoriSelect.innerHTML = '<option value="">Pilih kategori</option>';
    
    if(!jenisTransaksi) {
        kategoriSelect.innerHTML = '<option value="">Pilih jenis terlebih dahulu</option>';
        return;
    }
    
    let categories = jenisTransaksi === 'Pemasukan' ? (window.kategoriPemasukan || []) : (window.kategoriPengeluaran || []);
    
    if(!categories || categories.length === 0) {
        kategoriSelect.innerHTML = '<option value="">Belum ada kategori</option>';
    } else {
        categories.forEach(kategori => {
            const option = document.createElement('option');
            option.value = kategori;
            option.textContent = kategori;
            if (kategori === currentKategori) {
                option.selected = true;
            }
            kategoriSelect.appendChild(option);
        });
    }
}

function updateEditKategoriOptions() {
    updateOpsiKategoriEdit();
}

function updateTransaksi() {
    const btnUpdate = document.getElementById('btnUpdateTransaksi');
    btnUpdate.disabled = true;
    btnUpdate.textContent = 'Menyimpan...';
    
    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('action', 'edit');
    formData.append('id_keuangan', document.getElementById('editIdKeuangan').value);
    formData.append('transaksi', document.getElementById('editJumlah').value);
    formData.append('keteranganTransaksi', document.getElementById('editKeterangan').value);
    formData.append('jenisTransaksi', document.getElementById('editJenisTransaksi').value);
    formData.append('kategoriTransaksi', document.getElementById('editKategoriTransaksi').value);
    
    fetch('keuangan.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sukses) {
            document.querySelector('.modal-overlay').remove();
            window.alert('✓ Transaksi berhasil diperbarui!');
            window.location.reload();
        } else {
            btnUpdate.disabled = false;
            btnUpdate.textContent = 'Simpan Perubahan';
            window.alert('Kesalahan: ' + data.pesan);
        }
    })
    .catch(error => {
        console.error('Kesalahan:', error);
        btnUpdate.disabled = false;
        btnUpdate.textContent = 'Simpan Perubahan';
        window.alert('Terjadi kesalahan saat mengupdate transaksi');
    });
}

function hapusTransaksi(id_keuangan) {
    if (!window.confirm('Yakin ingin menghapus transaksi ini?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('ajax', '1');
    formData.append('action', 'hapus');
    formData.append('id_keuangan', id_keuangan);
    
    fetch('keuangan.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sukses) {
            window.alert('✓ Transaksi berhasil dihapus!');
            window.location.reload();
        } else {
            window.alert('Kesalahan: ' + data.pesan);
        }
    })
    .catch(error => {
        console.error('Kesalahan:', error);
        window.alert('Terjadi kesalahan saat menghapus transaksi');
    });
}

function bukaModalDetailTransaksi(id_keuangan) {
    console.log('Membuka detail transaksi:', id_keuangan);
    fetch('keuangan.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ajax=1&action=get&id_keuangan=' + id_keuangan
    })
    .then(response => response.text())
    .then(text => {
        const result = JSON.parse(text);
        if (result.sukses) {
            tampilkanModalDetailTransaksi(result.data);
        } else {
            window.alert('Gagal mengambil data transaksi: ' + result.pesan);
        }
    })
    .catch(error => {
        console.error('Kesalahan:', error);
        window.alert('Terjadi kesalahan saat mengambil data transaksi');
    });
}

function tampilkanModalDetailTransaksi(transaksi) {
    const isIncome = transaksi.jenisTransaksi === 'Pemasukan';
    const amountColor = isIncome ? '#10b981' : '#ef4444';
    const icon = isIncome ? '↙' : '↗';
    const iconBg = isIncome ? '#d1fae5' : '#fee2e2';
    const iconColor = isIncome ? '#10b981' : '#ef4444';
    
    const modalOverlay = PembantuModal.buatOverlay();
    const formattedDate = PembantuModal.formatTanggal(transaksi.tanggalKeuangan);
    const formattedAmount = PembantuModal.formatRupiah(transaksi.transaksi);
    
    modalOverlay.innerHTML = `
        <div style="background: white; border-radius: 16px; padding: 32px; width: 90%; max-width: 400px; text-align: center; position: relative;">
            <button onclick="this.closest('.modal-overlay').remove()" style="position: absolute; top: 16px; right: 16px; background: #f3f4f6; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 18px; color: #6b7280;">×</button>
            
            <div style="width: 64px; height: 64px; background: ${iconBg}; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 32px; font-weight: bold; color: ${iconColor};">
                ${icon}
            </div>
            
            <h3 style="margin: 0 0 8px 0; font-size: 20px; color: #1e293b;">${transaksi.keteranganTransaksi}</h3>
            <p style="margin: 0 0 16px 0; font-size: 14px; color: #64748b;">${formattedDate}</p>
            <p style="margin: 0 0 8px 0; font-size: 13px; color: #94a3b8;">${transaksi.kategoriTransaksi}</p>
            <p style="margin: 0 0 32px 0; font-size: 28px; font-weight: 600; color: ${amountColor};">${formattedAmount}</p>
            
            <div style="display: flex; gap: 12px;">
                <button onclick="this.closest('.modal-overlay').remove(); bukaModalEditTransaksi('${transaksi.id_keuangan}');" style="flex: 1; padding: 12px; background: #3b82f6; border: none; border-radius: 8px; color: white; font-weight: 500; cursor: pointer; font-size: 15px;">
                    Edit
                </button>
                <button onclick="this.closest('.modal-overlay').remove(); hapusTransaksi('${transaksi.id_keuangan}');" style="flex: 1; padding: 12px; background: #ef4444; border: none; border-radius: 8px; color: white; font-weight: 500; cursor: pointer; font-size: 15px;">
                    Hapus
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modalOverlay);
}
