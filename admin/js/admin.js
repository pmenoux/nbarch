/* admin.js — NB.ARCH admin
   Slug auto-génération, drag-and-drop, cover selection
   (drag-and-drop et cover ajoutés en Étape 3) */

// Auto-slug : transforme le titre en slug URL-safe
document.addEventListener('DOMContentLoaded', function () {
    var titre = document.getElementById('titre');
    var slug  = document.getElementById('slug');
    if (!titre || !slug) return;

    // Ne générer automatiquement que si le slug est vide (nouveau projet)
    if (slug.value !== '') return;

    titre.addEventListener('input', function () {
        var s = this.value.toLowerCase();
        s = s.replace(/[àâä]/g, 'a')
             .replace(/[éèêë]/g, 'e')
             .replace(/[îï]/g, 'i')
             .replace(/[ôö]/g, 'o')
             .replace(/[ùûü]/g, 'u')
             .replace(/ç/g, 'c')
             .replace(/œ/g, 'oe')
             .replace(/æ/g, 'ae')
             .replace(/[^a-z0-9]+/g, '-')
             .replace(/^-|-$/g, '');
        slug.value = s;
    });
});
