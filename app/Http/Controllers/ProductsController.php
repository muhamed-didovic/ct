<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $products = json_decode(File::get(public_path('products.json'))) ?? [];
//
//        $products = collect($products)->sortByDesc('submitted');
//
//        $total = $products->sum('total');
        
        return view('products.index');//compact('products', 'total')
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $products = json_decode(File::get(public_path('products.json'))) ?? [];
        
        $products = collect($products)->sortByDesc('submitted');
        
        //dd($products);
        
        $total = $products->sum('total');
        
        return response()->json(['products' => $products, 'total' => $total]);
        //return view('products.create', compact('products', 'total'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        //dd(request('number'));
        $products = json_decode(File::get(public_path('products.json'))) ?? [];
        
        //if there is index, remove that product and add as new one
        if ($index = request('number')) {
            $products = collect($products)->forget($index);
        }
        $data = collect($products)->push(
            [
                'name'      => request('name'),
                'quantity'  => request('quantity'),
                'price'     => request('price'),
                'submitted' => time(),
                'total'     => request('quantity') * request('price'),
            ]
        )
            //->sortByDesc('submitted');
        ;
        
        
        //dd($data);
        
        $fileName = 'products.json';
        File::put(public_path($fileName), $data->toJson());
        
        //$products = json_decode(File::get(public_path('products.json')))?? [];
        $products = $data->sortByDesc('submitted');
        
        $total = $products->sum('total');
        
        return response()->json(['products' => $products, 'total' => $total]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Product             $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
