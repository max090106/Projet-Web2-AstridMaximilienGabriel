<!DOCTYPE html>
<?php
require_once 'db.php';
$pdo = getDB();

$pdo->exec("
    CREATE TABLE IF NOT EXISTS reservations (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        id_etudiant VARCHAR(20)  NOT NULL,
        professeur  VARCHAR(100) NOT NULL,
        creneau     VARCHAR(50)  NOT NULL,
        date_rdv    DATE         NOT NULL,
        created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_rdv (professeur, creneau, date_rdv)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$prof = isset($_GET['prof']) ? htmlspecialchars($_GET['prof']) : "Inconnu";

$stmt = $pdo->prepare("SELECT * FROM reservations WHERE professeur = :prof ORDER BY date_rdv ASC, creneau ASC");
$stmt->execute([':prof' => $prof]);
$reservations = $stmt->fetchAll();
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calendrier — <?= $prof ?></title>
    <!-- ✅ CSS est dans ../CSS/ -->
    <link rel="stylesheet" href="../CSS/styles_cld.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* ── Modal de réservation ── */
        #modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        #modal-overlay.active { display: flex; }

        #modal-box {
            background: #fff;
            border-radius: 12px;
            padding: 30px 35px;
            width: 360px;
            box-shadow: 0 8px 30px rgba(0,0,0,.25);
            text-align: center;
        }
        #modal-box h3 { color: rgb(22,55,103); margin-bottom: 8px; font-size: 1.1em; }
        #modal-box p  { color: #555; font-size: .9em; margin-bottom: 20px; }

        #modal-box input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid rgb(22,55,103);
            border-radius: 8px;
            font-size: 1em;
            margin-bottom: 18px;
            box-sizing: border-box;
        }
        #modal-box input[type="text"]:focus { outline: none; border-color: #e60073; }

        .modal-btns { display: flex; gap: 12px; justify-content: center; }

        .btn-confirm {
            background: rgb(22,55,103);
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: .95em;
            transition: background .2s;
        }
        .btn-confirm:hover { background: #e60073; }

        .btn-cancel {
            background: #e9eef7;
            color: rgb(22,55,103);
            border: 1px solid rgb(22,55,103);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: .95em;
        }
        #modal-msg { margin-top: 14px; font-size: .9em; min-height: 20px; }
        #modal-msg.ok  { color: green; }
        #modal-msg.err { color: #c0392b; }

        /* ── Table des réservations ── */
        #reservations-section {
            max-width: 850px;
            margin: 30px auto 50px auto;
            padding: 0 20px;
        }
        #reservations-section h2 {
            color: rgb(22,55,103);
            text-align: center;
            margin-bottom: 18px;
            font-size: 1.3em;
        }
        #table-reservations {
            width: 100%;
            border-collapse: collapse;
            font-size: .9em;
            box-shadow: 0 2px 10px rgba(0,0,0,.12);
            border-radius: 10px;
            overflow: hidden;
        }
        #table-reservations thead {
            background: rgb(22,55,103);
            color: white;
        }
        #table-reservations th, #table-reservations td {
            padding: 12px 16px;
            text-align: left;
        }
        #table-reservations tbody tr:nth-child(even) { background: #f4f6fa; }
        #table-reservations tbody tr:hover { background: #e9eef7; transition: .2s; }
        .no-resa { text-align: center; color: #888; padding: 20px; font-style: italic; }

        /* ── Créneaux ── */
        .hour-slot.booked {
            background: #fde8e8 !important;
            color: #c0392b !important;
            cursor: not-allowed;
            font-weight: bold;
        }
        .hour-slot.booked::after { content: " 🔒"; }

        /* ── Titre prof ── */
        #prof-title {
            text-align: center;
            margin: 20px 0 10px 0;
            color: rgb(22,55,103);
            font-size: 1.4em;
        }
    </style>
</head>

