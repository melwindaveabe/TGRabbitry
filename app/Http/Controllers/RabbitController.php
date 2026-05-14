<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rabbit;
use App\Models\Product;
use App\Http\Requests\RabbitRequest;
use App\Services\Image;
use CLog;

class RabbitController 
{

    private $RFIDS = [
        '0006371463' => 'Californian',
        '0002971522' => 'Lion Head',
        '0006350105' => 'New Zealand',
        '0000096077' => 'Teddy Rabbit'

        
    ];

    public function index() {
        return inertia('Rabbit/Index');
    }
    public function breed() {
        return inertia('Rabbit/Breed');
    }
    public function weight(){
        return inertia('Rabbit/Weight');
    }
    

    public function dropdown(Request $req){
        return response(
            Rabbit::select(['id', 'name', 'product_id'])
                ->where(function($q) use($req){
                    if ($req['for_product'])
                        $q->where('status', 'For Sale');
                    
                    if ($req['breed']){
                        $q->whereHas('product', function($q) use($req){
                            $q->where('name', $req['breed']);
                            if ($req['for_product']){
                                $q->whereNull('price');
                            }
                        });
                    }
                })
                ->get()
        );
    }

    public function get(Request $req){
        if (!$req['rfid'])
            return response(
                Rabbit::with(['product:id,name', 'latest_weight', 'illness:illness,id,rabbit_id'])
                    ->paginate($req['page_size'] ?? 10)
            );

        if (!array_key_exists($req['rfid'], $this->RFIDS)){
            return response(
                Rabbit::with(['product:id,name', 'latest_weight', 'illness:illness,id,rabbit_id'])
                    ->where('rfid', $req['rfid'])
                    ->paginate($req['page_size'] ?? 10)
            );
        }

        return response(
            Rabbit::with(['product:id,name', 'latest_weight', 'illness:illness,id,rabbit_id'])
                ->whereHas('product', function($q) use($req){
                    $q->where('name', $this->RFIDS[$req['rfid']]);
                })
                ->paginate($req['page_size'] ?? 10)
        );
    }

    public function breeds(Request $req){
        return response(
            Product::where('category', 'Rabbit')->paginate($req['page_size'] ?? 10)
        );
    }

    public function getBucks(){
        return response(
            Rabbit::select(['id', 'name'])->where('gender', 'Buck')->get()
        );
    }

    public function getDoes(){
        return response(
            Rabbit::select(['id', 'name'])->where('gender', 'Doe')->get()
        );
    }

    public function store(RabbitRequest $req){
        $data = $req->validated();
        $product = Product::create([
            'name' => $req['breed'],
            'category' => 'Rabbit',
            'stock' => '1',
        ]);
        $data['images']= Image::proccessDefName($req->file('images'), 'images');
        $data['birth'] = date('Y-m-d', strtotime(substr($data['birth'], 0, 10) . ' +1 day'));
        $data['product_id'] = $product->id;

        $rabbit = Rabbit::create($data);

        if ($req['illness'] && $req['date']){
            \App\Models\Illness::create([
                'rabbit_id' => $rabbit->id,
                'illness' => $req['illness'],
                'date' => $req['date'],
            ]);
        }

        // if ($req['status'] == 'For Sale') {
        //     $i = Product::select(['id', 'stock'])->where('id', $req['product_id'])->first();
        //     $i->update([
        //         'stock' => ((int) $i->stock) + 1
        //     ]);
        // }

        CLog::create('Created', 'Rabbit');
    }

    public function update(RabbitRequest $req, $id){
        $v = Rabbit::select(['id', 'product_id', 'images', 'birth'])->where('id', $id)->first();
        // $v = Rabbit::select(['id', 'status', 'sold', 'images', 'birth'])->where('id', $id)->first();
        abort_if(!$v, 404);

        Product::where('id', $v->product_id)->update([
            'name' => $req['breed'],
        ]);

        $data = $req->validated();
        $images = $req->file('images');
        if ($images){
            Image::remove('images', $v->images);
            $data['images'] = Image::proccessDefName($images, 'images');
        } 
        else
            unset($req['images']);

        if($v->birth != $data['birth'])
            $data['birth'] = date('Y-m-d', strtotime(substr($data['birth'], 0, 10) . ' +1 day'));

        // if ($v->sold == 0 && $req['status'] == 'For Sale' && $v->status != 'For Sale') {
        //     $i = Product::select(['id', 'stock'])->where('id', $req['product_id'])->first();
        //     $i->update([
        //         'stock' => ((int) $i->stock) + 1
        //     ]);
        // }
        // else if ($v->sold == 0 && $req['status'] != 'For Sale' && $v->status == 'For Sale') {
        //     $i = Product::select(['id', 'stock'])->where('id', $req['product_id'])->first();
        //     $i->update([
        //         'stock' => ((int) $i->stock) - 1
        //     ]);
        // }
        
        $v->update($data);

        if ($req['illness'] && $req['date']){
            \App\Models\Illness::create([
                'rabbit_id' => $v->id,
                'illness' => $req['illness'],
                'date' => $req['date'],
            ]);
        }

        CLog::create('Updated', 'Rabbit');
    }

    public function delete($id){
        $data = Rabbit::select(['id', 'images', 'product_id'])->where('id', $id)->first();
        // $v = Rabbit::select(['id', 'status', 'sold', 'product_id'])->where('id', $id)->first();
        abort_if(!$data, 404);

        foreach(explode('|', $data->images ?? '') as $name){
            Image::remove('images', $name);
        }
        $data->delete();

        Product::where('id', $data->product_id)->delete();

        // if ($v->sold == 0 && $v->status == 'For Sale') {
        //     $i = Product::select(['id', 'stock'])->where('id', $v->product_id)->first();
        //     $i->update([
        //         'stock' => ((int) $i->stock) - 1
        //     ]);
        // }

        // $v->delete();

        CLog::create('Deleted', 'Rabbit');
    }
}
