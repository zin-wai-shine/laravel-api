<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public function stockManage($count){
        $status = "";
        if($count > 10){
            return $status = "Avaliable Product";
        }elseif($count > 0){
            return $status = "Few Product";
        }elseif($count === 0){
            return $status = "Product Off";
        }
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            "name" => $this->name,
            "price" => $this->price,
            "stock" => $this->stock."mmk",
            "stock_status" => $this->stockManage($this->stock),
            "owner" => new UserResource($this->user),
            "photos" => PhotoResource::collection($this->photos)
        ];
    }
}
