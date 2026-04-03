<!DOCTYPE html>
<?php
session_start();
require_once 'db.php';

// Redirige vers la connexion si pas connecté
if (!isset($_SESSION['pseudo'])) {
    header("Location: connexion.php");
    exit();
}

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

$prof       = isset($_GET['prof']) ? htmlspecialchars($_GET['prof']) : "Inconnu";
$pseudoUser = $_SESSION['pseudo'];

$stmt = $pdo->prepare("SELECT creneau, date_rdv FROM reservations WHERE professeur = :prof");
$stmt->execute([':prof' => $prof]);
$reservations = $stmt->fetchAll();
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calendrier — <?= $prof ?></title>
    <link rel="stylesheet" href="../CSS/styles_cld.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
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
        #modal-box p  { color: #555; font-size: .9em; margin-bottom: 12px; }
        #modal-box .user-badge {
            display: inline-block;
            background: #e8f0fe;
            color: rgb(22,55,103);
            border: 1px solid rgb(22,55,103);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: .88em;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .modal-btns { display: flex; gap: 12px; justify-content: center; margin-top: 8px; }

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

        .hour-slot.booked {
            background: #fde8e8 !important;
            color: #c0392b !important;
            cursor: not-allowed;
            font-weight: bold;
        }
        .hour-slot.booked::after { content: " 🔒"; }

        #prof-title {
            text-align: center;
            margin: 20px 0 10px 0;
            color: rgb(22,55,103);
            font-size: 1.4em;
        }

        #mes-resa-link {
            display: block;
            text-align: center;
            margin: 0 auto 24px auto;
            width: fit-content;
            padding: 10px 24px;
            background: rgb(22,55,103);
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-size: .95em;
            transition: background .2s;
        }
        #mes-resa-link:hover { background: #e60073; }
    </style>
</head>

<body>
    <?php include("header.php"); ?>

    <h2 id="prof-title">📅 Prise de rendez-vous — <?= $prof ?></h2>

    <a href="MesReservations.php" id="mes-resa-link">📋 Voir mes réservations</a>

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

    <div id="crenaux">
        <div id="selected-date"></div>
        <div class="hour-calendar" id="hour-calendar"></div>
    </div>

    <!-- Modal : plus de saisie, le pseudo vient de la session -->
    <div id="modal-overlay">
        <div id="modal-box">
            <h3>Confirmer la réservation</h3>
            <p id="modal-info"></p>
            <p>Réservation au nom de :</p>
            <span class="user-badge">👤 <?= htmlspecialchars($pseudoUser) ?></span>
            <div class="modal-btns">
                <button class="btn-confirm" onclick="confirmerReservation()">✅ Confirmer</button>
                <button class="btn-cancel"  onclick="fermerModal()">Annuler</button>
            </div>
            <div id="modal-msg"></div>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <script>
        const PROF_NAME = <?= json_encode($prof) ?>;

        const reservationsExistantes = {};
        <?php foreach ($reservations as $r): ?>
        (function(){
            const d = <?= json_encode($r['date_rdv']) ?>;
            const h = <?= json_encode($r['creneau']) ?>;
            if (!reservationsExistantes[d]) reservationsExistantes[d] = [];
            reservationsExistantes[d].push(h);
        })();
        <?php endforeach; ?>

        const calendarDates = document.querySelector('.calendar-dates');
        const monthYear     = document.getElementById('month-year');
        const prevMonthBtn  = document.getElementById('prev-month');
        const nextMonthBtn  = document.getElementById('next-month');

        let currentDate  = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear  = currentDate.getFullYear();

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
                day.addEventListener('click', () => renderHourCalendar(i));
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

        let pendingDay     = null;
        let pendingCreneau = null;

        function ouvrirModal(day, hStr) {
            pendingDay     = day;
            pendingCreneau = hStr;
            document.getElementById('modal-info').textContent =
                `${day} ${months[currentMonth]} ${currentYear} à ${hStr} avec ${PROF_NAME}`;
            document.getElementById('modal-msg').textContent = '';
            document.getElementById('modal-msg').className   = '';
            document.getElementById('modal-overlay').classList.add('active');
        }

        function fermerModal() {
            document.getElementById('modal-overlay').classList.remove('active');
        }

        function confirmerReservation() {
            const msgEl   = document.getElementById('modal-msg');
            const dateStr = padDate(currentYear, currentMonth, pendingDay);

            // id_etudiant n'est plus envoyé : reserver.php le lit depuis $_SESSION['pseudo']
            const body = new URLSearchParams({
                professeur: PROF_NAME,
                creneau:    pendingCreneau,
                date_rdv:   dateStr,
            });

            fetch('reserver.php', { method: 'POST', body })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        msgEl.textContent = '✅ ' + data.message;
                        msgEl.className   = 'ok';

                        if (!reservationsExistantes[dateStr]) reservationsExistantes[dateStr] = [];
                        reservationsExistantes[dateStr].push(pendingCreneau);

                        renderHourCalendar(pendingDay);
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

        document.getElementById('modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) fermerModal();
        });
    </script>
</body>
</html>
