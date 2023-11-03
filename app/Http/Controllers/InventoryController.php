<?php

namespace App\Http\Controllers;

use App\Http\Helper;
use App\Services\Rs;
use App\Models\Inventory;
use App\Models\PosInventory;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{   
    use Helper;

    protected $rs;

    public function __construct(Rs $rs)
    {
    	$this->rs = $rs;
    }

    public function index(Request $request)
    {   
        $searchJson = $request->input('search');
        $search = json_decode($searchJson);
        // $search = $request->search;

        $inventory = Inventory::select('inventories.id', 'pos_inventories.unit', 'pos_inventories.gudang', 'gudangs.nomor_batch', 'gudangs.harga_jual_satuan', 'gudangs.tanggal_ed', 'items.name as item', 'items.kode as item_kode', 'sediaans.name as sediaan', 'inventories.stok')
        ->join('pos_inventories', 'inventories.pos_inventory', '=', 'pos_inventories.id')
        ->join('gudangs', 'inventories.gudang', '=', 'gudangs.id')
        ->join('items', 'gudangs.item', '=', 'items.id')
        ->join('sediaans', 'items.sediaan', '=', 'sediaans.id')
        ->when($search, function ($query) use ($search) {
            foreach ($search as $key => $value) {
                if ($key === 'gudang') {
                    $query->where('pos_inventories.'.$key, 'LIKE', '%' . $value . '%');
                } else {
                    $query->where($key, 'LIKE', '%' . $value . '%');
                }
            }
        })
        ->orderBy('inventories.created_at', 'asc')
        ->cursorPaginate($request->input('per_page', 15));

        foreach ($inventory as $data) {
            $unit =  $this->rs->showUnit($request->bearerToken(), $data->unit);
            $data->unit = $unit->name;
        }        

        return $this->responseFormatterWithMeta($this->httpCode['StatusOK'],$this->httpMessage['StatusOK'], $inventory);
    }

    public function updateByUnit(Request $request)
    {
        list($posInventory, $err) = $this->getPosInventory($request);
        if ($err != null) return $err;

        $validator = Validator::make($request->all(), [
            'detail' => 'required|array',
            'detail.*.nomor_batch' => ['required', Rule::exists( 'inventories', 'nomor_batch')->where('pos_inventory', $posInventory->id)->whereNull('deleted_at')],
            'detail.*.jumlah' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $inventory = [];

        foreach ($request->detail as $data) {
            $inventory = Inventory::where('pos_inventory', $posInventory->id)->where('nomor_batch', $data->nomor_batch)->first();
        
            if ($data->jumlah <= $inventory->stok) {
                $inventory->stok -= $data->jumlah;
                $inventory->save();  
            }
        }
        
        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $inventory);

    }
    
    protected function getPosInventory($request)
    {
        $validator = Validator::make($request->all(), [
            'unit' => ['required', Rule::exists( 'pos_inventories', 'unit')->whereNull('deleted_at')]
        ]);

        if ($validator->fails()) {
            return array(null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors()));
        }

        $posInventory = PosInventory::where('unit', $request->unit)->first();

        if ($posInventory == null) return array(null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found"));

        return array($posInventory, null);
    }
}
