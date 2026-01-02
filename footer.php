    </div>

    <!-- Footer -->
    <footer class="footer mt-5 py-3 bg-light">
        <div class="container-fluid text-center">
            <span class="text-muted">Kurumsal CRM © <?php echo date('Y'); ?> - Tüm hakları saklıdır.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Silme onayı
        function confirmDelete(message) {
            return confirm(message || 'Bu kaydı silmek istediğinizden emin misiniz?');
        }
        
        // Form gönderme onayı
        function confirmSubmit(message) {
            return confirm(message || 'İşlemi onaylıyor musunuz?');
        }
    </script>
</body>
</html>
