<article>

    <h1><?php echo $pageTitle ?></h1>

    <div id="operationAlert" class="showHide"></div>

    <button href="javascript:void(0)" onclick="layercms.webscrp.getAddForm('<?php echo $objectParse ?>')" class="btn">Add New <?php echo $objectParse ?></button>    
    <div id="message" class="showHide adminBar"></div>

    <table class="bordered hoverRed">
        <thead>
            <tr>
                <th>ID</th>
                <th class="lefted">Name</th>
                <th class="lefted width150">Image</th>
                <th class="operations">Operations</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($products as $product) {
            ?>
                    <tr class="centered">
                        <td><?php echo $product['product_ID']; ?></td>
                        <td class="lefted"><?php echo $product['product_name']; ?></td>
                        <td class="lefted width150">
                            <?php if ($product['product_image'] != null) {
                                echo '<img src="' . BASE_PATH . '/templates/img/products/' . $product['product_image'] . '">';
                            } else {
                                echo '<img src="' . BASE_PATH . '/templates/img/' . 'NA.png' . '">';
                            } ?>
                        </td>
                        <td class="operations">
                            <button href="javascript:void(0)" onclick="layercms.webscrp.getEditForm(<?php echo $product['product_ID']; ?>)" class="btn">Edit</button>
                            <button href="javascript:void(0)" onclick="layercms.webscrp.doDelete(<?php echo $product['product_ID']; ?>)" class="btn">Delete</button>
                        </td>
                    </tr>
                    <tr class="noHover">
                        <td colspan="5"><div class="showHide" id="edit_<?php echo $product['product_ID']; ?>"></div></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>

</article>