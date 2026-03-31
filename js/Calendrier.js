const calendarDates = document.querySelector('.calendar-dates');
const calendarhorraire = document.querySelector('.horraire');
const monthYear = document.getElementById('month-year');
const prevMonthBtn = document.getElementById('prev-month');
const nextMonthBtn = document.getElementById('next-month');

let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

const months = [
  'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
  'Juilet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
];

function renderCalendar(month, year) {
  calendarDates.innerHTML = '';
  monthYear.textContent = `${months[month]} ${year}`;

  const firstDay = new Date(year, month, 1).getDay();

  const daysInMonth = new Date(year, month + 1, 0).getDate();

  for (let i = 0; i < firstDay; i++) {
    const blank = document.createElement('div');
    calendarDates.appendChild(blank);
  }

  for (let i = 1; i <= daysInMonth; i++) {
    const day = document.createElement('div');
    day.textContent = i;
    calendarDates.appendChild(day);
  }
  const today = new Date();

  for (let i = 1; i <= daysInMonth-31; i++) {
    const day = document.createElement('div');
    day.textContent = i;

    if (
      i === today.getDate() &&
      year === today.getFullYear() &&
      month === today.getMonth()
    ) {
      day.classList.add('current-date');
    }

    calendarDates.appendChild(day);
  }

}

renderCalendar(currentMonth, currentYear);

function renderHourCalendar(day) {
  const hourCalendar = document.getElementById('hour-calendar');
  hourCalendar.innerHTML = ''; // Vide le contenu actuel

  // Titre pour le calendrier horaire
  const title = document.createElement('h3');
  title.textContent = `Heures pour le ${day} ${months[currentMonth]} ${currentYear}`;
  hourCalendar.appendChild(title);

  // Génère les heures de 00:00 à 23:00
  for (let hour = 0; hour < 24; hour++) {
    const hourDiv = document.createElement('div');
    hourDiv.textContent = `${hour.toString().padStart(2, '0')}:00`;
    hourDiv.classList.add('hour-slot');
    hourCalendar.appendChild(hourDiv);
  }
}

prevMonthBtn.addEventListener('click', () => {
  currentMonth--;
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  }
  renderCalendar(currentMonth, currentYear);
});

nextMonthBtn.addEventListener('click', () => {
  currentMonth++;
  if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  renderCalendar(currentMonth, currentYear);
});
  

calendarDates.addEventListener('click', (e) => {
  if (e.target.textContent !== '') {
    const day = parseInt(e.target.textContent);
    renderHourCalendar(day);
  }
});

