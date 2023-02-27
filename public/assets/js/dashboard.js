// // Récupération des liens de modification
// var editLinks = document.querySelectorAll('a[href*="edit_request"]');
// // Parcours des liens et désactivation si nécessaire
// for (var i = 0; i < editLinks.length; i++) {
//     var statusSpan = editLinks[i].closest('tr').querySelector('.badge');
//     var status = statusSpan.textContent.trim();
//     console.log("yes");

//   console.log(status);
//   if (status !== 'en attente') {
//     editLinks[i].setAttribute('disabled', true);
//     editLinks[i].addEventListener('click', function(event) {
//       event.preventDefault();
//       alert("Impossible de modifier cette demande car elle a déjà été acceptée ou refusée.");
//     });
//   }
// }