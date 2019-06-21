<?php

trait IsNotWhiteSpaceOnly {
    public function isNotWhiteSpaceOnly ($data, $key)
    {
        return isset($data[$key]) && strlen(trim(strip_tags(nl2br(str_replace('&nbsp;', '', $data[$key]))))) > 0;
    }
}