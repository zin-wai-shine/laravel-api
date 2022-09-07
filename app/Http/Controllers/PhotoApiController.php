<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $photos = Photo::latest("id")->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "photos" => "required",
            "photos.*" => "file| mimes:jpeg, png, jpg",
            "product_id" => "required| exists:products,id"
        ]);

        if($request->hasFile("photos")){

            foreach($request->photos as $photo){
                $newName = $photo->store("public");
                Photo::create([
                    "name" => $newName,
                    "product_id" => $request->product_id,
                ]);
            }
            return response()->json(["message" => "Photo is created"]);
        }

        return response()->json(["message" => "Not Found"], 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
