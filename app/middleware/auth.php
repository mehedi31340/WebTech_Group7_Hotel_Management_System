<?php

function requireHousekeepingLogin(): void
{
    if (
        !isset($_SESSION['user_id']) ||
        !isset($_SESSION['role'])
    ) {
        $_SESSION['auth_message'] = "Please log in to access the Housekeeping Supervisor pages.";

        header("Location: index.php?page=login");
        exit;
    }

    if ($_SESSION['role'] !== 'housekeeping') {
        $_SESSION['auth_message'] = "You do not have permission to access this page.";

        header("Location: index.php?page=login");
        exit;
    }
}
