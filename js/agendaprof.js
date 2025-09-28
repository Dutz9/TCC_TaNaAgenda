document.addEventListener('DOMContentLoaded', () => {
    // --- VERIFICAÇÃO INICIAL ---
    // Garante que o PHP nos deu os dados dos eventos antes de continuar.
    if (typeof eventosDoBanco === 'undefined') {
        console.error("A variável 'eventosDoBanco' não foi encontrada. Verifique o script PHP.");
        return;
    }

    // --- ELEMENTOS DO MODAL (POP-UP) ---
    const modalOverlay = document.getElementById('modal-overlay');
    const modalContent = modalOverlay.querySelector('.modal-content');
    const dayModalOverlay = document.getElementById('day-modal-overlay');
    const dayModalContent = dayModalOverlay.querySelector('.modal-content');
    const selectedDaySpan = document.getElementById('selected-day');

    // --- FUNÇÕES AUXILIARES ---

    /**
     * Pega uma data e retorna todos os eventos daquele dia.
     * @param {Date} dateObject - O objeto de data do dia desejado.
     * @returns {Array} - Um array de eventos para aquele dia.
     */
    function getEventsForDate(dateObject) {
        // Formata a data para 'YYYY-MM-DD' para comparar com os dados do banco.
        const targetDate = dateObject.toISOString().split('T')[0];
        return eventosDoBanco.filter(event => event.dt_evento === targetDate);
    }
    
    /**
     * Formata a data do evento para o padrão brasileiro (DD/MM/YYYY).
     * @param {string} dateString - A data no formato 'YYYY-MM-DD'.
     * @returns {string} - A data formatada.
     */
    function formatarData(dateString) {
        const [ano, mes, dia] = dateString.split('-');
        return `${dia}/${mes}/${ano}`;
    }

    // --- LÓGICA DO MODAL DE EVENTO INDIVIDUAL ---

    // Função que abre o modal e o preenche com os dados do evento clicado.
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

    // Adiciona o "escutador de cliques" em cada evento que o PHP desenhou.
    document.querySelectorAll('.event').forEach(eventDiv => {
        eventDiv.addEventListener('click', (e) => {
            e.stopPropagation(); // Impede que outros cliques sejam acionados.
            const data = e.currentTarget.dataset;
            showEventModal(data);
        });
    });

    // Fecha o modal se o usuário clicar fora da caixa de conteúdo.
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) modalOverlay.style.display = 'none';
    });
    dayModalOverlay.addEventListener('click', (e) => {
        if (e.target === dayModalOverlay) dayModalOverlay.style.display = 'none';
    });


    // --- LÓGICA DO MINI CALENDÁRIO E RESUMOS ---
    
    // As funções do seu `index.js` original, agora adaptadas para usar `eventosDoBanco`.
    const rightCalendarDays = document.querySelector('.dias-calendario-lado-direito');
    const miniCalHeaderMonth = document.querySelector('.header-calendario-lado-direito h3:nth-child(1)');
    const miniCalHeaderYear = document.querySelector('.header-calendario-lado-direito h3:nth-child(2)');

    function updateRightPanel(date) {
        // Atualiza o mini-calendário
        const month = date.getMonth();
        const year = date.getFullYear();
        const monthsPt = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

        miniCalHeaderMonth.innerText = monthsPt[month];
        miniCalHeaderYear.innerText = year;

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

            if (d === date.getDate()) {
                dayP.style.backgroundColor = '#0479F9';
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
                dayModalContent.querySelector('div, p').innerHTML = eventsHtml; // Atualiza o conteúdo do modal do dia
                dayModalOverlay.style.display = 'flex';
            });
            rightCalendarDays.appendChild(dayP);
        }

        // Atualiza os resumos
        updateSummaries(date);
    }
    
    function updateSummaries(date) {
        const todayEvents = getEventsForDate(date);
        const tomorrowDate = new Date(date);
        tomorrowDate.setDate(date.getDate() + 1);
        const tomorrowEvents = getEventsForDate(tomorrowDate);

        // Resumo de hoje
        const todaySummaryDiv = document.querySelector(".resumo-geral-lado-direito:nth-of-type(1)");
        todaySummaryDiv.querySelectorAll(".area-escrita-resumo-geral").forEach(el => el.remove());
        
        if(todayEvents.length > 0){
            todayEvents.forEach(evt => {
                const p = document.createElement('div');
                p.className = 'area-escrita-resumo-geral';
                p.innerHTML = `<p>${evt.nm_evento}</p><p>${evt.horario_inicio.substr(0, 5)}</p>`;
                todaySummaryDiv.appendChild(p);
            });
        } else {
             const p = document.createElement('div');
             p.className = 'area-escrita-resumo-geral';
             p.innerHTML = `<p>Nenhum evento hoje.</p>`;
             todaySummaryDiv.appendChild(p);
        }

        // Resumo de amanhã
        const tomorrowSummaryDiv = document.querySelector(".resumo-geral-lado-direito:nth-of-type(2)");
        tomorrowSummaryDiv.querySelectorAll(".area-escrita-resumo-geral").forEach(el => el.remove());

        if(tomorrowEvents.length > 0){
            tomorrowEvents.forEach(evt => {
                const p = document.createElement('div');
                p.className = 'area-escrita-resumo-geral';
                p.innerHTML = `<p>${evt.nm_evento}</p><p>${evt.horario_inicio.substr(0, 5)}</p>`;
                tomorrowSummaryDiv.appendChild(p);
            });
        } else {
             const p = document.createElement('div');
             p.className = 'area-escrita-resumo-geral';
             p.innerHTML = `<p>Nenhum evento amanhã.</p>`;
             tomorrowSummaryDiv.appendChild(p);
        }
    }

    // Inicializa o painel direito com a data atual.
    updateRightPanel(new Date());
});