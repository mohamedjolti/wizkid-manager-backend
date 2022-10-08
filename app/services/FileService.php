<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;


class FileService
{

    /**
     * Get a validator for a contact.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function Upload($file)
    {
        //get the file name
        $filenameWithExt = $file->getClientOriginalName();

        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        //get extansion
        $extension = $file->getClientOriginalExtension();
        //get the name of the file
        $filenameToStore =date("Y_m_d_His") . '_' . $filename . '.' . $extension;
        // store the image
        $path = $file->storeAs('public/photos/', $filenameToStore);
    }

}
