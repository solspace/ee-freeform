<?php
/** @var \Solspace\Addons\FreeformNext\Library\Codepack\Codepack $codepack */
?>
<table>
    <tr>
        <th data-prefix class="folder">
            <?php echo $prefix ?>
        </th>
    </tr>
    <?php foreach ($codepack->getTemplates()->getContents() as $file) : ?>
        <tr>
            <td>
                <?= $file->getName() ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<style>
    .folder:before {
        content: '\f07c';
        font-family: 'Font Awesome 5 Free';
        color: #77bce6;
        font-weight: normal;
        margin-right: 5px;
    }
</style>


<script>

</script>
