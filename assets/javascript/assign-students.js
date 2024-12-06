// $(document).on('submit', '.assign-student-form', handleStudentAssignment);
//
// function handleStudentAssignment(e) {
//     e.preventDefault();
//
//     const form = $(e.currentTarget);
//     const groupId = form.data('group-id');
//     const assignUrl = form.data('assign-url');
//     const studentSelect = form.find('.student-select');
//     const studentId = studentSelect.val();
//     const studentName = studentSelect.find('option:selected').text();
//
//     if (!validateStudentSelection(studentId)) {
//         return;
//     }
//
//     assignStudent(assignUrl, studentId, groupId, studentName);
// }
//
// function validateStudentSelection(studentId) {
//     if (!studentId) {
//         alert("Please select a student.");
//         return false;
//     }
//     return true;
// }
//
// function assignStudent(assignUrl, studentId, groupId, studentName) {
//     $.ajax({
//         url: assignUrl,
//         method: 'POST',
//         headers: {
//             'X-Requested-With': 'XMLHttpRequest',
//         },
//         data: {
//             student: studentId,
//         },
//         success: function (data) {
//             if (data.success) {
//                 handleAssignmentSuccess(studentId, groupId, studentName);
//             } else {
//                 alert(data.message);
//             }
//         },
//         error: function (error) {
//             console.error("There was an error assigning the student:", error);
//             alert("An error occurred. Please try again.");
//         }
//     });
// }
//
// function handleAssignmentSuccess(studentId, groupId, studentName) {
//     removeStudentFromAllSelects(studentId);
//
//     const studentList = $('#group-' + groupId + '-students');
//     removeNoStudentsMessage(studentList);
//     addStudentToList(studentList, studentId, groupId, studentName);
// }
//
// function removeNoStudentsMessage(studentList) {
//     studentList.find('li.text-muted').remove();
// }
//
// function removeStudentFromAllSelects(studentId) {
//     $('.student-select option[value="' + studentId + '"]').remove();
// }
//
// function addStudentToList(studentList, studentId, groupId, studentName) {
//     const newStudentItem = $('<li>')
//         .addClass('list-group-item d-flex justify-content-between align-items-center')
//         .attr('id', `group-${groupId}-student-${studentId}`);
//
//     const studentSpan = $('<span>').text(studentName);
//     const removeButton = $('<button>')
//         .addClass('btn btn-outline-danger btn-sm remove-student-btn')
//         .text('×')
//         .data('student-id', studentId)
//         .data('group-id', groupId);
//
//     newStudentItem.append(studentSpan, removeButton);
//     studentList.append(newStudentItem);
// }
$(document).on('submit', '.assign-student-form', handleStudentAssignment);

function handleStudentAssignment(e) {
    e.preventDefault();

    const form = $(e.currentTarget);
    const groupId = form.data('group-id');
    const assignUrl = form.data('assign-url');
    const studentSelect = form.find('.student-select');
    const studentId = studentSelect.val();
    const studentName = studentSelect.find('option:selected').text();

    if (!validateStudentSelection(studentId)) {
        return;
    }

    assignStudent(assignUrl, studentId, groupId, studentName);
}

function validateStudentSelection(studentId) {
    if (!studentId) {
        alert("Please select a student.");
        return false;
    }
    return true;
}

function assignStudent(assignUrl, studentId, groupId, studentName) {
    $.ajax({
        url: assignUrl,
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        data: {
            student: studentId,
        },
        success: function (data) {
            if (data.success) {
                handleAssignmentSuccess(studentId, groupId, studentName);
                updateStudentList(studentId, groupId);
            } else {
                alert(data.message);
            }
        },
        error: function (error) {
            console.error("There was an error assigning the student:", error);
            alert("An error occurred. Please try again.");
        }
    });
}

function handleAssignmentSuccess(studentId, groupId, studentName) {
    removeStudentFromAllSelects(studentId);

    const studentList = $('#group-' + groupId + '-students');
    removeNoStudentsMessage(studentList);
    addStudentToList(studentList, studentId, groupId, studentName);
}

function removeNoStudentsMessage(studentList) {
    studentList.find('li.text-muted').remove();
}

function removeStudentFromAllSelects(studentId) {
    $('.student-select option[value="' + studentId + '"]').remove();
}

function addStudentToList(studentList, studentId, groupId, studentName) {
    const newStudentItem = $('<li>')
        .addClass('list-group-item d-flex justify-content-between align-items-center')
        .attr('id', `group-${groupId}-student-${studentId}`);

    const studentSpan = $('<span>').text(studentName);
    const removeButton = $('<button>')
        .addClass('btn btn-outline-danger btn-sm remove-student-btn')
        .text('×')
        .data('student-id', studentId)
        .data('group-id', groupId);

    newStudentItem.append(studentSpan, removeButton);
    studentList.append(newStudentItem);
}

// Update the student list dynamically
function updateStudentList(studentId, groupId) {
    const studentRow = $(`#student-row-${studentId}`);
    studentRow.find('.student-group').text(`Group #${groupId}`);
}
