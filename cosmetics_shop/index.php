<?php
    session_start();
    // Connexion à la base de données
    if (file_exists('../config/dbConnect.php')) {
        require '../config/dbConnect.php';
    } else {
        echo "File not found";
        die();
    }; 

    $query = "SELECT * FROM products";
    $result = mysqli_query($link, $query);
    $produits = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $produits[] = $row; 
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
    <title>Cosmetics Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="index_style.css">
</head>
<body>
    <header id="top">
        <img class="img-responsive d-block mx-auto" src="Design_sans_titre__1_-removebg-preview.png" alt="" width="50px"/>
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
            <div class="container">
                <a class="navbar-brand" href="#">Cosmetics Shop</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                        <li class="nav-item"><a class="nav-link" href="products/add_product/ajout_produit.php">Ajouter un Produit</a></li>
                        <li class="nav-item"><a class="nav-link" href="products/liste_produits.php">Liste des Produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="panier.html"><i class="fas fa-shopping-cart"></i> Panier</a></li>
                    </ul>
                    <form class="form-inline my-2 my-lg-0">
                        <input class="form-control mr-sm-2" type="search" placeholder="Rechercher" aria-label="Search">
                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Rechercher</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="s_cover text-center">
            <h1>Beauté sans frontières</h1>
            <p>Exprimez votre éclat unique</p>
            <a href="#produits" class="btn btn-primary btn-lg">Découvrir plus</a>
        </section>

        <section id="faq" class="s_faq_collapse pt-5 pb-5">
            <div class="container">
                <h3>Bienvenue sur Cosmetics Shop!</h3>
                <p>Découvrez l’univers de la beauté avec notre sélection exclusive de produits cosmétiques.</p>
            </div>
        </section>

        <section id="produits" class="s_dynamic_snippet_products pt-5 pb-5">
            <div class="container">
                <h4>Nos produits phares</h4>
                <div class="row">
                    <?php foreach ($produits as $produit): ?>
                    <div class="col-md-4 product-card">
                        <div class="card">
                        <img src="<?php echo '../images/' . $produit['image_url'] ; ?>" class="card-img-top" alt="<?php echo $produit['name']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $produit['name']; ?></h5>
                                <p class="card-text"><?php echo $produit['description']; ?></p>
                                <p class="card-text"><?php echo '€ ' . number_format($produit['price'], 2); ?></p>
                                <button class="btn btn-primary" onclick="ajouterAuPanier('<?php echo $produit['name']; ?>', <?php echo $produit['price']; ?>)">Ajouter au panier</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <span>Copyright &copy; <span>Cosmoshop</span></span>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function ajouterAuPanier(nameProduct, priceProduct) {
            const panier = JSON.parse(localStorage.getItem('panier')) || [];
            const produit = {
                name: nameProduct,
                price: priceProduct,
                quantite: 1
            };

            const index = panier.findIndex(item => item.name === nameProduct);
            if (index > -1) {
                panier[index].quantite += 1;
            } else {
                panier.push(produit);
            }

            localStorage.setItem('panier', JSON.stringify(panier));
            alert('Produit ajouté au panier !');
        }

        window.onscroll = function() {
            const navbar = document.getElementById("navbar");
            if (window.pageYOffset > navbar.offsetTop) {
                navbar.classList.add("sticky");
            } else {
                navbar.classList.remove("sticky");
            }
        };
    </script>
</body>
</html>
