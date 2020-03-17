<?php
$errors = "";
session_start();
$db = mysqli_connect('localhost', 'root', '', 'todo');

if (isset($_POST['submit'])) {
    $task = $_POST['task'];

    if (empty($task)) {
        $errors = "Fill this line";
    } else {
        mysqli_query($db, "INSERT INTO tasks (task) VALUES ('$task')");
    }
}

if (isset($_GET['del_task'])) {
    $id = $_GET['del_task'];
    mysqli_query($db, "DELETE FROM tasks WHERE id=$id");
}

$tasks = mysqli_query($db, "SELECT * FROM tasks");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Todo List</title>
</head>

<body>
    <header>
        <ul>
            <li class="logo"><a href="/todo-list.php">List App</a></li>
            <?php if (isset($_SESSION['username'])) { ?><li class="login"><a href="/logout.php"><?php echo "Sign Out" ?></a></li><?php } else { ?>
                <li class="login"><a href="/index.php"><?php echo "Sign In" ?></a></li><?php } ?>
        </ul>

    </header>

    <form method="POST" action="index.php" class="add-task">
        <?php if (isset($errors)) { ?>
            <p><?php echo $errors; ?></p>
        <?php } ?>
        <input type="text" name="task" class="task-input" />
        <button type="submit" class="task-btn" name="submit">Add Task</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>N</th>
                <th>Task</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1;
            while ($row = mysqli_fetch_array($tasks)) { ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $row['task']; ?></td>
                    <td class="delete">
                        <a href="index.php?del_task=<?php echo $row['id']; ?>">x</a>
                    </td>
                </tr>
            <?php $i++;
            } ?>
        </tbody>

    </table>

</body>

</html>s