<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Budget;
use  App\Models\OrderDetails;
use App\Http\Requests\ProductRequest;
use App\Services\Image;
use CLog;
use Illuminate\Support\Facades\DB;

class ProductController
{
    
    public function index(){
        return inertia('Product/Index');
    }
    
    public function get(Request $req){
        return response(
            Product::with(['rabbit:id,product_id,name'])
                ->select(['id', 'name', 'category', 'desc', 'price', 'stock', 'active', 'images'])
                ->whereNotNull('price')
                ->groupBy(['id', 'name', 'category', 'desc', 'price', 'stock', 'active', 'images'])
                ->paginate($req['page_size'] ?? 10)
        );
        // return response(
        //     Product::where(function ($q) use($req){
        //         $search = $req['search'];
        //         if ($search){
        //             $q->where('category', 'LIKE', $search.'%')
        //                 ->orWhere('name', 'LIKE', '%'. $search.'%');
        //         }
        //     })
        //     ->where('category', '!=' , 'Rabbit')
        //     ->paginate($req['page_size'] ?? 10)
        // );
    }

    public function getUser(Request $req){
        return response(
            Product::where('stock', '>', 0)
                ->where('active', 1)
                ->whereNotNull('price')
                ->select(['id', 'name', 'category', 'desc', 'price', 'stock', 'images'])
                ->groupBy(['id', 'name', 'category', 'desc', 'price', 'stock', 'images'])
                ->paginate($req['page_size'] ?? 10)
        );
    }

    public function getUserScroll(Request $req){
        if ($req['breed']){
            return response(
                Product::with(['rabbit:id,product_id,name,images'])
                    ->where('category',  'Rabbit')
                    ->where('name',  $req['breed'])
                    ->where('active', 1)
                    ->whereHas('rabbit', function($q){
                        $q->where('status', 'For Sale');
                    })
                    ->whereNotNull('price')
                    ->select(['id', 'desc', 'category', 'name', 'price', 'images'])
                    ->limit($req['limit'])
                    ->get()
            );
        }

        $p = Product::where('stock', '>', 0)
            ->where(function ($q) use($req){
                if (!$req['category']) {
                    $q->where('category', '!=', 'Rabbit')
                        ->orWhereHas('rabbit', function($q){
                            $q->where('status', 'For Sale');
                        });
                    return;
                }

                $q->where('category',  $req['category']);

                if ($req['category'] == 'Rabbit') {
                    $q->whereHas('rabbit', function($q){
                        $q->where('status', 'For Sale');
                    });
                }
            })
            ->where('active', 1)
            ->whereNotNull('price')
            ->select(['id', 'name', 'category', 'desc', 'price', 'stock', 'active', 'images'])
            ->groupBy(['id', 'name', 'category', 'desc', 'price', 'stock', 'active', 'images'])
            ->limit($req['limit'])
            ->get()
            ->toArray();

        $breeds = [];
        $np = [];
        foreach($p as $i => $val){
            if ($val['category'] != 'Rabbit') {
                array_push($np, $val);
                continue;
            }

            if (array_key_exists($val['name'], $breeds)) continue;

            $breeds[$val['name']] = 1;
            array_push($np, $val);
        }

        return response($np);
    }

    public function getRange(Request $req){
        $from = $this->getDate($req['from']);
        $until = $this->getDate($req['until']);

        return response(
            \App\Models\OrderDetails::with([
                    'order' => function ($q) use($from,$until){
                        $q->select(['id', 'user_id','created_at'])
                            ->where('orders.paid', 1)
                            ->where('orders.status', 'Delivered')
                            ->where(function($q) use($from,$until){
                                $q->whereDate('orders.created_at', '>=', $from)
                                    ->whereDate('orders.created_at', '<=', $until);
                            });
                    },
                    'order.user:id,name',
                    'product:id,name,category,price'
                ])
                ->whereHas('order', function($q) use($from,$until){
                    $q->where('orders.paid', 1)
                        ->where('orders.status', 'Delivered')
                        ->where(function($q) use($from,$until){
                            $q->whereDate('orders.created_at', '>=', $from)
                                ->whereDate('orders.created_at', '<=', $until);
                        });
                })
                ->get()
            // Product::with([
            //     'order_details' => function ($q) use($from,$until){
            //         $q->whereHas('order', function($q) use($from,$until){
            //             $q->where('orders.paid', 1)
            //                 ->where('orders.status', 'Delivered')
            //                 ->where(function($q) use($from,$until){
            //                     $q->whereDate('created_at', '>=', $from)
            //                         ->whereDate('created_at', '<=', $until);
            //                 });
            //         });
            //     },
            //     'order_details.order:id,user_id,created_at',
            //     'order_details.order.user:id,name'
            // ,])
            //     ->select(['id', 'name', 'category', 'price'])
            //     ->whereHas('order_details.order', function($q) use($from,$until){
            //         $q->where('orders.paid', 1)
            //             ->where('orders.status', 'Delivered')
            //             ->where(function($q) use($from,$until){
            //                 $q->whereDate('created_at', '>=', $from)
            //                     ->whereDate('created_at', '<=', $until);
            //             });
            //     })
            //     ->get()
        );
    }

