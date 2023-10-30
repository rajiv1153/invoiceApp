<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Counter;

class InvoiceController extends Controller
{
    public function get_all_invoice(Request $request){
        $invoices = Invoice::with('customer')->orderBy('id','DESC')->get();
        return response()->json([
            "invoices"=> $invoices

        ],200);
    }

    public function search_invoice(Request $request){
        $search = $request->get('s');
        if($search != null){
            $invoices = Invoice::with('customer')->where('id','LIKE','%'.$search.'%')->get();
            return response()->json([
                "invoices"=> $invoices
    
            ],200);
        }else{
            return $this->get_all_invoice($request);
        }
        
    }

    public function create_invoice(Request $request){
        $counter = Counter::where('key','invoice')->first();
        $random = Counter::where('key','invoice')->first();
        $invoice = Invoice::orderBy('id','DESC')->first();
        if($invoice){
            $invoice =$invoice->id+1;
            $counters=$counter->value + $invoice;


        }else{
            $counters = $counter->value ;
        }
        $formData= [
            'number' => $counter->prefix.$counters,
            'customer_id'=> null,
            'customer'=> null,
            'date'=> date('Y-m-d'),
            'due_date' =>null,
            'reference'=> null,
            'discount'=> 0,
            'term_and_conditions'=> 'Default term and condition',
            'items' =>[
                [
                    'product_id'=> null,
                    'quantity'=> 1,
                    'unit_price'=> 0,
                    'product'=> null,
                ]
            ]
            ];
            return response()->json($formData);
    }
    public function add_invoice(Request $request){
        $invoiceitem = $request->input('invoice_items');
        $invoicedata['sub_total'] = $request->input('subtotal');
        $invoicedata['total'] = $request->input('total');
        $invoicedata['customer_id'] = $request->input('customer_id');
        $invoicedata['number'] = $request->input('number');
        $invoicedata['date'] = $request->input('date');
        $invoicedata['due_date'] = $request->input('due_date');
        $invoicedata['discount'] = $request->input('discount');
        $invoicedata['reference'] = $request->input('reference');
        $invoicedata['term_and_conditions'] = $request->input('term_and_conditions');
        
        $invoice =Invoice::create($invoicedata);
        foreach(json_decode($invoiceitem) as $item){
            $itemdata['product_id']= $item->id;
            $itemdata['invoice_id']= $invoice->id;
            $itemdata['quantity']= $item->quantity;
            $itemdata['unit_price']= $item->unit_price;
            InvoiceItem::create($itemdata);
        }
    }
    public function show_invoice($id){
        $invoice= Invoice::with('customer','invoice_items.product')->find($id);
        return response()->json([
            'invoice' =>$invoice
        ],200);

    }
    public function edit_invoice($id){
        $invoice= Invoice::with('customer','invoice_items.product')->find($id);
        return response()->json([
            'invoice' =>$invoice
        ],200);
    }
    public function delete_invoice_items($id){
        $invoiceitem= InvoiceItem::findOrFail($id);
        $invoiceitem->delete();
    }
    public function update_invoice(Request $request, $id){
        $invoice =Invoice::where('id',$id)->first();
        $invoice->sub_total = $request->subtotal;
        $invoice->total = $request->total;
        $invoice->customer_id = $request->customer_id;
        $invoice->number = $request->number;
        $invoice->date = $request->date;
        $invoice->due_date = $request->due_date;
        $invoice->reference = $request->reference;
        $invoice->term_and_conditions = $request->term_and_conditions;
        $invoice->discount = $request->discount;
        $invoice->update($request->all());
        $invoiceitem =$request->input('invoice_items');
        // dd($invoice->invoice_items);
        $invoice->invoice_items()->delete();
        foreach(json_decode($invoiceitem) as $item){
            $itemdata['product_id']= $item->product_id;
            $itemdata['invoice_id']= $invoice->id;
            $itemdata['quantity']= $item->quantity;
            $itemdata['unit_price']= $item->unit_price;
            InvoiceItem::create($itemdata);

        }

        
    }
    public function delete_invoice($id){
        $invoice = Invoice::findorfail($id);
                // dd($invoice->invoice_items);

        $invoice->invoice_items()->delete();
        $invoice->delete();
    }
    
}
