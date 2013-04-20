<article>

    <h1><?php echo $pageTitle ?></h1>

    <div id="operationAlert" class="showHide"></div>

    <button href="javascript:void(0)" onclick="layercms.webscrp.getAddForm()" class="btn">Add New Review</button>    
    <div id="message" class="showHide adminBar"></div>
        
    <table class="bordered hoverRed">
        <thead>
            <tr>
                <th>ID</th>
                <th class="lefted">Product</th>
                <th class="lefted">Subject</th>
                <th class="lefted">Customer</th>
                <th class="operations">Operations</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($reviews as $review) {
                    foreach($products as $product) if ($review['product_ID'] == $product['product_ID']) $productName = $product['product_name'];
                    foreach($customers as $customer) if ($review['customer_ID'] == $customer['customer_ID']) $customerName = $customer['customer_firstname'] . ' ' . $customer['customer_lastname'];
            ?>
                    <tr class="centered">
                        <td><?php echo $review['review_ID']; ?></td>
                        <td class="lefted"><?php echo $productName; ?></td>
                        <td class="lefted"><?php echo $review['review_subject']; ?></td>
                        <td class="lefted"><?php echo $customerName; ?></td>
                        <td class="operations">
                            <button href="javascript:void(0)" onclick="layercms.webscrp.getEditForm(<?php echo $review['review_ID']; ?>)" class="btn">Edit</button>
                            <button href="javascript:void(0)" onclick="layercms.webscrp.doDelete(<?php echo $review['review_ID']; ?>)" class="btn">Delete</button>
                        </td>
                    </tr>
                    <tr class="noHover">
                        <td colspan="5"><div class="showHide" id="edit_<?php echo $review['review_ID']; ?>"></div></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>

</article>