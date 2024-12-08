export function showToast(message, type = 'success') {
    const alertContainer = $('#inline-alert-container');
    alertContainer.empty();

    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    alertContainer.append(alertHtml);

    setTimeout(() => {
        alertContainer.find('.alert').alert('close');
    }, 5000);
}