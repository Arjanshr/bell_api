<?php

namespace App\Http\Controllers;

use App\Models\ProcessingFeeRule;
use App\Models\Bank;
use Illuminate\Http\Request;

class ProcessingFeeRuleController extends Controller
{
    public function index()
    {
        $rules = ProcessingFeeRule::with('bank')->paginate(50);
        return view('admin.processing_fee_rules.index', compact('rules'));
    }

    public function create()
    {
        $banks = Bank::pluck('name', 'id');
        return view('admin.processing_fee_rules.form', compact('banks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_fee' => 'nullable|numeric|min:0',
        ]);

        ProcessingFeeRule::create($data);

        toastr()->success('Processing fee rule created successfully!');
        return redirect()->route('processing-fee-rules.index');
    }

    public function edit(ProcessingFeeRule $processing_fee_rule)
    {
        $banks = Bank::pluck('name', 'id');
        return view('admin.processing_fee_rules.form', [
            'rule' => $processing_fee_rule,
            'banks' => $banks
        ]);
    }

    public function update(Request $request, ProcessingFeeRule $processing_fee_rule)
    {
        $data = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_fee' => 'nullable|numeric|min:0',
        ]);

        $processing_fee_rule->update($data);

        toastr()->success('Processing fee rule updated successfully!');
        return redirect()->route('processing-fee-rules.index');
    }

    public function destroy(ProcessingFeeRule $processing_fee_rule)
    {
        $processing_fee_rule->delete();

        toastr()->success('Processing fee rule deleted successfully!');
        return redirect()->route('processing-fee-rules.index');
    }
}

