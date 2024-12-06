// $(document).on('click', '.remove-student-btn', handleStudentRemoval);
//
// function handleStudentRemoval() {
//     const button = $(this);
//     const studentId = button.data('student-id');
//     const groupId = button.data('group-id');
//     const studentList = $('#group-' + groupId + '-students');
//     const removeUrl = studentList.data('remove-url');
//
//     removeStudent(removeUrl, studentId, groupId, studentList, button);
// }
//
// function removeStudent(removeUrl, studentId, groupId, studentList, button) {
//     $.ajax({
//         url: removeUrl,
//         method: 'POST',
//         headers: {
//             'X-Requested-With': 'XMLHttpRequest',
//         },
//         data: {
//             student: studentId,
//         },
//         success: function (data) {
//             if (data.success) {
//                 handleRemovalSuccess(studentId, groupId, studentList, button, data.studentName);
//             } else {
//                 alert(data.message);
//             }
//         },
//         error: function (error) {
//             console.error("There was an error removing the student:", error);
//             alert("An error occurred. Please try again.");
//         }
//     });
// }
//
// function handleRemovalSuccess(studentId, groupId, studentList, button, studentName) {
//     button.closest('li').remove();
//
//     addStudentBackToDropdowns(studentId, studentName);
//
//     if (studentList.children('li').length === 0) {
//         studentList.append('<li class="list-group-item text-muted">No students assigned yet.</li>');
//     }
// }
//
// function addStudentBackToDropdowns(studentId, studentName) {
//     $('.student-select').each(function () {
//         if ($(this).find('option[value="' + studentId + '"]').length === 0) {
//             $(this).append(`<option value="${studentId}">${studentName}</option>`);
//         }
//     });
// }

$(document).on('click', '.remove-student-btn', handleStudentRemoval);

function handleStudentRemoval() {
    const button = $(this);
    const studentId = button.data('student-id');
    const groupId = button.data('group-id');
    const studentList = $('#group-' + groupId + '-students');
    const removeUrl = studentList.data('remove-url');

    removeStudent(removeUrl, studentId, groupId, studentList, button);
}

function removeStudent(removeUrl, studentId, groupId, studentList, button) {
    $.ajax({
        url: removeUrl,
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        data: {
            student: studentId,
        },
        success: function (data) {
            if (data.success) {
                handleRemovalSuccess(studentId, groupId, studentList, button, data.studentName);
                updateStudentListAfterRemoval(studentId); // Update the student list
            } else {
                alert(data.message);
            }
        },
        error: function (error) {
            console.error("There was an error removing the student:", error);
            alert("An error occurred. Please try again.");
        }
    });
}

function handleRemovalSuccess(studentId, groupId, studentList, button, studentName) {
    button.closest('li').remove();

    addStudentBackToDropdowns(studentId, studentName);

    if (studentList.children('li').length === 0) {
        studentList.append('<li class="list-group-item text-muted">No students assigned yet.</li>');
    }
}

function addStudentBackToDropdowns(studentId, studentName) {
    $('.student-select').each(function () {
        if ($(this).find('option[value="' + studentId + '"]').length === 0) {
            $(this).append(`<option value="${studentId}">${studentName}</option>`);
        }
    });
}

// Update the student list dynamically after removal
function updateStudentListAfterRemoval(studentId) {
    const studentRow = $(`#student-row-${studentId}`);
    studentRow.find('.student-group').text('No group assigned');
}
