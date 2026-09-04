document.addEventListener("DOMContentLoaded", function () {

    const searchInput =
        document.getElementById("maintenance-search");

    const statusFilter =
        document.getElementById("maintenance-status");

    const severityFilter =
        document.getElementById("severity");

    const maintenanceTable =
        document.getElementById("maintenance-table");


    if (
        !searchInput ||
        !statusFilter ||
        !severityFilter ||
        !maintenanceTable
    ) {
        return;
    }


    const reportRows =
        maintenanceTable.querySelectorAll("tbody tr");


    function filterMaintenanceReports() {

        const searchValue =
            searchInput.value
                .trim()
                .toLowerCase();

        const selectedStatus =
            statusFilter.value;

        const selectedSeverity =
            severityFilter.value;


        reportRows.forEach(function (row) {

            const roomNumber =
                row.dataset.roomNumber
                    .toLowerCase();

            const reportStatus =
                row.dataset.status;

            const reportSeverity =
                row.dataset.severity;


            const matchesSearch =
                roomNumber.includes(searchValue);

            const matchesStatus =
                selectedStatus === "all" ||
                reportStatus === selectedStatus;

            const matchesSeverity =
                selectedSeverity === "all" ||
                reportSeverity === selectedSeverity;


            const shouldShow =
                matchesSearch &&
                matchesStatus &&
                matchesSeverity;


            row.style.display =
                shouldShow ? "" : "none";

        });

    }


    searchInput.addEventListener(
        "input",
        filterMaintenanceReports
    );

    statusFilter.addEventListener(
        "change",
        filterMaintenanceReports
    );

    severityFilter.addEventListener(
        "change",
        filterMaintenanceReports
    );

});