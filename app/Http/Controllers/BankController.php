<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::paginate(50); // pagination like blogs
        return view('admin.banks.index', compact('banks'));
    }

    public function create()
    {
        return view('admin.banks.form');
    }

    public function insert(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_emi_price' => 'nullable|numeric|min:0',
        ]);

        Bank::create([
            'name' => $request->name,
            'min_emi_price' => $request->min_emi_price,
        ]);

        toastr()->success('Bank Created Successfully!');
        return redirect()->route('banks.index');
    }

    public function edit(Bank $bank)
    {
        return view('admin.banks.form', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'min_emi_price' => 'nullable|numeric|min:0',
        ]);

        $bank->update([
            'name' => $request->name,
            'min_emi_price' => $request->min_emi_price,
        ]);

        toastr()->success('Bank Edited Successfully!');
        return redirect()->route('banks.index');
    }

    public function delete(Bank $bank)
    {
        $bank->delete();

        toastr()->success('Bank Deleted Successfully!');
        return redirect()->route('banks.index');
    }
}
