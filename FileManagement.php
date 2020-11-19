<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Models\File;

trait FileManagement
{
    public function files()
    {
        return $this->morphMany(File::class, 'file')->orderBy('position', 'ASC');
    }

    public function fileUpload($requestFile, $column)
    {
        if ($requestFile !== null) {
            $this->removeFile($column);
            $originalFilename = $requestFile->getClientOriginalName();
            $extension = $requestFile->getClientOriginalExtension();
            $newFilename = Str::slug(fileName($originalFilename));
            $filename = time().'-'.$newFilename.'.'.$extension;
            if ($requestFile->move(storage_path('app/files'), $filename)) {
                $file = File::create([
                    'file_id' => $this->id,
                    'file_type' => get_class($this),
                    'name' => $filename,
                    'display_name' => $originalFilename
                ]);
                $this->update([$column => $file->id]);
            }
        }
    }

    public function removeFile($column)
    {
        if ($this->{$column} && $file = File::find($this->{$column})) {
            $file->destroyFile();
        }
    }
}
