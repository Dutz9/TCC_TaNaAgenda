<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário - TáNaAgenda</title>
    <link rel="shortcut icon" href="image/Favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/calendario.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <button class="menu-toggle" id="menu-toggle">☰</button>
        <a href="login.php"><p>Login</p></a>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M320 312C386.3 312 440 258.3 440 192C440 125.7 386.3 72 320 72C253.7 72 200 125.7 200 192C200 258.3 253.7 312 320 312zM290.3 368C191.8 368 112 447.8 112 546.3C112 562.7 125.3 576 141.7 576L498.3 576C514.7 576 528 562.7 528 546.3C528 447.8 448.2 368 349.7 368L290.3 368z"/></svg>
    </header>
    <script src="js/favicon.js"></script>
    <main>
    <section class="area-lado">
        <a href="index.php"><img src="image/logotipo fundo azul.png" alt=""></a>
        <section class="filtrar-calendario">
            <h2>Filtrar Calendário</h2>
            <div class="filtrar-calendario-periodo">
                <h3>Período</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M297.4 438.6C309.9 451.1 330.2 451.1 342.7 438.6L502.7 278.6C515.2 266.1 515.2 245.8 502.7 233.3C490.2 220.8 469.9 220.8 457.4 233.3L320 370.7L182.6 233.4C170.1 220.9 149.8 220.9 137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7L297.3 438.7z"/></svg>
            </div>
            <div class="divisao-checkbox"></div>
            <div class="filtrar-calendario-periodo">
                <h3>Turma</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M297.4 438.6C309.9 451.1 330.2 451.1 342.7 438.6L502.7 278.6C515.2 266.1 515.2 245.8 502.7 233.3C490.2 220.8 469.9 220.8 457.4 233.3L320 370.7L182.6 233.4C170.1 220.9 149.8 220.9 137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7L297.3 438.7z"/></svg>
            </div>
            <div class="divisao-checkbox"></div>
            <div class="filtrar-calendario-periodo">
                <h3>Evento</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M297.4 438.6C309.9 451.1 330.2 451.1 342.7 438.6L502.7 278.6C515.2 266.1 515.2 245.8 502.7 233.3C490.2 220.8 469.9 220.8 457.4 233.3L320 370.7L182.6 233.4C170.1 220.9 149.8 220.9 137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7L297.3 438.7z"/></svg>
            </div>
            <div class="divisao-checkbox"></div>
            <div class="filtrar-calendario-periodo">
                <h3>Calendário</h3>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><path fill="#ffffff" d="M297.4 438.6C309.9 451.1 330.2 451.1 342.7 438.6L502.7 278.6C515.2 266.1 515.2 245.8 502.7 233.3C490.2 220.8 469.9 220.8 457.4 233.3L320 370.7L182.6 233.4C170.1 220.9 149.8 220.9 137.3 233.4C124.8 245.9 124.8 266.2 137.3 278.7L297.3 438.7z"/></svg>
            </div>
        </section>
    </section>

    <section class="calendario">
        <div class="header-calendario">
            <div class="header-parte-de-cima">
                <h3>Janeiro 2025</h3>
            </div>
            <div class="header-divisoes-semanas">
                <div class="dias-da-semana" style="background-color: #B10000;">
                    <h2>Domingo</h2>
                </div>
                <div class="dias-da-semana" style="background-color: #0d102b;">
                    <h2>Segunda-Feira</h2>
                </div>
                <div class="dias-da-semana" style="background-color: #0479F9;">
                    <h2>Terça-Feira</h2>
                </div>
                <div class="dias-da-semana" style="background-color: #0d102b;">
                    <h2>Quarta-Feira</h2>
                </div>
                <div class="dias-da-semana" style="background-color: #0d102b;">
                    <h2>Quinta-Feira</h2>
                </div>
                <div class="dias-da-semana" style="background-color: #0d102b;">
                    <h2>Sexta-Feira</h2>
                </div>
                <div class="dias-da-semana" style="background-color: #0d102b;">
                    <h2>Sábado</h2>
                </div>
            </div>
        </div>
        <div class="fundo-grid">
            <div class="calendario-grid">
                <div class="time-slot outside">
                    <div class="day-number">31</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">01</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">02</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">03</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">04</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">05</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">06</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">07</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">08</div>
                    <div class="event verde">Visita(s) Técnica(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">09</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">10</div>
                    <div class="event azul">Palestra(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">11</div>
                    <div class="event verde">Visita(s) Técnica(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">12</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">13</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">14</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">15</div>
                    <div class="event verde">Visita(s) Técnica(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">16</div>
                    <div class="event azul">Palestra(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">17</div>
                    <div class="event amarelo">Oficina(s)</div>
                    <div class="event verde">Visita(s) Técnica(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">18</div>
                    <div class="event amarelo">Oficina(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">19</div>
                    <div class="event verde">Visita(s) Técnica(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">20</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">21</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">22</div>
                    <div class="event azul">Palestra(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">23</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">24</div>
                    <div class="event verde">Visita(s) Técnica(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">25</div>
                    <div class="event amarelo">Oficina(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">26</div>
                    <div class="event amarelo">Oficina(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">27</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">28</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">29</div>
                    <div class="event azul">Palestra(s)</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">30</div>
                </div>
                <div class="time-slot">
                    <div class="day-number">31</div>
                    <div class="event verde">Visita(s) Técnica(s)</div>
                </div>
                <div class="time-slot outside">
                    <div class="day-number">01</div>
                </div>
                <div class="time-slot outside">
                    <div class="day-number">02</div>
                </div>
                <div class="time-slot outside">
                    <div class="day-number">03</div>
                </div>
            </div>
        </div>
    </section>

    <section class="area-lado-direito">
        <div class="calendario-lado-direito">
            <div class="header-calendario-lado-direito">
                <h3>Janeiro</h3>
                <h3>2025</h3>
            </div>
            <div class="dias-da-semana-calendario-lado-direito">
                <p style="color: #022E5E">Seg</p>
                <p>Ter</p>
                <p>Qua</p>
                <p>Qui</p>
                <p>Sex</p>
                <p>Sab</p>
                <p style="color: #DD2B2B;">Dom</p>
            </div>
            <div class="dias-calendario-lado-direito">
                <p>01</p>
                <p>02</p>
                <p>03</p>
                <p>04</p>
                <p>05</p>
                <p>06</p>
                <p>07</p>
                <p style="background-color: #022E5E; color: white; border-radius: 10px;" >08</p>
                <p>09</p>
                <p>10</p>
                <p>11</p>
                <p>12</p>
                <p>13</p>
                <p>14</p>
                <p>15</p>
                <p>16</p>
                <p>17</p>
                <p>18</p>
                <p>19</p>
                <p>20</p>
                <p>21</p>
                <p>22</p>
                <p>23</p>
                <p>24</p>
                <p>25</p>
                <p>26</p>
                <p>27</p>
                <p>28</p>
                <p>29</p>
                <p>30</p>
                <p>31</p>
            </div>
        </div>

        <section class="resumo-geral-lado-direito">
            <h3>Resumo geral de hoje:</h3>
            <div class="area-escrita-resumo-geral">
                <p>Palestra</p>
                <p>7:10</p>
            </div>
            <div class="area-escrita-resumo-geral" style="border: 2px solid #34CF34;">
                <p>Visita Técnica</p>
                <p>7:10</p>
            </div>
            <div class="area-escrita-resumo-geral">
                <p>Palestra</p>
                <p>8:50</p>
            </div>
            <div class="area-escrita-resumo-geral" style="border: 2px solid #34CF34;">
                <p>Visita Técnica</p>
                <p>10:00</p>
            </div>
        </section>
    </section>
</main>
<div class="menu-overlay" id="menu-overlay"></div>
</body>
</html>