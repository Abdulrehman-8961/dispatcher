<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use Hash;
use DateTime;
use DateInterval;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use App\Mail\DescriptionEmail;
use Illuminate\Support\Facades\Mail;

class Main extends Controller
{
    public function index(){
        return view('main');
    }

    public function owners(){
        return view('Owners/owners');
    }

    public function percSettings(){
        return view('settings/settings');
    }



    public function add(){
        return view('Owners/add');
    }

    public function truck(){
        return view('Truck/view');
    }

    public function addTruck(){
        return view('Truck/add');
    }

    public function drivers(){
        return view('Drivers/view');
    }

    public function old_drivers(){
        return view('Drivers/rehire');
    }

    public function old_trucks(){
        return view('Truck/rehire');
    }

    public function old_owners(){
        return view('Owners/rehire');
    }

    public function old_dispatcher(){
        return view('Dispatcher/rehire');
    }

    public function old_company(){
        return view('company/rehire');
    }

    public function addDriver(){
        return view('Drivers/add');
    }

    public function dispatch_view(){
        return view('Truck/truck_dispatch');
    }

    public function updateSettings(Request $request){
        DB::table('settings')->update(['value_1'=>$request->value_1,'value_2'=>$request->value_2]);
        return redirect()->back();
    }


    public function saveOwner(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'company' => 'required|string|max:255|unique:owners,company_name',
            'owner_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'ssn' => 'required|string|max:20|unique:owners,ssn',
            'email' => 'required|email|max:255',
            'routing_number' => 'required|string|max:20',
            'account_number' => 'required|string|max:20',
        ]);

        // Upload and store the Driver License file
        if ($request->hasFile('license')) {
            // $licensePath = $request->file('license')->store('public/uploads');
            $fileName = time().'.'.$request->file('license')->extension();
        $request->file('license')->move(public_path('uploads'), $fileName);
        } else {
            $licensePath = null;
        }

        // Insert data into the "owners" table
        $id=DB::table('owners')->insertGetId([
            'company_name' => $request->input('company'),
            'owner_name' => $request->input('owner_name'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone_number'),
            'ssn' => $request->input('ssn'),
            'email' => $request->input('email'),
            'routing' => $request->input('routing_number'),
            'account' => $request->input('account_number'),
            'license' => $fileName,
        ]);
        if ($request->hasFile('license')) {
            DB::table('archived_owner')->insert([
                'owner_id'=>$id,
                'name'=>'License',
                'document_name'=>$fileName,
            ]);
        }
        // You can return a response if needed
        return response()->json(['message' => 'Owner data saved successfully']);
    }

    public function editowner($id){
        $data=DB::table('owners')->where('id',$id)->first();
        return view('Owners.edit',compact('data'));
    }

    public function updateowner(Request $request,$id)
    {
        // dd($id);
        // Validate the form data
        $validatedData = $request->validate([
            // 'company' => 'required|string|max:255|unique:owners,company_name',
            'company' => [
                'required',
                'string',
                'max:255',
                Rule::unique('owners', 'company_name')->ignore($request->id),
            ],
            'owner_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'ssn' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'routing_number' => 'required|string|max:20',
            'account_number' => 'required|string|max:20',
        ]);

        // Upload and store the Driver License file
        if ($request->hasFile('license')) {
            // $licensePath = $request->file('license')->store('public/uploads');
            $fileName = time().'.'.$request->file('license')->extension();
        $request->file('license')->move(public_path('uploads'), $fileName);
        DB::table('owners')->where('id',$id)->update([
            'license'=>$fileName
        ]);
        } else {
            $licensePath = null;
        }

        // Insert data into the "owners" table
        DB::table('owners')->where('id',$id)->update([
            'company_name' => $request->input('company'),
            'owner_name' => $request->input('owner_name'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone_number'),
            'ssn' => $request->input('ssn'),
            'email' => $request->input('email'),
            'routing' => $request->input('routing_number'),
            'account' => $request->input('account_number'),
        ]);
        if ($request->hasFile('license')) {
            DB::table('archived_owner')->insert([
                'owner_id'=>$id,
                'name'=>'License',
                'document_name'=>$fileName,
            ]);
        }
        // You can return a response if needed
        return response()->json(['message' => 'Owner data Updated successfully']);
    }

    public function saveTruck(Request $request)
{

    // Validate the form data
    $validatedData = $request->validate([
        'company' => 'required',
        'truck_number' => ['required','unique:truck,truck_number'],
        'vin' => ['required','unique:truck,vin'],
        'year' => 'required',
        'make' => 'required',
        'model' => 'required',
        'license_plate' => 'required',
        'truck_address' => 'required',
        // 'trailer_number' => 'required',
        'renew_date' => 'required',
        'dot_sticker_date' => 'required',
        'damage_insurance_date' => 'required',
        'policy_number' => 'required',
        'effective_Date' => 'required',
        'renewal_date_2290'=>'required',
        'damage_expiry' => 'required',
        // 'reg_renew' => 'required',
        'w9' => 'required|file|mimes:pdf,jpeg,png',
        'saftey_report' => 'required|file|mimes:pdf,jpeg,png',
        'cab_card' => 'required|file|mimes:pdf,jpeg,png',
        'truck_lease' => 'required|file|mimes:pdf,jpeg,png',
         'inspection' => 'required|file|mimes:pdf,jpeg,png',
        '2290_document' => 'required|file|mimes:pdf,jpeg,png',

    ]);

    // Handle file uploads and save them to the server
    $filePaths = [];
    if ($request->hasFile('cab_card')) {
        $filePaths['cab_card'] = "cab_card".time() . '.' . $request->file('cab_card')->extension();
        $request->file('cab_card')->move(public_path('uploads'), $filePaths['cab_card']);
    }

    if ($request->hasFile('inspection')) {
        $filePaths['inspection'] = "inspection".time() . '.' . $request->file('inspection')->extension();
        $request->file('inspection')->move(public_path('uploads'), $filePaths['inspection']);
    }

    if ($request->hasFile('truck_lease')) {
        $filePaths['truck_lease'] = "truck_lease".time() . '.' . $request->file('truck_lease')->extension();
        $request->file('truck_lease')->move(public_path('uploads'), $filePaths['truck_lease']);
    }

    if ($request->hasFile('trailer_reg')) {
        $filePaths['trailer_reg'] = "trailer_reg".time() . '.' . $request->file('trailer_reg')->extension();
        $request->file('trailer_reg')->move(public_path('uploads'), $filePaths['trailer_reg']);
    }

    if ($request->hasFile('physical_damage')) {
        $filePaths['physical_damage'] = "physical_damage".time() . '.' . $request->file('physical_damage')->extension();
        $request->file('physical_damage')->move(public_path('uploads'), $filePaths['physical_damage']);
    }
    if ($request->hasFile('damage_notice')) {
        $filePaths['damage_notice'] = "damage_notice".time() . '.' . $request->file('damage_notice')->extension();
        $request->file('damage_notice')->move(public_path('uploads'), $filePaths['damage_notice']);
    }
    if ($request->hasFile('2290_document')) {
        $filePaths['2290_document'] = "2290_document".time() . '.' . $request->file('2290_document')->extension();
        $request->file('2290_document')->move(public_path('uploads'), $filePaths['2290_document']);
    }

    // if ($request->hasFile('numbered_doc')) {
    //     $filePaths['numbered_doc'] = "numbered_doc".time() . '.' . $request->file('numbered_doc')->extension();
    //     $request->file('numbered_doc')->move(public_path('uploads'), $filePaths['numbered_doc']);
    // }

    if ($request->hasFile('w9')) {
        $filePaths['w9'] = "w9_".time() . '.' . $request->file('w9')->extension();
        $request->file('w9')->move(public_path('uploads'), $filePaths['w9']);
    }
    if ($request->hasFile('saftey_report')) {
        $filePaths['saftey_report'] = "saftey_report".time() . '.' . $request->file('saftey_report')->extension();
        $request->file('saftey_report')->move(public_path('uploads'), $filePaths['saftey_report']);
    }
    if ($request->hasFile('truck_pics')) {
        $filePaths['truck_pics'] = [];

        if ($request->hasfile('truck_pics')) {
            $i = 0;
            foreach ($request->file('truck_pics') as $file) {
                $name = "truck_pics".$i.time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $name);
                $filePaths['truck_pics'][] = $name;
                $i++;
            }
        }
    }
    // Save truck information to the "trucks" table
    $id=DB::table('truck')->insertGetId([
        'company_id' => $request->input('company'),
        'truck_number' => $request->input('truck_number'),
        'vin' => $request->input('vin'),
        'year' => $request->input('year'),
        'make' => $request->input('make'),
        'model' => $request->input('model'),
        'plate_number' => $request->input('license_plate'),
        'truck_address' => $request->input('truck_address'),
        'trailer' => $request->input('trailer_number'),
        'card_renew_date' => $request->input('renew_date'),
        'sticker_renew_date' => $request->input('dot_sticker_date'),
        'damage_insurance_name' => $request->input('damage_insurance_date'),
        'insurance_policy_number' => $request->input('policy_number'),
        'damage_effective_date' => $request->input('effective_Date'),
        'damage_expiry_date' => $request->input('damage_expiry'),
        'trailer_reg_renew_date' => $request->input('reg_renew'),
        'renewal_date_2290' => $request->input('renewal_date_2290'),
        'status' => 'active',
        'car_cab' => $filePaths['cab_card'] ?? null,
        'truck_lease' => $filePaths['truck_lease'] ?? null,
        'trailer_reg' => $filePaths['trailer_reg'] ?? null,
        'physical_damage' => $filePaths['physical_damage'] ?? null,
        'physical_notice' => $filePaths['damage_notice'] ?? null,
        '2290_document' => $filePaths['2290_document'] ?? null,
        'w9' => $filePaths['w9'] ?? null,
        'inspection' => $filePaths['inspection'] ?? null,
        'saftey_report' => $filePaths['saftey_report'] ?? null,
        // 'number_file' => $filePaths['numbered_doc'] ?? null,

        'four_pics' => json_encode($filePaths['truck_pics'] ?? []),
    ]);
    if ($request->hasFile('cab_card')) {
        DB::table('archived')->insert([
            'truck_id'=>$id,
            'name'=>'Cab Card',
            'document_name'=>$filePaths['cab_card'],
        ]);
        }
        if ($request->hasFile('2290_document')) {
            DB::table('archived')->insert([
                'truck_id'=>$id,
                'name'=>'2290',
                'document_name'=>$filePaths['2290_document'],
            ]);
            }
        if ($request->hasFile('inspection')) {
            DB::table('archived')->insert([
                'truck_id'=>$id,
                'name'=>'Inspection',
                'document_name'=>$filePaths['inspection'],
            ]);
            }
            if ($request->hasFile('truck_lease')) {
                DB::table('archived')->insert([
                    'truck_id'=>$id,
                    'name'=>'Truck Lease',
                    'document_name'=>$filePaths['truck_lease'],
                ]);
                }

                if ($request->hasFile('trailer_reg')) {
                    DB::table('archived')->insert([
                        'truck_id'=>$id,
                        'name'=>'Trailer reg',
                        'document_name'=>$filePaths['trailer_reg'],
                    ]);
                    }
                    if ($request->hasFile('physical_damage')) {
                        DB::table('archived')->insert([
                            'truck_id'=>$id,
                            'name'=>'Physical Damage',
                            'document_name'=>$filePaths['physical_damage'],
                        ]);
                        }
                        if ($request->hasFile('damage_notice')) {
                            DB::table('archived')->insert([
                                'truck_id'=>$id,
                                'name'=>'Damage Notice',
                                'document_name'=>$filePaths['damage_notice'],
                            ]);
                            }
                            if ($request->hasFile('w9')) {
                                DB::table('archived')->insert([
                                    'truck_id'=>$id,
                                    'name'=>'W9',
                                    'document_name'=>$filePaths['w9'],
                                ]);
                                }
                                if ($request->hasFile('saftey_report')) {
                                    DB::table('archived')->insert([
                                        'truck_id'=>$id,
                                        'name'=>'Safety Report',
                                        'document_name'=>$filePaths['saftey_report'],
                                    ]);
                                    }
    // You can return a response to indicate success
    return response()->json(['message' => 'Truck data saved successfully']);
}

