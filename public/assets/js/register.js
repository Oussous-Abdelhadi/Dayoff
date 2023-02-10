  
  if (document.getElementById("formAuthentication")) {
    document.getElementById("formAuthentication").addEventListener("submit", function(event) {
      var name = document.getElementById("registration_form_name").value;
      var firstname = document.getElementById("registration_form_firstname").value;
      var email = document.getElementById("registration_form_email").value;
      var emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      var password = document.getElementById("registration_form_password").value;
    
      if (!name.match(/^[a-zA-Z\s]+$/) || !firstname.match(/^[a-zA-Z\s]+$/)) {
        alert("Le nom et le prénom ne peuvent contenir que des lettres");
        event.preventDefault();
      } else if (name.length < 2 || firstname.length < 2) {
        alert("Le nom et le prénom doivent contenir au moins 2 caractères");
        event.preventDefault();
      } else if (!email.match(emailRegex)) {
        alert("L'adresse e-mail n'est pas valide");
        event.preventDefault();
      } else if (password.length < 6) {
        alert("Le mot de passe doit contenir au moins 6 caractères");
        event.preventDefault();
      }
    });
  }

// console.log('yes');


if (window.location.pathname === '/register') {
  console.log(window.location.href);
  fetch('/register', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (!response.ok) {
      return response.json().then(error => {
        alert(error.error);
      });
    }
    // do something on success
  });
}