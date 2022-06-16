<?php

namespace codewild\csubmboer\core;

abstract class DbFile extends db\DbModel
{

    public string $name = '';
    public string $path = '';
    public string $type = '';
    public int $size = 0;

    public static function attributes(): array
    {
        $output = parent::attributes();
        array_push($output, 'name', 'path', 'type', 'size');
        return $output;
    }

    public function upload(string $key, string $dir)
    {
        $file = $_FILES[$key];

        if ($file['error']){
            $code = $file['error'];
            $message = $this->phpFileUploadErrors()[$code];
            $this->errors[$key][] = $message;
            return false;
        }

        // Will load type and size, enough to validate
        $this->loadData($file);

        if ($this->validate()) {
            $basename = basename($file['name']);
            $explode = explode('.', $basename);
            array_shift($explode);
            $ext = strtolower(implode('.', $explode));

            $filename = uniqid('', false) . '.' . $ext;
            $filepath = $dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $filepath)) {
                $this->name = $basename;
                $this->path = $filepath;
                if ($this->save()) {
                    return true;
                }
            }
        }
        return false;
    }

    public function delete(){
        $fullPath = $_SERVER['DOCUMENT_ROOT'].$this->path;
        if(!unlink($fullPath)){
            return false;
        };
        return parent::delete();
    }

    public function phpFileUploadErrors()
    {
        return array(
            0 => 'There is no error, the file uploaded with success',
            1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk.',
            8 => 'A PHP extension stopped the file upload.',
        );
    }
}
