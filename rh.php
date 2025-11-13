<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: log.html");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: employer.php");
    exit;
}

$firstname = htmlspecialchars($_SESSION['firstname']);
$lastname = htmlspecialchars($_SESSION['lastname']);
$email = htmlspecialchars($_SESSION['email']);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Administrateur RH - Smart RH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assests/rh.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-brain"></i>
            <h2>Smart RH</h2>
        </div>
        <nav class="sidebar-menu">
            <a href="#" class="menu-item active" data-target="dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="#" class="menu-item" data-target="employee-management">
                <i class="fas fa-users"></i>
                <span>Gestion des employés</span>
            </a>
            <a href="#" class="menu-item" data-target="requests-management">
                <i class="fas fa-file-alt"></i>
                <span>Demandes des employés</span>
            </a>
            <a href="backend/logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <button class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="user-menu" style="margin-left:auto;">
                <div class="notification-icon" style="position: relative; margin-left:auto;">
                    <button id="rhNotifToggle" class="theme-toggle" title="Notifications" style="position: relative; color:inherit;">
                        <i class="fas fa-bell"></i>
                        <span id="rhNotifBadge" class="notification-badge" style="display:none; position:absolute; top:-6px; right:-6px; transform: translateX(50%);">0</span>
                    </button>
                    <div id="rhNotifDropdown" style="display:none; position:absolute; right:0; top:40px; width:340px; max-height:360px; overflow:auto; background:#fff; color:#333; border:1px solid #e5e7eb; border-radius:10px; box-shadow: 0 10px 20px rgba(0,0,0,0.12);">
                        <div style="padding:12px 14px; border-bottom:1px solid #f1f5f9; font-weight:600;">Notifications</div>
                        <div id="rhNotifList"></div>
                        <div style="padding:10px; border-top:1px solid #f1f5f9; text-align:right;">
                            <button id="rhMarkAllRead" class="btn btn-sm btn-outline" style="border:1px solid #cbd5e1; background:#fff; color:#334155;">Marquer comme lus</button>
                        </div>
                    </div>
                </div>
                <div class="user-info">
                    <div class="user-avatar" id="userAvatar"><?php echo strtoupper(substr($firstname, 0, 1)); ?></div>
                    <span id="userName"><?php echo $firstname . ' ' . $lastname; ?> (Admin)</span>
                </div>
            </div>
        </header>

        <!-- Dashboard Section -->
        <section id="dashboard" class="dashboard content-section">
            <h1 class="page-title">
                <i class="fas fa-tachometer-alt"></i>
                Tableau de Bord Administrateur
            </h1>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value" id="totalEmployees">0</div>
                    <div class="stat-label">Employés actifs</div>
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="pendingRequests">0</div>
                    <div class="stat-label">Demandes en attente</div>
                    <div class="stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="approvedRequests">0</div>
                    <div class="stat-label">Demandes approuvées</div>
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="rejectedRequests">0</div>
                    <div class="stat-label">Demandes rejetées</div>
                    <div class="stat-icon purple">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Left Column -->
                <div class="left-column">
                    <!-- Pending Requests -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clock"></i>
                                Demandes en attente
                            </h3>
                            <a href="#" class="view-all" id="viewAllRequests">Voir tout</a>
                        </div>
                        <div class="card-content" id="pendingRequestsList">
                            <div class="request-item">
                                <div class="request-info">
                                    <div class="request-name">Chargement...</div>
                                    <div class="request-details">Veuillez patienter</div>
                                </div>
                            </div>
                        </div>
                    </div><br><br>

                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history"></i>
                                Activité récente
                            </h3>
                            <a href="#" class="view-all" id="viewAllActivities">Voir tout</a>
                        </div>
                        <div class="card-content" id="recentActivityList">
                            <div class="activity-item">
                                <div class="activity-icon success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">Chargement...</div>
                                    <div class="activity-desc">Veuillez patienter</div>
                                    <div class="activity-time">...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="right-column">
                <img src="rh.png" height="400px" alt=""> <br><br><br><br><br><br><br>
                <img src="1.jpg" height="400px" >
                    </div>
                
            </div>
        </section>

        <!-- Requests Management Section -->
        <section id="requests-management" class="content-section" style="display: none;">
            <h1 class="page-title"><i class="fas fa-file-alt"></i> Demandes des employés</h1>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-filter"></i> Filtres</h3>
                    <div>
                        <select id="reqStatusFilter" class="filter-select">
                            <option value="">Tous les statuts</option>
                            <option value="pending">En attente</option>
                            <option value="approved">Approuvées</option>
                            <option value="rejected">Rejetées</option>
                        </select>
                        <select id="reqTypeFilter" class="filter-select">
                            <option value="">Tous les types</option>
                            <option value="leave">Congé</option>
                            <option value="document">Document</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list"></i> Liste des demandes</h3>
                    <div class="requests-stats">
                        <span id="totalRequestsCount">0</span> demandes au total
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Employé</th>
                                <th>Type</th>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Période</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reqTableBody">
                            <tr><td colspan="9">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Employee Management Section -->
        <section id="employee-management" class="content-section" style="display: none;">
            <h1 class="page-title">
                <i class="fas fa-users"></i>
                Gestion des employés
            </h1>

            <div class="card" style="margin-bottom: 20px;">
                <div class="card-header">
                    <h3 class="card-title" style="display:flex; align-items:center; gap:8px;"><i class="fas fa-toolbox"></i> Outils</h3>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <button class="btn btn-primary btn-sm" id="addEmployeeBtn"><i class="fas fa-user-plus"></i> Nouvel employé</button>
                       
                    </div>
                </div>
                <div class="card-content">
                    <div class="filters" style="row-gap: 12px;">
                        <div class="filter-group" style="flex: 1; min-width: 260px;">
                            <span class="filter-label">Rechercher un employé</span>
                            <input type="text" id="employeeSearch" class="filter-select" placeholder="Tapez une lettre (A) ou un nom complet (Ali)..." style="width: 100%;">
                        </div>
                        <div class="filter-group" style="flex: 2;">
                            <span class="filter-label">Filtrer par alphabet</span>
                            <div id="alphaBar" style="display: flex; flex-wrap: wrap; gap: 6px;">
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="">Tous</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="A">A</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="B">B</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="C">C</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="D">D</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="E">E</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="F">F</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="G">G</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="H">H</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="I">I</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="J">J</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="K">K</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="L">L</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="M">M</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="N">N</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="O">O</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="P">P</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="Q">Q</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="R">R</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="S">S</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="T">T</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="U">U</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="V">V</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="W">W</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="X">X</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="Y">Y</button>
                                <button type="button" class="btn btn-outline btn-sm" data-alpha="Z">Z</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="display:flex; align-items:center; gap:8px;"><i class="fas fa-id-card"></i> Liste des employés</h3>
                    <div class="employees-stats">
                        <span id="totalEmployeesCount">0</span> employés actifs
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="requests-table" style="border-radius: 8px; overflow: hidden;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom Complet</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Date d'embauche</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employeeTableBody">
                            <tr><td colspan="7">Chargement...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Inline Add Employee Form -->
            <div class="card" id="inlineAddCard" style="display: none; margin-top: 20px;">
                <div class="card-header">
                    <h3 class="card-title" style="display:flex; align-items:center; gap:8px;"><i class="fas fa-user-plus"></i> Ajouter un employé</h3>
                    <button type="button" class="btn btn-outline btn-sm" id="closeAddFormBtn"><i class="fas fa-times"></i> Fermer</button>
                </div>
                <div class="card-content">
                    <form id="addEmployeeForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; align-items: end;">
                        <div class="form-group">
                            <label for="empFirstname">Prénom *</label>
                            <input type="text" id="empFirstname" name="firstname" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="empLastname">Nom *</label>
                            <input type="text" id="empLastname" name="lastname" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="empEmail">Email *</label>
                            <input type="email" id="empEmail" name="email" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="empPassword">Mot de passe *</label>
                            <input type="text" id="empPassword" name="password" required class="form-input" placeholder="Générer automatiquement">
                            <button type="button" class="btn btn-outline btn-sm" id="generatePasswordBtn" style="margin-top: 5px;">
                                <i class="fas fa-sync-alt"></i> Générer
                            </button>
                        </div>
                        <div class="form-group">
                            <label for="empRole">Rôle</label>
                            <select id="empRole" name="role" class="form-input">
                                <option value="employee">Employé</option>
                                <option value="manager">Manager</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Ajouter l'employé</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- Modal pour voir les détails d'une demande -->
    <div id="requestModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Détails de la demande</h3>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body" id="requestModalBody">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>

    <script src="assests/style.js"></script>
    <script>
        // Navigation sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar');
            const menuItems = document.querySelectorAll('.menu-item[data-target]');
            const contentSections = document.querySelectorAll('.content-section');
            const employeeSearchInput = document.getElementById('employeeSearch');
            const employeeTableBody = document.getElementById('employeeTableBody');
            const addEmployeeBtn = document.getElementById('addEmployeeBtn');
            const addEmployeeForm = document.getElementById('addEmployeeForm');
            const inlineAddCard = document.getElementById('inlineAddCard');
            const closeAddFormBtn = document.getElementById('closeAddFormBtn');
            const generatePasswordBtn = document.getElementById('generatePasswordBtn');
            const alphaBar = document.getElementById('alphaBar');
            let currentAlpha = '';

            // Requests management refs
            const reqStatusFilter = document.getElementById('reqStatusFilter');
            const reqTypeFilter = document.getElementById('reqTypeFilter');
            const reqTableBody = document.getElementById('reqTableBody');
            const totalRequestsCount = document.getElementById('totalRequestsCount');
            const totalEmployeesCount = document.getElementById('totalEmployeesCount');

            // Modal elements
            const requestModal = document.getElementById('requestModal');
            const requestModalBody = document.getElementById('requestModalBody');
            const closeModalBtn = document.querySelector('.close-modal');

            // View all buttons
            const viewAllRequests = document.getElementById('viewAllRequests');
            const viewAllActivities = document.getElementById('viewAllActivities');

            // Toggle sidebar on mobile
            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('active');
                });
            }

            // Navigation between sections
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = this.getAttribute('data-target');
                    
                    // Hide all sections
                    contentSections.forEach(section => {
                        section.style.display = 'none';
                    });
                    
                    // Show target section
                    if (target) {
                        document.getElementById(target).style.display = 'block';
                    }
                    
                    // Update active menu item
                    menuItems.forEach(menuItem => {
                        menuItem.classList.remove('active');
                    });
                    this.classList.add('active');
                });
            });

            // View all requests - navigate to requests management
            if (viewAllRequests) {
                viewAllRequests.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Navigate to requests management section
                    document.querySelector('.menu-item[data-target="requests-management"]').click();
                });
            }

            // View all activities - show all activities
            if (viewAllActivities) {
                viewAllActivities.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadRecentActivity(true); // true = show all activities
                });
            }

            // Modal functionality
            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', function() {
                    requestModal.style.display = 'none';
                });
            }

            window.addEventListener('click', function(e) {
                if (e.target === requestModal) {
                    requestModal.style.display = 'none';
                }
            });

            // Generate password
            if (generatePasswordBtn) {
                generatePasswordBtn.addEventListener('click', function() {
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';
                    let password = '';
                    for (let i = 0; i < 12; i++) {
                        password += chars.charAt(Math.floor(Math.random() * chars.length));
                    }
                    document.getElementById('empPassword').value = password;
                });
            }


            // Close add form
            if (closeAddFormBtn) {
                closeAddFormBtn.addEventListener('click', function() {
                    inlineAddCard.style.display = 'none';
                    addEmployeeForm.reset();
                });
            }

            // Employee management logic
            function fetchEmployees(query = '', starts = '') {
                let url = 'backend/users.php';
                const params = new URLSearchParams();
                if (query) params.append('q', query);
                if (starts) params.append('starts', starts);
                const qs = params.toString();
                if (qs) url += `?${qs}`;
                
                fetch(url, { credentials: 'include' })
                    .then(r => {
                        if (!r.ok) throw new Error('Erreur réseau');
                        return r.json();
                    })
                    .then(data => {
                        const users = data.users || [];
                       employeeTableBody.innerHTML = users.map(u => `
    <tr>
        <td>${u.id}</td>
        <td>
            <div class="employee-name">${u.firstname} ${u.lastname}</div>
            <div class="employee-email">${u.email}</div>
        </td>
        <td>${u.email}</td>
        <td><span class="status-badge role-${u.role}">${u.role}</span></td>
        <td><span class="status-badge ${u.is_active ? 'status-active' : 'status-inactive'}">${u.is_active ? 'Actif' : 'Inactif'}</span></td>
        <td>${u.created_at ? new Date(u.created_at).toLocaleDateString('fr-FR') : 'N/A'}</td>
        <td>
            <div class="action-buttons">
                <button class="btn btn-danger btn-sm" data-action="delete" data-id="${u.id}" title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
`).join('');
                        
                        totalEmployeesCount.textContent = users.filter(u => u.is_active).length;
                        
                        if (users.length === 0) {
                            employeeTableBody.innerHTML = '<tr><td colspan="7" class="text-center">Aucun employé trouvé</td></tr>';
                        }
                    })
                    .catch((error) => {
                        console.error('Erreur fetchEmployees:', error);
                        employeeTableBody.innerHTML = '<tr><td colspan="7" class="text-center">Erreur de chargement: ' + error.message + '</td></tr>';
                    });
            }

            // Open employee management by default
            const employeeMenu = document.querySelector('.menu-item[data-target="employee-management"]');
            if (employeeMenu) {
                employeeMenu.click();
                fetchEmployees('', currentAlpha);
            }

            // Admin notifications logic
            function renderRhNotifs(notifs) {
                if (!Array.isArray(notifs)) notifs = [];
                if (notifs.length > 0) {
                    rhNotifBadge.style.display = 'flex';
                    rhNotifBadge.textContent = String(notifs.length);
                } else {
                    rhNotifBadge.style.display = 'none';
                }
                rhNotifList.innerHTML = notifs.map(n => `
                    <div style="padding:12px 14px; border-bottom:1px solid #f1f5f9;">
                        <div style="font-weight:600; color:#0f172a; margin-bottom:4px;">${n.title}</div>
                        <div style="font-size:13px; color:#475569;">${n.message}</div>
                        <div style="font-size:12px; color:#94a3b8; margin-top:6px;">${n.created_at}</div>
                    </div>
                `).join('') || '<div style="padding:14px; color:#64748b;">Aucune notification</div>';
            }

            function fetchRhNotifs() {
                fetch('backend/admin_notifications.php', { credentials: 'include' })
                    .then(r => {
                        if (!r.ok) throw new Error('Erreur réseau');
                        return r.json();
                    })
                    .then(data => renderRhNotifs(data.notifications || []))
                    .catch((error) => {
                        console.error('Erreur fetchRhNotifs:', error);
                    });
            }
            fetchRhNotifs();
            setInterval(fetchRhNotifs, 5000);

            if (rhNotifToggle) {
                rhNotifToggle.addEventListener('click', function() {
                    const open = rhNotifDropdown.style.display === 'block';
                    rhNotifDropdown.style.display = open ? 'none' : 'block';
                });
            }

            window.addEventListener('click', function(e) {
                const iconWrap = document.querySelector('.notification-icon');
                if (iconWrap && !iconWrap.contains(e.target)) {
                    rhNotifDropdown.style.display = 'none';
                }
            });

            if (rhMarkAllRead) {
                rhMarkAllRead.addEventListener('click', function() {
                    fetch('backend/admin_notifications.php', { 
                        method: 'POST', 
                        credentials: 'include' 
                    })
                    .then(r => {
                        if (!r.ok) throw new Error('Erreur réseau');
                        return r.json();
                    })
                    .then(() => fetchRhNotifs())
                    .catch((error) => {
                        console.error('Erreur rhMarkAllRead:', error);
                        alert('Erreur lors du marquage des notifications comme lues');
                    });
                });
            }

            // REQUESTS MANAGEMENT - CORRIGÉ POUR TEMPS RÉEL
            function fetchRequests(status = '', type = '') {
                let url = 'backend/request_admin.php';
                const params = new URLSearchParams();
                if (status) params.append('status', status);
                if (type) params.append('type', type);
                const qs = params.toString();
                if (qs) url += `?${qs}`;
                
                fetch(url, { credentials: 'include' })
                    .then(r => {
                        if (!r.ok) throw new Error('Erreur réseau');
                        return r.json();
                    })
                    .then(data => {
                        const requests = data.requests || [];
                        totalRequestsCount.textContent = requests.length;
                        
                        const rows = requests.map(r => {
                            const period = (r.start_date || r.end_date) ? `${r.start_date || ''} → ${r.end_date || ''}` : '-';
                            const statusBadge = r.status === 'pending' ? 'status-pending' : (r.status === 'approved' ? 'status-approved' : 'status-rejected');
                            const typeBadge = `type-${r.type}`;
                            
                            let actionBtns = '';
                            
                            if (r.status === 'pending') {
                                actionBtns += `
                                    <button class="btn btn-success btn-sm" data-action="approve" data-id="${r.id}" title="Approuver">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" data-action="reject" data-id="${r.id}" title="Rejeter">
                                        <i class="fas fa-times"></i>
                                    </button>
                                `;
                            } else if (r.status === 'approved' && r.type === 'document') {
                                const hasDoc = (parseInt(r.doc_count || 0, 10) > 0) || r.file_path;
                                actionBtns += `
                                    <label class="btn btn-primary btn-sm" style="cursor:pointer; position:relative;" title="${hasDoc ? 'Remplacer le PDF' : 'Téléverser le PDF'}">
                                        <i class="fas fa-upload"></i>
                                        <input type="file" accept=".pdf,application/pdf" data-action="upload_document" data-id="${r.id}" style="display:none;" />
                                    </label>
                                    ${hasDoc ? `<button class="btn btn-success btn-sm" data-action="view_document" data-id="${r.id}" title="Voir le document"><i class="fas fa-file-pdf"></i></button>` : ''}
                                `;
                            }
                            
                            return `
                                <tr>
                                    <td>${r.id}</td>
                                    <td>
                                        <strong>${r.firstname} ${r.lastname}</strong><br>
                                        <small style="color:#666;">${r.email}</small>
                                    </td>
                                    <td><span class="status-badge ${typeBadge}">${r.type}</span></td>
                                    <td>${r.title}</td>
                                    <td>${r.description ? (r.description.length > 50 ? r.description.substring(0, 50) + '...' : r.description) : '-'}</td>
                                    <td>${period}</td>
                                    <td><span class="status-badge ${statusBadge}">${r.status}</span></td>
                                    <td>${r.created_at ? new Date(r.created_at).toLocaleDateString('fr-FR') : 'N/A'}</td>
                                    <td>
                                        <div class="action-buttons">
                                            ${actionBtns}
                                        </div>
                                    </td>
                                </tr>`;
                        }).join('');
                        
                        reqTableBody.innerHTML = rows || '<tr><td colspan="9" class="text-center">Aucune demande</td></tr>';
                    })
                    .catch((error) => {
                        console.error('Erreur fetchRequests:', error);
                        reqTableBody.innerHTML = '<tr><td colspan="9" class="text-center">Erreur de chargement: ' + error.message + '</td></tr>';
                    });
            }

            // NOUVELLE FONCTION POUR LE TEMPS RÉEL DU DASHBOARD
            function loadDashboardRealtimeData() {
                // Charger les demandes en attente pour le dashboard
                fetch('backend/request_admin.php?status=pending', { credentials: 'include' })
                    .then(r => {
                        if (!r.ok) throw new Error('Erreur réseau');
                        return r.json();
                    })
                    .then(data => {
                        const pendingRequests = data.requests || [];
                        const pendingList = document.getElementById('pendingRequestsList');
                        
                        if (pendingList) {
                            if (pendingRequests.length > 0) {
                                // Afficher seulement les 5 premières demandes
                                const requestsToShow = pendingRequests.slice(0, 5);
                                pendingList.innerHTML = requestsToShow.map(req => {
                                    const period = req.start_date && req.end_date ? 
                                        `${req.start_date} → ${req.end_date}` : 
                                        (req.description ? req.description.substring(0, 30) + (req.description.length > 30 ? '...' : '') : req.title);
                                    return `
                                        <div class="request-item">
                                            <div class="request-info">
                                                <div class="request-name">${req.firstname} ${req.lastname}</div>
                                                <div class="request-details">${req.type} • ${period}</div>
                                            </div>
                                            <div class="request-actions">
                                                <button class="btn btn-success btn-sm" data-action="approve" data-id="${req.id}">✓</button>
                                                <button class="btn btn-danger btn-sm" data-action="reject" data-id="${req.id}">✕</button>
                                            </div>
                                        </div>
                                    `;
                                }).join('');
                                
                                // Ajouter les écouteurs d'événements pour les boutons
                                pendingList.querySelectorAll('button[data-action]').forEach(btn => {
                                    btn.addEventListener('click', function() {
                                        const id = this.getAttribute('data-id');
                                        const action = this.getAttribute('data-action');
                                        const fd = new FormData();
                                        fd.append('id', id);
                                        fd.append('action', action);
                                        
                                        fetch('backend/request_admin.php', { 
                                            method: 'POST', 
                                            body: fd, 
                                            credentials: 'include' 
                                        })
                                            .then(r => {
                                                if (!r.ok) throw new Error('Erreur réseau');
                                                return r.json();
                                            })
                                            .then(resp => {
                                                if (resp && resp.success) {
                                                    // Recharger les données en temps réel
                                                    loadDashboardRealtimeData();
                                                    loadDashboardStats();
                                                    // Si on est dans la section demandes, recharger aussi
                                                    if (document.getElementById('requests-management').style.display !== 'none') {
                                                        fetchRequests(reqStatusFilter.value, reqTypeFilter.value);
                                                    }
                                                }
                                            })
                                            .catch((error) => {
                                                console.error('Erreur dashboard action:', error);
                                                alert('Erreur: ' + error.message);
                                            });
                                    });
                                });
                            } else {
                                pendingList.innerHTML = '<div class="request-item"><div class="request-info"><div class="request-name">Aucune demande en attente</div></div></div>';
                            }
                        }

                        // Charger l'activité récente
                        loadRecentActivity(false); // false = afficher seulement les 5 premières
                    })
                    .catch((error) => {
                        console.error('Erreur loadDashboardRealtimeData:', error);
                    });
            }

            // NOUVELLE FONCTION POUR L'ACTIVITÉ RÉCENTE
            function loadRecentActivity(showAll = false) {
                fetch('backend/recent_activity.php', { credentials: 'include' })
                    .then(r => {
                        if (!r.ok) throw new Error('Erreur réseau');
                        return r.json();
                    })
                    .then(data => {
                        const activities = data.activities || [];
                        const activityList = document.getElementById('recentActivityList');
                        
                        if (activityList) {
                            if (activities.length > 0) {
                                // Si showAll est false, afficher seulement les 5 premières activités
                                const activitiesToShow = showAll ? activities : activities.slice(0, 5);
                                
                                activityList.innerHTML = activitiesToShow.map(activity => {
                                    const iconClass = activity.type === 'approved' ? 'success' : 
                                                    activity.type === 'rejected' ? 'danger' : 
                                                    activity.type === 'new' ? 'warning' : 'info';
                                    
                                    const icon = activity.type === 'approved' ? 'fa-check-circle' :
                                               activity.type === 'rejected' ? 'fa-times-circle' :
                                               activity.type === 'new' ? 'fa-exclamation-circle' : 'fa-info-circle';
                                    
                                    const title = activity.type === 'approved' ? 'Demande approuvée' :
                                                activity.type === 'rejected' ? 'Demande rejetée' :
                                                activity.type === 'new' ? 'Nouvelle demande' : 'Activité système';
                                    
                                    return `
                                        <div class="activity-item">
                                            <div class="activity-icon ${iconClass}">
                                                <i class="fas ${icon}"></i>
                                            </div>
                                            <div class="activity-content">
                                                <div class="activity-title">${title}</div>
                                                <div class="activity-desc">${activity.description}</div>
                                                <div class="activity-time">${activity.time_ago}</div>
                                            </div>
                                        </div>
                                    `;
                                }).join('');
                                
                                // Si on affiche toutes les activités, ajouter un bouton pour revenir aux 5 premières
                                if (showAll) {
                                    activityList.innerHTML += `
                                        <div class="activity-item" style="justify-content:center;">
                                            <button class="btn btn-outline btn-sm" id="showLessActivities">
                                                <i class="fas fa-chevron-up"></i> Voir moins
                                            </button>
                                        </div>
                                    `;
                                    
                                    // Ajouter l'écouteur d'événement pour le bouton "Voir moins"
                                    setTimeout(() => {
                                        const showLessBtn = document.getElementById('showLessActivities');
                                        if (showLessBtn) {
                                            showLessBtn.addEventListener('click', function() {
                                                loadRecentActivity(false);
                                            });
                                        }
                                    }, 100);
                                }
                            } else {
                                activityList.innerHTML = `
                                    <div class="activity-item">
                                        <div class="activity-icon info">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title">Aucune activité récente</div>
                                            <div class="activity-desc">Les nouvelles activités apparaîtront ici</div>
                                            <div class="activity-time">Maintenant</div>
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    })
                    .catch((error) => {
                        console.error('Erreur loadRecentActivity:', error);
                        const activityList = document.getElementById('recentActivityList');
                        if (activityList) {
                            activityList.innerHTML = `
                                <div class="activity-item">
                                    <div class="activity-icon danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Erreur de chargement</div>
                                        <div class="activity-desc">Impossible de charger l'activité récente</div>
                                        <div class="activity-time">Maintenant</div>
                                    </div>
                                </div>
                            `;
                        }
                    });
            }

            // Populate requests when navigating to the section
            const reqMenu = document.querySelector('.menu-item[data-target="requests-management"]');
            if (reqMenu) {
                reqMenu.addEventListener('click', function() {
                    fetchRequests(reqStatusFilter.value, reqTypeFilter.value);
                });
            }

            if (reqStatusFilter) {
                reqStatusFilter.addEventListener('change', function() {
                    fetchRequests(this.value, reqTypeFilter.value);
                });
            }

            if (reqTypeFilter) {
                reqTypeFilter.addEventListener('change', function() {
                    fetchRequests(reqStatusFilter.value, this.value);
                });
            }

            // Gestion des actions sur les demandes - CORRIGÉE POUR L'UPLOAD PDF
            if (reqTableBody) {
                reqTableBody.addEventListener('click', function(e) {
                    const btn = e.target.closest('button[data-action]');
                    if (!btn) return;
                    
                    const id = parseInt(btn.getAttribute('data-id'));
                    const action = btn.getAttribute('data-action');
                    if (!id || !action) return;
                    
                    if (action === 'view_details') {
                        // Afficher les détails de la demande dans le modal
                        fetch(`backend/request_admin.php?id=${id}`, { credentials: 'include' })
                            .then(r => {
                                if (!r.ok) throw new Error('Erreur réseau');
                                return r.json();
                            })
                            .then(data => {
                                if (data.request) {
                                    const req = data.request;
                                    requestModalBody.innerHTML = `
                                        <div class="request-details">
                                            <div class="detail-group">
                                                <label>Employé:</label>
                                                <span>${req.firstname} ${req.lastname} (${req.email})</span>
                                            </div>
                                            <div class="detail-group">
                                                <label>Type:</label>
                                                <span class="status-badge type-${req.type}">${req.type}</span>
                                            </div>
                                            <div class="detail-group">
                                                <label>Titre:</label>
                                                <span>${req.title}</span>
                                            </div>
                                            <div class="detail-group">
                                                <label>Description:</label>
                                                <span>${req.description || 'Aucune description'}</span>
                                            </div>
                                            ${req.start_date ? `
                                                <div class="detail-group">
                                                    <label>Période:</label>
                                                    <span>${req.start_date} ${req.end_date ? ' → ' + req.end_date : ''}</span>
                                                </div>
                                            ` : ''}
                                            <div class="detail-group">
                                                <label>Statut:</label>
                                                <span class="status-badge ${req.status === 'pending' ? 'status-pending' : (req.status === 'approved' ? 'status-approved' : 'status-rejected')}">${req.status}</span>
                                            </div>
                                            <div class="detail-group">
                                                <label>Date de création:</label>
                                                <span>${new Date(req.created_at).toLocaleString('fr-FR')}</span>
                                            </div>
                                            ${req.file_path ? `
                                                <div class="detail-group">
                                                    <label>Document:</label>
                                                    <a href="${req.file_path}" target="_blank" class="btn btn-success btn-sm">
                                                        <i class="fas fa-file-pdf"></i> Voir le document
                                                    </a>
                                                </div>
                                            ` : ''}
                                        </div>
                                    `;
                                    requestModal.style.display = 'block';
                                }
                            })
                            .catch((error) => {
                                console.error('Erreur view_details:', error);
                                alert('Erreur lors du chargement des détails: ' + error.message);
                            });
                        return;
                    }
                    
                    if (action === 'view_document') {
                        // Voir le document existant
                        fetch(`backend/request_admin.php?id=${id}`, { credentials: 'include' })
                            .then(r => {
                                if (!r.ok) throw new Error('Erreur réseau');
                                return r.json();
                            })
                            .then(data => {
                                if (data.request && data.request.file_path) {
                                    window.open(data.request.file_path, '_blank');
                                } else {
                                    alert('Aucun document disponible pour cette demande.');
                                }
                            })
                            .catch((error) => {
                                console.error('Erreur view_document:', error);
                                alert('Erreur lors du chargement du document: ' + error.message);
                            });
                        return;
                    }
                    
                    if (['approve', 'reject'].includes(action)) {
                        if (!confirm(`Êtes-vous sûr de vouloir ${action === 'approve' ? 'approuver' : 'rejetter'} cette demande ?`)) {
                            return;
                        }
                        
                        const fd = new FormData();
                        fd.append('id', String(id));
                        fd.append('action', action);
                        
                        fetch('backend/request_admin.php', { 
                            method: 'POST', 
                            body: fd, 
                            credentials: 'include' 
                        })
                            .then(r => {
                                if (!r.ok) throw new Error('Erreur serveur: ' + r.status);
                                return r.json();
                            })
                            .then(resp => {
                                if (resp && resp.success) {
                                    alert(`Demande ${action === 'approve' ? 'approuvée' : 'rejetée'} avec succès !`);
                                    fetchRequests(reqStatusFilter.value, reqTypeFilter.value);
                                    loadDashboardStats(); // Mettre à jour les stats
                                    loadDashboardRealtimeData(); // Mettre à jour le temps réel
                                } else {
                                    alert(resp.error || 'Erreur lors du traitement de la demande');
                                }
                            })
                            .catch((error) => {
                                console.error('Erreur approve/reject:', error);
                                alert('Erreur réseau: ' + error.message);
                            });
                    }
                });

                // Gestion de l'upload de documents PDF
                reqTableBody.addEventListener('change', function(e) {
                    const input = e.target.closest('input[type="file"][data-action="upload_document"]');
                    if (!input || !input.files || input.files.length === 0) return;
                    
                    const id = parseInt(input.getAttribute('data-id'));
                    const file = input.files[0];
                    
                    // Validation du fichier
                    if (file.type !== 'application/pdf') {
                        alert('❌ Seuls les fichiers PDF sont autorisés. Type détecté: ' + file.type);
                        input.value = '';
                        return;
                    }
                    
                    if (file.size > 10 * 1024 * 1024) {
                        alert('❌ Le fichier est trop volumineux. Maximum 10MB autorisé. Taille: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB');
                        input.value = '';
                        return;
                    }
                    
                    const formData = new FormData();
                    formData.append('document', file);
                    formData.append('request_id', id);
                    
                    // Afficher un indicateur de chargement
                    const originalContent = input.parentElement.innerHTML;
                    const originalDisabled = input.disabled;
                    input.parentElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Upload en cours...';
                    input.disabled = true;
                    
                    fetch('backend/upload_document.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'include',
                        signal: AbortSignal.timeout(60000)
                    })
                    .then(async (response) => {
                        if (!response.ok) {
                            const errorText = await response.text();
                            throw new Error(`Erreur HTTP ${response.status}: ${errorText}`);
                        }
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            alert('✅ PDF téléversé avec succès ! L\'employé a été notifié.');
                            fetchRequests(reqStatusFilter.value, reqTypeFilter.value);
                            loadDashboardRealtimeData(); // Mettre à jour le temps réel
                        } else {
                            let errorMessage = '❌ Erreur: ' + (data.message || 'Erreur inconnue lors du téléversement');
                            if (data.debug) {
                                errorMessage += '\nDétails: ' + JSON.stringify(data.debug);
                            }
                            alert(errorMessage);
                        }
                    })
                    .catch((error) => {
                        let errorMessage = '❌ Erreur lors du téléversement: ';
                        
                        if (error.name === 'AbortError') {
                            errorMessage = '❌ Upload annulé (timeout). Le fichier est peut-être trop volumineux.';
                        } else if (error.name === 'TypeError') {
                            errorMessage = '❌ Erreur réseau: Impossible de se connecter au serveur. Vérifiez votre connexion internet.';
                        } else if (error.message.includes('HTTP error')) {
                            errorMessage = '❌ Erreur serveur: ' + error.message;
                        } else {
                            errorMessage += error.message;
                        }
                        
                        alert(errorMessage);
                    })
                    .finally(() => {
                        input.parentElement.innerHTML = originalContent;
                        input.value = '';
                        input.disabled = originalDisabled;
                    });
                });
            }

            // Search employees
            if (employeeSearchInput) {
                let searchTimeout;
                employeeSearchInput.addEventListener('input', function() {
                    const val = this.value.trim();
                    if (val.length === 1) {
                        currentAlpha = val.toUpperCase();
                        if (alphaBar) {
                            alphaBar.querySelectorAll('button').forEach(b => b.classList.remove('active'));
                            const btn = alphaBar.querySelector(`button[data-alpha="${currentAlpha}"]`);
                            if (btn) btn.classList.add('active');
                        }
                        fetchEmployees('', currentAlpha);
                        return;
                    }
                    if (alphaBar && currentAlpha) {
                        alphaBar.querySelectorAll('button').forEach(b => b.classList.remove('active'));
                        const allBtn = alphaBar.querySelector('button[data-alpha=""]');
                        if (allBtn) allBtn.classList.add('active');
                        currentAlpha = '';
                    }
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => fetchEmployees(val, ''), 200);
                });
            }

            // Toggle inline add form
            if (addEmployeeBtn && inlineAddCard) {
                addEmployeeBtn.addEventListener('click', () => {
                    inlineAddCard.style.display = inlineAddCard.style.display === 'none' ? 'block' : 'none';
                });
            }

            // Submit add employee
            if (addEmployeeForm) {
                addEmployeeForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(addEmployeeForm);
                    formData.append('action', 'add');
                    
                    fetch('backend/users.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'include'
                    })
                    .then(r => {
                        if (!r.ok) throw new Error('Erreur réseau');
                        return r.json();
                    })
                    .then(resp => {
                        if (resp && resp.success) {
                            alert('✅ Employé ajouté avec succès !');
                            addEmployeeForm.reset();
                            inlineAddCard.style.display = 'none';
                            fetchEmployees(employeeSearchInput ? employeeSearchInput.value.trim() : '', currentAlpha);
                        } else {
                            alert(resp.error || 'Erreur lors de l\'ajout de l\'employé');
                        }
                    })
                    .catch((error) => {
                        console.error('Erreur add employee:', error);
                        alert('Erreur réseau lors de l\'ajout: ' + error.message);
                    });
                });
            }

            // Employee actions (view, edit, delete)
            if (employeeTableBody) {
                employeeTableBody.addEventListener('click', function(e) {
                    const btn = e.target.closest('button[data-action]');
                    if (!btn) return;
                    const id = btn.getAttribute('data-id');
                    const action = btn.getAttribute('data-action');
                    if (!id) return;
                    
                    if (action === 'delete') {
                        if (!confirm('Êtes-vous sûr de vouloir supprimer cet employé ? Cette action est irréversible.')) return;
                        
                        const formData = new FormData();
                        formData.append('action', 'delete');
                        formData.append('id', id);
                        
                        fetch('backend/users.php', {
                            method: 'POST',
                            body: formData,
                            credentials: 'include'
                        })
                        .then(r => {
                            if (!r.ok) throw new Error('Erreur réseau');
                            return r.json();
                        })
                        .then(resp => {
                            if (resp && resp.success) {
                                alert('✅ Employé supprimé avec succès !');
                                fetchEmployees(employeeSearchInput ? employeeSearchInput.value.trim() : '', currentAlpha);
                            } else {
                                alert(resp.error || 'Erreur lors de la suppression');
                            }
                        })
                        .catch((error) => {
                            console.error('Erreur delete employee:', error);
                            alert('Erreur réseau lors de la suppression: ' + error.message);
                        });
                    } else if (action === 'edit') {
                        alert('Fonctionnalité d\'édition à implémenter');
                    } else if (action === 'view') {
                        alert('Fonctionnalité de visualisation de profil à implémenter');
                    }
                });
            }

            // Alphabet bar filtering
            if (alphaBar) {
                alphaBar.addEventListener('click', function(e) {
                    const btn = e.target.closest('button[data-alpha]');
                    if (!btn) return;
                    currentAlpha = btn.getAttribute('data-alpha') || '';
                    alphaBar.querySelectorAll('button').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    fetchEmployees(employeeSearchInput ? employeeSearchInput.value.trim() : '', currentAlpha);
                });
            }

            // Load dashboard statistics
            function loadDashboardStats() {
                // Load employees count
                fetch('backend/users.php', { credentials: 'include' })
                    .then(r => {
                        if (!r.ok) throw new Error('Erreur réseau');
                        return r.json();
                    })
                    .then(data => {
                        const employees = data.users || [];
                        const activeEmployees = employees.filter(u => u.is_active);
                        document.getElementById('totalEmployees').textContent = activeEmployees.length;
                    })
                    .catch((error) => {
                        console.error('Erreur loadDashboardStats employees:', error);
                    });

                // Load requests statistics
                fetch('backend/request_admin.php', { credentials: 'include' })
                    .then(r => {
                        if (!r.ok) throw new Error('Erreur réseau');
                        return r.json();
                    })
                    .then(data => {
                        const requests = data.requests || [];
                        const pending = requests.filter(r => r.status === 'pending').length;
                        const approved = requests.filter(r => r.status === 'approved').length;
                        const rejected = requests.filter(r => r.status === 'rejected').length;
                        
                        document.getElementById('pendingRequests').textContent = pending;
                        document.getElementById('approvedRequests').textContent = approved;
                        document.getElementById('rejectedRequests').textContent = rejected;
                    })
                    .catch((error) => {
                        console.error('Erreur loadDashboardStats requests:', error);
                    });
            }

            // Load dashboard data on page load
            loadDashboardStats();
            loadDashboardRealtimeData(); // Charger les données temps réel
            
            // Mettre à jour les données temps réel toutes les 10 secondes
            setInterval(() => {
                if (document.getElementById('dashboard').style.display !== 'none') {
                    loadDashboardRealtimeData();
                    loadDashboardStats();
                }
            }, 10000);

            // Reload data when switching to dashboard
            const dashboardMenu = document.querySelector('.menu-item[data-target="dashboard"]');
            if (dashboardMenu) {
                dashboardMenu.addEventListener('click', function() {
                    setTimeout(() => {
                        loadDashboardStats();
                        loadDashboardRealtimeData();
                    }, 100);
                });
            }
        });
    </script>

    <style>
        .text-center { text-align: center; }
        .employee-name { font-weight: 600; }
        .employee-email { font-size: 12px; color: #666; }
        .action-buttons { display: flex; gap: 4px; flex-wrap: wrap; }
        .status-badge.role-employee { background: #e8f0fe; color: #1a73e8; }
        .status-badge.role-manager { background: #fff0e6; color: #ff6b35; }
        .status-badge.type-leave { background: #e6f7ff; color: #1890ff; }
        .status-badge.type-document { background: #f6ffed; color: #52c41a; }
        .status-badge.type-other { background: #f9f0ff; color: #722ed1; }
        .status-active { background: #f6ffed; color: #52c41a; }
        .status-inactive { background: #fff2e8; color: #fa541c; }
        .status-pending { background: #fff7e6; color: #fa8c16; }
        .status-approved { background: #f6ffed; color: #52c41a; }
        .status-rejected { background: #fff2f0; color: #ff4d4f; }
        .request-details { margin-top: 15px; }
        .detail-group { display: flex; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
        .detail-group label { font-weight: 600; min-width: 120px; color: #333; }
        .detail-group span { flex: 1; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 5% auto; padding: 0; border-radius: 8px; width: 90%; max-width: 600px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .modal-header { display: flex; justify-content: between; align-items: center; padding: 20px; border-bottom: 1px solid #e8e8e8; }
        .modal-header h3 { margin: 0; color: #333; }
        .close-modal { font-size: 24px; cursor: pointer; color: #999; }
        .close-modal:hover { color: #333; }
        .modal-body { padding: 20px; max-height: 70vh; overflow-y: auto; }
        .employees-stats, .requests-stats { font-size: 14px; color: #666; }
        .form-input { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .form-input:focus { outline: none; border-color: #1890ff; box-shadow: 0 0 0 2px rgba(24, 144, 255, 0.2); }
        .view-all { 
            font-size: 14px; 
            color: #1890ff; 
            text-decoration: none; 
            font-weight: 500;
            transition: color 0.2s;
        }
        .view-all:hover { 
            color: #096dd9; 
            text-decoration: underline;
        }
    </style>
</body>
</html>