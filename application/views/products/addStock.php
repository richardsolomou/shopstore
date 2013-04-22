<div id="editAlert_<?php echo $product_ID; ?>" class="showHide"></div>

<hr class="nomargin nopadding">

<form id="formUpdate_<?php echo $product_ID; ?>" method="post" onsubmit="layercms.webscrp.addStock(<?php echo $product_ID; ?>, 'formUpdate_<?php echo $product_ID; ?>', null, '<?php echo BASE_PATH . '/products/addStock/' . $product['product_ID']; ?>');return false;">

    <input type="hidden" id="operation" name="operation" value="edit">
    <input type="hidden" id="product_ID" name="product_ID" value="<?php echo $product_ID; ?>">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td colspan="2">Current Stock: <?php echo $product['product_stock']; ?></td>
                    </tr>
                    <tr>
                        <td class="tdleft"><label for="product_stock">Stock:</label></td>
                        <td class="tdright"><input type="text" id="product_stock" name="product_stock" required value="" pattern="[0-9]+"></td>
                    </tr>
                </table>
            </td>
            <td>
                <input type="submit" class="btn" value="Submit">
            </td>
        </tr>
    </table>
    
</form>

<hr class="nopadding">