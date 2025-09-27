function updateFavicon() {
    const favicon = document.getElementById('favicon');
    const basePath = window.location.pathname.split('/').slice(0, 2).join('/');
    const darkIcon = `${basePath}/image/Favicon-dark.png`;
    const lightIcon = `${basePath}/image/Favicon-light.png`;

    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        favicon.setAttribute('href', darkIcon);
    } else {
        favicon.setAttribute('href', lightIcon);
    }
}
updateFavicon();
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateFavicon);