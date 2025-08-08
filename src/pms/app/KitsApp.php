<?php

namespace pms\app;

use pms\facade\Path;

trait KitsApp{

    final protected static function path($suffix = null): string{
        if(!empty($suffix) && !str_starts_with($suffix,"/")){
            $suffix = "/".$suffix;
        }
        $name = explode("\\",get_called_class());
        $name = array_slice($name,1,2);
        $name = implode("\\",$name);
        return Path::getKitsRoot($name,$suffix);
    }

}