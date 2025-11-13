// 🌗 Mode nuit / jour
function setupThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    if (!themeToggle) return;

    const themeIcon = themeToggle.querySelector('i');
    const root = document.documentElement;

    const savedTheme = localStorage.getItem('darkMode');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    // Appliquer le thème au chargement
    if (savedTheme === 'enabled' || (!savedTheme && prefersDark)) {
        root.classList.add('dark-mode');
        if (themeIcon) themeIcon.classList.replace('fa-moon', 'fa-sun');
    }

    // Toggle mode sombre
    themeToggle.addEventListener('click', () => {
        root.classList.toggle('dark-mode');
        const darkModeEnabled = root.classList.contains('dark-mode');
        localStorage.setItem('darkMode', darkModeEnabled ? 'enabled' : 'disabled');

        if (themeIcon) {
            themeIcon.classList.toggle('fa-sun', darkModeEnabled);
            themeIcon.classList.toggle('fa-moon', !darkModeEnabled);
        }
    });
}

// 🧭 Navigation sans rechargement
function setupNavigation() {
    const contentSections = document.querySelectorAll('.content-section');
    const navLinks = document.querySelectorAll('.nav-links a[data-section]');
    if (!contentSections.length || !navLinks.length) return;

    function showSection(sectionId) {
        contentSections.forEach(sec => sec.classList.remove('active'));
        const section = document.getElementById(sectionId);
        if (section) section.classList.add('active');

        navLinks.forEach(link => {
            link.parentElement.classList.toggle('active', link.dataset.section === sectionId);
        });

        history.pushState(null, null, `#${sectionId}`);
    }

    navLinks.forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            showSection(link.dataset.section);
        });
    });

    const hash = window.location.hash.substring(1);
    if (hash && document.getElementById(hash)) showSection(hash);
    else showSection(contentSections[0].id);

    window.addEventListener('popstate', () => {
        const hash = window.location.hash.substring(1);
        if (hash && document.getElementById(hash)) showSection(hash);
    });
}

// 🔖 Tabs
function setupTabs() {
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabId = tab.dataset.tab;
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            tabContents.forEach(c => c.classList.remove('active'));
            const content = document.getElementById(tabId);
            if (content) content.classList.add('active');
        });
    });
}

// 🌊 Smooth scrolling
function setupSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    });
}

// 📝 Profil form
function setupProfileForm() {
    const profilForm = document.getElementById('profilForm');
    if (!profilForm) return;

    profilForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('backend/update_profil.php', { method: 'POST', body: formData, credentials: 'include' })
            .then(r => r.json())
            .then(resp => {
                document.getElementById('profilMsg').textContent = resp.success ? 'Profil mis à jour !' : (resp.error || 'Erreur');
            });
    });
}

// 📜 Historique utilisateur
function loadUserHistory() {
    fetch('backend/history_user.php', { credentials: 'include' })
        .then(r => r.json())
        .then(data => {
            const history = data.history || [];
            const table = document.getElementById('historyTable');
            if (table) {
                table.innerHTML = history.length ? history.map(req => {
                    const statusClass = req.status === 'pending' ? 'status-pending' :
                                        req.status === 'approved' ? 'status-approved' : 'status-rejected';
                    const statusText = req.status === 'pending' ? 'En attente' :
                                       req.status === 'approved' ? 'Approuvée' : 'Rejetée';
                    const details = req.start_date && req.end_date ? `Du ${req.start_date} au ${req.end_date}` : req.description || req.title;
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
        });
}
setInterval(loadUserHistory, 10000);

// 🔐 Validation mot de passe
function setupPasswordValidation() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm-password');
    if (!passwordInput) return;

    function toggleIcon(id, valid) {
        const el = document.getElementById(id);
        if (!el) return;
        const check = el.querySelector('.fa-check-circle');
        const times = el.querySelector('.fa-times-circle');
        if (valid) {
            check.style.display = 'inline';
            times.style.display = 'none';
            el.style.color = 'var(--success)';
        } else {
            check.style.display = 'none';
            times.style.display = 'inline';
            el.style.color = 'var(--dark)';
        }
    }

    function validate() {
        const pwd = passwordInput.value;
        toggleIcon('req-length', pwd.length >= 8);
        toggleIcon('req-uppercase', /[A-Z]/.test(pwd));
        toggleIcon('req-number', /[0-9]/.test(pwd));
        toggleIcon('req-special', /[!@#$%^&*(),.?":{}|<>]/.test(pwd));
        if (confirmInput) validateConfirm();
    }

    function validateConfirm() {
        const pwd = passwordInput.value;
        const conf = confirmInput.value;
        if (conf.length > 0) confirmInput.style.borderColor = (pwd === conf) ? 'var(--success)' : 'var(--danger)';
        else confirmInput.style.borderColor = 'var(--gray)';
    }

    passwordInput.addEventListener('input', validate);
    if (confirmInput) confirmInput.addEventListener('input', validateConfirm);
}

// 💻 Initialisation
document.addEventListener('DOMContentLoaded', function() {
    setupThemeToggle();
    setupNavigation();
    setupTabs();
    setupSmoothScrolling();
    setupProfileForm();
    setupPasswordValidation();
    loadUserHistory();
});
