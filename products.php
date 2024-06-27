<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="HTML5 Template" />
    <meta name="description"
        content="YARS - Html5 Template For Fauctes, Sanitary, Bathroom, Kitchen and Multipurpose E-commerce Store" />
    <meta name="author" content="webaashi.com" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>YARS - Bathwares</title>

    <!-- Favicon -->
    <!-- <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- <link rel="icon" href="assets/img/favicon.ico" type="image/x-icon"> -->
    <!-- Master Css -->
    <link href="main.css" rel="stylesheet">
    <link href="assets/css/color.css" rel="stylesheet" id="colors">
</head>

<body>
    <!--//==Preloader Start==//-->
    <div class="preloader loaderout">
        <div class="cssload-container">
            <div class="cssload-loading">
                <div id="object"><i class="fa fa-bath" aria-hidden="true"></i></div>
            </div>
            <h4 class="title">Loading</h4>
        </div>
    </div>
    <!--//==Preloader End==//-->
    <!--//==Header Start==//-->
    <header id="main-header">

        <!--//==Navbar Start==//-->
        <div id="main-menu" class="wa-main-menu">
            <div class="wathemes-menu relative">
                <div class="navbar navbar-default navbar-bg-light" role="navigation">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-3 col-sm-3 col-xs-12"style="margin-top:5px;">
                                <a class="logo hidden-xs" href="index.php" >
                                    <img class="site_logo" alt="Site Logo" style="filter: invert(100%);" src="assets/img/newlogobg.png" />
                                </a>
                            </div>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <div class="navbar-header">
                                    <!-- Button For Responsive toggle -->
                                    <button type="button" class="navbar-toggle" data-toggle="collapse"
                                        data-target=".navbar-collapse">
                                        <span class="sr-only">Toggle navigation</span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span></button>
                                    <!-- Logo -->
                                    <a class="navbar-brand hidden-lg hidden-md hidden-sm" href="index.php">
                                        <img class="site_logo" alt="Site Logo" style="filter: invert(100%);" src="assets/img/newlogobg1.png">
                                    </a>
                                </div>
                                <!-- Navbar Collapse -->
                                <div class="navbar-collapse collapse">
                                    <!-- Right nav Start -->
                                    <ul class="nav navbar-nav sm" data-menus-id="17133368737364475">
                                        <li><a href="index.php">Home</a></li>
                                        <li>
                                            <a href="products.php">Products</a>


                                        </li>
                                        <li>
                                            <a href="about.html">About</a>


                                        </li>
                                        <li>
                                            <a href="contact.html">Contact Us</a>


                                        </li>


                                    </ul>
                                    <!-- /.Right nav  End-->
                                </div>
                                <!-- /.navbar-collapse -->
                            </div>
                            <!-- /.nav Col -->

                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container -->
                </div>
            </div>
        </div>
        <!--//==Navbar End==//-->
    </header>
    <!--//==Header End==//-->
    <!--//==Page Header Start==//-->
    <div class="page-header black-overlay">
        <div class="container breadcrumb-section">
            <div class="row pad-s15">
                <div class="col-md-12">
                    <h2>Wide Range of Categories</h2>
                    <div class="clear"></div>
                    <div class="breadcrumb-box">
                        <ul class="breadcrumb">
                            <li>
                                <a href="index.php">Home</a>
                            </li>
                            <li class="active">products </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--//==Page Header End==//-->
    <!--//=========product Page Start=========//-->
    <?php
