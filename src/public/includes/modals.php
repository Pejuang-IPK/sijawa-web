<div id="modalTransaksi" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div class="modal-content" style="max-width: 520px; background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative; z-index: 1001;">
        <div class="modal-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important; color: white !important; border-radius: 16px 16px 0 0 !important; padding: 24px !important; display: flex !important; justify-content: space-between !important; align-items: flex-start !important; border-bottom: none !important;">
            <div>
                <h2 style="color: white !important; margin: 0 0 4px 0 !important; font-size: 20px !important;">Tambah Transaksi</h2>
                <p style="font-size: 13px; opacity: 0.9; margin: 0;">Catat pemasukan atau pengeluaran Anda</p>
            </div>
            <span class="close" onclick="tutupModal()" style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 32px; height: 32px;">&times;</span>
        </div>
        
        <form method="POST" action="" onsubmit="return persiapkanKirimForm(event)" style="padding: 28px;">
            <input type="hidden" name="action" value="tambah">
            
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="jenisTransaksi" style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1e293b;">
                    <i class="fa-solid fa-exchange-alt" style="color: #3b82f6;"></i>
                    <span>Jenis Transaksi</span>
                </label>
                <select name="jenisTransaksi" id="jenisTransaksi" required onchange="updateOpsiKategori()" style="width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; cursor: pointer; box-sizing: border-box;">
                    <option value="">Pilih Jenis</option>
                    <option value="Pemasukan">Pemasukan</option>
                    <option value="Pengeluaran">Pengeluaran</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="kategoriTransaksi" style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1e293b;">
                    <i class="fa-solid fa-tag" style="color: #3b82f6;"></i>
                    <span>Kategori</span>
                </label>
                <select name="kategoriTransaksi" id="kategoriTransaksi" required style="width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; cursor: pointer; box-sizing: border-box;">
                    <option value="">Pilih kategori terlebih dahulu</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label for="keteranganTransaksi" style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1e293b;">
                    <i class="fa-solid fa-file-alt" style="color: #3b82f6;"></i>
                    <span>Keterangan</span>
                </label>
                <input type="text" name="keteranganTransaksi" id="keteranganTransaksi" placeholder="Contoh: Gaji bulanan, Makan siang, dll" required style="width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; box-sizing: border-box;">
            </div>

            <div class="form-group" style="margin-bottom: 28px;">
                <label for="transaksi" style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1e293b;">
                    <i class="fa-solid fa-rupiah-sign" style="color: #3b82f6;"></i>
                    <span>Jumlah (Rp)</span>
                </label>
                <input type="text" name="transaksi_display" id="transaksi" placeholder="0" required oninput="formatRupiah(this)" style="width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; box-sizing: border-box;">
                <input type="hidden" name="transaksi" id="transaksi_raw">
            </div>

            <div class="modal-footer" style="display: flex; gap: 12px; padding-top: 20px; margin-top: 20px; border-top: 1px solid #f1f5f9;">
                <button type="button" class="btn-cancel" onclick="tutupModal()" style="flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; background: #f1f5f9; color: #64748b; border: none; font-family: 'Poppins', sans-serif;">
                    <i class="fa-solid fa-times"></i> Batal
                </button>
                <button type="submit" class="btn-submit" style="flex: 1 !important; padding: 14px !important; border-radius: 12px !important; font-size: 14px !important; font-weight: 600 !important; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important; cursor: pointer !important; border: none !important; color: white !important; font-family: 'Poppins', sans-serif !important; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important;">
                    <i class="fa-solid fa-check"></i> Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalKategori" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div class="modal-content" style="max-width: 520px; background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative; z-index: 1001;">
        <div class="modal-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important; color: white !important; border-radius: 16px 16px 0 0 !important; padding: 24px !important; display: flex !important; justify-content: space-between !important; align-items: flex-start !important; border-bottom: none !important;">
            <div>
                <h2 style="color: white !important; margin: 0 0 4px 0 !important; font-size: 20px !important;">Tambah Kategori <span id="jenisKategoriText"></span></h2>
                <p style="font-size: 13px; opacity: 0.9; margin: 0;">Buat kategori baru untuk transaksi</p>
            </div>
            <span class="close" onclick="tutupModalKategori()" style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 32px; height: 32px;">&times;</span>
        </div>
        
        <form method="POST" action="" style="padding: 28px;">
            <input type="hidden" name="action" value="tambah_kategori">
            <input type="hidden" name="jenisTransaksi" id="jenisKategoriInput">
            
            <div class="form-group" style="margin-bottom: 28px;">
                <label for="namaKategori" style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; font-weight: 600; color: #1e293b;">
                    <i class="fa-solid fa-tag" style="color: #3b82f6;"></i>
                    <span>Nama Kategori</span>
                </label>
                <input type="text" name="namaKategori" id="namaKategori" placeholder="Contoh: Transportasi, Gaji, dll" required style="width: 100%; padding: 14px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-family: 'Poppins', sans-serif; box-sizing: border-box;">
            </div>

            <div class="modal-footer" style="display: flex; gap: 12px; padding-top: 20px; margin-top: 20px; border-top: 1px solid #f1f5f9;">
                <button type="button" class="btn-cancel" onclick="tutupModalKategori()" style="flex: 1; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; background: #f1f5f9; color: #64748b; border: none; font-family: 'Poppins', sans-serif;">
                    <i class="fa-solid fa-times"></i> Batal
                </button>
                <button type="submit" class="btn-submit" style="flex: 1 !important; padding: 14px !important; border-radius: 12px !important; font-size: 14px !important; font-weight: 600 !important; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important; cursor: pointer !important; border: none !important; color: white !important; font-family: 'Poppins', sans-serif !important; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important;">
                    <i class="fa-solid fa-check"></i> Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalKategoriDetail" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
    <div class="modal-content" style="max-width: 600px; background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative; z-index: 1001;">
        <div class="modal-header" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important; color: white !important; border-radius: 16px 16px 0 0 !important; padding: 24px !important; display: flex !important; justify-content: space-between !important; align-items: flex-start !important; border-bottom: none !important;">
            <div>
                <h2 id="kategoriDetailTitle" style="color: white !important; margin: 0 0 4px 0 !important; font-size: 20px !important;">Detail Kategori</h2>
                <p style="font-size: 13px; opacity: 0.9; margin: 0;">Rincian transaksi per kategori</p>
            </div>
            <span class="close" onclick="tutupModalDetailKategori()" style="background: transparent; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 32px; height: 32px;">&times;</span>
        </div>
        
        <div id="kategoriDetailContent" style="max-height: 400px; overflow-y: auto; padding: 28px;">
            <p style="text-align: center; color: #94a3b8; padding: 20px;">Memuat data...</p>
        </div>

        <div class="modal-footer" style="padding: 20px 28px; border-top: 1px solid #f1f5f9;">
            <button type="button" class="btn-cancel" onclick="tutupModalDetailKategori()" style="width: 100%; padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; background: #f1f5f9; color: #64748b; border: none; font-family: 'Poppins', sans-serif;">
                <i class="fa-solid fa-times"></i> Tutup
            </button>
        </div>
    </div>
</div>
