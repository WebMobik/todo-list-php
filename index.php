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
        $sql = mysqli_query($connection, "UPDATE tasks SET task='{$_POST['task']}', edit='Edited'  WHERE id=$id ");
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
    $sortvalue = $_GET['sortvalue'];

    $sort = 'DESC';

    if ($sortvalue == 'asc') {
        $sort = 'ASC';
    }

    if ($sortby == 'id') {
        $sql = mysqli_query($connection, "SELECT * FROM tasks ORDER BY id $sort LIMIT $number, $limit");
    } elseif ($sortby == 'name') {
        $sql = mysqli_query($connection, "SELECT * FROM tasks ORDER BY name $sort LIMIT $number, $limit");
    } elseif ($sortby == 'email') {
        $sql = mysqli_query($connection, "SELECT * FROM tasks ORDER BY email $sort LIMIT $number, $limit");
    } elseif ($sortby == 'task') {
        $sql = mysqli_query($connection, "SELECT * FROM tasks ORDER BY task $sort LIMIT $number, $limit");
    } elseif ($sortby == 'perfomence') {
        $sql = mysqli_query($connection, "SELECT * FROM tasks ORDER BY progress $sort LIMIT $number, $limit");
    }
}

$query = http_build_query($_GET);
$query = preg_replace('/&page=\d*/i', '', $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/4/flatly/bootstrap.min.css" />
    <link rel="stylesheet" href="./styles/style.css">
    <title>Todo List</title>
</head>

<body>
    <header class="bg-primary">
        <ul>
            <li class="logo"><a href="/index.php">List App</a></li>
            <?php if (isset($_SESSION['username'])) { ?><li class="login"><a href="/logout.php"><?php echo "Sign Out" ?></a></li><?php } else { ?>
                <li class="login"><a href="/login.php"><?php echo "Sign In" ?></a></li><?php } ?>
        </ul>

    </header>
    <div class="container">
        <h2>Add your task</h2>
        <?php if (isset($_GET['edit_task'])) { ?>
            <form method="POST" action="" class="add-task">
                <textarea class="form-control" name="task" cols="40" rows="3" placeholder="Edit your task...">
                    <?php echo $tasks['task'];  ?>
                </textarea>
                <a class="come-back" href="/index.php">Come Back</a>
                <button type="submit" class="btn btn-outline-success">Edit Task</button>
            </form>
        <?php } else { ?>
            <form method="POST" action="index.php" class="add-task">
                <input type="email" class="form-control" name="email" placeholder="Enter email" required>
                <input type="text" class="form-control" name="name" placeholder="Enter name" required>
                <textarea class="form-control" name="task" rows="3" cols="40" placeholder="Input your task..." required></textarea>
                <button type="submit" class="btn btn-outline-success">Add Task</button>
            </form>
        <?php } ?>
        <div class="sort-by">
            <h4>Sort By:</h4>
            <form method="GET" action="" class="sort">
                <select name="sortby" class="form-control">
                    <option value="id">Id</option>
                    <option value="name">Name</option>
                    <option value="email">Email</option>
                    <option value="task">Task</option>
                    <option value="perfomence">Perfomence</option>
                </select>
                <select name="sortvalue" class="form-control">
                    <option value="asc" selected>Ascending</option>
                    <option value="desc">Descending</option>
                    <select />
                    <input type="submit" value="Sort" class="btn btn-outline-success" />
            </form>
        </div>

        <table class="table table-hover">
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
                        <td class="center"><?php echo $rows['id']; ?></td>
                        <td class="center"><?php echo $rows['email'] ?></td>
                        <td class="center"><?php echo $rows['name'] ?></td>
                        <td>
                            <?php if (isset($_SESSION['username'])) { ?>
                                <?php echo $rows['task']; ?>
                                <span class="text-info"><?php echo $rows['edit']; ?></span>
                                <br />
                                <a href="index.php?edit_task=<?php echo $rows['id']; ?>" class="blockquote-footer">Edit Task</a>
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

        <ul class="pagination pagination-sm">
            <?php
            for ($i = 1; $i <= $str_pag; $i++) { ?>
                <li><a class="page-link" href=?<?php echo $query ?>&page=<?php echo $i . "" ?>><?php echo $i; ?></a></li>
            <?php } ?>
        </ul>
    </div>

</body>

</html>