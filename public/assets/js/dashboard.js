  // Récupération des éléments DOM des boutons de tri
  const dateRecenteBtn = document.querySelector('.dropdown-menu li:nth-child(1) a');
  const dateAncienneBtn = document.querySelector('.dropdown-menu li:nth-child(2) a');

  // Récupération des éléments DOM des colonnes de dates de début et de fin
  const dateDebutCells = document.querySelectorAll('td:nth-child(2)');
  const dateFinCells = document.querySelectorAll('td:nth-child(3)');

  // Fonction pour trier les lignes en fonction de la date de début
  function trierParDateDebut() {
    // Convertir les cellules de dates de début en un tableau d'objets avec la date et l'indice de la ligne
    const dates = Array.from(dateDebutCells).map((cell, index) => ({ date: new Date(cell.innerText), index }));

    // Trier le tableau d'objets par ordre décroissant de date
    dates.sort((a, b) => b.date - a.date);

    // Récupérer les lignes dans leur ordre initial
    const rows = Array.from(document.querySelectorAll('tbody tr'));

    // Déplacer les lignes en fonction de l'indice de la ligne dans le tableau trié d'objets
    dates.forEach(({ index }) => {
      const row = rows[index];
      row.parentNode.appendChild(row);
    });
  }

  // Fonction pour trier les lignes en fonction de la date de fin
  function trierParDateFin() {
    // Convertir les cellules de dates de fin en un tableau d'objets avec la date et l'indice de la ligne
    const dates = Array.from(dateFinCells).map((cell, index) => ({ date: new Date(cell.innerText), index }));

    // Trier le tableau d'objets par ordre croissant de date
    dates.sort((a, b) => a.date - b.date);

    // Récupérer les lignes dans leur ordre initial
    const rows = Array.from(document.querySelectorAll('tbody tr'));

    // Déplacer les lignes en fonction de l'indice de la ligne dans le tableau trié d'objets
    dates.forEach(({ index }) => {
      const row = rows[index];
      row.parentNode.appendChild(row);
    });
  }

  // Ajouter des écouteurs d'événements pour les boutons de tri
  dateRecenteBtn.addEventListener('click', trierParDateDebut);
  dateAncienneBtn.addEventListener('click', trierParDateFin);
