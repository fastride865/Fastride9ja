<?php

namespace App\Http\Controllers\Corporate;

//use App\Http\Controllers\Helper\Merchant;
use App\Models\EmployeeDesignation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Merchant;
use Auth;
use DB;
use App\Traits\MerchantTrait;

class EmployeeDesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use MerchantTrait;
    public function index()
    {
        $corporate = Auth::user('corporate');
        $designations = EmployeeDesignation::where([['corporate_id','=',$corporate->id],['merchant_id', '=',$corporate->merchant_id],['delete_status','=',NULL]])->paginate(20);
        return view('corporate.designation.index',compact('designations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'designation_name' => 'required',
            'expense_limit' => 'required|integer'
        ]);

        if ($validator->fails()){
            $msg = $validator->messages()->all();
            return redirect()->back()->with('error',$msg[0]);
        }
        $corporate = Auth::user('corporate');
        $getLastDesignationId = EmployeeDesignation::select('designation_id')->where([['merchant_id','=',$corporate->merchant_id],['corporate_id','=',$corporate->id],['delete_status','=', null]])->latest()->first();

        $string_file = $this->getStringFile($corporate->merchant_id);
        if(!empty($getLastDesignationId)){
            if ($getLastDesignationId->designation_id != NULL){
                $designationId = $getLastDesignationId->designation_id;
                $designationId = str_pad(++$designationId, 4, '0', STR_PAD_LEFT);
            }
        } else {
            $designationId = "0001";
        }

        DB::beginTransaction();
        try{
            EmployeeDesignation::create([
                'merchant_id' => $corporate->merchant_id,
                'corporate_id' => $corporate->id,
                'designation_id' => $designationId,
                'designation_name' => $request->designation_name,
                'designation_expense_limit' => $request->expense_limit
            ]);
        }catch (\Exception $e) {
            $message = $e->getMessage();
            p($message);
            // Rollback Transaction
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('employeeDesignation.index')->withSuccess(trans("$string_file.added_successfully"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function updateDesignation(Request $request){
        $validator = Validator::make($request->all(),[
            'designationId' => 'required',
            'designation_name' => 'required',
            'expense_limit' => 'required|integer'
        ]);

        if ($validator->fails()){
            $msg = $validator->messages()->all();
            return redirect()->back()->with('error',$msg[0]);
        }

        DB::beginTransaction();
        try{
            $corporate = Auth::user('corporate');
            $string_file = $this->getStringFile($corporate->merchant_id);
            EmployeeDesignation::where('id',$request->designationId)
            ->update(
                [
                    'designation_name' => $request->designation_name,
                    'designation_expense_limit' => $request->expense_limit
                ]);
        }catch (\Exception $e){
            $msg = $e->getMessage();
            p($msg);
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('employeeDesignation.index')->withSuccess(trans("$string_file.saved_successfully"));
    }

    public function Delete(Request $request){
        $validator = Validator::make($request->all(),
            [
                'designationId' => 'required|integer'
            ]
        );

        if ($validator->fails()){
            $error = $validator->messages()->all();
            return redirect()->back()->with('error',$error[0]);
        }

        DB::beginTransaction();
        try{
            $corporate = Auth::user('corporate');
            $string_file = $this->getStringFile($corporate->merchant_id);
            EmployeeDesignation::where('id',$request->designationId)->update(['delete_status' => 1]);
        }catch (\Exception $e){
            $msg = $e->getMessage();
            p($msg);
            DB::rollback();
        }
        DB::commit();
        return redirect()->route('employeeDesignation.index')->withSuccess(trans("$string_file.deleted"));
    }
}
