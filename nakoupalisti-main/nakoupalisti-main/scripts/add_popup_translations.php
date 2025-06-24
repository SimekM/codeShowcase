<?php

// Script to add translation keys for the popup component

// Database connection parameters
$host = 'localhost';
$dbname = 'nakoupalisti';
$username = 'root';
$password = '';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the current max ID from translates table
    $stmt = $pdo->query("SELECT MAX(id) as max_id FROM translates");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextId = isset($result['max_id']) ? $result['max_id'] + 1 : 1;

    // Prepare the translations to insert
    $translations = [
        [
            'id' => $nextId++,
            'cs' => 'Vítejte na našem webu!',
            'is_image' => 0
        ],
        [
            'id' => $nextId++,
            'cs' => 'Děkujeme, že jste navštívili naše stránky. Doufáme, že se vám bude líbit naše nabídka.',
            'is_image' => 0
        ],
        [
            'id' => $nextId++,
            'cs' => 'Neváhejte nás kontaktovat s jakýmikoli dotazy nebo připomínkami.',
            'is_image' => 0
        ],
        [
            'id' => $nextId++,
            'cs' => 'Přejeme vám příjemný den!',
            'is_image' => 0
        ],
        [
            'id' => $nextId++,
            'cs' => 'true',
            'is_image' => 0
        ]
    ];

    // Insert the translations
    $pdo->beginTransaction();

    foreach ($translations as $index => $translation) {
        $stmt = $pdo->prepare("INSERT INTO translates (id, cs, is_image) VALUES (:id, :cs, :is_image)");
        $stmt->execute([
            ':id' => $translation['id'],
            ':cs' => $translation['cs'],
            ':is_image' => $translation['is_image']
        ]);

        // Update the key names for reference
        echo "Inserted key: " . ($index === 0 ? "popup-text-1" : ($index === 1 ? "popup-text-2" : ($index === 2 ? "popup-text-3" : ($index === 3 ? "popup-text-4" :
                        "popup-value")))) . " with ID: " . $translation['id'] . "\n";
    }

    $pdo->commit();

    echo "\nSuccessfully added popup translation keys.\n";
    echo "Please update your template to use these IDs in the data-key attributes.\n";
} catch (PDOException $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
