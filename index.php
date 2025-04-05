<?php
/**
 * Inicialización de la sesión y variables en memoria.
 * 
 * Utilizamos sesiones para almacenar datos temporales como "likes", comentarios
 * y Pokémon ocultos. Esto permite mantener el estado durante la sesión del usuario.
 */
session_start();

// Arreglos en memoria para likes, comentarios y Pokémon ocultos
$pokemons = []; // Almacenará los datos de los Pokémon obtenidos de la API
$likes = $_SESSION['likes'] ?? [];
$comments = $_SESSION['comments'] ?? [];
$hiddenPokemons = $_SESSION['hiddenPokemons'] ?? [];

/**
 * Función para consumir la API de Pokémon.
 * 
 * @param int $id El ID del Pokémon a consultar.
 * @return array Datos del Pokémon en formato asociativo.
 */
function fetchPokemonData($id) {
    $url = "https://pokeapi.co/api/v2/pokemon/{$id}";
    $response = file_get_contents($url);

    // Decodificamos la respuesta JSON a un array asociativo
    return json_decode($response, true);
}

/**
 * Cargar los primeros 10 Pokémon desde la API.
 * 
 * Iteramos sobre los IDs de los Pokémon y verificamos si están ocultos antes de mostrarlos.
 */
for ($i = 1; $i <= 10; $i++) {
    $pokemon = fetchPokemonData($i);

    // Verificamos si el Pokémon está oculto
    if (!in_array($pokemon['name'], $hiddenPokemons)) {
        $pokemons[] = $pokemon;
    }
}

/**
 * Manejar acciones del usuario: likes, comentarios y ocultar Pokémon.
 * 
 * Utilizamos POST para procesar las acciones del usuario y actualizar los arreglos en memoria.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['like'])) {
        $pokemonName = $_POST['pokemon_name'];
        $likes[$pokemonName] = ($likes[$pokemonName] ?? 0) + 1;
        $_SESSION['likes'] = $likes;

        // Log: Incrementando like para el Pokémon
    } elseif (isset($_POST['hide'])) {
        $pokemonName = $_POST['pokemon_name'];
        $hiddenPokemons[] = $pokemonName;
        $_SESSION['hiddenPokemons'] = $hiddenPokemons;

        // Log: Ocultando Pokémon
    } elseif (isset($_POST['comment'])) {
        $pokemonName = $_POST['pokemon_name'];
        $comment = trim($_POST['comment_text']);

        // Validamos que el comentario no esté vacío
        if (!empty($comment)) {
            $comments[$pokemonName][] = $comment;
            $_SESSION['comments'] = $comments;

            // Log: Agregando comentario al Pokémon
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokémon Blog - Material Design 3</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Barra de Navegación -->
    <header class="md-header">
        <h1>Pokémon Blog</h1>
        <p>Explora y descubre tus Pokémon favoritos</p>
    </header>

    <!-- Sección de Cards de Pokémon -->
    <main class="pokemon-grid">
        <?php foreach ($pokemons as $pokemon): ?>
            <div class="card">
                <!-- Imagen del Pokémon -->
                <img src="<?= htmlspecialchars($pokemon['sprites']['front_default']) ?>" alt="<?= htmlspecialchars($pokemon['name']) ?>">

                <!-- Nombre del Pokémon -->
                <h2><?= ucfirst(htmlspecialchars($pokemon['name'])) ?></h2>

                <!-- Acciones -->
                <div class="actions">
                    <!-- Botón de Like -->
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="pokemon_name" value="<?= htmlspecialchars($pokemon['name']) ?>">
                        <button type="submit" name="like" class="md-button md-button--icon">
                            ❤️ <?= $likes[$pokemon['name']] ?? 0 ?>
                        </button>
                    </form>

                    <!-- Botón para Ocultar Pokémon -->
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="pokemon_name" value="<?= htmlspecialchars($pokemon['name']) ?>">
                        <button type="submit" name="hide" class="md-button md-button--text">Ocultar</button>
                    </form>

                    <!-- Enlace al Modal -->
                    <a href="modal.php?pokemon=<?= urlencode($pokemon['name']) ?>" class="md-button md-button--outlined">Ver Detalles</a>
                </div>
            </div>
        <?php endforeach; ?>
    </main>

    <!-- Sección de Comentarios -->
    <section class="comments-section">
        <h2>Comentarios</h2>

        <!-- Formulario para agregar comentarios -->
        <form method="post" class="comment-form">
            <input type="hidden" name="pokemon_name" value="<?= htmlspecialchars($pokemon['name'] ?? '') ?>">
            <textarea name="comment_text" placeholder="Escribe un comentario"></textarea>
            <button type="submit" name="comment" class="md-button md-button--filled">Enviar Comentario</button>
        </form>

        <!-- Mostrar comentarios existentes -->
        <?php if (!empty($comments)): ?>
            <ul class="comment-list">
                <?php foreach ($comments as $pokemonName => $pokemonComments): ?>
                    <li>
                        <strong><?= ucfirst(htmlspecialchars($pokemonName)) ?>:</strong>
                        <ul>
                            <?php foreach ($pokemonComments as $comment): ?>
                                <li><?= htmlspecialchars($comment) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</body>
</html>
