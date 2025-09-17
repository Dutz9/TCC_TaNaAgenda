document.addEventListener('DOMContentLoaded', () => {
    const events = document.querySelectorAll('.event');
    const modalOverlay = document.getElementById('modal-overlay');

    events.forEach(event => {
        event.addEventListener('click', () => {
            modalOverlay.style.display = 'flex';
        });
    });

    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.style.display = 'none';
        }
    });


    const today = new Date();
    let selectedDate = new Date(today);
    const monthsPt = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];

    const updateCalendar = (date) => {
        const month = date.getMonth();
        const year = date.getFullYear();
        const dayOfWeek = date.getDay();

 
        document.querySelector('.header-parte-de-cima h3').innerText = `${monthsPt[month]} ${year}`;

        const rightHeader = document.querySelector('.header-calendario-lado-direito');
        rightHeader.querySelectorAll('h3')[0].innerText = monthsPt[month];
        rightHeader.querySelectorAll('h3')[1].innerText = year;


        const monday = new Date(date);
        monday.setDate(date.getDate() - ((dayOfWeek + 6) % 7));

     
        const diasSemana = document.querySelectorAll('.dias-da-semana');
        const daysOfWeekPt = [
            "Segunda-Feira", "Terça-Feira", "Quarta-Feira",
            "Quinta-Feira", "Sexta-Feira", "Sábado"
        ];
        diasSemana.forEach((dia, index) => {
            const currentDay = new Date(monday);
            currentDay.setDate(monday.getDate() + index);
            dia.querySelector('h2').innerText = `${daysOfWeekPt[index]} ${currentDay.getDate()}`;
            dia.style.backgroundColor = '#0d102b';
            if (currentDay.getDate() === date.getDate() && currentDay.getMonth() === month && currentDay.getFullYear() === year) {
                dia.style.backgroundColor = '#0479F9';
            }
        });

      
        const calendarDays = document.querySelector('.dias-calendario-lado-direito');
        calendarDays.innerHTML = ''; 
        const firstDay = new Date(year, month, 1);
        const firstDayWeek = (firstDay.getDay() + 6) % 7; 
        const daysInMonth = new Date(year, month + 1, 0).getDate();

      
        for (let i = 0; i < firstDayWeek; i++) {
            const empty = document.createElement('p');
            calendarDays.appendChild(empty);
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const dayP = document.createElement('p');
            dayP.innerText = d < 10 ? `0${d}` : d;
            dayP.classList.add('calendar-day');
            if (d === date.getDate() && month === date.getMonth() && year === date.getFullYear()) {
                dayP.style.backgroundColor = '#022E5E';
                dayP.style.color = 'white';
                dayP.style.borderRadius = '10px';
            }
            dayP.addEventListener('click', () => {
                selectedDate = new Date(year, month, d);
                updateCalendar(selectedDate);
            });
            calendarDays.appendChild(dayP);
        }
    };

    updateCalendar(selectedDate);
});