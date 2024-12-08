$(document).on('change', '.student-select', function () {
    const dropdown = $(this);
    const studentId = dropdown.val();
    const groupId = dropdown.data('group-id');
    const assignUrl = dropdown.data('assign-url');
    const studentName = dropdown.find('option:selected').text();

    if (!studentId) return;

    toggleDropdownState(dropdown, true);

    $.ajax({
        url: assignUrl,
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        data: { student: studentId, group: groupId },
        success: function (data) {
            if (data.success) {
                handleAssignmentSuccess(studentId, groupId, studentName, dropdown);
                updateStudentList(studentId, groupId, data.groupName);
                checkGroupCapacity(groupId);
            } else {
                showError(data.message);
                resetDropdownSelection(dropdown);
            }
        },
        error: function () {
            showError("An unexpected error occurred. Please try again.");
            resetDropdownSelection(dropdown);
        },
        complete: function () {
            toggleDropdownState(dropdown, false);
        }
    });
});

function checkGroupCapacity(groupId) {
    const groupContainer = $(`#group-${groupId}-students`);
    const dropdownContainer = $(`#group-${groupId}-dropdown-container`);
    const groupFullMessage = $(`#group-${groupId}-full-message`);

    $.get(`/api/groups/${groupId}/students`, function (data) {
        if (data.students >= data.maxStudentsPerGroup) {
            dropdownContainer.hide();
            groupFullMessage.text('This group is full').show();
        } else {
            dropdownContainer.show();
            groupFullMessage.hide();
        }
    });
}

function handleAssignmentSuccess(studentId, groupId, studentName, dropdown) {
    const studentList = $(`#group-${groupId}-students`);
    studentList.find('.text-muted').remove();
    addStudentToGroupList(studentId, groupId, studentName);
    removeStudentFromAllDropdowns(studentId);
    resetDropdownSelection(dropdown);
}

function addStudentToGroupList(studentId, groupId, studentName) {
    const studentList = $(`#group-${groupId}-students`);
    const newStudentItem = $('<li>')
        .addClass('list-group-item d-flex justify-content-between align-items-center')
        .attr('id', `group-${groupId}-student-${studentId}`);
    const studentSpan = $('<span>').text(studentName);
    const removeButton = $('<button>')
        .addClass('btn btn-outline-danger btn-sm remove-student-btn')
        .attr('data-student-id', studentId)
        .attr('data-group-id', groupId)
        .text('×');
    newStudentItem.append(studentSpan, removeButton);
    studentList.append(newStudentItem);
}

function removeStudentFromAllDropdowns(studentId) {
    $(`.student-select option[value="${studentId}"]`).remove();
}

function resetDropdownSelection(dropdown) {
    dropdown.val('');
}

function toggleDropdownState(dropdown, isDisabled) {
    dropdown.prop('disabled', isDisabled);
}

function updateStudentList(studentId, groupId, groupName) {
    const studentRow = $(`#student-row-${studentId}`);
    const groupCell = studentRow.find('.student-group');
    groupCell.text(groupName || '-');
}

function showError(message) {
    const errorContainer = $('#error-container');
    errorContainer.html(`<div class="alert alert-danger">${message}</div>`);
    errorContainer.fadeIn();
    setTimeout(() => errorContainer.fadeOut(), 5000);
}
