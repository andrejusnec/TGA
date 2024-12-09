$(document).ready(function () {
    const INTERVAL = 10000;
    const $statusPage = $('#status-page');
    if ($statusPage.length > 0) {
        const statusPageUrl = $statusPage.data('status-url');

        function fetchStatusUpdates() {
            if ($('body').hasClass('modal-open')) {
                return;
            }

            $.ajax({
                url: statusPageUrl,
                type: 'GET',
                dataType: 'html',
                success: handleStatusUpdateSuccess,
            });
        }

        function handleStatusUpdateSuccess(data) {
            const $html = $(data);

            const $newStudentsContent = $html.find('#students-container').html();
            $('#students-container').html($newStudentsContent);

            const $newGroupsContent = $html.find('#groups-container').html();
            $('#groups-container').html($newGroupsContent);
        }

        setInterval(fetchStatusUpdates, INTERVAL);
    }
});
