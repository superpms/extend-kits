<?php
if (class_exists('\pms\hook\LifecycleHook')) {
    \pms\hook\LifecycleHook::mount(LIFECYCLE_BOOT, \pms\extend\kits\Setup::class);
}