// Include database connection
        require 'db_connection.php';

        // Constants for pagination
        $categoriesPerPage = 12; // Number of categories to display per page
        $page = isset($_GET['page']) ? $_GET['page'] : 1; // Get current page number, default to 1 if not set

        // Calculate the offset for fetching categories
        $offset = ($page - 1) * $categoriesPerPage;

        // Fetch categories from the database with pagination
        $sql = "SELECT * FROM categories LIMIT :offset, :perPage";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $categoriesPerPage, PDO::PARAM_INT);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch total number of categories for pagination
        $sqlCount = "SELECT COUNT(*) as total FROM categories";
        $stmtCount = $pdo->query($sqlCount);
        $totalCategories = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($totalCategories / $categoriesPerPage);
        ?>
    <section class="wa-products-main padTB100">
        <div class="container">
            <div class="row">
                <!--//=========Product Sorting Section Start=========//-->
                <!--//=========Product Sorting Section End=========//-->
                <!--product Item-->
                <?php foreach ($categories as $category): ?>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 mix">
                        <a href="product.php?category_id=<?php echo $category['category_id']; ?>">
                            <div class="wa-products">
                                <div class="wa-products-thumbnail wa-item">
                                    <img src="uploads/<?php echo $category['category_image']; ?>"
                                        alt="<?php echo $category['category_name']; ?>" style="max-height:400px; min-height:400px;">
                                </div>
                                <div class="wa-products-caption">
                                    <h2>
                                        <a href="product.php?category_id=<?php echo $category['category_id']; ?>">
                                            <?php echo $category['category_name']; ?>
                                        </a>
                                    </h2>
                                    <div class="clear"></div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
                <!--product Item-->

                <div class="clear"></div>
                    <?php if ($totalPages > 1): ?>
                        <div class="col-md-12">
                            <div class="styled-pagination padB30 text-center">
                                <ul>
                                    <?php if ($page > 1): ?>
                                        <li><a class="prev" href="?page=<?php echo $page - 1; ?>"><i class="fa fa-angle-left"></i></a></li>
                                    <?php endif; ?>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li><a href="?page=<?php echo $i; ?>" <?php echo ($page == $i) ? 'class="active"' : ''; ?>><?php echo $i; ?></a></li>
                                    <?php endfor; ?>
                                    <?php if ($page < $totalPages): ?>
                                        <li><a class="next" href="?page=<?php echo $page + 1; ?>"><i class="fa fa-angle-right"></i></a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

            </div>
        </div>
    </section>
    <!--//=========product Page End=========//-->
    <!--//=========Footer Start=========//-->
    <footer id="main-footer" class="dark-footer footer-style1">
        <!--Upper Footer Block-->
        <div class="upper-footer wv_footer">
            <div class="container">
                <div class="row pad-s15">
                    <!--Widget Block-->
                    <div class="col-md-3 col-sm-6">
                        <div class="widget contact-widget">
                            <h4>Contact</h4>
                            <p>
                                Discover luxury, functionality, and style in every detail. Transform your bathroom into a sanctuary of relaxation with Yars.
                            </p>
                            <p><span class="rounded-icon"><i class="fa fa-map-marker"></i></span>Shivaji Nager linepar Moradabad UP India 244001.
                            </p>
                            <p><span class="rounded-icon"><i class="fa fa-phone"></i></span>+91 9456688608
                            </p>
                            <p><span class="rounded-icon"><i class="fa fa-envelope-o"></i></span>Yarsbathware@gmail.com
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="widget">
                            <h4>Information</h4>
                            <ul>
                                <li><a href="contact.html"><i class="fa fa-angle-double-right wv_circle"></i>Contact Us</a></li>
                                
                            </ul>
                        </div>
                    </div>
                    <!--Widget Block-->
                    <div class="col-md-3 col-sm-6">
                        <div class="widget">
                            <h4>Category</h4>
                            <ul>
                                <li><a href="products.php"><i class="fa fa-angle-double-right wv_circle"></i> Products</a></li>
                                
                            </ul>
                        </div>
                    </div>
                    <!--Widget Block-->
                    <div class="col-md-3 col-sm-6">
                        <div class="widget">
                            <h4>Location</h4>
                            <ul>
                                <li><a href="about.html"><i class="fa fa-angle-double-right wv_circle"></i> Google Map</a></li>
                                
                            </ul>
                        </div>
                    </div>
                    <!--Widget Block-->
                    
                </div>
            </div>
        </div>
        <!--Copyright Footer Block-->
        <div class="bottom-footer">
            <div class="container">
                <div class="row pad-s15">
                    <div class="col-md-12 copy-right text-center">
                        <p>Copyright &copy; 2024 YARS, All Rights Reserved</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!--//=========Footer End=========//-->
    <!--//=========Newsletter Popup Start =========//-->


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="assets/plugins/menu/js/hover-dropdown-menu.js"></script>
    <script type="text/javascript" src="assets/plugins/menu/js/jquery.hover-dropdown-menu-addon.js"></script>
    <script src="assets/plugins/owl-carousel/js/owl.carousel.js"></script>
    <script type="text/javascript" src="assets/plugins/switcher/switcher.js"></script>
    <script src="assets/js/main.js"></script>

</body>
<style>
    @media print {
        #simplifyJobsContainer {
            display: none;
        }
    }
</style>
<div id="simplifyJobsContainer"
    style="position: absolute; top: 0px; left: 0px; width: 0px; height: 0px; overflow: visible; z-index: 2147483647;">
    <span></span>
</div>
<script id="simplifyJobsPageScript"
    src="chrome-extension://pbanhockgagggenencehbnadejlgchfc/js/pageScript.bundle.js"></script>

</html>