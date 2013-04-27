<div id="addAlert" class="showHide"></div>

<form id="formInsert" method="post" onsubmit="layercms.webscrp.doAdd('categories', 'formInsert');return false;">

    <input type="hidden" name="operation" id="operation" value="add">

    <table class="table3">
        <tr>
            <td>
                <table class="table3">
                    <tr>
                        <td><label for="category_name">Name:</label></td>
                        <td><input type="text" id="category_name" name="category_name" required value=""></td>
                    </tr>
                    <tr>
                        <td><label for="category_parent_ID">Parent:</label></td>
                        <td>
                            <select id="category_parent_ID" name="category_parent_ID">
                                <option value="0">-- None --</option>
                                <?php
                                    foreach ($categories as $category) echo '<option value="' . $category['category_ID'] . '">' . $category['category_name'] . '</option>';
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