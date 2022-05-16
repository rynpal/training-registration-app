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
        header('Location: edit.php?profile_id=' . $_GET['profile_id']);
        return;
    }

    if (
        !$_POST['firstName'] || !$_POST['lastName'] || !$_POST['email']
        || !$_POST['headline'] || !$_POST['summary']
    ) {
        $_SESSION['error'] = 'All fields must be complete';
        header('Location: edit.php?profile_id=' . $_GET['profile_id']);
        return;
    }

    $pos = validatePos();

    if (is_string($pos)) {
        $_SESSION['error'] = $pos;
        header('Location: edit.php?profile_id=' . $_GET['profile_id']);
        return;
    }

    $cor = validateCourse();

    if (is_string($cor)) {
        $_SESSION['error'] = $cor;
        header('Location: edit.php?profile_id=' . $_GET['profile_id']);
        return;
    }

    $stmt = $pdo->prepare('UPDATE profile SET first_name = :fn,last_name = :ln,email = :em,
                            headline=:hl,summary = :su WHERE profile_id = :pid');

    $stmt->execute(array(
        ':pid' => $_GET['profile_id'],
        ':fn' => $_POST['firstName'],
        ':ln' => $_POST['lastName'],
        ':em' => $_POST['email'],
        ':hl' => $_POST['headline'],
        ':su' => $_POST['summary']
    ));

    $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));

    $stmt = $pdo->prepare('DELETE FROM training WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));

    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) continue;
        if (!isset($_POST['desc' . $i])) continue;
        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];
        $rank = $i;

        $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) 
        VALUES (:pid, :rank, :year, :desc)');

        $stmt->execute(array(
            ':pid' => $_GET['profile_id'],
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc
        ));
    };

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
            ':profile_id' => $_GET['profile_id'],
            ':course_id' => $course_id,
            ':rank' => $rank,
            ':year' => $year
        ));
    }

    $_SESSION['success'] = 'Profile updated!';
    header('Location: index.php');
    return;
};


if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = 'Missing profile id';
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare('SELECT * FROM profile WHERE profile_id = :pid');
$stmt->execute(array(
    ':pid' => $_GET['profile_id']
));

$row = $stmt->fetch(PDO::FETCH_ASSOC);

$stmts = $pdo->prepare('SELECT * FROM position WHERE profile_id = :pid');
$stmts->execute(array(
    ':pid' => $_GET['profile_id']
));

$pos = $stmts->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT training.year, course.name FROM course INNER JOIN training ON course.course_id = training.course_id WHERE training.profile_id = :pid ORDER BY training.rank');
$stmt->execute(array(':pid' => $_GET['profile_id']));

$course = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SESSION['user_id'] !== $row['user_id']) {
    $_SESSION['error'] = 'Unauthorized access';
    header('Location: index.php');
    return;
}

if (!$row) {
    $_SESSION['error'] = 'Invalid profile id';
    header('Location: index.php');
    return;
}

?>

<html>

<head>
    <?php require_once 'head.php' ?>
    <title>Edit Profile</title>
</head>

<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php

        flashMessages();

        ?>
        <form method="post">
            <p><label for='firstName'>First Name:</label>
                <input name='firstName' type='text' size='50' value='<?= htmlentities($row['first_name']) ?>' />
            </p>
            <p><label for='lastName'>Last Name:</label>
                <input name='lastName' type='text' size='50' value='<?= htmlentities($row['last_name']) ?>' />
            </p>
            <p><label for='email'>Email:</label>
                <input name='email' type='text' size='40' value='<?= htmlentities($row['email']) ?>' />
            </p>
            <p><label for='headline'>Headline:</label>
                <input name='headline' type='text' size='70' value='<?= htmlentities($row['headline']) ?>' />
            </p>
            <p><label for='summary'>Summary:</label><br>
                <textarea name='summary' type='text' cols='70' rows='6'><?= htmlentities($row['summary']) ?></textarea>
            </p>
            <p>
                <b>Training Log:</b>
                <input type='submit' id='addCourse' value='+' />
            </p>
            <div id="course_fields">
                <?php
                $n = 1;
                foreach ($course as $c) {
                    echo "<div id='course{$n}'>";
                    echo "<p>Year: <input type='text' name='course_year{$n}' value = '" . htmlentities($c['year']) . "' />";
                    echo "<input type='button' value='-' onclick=" . "$('#course{$n}')" . ".remove() /></p>";
                    echo "<input type='text' size='70' name='course{$n}' value='" . htmlentities($c['name']) . "' class='course' />";
                    echo "</div><p></p>";
                    $n++;
                }
                ?>
            </div>
            <p>
                <b>Note Log:</b>
                <input type='submit' id='addPos' value='+' />
            </p>
            <div id='position_fields'>
                <?php
                $n = 1;
                foreach ($pos as $p) {
                    echo "<div id='position{$n}'>";
                    echo "<p>Year: <input type='text' name='year{$n}' value = '" . htmlentities($p['year']) . "' />";
                    echo "<input type='button' value='-' onclick=" . "$('#position{$n}')" . ".remove() /></p>";
                    echo "<textarea name='desc{$n}' rows='6' cols='70'>" . htmlentities($p['description']) . "</textarea>";
                    echo "<p></p></div>";
                    $n++;
                };
                ?>
            </div>
            <p></p>
            <p><input type='submit' value='Submit' />
                <a href='index.php'>Cancel</a>
            </p>
        </form>
        <script src='script.js'></script>
    </div>
</body>

</html>