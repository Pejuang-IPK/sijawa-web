<!-- Modal Tambah Transaksi -->
<div id="modalTransaksi" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Tambah Transaksi</h2>
            <span class="close" onclick="tutupModal()">&times;</span>
        </div>
        
        <form method="POST" action="" onsubmit="prepareFormSubmit(event)">
            <input type="hidden" name="action" value="tambah">
            
            <div class="form-group">
                <label for="jenisTransaksi">Jenis Transaksi</label>
                <select name="jenisTransaksi" id="jenisTransaksi" required onchange="updateOpsiKategori()">
                    <option value="">Pilih Jenis</option>
                    <option value="Pemasukan">Pemasukan</option>
                    <option value="Pengeluaran">Pengeluaran</option>
                </select>
            </div>

            <div class="form-group">
                <label for="kategoriTransaksi">Kategori</label>
                <select name="kategoriTransaksi" id="kategoriTransaksi" required>
                    <option value="">Pilih kategori terlebih dahulu</option>
                </select>
            </div>

            <div class="form-group">
                <label for="keteranganTransaksi">Keterangan</label>
                <input type="text" name="keteranganTransaksi" id="keteranganTransaksi" placeholder="Contoh: Gaji bulanan, Makan siang, dll" required>
            </div>

            <div class="form-group">
                <label for="transaksi">Jumlah (Rp)</label>
                <input type="text" name="transaksi_display" id="transaksi" placeholder="0" required oninput="formatRupiah(this)">
                <input type="hidden" name="transaksi" id="transaksi_raw">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="tutupModal()">Batal</button>
                <button type="submit" class="btn-submit">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div id="modalKategori" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Tambah Kategori <span id="jenisKategoriText"></span></h2>
            <span class="close" onclick="tutupModalKategori()">&times;</span>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="tambah_kategori">
            <input type="hidden" name="jenisTransaksi" id="jenisKategoriInput">
            
            <div class="form-group">
                <label for="namaKategori">Nama Kategori</label>
                <input type="text" name="namaKategori" id="namaKategori" placeholder="Contoh: Transportasi, Gaji, dll" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="tutupModalKategori()">Batal</button>
                <button type="submit" class="btn-submit">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Kategori -->
<div id="modalKategoriDetail" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2 id="kategoriDetailTitle">Detail Kategori</h2>
            <span class="close" onclick="tutupModalDetailKategori()">&times;</span>
        </div>
        
        <div id="kategoriDetailContent" style="max-height: 400px; overflow-y: auto;">
            <p style="text-align: center; color: #94a3b8; padding: 20px;">Memuat data...</p>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="tutupModalDetailKategori()">Tutup</button>
        </div>
    </div>
</div>
