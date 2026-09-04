document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("room-search");
    const floorFilter = document.getElementById("floor");
    const statusFilter = document.getElementById("status");
    const roomTable = document.getElementById("room-table");

    if (!searchInput || !floorFilter || !statusFilter || !roomTable) {
        return;
    }

    const roomRows = roomTable.querySelectorAll("tbody tr");

    function filterRooms() {

        const searchValue = searchInput.value
            .trim()
            .toLowerCase();

        const selectedFloor = floorFilter.value;
        const selectedStatus = statusFilter.value;

        roomRows.forEach(function (row) {

            const roomNumber = row.dataset.roomNumber
                .toLowerCase();

            const roomFloor = row.dataset.floor;

            const roomStatus = row.dataset.status;

            const matchesSearch =
                roomNumber.includes(searchValue);

            const matchesFloor =
                selectedFloor === "all" ||
                roomFloor === selectedFloor;

            const matchesStatus =
                selectedStatus === "all" ||
                roomStatus === selectedStatus;

            const shouldShow =
                matchesSearch &&
                matchesFloor &&
                matchesStatus;

            row.style.display = shouldShow ? "" : "none";
        });
    }

    searchInput.addEventListener("input", filterRooms);

    floorFilter.addEventListener("change", filterRooms);

    statusFilter.addEventListener("change", filterRooms);

});

document.addEventListener("DOMContentLoaded", function () {

    console.log("Card snapshot code is running");

    const refreshButton = document.getElementById("refresh-status");

    const availableCard = document.getElementById("count-available");
    const occupiedCard = document.getElementById("count-occupied");
    const dirtyCard = document.getElementById("count-dirty");
    const inProgressCard = document.getElementById("count-in-progress");
    const maintenanceCard = document.getElementById("count-maintenance");
    const blockedCard = document.getElementById("count-blocked");


    // Check whether we already have a saved card snapshot
    const savedCardCounts =
        sessionStorage.getItem("roomStatusCounts");


    if (savedCardCounts) {

        const counts = JSON.parse(savedCardCounts);

        availableCard.textContent = counts.available;
        occupiedCard.textContent = counts.occupied;
        dirtyCard.textContent = counts.dirty;
        inProgressCard.textContent = counts.in_progress;
        maintenanceCard.textContent = counts.maintenance;
        blockedCard.textContent = counts.blocked;

    } else {

        // First visit:
        // Save the values that PHP initially displayed.
        const initialCounts = {
            available: availableCard.textContent.trim(),
            occupied: occupiedCard.textContent.trim(),
            dirty: dirtyCard.textContent.trim(),
            in_progress: inProgressCard.textContent.trim(),
            maintenance: maintenanceCard.textContent.trim(),
            blocked: blockedCard.textContent.trim()
        };

        sessionStorage.setItem(
            "roomStatusCounts",
            JSON.stringify(initialCounts)
        );

    }


    // Refresh Status button
    if (refreshButton) {

        refreshButton.addEventListener("click", function () {

            fetch("?page=api-room-status")
                .then(function (response) {

                    if (!response.ok) {
                        throw new Error(
                            "Failed to fetch room status."
                        );
                    }

                    return response.json();

                })
                .then(function (data) {

                    if (!data.success) {
                        throw new Error(
                            "API returned an unsuccessful response."
                        );
                    }

                    console.log(
                        "Room status refreshed:",
                        data
                    );

                    const counts = data.statusCounts;


                    // Update the six cards
                    availableCard.textContent =
                        counts.available;

                    occupiedCard.textContent =
                        counts.occupied;

                    dirtyCard.textContent =
                        counts.dirty;

                    inProgressCard.textContent =
                        counts.in_progress;

                    maintenanceCard.textContent =
                        counts.maintenance;

                    blockedCard.textContent =
                        counts.blocked;


                    // Save the newly refreshed values
                    sessionStorage.setItem(
                        "roomStatusCounts",
                        JSON.stringify(counts)
                    );

                })
                .catch(function (error) {

                    console.error(
                        "Room status refresh failed:",
                        error
                    );

                });

        });

    }

});

document.addEventListener("DOMContentLoaded", function () {

    const taskStatusFilter = document.getElementById("task-status");
    const taskTypeFilter = document.getElementById("task-type");
    const priorityFilter = document.getElementById("priority");
    const taskSearchInput = document.getElementById("task-search");
    const taskTable = document.getElementById("task-table");

    // Only run this section on the Tasks page
    if (
        !taskStatusFilter ||
        !taskTypeFilter ||
        !priorityFilter ||
        !taskSearchInput ||
        !taskTable
    ) {
        return;
    }

    const taskRows = taskTable.querySelectorAll("tbody tr");

    function filterTasks() {

        const searchValue = taskSearchInput.value
            .trim()
            .toLowerCase();

        const selectedStatus = taskStatusFilter.value;
        const selectedType = taskTypeFilter.value;
        const selectedPriority = priorityFilter.value;

        taskRows.forEach(function (row) {

            const roomNumber =
                row.dataset.roomNumber.toLowerCase();

            const taskStatus =
                row.dataset.taskStatus;

            const taskType =
                row.dataset.taskType;

            const priority =
                row.dataset.priority;


            const matchesSearch =
                roomNumber.includes(searchValue);

            const matchesStatus =
                selectedStatus === "all" ||
                taskStatus === selectedStatus;

            const matchesType =
                selectedType === "all" ||
                taskType === selectedType;

            const matchesPriority =
                selectedPriority === "all" ||
                priority === selectedPriority;


            const shouldShow =
                matchesSearch &&
                matchesStatus &&
                matchesType &&
                matchesPriority;


            row.style.display =
                shouldShow ? "" : "none";

        });

    }


    taskSearchInput.addEventListener(
        "input",
        filterTasks
    );

    taskStatusFilter.addEventListener(
        "change",
        filterTasks
    );

    taskTypeFilter.addEventListener(
        "change",
        filterTasks
    );

    priorityFilter.addEventListener(
        "change",
        filterTasks
    );

});
