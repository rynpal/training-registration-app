<?php
require_once 'pdo.php';
require_once 'util.php';
session_start();

if (!isset($_SESSION['name']) && !isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please log in';
    header('Location: index.php');
    return;
}

if (
    isset($_POST['firstName']) && isset($_POST['lastName']) &&
    isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])
) {

    if (!strpos($_POST['email'], '@')) {
        $_SESSION['error'] = 'Invalid email';
        header('Location: add.php');
        return;
    }

    if (
        !$_POST['firstName'] || !$_POST['lastName'] || !$_POST['email']
        || !$_POST['headline'] || !$_POST['summary']
    ) {
        $_SESSION['error'] = 'All fields must be complete';
        header('Location: add.php');
        return;
    }

    $pos = validatePos();

    if (is_string($pos)) {
        $_SESSION['error'] = $pos;
        header('Location: add.php');
        return;
    }

    $course = validateCourse();

    if (is_string($course)) {
        $_SESSION['error'] = $course;
        header('Location: add.php');
        return;
    }


    $stmt = $pdo->prepare('INSERT INTO profile (user_id,first_name,last_name,email,headline,summary) 
        VALUES (:uid,:fn,:ln,:em,:hl,:su)');
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['firstName'],
        ':ln' => $_POST['lastName'],
        ':em' => $_POST['email'],
        ':hl' => $_POST['headline'],
        ':su' => $_POST['summary']
    ));

    $profile_id = $pdo->lastInsertId();

    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) continue;
        if (!isset($_POST['desc' . $i])) continue;
        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];
        $rank = $i;

        $stmt = $pdo->prepare('INSERT INTO position (profile_id,rank,year,description)
        VALUES (:pid,:rank,:year,:desc)');

        $stmt->execute(array(
            ':pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc
        ));
    };

    // ADD INSERT PROFILE TRAININGS
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['course_year' . $i])) continue;
        if (!isset($_POST['course' . $i])) continue;
        $year = $_POST['course_year' . $i];
        $course = $_POST['course' . $i];
        $rank = $i;

        $course_id = false;
        //IS COURSE PRESENT
        $stmt = $pdo->prepare('SELECT course_id FROM course WHERE name = :name ');
        $stmt->execute(array(':name' => $course));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row !== false) $course_id = $row['course_id'];

        //ADD COURSE IF NOT PRESENT
        if ($course_id == false) {
            $stmt = $pdo->prepare('INSERT INTO course (name) VALUES (:name)');
            $stmt->execute(array(':name' => $course));
            $course_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare('INSERT INTO training (profile_id, course_id, rank, year) 
                                VALUES (:profile_id,:course_id,:rank,:year)');
        $stmt->execute(array(
            ':profile_id' => $profile_id,
            ':course_id' => $course_id,
            ':rank' => $rank,
            ':year' => $year
        ));
    }

    $_SESSION['success'] = 'Profile added!';
    header('Location: index.php');
    return;
};

?>

<html>

<head>
    <?php require_once 'head.php' ?>
    <title>Add Profile</title>
</head>

<body>
    <div class='container'>
        <h1>Add a New Profile</h1>
        <?php
        flashMessages();
        ?>
        <form method="post">
            <p><label for='firstName'>First Name:</label>
                <input name='firstName' type='text' size='50' />
            </p>
            <p><label for='lastName'>Last Name:</label>
                <input name='lastName' type='text' size='50' />
            </p>
            <p><label for='email'>Email:</label>
                <input name='email' type='text' size='40' />
            </p>
            <p><label for='headline'>Headline:</label><br>
                <input name='headline' type='text' size='70' />
            </p>
            <p><label for='summary'>Summary:</label><br>
                <textarea name='summary' type='text' cols='70' rows='6'></textarea>
            </p>
            <p>
                <b>Course Log:</b>
                <input type='submit' id='addCourse' value='+' />
            </p>
            <div id='course_fields'></div>
            <p></p>
            <p>
                <b>Note Log:</b>
                <input type='submit' id='addPos' value='+' />
            </p>
            <div id='position_fields'></div>
            <p></p>
            <p><input type='submit' value='Submit' />
                <a href='index.php'>Cancel</a>
            </p>
        </form>
        <script src='script.js'></script>
    </div>
</body>

</html>