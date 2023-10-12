<?php
$books = json_decode(file_get_contents('books.json'), true);

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = strtolower($_GET['search']);
    $books = array_filter($books, function($book) use ($search) {
        return stripos(strtolower($book['title']), $search) !== false ||
               stripos(strtolower($book['author']), $search) !== false ||
               stripos(strtolower($book['isbn']), $search) !== false;
    });
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newBook = [
        'title' => $_POST['title'],
        'author' => $_POST['author'],
        'available' => isset($_POST['available']), // Set to true if the checkbox is selected
        'pages' => $_POST['pages'],
        'isbn' => $_POST['isbn'],
    ];

    $books[] = $newBook;
    $updatedBooksData = json_encode($books, JSON_PRETTY_PRINT);
    file_put_contents('books.json', $updatedBooksData);
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    if (array_key_exists($id, $books)) {
        unset($books[$id]);
        $updatedBooksData = json_encode(array_values($books), JSON_PRETTY_PRINT);
        file_put_contents('books.json', $updatedBooksData);
    }
}
?>

<html lang="en">

<head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Manager</title>
    <link rel="stylesheet" type="text/css" href="styles.css"> <!-- Add this line to link the CSS file -->
</head>

<body>
    <h1><center>Book Query</center></h1>
    <form action="index.php" method="GET">
        <input type="text" name="search" size="32" placeholder="Enter title, author, or ISBN..." value=<?php echo (isset($_GET['search'])) ? $_GET['search'] : "" ?>>
        <input type="submit" value="🔍 Search">
    </form>
    <table border="4">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Available</th>
            <th>Pages</th>
            <th>ISBN</th>
        </tr>
        <?php foreach ($books as $key => $book) : ?>
            <tr>
                <td><?= $book['title'] ?></td>
                <td><?= $book['author'] ?></td>
                <td><?= $book['available'] ? 'True' : 'False' ?></td>
                <td><?= $book['pages'] ?></td>
                <td><?= $book['isbn'] ?></td>
                <td><a href="index.php?id=<?php echo $key ?>"><button>➖ Delete</button></a></td>
            </tr>
        <?php endforeach; ?>
        <form action="index.php" method="POST">
            <tr>
                <td><input type="text" name="title" placeholder="Title" required></td>
                <td><input type="text" name="author" placeholder="Author" required></td>
                <td><input type="checkbox" name="available" checked> </td>
                <td><input type="number" name="pages" placeholder="Pages" required></td>
                <td><input type="number" name="isbn" placeholder="ISBN" required></td>
                <td><input type="submit" value="➕ Save"></td>
            </tr>
        </form>
    </table>
    <br>
    <br>
    <a href="books.json" target="_blank">click to see Json Contents</a>
</body>

</html>
