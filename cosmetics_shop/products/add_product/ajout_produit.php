<?php
    $errors = [];
    $name = $price = $image = $category = $subcategory = $quantity = "";

    require '../../../config/dbConnect.php';
    // Récupération des catégories et sous-catégories
    $query = "SELECT c.id AS category_id, c.category_name, 
    s.id AS subcategory_id, s.subcategory_name 
    FROM categories c 
    LEFT JOIN subcategories s ON c.id = s.category_id";

    $result = mysqli_query($link, $query);
    $categories = [];

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
        $category_id = $row['category_id'];
        if (!isset($categories[$category_id])) {
            $categories[$category_id] = [
            'name' => $row['category_name'],
            'subcategories' => []
            ];
        }
        if (!empty($row['subcategory_id'])) {
            $categories[$category_id]['subcategories'][] = [
            'id' => $row['subcategory_id'],
            'name' => $row['subcategory_name']
            ];
        }
    }
    }


    // Traitement uniquement côté serveur pour l'insertion
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST['productName']);
        $price = trim($_POST['productPrice']);
        $category = trim($_POST['productCategory']);
        $subcategory = trim($_POST['productSubcategory']);
        $quantity = trim($_POST['productQuantity']);
        $description = "Aucune description disponible";

        // Gestion de l'image
        if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === 0) {
            $image = basename($_FILES['productImage']['name']);
            $uploadDir = '../../../images/';
            $uploadFile = $uploadDir . $image;

            if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $uploadFile)) {
                $errors['productImage'] = "Erreur lors du téléchargement de l'image.";
            }
        }

        // Si aucune erreur liée au téléchargement d'image, insertion dans la base
        if (empty($errors)) {
            require '../../../config/dbConnect.php';

            $sql = "INSERT INTO products (name, description, price, stock, image_url, categorie, sub_categorie) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, 'ssdisss', $name, $description, $price, $quantity, $image, $category, $subcategory);
                if (mysqli_stmt_execute($stmt)) {
                    header('Location: ../liste_produits.php');
                    exit();
                } else {
                    $errors['general'] = "Erreur lors de l'ajout du produit. Veuillez réessayer.";
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_close($link);
        }
    }
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ajouter un Produit - Cosmetics Shop</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            .error {
                color: red;
                font-size: 0.9em;
                margin-top: 5px;
            }
            .is-invalid {
                border-color: red;
            }
        </style>
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
                    <li class="nav-item"><a class="nav-link" href="../../index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="ajout_produit.php">Ajouter un Produit</a></li>
                    <li class="nav-item"><a class="nav-link" href="../liste_produits.php">Liste des Produits</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Ajouter un Produit</h2>
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
        <?php endif; ?>

        <form id="productForm" action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="productName">Nom du Produit</label>
                <input type="text" class="form-control" id="productName" name="productName">
                <div class="error" id="productNameError"></div>
            </div>

            <div class="form-group">
                <label for="productPrice">Prix</label>
                <input type="number" class="form-control" id="productPrice" name="productPrice">
                <div class="error" id="productPriceError"></div>
            </div>

            <div class="form-group">
                <label for="productImage">Image</label>
                <input type="file" class="form-control" id="productImage" name="productImage">
                <div class="error" id="productImageError"></div>
            </div>

            <div class="form-group">
                <label for="productCategory">Catégorie</label>
                <select class="form-control" id="productCategory" name="productCategory" onchange="updateSubcategories()">
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach ($categories as $category_id => $category): ?>
                        <option value="<?php echo $category_id; ?>"><?php echo $category['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="error" id="productCategoryError"></div>
            </div>

            <div class="form-group">
                <label for="productSubcategory">Sous-catégorie</label>
                <select class="form-control" id="productSubcategory" name="productSubcategory">
                    <option value="">Sélectionner une sous-catégorie</option>
                </select>
                <div class="error" id="productSubcategoryError"></div>
            </div>
            <div class="form-group">
                <label for="productQuantity">Quantité</label>
                <input type="number" class="form-control" id="productQuantity" name="productQuantity" min="1">
                <div class="error" id="productQuantityError"></div>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter le Produit</button>
        </form>
        <a href="../liste_produits.php" class="btn btn-secondary mt-3">Voir la Liste des Produits</a>
    </div>
    <script src="script.js"></script>
    <script>
        const categories = <?php echo json_encode($categories); ?>;

        function updateSubcategories() {
            const categorySelect = document.getElementById('productCategory');
            const subcategorySelect = document.getElementById('productSubcategory');
            const selectedCategory = categorySelect.value;

            // Vider les sous-catégories
            subcategorySelect.innerHTML = '<option value="">Sélectionner une sous-catégorie</option>';

            if (selectedCategory && categories[selectedCategory]) {
                const subcategories = categories[selectedCategory].subcategories;
                subcategories.forEach(subcategory => {
                    const option = document.createElement('option');
                    option.value = subcategory.id;
                    option.textContent = subcategory.name;
                    subcategorySelect.appendChild(option);
                });
            }
        }
    </script>

    </body>
</html>