public function edittruck($id){
    $data=DB::table('truck')->where('id',$id)->first();
    return view('Truck.edit',compact('data'));
}
public function updatetruck(Request $request,$id)
{

    // Validate the form data
    $validatedData = $request->validate([
        'company' => 'required',
        'truck_number' => 'required',
        'vin' => 'required',
        'year' => 'required',
        'make' => 'required',
        'model' => 'required',
        'license_plate' => 'required',
        'truck_address' => 'required',
        // 'trailer_number' => 'required',
        'renew_date' => 'required',
        'dot_sticker_date' => 'required',
        'damage_insurance_date' => 'required',
        'policy_number' => 'required',
        'effective_Date' => 'required',
        'renewal_date_2290'=>'required',
        'damage_expiry' => 'required',
        // 'reg_renew' => 'required',
        'w9' => 'file|mimes:pdf,jpeg,png',
        'saftey_report' => 'file|mimes:pdf,jpeg,png',
        'cab_card' => 'file|mimes:pdf,jpeg,png',
        'truck_lease' => 'file|mimes:pdf,jpeg,png',
         'inspection' => 'file|mimes:pdf,jpeg,png',
        'numbered_doc' => 'file|mimes:pdf,jpeg,png',
        '2290_document' => 'file|mimes:pdf,jpeg,png',

    ]);

    // Handle file uploads and save them to the server
    $filePaths = [];
    if ($request->hasFile('cab_card')) {
        $filePaths['cab_card'] = "cab_card".time() . '.' . $request->file('cab_card')->extension();
        $request->file('cab_card')->move(public_path('uploads'), $filePaths['cab_card']);
        DB::table('truck')->where('id',$id)->update(['car_cab'=>$filePaths['cab_card']]);
    }

    if ($request->hasFile('inspection')) {
        $filePaths['inspection'] = "inspection".time() . '.' . $request->file('inspection')->extension();
        $request->file('inspection')->move(public_path('uploads'), $filePaths['inspection']);
        DB::table('truck')->where('id',$id)->update(['inspection'=>$filePaths['inspection']]);
    }

    if ($request->hasFile('truck_lease')) {
        $filePaths['truck_lease'] = "truck_lease".time() . '.' . $request->file('truck_lease')->extension();
        $request->file('truck_lease')->move(public_path('uploads'), $filePaths['truck_lease']);
        DB::table('truck')->where('id',$id)->update(['truck_lease'=>$filePaths['truck_lease']]);
    }

    if ($request->hasFile('trailer_reg')) {
        $filePaths['trailer_reg'] = "trailer_reg".time() . '.' . $request->file('trailer_reg')->extension();
        $request->file('trailer_reg')->move(public_path('uploads'), $filePaths['trailer_reg']);
        DB::table('truck')->where('id',$id)->update(['trailer_reg'=>$filePaths['trailer_reg']]);
    }

    if ($request->hasFile('physical_damage')) {
        $filePaths['physical_damage'] = "physical_damage".time() . '.' . $request->file('physical_damage')->extension();
        $request->file('physical_damage')->move(public_path('uploads'), $filePaths['physical_damage']);
        DB::table('truck')->where('id',$id)->update(['physical_damage'=>$filePaths['physical_damage']]);

    }
    if ($request->hasFile('damage_notice')) {
        $filePaths['damage_notice'] = "damage_notice".time() . '.' . $request->file('damage_notice')->extension();
        $request->file('damage_notice')->move(public_path('uploads'), $filePaths['damage_notice']);
        DB::table('truck')->where('id',$id)->update(['physical_notice'=>$filePaths['damage_notice']]);
    }

    if ($request->hasFile('2290_document')) {
        $filePaths['2290_document'] = "2290_document".time() . '.' . $request->file('2290_document')->extension();
        $request->file('2290_document')->move(public_path('uploads'), $filePaths['2290_document']);
        DB::table('truck')->where('id',$id)->update(['physical_notice'=>$filePaths['2290_document']]);
    }

    // if ($request->hasFile('numbered_doc')) {
    //     $filePaths['numbered_doc'] = "numbered_doc".time() . '.' . $request->file('numbered_doc')->extension();
    //     $request->file('numbered_doc')->move(public_path('uploads'), $filePaths['numbered_doc']);
    // }

    if ($request->hasFile('w9')) {
        $filePaths['w9'] = "w9_".time() . '.' . $request->file('w9')->extension();
        $request->file('w9')->move(public_path('uploads'), $filePaths['w9']);
        DB::table('truck')->where('id',$id)->update(['w9'=>$filePaths['w9']]);
    }

    if ($request->hasFile('saftey_report')) {
        $filePaths['saftey_report'] = "saftey_report".time() . '.' . $request->file('saftey_report')->extension();
        $request->file('saftey_report')->move(public_path('uploads'), $filePaths['saftey_report']);
        DB::table('truck')->where('id',$id)->update(['saftey_report'=>$filePaths['saftey_report']]);
    }
    if ($request->hasFile('truck_pics')) {
        $filePaths['truck_pics'] = [];

        if ($request->hasfile('truck_pics')) {
            $i = 0;
            foreach ($request->file('truck_pics') as $file) {
                $name = "truck_pics".$i.time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $name);
                $filePaths['truck_pics'][] = $name;
                $i++;
                DB::table('truck')->where('id',$id)->update(['four_pics'=>$name]);
            }
        }
    }

    // Save truck information to the "trucks" table
    DB::table('truck')->where('id',$id)->update([
        'company_id' => $request->input('company'),
        'truck_number' => $request->input('truck_number'),
        'vin' => $request->input('vin'),
        'year' => $request->input('year'),
        'make' => $request->input('make'),
        'model' => $request->input('model'),
        'plate_number' => $request->input('license_plate'),
        'truck_address' => $request->input('truck_address'),
        'trailer' => $request->input('trailer_number'),
        'card_renew_date' => $request->input('renew_date'),
        'sticker_renew_date' => $request->input('dot_sticker_date'),
        'damage_insurance_name' => $request->input('damage_insurance_date'),
        'insurance_policy_number' => $request->input('policy_number'),
        'damage_effective_date' => $request->input('effective_Date'),
        'damage_expiry_date' => $request->input('damage_expiry'),
        'trailer_reg_renew_date' => $request->input('reg_renew'),
        'renewal_date_2290' => $request->input('renewal_date_2290'),
        'truck_leave_date' => $request->input('truck_leave_date'),
        'status' => 'active',
        // 'car_cab' => $filePaths['cab_card'] ?? null,
        // 'truck_lease' => $filePaths['truck_lease'] ?? null,
        // 'trailer_reg' => $filePaths['trailer_reg'] ?? null,
        // 'physical_damage' => $filePaths['physical_damage'] ?? null,
        // 'physical_notice' => $filePaths['damage_notice'] ?? null,
        // 'w9' => $filePaths['w9'] ?? null,
        // 'inspection' => $filePaths['inspection'] ?? null,
        // 'saftey_report' => $filePaths['saftey_report'] ?? null,
        // 'number_file' => $filePaths['numbered_doc'] ?? null,

        // 'four_pics' => json_encode($filePaths['truck_pics'] ?? []),
    ]);
    if ($request->hasFile('cab_card')) {
        DB::table('archived')->insert([
            'truck_id'=>$id,
            'name'=>'Cab Card',
            'document_name'=>$filePaths['cab_card'],
        ]);
    }
    if ($request->hasFile('2290_document')) {
        DB::table('archived')->insert([
            'truck_id'=>$id,
            'name'=>'2290',
            'document_name'=>$filePaths['2290_document'],
        ]);
    }
    if ($request->hasFile('inspection')) {
        DB::table('archived')->insert([
            'truck_id'=>$id,
            'name'=>'Inspection',
            'document_name'=>$filePaths['inspection'],
        ]);
    }
    if ($request->hasFile('truck_lease')) {
        DB::table('archived')->insert([
                    'truck_id'=>$id,
                    'name'=>'Truck lease',
                    'document_name'=>$filePaths['truck_lease'],
                ]);
                }

                if ($request->hasFile('trailer_reg')) {
                    DB::table('archived')->insert([
                        'truck_id'=>$id,
                        'name'=>'Trailer reg',
                        'document_name'=>$filePaths['trailer_reg'],
                    ]);
                    }
                    if ($request->hasFile('physical_damage')) {
                        DB::table('archived')->insert([
                            'truck_id'=>$id,
                            'name'=>'Physical demage',
                            'document_name'=>$filePaths['physical_damage'],
                        ]);
                    }
                    if ($request->hasFile('damage_notice')) {
                        DB::table('archived')->insert([
                            'truck_id'=>$id,
                            'name'=>'Demage Notice',
                            'document_name'=>$filePaths['damage_notice'],
                        ]);
                    }
                    if ($request->hasFile('w9')) {
                        DB::table('archived')->insert([
                            'truck_id'=>$id,
                            'name'=>'W9',
                            'document_name'=>$filePaths['w9'],
                        ]);
                    }
                    if ($request->hasFile('saftey_report')) {
                        DB::table('archived')->insert([
                            'truck_id'=>$id,
                            'name'=>'Safety Report',
                                        'document_name'=>$filePaths['saftey_report'],
                                    ]);
                                    }
    // You can return a response to indicate success
    return response()->json(['message' => 'Truck data update successfully']);
}

    public function saveDriver(Request $request){
        $validatedData = $request->validate([
            'driver_name' => 'required|string|max:255',
            // 'truck_number' => 'required|string|max:255',
            'truck_id' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'license' => 'required|string|max:255|unique:drivers,driver_license',
            'address' => 'required|string|max:255',
            'license_issue_date' => 'required|date',
            'license_expiry_date' => 'required|date',
            'medical_issue_date' => 'required|date',
            'hired_date' => 'required|date',
            'medical_expiry_date' => 'required|date',
            'ssn_number' => 'required|string|max:20|unique:drivers,ssn',

            'medical_card' => 'required|file|mimes:pdf,jpeg,png',
            'drug_test' => 'required|file|mimes:pdf,jpeg,png',
            'driver_license_file' => 'required|file|mimes:pdf,jpeg,png',
            'mvr' => 'required|file|mimes:pdf,jpeg,png',
            'employment_app' => 'required|file|mimes:pdf,jpeg,png',
            'clearing_house' => 'required|file|mimes:pdf,jpeg,png',
            'orentation' => 'required|file|mimes:pdf,jpeg,png',
            'emergency_contact' => 'required|file|mimes:pdf,jpeg,png',
            'ssn_pic' => 'required|file|mimes:pdf,jpeg,png',
        ]);

        // Handle file uploads and save them to the server
        $filePaths = [];

        if ($request->hasFile('medical_card')) {
            $filePaths['medical_card'] = "medical_card_" . time() . '.' . $request->file('medical_card')->extension();
            $request->file('medical_card')->move(public_path('uploads'), $filePaths['medical_card']);
        }
        if ($request->hasFile('drug_test')) {
            $filePaths['drug_test'] = "drug_test".time() . '.' . $request->file('drug_test')->extension();
            $request->file('drug_test')->move(public_path('uploads'), $filePaths['drug_test']);
        }

        if ($request->hasFile('driver_license_file')) {
            $filePaths['driver_license_file'] = "driver_license_file".time() . '.' . $request->file('driver_license_file')->extension();
            $request->file('driver_license_file')->move(public_path('uploads'), $filePaths['driver_license_file']);
        }

        if ($request->hasFile('mvr')) {
            $filePaths['mvr'] = "mvr".time() . '.' . $request->file('mvr')->extension();
            $request->file('mvr')->move(public_path('uploads'), $filePaths['mvr']);
        }

        if ($request->hasFile('employment_app')) {
            $filePaths['employment_app'] = "employment_app".time() . '.' . $request->file('employment_app')->extension();
            $request->file('employment_app')->move(public_path('uploads'), $filePaths['employment_app']);
        }

        if ($request->hasFile('clearing_house')) {
            $filePaths['clearing_house'] = "clearing_house".time() . '.' . $request->file('clearing_house')->extension();
            $request->file('clearing_house')->move(public_path('uploads'), $filePaths['clearing_house']);
        }
        if ($request->hasFile('orentation')) {
            $filePaths['orentation'] = "orentation".time() . '.' . $request->file('orentation')->extension();
            $request->file('orentation')->move(public_path('uploads'), $filePaths['orentation']);
        }

        if ($request->hasFile('emergency_contact')) {
            $filePaths['emergency_contact'] = "emergency_contact".time() . '.' . $request->file('emergency_contact')->extension();
            $request->file('emergency_contact')->move(public_path('uploads'), $filePaths['emergency_contact']);
        }

        if ($request->hasFile('ssn_pic')) {
            $filePaths['ssn_pic'] = "ssn_pic_".time() . '.' . $request->file('ssn_pic')->extension();
            $request->file('ssn_pic')->move(public_path('uploads'), $filePaths['ssn_pic']);
        }

        // Repeat the above block for other file uploads

        // Insert data into the "drivers" table
        $id=DB::table('drivers')->insertGetId([
            'driver_name' => $request->input('driver_name'),
            'truck_id' => $request->input('truck_id'),
            // 'truck_number' => $request->input('truck_number'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'driver_license' => $request->input('license'),
            'hired_date' => $request->input('hired_date'),
            'address' => $request->input('address'),
            'license_issue_date' => $request->input('license_issue_date'),
            'license_expiry_date' => $request->input('license_expiry_date'),
            'medical_issue_date' => $request->input('medical_issue_date'),
            'medical_expiry_date' => $request->input('medical_expiry_date'),
            'ssn' => $request->input('ssn_number'),
            'medical_card' => $filePaths['medical_card'] ?? null,
            'drug_test' => $filePaths['drug_test'] ?? null,
            'license' => $filePaths['driver_license_file'] ?? null,
            'mvr' => $filePaths['mvr'] ?? null,
            'employment_application' => $filePaths['employment_app'] ?? null,
            'clearing_house' => $filePaths['clearing_house'] ?? null,
            'orientation' => $filePaths['orentation'] ?? null,
            'emergency_contact' => $filePaths['emergency_contact'] ?? null,
            'ssn_file' => $filePaths['ssn_pic'] ?? null,
            // Add the rest of your database fields here
        ]);
        if ($request->hasFile('medical_card')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Medical card',
                'document_name'=>$filePaths['medical_card'],
            ]);
        }if ($request->hasFile('drug_test')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Drug test',
                'document_name'=>$filePaths['drug_test'],
            ]);
        }if ($request->hasFile('driver_license_file')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Driver license file',
                'document_name'=>$filePaths['driver_license_file'],
            ]);
        }if ($request->hasFile('mvr')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'MVR',
                'document_name'=>$filePaths['mvr'],
            ]);
        }if ($request->hasFile('employment_app')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Employment app',
                'document_name'=>$filePaths['employment_app'],
            ]);
        }if ($request->hasFile('clearing_house')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Clearing house',
                'document_name'=>$filePaths['clearing_house'],
            ]);
        }if ($request->hasFile('orentation')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Orentation',
                'document_name'=>$filePaths['orentation'],
            ]);
        }if ($request->hasFile('emergency_contact')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Emergency contact',
                'document_name'=>$filePaths['emergency_contact'],
            ]);
        }if ($request->hasFile('ssn_pic')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'SSN',
                'document_name'=>$filePaths['ssn_pic'],
            ]);
        }
        // You can return a response to indicate success
        return response()->json(['message' => 'Driver data saved successfully']);
    }

    public function editdriver($id){
        $data=DB::table('drivers')->where('id',$id)->first();
        return view('Drivers.edit',compact('data'));
    }
    public function updatedriver(Request $request,$id){
        $validatedData = $request->validate([
            'driver_name' => 'required|string|max:255',
            // 'truck_number' => 'required|string|max:255',
            'truck_id' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'license' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'license_issue_date' => 'required|date',
            'license_expiry_date' => 'required|date',
            'medical_issue_date' => 'required|date',
            'hired_date' => 'required|date',
            'medical_expiry_date' => 'required|date',
            'ssn_number' => 'required|string|max:20',

            'medical_card' => 'file|mimes:pdf,jpeg,png',
            'drug_test' => 'file|mimes:pdf,jpeg,png',
            'driver_license_file' => 'file|mimes:pdf,jpeg,png',
            'mvr' => 'file|mimes:pdf,jpeg,png',
            'employment_app' => 'file|mimes:pdf,jpeg,png',
            'clearing_house' => 'file|mimes:pdf,jpeg,png',
            'orentation' => 'file|mimes:pdf,jpeg,png',
            'emergency_contact' => 'file|mimes:pdf,jpeg,png',
            'ssn_pic' => 'file|mimes:pdf,jpeg,png',
        ]);

        // Handle file uploads and save them to the server
        $filePaths = [];

        if ($request->hasFile('medical_card')) {
            $filePaths['medical_card'] = "medical_card_" . time() . '.' . $request->file('medical_card')->extension();
            $request->file('medical_card')->move(public_path('uploads'), $filePaths['medical_card']);
            DB::table('drivers')->where('id',$id)->update(['medical_card' => $filePaths['medical_card']]);
        }
        if ($request->hasFile('drug_test')) {
            $filePaths['drug_test'] = "drug_test".time() . '.' . $request->file('drug_test')->extension();
            $request->file('drug_test')->move(public_path('uploads'), $filePaths['drug_test']);
            DB::table('drivers')->where('id',$id)->update(['drug_test'=>$filePaths['drug_test']]);
        }

        if ($request->hasFile('driver_license_file')) {
            $filePaths['driver_license_file'] = "driver_license_file".time() . '.' . $request->file('driver_license_file')->extension();
            $request->file('driver_license_file')->move(public_path('uploads'), $filePaths['driver_license_file']);
            DB::table('drivers')->where('id',$id)->update(['license'=>$filePaths['driver_license_file']]);
        }

        if ($request->hasFile('mvr')) {
            $filePaths['mvr'] = "mvr".time() . '.' . $request->file('mvr')->extension();
            $request->file('mvr')->move(public_path('uploads'), $filePaths['mvr']);
            DB::table('drivers')->where('id',$id)->update(['mvr'=>$filePaths['mvr']]);
        }

        if ($request->hasFile('employment_app')) {
            $filePaths['employment_app'] = "employment_app".time() . '.' . $request->file('employment_app')->extension();
            $request->file('employment_app')->move(public_path('uploads'), $filePaths['employment_app']);
            DB::table('drivers')->where('id',$id)->update(['employment_application'=>$filePaths['employment_app']]);
        }
        if ($request->hasFile('clearing_house')) {
            $filePaths['clearing_house'] = "clearing_house".time() . '.' . $request->file('clearing_house')->extension();
            $request->file('clearing_house')->move(public_path('uploads'), $filePaths['clearing_house']);
            DB::table('drivers')->where('id',$id)->update(['clearing_house'=>$filePaths['clearing_house']]);
        }
        if ($request->hasFile('orentation')) {
            $filePaths['orentation'] = "orentation".time() . '.' . $request->file('orentation')->extension();
            $request->file('orentation')->move(public_path('uploads'), $filePaths['orentation']);
            DB::table('drivers')->where('id',$id)->update(['orientation'=>$filePaths['orentation']]);
        }

        if ($request->hasFile('emergency_contact')) {
            $filePaths['emergency_contact'] = "emergency_contact".time() . '.' . $request->file('emergency_contact')->extension();
            $request->file('emergency_contact')->move(public_path('uploads'), $filePaths['emergency_contact']);
            DB::table('drivers')->where('id',$id)->update(['emergency_contact'=>$filePaths['emergency_contact']]);
        }

        if ($request->hasFile('ssn_pic')) {
            $filePaths['ssn_pic'] = "ssn_pic_".time() . '.' . $request->file('ssn_pic')->extension();
            $request->file('ssn_pic')->move(public_path('uploads'), $filePaths['ssn_pic']);
            DB::table('drivers')->where('id',$id)->update(['ssn_file'=>$filePaths['ssn_pic']]);
        }

        // Repeat the above block for other file uploads

        // Insert data into the "drivers" table
        DB::table('drivers')->where('id',$id)->update([
            'driver_name' => $request->input('driver_name'),
            'truck_id' => $request->input('truck_id'),
            // 'truck_number' => $request->input('truck_number'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'driver_license' => $request->input('license'),
            'hired_date' => $request->input('hired_date'),
            'address' => $request->input('address'),
            'license_issue_date' => $request->input('license_issue_date'),
            'license_expiry_date' => $request->input('license_expiry_date'),
            'medical_issue_date' => $request->input('medical_issue_date'),
            'medical_expiry_date' => $request->input('medical_expiry_date'),
            'ssn' => $request->input('ssn_number'),

            // Add the rest of your database fields here
        ]);
        if ($request->hasFile('medical_card')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Medical card',
                'document_name'=>$filePaths['medical_card'],
            ]);
        }if ($request->hasFile('drug_test')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Drug test',
                'document_name'=>$filePaths['drug_test'],
            ]);
        }if ($request->hasFile('driver_license_file')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Driver license file',
                'document_name'=>$filePaths['driver_license_file'],
            ]);
        }if ($request->hasFile('mvr')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'MVR',
                'document_name'=>$filePaths['mvr'],
            ]);
        }if ($request->hasFile('employment_app')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Employment app',
                'document_name'=>$filePaths['employment_app'],
            ]);
        }if ($request->hasFile('clearing_house')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Clearing house',
                'document_name'=>$filePaths['clearing_house'],
            ]);
        }if ($request->hasFile('orentation')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Orentation',
                'document_name'=>$filePaths['orentation'],
            ]);
        }if ($request->hasFile('emergency_contact')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'Emergency contact',
                'document_name'=>$filePaths['emergency_contact'],
            ]);
        }if ($request->hasFile('ssn_pic')) {
            DB::table('archived_driver')->insert([
                'driver_id'=>$id,
                'name'=>'SSN',
                'document_name'=>$filePaths['ssn_pic'],
            ]);
        }
        // You can return a response to indicate success
        return response()->json(['message' => 'Driver data update successfully']);
    }
    public function addDispatcher(){
        return view('Dispatcher/add');
    }

    public function saveDispatcher(Request $request){
    //    dd('hi');
        $validatedData = $request->validate([
            'dispatcher_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'salary' => 'required|numeric',
            'truck_id' => 'required',
            'address' => 'required|string|max:255',
            'routing_number' => 'required|string|max:20',
            'account_number' => 'required|string|max:20',
            'driver_license_number' => 'required|string|max:255|unique:dispatchers,driver_license_number',
            'ssn_number' => 'required|string|max:20|unique:dispatchers,ssn_number',
            'driver_licenses' => 'required|file|mimes:pdf,jpeg,png',
            'ssn_pic' => 'required|file|mimes:pdf,jpeg,png',
        ]);

        // Handle file uploads and save them to the server
        $filePaths = [];

        if ($request->hasFile('driver_licenses')) {
            $filePaths['driver_licenses'] = "driver_licenses" . time() . '.' . $request->file('driver_licenses')->extension();
            $request->file('driver_licenses')->move(public_path('uploads'), $filePaths['driver_licenses']);
        }

        if ($request->hasFile('ssn_pic')) {
            $filePaths['ssn_pic'] = "ssn_pic" . time() . '.' . $request->file('ssn_pic')->extension();
            $request->file('ssn_pic')->move(public_path('uploads'), $filePaths['ssn_pic']);
        }

        // Repeat the above block for other file uploads

        // Insert data into the "dispatchers" table
        // $truck_id=$request->input('truck_id');
        // dd(json);
        $id=DB::table('dispatchers')->insertGetId([
            'dispatcher_name' => $request->input('dispatcher_name'),
            'phone' => $request->input('phone'),
            'truck_id' => isset($request->truck_id)?implode(',',$request->truck_id):'',
            'email' => $request->input('email'),
            'salary' => $request->input('salary'),
            'address' => $request->input('address'),
            'routing_number' => $request->input('routing_number'),
            'account_number' => $request->input('account_number'),
            'driver_license_number' => $request->input('driver_license_number'),
            'ssn_number' => $request->input('ssn_number'),
            'driver_licenses_path' => $filePaths['driver_licenses'] ?? null,
            'ssn_pic_path' => $filePaths['ssn_pic'] ?? null,
            // Add the rest of your fields here
        ]);
        if ($request->hasFile('driver_licenses')) {
            DB::table('archived_dispatcher')->insert([
                'dispatcher_id'=>$id,
                'name'=>'Driver licenses',
                'document_name'=>$filePaths['driver_licenses'],
            ]);
        }
        if ($request->hasFile('ssn_pic')) {
            DB::table('archived_dispatcher')->insert([
                'dispatcher_id'=>$id,
                'name'=>'SSN',
                'document_name'=>$filePaths['ssn_pic'],
            ]);
        }
        // You can return a response to indicate success
        return response()->json(['message' => 'Dispatcher data saved successfully']);
    }

    public function editdispatcher($id){
        $data=DB::table('dispatchers')->where('id',$id)->first();
        return view('Dispatcher.edit',compact('data'));
    }

    public function updatedispatcher(Request $request,$id){
        $validatedData = $request->validate([
            'dispatcher_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'truck_id' => 'required',
            'email' => 'required|email|max:255',
            'salary' => 'required|numeric',
            'address' => 'required|string|max:255',
            'routing_number' => 'required|string|max:20',
            'account_number' => 'required|string|max:20',
            'driver_license_number' => 'required|string|max:255',
            'ssn_number' => 'required|string|max:20',
            'driver_licenses' => 'file|mimes:pdf,jpeg,png',
            'ssn_pic' => 'file|mimes:pdf,jpeg,png',
        ]);

        // Handle file uploads and save them to the server
        $filePaths = [];

        if ($request->hasFile('driver_licenses')) {
            $filePaths['driver_licenses'] = "driver_licenses" . time() . '.' . $request->file('driver_licenses')->extension();
            $request->file('driver_licenses')->move(public_path('uploads'), $filePaths['driver_licenses']);
            DB::table('dispatchers')->where('id',$id)->update(['driver_licenses_path'=>$filePaths['driver_licenses']]);
        }

        if ($request->hasFile('ssn_pic')) {
            $filePaths['ssn_pic'] = "ssn_pic" . time() . '.' . $request->file('ssn_pic')->extension();
            $request->file('ssn_pic')->move(public_path('uploads'), $filePaths['ssn_pic']);
            DB::table('dispatchers')->where('id',$id)->update(['ssn_pic_path'=>$filePaths['ssn_pic']]);
        }

        // Repeat the above block for other file uploads

        // Insert data into the "dispatchers" table
        DB::table('dispatchers')->where('id',$id)->update([
            'dispatcher_name' => $request->input('dispatcher_name'),
            'phone' => $request->input('phone'),
            'truck_id' => isset($request->truck_id)?implode(',',$request->truck_id):'',
            'email' => $request->input('email'),
            'salary' => $request->input('salary'),
            'address' => $request->input('address'),
            'routing_number' => $request->input('routing_number'),
            'account_number' => $request->input('account_number'),
            'driver_license_number' => $request->input('driver_license_number'),
            'ssn_number' => $request->input('ssn_number'),
            // 'driver_licenses_path' => $filePaths['driver_licenses'] ?? null,
            // 'ssn_pic_path' => $filePaths['ssn_pic'] ?? null,
            // Add the rest of your fields here
        ]);
        if ($request->hasFile('driver_licenses')) {
            DB::table('archived_dispatcher')->insert([
                'dispatcher_id'=>$id,
                'name'=>'Driver licenses',
                'document_name'=>$filePaths['driver_licenses'],
            ]);
        }
        if ($request->hasFile('ssn_pic')) {
            DB::table('archived_dispatcher')->insert([
                'dispatcher_id'=>$id,
                'name'=>'SSN',
                'document_name'=>$filePaths['ssn_pic'],
            ]);
        }
        // You can return a response to indicate success
        return response()->json(['message' => 'Dispatcher data updated successfully']);
    }

    public function roster(){
        $activeDrivers = DB::table('drivers')->where('is_deleted', 0)->get();
        $inactiveDrivers = DB::table('drivers')->where('is_deleted', 1)->get();

        return view('Drivers/roster')->with(compact('activeDrivers', 'inactiveDrivers'));
    }

    public function activate($id){
        DB::table('drivers')->where('id', $id)->update(['is_deleted' => 0]);
        return redirect()->back();
    }

    public function deactivate($id){
        DB::table('drivers')->where('id', $id)->update(['is_deleted' => 1]);
        return redirect()->back();
    }

    public function deleteOwner($id){
        DB::table('owners')->where('id', $id)->update(['is_deleted' => 1]);
        return redirect()->back();
    }

    public function deleteDriver($id){
        DB::table('drivers')->where('id', $id)->update(['is_deleted' => 1]);
        return redirect()->back();
    }

    public function deleteTruck(Request $request,$id){
        if ($request->hasFile('termination_letter')) {
            $filePaths['termination_letter'] = "termination_letter".time() . '.' . $request->file('termination_letter')->extension();
            $request->file('termination_letter')->move(public_path('uploads'), $filePaths['termination_letter']);
        }
        DB::table('truck')->where('id', $id)->update(['is_deleted' => 1,'quite'=>1,'quite_date'=>date('Y-m-d H:i:s'),'termination_letter'=>$filePaths['termination_letter']]);
        return redirect()->back();
    }

    public function getOwnerDetails(Request $r){
        $id = $r->id;
        $owner = DB::table('owners')->where('id', $id)->first();
        return $owner;
    }

    public function getDriverDetails(Request $r){
        $id = $r->id;
        $driver = DB::table('drivers')
        ->select('drivers.*','truck.truck_number','truck.id as truck_id')
        ->leftJoin('truck','drivers.truck_id','=','truck.id')
        ->where('drivers.id', $id)->first();
        return $driver;
    }

    public function getTruckDetails(Request $r){
        $id = $r->id;
    $truck=DB::table('truck')
    ->select('truck.*', 'drivers.*', 'owners.*','truck.truck_number as t_num','2290_document as document_2290')
    ->leftJoin('owners', 'truck.company_id', '=', 'owners.id')
    ->leftJoin('drivers', 'truck.id', '=', 'drivers.truck_id')
    ->where('truck.id', $id)
    ->first();
    // dd($truck);
        return $truck;
    }

    public function getDispatcherDetails(Request $r){
        $id = $r->id;
        $truck = DB::table('dispatchers')->where('id', $id)->first();
        return $truck;
    }

    public function dispatcher(){
        return view('Dispatcher/view');
    }

    public function truckRoster(){
        return view('Truck/roster');
    }

    public function loadTrucks(Request $request)
{
    $html = '';
    $filter = $request->input('filter');
    if($filter == 'active' || $filter == 'inactive'){

        $query = DB::table('truck')
        ->select('truck_number', 'plate_number', 'status', 'id')
            ->where(function ($query) use ($filter) {
                if ($filter == 'active') {
                    $query->where('status', 'active');
                } elseif ($filter == 'inactive') {
                    $query->where('status', 'inactive');
                }
            })
            ->orderBy('id','ASC')->where('is_deleted',0)->get(); // Paginate the results with 10 rows per page


        foreach ($query as $truck) {
            if ($truck->status == "active") {
                $button = '<td><button class="btn btn-danger btn-change-status" data-status="inactive" data-id="' . $truck->id . '">De-Activate</button></td>';
            } else {
                $button = '<td><button class="btn btn-primary btn-change-status" data-status="active" data-id="' . $truck->id . '">Activate</button></td>';
            }
            $html .= '<tr>';
            $html .= '<td>' . $truck->truck_number . '</td>';
            $html .= '<td>' . $truck->plate_number . '</td>';
            $html .= $button; // Add Activate button
            $html .= '</tr>';
        }

    }else{

        $query=DB::table('truck');

    if ($filter == 'under_dispatch') {
        $query->join('truck_dispatch', 'truck.id', '=', 'truck_dispatch.truck_id')
        ->where('truck_dispatch.is_deleted',0);
    } elseif ($filter == 'not_under_dispatch') {
        $query->leftJoin('truck_dispatch', 'truck.id', '=', 'truck_dispatch.truck_id')
        ->where(function ($query) {
            $query->whereNull('truck_dispatch.truck_id')
                ->orWhere('truck_dispatch.is_deleted', 1);
        });
    }

    $query = $query->select('truck.*')->where('truck.is_deleted',0)->orderBy('truck.id','ASC')->get();
    foreach ($query as $truck) {
        if ($filter == "under_dispatch") {
            $button = '<td><button style="background:transparent;border:none" class=" btn-return" data-id="' . $truck->id . '"><i class="ti ti-arrow-back text-success"></i></button></td>';
        } else {
            $button = '<td><button style="background:transparent;border:none" class=" btn-dispatch" data-id="' . $truck->id . '"><i class="ti ti-send text-primary"></i></button></td>';
        }
        $html .= '<tr>';
        $html .= '<td>' . $truck->truck_number . '</td>';
        $html .= '<td>' . $truck->plate_number . '</td>';
        $html .= $button; // Add Activate button
        $html .= '</tr>';
    }

    }
        return response()->json($html);
}



    public function getDispatchTrucks(Request $request)
{
    $currentDate = new DateTime();
    $lastDay = new DateTime($currentDate->format('Y-m-t'));
    $lastDayOfMonth = $lastDay->format('d');
    $month = $currentDate->format('n');
    $weekNumber = $currentDate->format('W');
    $yearNumber = $currentDate->format('o');
    $firstDay = new DateTime($yearNumber . '-01-01');
    $lastDay = new DateTime($yearNumber . '-12-31');
    $totalWeeks = 52;
    $html = '';
    $filter = $request->input('filter');

    $query=DB::table('truck')
    ->leftJoin('truck_dispatch', 'truck.id', '=', 'truck_dispatch.truck_id');

    $query = $query->select(
        'truck.*',
        DB::raw('(SELECT GROUP_CONCAT(truck_dispatch.description)  FROM truck_dispatch WHERE truck_dispatch.truck_id = truck.id AND truck_dispatch.created_on >= "' . $yearNumber . '-' . $month . '-01" AND truck_dispatch.created_on <= "' . $yearNumber . '-' . $month . '-07") AS week1'),
        DB::raw('(SELECT MAX(truck_dispatch.is_deleted) FROM truck_dispatch WHERE truck_dispatch.truck_id = truck.id AND truck_dispatch.created_on >= "' . $yearNumber . '-' . $month . '-01" AND truck_dispatch.created_on <= "' . $yearNumber . '-' . $month . '-07") AS is_deleted1'),
        DB::raw('(SELECT GROUP_CONCAT(truck_dispatch.description)  FROM truck_dispatch WHERE truck_dispatch.truck_id = truck.id AND truck_dispatch.created_on >= "' . $yearNumber . '-' . $month . '-08" AND truck_dispatch.created_on <= "' . $yearNumber . '-' . $month . '-14") AS week2'),
        DB::raw('(SELECT MAX(truck_dispatch.is_deleted) FROM truck_dispatch WHERE truck_dispatch.truck_id = truck.id AND truck_dispatch.created_on >= "' . $yearNumber . '-' . $month . '-08" AND truck_dispatch.created_on <= "' . $yearNumber . '-' . $month . '-14") AS is_deleted2'),
        DB::raw('(SELECT GROUP_CONCAT(truck_dispatch.description)  FROM truck_dispatch WHERE truck_dispatch.truck_id = truck.id AND truck_dispatch.created_on >= "' . $yearNumber . '-' . $month . '-15" AND truck_dispatch.created_on <= "' . $yearNumber . '-' . $month . '-23") AS week3'),
        DB::raw('(SELECT MAX(truck_dispatch.is_deleted) FROM truck_dispatch WHERE truck_dispatch.truck_id = truck.id AND truck_dispatch.created_on >= "' . $yearNumber . '-' . $month . '-15" AND truck_dispatch.created_on <= "' . $yearNumber . '-' . $month . '-23") AS is_deleted3'),
        DB::raw('(SELECT GROUP_CONCAT(truck_dispatch.description)  FROM truck_dispatch WHERE truck_dispatch.truck_id = truck.id AND truck_dispatch.created_on >= "' . $yearNumber . '-' . $month . '-24" AND truck_dispatch.created_on <= "' . $yearNumber . '-' . $month . '-31") AS week4'),
        DB::raw('(SELECT MAX(truck_dispatch.is_deleted) FROM truck_dispatch WHERE truck_dispatch.truck_id = truck.id AND truck_dispatch.created_on >= "' . $yearNumber . '-' . $month . '-24" AND truck_dispatch.created_on <= "' . $yearNumber . '-' . $month . '-31") AS is_deleted4')
    )
    ->where('truck.is_deleted', 0)
    ->orderBy('truck.id', 'ASC')
    ->groupBy('truck.id','truck.truck_number','truck.vin','truck.company_id','truck.year')
    ->get();

    $html = '';
    foreach ($query as $truck) {
        $html .= '<tr>';
        $html .= '<td>'.$truck->truck_number.'</td>';
        $html .= '</tr>';
    }

    // foreach ($query as $truck) {
    //     $html .= '<tr>';
    //     $html .= '<td>' . $truck->truck_number . '</td>';
    //     $html .= '<td ';
    //     if ($truck->is_deleted1 === 0) {
    //         // dd($truck->is_deleted1);
    //         $html .= 'style="background-color: Green; cursor: pointer; color:balck;"';
    //     } else if($truck->is_deleted1 === null) {
    //         $html .= 'style="background-color: Yellow; cursor: pointer; color:balck;"';
    //     } else if($truck->is_deleted1 === 1) {
    //         $html .= 'style="background-color: Red; cursor: pointer; color:balck;"';
    //     }
    //     $html .= ' onclick="dispatch(' . $truck->id . ')"';
    //     $html .='>' . $truck->week1 . '</td>';
    //     $html .= '<td ';
    //     if ($truck->is_deleted2 === 0) {
    //         // dd($truck->is_deleted1);
    //         $html .= 'style="background-color: Green;cursor: pointer; color:balck;"';
    //     } else if($truck->is_deleted2 === null) {
    //         $html .= 'style="background-color: Yellow;cursor: pointer;color:balck;"';
    //     } else if($truck->is_deleted2 === 1) {
    //         $html .= 'style="background-color: Red;cursor: pointer;color:balck;"';
    //     }
    //     $html .= ' onclick="dispatch(' . $truck->id . ')"';
    //     $html .='>' . $truck->week2 . '</td>';
    //     $html .= '<td ';
    //     if ($truck->is_deleted3 === 0) {
    //         // dd($truck->is_deleted1);
    //         $html .= 'style="background-color: Green;cursor: pointer;color:balck;"';
    //     } else if($truck->is_deleted3 === null) {
    //         $html .= 'style="background-color: Yellow;cursor: pointer;color:balck;"';
    //     } else if($truck->is_deleted3 === 1) {
    //         $html .= 'style="background-color: Red;cursor: pointer;color:balck;"';
    //     }
    //     $html .= ' onclick="dispatch(' . $truck->id . ')"';
    //     $html .= '>' . $truck->week3 . '</td>';
    //     $html .= '<td ';
    //     if ($truck->is_deleted4 === 0) {
    //         // dd($truck->is_deleted1);
    //         $html .= 'style="background-color: Green;cursor: pointer;color:balck;"';
    //     } else if($truck->is_deleted4 === null) {
    //         $html .= 'style="background-color: Yellow;cursor: pointer;color:balck;"';
    //     } else if($truck->is_deleted4 === 1) {
    //         $html .= 'style="background-color: Red;cursor: pointer;color:balck;"';
    //     }
    //     $html .= ' onclick="dispatch(' . $truck->id . ')"';
    //     $html .='>' . $truck->week4 . '</td>';
    //     $html .= '</tr>';
    // }

        return response()->json($html);
}



