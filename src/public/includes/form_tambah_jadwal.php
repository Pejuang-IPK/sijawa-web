<div id="addModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tambah Jadwal Baru</h3>
            <button class="close-btn" onclick="closeModal('addModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <form action="" method="POST">
            
            <div class="form-group">
                <label>Hari</label>
                <select name="hari" class="form-input" required>
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                </select>
            </div>

            <div class="form-group">
                <label>Mata Kuliah</label>
                <input type="text" name="matkul" class="form-input" placeholder="Contoh: Pemrograman Web" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ruangan</label>
                    <input type="text" name="ruangan" class="form-input" placeholder="R. 3.04" required>
                </div>
                <div class="form-group">
                    <label>SKS</label>
                    <input type="number" name="sks" class="form-input" placeholder="3" min="1" max="6">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Nama Dosen</label>
                <input type="text" name="dosen" class="form-input" placeholder="Dr. Budi Santoso">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Batal</button>
                <button type="submit" name="submit_tambah" class="btn-save">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>