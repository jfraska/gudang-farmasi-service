<?php

namespace App\Http\Controllers;

use App\Http\Helper;
use App\Models\Sediaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SediaanController extends Controller
{
    use Helper;

    public function index(Request $request)
    {
        return $this->responseFormatterWithMeta($this->httpCode['StatusOK'], 
        $this->httpMessage['StatusOK'], 
        Sediaan::select('id', 'name', 'created_at')->
        orderBy('created_at', 'asc')->cursorPaginate($request->input('per_page', 15)));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sediaan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $sediaan = Sediaan::create([
            'sediaan' => $request->sediaan,
        ]);

        return $this->responseFormatter($this->httpCode['StatusCreated'], $this->httpMessage['StatusCreated'], $sediaan);
    }

    public function show(Request $request)
    {
        $sediaan = $this->getData($request);

        if ($sediaan == null) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found");
        }

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $sediaan);
    }

    public function update(Request $request)
    {
        $sediaan = $this->getData($request);

        $this->validate($request, [
            'sediaan' => 'required',
        ]);

        $sediaan->update([
            'sediaan' => $request->sediaan,
        ]);

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $sediaan);
    }
    
    public function destroy(Request $request)
    {
        $sediaan = $this->getData($request);

        if ($sediaan == null) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found");
        }

        $sediaan->delete();

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], ["deleted_at" => $sediaan->deleted_at]);
    }

    protected function getData($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $sediaan = Sediaan::find($request->id);

        if ($sediaan == null) {
            return null;
        }

        return $sediaan;
    }
}