public function changeStatus(Request $r){
    $id = $r->id;
    $status = $r->status;
    DB::table('truck')->where('id', $id)->update(['status' => $status]);
    return true;
}

public function dispatchTruck(){
    return view('Truck/dispatch');
}

public function getNonDispatchTrucks(Request $r){
    $dis = DB::table('truck_dispatch')->where('dispatcher_id', $r->id)->where('is_deleted', 0)->pluck('truck_id')->toArray();
    $trucks = DB::table('truck as t')->where('t.is_deleted', 0)->whereNotIn('t.id', $dis)->get();
    $avail = DB::table('truck as t')->where('t.is_deleted', 0)->whereIn('t.id', $dis)->get();
    return compact('trucks', 'avail');
}

public function dispatchTrucks(Request $r,$truck_id,$dis_id){
    // $dispatcher_id = $r->dispatcher;
    // $truck_id = $r->trucks;

    DB::table('truck_dispatch')->insert(array(
        'dispatcher_id' => $dis_id,
        'truck_id' => $truck_id,
    ));
    return redirect()->back()->with('success', 'Truck Dispatched');
}

// public function dispatchTrucks(Request $r){
//     $dispatcher_id = $r->dispatcher;
//     $truck_id = $r->trucks;
//     DB::table('truck_dispatch')->insert(array(
//         'dispatcher_id' => $dispatcher_id,
//         'truck_id' => $truck_id,
//     ));
//     return redirect()->back()->with('success', 'Truck Dispatched');
// }

