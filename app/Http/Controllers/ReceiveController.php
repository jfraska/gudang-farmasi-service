<?php

namespace App\Http\Controllers;

use App\Http\Helper;
use App\Services\Rs;
use App\Models\Receive;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ReceiveController extends Controller
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

        // $search = $request->input('search');

        $receive = Receive::select('receives.id', 'purchase_orders.nomor_po', 'purchase_orders.supplier', 'purchase_orders.gudang', 'potypes.name as potype', 'nomor_faktur', 'tanggal_pembelian', 'tanggal_jatuh_tempo')
        ->join('purchase_orders', 'receives.purchase_order_id', '=', 'purchase_orders.id')
        ->join('potypes', 'purchase_orders.potype', '=', 'potypes.kode')
        ->when($search, function ($query) use ($search) {
            foreach ($search as $key => $value) {
                if ($key === "tanggal_pembelian" || $key === "tanggal_jatuh_tempo") {
                    $query->where($key, '<=' , $value);
                } else {
                    $query->where($key, 'LIKE', '%' . $value . '%');
                }
            }
        })
        ->orderBy('receives.created_at', 'desc')
        ->cursorPaginate($request->input('per_page', 15));
        
         // Get from RS service
        foreach ($receive as $data) {
            $supplier = $this->rs->showSuplier($request->bearerToken(), $data->supplier);
            $data->supplier = $supplier->name;
        }
        
        return $this->responseFormatterWithMeta($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $receive);  
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_faktur' => ['required', Rule::unique('receives', 'nomor_faktur')->whereNull('deleted_at')],
            'tanggal_pembelian' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date',
            'ppn' => 'required',
            'receive_detail' => 'required|array',
            'receive_detail.*.nomor_batch' => ['required', Rule::unique('gudangs', 'nomor_batch')->whereNull('deleted_at')],
            'receive_detail.*.item' => ['required', Rule::exists('items', 'id')->whereNull('deleted_at')],
            'receive_detail.*.stok' => 'required|integer',
            'receive_detail.*.harga_beli_satuan' => 'required|integer',
            'receive_detail.*.harga_jual_satuan' => 'required|integer',
            'receive_detail.*.tanggal_ed' => 'required|date',
            'receive_detail.*.diskon' => 'required|integer',
            'receive_detail.*.margin' => 'required|integer',
            'receive_detail.*.total_pembelian' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $receive = Receive::create([
            'nomor_faktur' => $request->nomor_faktur,
            'purchase_order_id' => $request->purchase_order_id,
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
            'ppn' => $request->ppn,
        ]);

        if ($receive->purchase_order_id === null) {
            $purchase_order = $receive->purchaseOrder()->create([
                'tanggal_po' => $request->tanggal_pembelian,
                'potype' => $request->potype,
                'gudang' => $request->gudang,
                'supplier' => $request->supplier,
                'keterangan' => $request->keterangan,
            ]);

            $receive->purchaseOrder()->associate($purchase_order);
            $receive->save();
        } 

        $receive->load(['purchaseOrder:id,gudang']);
        $receive->purchaseOrder()->delete();

        $receive_detail = array_map(function($detail) use ($receive){
            return [
                'gudang' => $receive->purchaseOrder->gudang,
                'nomor_batch' => $detail['nomor_batch'],
                'item' => $detail['item'],
                'stok' => $detail['stok'],
                'harga_beli_satuan' => $detail['harga_beli_satuan'],
                'harga_jual_satuan' => $detail['harga_jual_satuan'],
                'tanggal_ed' => $detail['tanggal_ed'],
                'diskon' => $detail['diskon'],
                'margin' => $detail['margin'],
                'total_pembelian' => $detail['total_pembelian']
            ];
        }, $request->receive_detail);
        
        $receive->Gudang()->createMany($receive_detail);
        $receive->load(['gudang.item']);

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $receive);
    }
    
    public function show(Request $request)
    {
        list($receive, $err) = $this->getData($request);
        if ($err != null) return $err;

        // Get from RS service
        $receive->getRelation('purchaseOrder')->supplier =  $this->rs->showSuplier($request->bearerToken(), $receive->getRelation('purchaseOrder')->supplier);

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $receive);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_faktur' => 'required',
            'tanggal_pembelian' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date',
            'ppn' => 'required',
            'receive_detail' => 'required|array',
            'receive_detail.*.nomor_batch' => 'required',
            'receive_detail.*.stok' => 'required|integer',
            'receive_detail.*.harga_beli_satuan' => 'required|integer',
            'receive_detail.*.harga_jual_satuan' => 'required|integer',
            'receive_detail.*.tanggal_ed' => 'required|date',
            'receive_detail.*.diskon' => 'required|integer',
            'receive_detail.*.margin' => 'required|integer',
            'receive_detail.*.total_pembelian' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }
            
        list($receive, $err) = $this->getData($request);
        if ($err != null) return $err;

        $receive->tanggal_pembelian = $request->tanggal_pembelian;
        $receive->tanggal_jatuh_tempo = $request->tanggal_jatuh_tempo;
        $receive->nomor_faktur = $request->nomor_faktur;
        $receive->ppn = $request->ppn;
        $receive->save();
        
        foreach ($receive->gudang->zip($request->receive_detail) as [$detail, $request])
        {           
            $detail->update([
                'nomor_batch' => $detail['nomor_batch'],
                'stok' => $detail['stok'],
                'harga_beli_satuan' => $detail['harga_beli_satuan'],
                'harga_jual_satuan' => $detail['harga_jual_satuan'],
                'tanggal_ed' => $detail['tanggal_ed'],
                'diskon' => $detail['diskon'],
                'margin' => $detail['margin'],
                'total_pembelian' => $detail['total_pembelian']
            ]);
        }

        $receive->refresh();

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $receive);
    }

    protected function getData($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return array(null, null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors()));
        }

        $receive = Receive::select('id', 'nomor_faktur', 'purchase_order_id', 'tanggal_pembelian', 'tanggal_jatuh_tempo', 'ppn')
        ->with(['purchaseOrder' => function ($query) {
            $query->select( 'id','nomor_po', 'supplier', 'gudang', 'potype')
            ->with(['potype:kode,name']);
        }])->with(['gudang' => function ($query) {
            $query->select('id', 'receive_id', 'nomor_batch', 'item', 'stok', 'harga_beli_satuan', 'harga_jual_satuan', 'tanggal_ed', 'diskon', 'margin', 'total_pembelian')
            ->with(['item' => function ($query) {
                $query->select( 'id', 'kode', 'name' , 'sediaan')
                ->with(['sediaan:id,name']);
            }]);
        }])->find($request->id);

        if ($receive == null) return array(null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found"));

        return array($receive, null);
    }
    
}
