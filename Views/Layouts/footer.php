        <?php if (isset($_SESSION['user_id'])): ?>
                </main>
                <footer class="mt-auto py-3 bg-white border-top text-center text-muted fs-7">
                    <div class="container-fluid">
                        <small>&copy; <?= date('Y') ?> <?= APP_NAME ?> &bull; Sistema Web de Gestión, Ventas, Inventario y Corresponsal Bancario con IA &bull; Desarrollado para Laragon</small>
                    </div>
                </footer>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Main JS -->
    <script src="<?= url('js/app.js') ?>"></script>
    <script>
        // Toggle responsive sidebar
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    sidebar.classList.toggle('d-none');
                }
            });
        }
    </script>
</body>
</html>
