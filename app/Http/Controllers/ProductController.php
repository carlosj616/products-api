<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $searchQuery = $request->input('search');

            $query->where('name', 'like', '%' . $searchQuery . '%')
                ->orWhere('description', 'like', '%' . $searchQuery . '%')
                ->orWhereHas('category', function ($categoryQuery) use ($searchQuery) {
                    $categoryQuery->where('name', 'like', '%' . $searchQuery . '%');
                })
                ->orWhere(function ($tagQuery) use ($searchQuery) {
                    $tagQuery->where('tags', 'like', '%' . $searchQuery . '%');
                });
        }

        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = $request->input('startDate') . ' 00:00:00';
            $endDate = $request->input('endDate') . ' 23:59:59';

            $query->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($query) use ($startDate, $endDate) {
                    $query->where('fecha_inicio', '>=', $startDate)
                        ->where('fecha_inicio', '<=', $endDate);
                })->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('fecha_fin', '>=', $startDate)
                        ->where('fecha_fin', '<=', $endDate);
                })->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('fecha_inicio', '<=', $startDate)
                        ->where('fecha_fin', '>=', $endDate);
                });
            });
        }

        $products = $query->with('category')->get();

        return response()->json($products);
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
        $product->tags = $request->tags;
        $product->fecha_inicio = $request->fechaInicio;
        $product->fecha_fin = $request->fechaFin;

        $product->save();

        return response()->json([
            'message' => 'Producto Guardado.'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('category')->find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Producto No Encontrado.'
            ], 404);
        }

        return response()->json([
            'product' => $product
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Producto No Encontrado.'
            ], 404);
        }

        $product->foto = $request->foto;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->category_id = $request->categoryId;
        $product->tags = $request->tags;
        $product->fecha_inicio = $request->fechaInicio;
        $product->fecha_fin = $request->fechaFin;

        $product->save();

        return response()->json([
            'message' => 'Producto Actualizado.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'message' => 'Producto No Encontrado.'
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Producto Eliminado.'
        ], 200);
    }

    public function generarReportePDF(Request $request)
    {
        $productosJson = $request->getContent();
        $productos = json_decode($productosJson, true);

        if ($productos === null) {
            return response()->json(['error' => 'Error al decodificar JSON'], 400);
        }

        $data = [
            'productos' => $productos
        ];

        $pdf = PDF::loadView('pdf.productsPdf', $data);
        return $pdf->download();
    }
}
