

function muatLangganan() {
    console.log('Loading subscriptions...');
    fetch('keuangan.php?api=langganan')
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Subscription data:', data);
            if (data.sukses) {
                tampilkanLangganan(data.data, data.total);
            } else {
                console.error('API returned error:', data.pesan);
            }
        })
        .catch(error => console.error('Kesalahan memuat langganan:', error));
}

function tampilkanLangganan(subscriptions, total) {
    console.log('Menampilkan langganan:', subscriptions, 'Total:', total);
    const listContainer = document.getElementById('subscriptionList');
    const totalElement = document.getElementById('totalLangganan');
    
    if (!listContainer || !totalElement) {
        console.error('Elemen tidak ditemukan! subscriptionList:', listContainer, 'totalLangganan:', totalElement);
        return;
    }
    
    totalElement.textContent = 'Rp ' + parseInt(total || 0).toLocaleString('id-ID');
    
    if (subscriptions.length === 0) {
        listContainer.innerHTML = `
            <div class="empty-state" style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                <i class="fa-solid fa-calendar-check" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                <p style="margin: 0;">Belum ada langganan</p>
            </div>
        `;
        return;
    }
    
    const iconColors = {
        'youtube': '#FF0000',
        'netflix': '#E50914',
        'spotify': '#1DB954',
        'apple': '#000000',
        'google': '#4285F4',
        'microsoft': '#00A4EF',
        'amazon': '#FF9900',
        'discord': '#5865F2'
    };
    
    let html = '';
    subscriptions.forEach(sub => {
        let iconColor = '#64748b';

        const iconName = sub.icon || 'fa-circle';
        for (const [key, color] of Object.entries(iconColors)) {
            if (iconName.includes(key)) {
                iconColor = color;
                break;
            }
        }
        
        html += `
            <div class="subscription-item" style="display: flex; align-items: center; gap: 16px; padding: 16px; background: #f8fafc; border-radius: 12px; margin-bottom: 12px;">
                <div class="sub-icon" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: white; font-size: 24px; color: ${iconColor};">
                    <i class="fa-brands ${iconName}"></i>
                </div>
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight: 600; color: #1e293b;">${sub.nama_langganan || 'Tidak ada nama'}</h4>
                    <p style="margin: 0; font-size: 13px; color: #3b82f6;">Rp ${parseInt(sub.harga_bulanan || 0).toLocaleString('id-ID')}/bulan</p>
                </div>
                <button onclick="hapusLangganan('${sub.id_langganan}')" class="btn-delete-sub" style="background: #fee2e2; color: #ef4444; border: none; width: 36px; height: 36px; border-radius: 8px; cursor: pointer; transition: all 0.3s;">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;
    });
    
    listContainer.innerHTML = html;
}

function bukaModalLangganan() {
    const existing = document.querySelector('.modal-overlay');
    if (existing) existing.remove();
    
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.style.cssText = 'display: flex; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;';
    
    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content';
    modalContent.style.cssText = 'max-width: 520px; background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative; z-index: 1001;';
    
    modalContent.innerHTML = `
        <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 16px 16px 0 0; padding: 24px; display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h2 style="color: white; margin: 0 0 4px 0; font-size: 20px;">Tambah Langganan Baru</h2>
                <p style="font-size: 13px; opacity: 0.9; margin: 0;">Kelola langganan aplikasi bulanan Anda</p>
            </div>
            <button type="button" id="btnCloseModal" style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 32px; height: 32px;">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <form id="subscriptionForm" style="padding: 28px;">
            <div style="margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1e293b;">
                    <i class="fa-solid fa-tag" style="color: #667eea;"></i>
                    <span>Nama Langganan</span>
                </label>
                <input type="text" name="nama_langganan" id="inputNama" required placeholder="Contoh: Netflix, Spotify, YouTube Premium" 
                       style="width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; box-sizing: border-box;">
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1e293b;">
                    <i class="fa-solid fa-icons" style="color: #667eea;"></i>
                    <span>Pilih Icon</span>
                </label>
                <select name="icon" id="inputIcon" required style="width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; cursor: pointer; box-sizing: border-box;">
                    <option value="fa-youtube">YouTube Premium</option>
                    <option value="fa-film">Netflix</option>
                    <option value="fa-spotify">Spotify</option>
                    <option value="fa-apple">Apple Music</option>
                    <option value="fa-google">Google One</option>
                    <option value="fa-microsoft">Microsoft 365</option>
                    <option value="fa-amazon">Amazon Prime</option>
                    <option value="fa-discord">Discord Nitro</option>
                    <option value="fa-steam">Steam</option>
                    <option value="fa-playstation">PlayStation Plus</option>
                    <option value="fa-xbox">Xbox Game Pass</option>
                    <option value="fa-tv">TV/Streaming</option>
                    <option value="fa-music">Music</option>
                    <option value="fa-cloud">Cloud Storage</option>
                    <option value="fa-gamepad">Gaming</option>
                </select>
            </div>
            <div style="margin-bottom: 28px;">
                <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1e293b;">
                    <i class="fa-solid fa-money-bill-wave" style="color: #667eea;"></i>
                    <span>Biaya per Bulan</span>
                </label>
                <input type="text" name="harga_bulanan" id="inputHarga" required placeholder="50.000" oninput="formatRupiah(this)"
                       style="width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; box-sizing: border-box;">
                <input type="hidden" name="harga_bulanan_raw" id="inputHarga_raw">
                <p style="font-size: 12px; color: #94a3b8; margin: 8px 0 0 0;">
                    <i class="fa-solid fa-info-circle"></i> Akan otomatis dipotong setiap tanggal 1
                </p>
            </div>
            <div style="display: flex; gap: 12px; padding-top: 20px; margin-top: 20px; border-top: 1px solid #f1f5f9;">
                <button type="button" id="btnBatal" style="flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; background: #f1f5f9; color: #64748b; border: none; font-family: 'Poppins', sans-serif;">
                    <i class="fa-solid fa-times"></i> Batal
                </button>
                <button type="button" id="btnSimpan" style="flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 600; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); cursor: pointer; border: none; color: white; font-family: 'Poppins', sans-serif; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);">
                    <i class="fa-solid fa-check"></i> Simpan
                </button>
            </div>
        </form>
    `;
    
    modal.appendChild(modalContent);
    document.body.appendChild(modal);

    document.getElementById('btnCloseModal').onclick = () => modal.remove();
    document.getElementById('btnBatal').onclick = () => modal.remove();
    
    document.getElementById('btnSimpan').onclick = function() {
        const nama = document.getElementById('inputNama').value.trim();
        const icon = document.getElementById('inputIcon').value;
        const hargaRaw = document.getElementById('inputHarga_raw').value || document.getElementById('inputHarga').value.replace(/[^0-9]/g, '');
        
        if (!nama || !icon || !hargaRaw) {
            window.alert('Mohon lengkapi semua field!');
            return;
        }
        
        const harga = parseInt(hargaRaw);
        if (isNaN(harga) || harga <= 0) {
            window.alert('Harga harus lebih dari 0!');
            return;
        }
        
        const btnSimpan = this;
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        
        const formData = new FormData();
        formData.append('action', 'tambah');
        formData.append('nama_langganan', nama);
        formData.append('icon', icon);
        formData.append('harga_bulanan', harga);
        
        fetch('keuangan.php?api=langganan', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Error parsing JSON:', text);
                throw new Error('Response tidak valid: ' + text);
            }
            if (data.sukses) {
                modal.remove();
                muatLangganan();
                setTimeout(() => window.alert('✓ Langganan berhasil ditambahkan!'), 100);
            } else {
                window.alert('Kesalahan: ' + data.pesan);
                btnSimpan.disabled = false;
                btnSimpan.innerHTML = '<i class="fa-solid fa-check"></i> Simpan';
            }
        })
        .catch(error => {
            console.error('Kesalahan:', error);
            window.alert('Terjadi kesalahan: ' + error.message);
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="fa-solid fa-check"></i> Simpan';
        });
    };
    
    modal.onclick = (e) => {
        if (e.target === modal) modal.remove();
    };
}

function hapusLangganan(id) {
    if (!id) {
        console.error('ID langganan tidak valid');
        return;
    }
    
    if (!window.confirm('Yakin ingin menghapus langganan ini?')) return;
    
    const formData = new FormData();
    formData.append('action', 'hapus');
    formData.append('id_langganan', id);
    
    fetch('keuangan.php?api=langganan', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sukses) {
            muatLangganan();
            setTimeout(() => window.alert('✓ Langganan berhasil dihapus!'), 100);
        } else {
            window.alert('Kesalahan: ' + data.pesan);
        }
    })
    .catch(error => {
        console.error('Kesalahan:', error);
        window.alert('Terjadi kesalahan saat menghapus langganan');
    });
}

function bayarSekarang() {
    if (!window.confirm('Yakin ingin membayar tagihan langganan bulanan sekarang? Total akan masuk ke pengeluaran.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('api', 'charge');
    
    fetch('keuangan.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.sukses) {
            window.alert('✓ Tagihan langganan berhasil dibayar dan masuk ke pengeluaran!');
            window.location.reload();
        } else {
            window.alert('Kesalahan: ' + data.pesan);
        }
    })
    .catch(error => {
        console.error('Kesalahan:', error);
        window.alert('Terjadi kesalahan: ' + error.message);
    });
}
