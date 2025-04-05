<?php
/**
 * Inicialización de la sesión y variables en memoria.
 */
session_start();

// Arreglos en memoria para likes, comentarios y Pokémon ocultos
$pokemons = []; // Almacenará los datos de los Pokémon obtenidos de la API
$likes = $_SESSION['likes'] ?? [];
$hiddenPokemons = $_SESSION['hiddenPokemons'] ?? [];

/**
 * Función para consumir la API de Pokémon.
 */
function fetchPokemonData($id) {
    $url = "https://pokeapi.co/api/v2/pokemon/{$id}";
    $response = file_get_contents($url);
    return json_decode($response, true);
}

// Manejar acciones del usuario: likes y ocultar Pokémon
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['like'])) {
        $pokemonName = $_POST['pokemon_name'];
        $likes[$pokemonName] = ($likes[$pokemonName] ?? 0) + 1;
        $_SESSION['likes'] = $likes;
    } elseif (isset($_POST['hide'])) {
        $pokemonName = $_POST['pokemon_name'];
        $hiddenPokemons[] = $pokemonName;
        $_SESSION['hiddenPokemons'] = $hiddenPokemons;
    }
}

// Obtener el número de Pokémon a cargar desde la URL (paginación)
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Página actual
$limit = 50; // Número de Pokémon por página
$start = ($page - 1) * $limit + 1; // ID inicial del Pokémon
$end = $start + $limit - 1; // ID final del Pokémon

// Cargar los Pokémon correspondientes a la página actual
for ($i = $start; $i <= $end; $i++) {
    $pokemon = fetchPokemonData($i);
    if (!in_array($pokemon['name'], $hiddenPokemons)) {
        $pokemons[] = $pokemon;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pokémon Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="md-header">
        <h1>Pokémon Blog</h1>
        <p>Explora y descubre tus Pokémon favoritos</p>
    </header>

    <!-- Cards de Pokémon -->
    <main class="pokemon-grid">
        <?php foreach ($pokemons as $pokemon): ?>
            <div class="card">
                <img src="<?= htmlspecialchars($pokemon['sprites']['front_default']) ?>" alt="<?= htmlspecialchars($pokemon['name']) ?>">
                <h2><?= ucfirst(htmlspecialchars($pokemon['name'])) ?></h2>
                <div class="actions">
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="pokemon_name" value="<?= htmlspecialchars($pokemon['name']) ?>">
                        <button type="submit" name="like" class="md-button md-button--icon">❤️ <?= $likes[$pokemon['name']] ?? 0 ?></button>
                    </form>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="pokemon_name" value="<?= htmlspecialchars($pokemon['name']) ?>">
                        <button type="submit" name="hide" class="md-button md-button--text">Ocultar</button>
                    </form>
                    <a href="modal.php?pokemon=<?= urlencode($pokemon['name']) ?>" class="md-button md-button--outlined">Ver Detalles</a>
                </div>
            </div>
        <?php endforeach; ?>
    </main>

    <!-- Botón para cargar más Pokémon -->
    <div class="load-more">
        <a href="?page=<?= $page + 1 ?>" class="md-button md-button--filled">Cargar Más Pokémon</a>
    </div>

    <!-- Enlace para ver comentarios -->
    <div class="comments-link">
        <a href="comentarios.php" class="md-button md-button--outlined">Ver/Agregar Comentarios</a>
    </div>
</body>
</html>
