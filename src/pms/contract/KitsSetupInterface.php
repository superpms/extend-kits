<?php

namespace pms\contract;

interface KitsSetupInterface
{
    public static function install(string $app):bool;
}