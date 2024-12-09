$(document).on('click', '.remove-student-btn', handleStudentRemoval);

function handleStudentRemoval() {
    const { studentId, groupId } = $(this).data();
    const studentList = $(`#group-${groupId}-students`);
    const removeUrl = studentList.data('remove-url');

    removeStudent(removeUrl, studentId, groupId, studentList, $(this));
}

function removeStudent(removeUrl, studentId, groupId, studentList, button) {
    toggleLoadingState(button, true);
    $.ajax({
        url: removeUrl,
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        data: { student: studentId },
        success: function (data) {
            handleRemovalSuccess(studentId, groupId, studentList, button, data);
        },
        error: function (error) {
            console.error("Error:", error);
            showError("An error occurred. Please try again.");
        },
        complete: function () {
            toggleLoadingState(button, false);
        }
    });
}

function handleRemovalSuccess(studentId, groupId, studentList, button, data) {
    button.closest('li').remove();

    addStudentBackToDropdowns(studentId, data.studentName);

    if (studentList.children('li').length === 0) {
        studentList.append(createNoStudentsMessage());
    }

    updateGroupCapacity(groupId, data.currentStudentCount, data.maxStudentsPerGroup);

    location.reload();
}

function updateGroupCapacity(groupId, currentStudentCount, maxStudentsPerGroup) {

    const dropdownContainer = $(`#group-${groupId}-dropdown-container`);
    const groupFullMessage = $(`#group-${groupId}-full-message`);

    if (currentStudentCount < maxStudentsPerGroup) {
        dropdownContainer.show();
        groupFullMessage.hide();
    } else {
        console.log("Hiding dropdown, showing 'Group is full' message.");
        dropdownContainer.hide();
        groupFullMessage.text('This group is full').show();
    }
}


function createNoStudentsMessage() {
    return $('<li>')
        .addClass('list-group-item text-muted')
        .text('No students assigned yet.');
}

function addStudentBackToDropdowns(studentId, studentName) {
    $('.student-select').each(function () {
        if (!$(this).find(`option[value="${studentId}"]`).length) {
            $(this).append(new Option(studentName, studentId));
        }
    });
}

function toggleLoadingState(button, isLoading) {
    if (isLoading) {
        button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
    } else {
        button.prop('disabled', false).text('×');
    }
}

function showError(message) {
    const errorContainer = $('#error-container');
    errorContainer.html(`<div class="alert alert-danger">${message}</div>`);
    errorContainer.fadeIn();
    setTimeout(() => errorContainer.fadeOut(), 5000);
}

