<?php
session_start();

// Arreglo en memoria para comentarios
$comments = $_SESSION['comments'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $pokemonName = trim($_POST['pokemon_name']);
    $commentText = trim($_POST['comment_text']);

    if (!empty($pokemonName) && !empty($commentText)) {
        $comments[$pokemonName][] = htmlspecialchars($commentText);
        $_SESSION['comments'] = $comments;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentarios - Pokémon Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="md-header">
        <h1>Comentarios</h1>
        <p>Explora los comentarios sobre tus Pokémon favoritos</p>
    </header>

    <main class="comments-section">
        <h2>Agregar Comentario</h2>
        <form method="post" class="comment-form">
            <label for="pokemon_name">Nombre del Pokémon:</label>
            <input type="text" id="pokemon_name" name="pokemon_name" placeholder="Ejemplo: pikachu" required>
            <label for="comment_text">Comentario:</label>
            <textarea id="comment_text" name="comment_text" placeholder="Escribe tu comentario aquí" required></textarea>
            <button type="submit" name="comment" class="md-button md-button--filled">Enviar Comentario</button>
        </form>

        <h2>Comentarios Existentes</h2>
        <?php if (!empty($comments)): ?>
            <ul class="comment-list">
                <?php foreach ($comments as $pokemonName => $pokemonComments): ?>
                    <li>
                        <strong><?= ucfirst(htmlspecialchars($pokemonName)) ?>:</strong>
                        <ul>
                            <?php foreach ($pokemonComments as $comment): ?>
                                <li><?= $comment ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No hay comentarios disponibles.</p>
        <?php endif; ?>
    </main>
</body>
</html>
