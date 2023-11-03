<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Helper;
use App\Services\Rs;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GudangController extends Controller
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

        $laporan = $request->input('laporan', '');

        $gudang = Gudang::when($laporan === '', function ($query) {
            $query->select('nomor_batch', 'gudang', 'stok', 'harga_jual_satuan', 'items.name as item', 'items.kode as item_kode', 'sediaans.name as sediaan', 'potypes.name as kategori', 'harga_beli_satuan', 'diskon', 'margin','total_pembelian', 'tanggal_ed')
            ->join('items', 'gudangs.item', '=', 'items.id')
            ->join('sediaans', 'items.sediaan', '=', 'sediaans.id')
            ->join('potypes', 'items.kategori', '=', 'potypes.kode')
            ->orderBy('gudangs.created_at', 'asc');
        }, function ($query) use ($laporan) {
            //stok opname
            if ($laporan === 'opname') {
                $query->selectRaw('potypes.name as kategori, SUM(stok) as total_stok, SUM(total_pembelian) as total_pembelian')
                ->join('items', 'gudangs.item', '=', 'items.id')
                ->join('potypes', 'items.kategori', '=', 'potypes.kode')
                ->groupBy('potypes.name')
                ->orderBy('potypes.name', 'asc');
            } 
            
            //stok pembeliaan
            elseif ($laporan === 'pembelian') {
                $query->select('receives.nomor_faktur', 'receives.tanggal_pembelian', 'receives.tanggal_jatuh_tempo', 'receives.ppn', 'purchase_orders.supplier', 'purchase_orders.gudang', 'potypes.name', 'harga_beli_satuan', 'diskon', 'total_pembelian')
                ->join('receives', 'gudangs.receive_id', '=', 'receives.id')
                ->join('purchase_orders', 'receives.purchase_order_id', '=', 'purchase_orders.id')
                ->join('potypes', 'purchase_orders.potype', '=', 'potypes.kode')
                ->orderBy('receives.tanggal_pembelian', 'asc');
            } 

            //stok pembeliaan
            elseif ($laporan === 'pembelian_kategori') {
                $query->selectRaw('potypes.name as kategori, SUM(stok) as total_stok, SUM(total_pembelian) as total_pembelian')
                ->join('receives', 'gudangs.receive_id', '=', 'receives.id')
                ->join('purchase_orders', 'receives.purchase_order_id', '=', 'purchase_orders.id')
                ->join('potypes', 'purchase_orders.potype', '=', 'potypes.kode')
                ->groupBy('potypes.name')
                ->orderBy('potypes.name', 'asc');
            } 
            
            //stok expired
            elseif ($laporan === 'expired') {
                $query->selectRaw('items.name as item, gudang, stok as jumlah, harga_beli_satuan as harga_beli, total_pembelian as total, tanggal_ed')
                ->join('items', 'gudangs.item', '=', 'items.id')
                ->where('tanggal_ed', '<=', now())
                ->orderBy('tanggal_ed', 'asc');
            }

            //stok mati
            elseif ($laporan === 'mati') {
                $now = Carbon::now();
                $sixMonthsAgo = $now->copy()->subMonths(6);

                $query->selectRaw( 'nomor_batch, items.name as item, sediaans.name as sediaan, potypes.name as potype, inventories.stok, pos_inventories.unit, gudangs.gudang, tanggal_ed')
                ->rightjoin('inventories', 'gudangs.id', '=', 'inventories.gudang')
                ->join('pos_inventories', 'inventories.pos_inventory', '=', 'pos_inventories.id')
                ->join('items', 'gudangs.item', '=', 'items.id')
                ->join('sediaans', 'items.sediaan', '=', 'sediaans.id')
                ->join('potypes', 'items.potype', '=', 'potypes.kode')
                ->whereDate('inventories.updated_at', '<', $sixMonthsAgo)
                ->orderBy('tanggal_ed', 'asc');
            } 
        })
        ->when($search, function ($query) use ($search) {
            foreach ($search as $key => $value) {
                if ($key === "tanggal_ed") {
                    $query->where($key, '<=' , $value);
                } elseif ($key === "gudang") {
                    $query->where('gudangs.'.$key, 'LIKE', '%' . $value . '%');
                } elseif ($key === "tahun") {
                    $query->whereBetween('receives.tanggal_pembelian', [$value.'-01-01', $value.'-12-31']);
                } else {
                    $query->where($key, 'LIKE', '%' . $value . '%');
                }
            }
        })
        ->cursorPaginate($request->input('per_page', 15));       

        // Get from RS service
        if ($laporan === "pembelian"){
            foreach ($gudang as $data) {
                $supplier = $this->rs->showSuplier($request->bearerToken(), $data->supplier);
                $data->supplier = $supplier->name;
            }
        }

        // Get from RS service
        if ($laporan === "mati"){
            foreach ($gudang as $data) {
                $unit = $this->rs->showUnit($request->bearerToken(), $data->unit);
                $data->unit = $unit->name;
            }
        }

        return $this->responseFormatterWithMeta($this->httpCode['StatusOK'],$this->httpMessage['StatusOK'], $gudang);
    }

    public function show(Request $request)
    {
        $gudang = $this->getData($request);

        if ($gudang == null) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found");
        }

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $gudang);    
    }

    protected function getData($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return array(null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors()));
        }
        
        $result = Gudang::select('nomor_batch', 'item', 'stok', 'harga_jual_satuan', 'tanggal_ed')
        ->with(['item' => function ($query) {
            $query->select( 'id', 'kode', 'name' , 'sediaan')
            ->with(['sediaan:id,name']);
        }])
        ->find($request->id);

        if ($result == null) {
            return null;
        }

        return $result;
    }
    
}
