/* admin.js — NB.ARCH admin
   Slug auto, drag-and-drop photos, cover selection, sortable lists */

document.addEventListener('DOMContentLoaded', function () {

    // ========================================
    // Auto-slug depuis le titre
    // ========================================
    var titre = document.getElementById('titre');
    var slug  = document.getElementById('slug');
    if (titre && slug && slug.value === '') {
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
    }

    // ========================================
    // CSRF token (lu depuis le premier hidden input)
    // ========================================
    function getToken() {
        var el = document.querySelector('input[name="_token"]');
        return el ? el.value : '';
    }

    // ========================================
    // Photo grid — drag-and-drop reorder
    // ========================================
    var grid = document.getElementById('photoGrid');
    if (grid) {
        var dragSrc = null;

        grid.querySelectorAll('.photo-card').forEach(function (card) {
            card.setAttribute('draggable', 'true');

            card.addEventListener('dragstart', function (e) {
                dragSrc = this;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', this.dataset.id);
            });

            card.addEventListener('dragend', function () {
                this.classList.remove('dragging');
                grid.querySelectorAll('.photo-card').forEach(function (c) {
                    c.classList.remove('drag-over');
                });
            });

            card.addEventListener('dragover', function (e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                if (this !== dragSrc) this.classList.add('drag-over');
            });

            card.addEventListener('dragleave', function () {
                this.classList.remove('drag-over');
            });

            card.addEventListener('drop', function (e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                if (dragSrc === this) return;

                // Réorganiser les éléments DOM
                var cards = Array.from(grid.querySelectorAll('.photo-card'));
                var fromIdx = cards.indexOf(dragSrc);
                var toIdx = cards.indexOf(this);

                if (fromIdx < toIdx) {
                    grid.insertBefore(dragSrc, this.nextSibling);
                } else {
                    grid.insertBefore(dragSrc, this);
                }

                // Mettre à jour badges couverture
                updateCoverBadges();

                // Envoyer le nouvel ordre au serveur
                savePhotoOrder();
            });
        });

        // Sélection couverture via bouton
        grid.querySelectorAll('.btn-cover').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var photoId = this.dataset.id;
                var projetId = grid.dataset.projet;
                var data = new FormData();
                data.append('photo_id', photoId);
                data.append('projet_id', projetId);
                data.append('_token', getToken());

                fetch(window.APP_URL + '/actions/photo_cover.php', {
                    method: 'POST',
                    body: data
                })
                .then(function (r) { return r.json(); })
                .then(function () { location.reload(); });
            });
        });

        function updateCoverBadges() {
            grid.querySelectorAll('.photo-card').forEach(function (card, i) {
                card.classList.toggle('is-cover', i === 0);
                var badge = card.querySelector('.photo-badge');
                if (i === 0 && !badge) {
                    badge = document.createElement('span');
                    badge.className = 'photo-badge';
                    badge.textContent = 'COUVERTURE';
                    card.appendChild(badge);
                } else if (i > 0 && badge) {
                    badge.remove();
                }
            });
        }

        function savePhotoOrder() {
            var ids = [];
            grid.querySelectorAll('.photo-card').forEach(function (c) {
                ids.push(c.dataset.id);
            });
            var data = new FormData();
            ids.forEach(function (id) { data.append('ids[]', id); });
            data.append('_token', getToken());

            fetch(window.APP_URL + '/actions/photo_reorder.php', {
                method: 'POST',
                body: data
            });
        }
    }

    // ========================================
    // Sortable lists (categories, projets)
    // ========================================
    document.querySelectorAll('.sortable-list').forEach(function (list) {
        var dragItem = null;

        list.querySelectorAll('.sortable-item').forEach(function (item) {
            item.setAttribute('draggable', 'true');

            item.addEventListener('dragstart', function (e) {
                dragItem = this;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
            });

            item.addEventListener('dragend', function () {
                this.classList.remove('dragging');
                list.querySelectorAll('.sortable-item').forEach(function (i) {
                    i.classList.remove('drag-over');
                });
            });

            item.addEventListener('dragover', function (e) {
                e.preventDefault();
                if (this !== dragItem) this.classList.add('drag-over');
            });

            item.addEventListener('dragleave', function () {
                this.classList.remove('drag-over');
            });

            item.addEventListener('drop', function (e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                if (dragItem === this) return;

                var items = Array.from(list.querySelectorAll('.sortable-item'));
                var fromIdx = items.indexOf(dragItem);
                var toIdx = items.indexOf(this);

                if (fromIdx < toIdx) {
                    list.insertBefore(dragItem, this.nextSibling);
                } else {
                    list.insertBefore(dragItem, this);
                }

                // Sauvegarder le nouvel ordre
                saveSortableOrder(list);
            });
        });
    });

    function saveSortableOrder(list) {
        var url = list.dataset.url;
        if (!url) return;

        var ids = [];
        list.querySelectorAll('.sortable-item').forEach(function (item) {
            ids.push(item.dataset.id);
        });
        var data = new FormData();
        ids.forEach(function (id) { data.append('ids[]', id); });
        data.append('_token', getToken());

        fetch(url, { method: 'POST', body: data });
    }

    // ========================================
    // Accueil image selector (radio cards)
    // ========================================
    document.querySelectorAll('.accueil-card input[type="radio"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.accueil-card').forEach(function (c) {
                c.classList.remove('selected');
            });
            this.closest('.accueil-card').classList.add('selected');
        });
    });

    // ========================================
    // Quill WYSIWYG editors
    // ========================================
    var quillConfig = {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic'],
                ['link'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['clean']
            ]
        }
    };

    // Éditeur description (projets)
    var edDesc = document.getElementById('editor-description');
    if (edDesc) {
        var qDesc = new Quill(edDesc, quillConfig);
        edDesc.closest('form').addEventListener('submit', function () {
            document.getElementById('description').value = qDesc.root.innerHTML;
        });
    }

    // Éditeur contenu (pages)
    var edCont = document.getElementById('editor-contenu');
    if (edCont) {
        var qCont = new Quill(edCont, quillConfig);
        edCont.closest('form').addEventListener('submit', function () {
            document.getElementById('contenu').value = qCont.root.innerHTML;
        });
    }

});
