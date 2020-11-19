<?php

namespace Bitkit\Core\Entities;

/**
 * Class File
 * @package Bitkit\Core\Entities
 */
class File
{
    /**
     * @param string $file
     * @return bool
     */
    public function fileDelete(string $file ) : bool
    {
        if (file_exists($_SERVER["DOCUMENT_ROOT"].$file)) { //delete file
            if (unlink($_SERVER["DOCUMENT_ROOT"].$file)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $file
     * @return string
     */
    public function getFileExtension($file)
    {
        $ext = '.'.array_pop(explode('.',$file));
        return $ext;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getFileName(string $file)
    {
        $name_parts = explode('.',$file);
        array_pop($name_parts);
        return implode('.',$name_parts);
    }

    /**
     * @param $file
     * @return mixed
     */
    public function getFilePureName($file)
    {
        $name_parts = explode('/',$this->getFileName($file));
        $name = array_pop($name_parts);
        return $name;
    }



}