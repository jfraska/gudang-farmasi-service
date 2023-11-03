<?php

namespace App\Http\Controllers;

use App\Http\Helper;
use App\Services\Rs;
use App\Models\Retur;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ReturController extends Controller
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
        
        $retur = Retur::select('returs.id', 'nomor_retur', 'receives.nomor_faktur', 'purchase_orders.supplier', 'purchase_orders.gudang', 'tanggal_retur')
        ->join('receives', 'returs.receive_id', '=', 'receives.id')
        ->join('purchase_orders', 'receives.purchase_order_id', '=', 'purchase_orders.id')
        ->when($search, function ($query) use ($search) {
            foreach ($search as $key => $value) {
                    $query->where($key, 'LIKE', '%' . $value . '%');
            }
        })
        ->orderBy('returs.created_at', 'desc')
        ->cursorPaginate($request->input('per_page', 15));

        // Get from RS service
        foreach ($retur as $data) {
            $supplier = $this->rs->showSuplier($request->bearerToken(), $data->supplier);
            $data->supplier = $supplier->name;
        }

        return $this->responseFormatterWithMeta(
            $this->httpCode['StatusOK'],
            $this->httpMessage['StatusOK'],
            $retur);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_retur' => 'required|date',
            'receive_id' => ['required', Rule::exists('receives', 'id')->whereNull('deleted_at')],
            'retur_detail' => 'required|array',
            'retur_detail.*.gudang' => ['required', Rule::exists( 'gudangs', 'id')->where('receive_id', $request->receive_id)->whereNull('deleted_at')],
            'retur_detail.*.jumlah' => 'required|integer',
            'retur_detail.*.alasan' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $retur = Retur::create([
            'tanggal_retur' => $request->tanggal_retur,
            'receive_id' => $request->receive_id,
        ]);
        
        $retur_detail = array_map(function($detail) {
            return [
                'gudang' => $detail['gudang'],
                'jumlah' => $detail['jumlah'],
                'alasan' => $detail['alasan'],
            ];
        }, $request->retur_detail);
        
        $retur->returDetail()->createMany($retur_detail);
        $retur->load(['returDetail.gudang']);

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $retur);
    }

    public function show(Request $request)
    {
        list($retur, $err) = $this->getData($request);
        if ($err != null) return $err;

        $retur->getRelation('receive')->getRelation('purchaseOrder')->supplier =  $this->rs->showSuplier($request->bearerToken(), $retur->getRelation('receive')->getRelation('purchaseOrder')->supplier);

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $retur);
    }

    protected function getData($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return array(null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors()));
        }

        $retur = Retur::select('id', 'nomor_retur', 'tanggal_retur', 'receive_id')
        ->with(['receive' => function ($query) {
            $query->select('id', 'nomor_faktur', 'purchase_order_id')
            ->with(['purchaseOrder:id,gudang,supplier']);
        }])->with(['returDetail' => function ($query) {
            $query->select('id', 'retur_id', 'gudang', 'jumlah', 'alasan')
            ->with(['gudang' => function ($query){
                $query->select('id', 'nomor_batch', 'item', 'stok', 'tanggal_ed')
                ->with(['item' => function ($query) {
                    $query->select( 'id', 'kode', 'name' , 'sediaan')
                    ->with(['sediaan:id,name']);
                }]);
            }]);
        }])->find($request->id);

        if ($retur == null) return array(null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found"));

        return array($retur, null);
    }
}
