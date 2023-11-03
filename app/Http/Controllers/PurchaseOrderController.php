<?php

namespace App\Http\Controllers;

use App\Http\Helper;
use App\Services\Rs;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
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

        $purchaseOrder = PurchaseOrder::select('id', 'nomor_po', 'potypes.name as potype', 'tanggal_po', 'gudang', 'supplier', 'keterangan')
        ->join('potypes', 'purchase_orders.potype', '=', 'potypes.kode')
        ->when($search, function ($query) use ($search) {
            foreach ($search as $key => $value) {
                if ($key === "tanggal_po") {
                    $query->where($key, '<=' , $value);
                } else {
                    $query->where($key, 'LIKE', '%' . $value . '%');
                }
            }
        })
        ->orderBy('purchase_orders.created_at', 'desc')
        ->cursorPaginate($request->input('per_page', 15));
        
        // Get from RS service
        foreach ($purchaseOrder as $data) {
            $supplier = $this->rs->showSuplier($request->bearerToken(), $data->supplier);
            $data->supplier = $supplier->name;
        }

        return $this->responseFormatterWithMeta($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $purchaseOrder);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_po' => ['required', Rule::unique('purchase_orders')->whereNull('deleted_at')],
            'tanggal_po' => 'required|date',
            'gudang' => 'required',
            'supplier' => 'required',
            'potype' => ['required', Rule::exists('potypes', 'kode')->whereNull('deleted_at')],
            'purchase_order_detail' => 'required|array',
            'purchase_order_detail.*.item' => ['required', Rule::exists('items', 'id')->whereNull('deleted_at')],
            'purchase_order_detail.*.sediaan' => ['required', Rule::exists('sediaans', 'id')->whereNull('deleted_at')],
            'purchase_order_detail.*.jumlah' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $purchaseOrder = PurchaseOrder::create([
            'nomor_po' => $request->nomor_po,
            'potype' => $request->potype,
            'tanggal_po' => $request->tanggal_po,
            'gudang' => $request->gudang,
            'supplier' => $request->supplier,
            'keterangan' => $request->keterangan,
        ]);

        $purchase_order_detail = array_map(function($detail) {
            return [
                'item' => $detail['item'],
                'sediaan' => $detail['sediaan'],
                'jumlah' => $detail['jumlah'],
            ];
        }, $request->purchase_order_detail);
        
        $purchaseOrder->PurchaseOrderDetail()->createMany($purchase_order_detail);
        $purchaseOrder->load(['purchaseOrderDetail.sediaan', 'purchaseOrderDetail.item']);

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $purchaseOrder);
    }

    public function show(Request $request)
    {
        list($purchaseOrder, $err) = $this->getData($request);
        if ($err != null) return $err;

        // Get from RS service
        $purchaseOrder->supplier =  $this->rs->showSuplier($request->bearerToken(), $purchaseOrder->supplier);

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $purchaseOrder);
    }

    public function destroy(Request $request)
    {
        list($purchaseOrder, $err) = $this->getData($request);
        if ($err != null) return $err;

        $purchaseOrder->delete();

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], ["deleted_at" => $purchaseOrder->deleted_at]);
    }

    protected function getData($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return array(null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors()));
        }

        $purchaseOrder = PurchaseOrder::select('id', 'nomor_po', 'potype', 'tanggal_po', 'gudang', 'supplier', 'keterangan')
        ->with(['potype:kode,name'])
        ->with(['purchaseOrderDetail' => function ($query) {
            $query->select('id', 'purchase_order_id', 'item', 'sediaan', 'jumlah')
            ->with(['item' => function ($query) {
                $query->select( 'id', 'kode', 'name' , 'sediaan')
                ->with(['sediaan:id,name']);
            }])
            ->with(['sediaan:id,name']);
        }])->find($request->id);

        if ($purchaseOrder == null) return array(null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found"));

        return array($purchaseOrder, null);
    }

}
