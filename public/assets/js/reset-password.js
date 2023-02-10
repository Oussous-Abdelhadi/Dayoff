var formResetPassword = document.getElementById("formResetPassword");
if (formResetPassword) {
  formResetPassword.addEventListener("submit", function(event) {
    var passwordFirst = document.getElementById("change_password_form_password_first");
    var passwordSecond = document.getElementById("change_password_form_password_second");
    if (passwordFirst && passwordSecond) {
      var password = passwordFirst.value;
      var repeatPassword = passwordSecond.value;
      if (password != repeatPassword) {
        alert("Les mots de passe ne sont pas identiques.");
        event.preventDefault();
      } else {
        if (password.length < 6 && repeatPassword.length < 6) {
          alert("Le mot de passe doit contenir au moins 6 caractères");
          event.preventDefault();
        }
      }
    }
  });
}
