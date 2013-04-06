<article>

    <div id="addAlert" class="showHide"></div>

    <?php if (isset($message) && isset($alert)) { ?>
        <div class="alert <?php echo $alert ?>"><?php echo $message ?></div>
    <?php } ?>

    <form method="post" action="<?php echo BASE_PATH . '/categories/insert'; ?>">

        <input type="hidden" name="operation" id="operation" value="add">

        <table class="table3">
            <tr>
                <td class="tdleft"><label for="category_name">Name:</label></td>
                <td class="tdright"><input type="text" id="category_name" name="category_name" required value=""></td>
            </tr>
            <tr>
                <td class="tdleft"><label for="category_parent_ID">Parent:</label></td>
                <td class="tdright">
                    <select id="category_parent_ID" name="category_parent_ID">
                        <option value="0">-- None --</option>
                        <?php
                            foreach($categories as $category) echo "<option value=\"" . $category['category_ID'] . "\">" . $category['category_name'] . "</option>";
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" class="btn" value="Submit">
                </td>
            </tr>
        </table>

    </form>

</article>