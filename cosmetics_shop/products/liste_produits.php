<?php
// Connexion à la base de données
if (file_exists('../../config/dbConnect.php')) {
    require '../../config/dbConnect.php';
} else {
    echo "File not found";
    die();
}

// Récupérer les produits depuis la base de données
$query = "SELECT * FROM products";
$result = mysqli_query($link, $query);
$products = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row; // Stocker les produits dans un tableau
    }
}

// Fermer la connexion
mysqli_close($link);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Produits - Cosmetics Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <img class="img-responsive d-block mx-auto" src="Design_sans_titre__1_-removebg-preview.png" alt="" width="50px"/>
    <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">Cosmetics Shop</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_product/ajout_produit.php">Ajouter un Produit</a></li>
                    <li class="nav-item"><a class="nav-link" href="liste_produits.php">Liste des Produits</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container mt-5">
        <h2>Liste des Produits</h2>
        <div id="productCount" class="mb-4"></div>
        <div id="productList" class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4" id="product-<?= $product['id'] ?>">
                    <div class="card">
                    <img src="../../images/<?= $product['image_url'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">

                        <div class="card-body">
                            <h5 class="card-title"><?= $product['name'] ?></h5>
                            <p class="card-text">Prix: €<?= $product['price'] ?></p>
                            <p class="card-text">Quantité: <?= $product['stock'] ?></p>
                            <button class="btn btn-danger" onclick="supprimerProduit(<?= $product['id'] ?>)">Supprimer</button>
                            <button class="btn btn-warning" onclick="modifierProduit(<?= $product['id'] ?>)">Modifier</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const productCount = document.getElementById("productCount");
            const productList = document.getElementById("productList");

            // Affichage du nombre total de produits
            const products = <?= json_encode($products) ?>;
            productCount.innerHTML = `<h5>Total des produits: ${products.length}</h5>`;
        });

        function supprimerProduit(productId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce produit ?")) {
                // Effectuer une requête AJAX pour supprimer un produit depuis la base de données
                fetch('liste_produits.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Produit supprimé avec succès!');
                        location.reload(); // Recharger la page après suppression réussie
                    } else {
                        alert('Erreur lors de la suppression du produit.');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la suppression du produit:', error);
                });
            }
        }

        function modifierProduit(productId) {
            const newName = prompt("Modifier le nom du produit:");
            const newPrice = prompt("Modifier le prix du produit:");
            const newImage = prompt("Modifier l'URL de l'image:");
            const newCategory = prompt("Modifier la catégorie:");
            const newQuantity = prompt("Modifier la quantité:");

            if (newName && newPrice && newImage && newCategory && newQuantity) {
                const updatedProduct = {
                    id: productId,
                    name: newName,
                    price: parseFloat(newPrice),
                    image_url: newImage,
                    category: newCategory,
                    stock: parseInt(newQuantity),
                };

                fetch('liste_produits.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'update', product: updatedProduct })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Produit modifié avec succès!');
                        location.reload(); // Recharger la page après modification réussie
                    } else {
                        alert('Erreur lors de la modification du produit.');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la modification du produit:', error);
                });
            }
        }

    </script>
</body>
</html>

<?php
// Traitement des actions de suppression et modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data['action'] == 'delete' && isset($data['id'])) {
        // Suppression du produit de la base de données
        require '../../config/dbConnect.php';
        $productId = $data['id'];
        $query = "DELETE FROM products WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $query)) {
            mysqli_stmt_bind_param($stmt, 'i', $productId);
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
        exit();
    }

    if ($data['action'] == 'update' && isset($data['product'])) {
        // Mise à jour du produit dans la base de données
        require '../../config/dbConnect.php';
        $product = $data['product'];
        $query = "UPDATE products SET name = ?, price = ?, image_url = ?, categorie = ?, stock = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($link, $query)) {
            mysqli_stmt_bind_param($stmt, 'sdssii', $product['name'], $product['price'], $product['image_url'], $product['category'], $product['stock'], $product['id']);
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
        exit();
    }
}
?>