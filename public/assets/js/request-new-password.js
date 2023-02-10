var formRequest = document.getElementById("formRequest");
if (formRequest) {
  formRequest.addEventListener("submit", function(event) {
    var email = document.getElementById("reset_password_request_form_email");
    if (email) {
      var emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

      if (!email.value.match(emailRegex)) {
        alert("L'adresse e-mail n'est pas valide");
        event.preventDefault();
      }
    }
  });
}
