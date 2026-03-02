    </main>

</div><!-- .site -->

<script>
// Accordéon sidebar : clic sur une catégorie la déplie/replie
document.querySelectorAll('.nav-cat-title').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        // Si on clique sur la catégorie déjà active, on la referme
        var cat = this.closest('.nav-cat');
        if (cat.classList.contains('active')) {
            e.preventDefault();
            cat.classList.remove('active');
        }
    });
});
</script>
</body>
</html>
