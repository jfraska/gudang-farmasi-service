<?php

namespace App\Http\Controllers;

use App\Http\Helper;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    use Helper;

    public function index(Request $request)
    {
        $searchJson = $request->input('search');
        $search = json_decode($searchJson);

        $item = Item::select('id', 'kode', 'name', 'status', 'minimum_stok', 'sediaan', 'kategori')
        ->with('sediaan:id,name')
        ->with('kategori:kode,name')
        ->when($search, function ($query) use ($search) {
            foreach ($search as $key => $value) {
                foreach ($search as $key => $value) {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                }
            }
        })
        ->orderBy('id', 'asc')
        ->cursorPaginate($request->input('per_page', 15));       

        return $this->responseFormatterWithMeta($this->httpCode['StatusOK'],$this->httpMessage['StatusOK'], $item);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', Rule::unique('items')->whereNull('deleted_at')],
            'kode' => 'required|string',
            'name' => 'required|string',
            'minimum_stok' => 'required|int',
            'sediaan' => ['required', Rule::exists('sediaans', 'id')->whereNull('deleted_at')],
            'kategori' => ['required', Rule::exists('potypes', 'kode')->whereNull('deleted_at')],
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $item = Item::create([
            'id' => $request->id,
            'kode' => $request->kode,
            'name' => $request->name,
            'minimum_stok' => $request->minimum_stok,
            'sediaan' => $request->sediaan,
            'kategori'=> $request->potype
        ]);

        return $this->responseFormatter($this->httpCode['StatusCreated'], $this->httpMessage['StatusCreated'], $item);
    }

    public function show(Request $request)
    {
        $item = $this->getData($request);

        if ($item == null) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found");
        }

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $item);
    }

    public function update(Request $request)
    {
        $item = $this->getData($request);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'minimum_stok' => 'required|int',
            'status' => 'required|boolean',
            'sediaan' => ['required', Rule::exists('sediaans', 'id')->whereNull('deleted_at')],
            'kategori' => ['required', Rule::exists('potypes', 'kode')->whereNull('deleted_at')],
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }


        $item->update([
            'name' => $request->name,
            'minimum_stok' => $request->minimum_stok,
            'status' => $request->status,
            'sediaan' => $request->sediaan,
            'kategori'=> $request->potype
        ]);

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], $item);
    }

    public function destroy(Request $request)
    {
        $item = $this->getData($request);

        if ($item == null) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], "Data Not Found");
        }

        $item->delete();

        return $this->responseFormatter($this->httpCode['StatusOK'], $this->httpMessage['StatusOK'], ["deleted_at" => $item->deleted_at]);
    }

    protected function getData($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponseFormatter($this->httpCode['StatusUnprocessableEntity'], $this->httpMessage['StatusUnprocessableEntity'], $validator->errors());
        }

        $item = Item::find($request->id);

        if ($item == null) {
            return null;
        }

        return $item;
    }
}
