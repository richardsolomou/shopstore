<nav>

    <h3>Navigation</h3>
    <ul>
        <li><a href="<?php echo BASE_PATH; ?>">Home</a></li>
        <li><a href="<?php echo BASE_PATH . '/basket'; ?>">My Shopping Basket</a></li>
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

    <hr>

    <h3>Administrator.</h3>
    <div id="login_1"></div>
    
    <?php if(!isset($_SESSION['SESS_ADMINLOGGEDIN']) || !isset($_SESSION['SESS_ADMINID'])) { ?>
    
        <form method="post" id="loginForm_1">
            
            <input type="hidden" name="operation" id="operation" value="true">
            <input type="hidden" name="admin" id="admin" value="1">

            <table class="table3">
                <tr><td class="nopadding nomargin"><input type="text" id="username" placeholder="Username" name="username" required value="" pattern="[a-zA-Z0-9]+"></td></tr>
                <tr><td class="nopadding nomargin"><input type="password" id="password" placeholder="Password" name="password" required value="" pattern="[a-zA-Z0-9]+"></td></tr>
                <tr><td><input type="submit" class="btn" name="login" value="Login" onclick="layercms.webscrp.login(1, '<?php echo BASE_PATH; ?>'); return false;"></td></tr>
            </table>

        </form>

    <?php } else { ?>

        <p class="centered"><button onclick="layercms.webscrp.logout(1, '<?php echo BASE_PATH; ?>', 1); return false;" class="btn">Logout</button></p>
    
    <?php } ?>

    <hr>

</nav>