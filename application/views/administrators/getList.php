<article>

    <h1><?php echo $pageTitle ?></h1>

    <div id="operationAlert" class="showHide"></div>

    <button href="javascript:void(0)" onclick="layercms.webscrp.getAddForm()" class="btn">Add New Administrator</button>    
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
                foreach ($administrators as $administrator) {
            ?>
                    <tr class="centered">
                        <td><?php echo $administrator['admin_ID']; ?></td>
                        <td class="lefted"><?php echo $administrator['admin_username']; ?></td>
                        <td class="lefted"><?php echo $administrator['admin_firstname']; ?></td>
                        <td class="lefted"><?php echo $administrator['admin_lastname']; ?></td>
                        <td class="operations">
                            <button href="javascript:void(0)" onclick="layercms.webscrp.getEditForm(<?php echo $administrator['admin_ID']; ?>)" class="btn">Edit</button>
                            <button href="javascript:void(0)" onclick="layercms.webscrp.doDelete(<?php echo $administrator['admin_ID']; ?>)" class="btn">Delete</button>
                        </td>
                    </tr>
                    <tr class="noHover">
                        <td colspan="5"><div class="showHide" id="edit_<?php echo $administrator['admin_ID']; ?>"></div></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>

</article>