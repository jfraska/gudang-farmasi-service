<?php

namespace App\Http\Controllers;

use App\Http\Helper;
use App\Services\Rs;
use App\Models\Mutation;
use App\Models\MutationDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class MutationController extends Controller
{
    use Helper;

    protected  $rs;

    public function __construct(Rs $rs)
    {
    	$this->rs = $rs;
    }

    public function index(Request $request)
    {
        $searchJson = $request->input('search');
        $search = json_decode($searchJson);

        // $search = $request->input('search');

        $mutation = Mutation::select('id', 'nomor_mutasi', 'tanggal_permintaan', 'tanggal_mutasi', 'unit', 'gudang')
        ->when($search, function ($query) use ($search) {
            foreach ($search as $key => $value) {
                if ($key === "tanggal_permintaan" || $key === "tanggal_mutasi") {
                    $query->where($key, '<=' , $value);
                } else {
                    $query->where($key, 'LIKE', '%' . $value . '%');
                }
            }
        });

        if ($request->has('trashed')) $mutation->onlyTrashed()->orderBy('mutations.deleted_at', 'asc'); // Hanya mengambil data yang sudah dihapus
        else $mutation->orderBy('mutations.created_at', 'desc');

        $mutation = $mutation->cursorPaginate($request->input('per_page', 15));

        foreach ($mutation as $data) {
            $unit =  $this->rs->showUnit($request->bearerToken(), $data->unit);
            $data->unit = $unit->name;
        }        

        return $this->responseFormatterWithMeta($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $mutation);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_permintaan' => 'required|date',
            'unit' => 'required',
            'mutation_detail' => 'required|array',
            'mutation_detail.*.item' => ['required', Rule::exists('items', 'id')->whereNull('deleted_at')],
            'mutation_detail.*.jumlah' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $mutation = Mutation::create([
            'tanggal_permintaan' => $request->tanggal_permintaan,
            'unit' => $request->unit,
        ]);

        $mutation_detail = array_map(function($detail) {
            return [
                'item' => $detail['item'],
                'jumlah' => $detail['jumlah'],
            ];
        }, $request->mutation_detail);
        
        $mutation->mutationDetail()->createMany($mutation_detail);
        $mutation->load('mutationDetail');

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $mutation);
    }

    public function show(Request $request)
    {
        list($mutation, $err) = $this->getData($request);
        if ($err != null) return $err;

        $mutation->unit =  $this->rs->showUnit($request->bearerToken(), $mutation->unit);

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $mutation);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mutasi' => 'required|date',
            'mutation_detail' => 'required|array',
            'mutation_detail.*.gudang' => ['required', Rule::exists( 'gudangs', 'id')->whereNull('deleted_at')],
            'mutation_detail.*.jumlah' => 'required|integer',
        ]);
        
        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }
        
        list($mutation, $err) = $this->getData($request);
        if ($err != null) return $err;

        $mutation->tanggal_mutasi = $request->tanggal_mutasi;
        $mutation->save();

        $mutation->mutationDetail->load(['gudang.inventory']);
        
        foreach ($mutation->mutationDetail->zip($request->mutation_detail) as [$detail, $request])
        {           
            $detail->update([
                'gudang' => $request['gudang'],
                'jumlah' => $request['jumlah'],
                'unit' => $mutation->unit
            ]);

            $detail->afterupdate([
                'unit'  => $mutation->unit,
                'jumlah'=> $detail['jumlah']
            ]);
        }

        $mutation->delete();
        $mutation->refresh();

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $mutation);
    }

    public function destroyItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return array(null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors()));
        }

        $mutation_detail = MutationDetail::find($request->id);

        $mutation_detail->delete();

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], ["deleted_at" => $mutation_detail->deleted_at]);
    }

    protected function getData($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return array(null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors()));
        }

        $mutation = Mutation::select('id', 'nomor_mutasi', 'tanggal_permintaan', 'tanggal_mutasi', 'unit', 'gudang')
        ->with(['mutationDetail' => function ($query) {
            $query->select('id', 'gudang', 'mutation_id', 'item', 'jumlah')
            ->with(['item' => function ($query) {
                $query->select( 'id', 'kode', 'name' , 'sediaan')
                ->with(['sediaan:id,name']);
            }])
            ->with(['gudang' => function ($query){
                $query->select('id', 'nomor_batch', 'tanggal_ed');
            }]);
        }])
        ->withTrashed()
        ->find($request->id);

        if ($mutation == null) return array(null, $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found"));

        return array($mutation, null);
    }

}
