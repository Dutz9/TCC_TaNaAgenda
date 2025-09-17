document.addEventListener('DOMContentLoaded', () => {
    const events = document.querySelectorAll('.event');
    const modalOverlay = document.getElementById('modal-overlay');
    const dayModalOverlay = document.getElementById('day-modal-overlay'); // Novo modal
    const selectedDaySpan = document.getElementById('selected-day'); // Span pra mostrar o dia

    // Funcionalidade do modal de evento
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

    // Funcionalidade do novo modal de dia
    dayModalOverlay.addEventListener('click', (e) => {
        if (e.target === dayModalOverlay) {
            dayModalOverlay.style.display = 'none';
        }
    });

    // Configurações do calendário
    const monthsPt = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];
    const daysOfWeekPt = [
        "Segunda-Feira", "Terça-Feira", "Quarta-Feira",
        "Quinta-Feira", "Sexta-Feira", "Sábado"
    ];
    const timeSlots = ["07:10", "08:00", "08:50", "10:00", "10:50", "11:40"];

    // Função para obter o índice do dia da semana com base na data
    function getDayIndex(date) {
        const dayOfWeek = date.getDay(); // 0 (Domingo), 1 (Segunda), ..., 6 (Sábado)
        // Ajustar para o calendário (Segunda=0, Terça=1, ..., Sábado=5)
        if (dayOfWeek === 0) return -1; // Domingo não está no calendário
        return dayOfWeek - 1; // Segunda=0, Terça=1, ..., Sábado=5
    }

    // Função para obter eventos de um dia específico
    function getEventsForDay(dayIndex) {
        if (dayIndex < 0 || dayIndex > 5) return []; // Retorna vazio para dias fora do calendário (ex.: Domingo)
        const events = [];
        const grid = document.querySelector(".calendario-grid");
        const timeSlotCells = grid.querySelectorAll(`.time-slot:nth-child(7n + ${dayIndex + 2})`); // +2 para pular time-slot-label

        timeSlotCells.forEach((cell, index) => {
            const time = timeSlots[index];
            const eventElements = cell.querySelectorAll(".event");
            eventElements.forEach(event => {
                const eventName = event.textContent;
                const eventColor = Array.from(event.classList).find(cls => ["azul", "verde", "amarelo"].includes(cls));
                events.push({ time, name: eventName, color: eventColor });
            });
        });

        return events;
    }

    // Função para atualizar os resumos gerais
    function updateSummaries(date) {
        const todayIndex = getDayIndex(date);
        const tomorrowDate = new Date(date);
        tomorrowDate.setDate(date.getDate() + 1);
        const tomorrowIndex = getDayIndex(tomorrowDate);

        // Atualizar resumo de hoje
        const todaySummary = document.querySelector(".resumo-geral-lado-direito:nth-child(2)");
        const todayContainer = todaySummary.querySelectorAll(".area-escrita-resumo-geral");
        todayContainer.forEach(container => container.remove()); // Limpar resumo atual

        const todayEvents = getEventsForDay(todayIndex);
        if (todayEvents.length === 0) {
            const noEventsDiv = document.createElement("div");
            noEventsDiv.classList.add("area-escrita-resumo-geral");
            noEventsDiv.style.border = "2px solid #AFAFAF"; // Borda cinza para "Sem Eventos"
            noEventsDiv.innerHTML = `<p>Sem Eventos</p>`;
            todaySummary.appendChild(noEventsDiv);
        } else {
            todayEvents.forEach(event => {
                const eventDiv = document.createElement("div");
                eventDiv.classList.add("area-escrita-resumo-geral");
                eventDiv.style.border = `2px solid ${
                    event.color === "azul" ? "#4291D8" :
                    event.color === "verde" ? "#34CF34" :
                    "#F9C833"
                }`;
                eventDiv.innerHTML = `<p>${event.name}</p><p>${event.time}</p>`;
                todaySummary.appendChild(eventDiv);
            });
        }

        // Atualizar resumo de amanhã
        const tomorrowSummary = document.querySelector(".resumo-geral-lado-direito:nth-child(3)");
        const tomorrowContainer = tomorrowSummary.querySelectorAll(".area-escrita-resumo-geral");
        tomorrowContainer.forEach(container => container.remove()); // Limpar resumo atual

        // Verificar se hoje é sábado (índice 5) ou se não há eventos amanhã
        if (todayIndex === 5 || tomorrowIndex < 0 || tomorrowIndex > 5) {
            const noEventsDiv = document.createElement("div");
            noEventsDiv.classList.add("area-escrita-resumo-geral");
            noEventsDiv.style.border = "2px solid #AFAFAF"; // Borda cinza para "Sem Eventos"
            noEventsDiv.innerHTML = `<p>Sem Eventos</p>`;
            tomorrowSummary.appendChild(noEventsDiv);
        } else {
            const tomorrowEvents = getEventsForDay(tomorrowIndex);
            if (tomorrowEvents.length === 0) {
                const noEventsDiv = document.createElement("div");
                noEventsDiv.classList.add("area-escrita-resumo-geral");
                noEventsDiv.style.border = "2px solid #AFAFAF"; // Borda cinza para "Sem Eventos"
                noEventsDiv.innerHTML = `<p>Sem Eventos</p>`;
                tomorrowSummary.appendChild(noEventsDiv);
            } else {
                tomorrowEvents.forEach(event => {
                    const eventDiv = document.createElement("div");
                    eventDiv.classList.add("area-escrita-resumo-geral");
                    eventDiv.style.border = `2px solid ${
                        event.color === "azul" ? "#4291D8" :
                        event.color === "verde" ? "#34CF34" :
                        "#F9C833"
                    }`;
                    eventDiv.innerHTML = `<p>${event.name}</p><p>${event.time}</p>`;
                    tomorrowSummary.appendChild(eventDiv);
                });
            }
        }
    }

    // Função para atualizar o calendário
    const updateCalendar = (date) => {
        const month = date.getMonth();
        const year = date.getFullYear();
        const dayOfWeek = date.getDay();

        // Atualizar cabeçalhos
        document.querySelector('.header-parte-de-cima h3').innerText = `${monthsPt[month]} ${year}`;
        const rightHeader = document.querySelector('.header-calendario-lado-direito');
        rightHeader.querySelectorAll('h3')[0].innerText = monthsPt[month];
        rightHeader.querySelectorAll('h3')[1].innerText = year;

        // Atualizar dias da semana no calendário principal
        const monday = new Date(date);
        monday.setDate(date.getDate() - ((dayOfWeek + 6) % 7));
        const diasSemana = document.querySelectorAll('.dias-da-semana');
        diasSemana.forEach((dia, index) => {
            const currentDay = new Date(monday);
            currentDay.setDate(monday.getDate() + index);
            dia.querySelector('h2').innerText = `${daysOfWeekPt[index]} ${currentDay.getDate()}`;
            dia.style.backgroundColor = '#0d102b';
            if (currentDay.getDate() === date.getDate() && currentDay.getMonth() === month && currentDay.getFullYear() === year) {
                dia.style.backgroundColor = '#0479F9';
            }
        });

        // Destaque dinâmico nos dias da semana do mini calendário (dependendo do dia atual)
        const diasSemanaMini = document.querySelectorAll('.dia-semana');
        diasSemanaMini.forEach(dia => dia.classList.remove('atual')); // Remove destaque anterior
        if (dayOfWeek >= 1 && dayOfWeek <= 6) { // 1=Seg, 6=Sab
            const classes = ['seg', 'ter', 'qua', 'qui', 'sex', 'sab', 'dom'];
            diasSemanaMini[dayOfWeek - 1].classList.add('atual'); // Adiciona classe 'atual' pro dia atual
        } else if (dayOfWeek === 0) {
            diasSemanaMini[6].classList.add('atual'); // Domingo
        }

        // Atualizar calendário lateral direito
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
                // Abre o novo modal com eventos do dia (estático por enquanto)
                selectedDaySpan.innerText = `${d < 10 ? '0' + d : d}/${month + 1 < 10 ? '0' + (month + 1) : month + 1}/${year}`;
                dayModalOverlay.style.display = 'flex';
            });
            calendarDays.appendChild(dayP);
        }

        // Atualizar resumos gerais
        updateSummaries(date);
    };

    // Inicializar o calendário com a data atual
    const today = new Date(); // Data atual do sistema
    let selectedDate = new Date(today);
    updateCalendar(selectedDate);
});