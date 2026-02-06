<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankTenureRequest;
use App\Models\Bank;
use App\Models\BankTenure;
use Illuminate\Http\Request;

class BankTenureController extends Controller
{
    public function index()
    {
        $tenures = BankTenure::with('bank')->paginate(50);
        return view('admin.banks_tenures.index', compact('tenures'));
    }

    public function create()
    {
        $banks = Bank::all();
        return view('admin.banks_tenures.form', compact('banks'));
    }

    public function insert(BankTenureRequest $request)
    {
        BankTenure::create($request->validated());
        toastr()->success('Bank Tenure Created Successfully!');
        return redirect()->route('banks-tenures.index');
    }

    public function edit(BankTenure $bankTenure)
    {
        $banks = Bank::all();
        return view('admin.banks_tenures.form', compact('bankTenure', 'banks'));
    }

    public function update(BankTenure $bankTenure, BankTenureRequest $request)
    {
        $bankTenure->update($request->validated());
        toastr()->success('Bank Tenure Updated Successfully!');
        return redirect()->route('banks-tenures.index');
    }

    public function delete(BankTenure $bankTenure)
    {
        $bankTenure->delete();
        toastr()->success('Bank Tenure Deleted Successfully!');
        return redirect()->route('banks-tenures.index');
    }
}
