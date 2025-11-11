<?php

use pms\extend\kits\Setup;
use pms\hook\LifecycleHook;

if (class_exists('\pms\hook\LifecycleHook')) {
    LifecycleHook::mount(LIFECYCLE_BOOT, Setup::class);
}

