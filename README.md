# sijawa-web

## Database (Tugas)

Gunakan skema ini agar halaman Tugas berjalan.

```sql
CREATE TABLE IF NOT EXISTS StatusTugas (
	id_status INT AUTO_INCREMENT PRIMARY KEY,
	status ENUM('Masih Bisa Ditunda','Tolong Dikerjakan','Harus Dikerjakan') NOT NULL UNIQUE
);

INSERT IGNORE INTO StatusTugas (status)
VALUES ('Masih Bisa Ditunda'), ('Tolong Dikerjakan'), ('Harus Dikerjakan');

CREATE TABLE IF NOT EXISTS Tugas (
	id_tugas INT AUTO_INCREMENT PRIMARY KEY,
	id_mahasiswa INT NOT NULL,
	id_status INT NULL,
	namaTugas VARCHAR(255) NOT NULL,
	matkulTugas VARCHAR(100),
	tenggatTugas DATETIME NOT NULL,
	INDEX (id_mahasiswa),
	INDEX (id_status),
	INDEX (tenggatTugas),
	CONSTRAINT fk_tugas_user FOREIGN KEY (id_mahasiswa) REFERENCES Mahasiswa(id_mahasiswa) ON DELETE CASCADE,
	CONSTRAINT fk_tugas_status FOREIGN KEY (id_status) REFERENCES StatusTugas(id_status) ON DELETE SET NULL
);
```

Demo mode: `src/public/tugas.php` tidak memakai session. Data baru disimpan dengan `id_mahasiswa = 1` (ubah konstanta `$DEMO_USER_ID`). Setelah login aktif, ganti ke `$_SESSION['user_id']` untuk filter dan insert.