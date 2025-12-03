document.addEventListener('DOMContentLoaded', () => {
    // --- LÓGICA DOS FILTROS ACORDEÃO ---
    const formFiltrosAgenda = document.getElementById('form-filtros-agenda');
    if (formFiltrosAgenda) {
        document.querySelectorAll('.filtro-header').forEach(header => {
            header.addEventListener('click', () => {
                const item = header.parentElement;
                item.classList.toggle('aberto');
            });
        });
    }

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
    
    // --- Seletores do Mini-Calendário ATUALIZADOS ---
    const miniCalHeaderMonth = document.querySelector('.header-calendario-lado-direito h3:nth-of-type(1)');
    const miniCalHeaderYear = document.querySelector('.header-calendario-lado-direito h3:nth-of-type(2)');
    const miniCalPrevBtn = document.getElementById('mini-cal-prev');
    const miniCalNextBtn = document.getElementById('mini-cal-next');

    // --- FUNÇÕES AUXILIARES ---
    function formatarDataParaFiltro(dateObject) {
        const dia = String(dateObject.getDate()).padStart(2, '0');
        const mes = String(dateObject.getMonth() + 1).padStart(2, '0');
        const ano = dateObject.getFullYear();
        return `${ano}-${mes}-${dia}`;
    }

    const getEventsForDate = (dateObject) => {
        const targetDate = formatarDataParaFiltro(dateObject);
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

    function getMondayString(date) {
        const d = new Date(date);
        const dayOfWeek = d.getDay();
        const diff = d.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
        const monday = new Date(d.setDate(diff));
        return formatarDataParaFiltro(monday);
    }

    // --- LÓGICA DO MODAL DE EVENTO INDIVIDUAL ---
    function showEventModal(eventData) {
        // (Esta função continua 100% igual à da última vez)
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
    
    // Pega a data da URL (se existir) para o mini-calendário
    const urlParams = new URLSearchParams(window.location.search);
    const weekParam = urlParams.get('week');
    let dataInicial = new Date();
    if (weekParam) {
        dataInicial = new Date(weekParam + 'T12:00:00'); // Adiciona T12:00 para evitar bugs de fuso
    }
    
    // Variável de estado para o mês do mini-calendário
    let dataAtualMiniCal = new Date(dataInicial);

    // --- NOVOS EVENT LISTENERS PARA AS SETAS ---
    miniCalPrevBtn.addEventListener('click', () => {
        dataAtualMiniCal.setMonth(dataAtualMiniCal.getMonth() - 1);
        updateRightPanel(dataAtualMiniCal);
    });

    miniCalNextBtn.addEventListener('click', () => {
        dataAtualMiniCal.setMonth(dataAtualMiniCal.getMonth() + 1);
        updateRightPanel(dataAtualMiniCal);
    });
    // --- FIM DOS NOVOS LISTENERS ---

    function updateSummaries(date) {
        // (Esta função continua 100% igual à da última vez)
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
        const today = new Date();
        const todayDate = today.getDate();
        const todayMonth = today.getMonth();
        const todayYear = today.getFullYear();
        
        const monthsPt = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];

        miniCalHeaderMonth.innerText = monthsPt[month];
        miniCalHeaderYear.innerText = year;
        
        const diasSemanaMini = document.querySelectorAll('.dia-semana');
        diasSemanaMini.forEach(dia => dia.classList.remove('atual'));
        
        // Destaque do dia da semana (Seg, Ter, Qua...)
        // Só destaca se o mês/ano do mini-calendário for o mesmo do "hoje"
        if (month === todayMonth && year === todayYear) {
            if (dayOfWeek >= 1 && dayOfWeek <= 6) {
                diasSemanaMini[dayOfWeek - 1].classList.add('atual');
            } else if (dayOfWeek === 0) {
                diasSemanaMini[6].classList.add('atual');
            }
        }

        rightCalendarDays.innerHTML = '';
        const firstDayOfMonth = new Date(year, month, 1);
        const firstDayOfWeek = (firstDayOfMonth.getDay() + 6) % 7;
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDayOfWeek; i++) {
            rightCalendarDays.appendChild(document.createElement('p'));
        }

        const filtrosQueryString = window.location.search.split('?')[1] || '';
        const params = new URLSearchParams(filtrosQueryString);
        params.delete('week');
        const filtrosAtuais = params.toString();

        
    for (let d = 1; d <= daysInMonth; d++) {
        const dayLink = document.createElement('a');
        dayLink.innerText = d < 10 ? `0${d}` : d;
        dayLink.className = 'calendar-day-link';

        const clickedDate = new Date(year, month, d);
        
        // --- NOVA LÓGICA: VERIFICA SE TEM EVENTO NESTE DIA ---
        // Usa a função auxiliar existente para formatar a data como YYYY-MM-DD
        const dateStr = formatarDataParaFiltro(clickedDate);
        
        // Verifica no array (que agora contém o mês todo) se existe algum evento nesta data
        const hasEvent = eventosDoBanco.some(ev => ev.dt_evento === dateStr);
        
        // Se tiver, adiciona a classe que faz a linha azul aparecer
        if (hasEvent) {
            dayLink.classList.add('has-event');
        }
        // --- FIM DA NOVA LÓGICA ---

        const mondayString = getMondayString(clickedDate);
        
        dayLink.href = `?week=${mondayString}&${filtrosAtuais}`;

        if (d === todayDate && month === todayMonth && year === todayYear) {
            dayLink.classList.add('today');
        }

        rightCalendarDays.appendChild(dayLink);
    }
        
        // Atualiza os resumos para a data da AGENDA PRINCIPAL, não do mini-calendário
        updateSummaries(dataInicial);
    }

    // --- INICIALIZAÇÃO ---
    updateRightPanel(dataInicial);
});