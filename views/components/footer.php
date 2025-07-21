            </main>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <span class="text-muted"><?= APP_NAME ?> v<?= APP_VERSION ?></span>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted">© <?= date('Y') ?> Sports Shop. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="<?= BASE_URL ?>assets/js/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables -->
    <script src="<?= BASE_URL ?>assets/js/dataTables.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Select2 -->
    <script src="<?= BASE_URL ?>assets/js/select2.min.js"></script>
    
    <!-- Chart.js -->
    <script src="<?= BASE_URL ?>assets/js/chart.min.js"></script>
    
    <!-- Toastify -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <!-- Custom Scripts -->
    <script src="<?= BASE_URL ?>assets/js/script.js"></script>
    
    <?php if (isset($customScript)): ?>
    <script src="<?= BASE_URL ?>assets/js/<?= $customScript ?>.js"></script>
    <?php endif; ?>
</body>
</html>