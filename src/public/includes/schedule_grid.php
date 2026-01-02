<div class="schedule-grid">

    <?php foreach ($urutan_hari as $hari) : ?>
        <div class="day-card">

            <div class="day-header">
                Hari <?= htmlspecialchars($hari) ?>
            </div>

            <div class="course-list">

                <?php if (count($jadwal_per_hari[$hari]) > 0) : ?>

                    <?php foreach ($jadwal_per_hari[$hari] as $item) : ?>
                        <div class="course-item">

                            <div class="course-info-wrapper">
                                <div class="room-badge">
                                    <?= htmlspecialchars($item['kelasMatkul']) ?>
                                </div>

                                <h4 class="subject-name">
                                     <?= singkatanMatkul($item['namaMatkul']) ?>
                                </h4>
                            </div>

                            <div class="item-actions">
                                <button class="btn-mini edit" 
                                    onclick="openEditModal(
                                        '<?= $item['id_jadwal'] ?>',
                                        '<?= htmlspecialchars($item['hari']) ?>',
                                        '<?= htmlspecialchars($item['namaMatkul']) ?>',
                                        '<?= htmlspecialchars($item['kelasMatkul']) ?>',
                                        '<?= htmlspecialchars($item['sks'] ?? 1) ?>',
                                        '<?= $item['jam_mulai'] ?>',
                                        '<?= $item['jam_selesai'] ?>',
                                        '<?= htmlspecialchars($item['dosenMatkul']) ?>'
                                    )">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <button class="btn-mini delete" onclick="openDeleteModal('<?= $item['id_jadwal'] ?>')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>

                        </div>
                    <?php endforeach; ?>

                <?php else : ?>

                    <p style="text-align:center; font-size:12px; color:#aaa;">
                        Tidak ada jadwal
                    </p>

                <?php endif; ?>

            </div>
        </div>
    <?php endforeach; ?>

</div>
