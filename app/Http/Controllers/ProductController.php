<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return Product::with('category')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $product = new Product();
        $product->foto = $request->foto;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->categoryId;

        $product->save();

        return response()->json([
            'message'=>'Producto Guardado.'
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('category')->find($id);
        if(!$product){
            return response()->json([
                'message' => 'Producto No Encontrado.'
            ],404);
        }

        return response()->json([
            'product' => $product
        ],200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'message' => 'Producto No Encontrado.'
            ],404);
        }
        
        $product->foto = $request->foto;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->categoryId;

        $product->save();

        return response()->json([
            'message'=>'Producto Actualizado.'
        ],200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'message' => 'Producto No Encontrado.'
            ],404);
        }

        $product->delete();

        return response()->json([
            'message'=>'Producto Eliminado.'
        ],200);
    }
}
