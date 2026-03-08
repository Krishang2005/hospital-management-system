const App = {
    // Utility: Fetch Specializations
    getSpecializations: async function() {
        try {
            const res = await fetch('api/public/get_specializations.php');
            return await res.json();
        } catch (e) {
            return { status: 'error', data: [] };
        }
    },

    getDoctorInitials: function(name) {
        if (!name) return 'D';
        let cleanName = name.replace(/^Dr\.\s+/i, '');
        let parts = cleanName.trim().split(/\s+/);
        if (parts.length === 0 || parts[0] === "") return 'D';
        let lastInitial = parts[parts.length - 1].charAt(0).toUpperCase();
        return 'D' + lastInitial;
    },

    // Utility: Fetch Doctors with limits & search
    getDoctors: async function(limit = 0, search = '', specialization = '') {
        try {
            let url = `api/public/get_doctors.php?limit=${limit}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (specialization) url += `&specialization=${encodeURIComponent(specialization)}`;
            const res = await fetch(url);
            return await res.json();
        } catch (e) {
            return { status: 'error', data: [] };
        }
    },

    // Populate Top Doctors on Homepage
    renderTopDoctors: async function(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>';
        
        const response = await this.getDoctors(3);
        if (response.status === 'success' && response.data.length > 0) {
            let html = '';
            response.data.forEach(doc => {
                const initials = this.getDoctorInitials(doc.doctor_name);
                const photoHtml = (doc.photo && !doc.photo.includes('default')) 
                    ? `<img src="images/${doc.photo}?v=${Date.now()}" class="card-img-top doctor-photo" alt="Doctor Photo" onerror="this.outerHTML='<div class=\'doctor-avatar\'>${initials}</div>';">`
                    : `<div class="doctor-avatar">${initials}</div>`;

                html += `
                <div class="col-md-4 mb-4">
                    <div class="card doctor-card h-100">
                        ${photoHtml}
                        <div class="card-body text-center d-flex flex-column">
                            <h5 class="card-title fw-bold text-primary mb-1">Dr. ${doc.doctor_name}</h5>
                            <p class="card-text text-muted mb-2">${doc.specialization}</p>
                            <p class="small mb-3 mt-auto"><strong>${doc.experience} Years Expr.</strong> | ${doc.hospital}</p>
                            <a href="doctor_profile.html?id=${doc.id}" class="btn btn-outline-primary w-100 rounded-pill">View Profile</a>
                        </div>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p class="text-center text-muted col-12">No doctors found.</p>';
        }
    },

    // Populate Departments on Homepage dynamically
    renderDepartments: async function(containerId) {
        const container = document.getElementById(containerId);
        if(!container) return;

        const response = await this.getSpecializations();
        if(response.status === 'success' && response.data.length > 0) {
            const deptImages = {
                'Cardiologist': 'cardiology_3d.jpeg', 'Dentist': 'dental_3d.jpeg', 
                'Dermatologist': 'dermatology_3d.jpeg', 'Neurologist': 'neurology_3d.jpeg', 
                'Orthopedic': 'orthopedics_3d.jpeg', 'Pediatrician': 'pediatric_3d.jpeg'
            };
            
            let html = '';
            // Limit to first 6 for homepage
            const specs = response.data.slice(0, 6);
            specs.forEach(spec => {
                const specName = spec.specialization_name;
                const img = deptImages[specName] || 'default_dept.png';
                html += `
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="doctors.html?specialization=${encodeURIComponent(specName)}" class="category-card shadow-sm text-decoration-none">
                        <div class="category-image-wrapper mb-3">
                            <img src="images/${img}" class="category-image" alt="${specName}" onerror="this.src='https://via.placeholder.com/200?text=${specName}';">
                        </div>
                        <h5 class="fw-bold mt-2 mb-0">${specName}</h5>
                    </a>
                </div>`;
            });
            container.innerHTML = html;
        }
    }
};
