<nav>

    <h3>Navigation</h3>
    <ul>
        <li><a href="<?php echo BASE_PATH . DS; ?>">Home</a></li>
        <li><a href="<?php echo BASE_PATH . DS . 'basket'; ?>">My Shopping Basket</a></li>
        <li><a href="<?php echo ADMIN_PATH; ?>">Administration Area</a></li>
    </ul>
    
    <h3><a href="<?php echo  BASE_PATH . DS . 'categories' . DS . 'getList'; ?>">Categories</a></h3>
    <ul class="categories">
    <?php

        /**
         * Creates a new Categories class instance and calls the method every
         * time the loop runs so as to update the number of products under each
         * category.
         */
        
        $dispatch = new Categories;

        foreach ($categories as $navCats) {
            // Runs the method on every loop
            $productNumber = $dispatch->getProductCountByCat($navCats['category_ID']);
            if (isset($category['category_ID'])) {
                $active = $category['category_ID'] == $navCats['category_ID'] ? ' class="active"' : '';
                    echo '<li' . $active . '><a href="' . BASE_PATH . DS . 'categories' . DS . 'getById' . DS . $navCats['category_ID'] . '">' . $navCats['category_name'] . ' (' . $productNumber . ')</a></li>';
            } else {
                echo '<li><a href="' . BASE_PATH . DS . 'categories' . DS . 'getById' . DS . $navCats['category_ID'] . '">' . $navCats['category_name'] . ' (' . $productNumber . ')</a></li>';
            }
        }

    ?>
    </ul>

    <form action="<?php echo BASE_PATH . '/search'; ?>" id="search" method="post">
        <input type="text" value="" name="searchText" class="searchText">
        <input type="submit" value="Search" name="searchButton" class="searchButton">
    </form>

</nav>