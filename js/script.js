document.addEventListener('DOMContentLoaded', () => {
    // Show Toast Function
    window.showToast = function(title, message, type='success') {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            const div = document.createElement('div');
            div.id = 'toast-container';
            div.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(div);
        }

        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';

        const toastHtml = `
            <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong><br>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        const toastEl = document.createElement('div');
        toastEl.innerHTML = toastHtml;
        document.getElementById('toast-container').appendChild(toastEl.firstElementChild);

        const toastInstance = new bootstrap.Toast(document.getElementById('toast-container').lastElementChild);
        toastInstance.show();
    };
});
