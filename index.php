<?php
require('connection.php');
$errors = "";
session_start();

if (isset($_POST['task'])) {
    $task = $_POST['task'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $progress = '-';
    $edit = '';
    if (isset($_GET['edit_task'])) {
        $id = $_GET['edit_task'];
        $sql = mysqli_query($connection, "UPDATE tasks SET task='{$_POST['task']}' WHERE id=$id ");
    } else {
        $sql = mysqli_query($connection, "INSERT INTO tasks (task, name, email, progress, edit) VALUES ('$task', '$name', '$email', '$progress', '$edit')");
    }
}

if (isset($_POST['submit'])) {
    $id = $_GET['edit_task'];
    $sql = mysqli_query($connection, "UPDATE tasks SET task=$_POST WHERE id=$id ");
}

if (isset($_GET['del_task'])) {
    $id = $_GET['del_task'];
    mysqli_query($connection, "DELETE FROM tasks WHERE id=$id");
}

if (isset($_GET['access_task'])) {
    $id = $_GET['access_task'];
    mysqli_query($connection, "UPDATE tasks SET progress='+' WHERE id=$id");
}

if (isset($_GET['edit_task'])) {
    $id = $_GET['edit_task'];
    $sql = mysqli_query($connection, "SELECT task FROM tasks WHERE id=$id");
    $tasks = mysqli_fetch_array($sql);
}

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

$limit = 3;
$number = ($page * $limit) - $limit;
$res_count = mysqli_query($connection, "SELECT COUNT(*) FROM tasks");
$row = mysqli_fetch_row($res_count);
$total = $row[0];
$str_pag = ceil($total / $limit);

$sql = mysqli_query($connection, "SELECT * FROM tasks LIMIT $number, $limit");

if (isset($_GET['sortby'])) {

    $sortby = $_GET['sortby'];

    // DESC ASC

    if ($sortby == 'id') {
        $sql = mysqli_query($connection, "SELECT * FROM tasks ORDER BY id LIMIT $number, $limit");
    } elseif ($sortby == 'name') {
        $sql = mysqli_query($connection, "SELECT * FROM tasks ORDER BY name LIMIT $number, $limit");
    } elseif ($sortby == 'email') {
        $sql = mysqli_query($connection, "SELECT * FROM tasks ORDER BY email LIMIT $number, $limit");
    } elseif ($sortby == 'task') {
        $sql = mysqli_query($connection, "SELECT * FROM tasks ORDER BY task LIMIT $number, $limit");
    }
}

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
            <li class="logo"><a href="/index.php">List App</a></li>
            <?php if (isset($_SESSION['username'])) { ?><li class="login"><a href="/logout.php"><?php echo "Sign Out" ?></a></li><?php } else { ?>
                <li class="login"><a href="/login.php"><?php echo "Sign In" ?></a></li><?php } ?>
        </ul>

    </header>
    <?php if (isset($_GET['edit_task'])) { ?>
        <form method="POST" action="" class="add-task">
            <textarea name="task" cols="40" rows="3" class="task-input" placeholder="Input your task...">
                <?php echo $tasks['task'];  ?>
            </textarea>
            <button type="submit" class="task-btn">Edit Task</button>
            <a href="/index.php"><button type="submit" class="task-btn">Add Task</button></a>
        </form>
    <?php } else { ?>
        <form method="POST" action="index.php" class="add-task">
            <input type="email" name="email" class="task-email" placeholder="Email" required />
            <input type="text" name="name" class="task-name" placeholder="Login" required />
            <textarea name="task" cols="40" rows="3" class="task-input" placeholder="Input your task..." required></textarea>
            <button type="submit" class="task-btn">Add Task</button>
        </form>
    <?php } ?>
    <div class="sort-by">
        <span>Sort By:</span>
        <a href="index.php?sortby=id">Id</a></<a>
        <a href="index.php?sortby=name">Name</a></option>
        <a href="index.php?sortby=email">Email</a></option>
        <a href="index.php?sortby=task">Tasks</a></option>
    </div>

    <table>
        <thead>
            <tr>
                <th>N</th>
                <th>Email</th>
                <th>Name</th>
                <th>Task</th>
                <th>Perfomence</th>
                <?php if (isset($_SESSION['username'])) { ?><th>Action</th><?php } ?>
            </tr>
        </thead>

        <tbody>
            <?php $i = 1;
            while ($rows = mysqli_fetch_array($sql)) { ?>
                <tr>
                    <td class="center"><?php echo $i; ?></td>
                    <td class="center"><?php echo $rows['email'] ?></td>
                    <td class="center"><?php echo $rows['name'] ?></td>
                    <td>
                        <?php if (isset($_SESSION['username'])) { ?>
                            <?php echo $rows['task']; ?>
                            <?php echo $rows['edit']; ?>
                            <br />
                            <a href="index.php?edit_task=<?php echo $rows['id']; ?>">Edit Task</a>
                        <?php } else { ?>
                            <?php echo $rows['task']; ?>
                        <?php } ?>
                    </td>
                    <?php if (isset($_SESSION['username'])) { ?>
                        <td class="center access">
                            <a href="index.php?access_task=<?php echo $rows['id']; ?>"><?php echo $rows['progress'] ?></a>
                        </td>
                        <td class="center delete">
                            <a href="index.php?del_task=<?php echo $rows['id']; ?>">x</a>
                        </td>
                    <?php } else { ?>
                        <td class="center access">
                            <a href=""><?php echo $rows['progress'] ?></a>
                        </td>
                    <?php } ?>
                </tr>
            <?php $i++;
            } ?>
        </tbody>

    </table>

    <div class='pagination'>
        <?php
        $page_url = (isset($_SERVER["QUERY_STRING"]) ? '&' : '?');
        for ($i = 1; $i <= $str_pag; $i++) {
            echo "<a class='page' href=$page_url.page=" . $i . ">" . $i . "</a>";
            // echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }
        ?>
    </div>

</body>

</html>