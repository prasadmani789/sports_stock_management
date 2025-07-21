<div class="container-fluid mt-3">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/items') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>items">
                            <i class="fas fa-basketball me-2"></i>Items Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/sales') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>sales/create">
                            <i class="fas fa-shopping-cart me-2"></i>Sales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/quotations') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>quotations/create">
                            <i class="fas fa-file-invoice me-2"></i>Quotations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/stock') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>stock">
                            <i class="fas fa-boxes me-2"></i>Stock Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/customers') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>customers">
                            <i class="fas fa-users me-2"></i>Customers
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <div class="card bg-light border-0">
                            <div class="card-body p-2">
                                <small class="text-muted">Quick Actions</small>
                                <div class="d-grid gap-2 mt-2">
                                    <a href="<?= BASE_URL ?>sales/create" class="btn btn-sm btn-success">
                                        <i class="fas fa-plus me-1"></i> New Sale
                                    </a>
                                    <a href="<?= BASE_URL ?>stock/purchase" class="btn btn-sm btn-primary">
                                        <i class="fas fa-cart-plus me-1"></i> Purchase Stock
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>