<?php

namespace Solspace\Addons\FreeformNext\Utilities\ControlPanel\Navigation;

/** Adds the default shortcut on installation, leaving upgrades administrator-controlled. */
class NavigationInstaller
{
    public function install(): void
    {
        $session = ee()->session ?? null;
        $roleIds = array_unique([1, (int) ($session ? $session->userdata('role_id') : 1)]);
        $settings = ee('Model')->get('RoleSetting')->filter('role_id', 'IN', $roleIds)->all();
        $handledSets = [];
        $newSet = null;

        foreach ($settings as $setting) {
            $set = $setting->menu_set_id
                ? ee('Model')->get('MenuSet', $setting->menu_set_id)->first()
                : null;

            if (!$set) {
                if (!$newSet) {
                    $name = 'Freeform';
                    $suffix = 2;
                    while (ee('Model')->get('MenuSet')->filter('name', $name)->count()) {
                        $name = 'Freeform ' . $suffix++;
                    }
                    $newSet = ee('Model')->make('MenuSet', ['name' => $name]);
                    $newSet->save();
                }

                $set = $newSet;
                $setting->menu_set_id = $set->getId();
                $setting->save();
            }

            if (isset($handledSets[$set->getId()])) {
                continue;
            }
            $handledSets[$set->getId()] = true;

            $sort = 0;
            $hasFreeform = false;
            foreach ($set->Items as $item) {
                $sort = max($sort, (int) $item->sort);
                if ($item->parent_id) {
                    continue;
                }
                if (($item->type === 'addon' && in_array(strtolower($item->data), ['freeform_next', 'freeform_next_ext'], true))
                    || ($item->type === 'link' && preg_match('~(?:^|/)addons/settings/freeform_next(?:/|[?&]|$)~', $item->data))) {
                    $hasFreeform = true;
                }
            }

            if (!$hasFreeform) {
                ee('Model')->make('MenuItem', [
                    'set_id' => $set->getId(),
                    'parent_id' => 0,
                    'type' => 'addon',
                    'name' => 'Freeform',
                    'data' => 'Freeform_next_ext',
                    'sort' => $sort + 1,
                ])->save();
            }
        }
    }
}