public function deleteDispatch($id){
    DB::table('truck_dispatch')->where('id', $id)->update(['is_deleted' => 1]);
    return redirect()->back();
}

public function deleteDispatcher($id){
    DB::table('dispatchers')->where('id', $id)->update(['is_deleted' => 1]);
    return redirect()->back();
}

public function dispatcherRoster(){
    return view('Dispatcher/roster');
}

public function documents(){
    return view('Documents/document');
}

public function upload(Request $request)
    {
        if ($request->file('documentUpload')->isValid()) {
            $file = $request->file('documentUpload');
            $mimeType = explode('/', $file->getMimeType())[0];
            $fileName = "doc_" . uniqid() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('uploads');

            if (file_exists($file) && is_readable($file) && is_dir($destinationPath) && is_writable($destinationPath)) {
                $file->move($destinationPath, $fileName);
                DB::table('documents')->insert(['file' => $fileName, 'type' => $mimeType,'orignal_name'=>$file->getClientOriginalName()]);
                return response()->json(['message' => 'File uploaded successfully']);
            } else {
                return response()->json(['message' => 'File upload failed'], 400);
            }
        } else {
            return response()->json(['message' => 'Invalid file'], 400);
        }

    }

    public function upcomings(){

        return view('upcoming');
    }

    public function truckAccounting(){
        // $dispatched = DB::table('truck_accounting as td')
        //         ->join('truck as t', 't.id', '=', 'td.truck_id')
        //         ->where('t.is_deleted', 0)->where('td.is_deleted', 0)
        //         ->select('td.truck_id as truck_id','t.truck_number', 't.vin', 't.year', 't.make', 't.model', 't.plate_number', 'td.created_on')->first();
        //         if(isset($dispatched)){
        //             $id=$dispatched->truck_id;
        //         }else{
        //             $id=0;
        //         }

        $truck=DB::table('truck')->where('is_deleted',0)->first();
        return redirect('Truck/Accounting/'.$truck->id);
    }
    public function truckaccounting_(){
        return view('truckAccounting');
    }
    public function truckaccounting_previous(){
        return view('truckAccountingOld');
    }

    public function categories(){
        return view('Category/add');
    }

    public function saveCategory(Request $r){
        $name = $r->name;
        $r->validate(['name' => 'required']);
        DB::table('categories')->insert(['name' => $name]);
        return redirect()->back()->with('success', 'Category Saved Successfully!!!');
    }

    public function deleteCategory($id){
        DB::table('categories')->where('id', $id)->update(['is_deleted' => 1]);
        return redirect()->back();
    }

    public function saveTruckAccountInfo(Request $r){
        $income = $r->data['income'];
        $expense = $r->data['expense'];
        $name = $r->data['name'];
        $truck_id = $r->data['id'];
        $account = DB::table('truck_accounting')->where('truck_id', $truck_id)->where('name', $name)->where('is_deleted',0);
        if($account->count() > 0){
            $aid = $account->first()->id;
        }else{
            $aid = DB::table('truck_accounting')->insertGetId([
                'truck_id' => $truck_id,
                'name' => $name
            ]);
        }
        DB::table('truck_income')->where('accounting_id', $aid)->delete();
        foreach ($income as $i) {
            $s_date = date('Y-m-d', strtotime($i['startdate']));
            $e_date = date('Y-m-d', strtotime($i['enddate']));
            if (isset($i['percent']) && $i['percent'] != "") {
                $percentages = implode(',', @$i['percent']);
            } else {
                $percentages = "";
            }
            $array = array(
                'accounting_id' => $aid,
                'date' => $s_date,
                'end_date' => $e_date,
                // 'category' => $i['category'],
                'description' => $i['description'],
                'amount' => $i['amount'],
                'percent' => @$percentages,
            );
            DB::table('truck_income')->insert($array);
        }

        DB::table('truck_expense')->where('accounting_id', $aid)->delete();
        foreach ($expense as $i) {
            $s_date = date('Y-m-d', strtotime($i['startdate']));
            $e_date = date('Y-m-d', strtotime($i['enddate']));
            $array = array(
                'accounting_id' => $aid,
                'date' => $s_date,
                'end_date' => $e_date,
                'category' => $i['category'],
                'description' => $i['description'],
                'amount' => $i['amount'],
            );
            DB::table('truck_expense')->insert($array);
        }

        return true;
    }

    public function truckAccountPDF($id){
        $pdf = PDF::loadView('pdf');
        return $pdf->download('my-pdf.pdf');
    }

    public function dispatchStatement(){
        return view('dispatchStatement');
    }

    public function getWeeksByTruck(Request $r){
        $id = $r->id;

        $weeks = DB::table('truck_accounting')->where('dispatch_id', $id)->get();
        return $weeks;
    }

    public function profile(){
        return view('profile');
    }

    public function updateProfile(Request $r){
        $r->validate([
            'name'=> 'required',
            'email'=> 'required:unique:users',
        ]);

        if($r->password != ""){
            DB::table('users')->where('id', $r->id)->update([
                'name' => $r->name,
                'email' => $r->email,
                'password' => Hash::make($r->password),
            ]);
        }else{
            DB::table('users')->where('id', $r->id)->update([
                'name' => $r->name,
                'email' => $r->email,
            ]);
        }
        if ($r->hasFile('image')) {
            $fileName = 'Profile-'.time().'.'.$r->file('image')->extension();
        $r->file('image')->move(public_path('uploads'), $fileName);
        DB::table('users')->where('id', $r->id)->update([
            'image' => $fileName,
        ]);
        }
        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function ytd(){
        return view('ytd');
    }

    public function ytd_2(){
        return view('ytd2');
    }

    public function getCompanyTrucks(Request $r){
        $id = $r->id;
        $trucks = DB::table('truck')->where('company_id', $id)->get();
        return $trucks;
    }

    public function createInvoice(){
        return view('Invoices/addInvoice');
    }
public function saveInvoice(Request $request){
    $date = $request->input('date');
    $due_date = $request->input('due_date');
    $invoice_no = $request->input('invoice_no');
    $custom_invoice = $request->input('custom_invoice');
    $customer_id = $request->input('customer_id');
    $bill_of_landing = $request->input('bill_of_landing');
    $cust_name = $request->input('cust_name');
    $cust_address_1 = $request->input('cust_address_1');
    $cust_address_2 = $request->input('cust_address_2');
    $cust_phone_no = $request->input('cust_no');
    $notes = $request->input('notes');
    $sales_tex_rate = $request->input('sales_tex_rate');
    $s_h = $request->input('s_h');
    $discount = $request->input('discount');
    $description = $request->input('description');
    $amount = $request->input('amount');
    $sub_total = $request->input('sub_total_');
    $sales_tex = $request->input('sales_tex_');
    $total_amount = $request->input('total_amount');
    try {
    $invoice_id=DB::table('invoices')->insertGetId([
                    'date' => $date,
                    'due_date' => $due_date,
                    'invoice_no' => $invoice_no,
                    'custom_invoice' => $custom_invoice,
                    'customer_id' => $customer_id,
                    'bill_of_landing' => $bill_of_landing,
                    'cust_name' => $cust_name,
                    'cust_address_1' => $cust_address_1,
                    'cust_address_2' => $cust_address_2,
                    'cust_phone_no' => $cust_phone_no,
                    'notes' => $notes,
                    'sales_tex_rate' => $sales_tex_rate,
                    's_h' => $s_h,
                    'discount' => $discount,
                    'sales_tex' => $sales_tex,
                    'sub_total' => $sub_total,
                    'total_amount' => $total_amount,
                    'status' => 'pending',
                ]);

                foreach ($description as $key => $d) {
                    $a = $amount[$key];
                    DB::table('invoice_description')->insert([
                        'invoice_id'=>$invoice_id,
                        'description'=>$d,
                        'amount'=>$a,
                    ]);
                }
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => 'Failed to save the invoice.']);
    }
    return redirect()->back()->with('success', 'Invoice created successfully.');


}

