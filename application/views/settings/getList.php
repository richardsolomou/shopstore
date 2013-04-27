<article>

    <h1><?php echo $pageTitle ?></h1>

    <div id="operationAlert" class="showHide"></div>

    <div id="message" class="showHide adminBar"></div>
    
    <p><a href="<?php echo BASE_PATH . '/settings/resetDatabase'; ?>" class="btn">Reset Database</a></p>

    <table class="bordered hoverRed">
        <thead>
            <tr>
                <th>ID</th>
                <th class="lefted">Column</th>
                <th class="lefted">Value</th>
                <th class="operations">Operations</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($settings as $setting) {
            ?>
                    <tr class="centered">
                        <td><?php echo $setting['setting_ID']; ?></td>
                        <td class="lefted"><?php echo $setting['setting_column']; ?></td>
                        <td class="lefted"><?php echo $setting['setting_value']; ?></td>
                        <td class="operations">
                            <button href="javascript:void(0)" onclick="layercms.webscrp.getEditForm(<?php echo $setting['setting_ID']; ?>)" class="btn">Edit</button>
                        </td>
                    </tr>
                    <tr class="noHover">
                        <td colspan="4"><div class="showHide" id="edit_<?php echo $setting['setting_ID']; ?>"></div></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>


</article>