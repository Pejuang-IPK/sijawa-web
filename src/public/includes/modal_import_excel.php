<div id="importModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Import Jadwal (XLSX)</h3>
            <button class="close-btn" onclick="closeModal('importModal')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form action="" method="POST" enctype="multipart/form-data">
            
            <div style="background: #f0f9ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; color: #0369a1;">
                <strong>Catatan Penting:</strong><br>
                File yang diupload harus berformat <b>.xlsx</b>.<br>
                Jika dari Excel, pilih <b>Save As > XLSX (Comma delimited)</b>.
            </div>

            <div class="form-group">
                <label>Pilih File XLSX</label>
                <input type="file" name="file_excel" class="form-input" accept=".xlsx" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('importModal')">Batal</button>
                <button type="submit" name="submit_import" class="btn-save">Upload & Import</button>
            </div>
        </form>
    </div>
</div>