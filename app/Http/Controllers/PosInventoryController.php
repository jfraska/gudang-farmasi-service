<?php

namespace App\Http\Controllers;

use App\Http\Helper;
use App\Services\Rs;
use App\Models\PosInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PosInventoryController extends Controller
{
    use Helper;
    
    protected $rs;

    public function __construct(Rs $rs)
    {
    	$this->rs = $rs;
    }

    public function index(Request $request)
    {
        $posInventory = PosInventory::select('id', 'unit', 'gudang', 'created_at')
        ->orderBy('created_at', 'desc')->cursorPaginate($request->input('per_page', 15));

        foreach ($posInventory as $data) {
            $data->unit =  $this->rs->showUnit($request->bearerToken(), $data->unit);
        }

        return $this->responseFormatterWithMeta($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $posInventory);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit' => 'required',
            'gudang' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $posInventory = PosInventory::create([
            'unit' => $request->unit,
            'gudang' => $request->gudang,
        ]);

        return $this->responseFormatter($this->httpCode['StatusCreated'], $this->httpMessage['StatusCreated'], $posInventory);
    }

    public function show(Request $request)
    {
        $posInventory = $this->getData($request);

        if ($posInventory == null) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found");
        }

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $posInventory);
    }

    public function update(Request $request)
    {
        $posInventory = $this->getData($request);
        
        if ($posInventory == null) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found");
        }

        $validator = Validator::make($request->all(), [
            'unit' => 'required',
            'gudang' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $posInventory->update([
            'unit' => $request->unit,
            'gudang' => $request->gudang,
        ]);

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $posInventory);
    }

    public function destroy(Request $request)
    {
        $posInventory = $this->getData($request);

        if ($posInventory == null) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found");
        }

        $posInventory->delete();

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], ["deleted_at" => $posInventory->deleted_at]);
    }

    protected function getData($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
    
        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $posInventory = PosInventory::find($request->id);
    
        if ($posInventory == null) {
            return null;
        }
    
        return $posInventory;
    }

}
