document.addEventListener('DOMContentLoaded', () => {
    // --- VERIFICAÇÃO INICIAL ---
    if (typeof eventosDoBanco === 'undefined') {
        console.error("A variável 'eventosDoBanco' não foi encontrada. Verifique o script PHP.");
        return;
    }

    // --- ELEMENTOS PRINCIPAIS DA PÁGINA ---
    const modalOverlay = document.getElementById('modal-overlay');
    const modalContent = modalOverlay.querySelector('.modal-content');
    const dayModalOverlay = document.getElementById('day-modal-overlay');
    const dayModalContent = dayModalOverlay.querySelector('.modal-content');
    const selectedDaySpan = document.getElementById('selected-day');
    const rightCalendarDays = document.querySelector('.dias-calendario-lado-direito');
    const miniCalHeaderMonth = document.querySelector('.header-calendario-lado-direito h3:nth-child(1)');
    const miniCalHeaderYear = document.querySelector('.header-calendario-lado-direito h3:nth-child(2)');

    // --- FUNÇÕES AUXILIARES ---
    const getEventsForDate = (dateObject) => {
        const targetDate = dateObject.toISOString().split('T')[0];
        return eventosDoBanco.filter(event => event.dt_evento === targetDate);
    };
    
    const formatarData = (dateString) => {
        const [ano, mes, dia] = dateString.split('-');
        return `${dia}/${mes}/${ano}`;
    };

    const getEventTypeClass = (eventType) => {
        if (!eventType) return 'tipo-outro';
        return 'tipo-' + eventType.toLowerCase().replace(/ /g, '-');
    };

    // --- LÓGICA DO MODAL DE EVENTO INDIVIDUAL ---
    function showEventModal(eventData) {
        modalContent.innerHTML = `
            <h3>${eventData.nome}</h3>
            <p><strong>Data:</strong> ${new Date(eventData.data + 'T00:00:00').toLocaleDateString('pt-BR')}</p>
            <p><strong>Horário:</strong> ${eventData.inicio.substr(0, 5)} - ${eventData.fim.substr(0, 5)}</p>
            <p style="font-weight: 600; margin-top: 10px;">Turmas Envolvidas:</p>
            <p>${eventData.turmas || 'Nenhuma turma especificada.'}</p>
            <p style="font-weight: 600; margin-top: 10px;">Professores Envolvidos:</p>
            <p>${eventData.professores || 'Nenhum professor especificado.'}</p>
            <p style="font-weight: 600; margin-top: 10px;">Descrição:</p>
            <p>${eventData.descricao}</p>
        `;
        modalOverlay.style.display = 'flex';
    }

    document.querySelectorAll('.event').forEach(eventDiv => {
        eventDiv.addEventListener('click', (e) => {
            e.stopPropagation();
            const data = e.currentTarget.dataset;
            showEventModal(data);
        });
    });

    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) modalOverlay.style.display = 'none';
    });
    dayModalOverlay.addEventListener('click', (e) => {
        if (e.target === dayModalOverlay) dayModalOverlay.style.display = 'none';
    });

    // --- LÓGICA DO PAINEL DIREITO (MINI-CALENDÁRIO E RESUMOS) ---
    function updateSummaries(date) {
        const todayEvents = getEventsForDate(date);
        const tomorrowDate = new Date(date);
        tomorrowDate.setDate(date.getDate() + 1);
        const tomorrowEvents = getEventsForDate(tomorrowDate);

        const todaySummaryContainer = document.querySelector(".resumo-geral-lado-direito:nth-of-type(1) .container-scroll-eventos");
        const tomorrowSummaryContainer = document.querySelector(".resumo-geral-lado-direito:nth-of-type(2) .container-scroll-eventos");
        
        todaySummaryContainer.innerHTML = '';
        if (todayEvents.length > 0) {
            todayEvents.forEach(evt => {
                const p = document.createElement('div');
                p.className = 'area-escrita-resumo-geral event-summary ' + getEventTypeClass(evt.tipo_evento);
                p.innerHTML = `<p>${evt.nm_evento}</p><p>${evt.horario_inicio.substr(0, 5)}</p>`;
                todaySummaryContainer.appendChild(p);
            });
        } else {
            todaySummaryContainer.innerHTML = '<div class="area-escrita-resumo-geral"><p>Nenhum evento hoje.</p></div>';
        }
        
        tomorrowSummaryContainer.innerHTML = '';
        if (tomorrowEvents.length > 0) {
            tomorrowEvents.forEach(evt => {
                const p = document.createElement('div');
                p.className = 'area-escrita-resumo-geral event-summary ' + getEventTypeClass(evt.tipo_evento);
                p.innerHTML = `<p>${evt.nm_evento}</p><p>${evt.horario_inicio.substr(0, 5)}</p>`;
                tomorrowSummaryContainer.appendChild(p);
            });
        } else {
            tomorrowSummaryContainer.innerHTML = '<div class="area-escrita-resumo-geral"><p>Nenhum evento amanhã.</p></div>';
        }
    }
    
    function updateRightPanel(date) {
        const month = date.getMonth();
        const year = date.getFullYear();
        const dayOfWeek = date.getDay();
        const monthsPt = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

        miniCalHeaderMonth.innerText = monthsPt[month];
        miniCalHeaderYear.innerText = year;
        
        const diasSemanaMini = document.querySelectorAll('.dia-semana');
        diasSemanaMini.forEach(dia => dia.classList.remove('atual'));
        if (dayOfWeek >= 1 && dayOfWeek <= 6) {
            diasSemanaMini[dayOfWeek - 1].classList.add('atual');
        } else if (dayOfWeek === 0) {
            diasSemanaMini[6].classList.add('atual');
        }

        rightCalendarDays.innerHTML = '';
        const firstDayOfMonth = new Date(year, month, 1);
        const firstDayOfWeek = (firstDayOfMonth.getDay() + 6) % 7;
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDayOfWeek; i++) {
            rightCalendarDays.appendChild(document.createElement('p'));
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
                const clickedDate = new Date(year, month, d);
                const eventsOfTheDay = getEventsForDate(clickedDate);
                
                selectedDaySpan.innerText = formatarData(`${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`);
                
                let eventsHtml = '';
                if (eventsOfTheDay.length > 0) {
                    eventsOfTheDay.forEach(evt => {
                        eventsHtml += `<p>${evt.nm_evento} às ${evt.horario_inicio.substr(0, 5)}</p>`;
                    });
                } else {
                    eventsHtml = '<p>Nenhum evento para este dia.</p>';
                }
                dayModalContent.querySelector('div').innerHTML = eventsHtml;
                dayModalOverlay.style.display = 'flex';
            });
            rightCalendarDays.appendChild(dayP);
        }
        updateSummaries(date);
    }

    // --- INICIALIZAÇÃO ---
    updateRightPanel(new Date());
});