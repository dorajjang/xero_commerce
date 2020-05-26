<?php

namespace Xpressengine\Plugins\XeroCommerce\Controllers\Settings;

use Illuminate\Support\Facades\Auth;
use XePresenter;
use XeFrontend;
use App\Http\Controllers\Controller;
use Xpressengine\Http\Request;
use Xpressengine\Plugins\XeroCommerce\Models\Badge;
use Xpressengine\Plugins\XeroCommerce\Models\Label;
use Xpressengine\Plugins\XeroCommerce\Models\Product;
use Xpressengine\Plugins\XeroCommerce\Models\ProductCustomOption;
use Xpressengine\Plugins\XeroCommerce\Models\Products\BasicProduct;
use Xpressengine\Plugins\XeroCommerce\Models\Shop;
use Xpressengine\Plugins\XeroCommerce\Plugin\ValidateManager;
use Xpressengine\Plugins\XeroCommerce\Services\ProductCategoryService;
use Xpressengine\Plugins\XeroCommerce\Services\ProductManager;
use Xpressengine\Plugins\XeroCommerce\Services\ProductSettingService;

class ProductController extends SettingBaseController
{
    /** @var ProductManager $productManager */
    protected $productManager;

    /** @var ProductSettingService $productSettingService */
    protected $productSettingService;

    public function __construct()
    {
        parent::__construct();
        $this->productManager = new ProductManager();
        $this->productSettingService = new ProductSettingService();
    }

    public function index(Request $request)
    {
        $displayStates = Product::getDisplayStates();
        $dealStates = Product::getDealStates();
        $products = $this->productSettingService->getProducts($request);
        $types = Product::getSingleTableNameMap();

        return XePresenter::make(
            'product.index',
            compact('displayStates', 'dealStates', 'products', 'types')
        );
    }

    public function show(Request $request, $productId)
    {
        $product = $this->productSettingService->getProduct($productId);
        $options = $this->productSettingService->getProductOptionArrays($product);
        $variants = $this->productSettingService->getProductVariantArrays($product);
        $customOptionTypes = ProductCustomOption::getSingleTableNameMap();
        $customOptions = $this->productSettingService->getProductCustomOptionArrays($product);

        return XePresenter::make('product.show', compact('product', 'options', 'variants', 'customOptionTypes', 'customOptions'));
    }

    public function create(Request $request, ProductCategoryService $productCategoryService)
    {
        $labels = Label::get();
        $badges = Badge::get();
        $shops = Shop::whereHas('users', function ($query) {
            $query->where('user.id', Auth::id());
        })->get();

        $categoryItems = $productCategoryService->getCategoryItems();

        $type = $request->get('type', BasicProduct::$singleTableType);

        $customOptionTypes = ProductCustomOption::getSingleTableNameMap();

        $productClass = Product::getSingleTableTypeMap()[$type];

        XeFrontend::rule('product', ValidateManager::getProductValidateRules());

        return XePresenter::make('product.create', compact('productClass', 'labels', 'badges', 'categoryItems', 'shops', 'type', 'customOptionTypes'));
    }

    public function store(Request $request)
    {
        $productId = $this->productManager->store($request);

        return redirect()->route('xero_commerce::setting.product.show', ['productId' => $productId]);
    }

    public function tempStore(Request $request)
    {
        $productId = $this->productManager->temp($request);

        return redirect()->route('xero_commerce::setting.product.edit', ['productId' =>$productId]);
    }

    public function edit(Request $request, $productId, ProductCategoryService $productCategoryService)
    {
        $product = $this->productSettingService->getProduct($productId);
        $categoryItems = $productCategoryService->getCategoryItems();
        $productCategorys = $productCategoryService->getProductCategory($productId);
        $options = $this->productSettingService->getProductOptionArrays($product);
        $variants = $this->productSettingService->getProductVariantArrays($product);
        $customOptionTypes = ProductCustomOption::getSingleTableNameMap();
        $customOptions = $this->productSettingService->getProductCustomOptionArrays($product);

        $productLabelIds = [];
        foreach ($product->labels as $label) {
            $productLabelIds[] = $label->id;
        }

        $labels = Label::get();
        $badges = Badge::get();

        XeFrontend::rule('product', ValidateManager::getProductValidateRules());

        return XePresenter::make('product.edit', compact('product', 'productLabelIds', 'labels', 'badges', 'categoryItems', 'productCategorys', 'options', 'variants', 'customOptionTypes', 'customOptions'));
    }

    public function update(Request $request, $productId)
    {
        $this->productManager->update($request, $productId);

        return redirect()->route('xero_commerce::setting.product.show', ['productId' => $productId]);
    }

    public function remove(Request $request, $productId)
    {
        $this->productManager->remove($productId);

        return redirect()->route('xero_commerce::setting.product.index');
    }

    public function getChildCategory(Request $request, ProductCategoryService $categoryService)
    {
        $parentId = $request->get('parentId');

        $childCategory = $categoryService->getChildCategory($parentId);

        return XePresenter::makeApi(['type' => 'success', 'categories' => $childCategory]);
    }

    // 번들상품 검색을 위한 API
    public function search(Request $request)
    {
        $products = $this->productSettingService->getProducts($request);

        $products->load(['variants', 'customOptions']);

        return XePresenter::makeApi(['type' => 'success', 'products' => $products]);
    }

}