<body>
    <!-- ✅ header.php est dans le même dossier PHP/ -->
    <?php include("header.php"); ?>

    <h2 id="prof-title">📅 Prise de rendez-vous — <?= $prof ?></h2>

    <!-- Calendrier mensuel -->
    <div class="calendar">
        <div class="calendar-header">
            <button id="prev-month">‹</button>
            <div id="month-year"></div>
            <button id="next-month">›</button>
        </div>
        <div class="calendar-body">
            <div class="calendar-weekdays">
                <div>Dim</div><div>Lun</div><div>Mar</div><div>Mer</div>
                <div>Jeu</div><div>Ven</div><div>Sam</div>
            </div>
            <div class="calendar-dates"></div>
        </div>
    </div>

    <!-- Créneaux horaires -->
    <div id="crenaux">
        <div id="selected-date"></div>
        <div class="hour-calendar" id="hour-calendar"></div>
    </div>

    <!-- Modal saisie numéro étudiant -->
    <div id="modal-overlay">
        <div id="modal-box">
            <h3>Confirmer la réservation</h3>
            <p id="modal-info"></p>
            <input type="text" id="input-etudiant" placeholder="Votre numéro étudiant (ex: 12345678)" maxlength="20">
            <div class="modal-btns">
                <button class="btn-confirm" onclick="confirmerReservation()">✅ Confirmer</button>
                <button class="btn-cancel"  onclick="fermerModal()">Annuler</button>
            </div>
            <div id="modal-msg"></div>
        </div>
    </div>

    <!-- Table des réservations -->
    <div id="reservations-section">
        <h2>📋 Réservations enregistrées — <?= $prof ?></h2>
        <table id="table-reservations">
            <thead>
                <tr>
                    <th>#</th>
                    <th>N° Étudiant</th>
                    <th>Professeur</th>
                    <th>Date</th>
                    <th>Créneau</th>
                    <th>Enregistré le</th>
                </tr>
            </thead>
            <tbody id="tbody-reservations">
                <?php if (empty($reservations)): ?>
                    <tr><td colspan="6" class="no-resa">Aucune réservation pour l'instant.</td></tr>
                <?php else: ?>
                    <?php foreach ($reservations as $i => $r): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($r['id_etudiant']) ?></td>
                        <td><?= htmlspecialchars($r['professeur']) ?></td>
                        <td><?= htmlspecialchars($r['date_rdv']) ?></td>
                        <td><?= htmlspecialchars($r['creneau']) ?></td>
                        <td><?= htmlspecialchars($r['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include("footer.php"); ?>

    <!-- ✅ JS est dans ../JS/ -->
    <script>
        const PROF_NAME = <?= json_encode($prof) ?>;

        // Créneaux déjà réservés indexés par date : { "2026-04-01": ["09:00", ...] }
        const reservationsExistantes = {};
        <?php foreach ($reservations as $r): ?>
        (function(){
            const d = <?= json_encode($r['date_rdv']) ?>;
            const h = <?= json_encode($r['creneau']) ?>;
            if (!reservationsExistantes[d]) reservationsExistantes[d] = [];
            reservationsExistantes[d].push(h);
        })();
        <?php endforeach; ?>

        // ── Calendrier ──────────────────────────────────────────────────────
        const calendarDates = document.querySelector('.calendar-dates');
        const monthYear     = document.getElementById('month-year');
        const prevMonthBtn  = document.getElementById('prev-month');
        const nextMonthBtn  = document.getElementById('next-month');

        let currentDate  = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear  = currentDate.getFullYear();
        let selectedDay  = null;

        const months = ['Janvier','Février','Mars','Avril','Mai','Juin',
                        'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

        function padDate(y, m, d) {
            return `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        }

        function renderCalendar(month, year) {
            calendarDates.innerHTML = '';
            monthYear.textContent   = `${months[month]} ${year}`;
            const firstDay    = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today       = new Date();

            for (let i = 0; i < firstDay; i++) {
                calendarDates.appendChild(document.createElement('div'));
            }
            for (let i = 1; i <= daysInMonth; i++) {
                const day = document.createElement('div');
                day.textContent = i;
                if (i === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
                    day.classList.add('current-date');
                }
                day.addEventListener('click', () => {
                    selectedDay = i;
                    renderHourCalendar(i);
                });
                calendarDates.appendChild(day);
            }
        }

        function renderHourCalendar(day) {
            const hourCalendar = document.getElementById('hour-calendar');
            const crenauxTitle = document.getElementById('selected-date');
            hourCalendar.innerHTML = '';
            crenauxTitle.innerHTML = '';

            const dateStr = padDate(currentYear, currentMonth, day);
            const booked  = reservationsExistantes[dateStr] || [];

            const title = document.createElement('h3');
            title.textContent = `${day} ${months[currentMonth]} ${currentYear}`;
            crenauxTitle.appendChild(title);

            for (let hour = 8; hour < 19; hour++) {
                const slot = document.createElement('div');
                const hStr = `${String(hour).padStart(2,'0')}:00`;
                slot.textContent = hStr;
                slot.classList.add('hour-slot');

                if (booked.includes(hStr)) {
                    slot.classList.add('booked');
                    slot.title = 'Créneau déjà réservé';
                } else {
                    slot.addEventListener('click', () => ouvrirModal(day, hStr));
                }
                hourCalendar.appendChild(slot);
            }
        }

        prevMonthBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            renderCalendar(currentMonth, currentYear);
        });
        nextMonthBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            renderCalendar(currentMonth, currentYear);
        });

        renderCalendar(currentMonth, currentYear);

        // ── Modal ───────────────────────────────────────────────────────────
        let pendingDay     = null;
        let pendingCreneau = null;

        function ouvrirModal(day, hStr) {
            pendingDay     = day;
            pendingCreneau = hStr;
            document.getElementById('modal-info').textContent =
                `${day} ${months[currentMonth]} ${currentYear} à ${hStr} avec ${PROF_NAME}`;
            document.getElementById('input-etudiant').value = '';
            document.getElementById('modal-msg').textContent = '';
            document.getElementById('modal-msg').className   = '';
            document.getElementById('modal-overlay').classList.add('active');
        }

        function fermerModal() {
            document.getElementById('modal-overlay').classList.remove('active');
        }

        function confirmerReservation() {
            const idEtudiant = document.getElementById('input-etudiant').value.trim();
            const msgEl      = document.getElementById('modal-msg');

            if (!idEtudiant) {
                msgEl.textContent = 'Veuillez entrer votre numéro étudiant.';
                msgEl.className   = 'err';
                return;
            }

            const dateStr = padDate(currentYear, currentMonth, pendingDay);
            const body    = new URLSearchParams({
                id_etudiant: idEtudiant,
                professeur:  PROF_NAME,
                creneau:     pendingCreneau,
                date_rdv:    dateStr,
            });

            // ✅ reserver.php est dans le même dossier PHP/
            fetch('reserver.php', { method: 'POST', body })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        msgEl.textContent = '✅ ' + data.message;
                        msgEl.className   = 'ok';

                        // Marquer le créneau localement
                        if (!reservationsExistantes[dateStr]) reservationsExistantes[dateStr] = [];
                        reservationsExistantes[dateStr].push(pendingCreneau);

                        renderHourCalendar(pendingDay);
                        ajouterLigneTable(idEtudiant, PROF_NAME, dateStr, pendingCreneau);
                        setTimeout(fermerModal, 1500);
                    } else {
                        msgEl.textContent = '❌ ' + data.message;
                        msgEl.className   = 'err';
                    }
                })
                .catch(() => {
                    msgEl.textContent = '❌ Erreur réseau, réessayez.';
                    msgEl.className   = 'err';
                });
        }

        // ── Mise à jour dynamique de la table ───────────────────────────────
        function ajouterLigneTable(idEtudiant, prof, date, creneau) {
            const tbody   = document.getElementById('tbody-reservations');
            const emptyRow = tbody.querySelector('td[colspan]');
            if (emptyRow) emptyRow.closest('tr').remove();

            const now = new Date().toLocaleString('fr-FR');
            const num = tbody.querySelectorAll('tr').length + 1;
            const tr  = document.createElement('tr');
            tr.innerHTML = `
                <td>${num}</td>
                <td>${idEtudiant}</td>
                <td>${prof}</td>
                <td>${date}</td>
                <td>${creneau}</td>
                <td>${now}</td>
            `;
            tbody.appendChild(tr);
        }

        // Fermer la modal en cliquant sur l'overlay
        document.getElementById('modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) fermerModal();
        });
    </script>
</body>
</html>
