<?php

function hasPermission($permission)
{
    return isset($_SESSION['permissions'])
        && in_array($permission, $_SESSION['permissions'], true);
}

function requirePermission($permission)
{
    if (!hasPermission($permission)) {
        $_SESSION['flash_error'] = 'You do not have permission to view that page.';
        header('Location: ../index.php');
        exit;
    }
}