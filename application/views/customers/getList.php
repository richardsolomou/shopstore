<article>

    <h1><?php echo $pageTitle ?></h1>

    <div id="operationAlert" class="showHide"></div>

    <button href="javascript:void(0)" onclick="layercms.webscrp.getAddForm()" class="btn">Add New Customer</button>    
    <div id="message" class="showHide adminBar"></div>

    <table class="bordered hoverRed">
        <thead>
            <tr>
                <th>ID</th>
                <th class="lefted">Username</th>
                <th class="lefted">First Name</th>
                <th class="lefted">Last Name</th>
                <th class="operations">Operations</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($customers as $customer) {
            ?>
                    <tr class="centered">
                        <td><?php echo $customer['customer_ID']; ?></td>
                        <td class="lefted"><?php echo $customer['customer_username']; ?></td>
                        <td class="lefted"><?php echo $customer['customer_firstname']; ?></td>
                        <td class="lefted"><?php echo $customer['customer_lastname']; ?></td>
                        <td class="operations">
                            <button href="javascript:void(0)" onclick="layercms.webscrp.getEditForm(<?php echo $customer['customer_ID']; ?>)" class="btn">Edit</button>
                            <button href="javascript:void(0)" onclick="layercms.webscrp.doDelete(<?php echo $customer['customer_ID']; ?>)" class="btn">Delete</button>
                        </td>
                    </tr>
                    <tr class="noHover">
                        <td colspan="5"><div class="showHide" id="edit_<?php echo $customer['customer_ID']; ?>"></div></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>

</article>