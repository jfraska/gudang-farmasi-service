<?php

namespace App\Http\Controllers;

use App\Http\Helper;
use App\Models\Potype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PotypeController extends Controller
{
    use Helper;

    public function index(Request $request)
    {
        return $this->responseFormatterWithMeta($this->httpCode['StatusOK'], 
        $this->httpMessage['StatusOK'], 
        Potype::select('kode', 'name', 'state_number', 'created_at')->
        orderBy('created_at', 'desc')->cursorPaginate($request->input('per_page', 15)));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $potype = Potype::create([
            'name' => $request->name,
            'state_number' => 0,
        ]);

        return $this->responseFormatter($this->httpCode['StatusCreated'], $this->httpMessage['StatusCreated'], $potype);
    }

    public function show(Request $request)
    {
        $potype = $this->getData($request);

        if ($potype == null) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found");
        }

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $potype);
    }

    public function update(Request $request)
    {
        $potype = $this->getData($request);
        
        if ($potype == null) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found");
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $potype->update([
            'name' => $request->name,
        ]);

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $potype);
    }

    public function destroy(Request $request)
    {
        $potype = $this->getData($request);

        if ($potype == null) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found");
        }

        $potype->delete();

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], ["deleted_at" => $potype->deleted_at]);
    }

    protected function getData($request)
    {
        $this->validate($request, [
            'kode' => 'required',
        ]);
    
        $potype = Potype::where('kode', $request->kode)->first();
    
        if ($potype == null) {
            return null;
        }
    
        return $potype;
    }

}
