<?php

namespace pms\program\kits;

use pms\OptionsAccess;

class KitsResource extends OptionsAccess
{

    /**
     * @param string     $name
     * @param mixed|null $default
     * @return mixed
     */
    protected function get(string $name, mixed $default = null): mixed
    {
        if (!isset($this->data[$name])) {
            $data = $default;
        }else{
            $data = &$this->data[$name];
        }
        if (is_array($data)) {
            $data = (new self())->restore($data);
        }
        return $data;
    }

    protected function restore(array $data): static
    {
        $this->data = $data;
        return $this;
    }


}