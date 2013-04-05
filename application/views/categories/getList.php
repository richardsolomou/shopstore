<article>

	<h1>Categories</h1>

    <table class="bordered hoverRed">
        <thead>
            <tr>
                <th>ID</th>
                <th class="lefted">Name</th>
                <th class="lefted">Product No.</th>
                <th class="lefted">Parent</th>
                <th class="operations">Operations</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($categories1 as $category1) {
                    $category_parent = "-";
                    foreach($categories2 as $category2) if ($category1['category_parent_ID'] == $category2['category_ID']) $category_parent = $category2['category_name'];
                    $productNumber = $categoryDispatch->_getProductCountByCat($category1['category_ID']);
                    if ($productNumber == 0) $productNumber = "-";
            ?>
                    <tr class="centered">
                        <td><?php echo $category1['category_ID']; ?></td>
                        <td class="lefted"><?php echo $category1['category_name']; ?></td>
                        <td class="lefted"><?php echo $productNumber ?></td>
                        <td class="lefted"><?php echo $category_parent ?></td>
                        <td class="operations">
                            <button href="javascript:void(0)" onclick="layercms.webscrp.getEditForm('categories', 'category_ID', <?php echo $category1['category_ID'] ?>)" class="btn">Edit</button>
                            <button href="javascript:void(0)" onclick="layercms.webscrp.doDelete('categories', 'category_ID', <?php echo $category1['category_ID'] ?>)" class="btn">Delete</button>
                        </td>
                    </tr>
                    <tr class="noHover">
                        <td colspan="5"><div class="showHide" id="edit_<?php echo $category1['category_ID'] ?>"></div></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>

</article>