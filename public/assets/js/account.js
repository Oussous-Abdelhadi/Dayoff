if (document.getElementById("formEditAccount")) {
    document.getElementById("formEditAccount").addEventListener("submit", function(event) {
      var name = document.getElementById("edite_user_name").value;
      var firstname = document.getElementById("edite_user_firstname").value;
      var email = document.getElementById("edite_user_email").value;
      var emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      // /^[a-zA-Z\s]+$/
      if (!name.match(/^[a-zA-ZÀ-ÖØ-öø-ÿ]+$/) || !firstname.match(/^[a-zA-ZÀ-ÖØ-öø-ÿ]+$/)) {
        alert("Le nom et le prénom ne peuvent contenir que des lettres");
        event.preventDefault();
      } else if (name.length < 2 || firstname.length < 2) {
        alert("Le nom et le prénom doivent contenir au moins 2 caractères");
        event.preventDefault();
      } else if (!email.match(emailRegex)) {
        alert("L'adresse e-mail n'est pas valide");
        event.preventDefault();
      }
    });
  }