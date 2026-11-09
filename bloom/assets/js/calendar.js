// Day 11: Calendar rendered in the browser using birthday records loaded by PHP.
const birthdayData = JSON.parse(document.getElementById('birthdayCalendarData').textContent);
const grid = document.getElementById('calendarGrid');
const monthLabel = document.getElementById('calendarMonth');
const selectedDateLabel = document.getElementById('selectedDate');
const details = document.getElementById('selectedBirthdays');
const now = new Date();
let visibleYear = now.getFullYear();
let visibleMonth = now.getMonth(); // JavaScript months are 0–11.
let selectedDay = now.getDate();

function birthdayDayForYear(isoDate, year) {
    const [, month, day] = isoDate.split('-').map(Number);
    const leap = year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0);
    return { month: month - 1, day: month === 2 && day === 29 && !leap ? 28 : day };
}
function birthdaysOn(day) {
    return birthdayData.filter(birthday => {
        const date = birthdayDayForYear(birthday.birth_date, visibleYear);
        return date.month === visibleMonth && date.day === day;
    });
}
function displaySelectedDay() {
    const date = new Date(visibleYear, visibleMonth, selectedDay);
    selectedDateLabel.textContent = date.toLocaleDateString(undefined, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    details.replaceChildren();
    const birthdays = birthdaysOn(selectedDay);
    if (birthdays.length === 0) {
        const empty = document.createElement('p');
        empty.className = 'calendar-empty';
        empty.textContent = 'No birthdays on this date. ✨';
        details.appendChild(empty);
        return;
    }
    birthdays.forEach(birthday => {
        const card = document.createElement('div');
        card.className = 'calendar-person';
        const name = document.createElement('strong');
        name.textContent = `🎂 ${birthday.person_name}`;
        const category = document.createElement('span');
        category.textContent = birthday.relationship;
        const link = document.createElement('a');
        link.href = `edit-birthday.php?id=${encodeURIComponent(birthday.id)}`;
        link.textContent = 'Edit birthday';
        card.append(name, category, link);
        details.appendChild(card);
    });
}
function renderCalendar() {
    const firstDay = new Date(visibleYear, visibleMonth, 1);
    const daysInMonth = new Date(visibleYear, visibleMonth + 1, 0).getDate();
    selectedDay = Math.min(selectedDay, daysInMonth);
    monthLabel.textContent = firstDay.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
    grid.replaceChildren();
    ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].forEach(label => {
        const heading = document.createElement('div');
        heading.className = 'calendar-weekday';
        heading.textContent = label;
        grid.appendChild(heading);
    });
    const offset = (firstDay.getDay() + 6) % 7; // Monday first
    for (let i = 0; i < offset; i++) {
        const blank = document.createElement('div');
        blank.className = 'calendar-blank';
        grid.appendChild(blank);
    }
    for (let day = 1; day <= daysInMonth; day++) {
        const matches = birthdaysOn(day);
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'calendar-day';
        if (matches.length) button.classList.add('has-birthday');
        if (day === selectedDay) button.classList.add('selected');
        if (visibleYear === now.getFullYear() && visibleMonth === now.getMonth() && day === now.getDate()) button.classList.add('today');
        button.setAttribute('aria-pressed', String(day === selectedDay));
        button.setAttribute('aria-label', `${day} ${monthLabel.textContent}, ${matches.length} birthday${matches.length === 1 ? '' : 's'}`);
        const number = document.createElement('span');
        number.textContent = String(day);
        button.appendChild(number);
        if (matches.length) {
            const marker = document.createElement('span');
            marker.className = 'calendar-marker';
            marker.textContent = matches.length > 1 ? `🎂 ${matches.length}` : '🎂';
            button.appendChild(marker);
        }
        button.addEventListener('click', () => { selectedDay = day; renderCalendar(); });
        grid.appendChild(button);
    }
    displaySelectedDay();
}
function changeMonth(delta) {
    const next = new Date(visibleYear, visibleMonth + delta, 1);
    visibleYear = next.getFullYear();
    visibleMonth = next.getMonth();
    selectedDay = 1;
    renderCalendar();
}
document.getElementById('previousMonth').addEventListener('click', () => changeMonth(-1));
document.getElementById('nextMonth').addEventListener('click', () => changeMonth(1));
document.getElementById('todayMonth').addEventListener('click', () => {
    visibleYear = now.getFullYear(); visibleMonth = now.getMonth(); selectedDay = now.getDate(); renderCalendar();
});
renderCalendar();
