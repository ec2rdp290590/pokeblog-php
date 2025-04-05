<?php
/**
 * Inicialización de la sesión y obtención del nombre del Pokémon desde la URL.
 * 
 * Este archivo maneja la lógica del modal para mostrar detalles específicos de un Pokémon.
 */
session_start();

// Obtener el nombre del Pokémon desde la URL
$pokemonName = $_GET['pokemon'] ?? null;

if (!$pokemonName) {
    die("No se especificó un Pokémon.");
}

/**
 * Función para consumir la API de Pokémon y obtener detalles específicos.
 * 
 * @param string $name Nombre del Pokémon.
 * @return array Datos detallados del Pokémon en formato asociativo.
 */
function fetchPokemonDetails($name) {
    $url = "https://pokeapi.co/api/v2/pokemon/{$name}";
    $response = file_get_contents($url);

    // Decodificamos la respuesta JSON a un array asociativo
    return json_decode($response, true);
}

// Obtener los detalles del Pokémon seleccionado
$pokemon = fetchPokemonDetails($pokemonName);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de <?= ucfirst(htmlspecialchars($pokemonName)) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Modal -->
    <div class="md-modal">
        <div class="md-modal__content">
            <h1>Detalles de <?= ucfirst(htmlspecialchars($pokemonName)) ?></h1>

            <!-- Detalles del Pokémon -->
            <div class="pokemon-details">
                <img src="<?= htmlspecialchars($pokemon['sprites']['front_default']) ?>" alt="<?= htmlspecialchars($pokemon['name']) ?>">
                <h2><?= ucfirst(htmlspecialchars($pokemon['name'])) ?></h2>

                <!-- Tipo del Pokémon -->
                <p><strong>Tipo:</strong> <?= implode(', ', array_column($pokemon['types'], 'type')['name']) ?></p>

                <!-- Habilidades del Pokémon -->
                <p><strong>Habilidades:</strong> <?= implode(', ', array_column($pokemon['abilities'], 'ability')['name']) ?></p>

                <!-- Estadísticas del Pokémon -->
                <p><strong>Estadísticas:</strong></p>
                <ul>
                    <?php foreach ($pokemon['stats'] as $stat): ?>
                        <li><?= ucfirst(str_replace('-', ' ', htmlspecialchars($stat['stat']['name']))) ?>: <?= $stat['base_stat'] ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Botón para cerrar el modal -->
            <a href="index.php" class="md-button md-button--text">Cerrar</a>
        </div>
    </div>
</body>
</html>
