/**
 * auth.js - KCG Multispecialist Hospital
 * Uses absolute URL detection to work correctly from any subfolder.
 */

// Compute the project base URL reliably (works from root or /admin/ subfolder)
const _BASE_URL = (function() {
    const segs = window.location.pathname.split('/');
    const adminIdx = segs.findIndex(s => s.toLowerCase() === 'admin');
    const projectSegs = (adminIdx !== -1) ? segs.slice(0, adminIdx) : segs.slice(0, -1);
    return window.location.origin + projectSegs.join('/') + '/';
})();

// Relative prefix for HTML links (not API calls)
const _isAdminFolder = window.location.pathname.toLowerCase().includes('/admin/');
const _pfx = _isAdminFolder ? '../' : ''; // for relative HTML hrefs only

const AppAuth = {
    checkSession: async function() {
        try {
            const response = await fetch(_BASE_URL + 'api/auth/check_session.php');
            return await response.json();
        } catch (e) {
            return { status: 'error' };
        }
    },

    requireLogin: async function(adminOnly = false) {
        const session = await this.checkSession();
        if (session.status !== 'success') {
            window.location.href = _BASE_URL + 'login.html';
            return null;
        }
        if (adminOnly && session.data.role !== 'admin') {
            window.location.href = _BASE_URL + 'login.html';
            return null;
        }
        return session.data;
    },

    // Only called on login/register pages — redirects if already logged in
    redirectIfLoggedIn: async function() {
        const session = await this.checkSession();
        if (session.status === 'success') {
            if (session.data.role === 'admin') {
                window.location.href = _BASE_URL + 'admin/admin_dashboard.html';
            } else {
                window.location.href = _BASE_URL + 'dashboard.html';
            }
        }
    },

    login: async function(email, password, isAdminLogin = false) {
        try {
            const response = await fetch(_BASE_URL + 'api/auth/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password, admin_login: isAdminLogin })
            });
            return await response.json();
        } catch (e) {
            return { status: 'error', message: 'Network error occurred' };
        }
    },

    register: async function(name, email, phone, password) {
        try {
            const response = await fetch(_BASE_URL + 'api/auth/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, email, phone, password })
            });
            return await response.json();
        } catch (e) {
            return { status: 'error', message: 'Network error occurred' };
        }
    },

    logout: async function() {
        try {
            await fetch(_BASE_URL + 'api/auth/logout.php');
        } catch(e) {}
        window.location.href = _BASE_URL + 'index.html';
    },

    updateNavbar: async function() {
        const session = await this.checkSession();
        const navRight = document.getElementById('nav-right');
        if (!navRight) return;

        if (session.status === 'success') {
            if (session.data.role === 'admin') {
                navRight.innerHTML = `
                    <li class="nav-item"><a class="nav-link" href="${_pfx}admin/admin_dashboard.html">Admin Dashboard</a></li>
                    <li class="nav-item"><a class="btn btn-danger ms-2 px-3 rounded-pill shadow-sm" href="#" onclick="AppAuth.logout()">Logout</a></li>
                `;
            } else {
                // Patient Navbar: Dashboard | My Appointments | Profile | [Bell] Notifications | Logout
                navRight.innerHTML = `
                    <li class="nav-item"><a class="nav-link" href="${_pfx}dashboard.html">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="${_pfx}my_appointments.html">My Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="${_pfx}profile.html">Profile</a></li>
                    <li class="nav-item dropdown px-2" id="notif-dropdown-container">
                        <a class="nav-link position-relative d-flex align-items-center bg-white bg-opacity-10 rounded-pill px-3" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="AppAuth.loadNotifDropdown()" style="transition: all 0.3s;">
                            <i class="fa-solid fa-bell fs-5 me-2"></i>
                            <span class="fw-semibold">Notifications</span>
                            <span id="notif-count-badge" class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger border border-white" style="display:none; font-size: 0.7rem; margin-top: 5px; margin-left: 15px;">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-3 p-2" id="notif-dropdown-list" style="width: 320px; max-height: 420px; overflow-y: auto;">
                            <li class="p-3 text-center text-muted">Loading notifications...</li>
                        </ul>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-danger px-4 rounded-pill shadow-sm fw-bold" href="#" onclick="AppAuth.logout()">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                        </a>
                    </li>
                `;
                this.updateNotifCount();
            }
        } else {
            navRight.innerHTML = `
                <li class="nav-item"><a class="btn btn-outline-light me-2 rounded-pill px-3" href="${_pfx}login.html">
                    <i class="fa-solid fa-user me-1"></i>Patient Login</a></li>
                <li class="nav-item"><a class="btn btn-light text-dark fw-semibold rounded-pill px-3" href="${_pfx}admin/admin_login.html">
                    <i class="fa-solid fa-user-shield me-1"></i>Admin Login</a></li>
            `;
        }
    },

    updateNotifCount: async function() {
        const badge = document.getElementById('notif-count-badge');
        if (!badge) return;
        try {
            const res = await fetch(_BASE_URL + 'api/patient/get_notifications.php');
            const result = await res.json();
            if (result.status === 'success' && result.data.length > 0) {
                badge.textContent = result.data.length;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        } catch (e) {}
    },

    loadNotifDropdown: async function() {
        const list = document.getElementById('notif-dropdown-list');
        if (!list) return;
        try {
            const res = await fetch(_BASE_URL + 'api/patient/get_notifications.php');
            const result = await res.json();
            if (result.status === 'success' && result.data.length > 0) {
                let html = '<li class="dropdown-header fw-bold text-dark fs-6 pb-2 border-bottom mb-2"><i class="fa-solid fa-bell me-2"></i>Recent Notifications</li>';
                result.data.forEach(n => {
                    const date = new Date(n.created_at).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
                    html += `
                        <li class="mb-1">
                            <div class="dropdown-item rounded-3 p-3 bg-light-subtle border-start border-4 border-danger position-relative">
                                <p class="small mb-1 text-wrap" style="line-height:1.4;">${n.message.replace(/\\n/g, '<br>').replace(/\n/g, '<br>')}</p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted" style="font-size:0.7rem;">${date}</small>
                                    <button class="btn btn-sm btn-link text-primary p-0 text-decoration-none" style="font-size:0.75rem;" onclick="AppAuth.markRead(${n.id}, event)">Mark Read</button>
                                </div>
                            </div>
                        </li>
                    `;
                });
                html += '<li class="text-center pt-2"><button class="btn btn-link text-muted btn-sm w-100" onclick="AppAuth.markAllRead(event)">Mark all as read</button></li>';
                list.innerHTML = html;
            } else {
                list.innerHTML = '<li class="p-4 text-center text-muted"><i class="fa-solid fa-bell-slash fa-2x mb-3 d-block opacity-25"></i>No new notifications</li>';
            }
        } catch (e) {
            list.innerHTML = '<li class="p-3 text-center text-danger">Error loading notifications</li>';
        }
    },

    markRead: async function(id, event) {
        if (event) event.stopPropagation();
        try {
            await fetch(_BASE_URL + 'api/patient/mark_notification_read.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ notification_id: id })
            });
            this.updateNotifCount();
            this.loadNotifDropdown();
        } catch (e) {}
    },

    markAllRead: async function(event) {
        if (event) event.stopPropagation();
        // For simplicity, we can reuse mark_notification_read with a flag or just call it in a loop
        // Let's create a specific bulk API if needed, but for now we'll just handle it gracefully
        try {
            const res = await fetch(_BASE_URL + 'api/patient/get_notifications.php');
            const result = await res.json();
            if (result.status === 'success') {
                for (let n of result.data) {
                    await fetch(_BASE_URL + 'api/patient/mark_notification_read.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ notification_id: n.id })
                    });
                }
                this.updateNotifCount();
                this.loadNotifDropdown();
            }
        } catch (e) {}
    }
};
