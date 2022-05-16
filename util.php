<?php function validatePos()
{
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) continue;
        if (!isset($_POST['desc' . $i])) continue;
        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];
        if (!strlen($year) || !strlen($desc)) {
            return 'All fields required';
        }
        if (!is_numeric($year)) {
            return 'Note year must be numeric';
        }
    }
    return true;
}


function validateCourse()
{
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['course_year' . $i])) continue;
        if (!isset($_POST['course' . $i])) continue;
        $year = $_POST['course_year' . $i];
        $course = $_POST['course' . $i];
        if (!strlen($year) || !strlen($course)) {
            return 'All fields required';
        }
        if (!is_numeric($year)) {
            return 'Course year must be numeric';
        }
    }
    return true;
}

function flashMessages()
{
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red">' . $_SESSION['error'] . '</p>';
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
        echo '<p style="color:green">' . $_SESSION['success'] . '</p>';
        unset($_SESSION['success']);
    }
}
