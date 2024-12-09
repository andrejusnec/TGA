import {showToast} from "./toast";

$(document).ready(function () {
    const successfulDelete = localStorage.getItem('successfulDelete');
    if (successfulDelete) {
        showToast(successfulDelete, 'success');
        localStorage.removeItem('successfulDelete');
    }
});

$(document).on('click', '.delete-student', function () {
    const studentId = $(this).data('student-id');

    if (!confirm('Are you sure you want to delete this student?')) {
        return;
    }

    $.ajax({
        url: `/api/students/${studentId}`,
        type: 'DELETE',
        success: function (data) {
            localStorage.setItem('successfulDelete', 'Student deleted successfully!');
            location.reload();
        },
        error: function (xhr, data) {
            console.log(xhr.responseJSON?.error);

            const errorMessage = xhr.responseJSON?.error || 'An error occurred.';
            showToast(errorMessage, 'danger');
        },
    });
});

