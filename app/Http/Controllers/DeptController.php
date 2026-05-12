<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Addperson;
use App\Models\Product;
use App\Models\Sale;
use App\Models\ViewUtang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class DeptController extends Controller
{
    //LOGIN
    function viewLogin(){
        return view('login');
    }

    //REGISTER
    function viewRegister(){
        return view('register');
    }
    //REGISTER SUBMIT
    function registerSubmit(Request $request){
        $request->validate([
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
        ]);         
        $exists = \App\Models\User::where('email', $request->email)->exists();
        if($exists){
            return back()->withErrors([
                'email' => 'The email has already been taken.',
            ]);
        }
        $user = \App\Models\User::create([
            'name' =>$request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        Auth::login($user);
        session([
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email,
            'user_name' => Auth::user()->name,
        ]);
        return redirect()->route('home');
    }
    //LOGIN SUBMIT
    function loginSubmit(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                session([
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email,
                    'user_name' => Auth::user()->name,
                ]);
                return redirect()->intended('home');
            }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    //LOGOUT
    function logout(){
        Auth::logout();
        session()->flush();
        return redirect()->route('login');
    }

    
    // HOME
    function home(){
        $barcodeProducts = Product::where('user_id', Auth::id())
                                ->whereNotNull('barcode')
                                ->where('barcode', '!=', '')
                                ->get(['id', 'barcode', 'name', 'price', 'stock_quantity'])
                                ->mapWithKeys(function($product){
                                    return [
                                        $product->barcode => [
                                            'id' => $product->id,
                                            'item' => $product->name,
                                            'price' => (float) $product->price,
                                            'stock_quantity' => $product->stock_quantity,
                                        ],
                                    ];
                                });

        return view('home', compact('barcodeProducts'));
    }

    //ADD PERSON
    function addperson(Request $request){
        $request->validate([
            'person_name' => 'required|string|max:20',
            'person_address' => 'required|string|max:100'
        ]);

        $exists = Addperson::where('user_id', Auth::id())
                            ->where('name', ucwords($request->person_name))
                            ->where('address', ucfirst($request->person_address))
                            ->exists();
        if(!$exists){
            Addperson::create([
                'user_id' => Auth::id(),
                'name' => ucwords($request->person_name),
                'address' => ucfirst($request->person_address)
            ]);

            return back()->with('success', "Person was added successfully!");
        }else{
            return back()->with('error', 'A person is already exsisted!');
        }
    }

    // GET PERSON
    function getPerson(Request $request){
        $data = Addperson::where('user_id', Auth::id())
                        ->select('id','name', 'address','total')
                        ->get();
        return response()->json($data);
    }

    // ADD UTANG
    function addUtang(Request $request){
        $request->validate([
            'person_id' => 'required|integer',
            'item' => 'required|string|max:100',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $person = Addperson::where('user_id', Auth::id())
                            ->where('id', $request->person_id)
                            ->firstOrFail();

        $total = $request->qty * $request->price;

        ViewUtang::create([
            'user_id' => Auth::id(),
            'person_id' => $person->id,
            'item' => $request->item,
            'qty' => $request->qty,
            'price' => $request->price,
            'total' => $total,
            'status' => 'unpaid',
        ]);

        $person->increment('total', $total);

        return back()->with('success', 'Utang was added successfully!');
    }

    // GET PERSON UTANG
    function getPersonUtang($personId){
        Addperson::where('user_id', Auth::id())
                ->where('id', $personId)
                ->firstOrFail();

        $utang = ViewUtang::where('user_id', Auth::id())
                        ->where('person_id', $personId)
                        ->orderByRaw("status = 'paid'")
                        ->latest()
                        ->get(['id', 'item', 'qty', 'price', 'total', 'status']);

        return response()->json($utang);
    }

    // PAY UTANG
    function payUtang($utangId){
        DB::transaction(function() use ($utangId){
            $utang = ViewUtang::where('user_id', Auth::id())
                            ->where('id', $utangId)
                            ->lockForUpdate()
                            ->firstOrFail();

            if($utang->status === 'paid'){
                return;
            }

            $person = Addperson::where('user_id', Auth::id())
                                ->where('id', $utang->person_id)
                                ->lockForUpdate()
                                ->firstOrFail();

            $utang->update([
                'status' => 'paid',
            ]);

            $person->total = max(0, $person->total - $utang->total);
            $person->save();
        });

        return response()->json([
            'success' => true,
        ]);
    }

    // VIEW PRODUCTS
    function viewProducts(){
        $products = Product::where('user_id', Auth::id())
                            ->orderBy('name')
                            ->get();

        return view('products', compact('products'));
    }

    // ADD PRODUCT
    function addProduct(Request $request){
        $request->validate([
            'barcode' => 'nullable|string|max:100',
            'product_name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $barcode = $request->filled('barcode') ? trim($request->barcode) : null;

        if($barcode){
            $exists = Product::where('user_id', Auth::id())
                            ->where('barcode', $barcode)
                            ->exists();

            if($exists){
                return back()->with('error', 'A product with this barcode already exists.');
            }
        }

        Product::create([
            'user_id' => Auth::id(),
            'barcode' => $barcode,
            'name' => ucwords($request->product_name),
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
        ]);

        return back()->with('success', 'Product was added successfully!');
    }

    // ADD SALE
    function addSale(Request $request){
        $request->validate([
            'product_id' => 'required|integer',
            'barcode' => 'nullable|string|max:100',
            'item' => 'required|string|max:100',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function() use ($request){
            $product = Product::where('user_id', Auth::id())
                            ->where('id', $request->product_id)
                            ->lockForUpdate()
                            ->firstOrFail();

            if($product->stock_quantity < $request->qty){
                abort(422, 'Not enough stock for this sale.');
            }

            $total = $request->qty * $request->price;

            Sale::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'barcode' => $request->barcode,
                'item' => $product->name,
                'qty' => $request->qty,
                'price' => $request->price,
                'total' => $total,
            ]);

            $product->decrement('stock_quantity', $request->qty);
        });

        return back()->with('success', 'Sale was saved successfully!');
    }

    // VIEW SALES
    function viewSales(){
        $sales = Sale::where('user_id', Auth::id())
                    ->latest()
                    ->get();

        return view('sales', compact('sales'));
    }
}
