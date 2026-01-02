<div id="editModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Ubah Jadwal</h3>
            <button class="close-btn" type="button" onclick="closeModal('editModal')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form action="" method="POST">
            
            <input type="hidden" name="id_jadwal" id="edit_id">

            <div class="form-group">
                <label>Hari</label>
                <select name="hari" id="edit_hari" class="form-input" required>
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
                <input type="text" name="matkul" id="edit_matkul" class="form-input" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Ruangan</label>
                    <input type="text" name="ruangan" id="edit_ruangan" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>SKS</label>
                    <input type="number" name="sks" id="edit_sks" class="form-input" min="1" max="6">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Jam Mulai</label>
                    <input type="time" name="jam_mulai" id="edit_mulai" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Jam Selesai</label>
                    <input type="time" name="jam_selesai" id="edit_selesai" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Nama Dosen</label>
                <input type="text" name="dosen" id="edit_dosen" class="form-input">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" name="submit_edit" class="btn-save">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>