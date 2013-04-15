<article>

    <h1><?php echo $pageTitle ?></h1>

    <div id="operationAlert" class="showHide"></div>

    <button href="javascript:void(0)" onclick="layercms.webscrp.getAddForm()" class="btn">Add New Currency</button>    
    <div id="message" class="showHide adminBar"></div>

    <table class="bordered hoverRed">
        <thead>
            <tr>
                <th>ID</th>
                <th class="lefted">Name</th>
                <th class="lefted">Code</th>
                <th class="lefted">Symbol</th>
                <th class="operations">Operations</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($currencies as $currency) {
            ?>
                    <tr class="centered">
                        <td><?php echo $currency['currency_ID']; ?></td>
                        <td class="lefted"><?php echo $currency['currency_name']; ?></td>
                        <td class="lefted"><?php echo $currency['currency_code']; ?></td>
                        <td class="lefted"><?php echo $currency['currency_symbol']; ?></td>
                        <td class="operations">
                            <button href="javascript:void(0)" onclick="layercms.webscrp.getEditForm(<?php echo $currency['currency_ID']; ?>)" class="btn">Edit</button>
                            <button href="javascript:void(0)" onclick="layercms.webscrp.doDelete(<?php echo $currency['currency_ID']; ?>)" class="btn">Delete</button>
                        </td>
                    </tr>
                    <tr class="noHover">
                        <td colspan="5"><div class="showHide" id="edit_<?php echo $currency['currency_ID']; ?>"></div></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>

</article>