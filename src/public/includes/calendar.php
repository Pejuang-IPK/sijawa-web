<div class="calendar-container">
                    <div class="calendar-header">
                        <h1 class="title">Jadwal & Kalender</h1>
                        <div class="month-nav">
                            <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="btn-nav"><i class="fa-solid fa-chevron-left"></i></a>
                            <span><?= $bulanIndo[$monthName] . " " . $year ?></span>
                            <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="btn-nav"><i class="fa-solid fa-chevron-right"></i></a>
                        </div>
                    </div>

                    <div class="calendar-grid">
                        
                        <div class="day-name">Sen</div>
                        <div class="day-name">Sel</div>
                        <div class="day-name">Rab</div>
                        <div class="day-name">Kam</div>
                        <div class="day-name">Jum</div>
                        <div class="day-name">Sab</div>
                        <div class="day-name">Min</div>

                        <?php
                        // 2. Loop Kotak Kosong / Bulan Lalu (Padding Awal)
                        // Hitung tanggal akhir bulan lalu
                        $daysInPrevMonth = date('t', mktime(0,0,0, $month-1, 1, $year));
                        
                        for ($i = 0; $i < $dayOfWeek; $i++) {
                            $prevDate = $daysInPrevMonth - ($dayOfWeek - 1 - $i);
                            echo '<div class="calendar-day other-month">';
                            echo '<span class="date-number">' . $prevDate . '</span>';
                            echo '</div>';
                        }

                        // 3. Loop Tanggal Bulan Ini
                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            // Format tanggal YYYY-MM-DD untuk cek event
                            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
                            
                            // Cek apakah hari ini
                            $isToday = ($currentDate == date('Y-m-d')) ? 'today' : '';

                            echo "<div class='calendar-day $isToday'>";
                            echo "<span class='date-number'>$day</span>";

                            // Tentukan hari (Senin, Selasa, dst)
                            $namaHari = date('l', strtotime($currentDate));

                            $mapHari = [
                                'Monday' => 'Senin',
                                'Tuesday' => 'Selasa',
                                'Wednesday' => 'Rabu',
                                'Thursday' => 'Kamis',
                                'Friday' => 'Jumat',
                                'Saturday' => 'Sabtu',
                                'Sunday' => 'Minggu'
                            ];

                            $hariIndo = $mapHari[$namaHari] ?? null;

                            // Jika ada jadwal di hari tersebut
                            if ($hariIndo && isset($jadwal_mingguan[$hariIndo])) {

                                $count = 0;
                                foreach ($jadwal_mingguan[$hariIndo] as $jadwal) {

                                    if ($count >= 2) {
                                        echo '<div class="event-more">+ lainnya</div>';
                                        break;
                                    }

                                    echo '<div class="event-item clean">';
                                    echo '<span class="event-time">' . substr($jadwal['jam_mulai'],0,5) . '</span>';
                                    echo '<span class="event-title">' . singkatanMatkul($jadwal['namaMatkul']) . '</span>';
                                    echo '</div>';

                                    $count++;
                                }
                            }

                            echo "</div>";
                        }

                        // 4. Loop Kotak Kosong / Bulan Depan (Sisa Grid)
                        // Total grid yang terpakai sejauh ini
                        $totalCells = $dayOfWeek + $daysInMonth;
                        // Sisa kotak untuk melengkapi grid 7 kolom
                        $remainingCells = (7 - ($totalCells % 7)) % 7;
                        // Tambahan baris jika total baris kurang dari 6 (opsional, agar kotak tetap rapi)
                        if($totalCells + $remainingCells < 35) {
                            $remainingCells += 7; // Tambah 1 baris lagi
                        }

                        for ($j = 1; $j <= $remainingCells; $j++) {
                            echo '<div class="calendar-day other-month">';
                            echo '<span class="date-number">' . sprintf('%02d', $j) . '</span>';
                            echo '</div>';
                        }
                        ?>

                    </div>
                </div>