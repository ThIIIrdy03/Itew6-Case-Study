<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve 'patientID' from the request
        $patientID = $request->input('patientID');

        // Construct the query to fetch medical records with patient names
        $query = MedicalRecord::leftJoin('users as patients', 'medical_records.patientID', '=', 'patients.id')
            ->select('medical_records.*', 'patients.name as PatientName');

        // Filter records by patientID if provided
        if ($patientID) {
            $query->where('medical_records.patientID', $patientID);
        }

        // Execute the query
        $medicalRecords = $query->get();

        // Check if records were found
        if ($medicalRecords->count() > 0) {
            return response()->json([
                'status' => 200,
                'MedicalRecords' => $medicalRecords
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'Message' => 'No Records Found'
            ], 404); 
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validate = Validator::make($request->all(),
            [
                'patientID' => 'required',
                'RecordDetails' => 'required'
            ]);

        // Return validation errors if any
        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        }

        try {
            // Create a new medical record
            MedicalRecord::create([
                'patientID' => $request->patientID,
                'RecordDetails' => $request->RecordDetails
            ]);

            // Return success response
            return response()->json([
                'status' => 200,
                'message' => "Medical Record Created Successfully"
            ], 200);

        } catch (\Exception $e) {
            // Return error response if something went wrong
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find a specific medical record by ID
        $medicalRecord = MedicalRecord::find($id);

        // Check if the record exists
        if ($medicalRecord) {
            return response()->json([
                'status' => 200,
                'medicalRecord' => $medicalRecord
            ], 200);
        } else {
            return response()->json([
                'status' => 404,
                'message' => "No record found."
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate incoming request data
        $validate = Validator::make($request->all(),
            [
                'patientID' => 'required',
                'RecordDetails' => 'required'
            ]);

        // Return validation errors if any
        if ($validate->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validate->messages()
            ], 400);
        }

        try {
            // Find the medical record to update
            $medicalRecord = MedicalRecord::find($id);
            
            // Update the medical record fields
            $medicalRecord->update([
                'patientID' => $request->patientID,
                'RecordDetails' => $request->RecordDetails
            ]);

            // Return success response
            return response()->json([
                'status' => 200,
                'message' => "Medical Record updated Successfully"
            ], 200);

        } catch (\Exception $e) {
            // Return error response if something went wrong
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the medical record to delete
        $medicalRecord = MedicalRecord::find($id);

        // Check if the record exists
        if ($medicalRecord) {
            // Delete the medical record
            $medicalRecord->delete();
            return response()->json([
                'status' => 200,
                'message' => "Medical Record deleted successfully"
            ], 200);
        } else {
            // Return error response if no valid record found to delete
            return response()->json([
                'status' => 404,
                'message' => "No valid medical record to delete."
            ], 404);
        }
    }
}
