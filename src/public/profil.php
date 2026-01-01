<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Profil</title>
    <link rel="stylesheet" href="style/profil.css">
</head>
<body>
    <div class="container">
        <h1>Informasi Profil</h1>
        
        <div class="profile-section">
            <div class="profile-picture"></div>
            <p class="profile-name">Nasihuyyy</p>
        </div>
        
        <hr>

        <h2>Detail Personal</h2>
        
        <form>
            <div class="form-row">
                <div class="form-field">
                    <label for="namaDepan">Nama Depan</label>
                    <input type="text" id="namaDepan" name="namaDepan">
                </div>
                <div class="form-field">
                    <label for="namaBelakang">Nama Belakang</label>
                    <input type="text" id="namaBelakang" name="namaBelakang">
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="email">Alamat Email</label>
                    <input type="email" id="email" name="email">
                </div>
                <div class="form-field">
                    <label for="telepon">Nomor Telepon</label>
                    <input type="tel" id="telepon" name="telepon">
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="tanggalLahir">Tanggal Lahir</label>
                    <input type="date" id="tanggalLahir" name="tanggalLahir">
                </div>
                <div class="form-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password">
                </div>
            </div>
        </form>
    </div>
</body>
</html>