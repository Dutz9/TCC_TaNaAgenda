document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.area-lado');
    const overlay = document.getElementById('menu-overlay');

    if (menuToggle && sidebar && overlay) {
        // Função para abrir/fechar
        const toggleMenu = () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        };

        menuToggle.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu); // Fecha ao clicar fora
    }
});