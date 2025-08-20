<?php
$isAuth = isset($_SESSION['user']);
$base = "/job/quotation-management-system"; // base path
?>

<?php if ($isAuth): ?>

<!-- Mobile Navbar -->
<nav class="navbar navbar-dark bg-dark d-md-none">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $base ?>/dashboard.php">QMS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
    <div class="collapse navbar-collapse bg-dark" id="mobileMenu">
        <ul class="navbar-nav px-2">
            <li class="nav-item"><a class="nav-link text-white" href="<?= $base ?>/customers/manage_customers.php">👥 Customers</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= $base ?>/items/manage_items.php">📦 Items</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= $base ?>/terms/manage_terms.php">📜 Terms</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="<?= $base ?>/quotations/create_quotation.php">📝 New Quotation</a></li>
            <li class="nav-item mt-2">
                <a href="<?= $base ?>/logout.php" class="btn btn-outline-light w-100">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<!-- Layout -->
<div class="container-fluid">
    <div class="row flex-nowrap">
        <!-- Sidebar (Desktop Only) -->
        <nav class="col-md-3 col-xl-3 px-sm-2 bg-dark d-none d-md-block">
            <div class="d-flex flex-column vh-100 p-2">

                <a href="<?= $base ?>/dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 text-white text-decoration-none fs-5 fw-bold">
                    Quotation Management System
                </a>

                <hr class="text-white">

                <div class="accordion accordion-flush" id="sidebarAccordion">

                    <!-- Customers -->
                    <div class="accordion-item bg-dark">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-dark text-white" data-bs-toggle="collapse" data-bs-target="#collapseCustomers">
                                👥 Customers
                            </button>
                        </h2>
                        <div id="collapseCustomers" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                            <div class="accordion-body p-0">
                                <ul class="nav flex-column">
                                    <li class="nav-item"><a class="nav-link text-white ps-4" href="<?= $base ?>/customers/manage_customers.php">Manage Customers</a></li>
                                    <li class="nav-item"><a class="nav-link text-white ps-4" href="<?= $base ?>/customers/add_customer.php">Add Customer</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="accordion-item bg-dark">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-dark text-white" data-bs-toggle="collapse" data-bs-target="#collapseItems">
                                📦 Items
                            </button>
                        </h2>
                        <div id="collapseItems" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                            <div class="accordion-body p-0">
                                <ul class="nav flex-column">
                                    <li class="nav-item"><a class="nav-link text-white ps-4" href="<?= $base ?>/items/manage_items.php">Manage Items</a></li>
                                    <li class="nav-item"><a class="nav-link text-white ps-4" href="<?= $base ?>/items/add_item.php">Add Item</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="accordion-item bg-dark">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-dark text-white" data-bs-toggle="collapse" data-bs-target="#collapseTerms">
                                📜 Terms
                            </button>
                        </h2>
                        <div id="collapseTerms" class="accordion-collapse collapse" data-bs-parent="#sidebarAccordion">
                            <div class="accordion-body p-0">
                                <ul class="nav flex-column">
                                    <li class="nav-item"><a class="nav-link text-white ps-4" href="<?= $base ?>/terms/manage_terms.php">Manage Terms</a></li>
                                    <li class="nav-item"><a class="nav-link text-white ps-4" href="<?= $base ?>/terms/add_term.php">Add Term</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Quotation -->
                    <div class="accordion-item bg-dark">
                        <h2 class="accordion-header">
                            <a href="<?= $base ?>/quotations/create_quotation.php" class="accordion-button collapsed bg-dark text-white text-decoration-none">
                                📝 New Quotation
                            </a>
                        </h2>
                    </div>
                </div>

                <hr class="text-white mt-auto">
                <a href="<?= $base ?>/logout.php" class="btn btn-outline-light w-100">Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col py-3" id="content">

<?php else: ?>

<!-- Guest Navbar -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= $base ?>/dashboard.php">QMS</a>
    </div>
</nav>
<main class="container mt-4" id="content">

<?php endif; ?>
