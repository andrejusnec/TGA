import { showToast } from './toast';

$(document).ready(function () {
    const $addStudentForm = $('#add-student-form');
    const $modalErrorContainer = $('#modal-error-container');

    $addStudentForm.on('submit', function (event) {
        event.preventDefault();
        $modalErrorContainer.empty();

        const jsonData = getFormData($addStudentForm);

        submitStudentData(jsonData)
            .done(handleSuccess)
            .fail(handleApiErrors);
    });

    function getFormData($form) {
        const formData = $form.serializeArray();
        const jsonData = {};
        $.each(formData, function (_, field) {
            jsonData[field.name] = field.value;
        });
        return jsonData;
    }

    function submitStudentData(data) {
        return $.ajax({
            url: '/api/students',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
        });
    }

    function handleSuccess() {
        $modalErrorContainer.empty();
        $addStudentForm.trigger('reset');
        $('#addStudentModal').modal('hide');

        showToast('Student created successfully!', 'success');
    }

    function handleApiErrors(xhr) {
        if (xhr.status === 400 && xhr.responseJSON?.errors) {
            renderValidationErrors(xhr.responseJSON.errors);
        } else {
            renderGenericError();
        }
    }

    function renderValidationErrors(errors) {
        errors.forEach(error => {
            $modalErrorContainer.append(
                `<div class="alert alert-danger">${error.message}</div>`
            );
        });
    }

    function renderGenericError() {
        $modalErrorContainer.append(
            `<div class="alert alert-danger">An unexpected error occurred. Please try again later.</div>`
        );
    }
});
