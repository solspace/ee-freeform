<table>
    <tr>
        <th class="folder">
            <?php echo $path ?>

            <a class="btn action add-template" href="<?php echo $url ?>">
                Add an email template
            </a>
        </th>
    </tr>
    <?php foreach ($files as $file) : ?>
        <tr>
            <td>
                <?= $file ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<style>
    .folder {
        position: relative;
    }

    .folder:before {
        content: '\f07c';
        font-family: FontAwesome;
        color: #77bce6;
        font-weight: normal;
        margin-right: 5px;
    }

    a.add-template {
        position: absolute;
        right: 5px;
        top: 5px;

        display: inline-block;
        font-weight:normal;
    }

    a.add-template:before {
        content: '\f067';
        font-family: FontAwesome;
    }
</style>
