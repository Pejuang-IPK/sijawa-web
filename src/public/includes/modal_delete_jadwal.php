<div id="deleteModal" class="modal-overlay" style="display:none;">
    <div class="modal-content" style="max-width: 400px; text-align: center;">
        
        <div style="margin-bottom: 15px;">
            <div style="width: 60px; height: 60px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 30px;"></i>
            </div>
        </div>

        <h3 style="margin-bottom: 10px;">Hapus Jadwal?</h3>
        <p style="color: #666; margin-bottom: 25px;">
            Apakah Anda yakin ingin menghapus jadwal ini? <br>
            Tindakan ini tidak dapat dibatalkan.
        </p>

        <div style="display: flex; gap: 10px; justify-content: center;">
            <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">
                Batal
            </button>
            
            <a href="#" id="confirmDeleteBtn" class="btn-save" style="background-color: #ef4444; text-decoration: none; display: inline-flex; align-items: center;">
                Ya, Hapus
            </a>
        </div>
    </div>
</div>