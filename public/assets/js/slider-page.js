// Script para efeito de slide entre Login e Cadastro
$(document).ready(function() {
  // Toggle entre Login e Cadastro com efeito de slide
  const signUpButton = $('#signUp');
  const signInButton = $('#signIn');
  const mobileSignUpButton = $('#mobileSignUp');
  const mobileSignInButton = $('#mobileSignIn');
  const container = $('#container');

  // Desktop buttons
  if (signUpButton.length) {
    signUpButton.on('click', function() {
      container.addClass('right-panel-active');
      updateMobileButtons();
      // Rolar para o topo
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  if (signInButton.length) {
    signInButton.on('click', function() {
      container.removeClass('right-panel-active');
      updateMobileButtons();
    });
  }

  // Mobile buttons
  if (mobileSignUpButton.length) {
    mobileSignUpButton.on('click', function() {
      container.addClass('right-panel-active');
      updateMobileButtons();
      // Rolar para o topo
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  if (mobileSignInButton.length) {
    mobileSignInButton.on('click', function() {
      container.removeClass('right-panel-active');
      updateMobileButtons();
    });
  }

  // Atualizar estado dos botões mobile
  function updateMobileButtons() {
    if (!mobileSignUpButton.length || !mobileSignInButton.length) return;
    
    if (container.hasClass('right-panel-active')) {
      mobileSignUpButton.addClass('btn-dark').removeClass('btn-outline-dark');
      mobileSignInButton.addClass('btn-outline-dark').removeClass('btn-dark');
    } else {
      mobileSignInButton.addClass('btn-dark').removeClass('btn-outline-dark');
      mobileSignUpButton.addClass('btn-outline-dark').removeClass('btn-dark');
    }
  }

  // Inicializar botões mobile
  updateMobileButtons();
});
