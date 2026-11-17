<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchoolApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schools = School::latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar data sekolah berhasil',
            'data'    => $schools
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|unique:schools,domain',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $school = School::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Sekolah berhasil ditambahkan',
            'data'    => $school
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail data sekolah berhasil',
            'data'    => $school
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, School $school)
    {
        $school = School::findOrFail($school);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|unique:schools,domain,' . $school,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $school->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Sekolah berhasil diperbarui',
            'data'    => $school
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        $school->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sekolah berhasil dihapus'
        ], 200);
    }
}
