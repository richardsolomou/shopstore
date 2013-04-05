<article>

    <div id="editAlert_<?php echo $category_ID ?>" class="showHide"></div>

    <hr class="nomargin nopadding">

    <form method="post" action="<?php echo BASE_PATH . '/categories/update'; ?>">

        <input type="hidden" id="operation" name="operation" value="edit">
        <input type="hidden" name="category_ID" id="category_ID" value="<?php echo $category_ID ?>">

        <table class="table3">
            <tr>
                <td>
                    <table class="table3">
                        <tr>
                            <td class="tdleft width25"><label for="category_name">Category Name:</label></td>
                            <td class="tdright"><input type="text" id="category_name" name="category_name" required value="<?php echo $category['category_name']; ?>"></td>
                        </tr>
                        <tr>
                            <td class="tdleft width25"><label for="category_parent_ID">Category Parent:</label></td>
                            <td class="tdright">
                                <select name="category_parent_ID" id="category_parent_ID">
                                    <option value="0">-- None --</option>
                                    <?php
                                        foreach($categories as $category1) {
                                            if ($category1['category_ID'] == $category['category_parent_ID']) {
                                                echo "<option value=\"" . $category1['category_ID'] . "\" selected=\"selected\">" . $category1['category_name'] . "</option>";
                                            } else {
                                                echo "<option value=\"" . $category1['category_ID'] . "\">" . $category1['category_name'] . "</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </td>
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

</article>