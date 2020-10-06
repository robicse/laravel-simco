<?php

namespace App\Http\Controllers;

use App\Expense;
use App\OfficeCostingCategory;
use App\Store;
use App\Transaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        if($auth_user == "Admin"){
            $expenses = Expense::latest()->get();
        }else{
            $expenses = Expense::where('user_id',$auth_user_id)->get();
        }
        return view('backend.expense.index',compact('expenses'));
    }

    public function create()
    {
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        $officeCostingCategories = OfficeCostingCategory::all() ;
        if($auth_user == "Admin"){
            $stores = Store::all();
        }else{
            $stores = Store::where('user_id',$auth_user_id)->get();
        }

        return view('backend.expense.create',compact('officeCostingCategories','stores'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $this->validate($request, [
            'payment_type'=> 'required',
            'amount'=> 'required',
        ]);

        $expense = new Expense();
        $expense->user_id = Auth::id();
        $expense->store_id = $request->store_id;
        $expense->office_costing_category_id = $request->office_costing_category_id;
        $expense->payment_type = $request->payment_type;
        $expense->check_number = $request->check_number ? $request->check_number : NULL;
        $expense->amount = $request->amount;
        $expense->date = date('d-m-Y');
        $expense->save();

        $insert_id = $expense->id;
        if($insert_id){
            // transaction
            $transaction = new Transaction();
            //$transaction->invoice_no = $product_sale->invoice_no;
            $transaction->user_id = Auth::id();
            $transaction->store_id = $request->store_id;
            //$transaction->party_id = $product_sale->party_id;
            $transaction->ref_id = $insert_id;
            $transaction->transaction_type = 'expense';
            $transaction->payment_type = $request->payment_type;
            $transaction->check_number = $request->check_number ? $request->check_number : '';
            $transaction->amount = $request->amount;
            $transaction->save();
        }


        Toastr::success('Expense Created Successfully', 'Success');
        return redirect()->route('expenses.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $auth_user_id = Auth::user()->id;
        $auth_user = Auth::user()->roles[0]->name;
        $officeCostingCategories = OfficeCostingCategory::all() ;
        if($auth_user == "Admin"){
            $stores = Store::all();
        }else{
            $stores = Store::where('user_id',$auth_user_id)->get();
        }
        $expense = Expense::find($id);

        return view('backend.expense.edit',compact('expense','officeCostingCategories','stores'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'payment_type'=> 'required',
            'amount'=> 'required',
        ]);

        $expense = Expense::find($id);
        $expense->user_id = Auth::id();
        //$expense->store_id = $request->store_id;
        $expense->office_costing_category_id = $request->office_costing_category_id;
        $expense->payment_type = $request->payment_type;
        $expense->check_number = $request->check_number ? $request->check_number : NULL;
        $expense->amount = $request->amount;
        //$expense->date = date('d-m-Y');
        $expense->save();


        Toastr::success('Expense Updated Successfully', 'Success');
        return redirect()->route('expenses.index');
    }

    public function destroy($id)
    {
        Expense::destroy($id);
        Toastr::success('Expense Updated Successfully', 'Success');
        return redirect()->route('expenses.index');
    }
}
