// Esta função garante que, ao carregar a página, os campos do formulário estejam vazios.
  window.onload = function() {
      if (document.getElementById('form_login')) {
          document.getElementById('form_login').reset();
      }
  };