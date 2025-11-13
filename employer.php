<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: log.html");
    exit;
}

$firstname = htmlspecialchars($_SESSION['firstname']);
$lastname = htmlspecialchars($_SESSION['lastname']);
$email = htmlspecialchars($_SESSION['email']);
$role = htmlspecialchars($_SESSION['role']);
$position = isset($_SESSION['position']) ? htmlspecialchars($_SESSION['position']) : 'Non défini';
$department = isset($_SESSION['department']) ? htmlspecialchars($_SESSION['department']) : 'Non défini';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Employé - Smart RH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --secondary: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 8px;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        .dark-theme {
            --primary: #4a9fe3;
            --primary-dark: #3d8fd6;
            --secondary: #34495e;
            --light: #2c3e50;
            --dark: #ecf0f1;
            --gray: #95a5a6;
            --light-gray: #34495e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            transition: var(--transition);
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Header */
        header {
            background-color: var(--secondary);
            color: white;
            padding: 15px 0;
            box-shadow: var(--shadow);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 28px;
            color: var(--primary);
        }

        .logo h1 {
            font-size: 24px;
            font-weight: 600;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .theme-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: var(--transition);
        }

        .theme-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Navigation */
        .main-nav {
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .dark-theme .main-nav {
            background-color: var(--light);
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            position: relative;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 15px 20px;
            text-decoration: none;
            color: var(--dark);
            transition: var(--transition);
            border-bottom: 3px solid transparent;
        }

        .dark-theme .nav-links a {
            color: var(--dark);
        }

        .nav-links li.active a,
        .nav-links a:hover {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        /* Main Content */
        .main-content {
            padding: 30px 0;
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--secondary);
        }

        .dark-theme .page-title {
            color: var(--dark);
        }

        /* Cards */
        .card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px;
            margin-bottom: 20px;
        }

        .dark-theme .card {
            background-color: var(--secondary);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
        }

        /* Buttons */
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background-color: var(--primary);
            color: white;
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 14px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
        }

        .stat-label {
            color: var(--gray);
            font-size: 14px;
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
        }

        .requests-table {
            width: 100%;
            border-collapse: collapse;
        }

        .requests-table th,
        .requests-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }

        .requests-table th {
            font-weight: 600;
            color: var(--secondary);
        }

        .dark-theme .requests-table th {
            color: var(--dark);
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Requests Grid */
        .requests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .request-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px;
            text-align: center;
            transition: var(--transition);
        }

        .dark-theme .request-card {
            background-color: var(--secondary);
        }

        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .request-icon {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .request-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .request-description {
            color: var(--gray);
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid var(--light-gray);
            margin-bottom: 20px;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            transition: var(--transition);
            border-bottom: 3px solid transparent;
        }

        .tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Filters */
        .filters {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 600;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            background-color: white;
        }

        .dark-theme .filter-select {
            background-color: var(--secondary);
            color: var(--dark);
            border-color: var(--gray);
        }

        /* Documents Grid */
        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .document-card {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px;
            text-align: center;
            transition: var(--transition);
        }

        .dark-theme .document-card {
            background-color: var(--secondary);
        }

        .document-card:hover {
            transform: translateY(-3px);
        }

        .document-icon {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .document-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .document-date {
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 15px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background-color: white;
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .dark-theme .modal-content {
            background-color: var(--secondary);
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .modal-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        #requestForm {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 16px;
        }

        .dark-theme .form-group input,
        .dark-theme .form-group select,
        .dark-theme .form-group textarea {
            background-color: var(--light);
            color: var(--dark);
            border-color: var(--gray);
        }

        /* Footer */
        footer {
            background-color: var(--secondary);
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-top: 40px;
        }

        /* Document Item Styles */
        .document-item {
            transition: all 0.3s ease;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .document-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .document-item:last-child {
            border-bottom: none;
        }

        .btn-download {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .document-icon {
            font-size: 18px;
            margin-right: 8px;
        }

        .document-info {
            flex: 1;
        }

        .document-actions {
            display: flex;
            gap: 8px;
        }

        .text-center {
            text-align: center;
        }

        .spinner-border {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            white-space: nowrap;
            border: 0;
        }

        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }

        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .requests-grid,
            .documents-grid {
                grid-template-columns: 1fr;
            }

            .filters {
                flex-direction: column;
            }

            .header-content {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Main Application -->
    <div id="appContainer">
        <!-- Header -->
        <header>
            <div class="container">
                <div class="header-content">
                    <div class="logo">
                        <i class="fas fa-brain"></i>
                        <h1>Smart RH</h1>
                    </div>
                    <div class="user-menu">
                        <div class="user-info">
                            <div class="user-avatar" id="userAvatar"><?php echo strtoupper(substr($firstname, 0, 1)); ?></div>
                            <span id="userName"><?php echo $firstname . ' ' . $lastname; ?> (Employé)</span>
                        </div>
                        <div id="notifWrapper" style="position: relative;">
                            <button class="theme-toggle" id="notifToggle" title="Notifications" style="position: relative;">
                                <i class="fas fa-bell"></i>
                                <span id="notifBadge" class="notification-badge" style="display:none; position: absolute; top: -6px; right: -6px; background: #e74c3c; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center;">0</span>
                            </button>
                            <div id="notifDropdown" style="display:none; position:absolute; right:0; top:40px; width:320px; max-height:360px; overflow:auto; background:#fff; color:#333; border:1px solid #e5e7eb; border-radius:10px; box-shadow: 0 10px 20px rgba(0,0,0,0.12); z-index: 1000;">
                                <div style="padding:12px 14px; border-bottom:1px solid #f1f5f9; font-weight:600;">Notifications</div>
                                <div id="notifList"></div>
                                <div style="padding:10px; border-top:1px solid #f1f5f9; text-align:right;">
                                    <button id="markAllRead" class="btn btn-sm btn-outline" style="border:1px solid #cbd5e1; background:#fff; color:#334155;">Marquer comme lus</button>
                                </div>
                            </div>
                        </div>
                        <button class="theme-toggle" id="themeToggle">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main navigation -->
        <nav class="main-nav">
            <div class="container">
                <ul class="nav-links">
                    <li class="active"><a href="#" data-section="employee-space"><i class="fas fa-user"></i> Espace Employé</a></li>
                    <li><a href="#" data-section="new-request"><i class="fas fa-plus-circle"></i> Nouvelle Demande</a></li>
                    <li><a href="#" data-section="my-requests"><i class="fas fa-tasks"></i> Mes Demandes</a></li>
                    <li><a href="#" data-section="history"><i class="fas fa-history"></i> Historique</a></li>
                    <li><a href="#" data-section="documents"><i class="fas fa-file"></i> Mes Documents</a></li>
                    <li><a href="backend/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="main-content">
            <div class="container">
                <!-- Section Espace Employé -->
                <div id="employee-space" class="content-section active">
                    <div class="page-header">
                        <h2 class="page-title">Mon Espace Employé</h2>
                    </div>

                    <!-- Profile Card -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Mon Profil</h3>
                        </div>
                        <div class="profile-info">
                            <p><strong>Nom complet:</strong> <span id="profileName"><?php echo $firstname . ' ' . $lastname; ?></span></p>
                            <p><strong>Email:</strong> <span id="profileEmail"><?php echo $email; ?></span></p>
                            <p><strong>Rôle:</strong> <span id="profileRole"><?php echo $role; ?></span></p>
                        </div>
                    </div>

                    <!-- Stats Overview -->
                    <div class="dashboard-grid">
                        <div class="stats-grid">
                            <div class="card stat-card">
                                <div class="stat-icon" style="background-color: #e0f2fe; color: #0369a1;">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div>
                                    <div class="stat-value" id="totalRequests">0</div>
                                    <div class="stat-label">Demandes actives</div>
                                </div>
                            </div>
                            <div class="card stat-card">
                                <div class="stat-icon" style="background-color: #fef3c7; color: #92400e;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <div class="stat-value" id="pendingRequests">0</div>
                                    <div class="stat-label">En attente</div>
                                </div>
                            </div>
                            <div class="card stat-card">
                                <div class="stat-icon" style="background-color: #d1fae5; color: #065f46;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <div class="stat-value" id="approvedRequests">0</div>
                                    <div class="stat-label">Approuvées</div>
                                </div>
                            </div>
                            <div class="card stat-card">
                                <div class="stat-icon" style="background-color: #fee2e2; color: #b91c1c;">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div>
                                    <div class="stat-value" id="rejectedRequests">0</div>
                                    <div class="stat-label">Rejetées</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent requests -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Mes demandes récentes</h3>
                
                        </div>
                        <div class="table-responsive">
                            <table class="requests-table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Détails</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody id="recentRequestsTable">
                                    <tr><td colspan="4">Chargement...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Section Nouvelle Demande -->
                <div id="new-request" class="content-section">
                    <div class="page-header">
                        <h2 class="page-title">Nouvelle Demande</h2>
                    </div>

                    <div class="requests-grid">
                        <div class="request-card" data-type="leave">
                            <div class="request-icon">
                                <i class="fas fa-umbrella-beach"></i>
                            </div>
                            <h3 class="request-title">Demande de congé</h3>
                            <p class="request-description">Congé annuel, maladie, exceptionnel ou sans solde</p>
                            <button class="btn btn-primary">Créer une demande</button>
                        </div>

                        <div class="request-card" data-type="remote">
                            <div class="request-icon">
                                <i class="fas fa-laptop-house"></i>
                            </div>
                            <h3 class="request-title">Demande de télétravail</h3>
                            <p class="request-description">Travail à distance occasionnel ou régulier</p>
                            <button class="btn btn-primary">Créer une demande</button>
                        </div>

                        <div class="request-card" data-type="training">
                            <div class="request-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h3 class="request-title">Demande de formation</h3>
                            <p class="request-description">Formation interne ou externe pour développement de compétences</p>
                            <button class="btn btn-primary">Créer une demande</button>
                        </div>

                        <div class="request-card" data-type="document">
                            <div class="request-icon">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <h3 class="request-title">Demande de document</h3>
                            <p class="request-description">Attestation de travail, fiche de paie ou autre document RH</p>
                            <button class="btn btn-primary">Créer une demande</button>
                        </div>

                        <div class="request-card" data-type="equipment">
                            <div class="request-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h3 class="request-title">Demande de matériel</h3>
                            <p class="request-description">Matériel informatique, support technique ou logistique</p>
                            <button class="btn btn-primary">Créer une demande</button>
                        </div>
                    </div>
                </div>

                <!-- Section Mes Demandes -->
                <div id="my-requests" class="content-section">
                    <div class="page-header">
                        <h2 class="page-title">Mes Demandes</h2>
                    </div>

                    <div class="card">
                        <div class="tabs">
                            <div class="tab active" data-tab="requests">Toutes mes demandes</div>
                            <div class="tab" data-tab="pending">En attente</div>
                            <div class="tab" data-tab="approved">Approuvées</div>
                            <div class="tab" data-tab="rejected">Rejetées</div>
                        </div>

                        <div class="tab-content active" id="requests-tab">
                            <div class="filters">
                                <div class="filter-group">
                                    <span class="filter-label">Type de demande</span>
                                    <select class="filter-select" id="requestTypeFilter">
                                        <option value="all">Tous les types</option>
                                        <option value="leave">Congés</option>
                                        <option value="remote">Télétravail</option>
                                        <option value="training">Formation</option>
                                        <option value="document">Documents</option>
                                        <option value="equipment">Matériel</option>
                                    </select>
                                </div>
                                <div class="filter-group">
                                    <span class="filter-label">Période</span>
                                    <select class="filter-select" id="periodFilter">
                                        <option value="all">Toute période</option>
                                        <option value="month">Ce mois</option>
                                        <option value="quarter">Ce trimestre</option>
                                        <option value="year">Cette année</option>
                                    </select>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="requests-table">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Date de demande</th>
                                            <th>Détails</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody id="allRequestsTable">
                                        <tr><td colspan="4">Chargement...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Historique -->
                <div id="history" class="content-section">
                    <div class="page-header">
                        <h2 class="page-title">Historique des Demandes</h2>
                    </div>

                    <div class="card">
                        <div class="filters">
                            <div class="filter-group">
                                <span class="filter-label">Type de demande</span>
                                <select class="filter-select" id="historyTypeFilter">
                                    <option value="all">Tous les types</option>
                                    <option value="leave">Congés</option>
                                    <option value="remote">Télétravail</option>
                                    <option value="training">Formation</option>
                                    <option value="document">Documents</option>
                                    <option value="equipment">Matériel</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <span class="filter-label">Période</span>
                                <select class="filter-select" id="historyPeriodFilter">
                                    <option value="all">Toute période</option>
                                    <option value="month">Ce mois</option>
                                    <option value="quarter">Ce trimestre</option>
                                    <option value="year">Cette année</option>
                                    <option value="lastyear">L'année dernière</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="requests-table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Date de demande</th>
                                        <th>Détails</th>
                                        <th>Statut</th>
                                        <th>Date de traitement</th>
                                    </tr>
                                </thead>
                                <tbody id="historyTable">
                                    <tr><td colspan="5">Chargement...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Section Mes Documents - MODIFIÉE -->
                <div id="documents" class="content-section">
                    <div class="page-header">
                        <h2 class="page-title">Mes Documents</h2>
                    </div>

                    <!-- Section Documents améliorée -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-pdf" style="color: #e74c3c;"></i>
                                Mes Documents
                                <span id="documentsCount" class="badge" style="background: #e74c3c; color: white; margin-left: 8px;">0</span>
                            </h3>
                            <button class="btn btn-outline btn-sm" onclick="loadEmployeeDocuments()" title="Actualiser">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <div class="card-content">
                            <div id="employeeDocumentsList">
                                <div class="text-center" style="padding: 40px;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Chargement...</span>
                                    </div>
                                    <p class="mt-3">Chargement de vos documents...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal for request forms -->
        <div class="modal" id="requestModal">
            <div class="modal-content">
                <button class="modal-close" id="modalClose">&times;</button>
                <div class="modal-header">
                    <h3 class="modal-title" id="modalTitle">Nouvelle demande</h3>
                    <p id="modalDescription">Remplissez le formulaire pour soumettre votre demande</p>
                </div>
                <form id="requestForm" action="backend/request.php" method="POST">
                    <div id="formContent">
                        <!-- Form content will be loaded dynamically based on request type -->
                    </div>
                    <input type="hidden" id="requestType" name="type">
                    <div class="form-group">
                        <label for="comments">Commentaires supplémentaires</label>
                        <textarea id="comments" name="comments" rows="3" placeholder="Informations complémentaires..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Soumettre la demande</button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <div class="container">
                <p>&copy; 2023 Smart RH. Tous droits réservés.</p>
            </div>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const navLinks = document.querySelectorAll('.nav-links a[data-section]');
            const contentSections = document.querySelectorAll('.content-section');
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            const requestCards = document.querySelectorAll('.request-card');
            const modal = document.getElementById('requestModal');
            const modalClose = document.getElementById('modalClose');
            const requestForm = document.getElementById('requestForm');
            const notifToggle = document.getElementById('notifToggle');
            const notifBadge = document.getElementById('notifBadge');
            const notifDropdown = document.getElementById('notifDropdown');
            const notifList = document.getElementById('notifList');
            const markAllReadBtn = document.getElementById('markAllRead');

            // Gérer le changement de thème
            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-theme');
                const icon = themeToggle.querySelector('i');
                if (document.body.classList.contains('dark-theme')) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                    localStorage.setItem('theme', 'dark');
                } else {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                    localStorage.setItem('theme', 'light');
                }
            });

            // Appliquer le thème sauvegardé
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-theme');
                themeToggle.querySelector('i').classList.remove('fa-moon');
                themeToggle.querySelector('i').classList.add('fa-sun');
            }

            // Gérer la navigation entre les sections
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetSection = this.getAttribute('data-section');
                    
                    // Mettre à jour la navigation active
                    navLinks.forEach(l => l.parentElement.classList.remove('active'));
                    this.parentElement.classList.add('active');
                    
                    // Afficher la section cible
                    contentSections.forEach(section => {
                        section.classList.remove('active');
                    });
                    document.getElementById(targetSection).classList.add('active');

                    // Charger les documents si on clique sur la section documents
                    if (targetSection === 'documents') {
                        setTimeout(loadEmployeeDocuments, 100);
                    }
                });
            });

            // Gérer les onglets
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Mettre à jour les onglets actifs
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Afficher le contenu de l'onglet
                    tabContents.forEach(content => {
                        content.classList.remove('active');
                    });
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                });
            });

            // Gérer l'ouverture des formulaires de demande
            requestCards.forEach(card => {
                card.addEventListener('click', function() {
                    const requestType = this.getAttribute('data-type');
                    openRequestModal(requestType);
                });
            });

            // Gérer la fermeture de la modal
            modalClose.addEventListener('click', function() {
                modal.classList.remove('active');
            });

            // Fermer la modal en cliquant à l'extérieur
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });

            // Gérer la soumission du formulaire de demande
            requestForm.addEventListener('submit', function(e) {
                // La soumission se fera normalement vers request.php
                // Vous pouvez ajouter une validation supplémentaire ici si besoin
            });

            // Fonction pour charger l'historique
            function loadUserHistory() {
                fetch('backend/history_user.php', { credentials: 'include' })
                    .then(r => r.json())
                    .then(data => {
                        cachedHistory = data.history || [];
                        renderHistory();
                    })
                    .catch(error => {
                        console.error('Erreur chargement historique:', error);
                    });
            }

            // Charger l'historique au démarrage
            loadUserHistory();
            setInterval(loadUserHistory, 10000);

            // Recharger l'historique quand on clique sur l'onglet historique
            const historyLink = document.querySelector('a[data-section="history"]');
            if (historyLink) {
                historyLink.addEventListener('click', function() {
                    setTimeout(loadUserHistory, 100);
                });
            }

            // Fonction pour ouvrir la modal avec le formulaire approprié
            function openRequestModal(type) {
                const modalTitle = document.getElementById('modalTitle');
                const modalDescription = document.getElementById('modalDescription');
                const formContent = document.getElementById('formContent');
                const requestTypeInput = document.getElementById('requestType');
                
                let title = '';
                let description = '';
                let formHTML = '';
                
                switch(type) {
                    case 'leave':
                        title = 'Demande de congé';
                        description = 'Remplissez les informations pour votre demande de congé';
                        formHTML = `
                            <div class="form-group">
                                <label for="title">Titre de la demande</label>
                                <input type="text" id="title" name="title" value="Demande de congé" required>
                            </div>
                            <div class="form-group">
                                <label for="leaveType">Type de congé</label>
                                <select id="leaveType" name="description" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="Congé annuel">Congé annuel</option>
                                    <option value="Congé maladie">Congé maladie</option>
                                    <option value="Congé exceptionnel">Congé exceptionnel</option>
                                    <option value="Congé sans solde">Congé sans solde</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="startDate">Date de début</label>
                                <input type="date" id="startDate" name="start_date" required>
                            </div>
                            <div class="form-group">
                                <label for="endDate">Date de fin</label>
                                <input type="date" id="endDate" name="end_date" required>
                            </div>
                        `;
                        break;
                    case 'remote':
                        title = 'Demande de télétravail';
                        description = 'Remplissez les informations pour votre demande de télétravail';
                        formHTML = `
                            <div class="form-group">
                                <label for="title">Titre de la demande</label>
                                <input type="text" id="title" name="title" value="Demande de télétravail" required>
                            </div>
                            <div class="form-group">
                                <label for="remoteType">Type de télétravail</label>
                                <select id="remoteType" name="description" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="Télétravail régulier">Télétravail régulier (jours fixes)</option>
                                    <option value="Télétravail occasionnel">Télétravail occasionnel</option>
                                    <option value="Télétravail à temps plein">Télétravail à temps plein</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="startDate">Date de début</label>
                                <input type="date" id="startDate" name="start_date" required>
                            </div>
                            <div class="form-group">
                                <label for="remoteDays">Jours de télétravail par semaine</label>
                                <input type="number" id="remoteDays" name="remote_days" min="1" max="5">
                            </div>
                        `;
                        break;
                    case 'training':
                        title = 'Demande de formation';
                        description = 'Remplissez les informations pour votre demande de formation';
                        formHTML = `
                            <div class="form-group">
                                <label for="title">Titre de la demande</label>
                                <input type="text" id="title" name="title" placeholder="Intitulé de la formation" required>
                            </div>
                            <div class="form-group">
                                <label for="trainingProvider">Organisme de formation</label>
                                <input type="text" id="trainingProvider" name="description" placeholder="Nom de l'organisme" required>
                            </div>
                            <div class="form-group">
                                <label for="startDate">Date de début</label>
                                <input type="date" id="startDate" name="start_date" required>
                            </div>
                            <div class="form-group">
                                <label for="endDate">Date de fin</label>
                                <input type="date" id="endDate" name="end_date" required>
                            </div>
                            <div class="form-group">
                                <label for="trainingCost">Coût estimé (€)</label>
                                <input type="number" id="trainingCost" name="training_cost" min="0" step="0.01">
                            </div>
                        `;
                        break;
                    case 'document':
                        title = 'Demande de document';
                        description = 'Remplissez les informations pour votre demande de document';
                        formHTML = `
                            <div class="form-group">
                                <label for="title">Titre de la demande</label>
                                <input type="text" id="title" name="title" value="Demande de document" required>
                            </div>
                            <div class="form-group">
                                <label for="documentType">Type de document</label>
                                <select id="documentType" name="description" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="Attestation de travail">Attestation de travail</option>
                                    <option value="Fiche de paie">Fiche de paie</option>
                                    <option value="Certificat de travail">Certificat de travail</option>
                                    <option value="Autre document">Autre document</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="documentDetails">Détails supplémentaires</label>
                                <textarea id="documentDetails" name="document_details" rows="3" placeholder="Précisez vos besoins..."></textarea>
                            </div>
                        `;
                        break;
                    case 'equipment':
                        title = 'Demande de matériel';
                        description = 'Remplissez les informations pour votre demande de matériel';
                        formHTML = `
                            <div class="form-group">
                                <label for="title">Titre de la demande</label>
                                <input type="text" id="title" name="title" value="Demande de matériel" required>
                            </div>
                            <div class="form-group">
                                <label for="equipmentType">Type de matériel</label>
                                <select id="equipmentType" name="description" required>
                                    <option value="">Sélectionnez un type</option>
                                    <option value="Matériel informatique">Matériel informatique</option>
                                    <option value="Mobilier de bureau">Mobilier de bureau</option>
                                    <option value="Logiciel">Logiciel</option>
                                    <option value="Autre matériel">Autre matériel</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="equipmentDetails">Détails du matériel</label>
                                <textarea id="equipmentDetails" name="equipment_details" rows="3" placeholder="Décrivez le matériel demandé..." required></textarea>
                            </div>
                        `;
                        break;
                }
                
                modalTitle.textContent = title;
                modalDescription.textContent = description;
                formContent.innerHTML = formHTML;
                requestTypeInput.value = type;
                modal.classList.add('active');
            }
            
            // Notifications UI
            function renderNotifications(notifs) {
                if (!Array.isArray(notifs)) notifs = [];
                if (notifs.length > 0) {
                    notifBadge.style.display = 'flex';
                    notifBadge.textContent = String(notifs.length);
                } else {
                    notifBadge.style.display = 'none';
                }
                notifList.innerHTML = notifs.map(n => `
                    <div style="padding:12px 14px; border-bottom:1px solid #f1f5f9;">
                        <div style="font-weight:600; color:#0f172a; margin-bottom:4px;">${n.title}</div>
                        <div style="font-size:13px; color:#475569;">${n.message}</div>
                        <div style="font-size:12px; color:#94a3b8; margin-top:6px;">${n.created_at}</div>
                    </div>
                `).join('') || '<div style="padding:14px; color:#64748b;">Aucune notification</div>';
            }

            function fetchNotifications() {
                fetch('backend/user_notifications.php', { credentials: 'include' })
                    .then(r => r.json())
                    .then(data => {
                        renderNotifications(data.notifications || []);
                    }).catch(() => {});
            }

            // Initial fetch + polling
            fetchNotifications();
            setInterval(fetchNotifications, 15000);

            // Toggle dropdown
            if (notifToggle) {
                notifToggle.addEventListener('click', function() {
                    const isOpen = notifDropdown.style.display === 'block';
                    notifDropdown.style.display = isOpen ? 'none' : 'block';
                });
            }

            // Click outside to close
            window.addEventListener('click', function(e) {
                const wrap = document.getElementById('notifWrapper');
                if (wrap && !wrap.contains(e.target)) {
                    notifDropdown.style.display = 'none';
                }
            });

            // Mark all as read
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function() {
                    fetch('backend/user_notifications.php', { method: 'POST', credentials: 'include' })
                        .then(() => fetchNotifications());
                });
            }

            // Load user requests data
            function loadUserRequests() {
                fetch('backend/request_user.php', { credentials: 'include' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.requests && data.stats) {
                            cachedRequests = data.requests.slice();
                            // Update stats
                            document.getElementById('totalRequests').textContent = 
                                (data.stats.pending || 0) + (data.stats.approved || 0) + (data.stats.rejected || 0);
                            document.getElementById('pendingRequests').textContent = data.stats.pending || 0;
                            document.getElementById('approvedRequests').textContent = data.stats.approved || 0;
                            document.getElementById('rejectedRequests').textContent = data.stats.rejected || 0;

                            // Update recent requests (last 3)
                            const recentRequests = data.requests.slice(0, 3);
                            const recentTable = document.getElementById('recentRequestsTable');
                            if (recentTable) {
                                recentTable.innerHTML = recentRequests.map(req => {
                                    const statusClass = req.status === 'pending' ? 'status-pending' : 
                                                      (req.status === 'approved' ? 'status-approved' : 'status-rejected');
                                    const statusText = req.status === 'pending' ? 'En attente' : 
                                                     (req.status === 'approved' ? 'Approuvée' : 'Rejetée');
                                    const details = req.start_date && req.end_date ? 
                                        `Du ${req.start_date} au ${req.end_date}` : 
                                        req.description || req.title;
                                    return `
                                        <tr>
                                            <td>${req.type}</td>
                                            <td>${new Date(req.created_at).toLocaleDateString('fr-FR')}</td>
                                            <td>${details}</td>
                                            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                                        </tr>
                                    `;
                                }).join('') || '<tr><td colspan="4">Aucune demande</td></tr>';
                            }

                            // Update all requests table
                            renderAllRequests();
                        }
                    })
                    .catch(() => {
                        console.error('Erreur lors du chargement des demandes');
                    });
            }

            // References for filters
            const requestTypeFilter = document.getElementById('requestTypeFilter');
            const periodFilter = document.getElementById('periodFilter');
            const historyTypeFilter = document.getElementById('historyTypeFilter');
            const historyPeriodFilter = document.getElementById('historyPeriodFilter');

            // Keep data in memory for filtering
            let cachedRequests = [];
            let cachedHistory = [];

            function withinPeriod(dateStr, period) {
                if (!period || period === 'all') return true;
                const d = new Date(dateStr);
                const now = new Date();
                if (period === 'month') {
                    return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
                }
                if (period === 'quarter') {
                    const q = Math.floor(d.getMonth() / 3);
                    const qNow = Math.floor(now.getMonth() / 3);
                    return q === qNow && d.getFullYear() === now.getFullYear();
                }
                if (period === 'year') {
                    return d.getFullYear() === now.getFullYear();
                }
                if (period === 'lastyear') {
                    return d.getFullYear() === (now.getFullYear() - 1);
                }
                return true;
            }

            function renderAllRequests() {
                const typeVal = requestTypeFilter ? requestTypeFilter.value : 'all';
                const periodVal = periodFilter ? periodFilter.value : 'all';
                const allRequestsTable = document.getElementById('allRequestsTable');
                if (!allRequestsTable) return;
                const filtered = cachedRequests.filter(req => {
                    const typeOk = typeVal === 'all' || req.type === typeVal;
                    const periodOk = withinPeriod(req.created_at, periodVal);
                    return typeOk && periodOk;
                });
                allRequestsTable.innerHTML = filtered.map(req => {
                    const statusClass = req.status === 'pending' ? 'status-pending' : 
                                      (req.status === 'approved' ? 'status-approved' : 'status-rejected');
                    const statusText = req.status === 'pending' ? 'En attente' : 
                                     (req.status === 'approved' ? 'Approuvée' : 'Rejetée');
                    const details = req.start_date && req.end_date ? 
                        `Du ${req.start_date} au ${req.end_date}` : 
                        req.description || req.title;
                    return `
                        <tr>
                            <td>${req.type}</td>
                            <td>${new Date(req.created_at).toLocaleDateString('fr-FR')}</td>
                            <td>${details}</td>
                            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        </tr>
                    `;
                }).join('') || '<tr><td colspan="4">Aucune demande</td></tr>';
            }

            function renderHistory() {
                const typeVal = historyTypeFilter ? historyTypeFilter.value : 'all';
                const periodVal = historyPeriodFilter ? historyPeriodFilter.value : 'all';
                const table = document.getElementById('historyTable');
                if (!table) return;
                const filtered = cachedHistory.filter(req => {
                    const typeOk = typeVal === 'all' || req.type === typeVal;
                    const periodOk = withinPeriod(req.created_at, periodVal);
                    return typeOk && periodOk;
                });
                table.innerHTML = filtered.length ? filtered.map(req => {
                    const statusClass = req.status === 'pending' ? 'status-pending' : 
                        (req.status === 'approved' ? 'status-approved' : 'status-rejected');
                    const statusText = req.status === 'pending' ? 'En attente' : 
                        (req.status === 'approved' ? 'Approuvée' : 'Rejetée');
                    const details = req.start_date && req.end_date
                        ? `Du ${req.start_date} au ${req.end_date}`
                        : req.description || req.title;
                    return `
                        <tr>
                            <td>${req.type}</td>
                            <td>${new Date(req.created_at).toLocaleDateString('fr-FR')}</td>
                            <td>${details}</td>
                            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                            <td>${req.updated_at ? new Date(req.updated_at).toLocaleDateString('fr-FR') : '-'}</td>
                        </tr>
                    `;
                }).join('') : '<tr><td colspan="5">Aucune demande</td></tr>';
            }

            // NOUVELLE FONCTION POUR CHARGER LES DOCUMENTS
            function loadEmployeeDocuments() {
                const container = document.getElementById('employeeDocumentsList');
                const countBadge = document.getElementById('documentsCount');
                
                // Afficher le loader
                container.innerHTML = `
                    <div class="text-center" style="padding: 40px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                        <p class="mt-3">Chargement de vos documents...</p>
                    </div>
                `;

                fetch('backend/get_employee_documents.php', {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Documents reçus:', data);
                    
                    if (data.success && data.documents && data.documents.length > 0) {
                        // Mettre à jour le compteur
                        countBadge.textContent = data.documents.length;
                        
                        // Afficher les documents
                        container.innerHTML = data.documents.map(doc => `
                            <div class="document-item" style="padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: white; border-radius: 8px; margin-bottom: 10px;">
                                <div class="document-info" style="flex: 1;">
                                    <div style="font-weight: bold; color: #2c3e50; margin-bottom: 8px; display: flex; align-items: center;">
                                        <i class="fas fa-file-pdf document-icon" style="color: #e74c3c;"></i>
                                        ${doc.title}
                                    </div>
                                    <div style="display: flex; gap: 15px; font-size: 12px; color: #7f8c8d;">
                                        <span><i class="fas fa-calendar"></i> ${doc.uploaded_at}</span>
                                        <span><i class="fas fa-tag"></i> ${doc.type}</span>
                                        ${doc.file_size ? `<span><i class="fas fa-hdd"></i> ${(doc.file_size / 1024 / 1024).toFixed(2)} MB</span>` : ''}
                                    </div>
                                </div>
                                <div class="document-actions">
                                    <a href="${doc.file_path}" class="btn-download" target="_blank" download="${doc.file_name}">
                                        <i class="fas fa-download"></i>
                                        Télécharger
                                    </a>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        countBadge.textContent = '0';
                        container.innerHTML = `
                            <div class="text-center" style="padding: 60px 20px; color: #95a5a6;">
                                <i class="fas fa-folder-open" style="font-size: 64px; margin-bottom: 20px; opacity: 0.3;"></i>
                                <h4 style="color: #7f8c8d; margin-bottom: 10px;">Aucun document disponible</h4>
                                <p style="margin-bottom: 5px;">Les documents approuvés par l'administration apparaîtront ici.</p>
                                <small>Vos demandes de documents doivent être approuvées pour être visibles.</small>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Erreur chargement documents:', error);
                    countBadge.textContent = '!';
                    countBadge.style.background = '#e74c3c';
                    container.innerHTML = `
                        <div class="text-center" style="padding: 40px; color: #e74c3c;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 15px;"></i>
                            <h4>Erreur de chargement</h4>
                            <p>Impossible de charger vos documents: ${error.message}</p>
                            <button class="btn btn-primary btn-sm" onclick="loadEmployeeDocuments()">
                                <i class="fas fa-sync-alt"></i>
                                Réessayer
                            </button>
                        </div>
                    `;
                });
            }

            // Load data on page load
            loadUserRequests();
            
            // Charger les documents au démarrage si on est dans la section documents
            if (document.getElementById('documents').classList.contains('active')) {
                loadEmployeeDocuments();
            }
            
            // Reload data when switching to my-requests section
            const myRequestsLink = document.querySelector('a[data-section="my-requests"]');
            if (myRequestsLink) {
                myRequestsLink.addEventListener('click', function() {
                    setTimeout(loadUserRequests, 100);
                });
            }

            // Wire filters: My Requests
            if (requestTypeFilter) requestTypeFilter.addEventListener('change', renderAllRequests);
            if (periodFilter) periodFilter.addEventListener('change', renderAllRequests);

            // Wire filters: History
            if (historyTypeFilter) historyTypeFilter.addEventListener('change', renderHistory);
            if (historyPeriodFilter) historyPeriodFilter.addEventListener('change', renderHistory);

            // Ensure "Voir tout" opens all requests and resets filters
            const viewAllRecent = document.querySelector('.card-header a.btn.btn-outline.btn-sm[data-section="my-requests"], a[data-section="my-requests"]');
            if (viewAllRecent) {
                viewAllRecent.addEventListener('click', function() {
                    if (requestTypeFilter) requestTypeFilter.value = 'all';
                    if (periodFilter) periodFilter.value = 'all';
                    // Switch to My Requests section already handled by nav
                    // Ensure the first tab is active
                    const firstTab = document.querySelector('.tab[data-tab="requests"]');
                    if (firstTab) firstTab.click();
                    setTimeout(renderAllRequests, 150);
                });
            }

            // Rafraîchir les documents quand la page redevient visible
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden && document.getElementById('documents').classList.contains('active')) {
                    loadEmployeeDocuments();
                }
            });

            // Exposer la fonction globalement pour le bouton d'actualisation
            window.loadEmployeeDocuments = loadEmployeeDocuments;
        });
    </script>
</body>
</html>