<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{

    public function register() {
        return view('registration-form');
    }


    public function datastore(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:20'],
                'email' => ['required', 'email', 'unique:users,email'],
                'ph_number' => ['required', 'numeric', 'min:10'],
                'password' => ['required', 'alpha_num', 'min:6', 'confirmed'],
            ]);

        $sql=  User::create([
                'name'          => $request['name'],
                'email'         => $request['email'],
                'ph_number'     => $request['ph_number'],
                'password'      => Hash::make($request['password']),
            ]);
        } catch(Exception $e) {
            return back()->withErrors($e->getMessage());
        }
        return redirect()->route('login');

    }


    public function login() {
        return view('login');
    }


    public function authcheck(Request $request) {

        $data = $request->validate( [
            'email' => ['required'],
            'password' => ['required'],
        ]);

        $user_data = [
        'email'  => $request->email,
        'password' => $request->password
        ];

        if (Auth::attempt($user_data)) {
            return redirect()->route('dashboard');
        }
    }

    public function dashboard() {
        $categories = Category::all();
        return view('dashboard', compact('categories'));
    }

    public function store_order(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'product_name' => ['required', 'string'],
                'start_date' => ['required', 'string'],
                'end_date' => ['required', 'string'],
                'price' => ['required', 'string'],
                'reminder' => ['required', 'string'],
                'reminder2' => ['required', 'string'],
                'company_name' => ['required', 'string'],
                'company_number' => ['required', 'string'],
                'agent_name' => ['required', 'string'],
                'agent_number' => ['required', 'string'],
                'attachment' => ['nullable'],
                'status'     => ['required'],
                'category_id'                 => ['required', 'exists:categories,id'],

            ]);


            if($request->attachment ==''){
                $order=  Order::create([
                    'product_name'          => $request['product_name'],
                    'start_date'         => $request['start_date'],
                    'end_date'     => $request['end_date'],
                    'price'         => $request['price'],
                    'reminder'         => json_encode($request['reminder']),
                    'company_name'         => $request['company_name'],
                    'company_number'         => $request['company_number'],
                    'agent_name'         => $request['agent_name'],
                    'agent_number'         => $request['agent_number'],
                    'status'             => $request['status'],
                    'category_id'                 => $request['category_id'],
                ]);
            }else{
                $imageName = time().'.'.$request->attachment;
                $request->attachment->move(public_path('images'), $imageName);
                $order=  Order::create([
                    'product_name'          => $request['product_name'],
                    'start_date'         => $request['start_date'],
                    'end_date'     => $request['end_date'],
                    'price'         => $request['price'],
                    'reminder'         => json_encode($request['reminder']),
                    'company_name'         => $request['company_name'],
                    'company_number'         => $request['company_number'],
                    'agent_name'         => $request['agent_name'],
                    'agent_number'         => $request['agent_number'],
                    'attachment'        => $imageName,
                    'status'             => $request['status'],
                    'category_id'                 => $request['category_id'],
                ]);
            }



        } catch(Exception $e) {
            return back()->withErrors($e->getMessage());
        }
        return redirect()->route('order.index');
    }

    public function index(){
        $orders = Order::latest()->get();

        // $today = Carbon::today();
        // $futureDay = $today->addDays(7)->format('Y-m-d');
        // $strtoday = $today->format('Y-m-d');
        foreach($orders as $order){
            if($order->reminder){
                $data = json_decode($order->reminder);
                $array = [];
                foreach($data as $dd){
                    $array[$dd]= $dd;
                }
            }
            $date = date('Y-m-d', strtotime('+7 days'));
            if($order->status == 'complete'){
                $order->msg = 'complete';
            }else{
            $order->msg  = isset($array[$date]) ? 'alert' : 'no';
            }

            // $date1 = date('Y-m-d', strtotime('+6 days'));
            // if($order->status == 'complete'){
            //     $order->msg = 'complete';
            // }else{
            // $order->msg  = isset($array[$date1]) ? 'alert' : 'no';
            // }
        }


        return view('index', compact('orders'));

    }

    public function edit_view($id){

        $orders = Order::where('id', $id)->get()->first();
        $categories = Category::all();
        $reminders = $orders->pluck('reminder')->toArray();
        //dd($reminders);

        return view('edit', compact('orders', 'categories', 'reminders', ));
    }

    public function edit(Request $request, $id){
    try {

        $order = Order::query()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'product_name' => ['required', 'string'],
            'start_date' => ['required', 'string'],
            'end_date' => ['required', 'string'],
            'price' => ['required', 'string'],
            'reminder' => ['required', 'string'],
            'reminder2' => ['required', 'string'],
            'company_name' => ['required', 'string'],
            'company_number' => ['required', 'string'],
            'agent_name' => ['required', 'string'],
            'agent_number' => ['required', 'string'],
            'attachment' => ['nullable'],
            'status'     => ['required'],
            'category_id'                 => ['required', 'exists:categories,id'],


        ]);


if($request->attachment =='') {
    $data= ([
        'product_name'          => $request['product_name'],
        'start_date'         => $request['start_date'],
        'end_date'     => $request['end_date'],
        'price'         => $request['price'],
        'reminder'         => json_encode($request['reminder']),
        'company_name'         => $request['company_name'],
        'company_number'         => $request['company_number'],
        'agent_name'         => $request['agent_name'],
        'agent_number'         => $request['agent_number'],
        'status'             => $request['status'],
        'category_id'                 => $request['category_id'],
    ]);
}else{
    $imageName = time().'.'.$request->attachment->extension();
    $request->attachment->move(public_path('images'), $imageName);
    $data= ([
        'product_name'          => $request['product_name'],
        'start_date'         => $request['start_date'],
        'end_date'     => $request['end_date'],
        'price'         => $request['price'],
        'reminder'         => json_encode($request['reminder']),
        'company_name'         => $request['company_name'],
        'company_number'         => $request['company_number'],
        'agent_name'         => $request['agent_name'],
        'agent_number'         => $request['agent_number'],
        'status'             => $request['status'],
        'category_id'        => $request['category_id'],
        'attachment'        => $imageName ]);
}

        $order->update($data);
    } catch(Exception $e) {
        return back()->withErrors($e->getMessage());
    }
    return redirect()->route('order.index');
    }

    public function trashed($id)
    {
        $order = Order::query()->findOrFail($id);
        $order->delete();
        return redirect()->route('order.index');
    }
}
