<nav>

    <h3>Navigation</h3>
    <ul>
        <li><a href="<?php echo BASE_PATH; ?>">Home</a></li>
        <li><a href="<?php echo BASE_PATH . '/basket'; ?>">My Shopping Basket</a></li>
        <li><a href="<?php echo BASE_PATH . '/admin'; ?>">Administration Area</a></li>
    </ul>
    
    
    <h3>Categories</h3>
    <ul class="categories">
    <?php

        /**
         * Creates a new Categories class instance and calls the method every
         * time the loop runs so as to update the number of products under each
         * category.
         */
        
        $navigationDispatch = new CategoriesController('categories', '_getProductCountByCat');

        foreach ($categories as $navCats) {
            // Runs the method on every loop
            $productNumber = $navigationDispatch->_getProductCountByCat($navCats['category_ID']);
            if (isset($category['category_ID'])) {
                $active = $category['category_ID'] == $navCats['category_ID'] ? ' class="active"' : '';
                    echo '<li' . $active . '><a href="' . BASE_PATH . '/categories/getById/' . $navCats['category_ID'] . '">' . $navCats['category_name'] . ' (' . $productNumber . ')</a></li>';
            } else {
                echo '<li><a href="' . BASE_PATH . '/categories/getById/' . $navCats['category_ID'] . '">' . $navCats['category_name'] . ' (' . $productNumber . ')</a></li>';
            }
        }

    ?>
    </ul>

    <div id="search">
        <h3>Search</h3>
        <input type="text" value="" id="searchText" name="searchText" autocomplete="off" class="searchText" onkeyup="layercms.webscrp.liveSearch(this.value, '<?php echo BASE_PATH; ?>')">
        <div id="liveSearch" class="noBorder"></div>
    </div>

</nav>