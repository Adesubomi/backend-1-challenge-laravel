<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductBuyRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize("viewAny", Product::class);

        return response()->success(
            message: "List of all products",
            data: [
                "products" => Product::simplePaginate(50)
            ],
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function myProducts(): JsonResponse
    {
        $this->authorize("create", Product::class);

        /** @var User $user */
        $user = Auth::user();

        return response()->success(
            message: "List of all products",
            data: [
                "products" => Product::where('seller_id', $user->id)
                    ->simplePaginate(50)
            ],
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Product $product): JsonResponse
    {
        $this->authorize("view", $product);

        return response()->success(
            message: "Product Information",
            data: [
                "product" => $product
            ],
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function store(ProductStoreRequest $request, Product $product): JsonResponse
    {
        $this->authorize("create", Product::class);

        /** @var User $user */
        $user = Auth::user();
        $new_product = $product->newProduct($user, $request->validated());

        return response()->success(
            message: "Product has been created",
            data: [
                "product" => $new_product
            ],
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function update(ProductUpdateRequest $request, Product $product): JsonResponse
    {
        $this->authorize("update", $product);

        $is_updated = $product->updateProduct($request->validated());

        if (!$is_updated) {
            return response()->failed(
                message: "Unable to update product information",
                data: [],
            );
        }

        return response()->success(
            message: "Product has been updated",
            data: [
                "product" => $product->refresh()
            ],
        );
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize("delete", $product);

        $product->delete();
        return response()->success(
            message: "Product has been deleted",
        );
    }

    public function buyProduct(ProductBuyRequest $request)
    {

    }
}
