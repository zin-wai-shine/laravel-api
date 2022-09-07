<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Photo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::latest("id")->paginate();
//        return response()->json($products);
        return ProductResource::collection($products);
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
           "name" => "required| min:3| max:20",
           "price" => "required| numeric| min:1| max:200000",
           "stock" => "required| numeric| min:1| max:200",
            "photos" => "required",
            "photos.*" => "file| mimes:jpeg,png,jpg"
        ]);
        $product = Product::create([
            "name" => $request->name,
            "price" => $request->price,
            "stock" => $request->stock,
            "user_id" => Auth::id()
        ]);

        $photos = [];
        if($request->hasFile("photos")){
            foreach ($request->photos as $key=>$photo){
                $newName = $photo->store("public/productImages");
                $photos[$key] = new Photo(["name"=>$newName]);
            }
            $product->photos()->saveMany($photos);
        }
        return response()->json(["message" => "product is created", $product]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::all()->find($id);
        if(is_null($product)){
            return response()->json(["message" => "Product Not Found"], 404);
        }
//        return response()->json($product);
        return new ProductResource($product);
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
        $product = Product::all()->find($id);

        if(is_null($product)){
            return response()->json(["message" => "Product Not Found"], 404);
        }
        $request->validate([
            "name" => "required| min:3| max:20",
            "price" => "required| min:1| numeric| max:2000000",
            "stock" => "required| min:1| numeric| max:100",
        ]);

        if($request->name){
            $product->name = $request->name;
        } elseif($request->price){
            $product->price = $request->price;
        } elseif($request->stock){
            $product->stock = $request->stock;
        }
        $product->update();
        return response()->json($product);

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::all()->find($id);

        if(is_null($product)){
            return response()->json(["message" => "Product Not Found"], 404);
        }
        $getPhoto= Photo::where("product_id", $product->id)->get();

        Storage::delete($getPhoto->map(fn($photo) => $photo->name)->toArray());
        $product->delete();

        return response()->json(["message"=>"Product is Deleted."],204);
    }
}