public function updateInvoice(Request $request,$id){
    $date = $request->input('date');
    $due_date = $request->input('due_date');
    $invoice_no = $request->input('invoice_no');
    $customer_id = $request->input('customer_id');
    $bill_of_landing = $request->input('bill_of_landing');
    $cust_name = $request->input('cust_name');
    $cust_address_1 = $request->input('cust_address_1');
    $cust_address_2 = $request->input('cust_address_2');
    $cust_phone_no = $request->input('cust_no');
    $notes = $request->input('notes');
    $sales_tex_rate = $request->input('sales_tex_rate');
    $s_h = $request->input('s_h');
    $discount = $request->input('discount');
    $description = $request->input('description');
    $amount = $request->input('amount');
    $sub_total = $request->input('sub_total_');
    $sales_tex = $request->input('sales_tex_');
    $total_amount = $request->input('total_amount');
    try {
    DB::table('invoices')->where('id',$id)->update([
                    'date' => $date,
                    'due_date' => $due_date,
                    'invoice_no' => $invoice_no,
                    'customer_id' => $customer_id,
                    'bill_of_landing' => $bill_of_landing,
                    'cust_name' => $cust_name,
                    'cust_address_1' => $cust_address_1,
                    'cust_address_2' => $cust_address_2,
                    'cust_phone_no' => $cust_phone_no,
                    'notes' => $notes,
                    'sales_tex_rate' => $sales_tex_rate,
                    's_h' => $s_h,
                    'discount' => $discount,
                    'sales_tex' => $sales_tex,
                    'sub_total' => $sub_total,
                    'total_amount' => $total_amount,
                    'status' => 'pending',
                ]);
DB::table('invoice_description')->where('invoice_id',$id)->delete();
                foreach ($description as $key => $d) {
                    $a = $amount[$key];
                    DB::table('invoice_description')->insert([
                        'invoice_id'=>$id,
                        'description'=>$d,
                        'amount'=>$a,
                    ]);
                }
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => 'Failed to update the invoice.']);
    }
    return redirect()->back()->with('success', 'Invoice Update successfully.');


}
    // public function saveInvoice(Request $request){
    //     $this->validate($request, [
    //         'date' => 'required|date',
    //         'bol_number' => 'required|string',
    //         'pickup_address' => 'required|string',
    //         'pickup_date' => 'required|date',
    //         'dropoff_address' => 'required|string',
    //         'dropoff_date' => 'required|date',
    //         'truck_number' => 'required|integer',
    //         'amount' => 'required|numeric',
    //         'exp_payment_date' => 'required|date',
    //     ]);

    //     // Extract form data
    //     $date = $request->input('date');
    //     $bolNumber = $request->input('bol_number');
    //     $pickupAddress = $request->input('pickup_address');
    //     $pickupDate = $request->input('pickup_date');
    //     $dropoffAddress = $request->input('dropoff_address');
    //     $dropoffDate = $request->input('dropoff_date');
    //     $truckNumber = $request->input('truck_number');
    //     $amount = $request->input('amount');
    //     $expPaymentDate = $request->input('exp_payment_date');

    //     // Save the invoice to the database (without using a model)
    //     try {
    //         DB::table('invoices')->insert([
    //             'date' => $date,
    //             'bol_number' => $bolNumber,
    //             'pickup_address' => $pickupAddress,
    //             'pickup_date' => $pickupDate,
    //             'dropoff_address' => $dropoffAddress,
    //             'dropoff_date' => $dropoffDate,
    //             'truck_number' => $truckNumber,
    //             'amount' => $amount,
    //             'exp_payment_date' => $expPaymentDate,
    //             'status' => 'pending',
    //         ]);
    //     } catch (\Exception $e) {
    //         // Handle any database-related errors
    //         return redirect()->back()->withErrors(['error' => 'Failed to save the invoice.']);
    //     }

    //     // Redirect back with a success message
    //     return redirect()->back()->with('success', 'Invoice created successfully.');
    // }

    public function pendingInvoice(){
        return view('Invoices/pending');
    }

    public function deleteInvoice($id){
        DB::table('invoices')->where('id', $id)->update(['is_deleted' => 1]);
        return redirect()->back();
    }

    public function payInvoice($id){
        DB::table('invoices')->where('id', $id)->update(['status' => 'paid']);
        return redirect()->back();
    }

    public function editInvoice($id){
        $data = DB::table('invoices')->where('id', $id)->where('status', 'pending')->first();
        if($data){
            return view('Invoices/edit')->with(compact('data'));
        }else{
            return redirect(url('Invoice/Create'))->with('error', 'No record found');
        }
    }

    // public function updateInvoice(Request $request){
    //     $this->validate($request, [
    //      'date' => 'required|date',
    //         'bol_number' => 'required|string',
    //         'pickup_address' => 'required|string',
    //         'pickup_date' => 'required|date',
    //         'dropoff_address' => 'required|string',
    //         'dropoff_date' => 'required|date',
    //         'truck_number' => 'required|integer',
    //         'amount' => 'required|numeric',
    //         'exp_payment_date' => 'required|date',
    //     ]);

    //     // Extract form data
    //    $date = $request->input('date');
    //     $bolNumber = $request->input('bol_number');
    //     $pickupAddress = $request->input('pickup_address');
    //     $pickupDate = $request->input('pickup_date');
    //     $dropoffAddress = $request->input('dropoff_address');
    //     $dropoffDate = $request->input('dropoff_date');
    //     $truckNumber = $request->input('truck_number');
    //     $amount = $request->input('amount');
    //     $expPaymentDate = $request->input('exp_payment_date');
    //     $id = $request->input('id');

    //     // Save the invoice to the database (without using a model)
    //     try {
    //         DB::table('invoices')->where('id', $id)->update([
    //             'date' => $date,
    //             'bol_number' => $bolNumber,
    //             'pickup_address' => $pickupAddress,
    //             'pickup_date' => $pickupDate,
    //             'dropoff_address' => $dropoffAddress,
    //             'dropoff_date' => $dropoffDate,
    //             'truck_number' => $truckNumber,
    //             'amount' => $amount,
    //             'exp_payment_date' => $expPaymentDate,
    //         ]);
    //     } catch (\Exception $e) {
    //         // Handle any database-related errors
    //         return redirect()->back()->withErrors(['error' => 'Failed to save the invoice.']);
    //     }

    //     // Redirect back with a success message
    //     return redirect()->back()->with('success', 'Invoice updated successfully.');
    // }

    public function paidInvoice(){
        return view('Invoices/paid');
    }

    public function onenineReport(){
        return view('1099');
    }

    public function getYear(Request $r){
        $id = $r->id;
        $year = DB::table('truck_dispatch as td')->join('truck as t', 't.id', '=', 'td.truck_id')->where('td.is_deleted', 0)->where('t.is_deleted', 0)->where('t.company_id', $id)->pluck('td.id')->toArray();
        $accounting = DB::table('truck_accounting')->whereIn('dispatch_id', $year)->groupBy('name');
        $years = array();
        if($accounting->count() > 0){
            foreach ($accounting->pluck('name')->toArray() as $a) {
                $years[] = explode('-', $a)[2];
            }
        }
        return $years;
    }

    public function accident(){
        return view('accident/add');
    }

    public function saveReport(Request $request)
    {
        $validatedData = $request->validate([
            'truck_number' => 'required|exists:truck,id',
            'report_files.*' => 'required',
        ]);

        $truckId = $validatedData['truck_number'];
        $reportFiles = $request->file('report_files');
        // dd($reportFile);

        $fileNames = [];

        foreach ($reportFiles as $reportFile) {
            $filename = time() . '_' . $reportFile->getClientOriginalName();
            $reportFile->move(public_path('uploads'), $filename);
            $fileNames[] = $filename;
        }
        // dd(json_encode($fileNames));

        // Generate a unique filename for the uploaded file
        // $filename = time() . '_' . $reportFile->getClientOriginalName();

        // Move the uploaded file to a storage location
        // $reportFile->move(public_path('uploads'), $filename);

        // Save the data to the database using the query builder
        DB::table('accident_report')->insert([
            'truck_id' => $truckId,
            'file' => json_encode($fileNames),
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Report created successfully');
    }

    public function deleteReport($id){
        DB::table('accident_report')->where('id', $id)->update(['is_deleted'=> 1]);
        return redirect()->back()->with('success', 'Record deleted successfully');
    }

    public function users(){
        return view('users');
    }

    public function saveUser(Request $r){
        $r->validate([
            'name'=> 'required|string',
            'email'=> 'required|email|unique:users',
            'password' => 'required'
        ]);

        $data = array(
            'name' => $r->name,
            'email' => $r->email,
            'password' => Hash::make($r->password),
        );
        DB::table('users')->insert($data);
        return redirect()->back()->with('success', 'User added successfully');
    }

    public function cron_truck_dispatch_clear_after_week(){
        $truck_dispatched=DB::table('truck_dispatch')->where('is_deleted',0)->get();
        foreach($truck_dispatched as $td){
            $givenDateTime = new DateTime($td->created_on);
            $givenDateTime->setTime(0, 0, 0);
            $currentDayOfWeek = $givenDateTime->format('w');
            $daysUntilNextSunday = 7 - $currentDayOfWeek;
            $nextSunday = $givenDateTime->add(new DateInterval("P{$daysUntilNextSunday}D"));
            $week_end=$nextSunday->format('Y-m-d');
            $today=date('Y-m-d');
            if($today >= $week_end) {
                echo $td->truck_id;
                DB::table('truck_dispatch')->where('id',$td->id)->update(['is_deleted'=>1]);
                DB::table('truck_accounting')->where('truck_id',$td->truck_id)->update(['is_deleted'=>1]);
                $truck_accounting=DB::table('truck_accounting')->where('truck_id',@$td->truck_id)->first();

                $currentDate = new DateTime();
            $weekNumber = $currentDate->format('W');
            $yearNumber = $currentDate->format('o');
            $firstDay = new DateTime($yearNumber . '-01-01');
            $lastDay = new DateTime($yearNumber . '-12-31');
            $totalWeeks = 52;
            $name = $weekNumber . ' - ' . $totalWeeks . ' - ' . $yearNumber;

             dd($name);
            $truck_expense_db=DB::table('truck_expense')->where('accounting_id',@$truck_accounting->id)->get();
            $total_expenses=0;
            foreach($truck_expense_db as $expense){
                $total_expenses = $total_expenses+$expense->amount;
            }

            $truck_income_db=DB::table('truck_income')->where('accounting_id',@$truck_accounting->id)->get();
            $total_incomes=0;
            foreach($truck_income_db as $income){
                $total_incomes = $total_incomes+$income->amount;
            }
            $net_income=$total_incomes-$total_expenses;
            $truck_accounting_id = DB::table('truck_accounting')->insertGetId([
                'truck_id' => $td->truck_id,
                'name' => $name
            ]);
            if($net_income < 0){
                DB::table('truck_expense')->insert([
                    'accounting_id'=>$truck_accounting_id,
                    'date'=>date('Y-m-d'),
                    'category'=>'12',
                    'description'=>'previous week expense',
                    'amount'=>$net_income,
                ]);
            }else{
                DB::table('truck_income')->insert([
                    'date'=>date('Y-m-d'),
                    // 'category'=>'12',
                    'description'=>'previous week Income',
                    'amount'=>$net_income,
                    'percent'=>'3%',
                    'accounting_id'=>$truck_accounting_id,
                ]);
            }

            }
        }

    }


    public function dispatch_truck(Request $r,$id){
        $description=$r->description;
        $status=$r->dipatch_status;
        $dispatch_date=$r->dispatch_date;
        $truck=$r->truck;
        $dispatch_id=$r->dispatch_id;
        $dispatcher_id=$r->dispatcher_id;


        // dd($dispatch_date);
        if($dispatch_id != 0){
            DB::table('truck_dispatch')->where('id',$dispatch_id)
            ->update([
                'description'=>$description,
                'truck_id'=>$truck,
                'is_deleted'=>$status,

            ]);


        } else {
            DB::table('truck_dispatch')->insert([
                'description'=>$description,
                'truck_id'=>$truck,
                'is_deleted'=>$status,
                'dispatcher_id'=>$dispatcher_id,
                'created_on'=> date("Y-m-d", strtotime($dispatch_date)) . " 23:59:59",
            ]);
        }
        return redirect()->back();
    }


    public function saveTruckExpense(Request $r){
        $expense = $r->data['expense'];
        $name = $r->data['name'];
        $net_total = $r->input('net_total');
        $date = date('Y-m-d');
        // dd($r);

            foreach($expense as $a){
                DB::table('extra_truck_expense')->where('truck_id', $a['truck_id'])->delete();
            }

                foreach ($expense as $i) {
                    $array = array(
                'date' => $i['date'],
                'description' => $i['description'],
                'amount' => $i['amount'],
                'truck_id' => $i['truck_id'],
                'week' => $name,
            );
            DB::table('extra_truck_expense')->insert($array);
        }
        $company_expense = DB::table('company_expense')->whereBetween('date',[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->first();
        // dd($company_expense);
        if($company_expense){
            $company_id = $company_expense->id;
            DB::table('company_expense')
            ->where('id',$company_id)
            ->update([
                'company_id' => 14,
                'amount' => $net_total,
                'expense_name' => 'Dispatcher Statement',
            ]);
        } else {
            DB::table('company_expense')
            ->insert([
                'company_id' => 14,
                'date' => $date,
                'amount' => $net_total,
                'expense_name' => 'Dispatcher Statement',
            ]);
        }



        return true;
    }

    public function deleteDoc($id){
        DB::table('documents')->where('id',$id)->update(['is_deleted' => 1]);
        return redirect()->back();
    }
    public function Dispatch_Statement_PDF_($id){
        $pdf = PDF::loadView('weekpdf', ['dispatcher_id' => $id]);
        return $pdf->download('my-pdf.pdf');
    }
    public function Dispatch_Statement_PDF(){
        $pdf = PDF::loadView('weekpdf2');
        return $pdf->download('my-pdf.pdf');
    }

    public function return_truck($id){
        DB::table('truck_dispatch')->where('truck_id',$id)->where('is_deleted',0)->update([
            'is_deleted'=>1
        ]);
        DB::table('truck_accounting')->where('truck_id',$id)->where('is_deleted',0)->update([
            'is_deleted'=>1
        ]);
        return redirect()->back();
    }

    public function invoice_pdf($id){
        $pdf = PDF::loadView('invoicepdf');
        return $pdf->download('my-pdf.pdf');
    }

    public function view_company(){
        return view('company.view');
    }

    public function save_company(Request $r){
        $name=$r->company_name;
        DB::table('company')->insert([
            'company_name'=>$name,
        ]);
        return redirect()->back();
    }

    public function update_company(Request $r,$id){
        $name=$r->company_name;
        DB::table('company')->where('id',$id)->update([
            'company_name'=>$name,
        ]);
        return redirect()->back();
    }

    public function delete_company($id){
        DB::table('company')->where('id',$id)->update([
            'is_deleted'=>1,
        ]);
        return redirect()->back();
    }

    public function view_company_expense(){
        return view('company.expense');
    }

    public function save_expense(Request $r){
        $name=$r->company_name;
        $expense_name=$r->expense_name;
        $amount=$r->amount;
        $date=$r->date;
        DB::table('company_expense')->insert([
            'company_id'=>$name,
            'expense_name'=>$expense_name,
            'amount'=>$amount,
            'date'=>$date,
        ]);
        return redirect()->back();
    }

    public function update_expense(Request $r,$id){
        $name=$r->company_name;
        $expense_name=$r->expense_name;
        $amount=$r->amount;
        $date=$r->date;
        DB::table('company_expense')->where('id',$id)->update([
            'company_id'=>$name,
            'expense_name'=>$expense_name,
            'amount'=>$amount,
            'date'=>$date,
        ]);
        return redirect()->back();
    }

    public function delete_expense($id){
        DB::table('company_expense')->where('id',$id)->update([
            'is_deleted'=>1,
        ]);
        return redirect()->back();
    }
    public function escrow(){
        return view('escrow.escrow');
    }
    public function escrow_return(){
        return view('escrow.escrow_return');
    }

    public function quite_truck($id){
        DB::table('truck')->where('id',$id)->update(['quite'=>1,'quite_date'=>date('Y-m-d H:i:s')]);
        return redirect()->back();
    }

    public function do_escrow_return($id){
        DB::table('truck')->where('id',$id)->update(['escrow_return'=>1]);
        return redirect()->back();
    }
    public function do_escrow_return_to_escrow($id){
        DB::table('truck')->where('id',$id)->update(['escrow_return'=>0]);
        return redirect()->back();
    }
    public function rehire_driver($id){
        DB::table('drivers')->where('id',$id)->update(['is_deleted'=>0]);
        return redirect()->back();
    }

    public function rehire_truck($id){
        DB::table('truck')->where('id',$id)->update([
            'is_deleted'=>0,
            'quite'=>0,
            'quite_date'=>null,
        ]);
        return redirect()->back();
    }

    public function rehire_owner($id){
        DB::table('owners')->where('id',$id)->update(['is_deleted'=>0]);
        return redirect()->back();
    }

    public function rehire_dispatcher($id){
        DB::table('dispatchers')->where('id',$id)->update(['is_deleted'=>0]);
        return redirect()->back();
    }

    public function rehire_company($id){
        DB::table('company')->where('id',$id)->update(['is_deleted'=>0]);
        return redirect()->back();
    }
    public function make_1099(Request $r){
        $payername = $r->payername;
        $streetaddress = $r->streetaddress;
        $citytown = $r->citytown;
        $stateprorovince = $r->stateprorovince;
        $country = $r->country;
        $zipcode = $r->zipcode;
        $telephoneno = $r->telephoneno;
        $p_tin = $r->p_tin;
        $r_tim = $r->r_tim;
        $r_name = $r->r_name;
        $owners = $r->owners;
        $year = $r->year;

        $total = 0;
        $data = DB::table('truck')
            ->select('id')
            ->where('company_id', $owners)
            ->get();
            // dd($data);
            foreach ($data as $t_id) {
                $accounting = DB::table('truck_accounting')
                ->select('id')
                ->where(DB::raw('SUBSTRING(name, -4)'), $year)
                ->where('truck_id', $t_id->id)
                ->get();
                // dd($accounting);
                foreach ($accounting as $a) {
                $sum = DB::table('truck_expense')
                    ->select('amount')
                    ->where('accounting_id', $a->id)
                    ->where(DB::raw('YEAR(date)'), $year)
                    ->where('category', 13)
                    ->sum('amount');
                $total = $total + $sum;
            }
        }
        return response()->json(['result' => $total]);
    }

    public function archive_truck($id){
        return view('Truck.archive',compact('id'));
    }
    public function archive_dispatcher($id){
        return view('Dispatcher.archive',compact('id'));
    }
    public function archive_owner($id){
        return view('Owners.archive',compact('id'));
    }
    public function archive_driver($id){
        return view('Drivers.archive',compact('id'));
    }
    public function download_files($id){
        // dd($id);
        // Retrieve the file names from the database column for the specific record ID
        $fileNamesJson = DB::table('accident_report')->where('id',$id)->first();
        $names = $fileNamesJson->file;
        // Decode the JSON array
        $fileNames = json_decode($names);

        // Create a unique temporary directory to store the zip file
        $tempDirectory = public_path('temp');
        // Check if the directory already exists
        if (!File::exists($tempDirectory)) {
            // Create the directory if it doesn't exist
            File::makeDirectory($tempDirectory);
        }

        // Create a unique zip file in the temporary directory
        $zipFile = public_path('temp/all_files.zip');
        $zip = new ZipArchive;
        $zip->open($zipFile, ZipArchive::CREATE);

        // Add each file to the zip archive
        foreach ($fileNames as $fileName) {
            $filePath = public_path("uploads/{$fileName}");
            $zip->addFile($filePath, $fileName);
        }

        $zip->close();

        // Download the zip file
        return response()->download($zipFile)->deleteFileAfterSend(true);
    }

    public function getWeekDates($weekNumber, $year)
{
    $firstDayOfYear = Carbon::createFromDate($year, 1, 1);

    $startOfWeek = $firstDayOfYear->startOfWeek(Carbon::SUNDAY);

    $startDate = $startOfWeek->addWeeks($weekNumber - 1);

    $endDate = $startDate->copy()->endOfWeek(Carbon::SATURDAY);

    $formattedStartDate = $startDate->format('F d');
    $formattedEndDate = $endDate->format('F d');

    return $formattedStartDate . ' - ' . $formattedEndDate;
}



    public function sendEmail(Request $request, $id)
    {
        // $dispatcher = DB::table('dispatchers')->where('id',$id)->first();
        // $dispatcher_email = $dispatcher->email;
        $email = $request->input('email');
        $week_no = $request->input('week_no');
        $year_no = $request->input('year_no');

        $weekDates = $this->getWeekDates($week_no, $year_no);

            // Generate the PDF
        $pdf = PDF::loadView('weekpdf', ['dispatcher_id' => $id]);

        // Get the raw PDF content
        $pdfContent = $pdf->output();

        $subject = 'Weekly Settlement ('.$weekDates.')';
        // $senderName = 'Dispatcher';

        $emailContent = <<<EOT
        Hello,<br><br>Please find attached your weekly settlement.<br><br>Thank you
        EOT;

        Mail::html($emailContent, function ($message) use ($email, $pdfContent, $subject) {
            $message->to($email)
                    ->subject($subject)
                    // ->from('sender@example.com', $senderName)
                    ->attachData($pdfContent, 'Dispatch Statement.pdf', ['mime' => 'application/pdf']);
        });

        return redirect()->back()->with('success', 'Email sent successfully!');
    }
    public function sendTruckAccountingEmail(Request $request, $id)
    {
        $email = $request->input('email');
        $week_no = $request->input('week_no');
        $year_no = $request->input('year_no');

        $weekDates = $this->getWeekDates($week_no, $year_no);

        // Generate the PDF
        $pdf = PDF::loadView('pdf');

        // Get the raw PDF content
        $pdfContent = $pdf->output();

        $subject = 'Weekly Settlement ('.$weekDates.')';
        // $senderName = 'Dispatcher';
        $emailContent = <<<EOT
        Hello,<br><br>Please find attached your weekly settlement.<br><br>Thank you
        EOT;

        Mail::html($emailContent, function ($message) use ($email, $pdfContent, $subject) {
            $message->to($email)
                    ->subject($subject)
                    // ->from('sender@example.com', $senderName)
                    ->attachData($pdfContent, 'Truck Accounting.pdf', ['mime' => 'application/pdf']);
        });

        return redirect()->back()->with('success', 'Email sent successfully!');
    }
    public function sendInvoicePDF(Request $request, $id)
    {
        $email = $request->input('email');
        $invoice_no = $request->input('invoice_no');

        // Generate the PDF
        $pdf = PDF::loadView('invoicepdf');

        // Get the raw PDF content
        $pdfContent = $pdf->output();

        $subject = 'Invoice For Booking # '.$invoice_no;
        // $senderName = 'Dispatcher';
        $emailContent = <<<EOT
        Hello,<br><br>
        Please find attached our invoice for booking # $invoice_no.<br>
        Bank information is listed on the invoice.<br><br>
        Thank You<br>
        Ahamad Natsheh<br>
        Accounting<br>
        American Trans LLC<br>
        469-994-7868<br>
        EOT;

        // Use the html method to compose the email body
        Mail::html($emailContent, function ($message) use ($email, $pdfContent, $subject) {
            $message->to($email)
                    ->subject($subject)
                    ->attachData($pdfContent, 'document.pdf', ['mime' => 'application/pdf']);
        });

        return redirect()->back()->with('success', 'Email sent successfully!');
    }

    public function saveNotification(Request $request) {
        $document = $request->input('document');
        $truck = $request->input('truck');
        $date = $request->input('date');
        DB::table('notifications')->insert([
            'document' => $document,
            'truck_number' => $truck,
            'date' => $date,
        ]);
        return redirect()->back()->with('success', 'Notification Added!');
    }
    public function resolveNotification($id) {

        DB::table('notifications')
        ->where('id',$id)
        ->update([
            'is_resolved' => 1
        ]);
        return redirect()->back();
    }
    public function saveNote(Request $request) {
        $note = $request->input('notes');
        $id = $request->input('update_id');
        if ($id != 0) {
            DB::table('notes')
        ->where('id',$id)
        ->update([
            'note' => $note
        ]);
        } else {
            DB::table('notes')
            ->insert([
                'note' => $note,
                'created_at' => now()
            ]);
        }
        return redirect()->back();
    }
    public function deleteNote($id){
        DB::table('notes')->where('id',$id)->update([
            'is_deleted' => 1
        ]);
        return redirect()->back();
    }
    public function truckAccountingDelete($id){
        DB::table('truck_accounting')->where('id',$id)->update([
            'is_deleted' => 1
        ]);
        return redirect()->away('/dispatcher/Truck/Accounting');
    }
    }
