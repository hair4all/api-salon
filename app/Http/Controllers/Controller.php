<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
abstract class Controller
{
    //
    public function handleRequest(Request $request)
    {
        $data = $request->all();
        // foreach ($data as $key => $value) {
        //     /*
        //     if (is_string($value)) {
        //         $data[$key] = $this->sanitizeInput($value);
        //     }
        //     if (in_array($key, ['start_date', 'end_date']) && strtotime($value)) {
        //         $data[$key] = date(DATE_ISO8601, strtotime($value));
        //     }
        //     */
        //     if ($key === 'image' ) {
        //         //$fileName = 'blob_' . time() . '.png'; // Adjust the file extension as needed
        //         //Storage::put('public/files/' . $fileName, base64_decode($value));
        //         //dd($value);
        //         if($this->isBlobString($value)){
        //         $fileName = $this->saveBase64File($value,"files" );
        //         $data['image'] = $fileName;
        //         }
        //         else if($value){
        //             unset($data['image']);
        //         }
        //     }
           
        // }
        return $data;
    }
}
