<?php

if (file_exists(PATH_THIRD . '/freeform_next/Library/Pro')) {
    return FREEFORM_PRO;
}

if (file_exists(PATH_THIRD . '/freeform_next/ft.freeform_next.php')) {
    return FREEFORM_LITE;
}

return FREEFORM_EXPRESS;