    private function getDate($date){
        if (!$date) return substr(now(), 0, 10);

        return date('Y-m-d', strtotime(substr($date, 0, 10) . ' +1 day'));
    }

    public function rabbits(){
        return response(
            Product::select(['id', 'name'])
                ->where('category', 'Rabbit')
                ->groupBy('name', 'id')
                ->get()
        );
    }

    public function store(ProductRequest $req){
        if ($req['category'] == 'Rabbit'){
            Product::where('id', $req['product_id'])->update([
                'price' => $req['price']
            ]);
            CLog::create('Updated', 'Product');
            return;
        }

        $data = $req->validated();

        $images = $req->file('images');
        if ($images){
            $names = '';
            foreach($images as $image){
                $names .= '|' . Image::proccessDefName($image, 'images');
            }
            $data['images'] = substr($names, 1);
        }
        Product::create($data);

        CLog::create('Created', 'Product');
    }

    public function update(ProductRequest $req, $id){
        if ($req['category'] == 'Rabbit'){
            Product::where('id', $req['product_id'])->update([
                'price' => $req['price']
            ]);
            CLog::create('Updated', 'Product');
            return;
        }

        $data = $req->validated();

        $images = $req->file('images');
        if ($images){
            $p = Product::find($id);
            foreach(explode('|', $p->images ?? '') as $name){
                Image::remove('images', $name);
            }

            $names = '';
            foreach($images as $image){
                $names .= '|' . Image::proccessDefName($image, 'images');
            }
            $data['images'] = substr($names, 1);
        } else
            unset($data['images']);
            
        Product::where('id', $id)->update($data);

        CLog::create('Updated', 'Product');
    }

    public function delete($id){
        $data = Product::find($id);

        abort_if(!$data, 404);

        if ($data->category == 'Rabbit'){
            $data->update([
                'price' => null
            ]);
            CLog::create('Deleted', 'Product');
            return;
        }

        foreach(explode('|', $data->images ?? '') as $name){
            Image::remove('images', $name);
        }
        $data->delete();

        CLog::create('Deleted', 'Product');
    }

    public function getHighestSale(){

        // $data = DB::select("SELECT t1.id, t2.price,t2.category,t2.name,sum(t2.price * t1.qty) as 'total_sale' FROM `order_details` t1, products t2, orders t3  WHERE t3.paid = 1 and t3.id = t1.order_id and t1.product_id = t2.id group by id,category,'name', total_sale limit 5");
        $data = Product::select(['name','category','price'])
            ->whereHas('order_details.order', function($q){
                $q->where('status', 'Delivered')
                    ->where('paid', 1);
            })
            ->groupBy('name','category','price')
            ->get();
        
        foreach ($data as $key => $value) {
            $name = $value->name;
            $qty = OrderDetails::whereHas('order', function($q) use($name){
                $q->where('status', 'Delivered')
                    ->where('paid', 1);
            })
            ->whereHas('product', function($q) use($name){
                $q->where('name', $name);
            })
            ->sum('qty');
            $value->total_sale = (int) $value->price * $qty;
        }
        
        return response($data);
    }

    public function getNewProduct(){
        $data = Product::whereDate('created_at',now())->whereNotNull('price')->get();
        return response($data);
    }

}
