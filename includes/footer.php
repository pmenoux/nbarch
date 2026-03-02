    </main>

</div><!-- .site -->

<script>
// Accordéon sidebar : clic sur une catégorie la déplie/replie
document.querySelectorAll('.nav-cat-title').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        var cat = this.closest('.nav-cat');
        if (cat.classList.contains('active')) {
            e.preventDefault();
            cat.classList.remove('active');
        }
    });
});

// Hamburger mobile
var hamburger = document.getElementById('hamburger');
var sidebarNav = document.getElementById('sidebarNav');
if (hamburger && sidebarNav) {
    hamburger.addEventListener('click', function() {
        this.classList.toggle('open');
        sidebarNav.classList.toggle('open');
    });
}
</script>
</body>
</html>
