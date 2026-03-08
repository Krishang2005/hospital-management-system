const AdminApp = {
    renderAdminNavbar: function() {
        document.getElementById('admin-nav').innerHTML = `
        <div class="container">
            <a class="navbar-brand fw-bold" href="admin_dashboard.html">
                <i class="fa-solid fa-user-shield me-2"></i>KCG Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.html"><i class="fa-solid fa-gauge me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_appointments.html"><i class="fa-solid fa-calendar-check me-1"></i>Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_doctors.html"><i class="fa-solid fa-user-doctor me-1"></i>Doctors</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_patients.html"><i class="fa-solid fa-users me-1"></i>Patients</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="btn btn-sm btn-outline-light rounded-pill me-2" href="../index.html" target="_blank">
                            <i class="fa-solid fa-eye me-1"></i>View Site
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-sm btn-danger rounded-pill" href="#" onclick="AppAuth.logout()">
                            <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>`;
    },

    getDashboardStats: async function() {
        try {
            const res = await fetch(_BASE_URL + 'api/admin/get_stats.php');
            return await res.json();
        } catch (e) {
            return { status: 'error' };
        }
    },

    initChart: function(labels, data) {
        const ctx = document.getElementById('appointmentsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Appointments',
                    data: data,
                    backgroundColor: 'rgba(99, 102, 241, 0.6)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }
};
