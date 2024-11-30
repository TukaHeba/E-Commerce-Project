<?php
namespace App\Service\Product;

use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product\Product;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ProductService{
    public function getProducts(Request $request){
        try{
            $products = Product::with('category')->paginate(5);
            return $products ;
        }catch(AccessDeniedHttpException){
            throw new AccessDeniedHttpException();
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }
    public function storeProduct($data){
        try{
            return Product::create($data);
        }catch(ModelNotFoundException){
            throw new ModelNotFoundException();
        }catch(Exception){
            throw new Exception();
        }catch(AccessDeniedHttpException){
            throw new AccessDeniedHttpException();
        }catch(Exception){
            throw new Exception();
        }
    }
    public function updateProduct($product,$data){
        try{
            $product->update($data);
            $product->save();
            return $product ;
        }catch(ModelNotFoundException){
            throw new ModelNotFoundException();
        }catch(Exception){
            throw new Exception();
        }
    }

}
