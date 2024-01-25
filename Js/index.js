// index.js

document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");

  form.addEventListener("submit", function (event) {
    event.preventDefault();
    console.log("Form submission intercepted");

    // Validation des champs
    const nom = document.getElementById("nom").value;
    const prenom = document.getElementById("prenom").value;
    const tel = document.getElementById("tel").value;
    const email = document.getElementById("email").value;
    const profession = document.getElementById("profession").value;
    const entreprise = document.getElementById("entreprise").value;
    const salaire = document.getElementById("salaire").value;
    const mtPret = document.getElementById("mt_pret").value;
    const duree = document.getElementById("duree").value;

    // Validation des champs
    if (
      !nom ||
      !prenom ||
      !tel ||
      !email ||
      !profession ||
      !entreprise ||
      !salaire ||
      !mtPret ||
      !duree
    ) {
      alert("Veuillez remplir tous les champs du formulaire.");
      return;
    }

    // Validation du numéro de téléphone à 8 chiffres
    if (tel.length !== 8 || isNaN(tel)) {
      alert("Veuillez entrer un numéro de téléphone valide de 8 chiffres.");
      return;
    }

    if (isNaN(mtPret) || mtPret <= 0) {
      alert("Veuillez entrer un montant de prêt valide.");
      return;
    }

    if (isNaN(duree) || duree <= 0) {
      alert("Veuillez entrer une durée de prêt valide.");
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
      alert("Veuillez entrer une adresse email valide.");
      return;
    }

    if (isNaN(salaire) || salaire <= 0) {
      alert("Veuillez entrer un salaire valide.");
      return;
    }

    const nameRegex = /^[A-Za-z]+$/;

    if (!nameRegex.test(nom) || !nameRegex.test(prenom)) {
      alert(
        "Veuillez entrer un nom et un prénom valides (seules les lettres sont autorisées)."
      );
      return;
    }

    const professionRegex = /^[A-Za-z]+$/;

    if (!professionRegex.test(profession)) {
      alert(
        "Veuillez entrer une profession valide (seules les lettres sont autorisées)."
      );
      return;
    }

    const entrepriseRegex = /^[A-Za-z]+$/;

    if (!entrepriseRegex.test(entreprise)) {
      alert(
        "Veuillez entrer un nom d'entreprise valide (seules les lettres sont autorisées)."
      );
      return;
    }

    if (isNaN(duree) || duree <= 0 || duree > 5) {
      alert("Veuillez entrer une durée de prêt valide (entre 1 et 5 ans).");
      return;
    }

    if (parseFloat(mtPret) < parseFloat(salaire)) {
      alert("Le montant du prêt ne peut pas être inférieur au salaire.");
      return;
    }

    if (duree < 1 || duree > 5) {
      alert("La durée du prêt doit être comprise entre 1 et 5 ans.");
      return;
    }

    const mens = calculateMens(mtPret, duree);
    if (mens > parseFloat(salaire) / 3) {
      alert(
        "La mensualité est supérieure au tiers du salaire. Veuillez ajuster la durée ou le montant du prêt."
      );
      return;
    }

    // Soumettre le formulaire si la validation passe
    form.submit();
  });
});

// Fonction pour calculer la mensualité
function calculateMens(mtPret, duree) {
  const taux = duree <= 2 ? 1.08 : duree <= 4 ? 1.1 : 1.12;
  return (mtPret * taux) / (duree * 12);
}
