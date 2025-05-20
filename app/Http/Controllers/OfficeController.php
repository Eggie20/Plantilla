<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Office;
use App\Models\Personnel;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\ActivityLogger;

class OfficeController extends Controller
{
    /**
     * Helper function to log activities
     * 
     * @param string $logName Category of the log (e.g., 'offices', 'personnel', 'positions')
     * @param string $description Description of the action (e.g., 'Office was created')
     * @param object $subject The model instance that was affected
     * @param array $properties Additional properties to log
     * @return void
     */
    private function logActivity($logName, $description, $subject, $properties = [])
    {
        try {
            ActivityLogger::log($logName, $description, $subject, $properties);
            
            // Log success for debugging
            Log::info("Activity log entry created for {$logName}: {$description}");
        } catch (\Exception $e) {
            // Detailed error logging
            Log::error('Activity log insertion failed: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e; // Re-throw for caller to handle
        }
    }

    /**
     * Display a listing of the offices.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $offices = Office::with(['subOffices', 'parentOffice'])->get();
            return response()->json([
                'success' => true,
                'offices' => $offices->map(function($office) {
                    return [
                        'id' => $office->id,
                        'code' => $office->code,
                        'name' => $office->name,
                        'abbreviation' => $office->abbreviation,
                        'parent_id' => $office->parent_id,
                        'parentOffice' => $office->parentOffice ? [
                            'id' => $office->parentOffice->id,
                            'name' => $office->parentOffice->name
                        ] : null
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch offices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all offices for API requests.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOffices()
    {
        $offices = Office::orderBy('name')->get();
        return response()->json([
            'success' => true,
            'offices' => $offices
        ]);
    }

    /**
     * Store a newly created office via API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function apiStore(Request $request)
    {
        Log::info('Office API store method called with data:', $request->all());
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|between:3,7|unique:offices,code|regex:/^[a-z]+$/i',
                'name' => 'required|string|between:1,50|regex:/^[A-Z\s\.,\/\'\']+$/i',
                'abbreviation' => 'required|string|between:3,7|regex:/^[A-Z]+$/i',
                'parent_id' => 'nullable|exists:offices,id'
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                
                // Get specific error messages for each field
                $errors = $validator->errors();
                $specificErrors = [];
                
                if ($errors->has('code')) {
                    $specificErrors['code'] = [
                        'required' => 'Office code is required',
                        'between' => 'Office code must be between 3 and 7 characters',
                        'unique' => 'This office code is already in use',
                        'string' => 'Office code must be a valid string',
                        'regex' => 'Office code must contain only letters'
                    ];
                }
                
                if ($errors->has('name')) {
                    $specificErrors['name'] = [
                        'required' => 'Office name is required',
                        'between' => 'Office name must be between 1 and 60 characters',
                        'string' => 'Office name must be a valid string',
                        'regex' => 'Office name must contain only uppercase letters, spaces, and these characters: .,/ \''
                    ];
                }
                
                if ($errors->has('abbreviation')) {
                    $specificErrors['abbreviation'] = [
                        'required' => 'Abbreviation is required',
                        'between' => 'Abbreviation must be between 3 and 7 characters',
                        'string' => 'Abbreviation must be a valid string',
                        'regex' => 'Abbreviation must contain only uppercase letters'
                    ];
                }
                
                if ($errors->has('parent_id')) {
                    $specificErrors['parent_id'] = [
                        'exists' => 'Selected parent office does not exist',
                        'nullable' => 'Parent office is optional'
                    ];
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Form validation failed. Please check the highlighted fields.',
                    'errors' => $specificErrors,
                    'validation_rules' => [
                        'code' => '3-7 lowercase letters without spaces',
                        'name' => '1-60 uppercase letters, spaces, and these characters: .,/\'',
                        'abbreviation' => '3-7 uppercase letters without spaces',
                        'parent_id' => 'Optional, must be an existing office'
                    ]
                ], 422);
            }

            // Create the office
            $office = Office::create([
                'code' => $request->code,
                'name' => $request->name,
                'abbreviation' => $request->abbreviation,
                'parent_id' => $request->parent_id ?: null
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Office created successfully',
                'office' => $office
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create office: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created office in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('Office store method called with data:', $request->all());
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Convert parent_id to integer if it exists
            $parent_id = $request->input('parent_id');
            $request->merge(['parent_id' => $parent_id ? (int)$parent_id : null]);
            
            // Validate the request
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|between:3,7|unique:offices,code|regex:/^[a-z]+$/i',
                'name' => 'required|string|between:1,60|regex:/^[A-Z\s\.,\/\']+$/i',
                'abbreviation' => 'required|string|between:3,7|regex:/^[A-Z]+$/i',
                'parent_id' => 'nullable|exists:offices,id|numeric'
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                
                // Get specific error messages for each field
                $errors = $validator->errors();
                $specificErrors = [];
                
                if ($errors->has('code')) {
                    $specificErrors['code'] = [
                        'required' => 'Office code is required',
                        'between' => 'Office code must be between 3 and 7 characters',
                        'unique' => 'This office code is already in use',
                        'string' => 'Office code must be a valid string',
                        'regex' => 'Office code must contain only lowercase letters'
                    ];
                }
                
                if ($errors->has('name')) {
                    $specificErrors['name'] = [
                        'required' => 'Office name is required',
                        'between' => 'Office name must be between 1 and 60 characters',
                        'string' => 'Office name must be a valid string',
                        'regex' => 'Office name must contain only uppercase letters, spaces, and these characters: .,/\''
                    ];
                }
                
                if ($errors->has('abbreviation')) {
                    $specificErrors['abbreviation'] = [
                        'required' => 'Abbreviation is required',
                        'between' => 'Abbreviation must be between 3 and 7 characters',
                        'string' => 'Abbreviation must be a valid string',
                        'regex' => 'Abbreviation must contain only uppercase letters'
                    ];
                }
                
                if ($errors->has('parent_id')) {
                    $specificErrors['parent_id'] = [
                        'exists' => 'Selected parent office does not exist',
                        'nullable' => 'Parent office is optional',
                        'numeric' => 'Parent office ID must be a number'
                    ];
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Form validation failed. Please check the highlighted fields.',
                    'errors' => $specificErrors,
                    'validation_rules' => [
                        'code' => '3-7 lowercase letters without spaces',
                        'name' => '1-60 uppercase letters, spaces, and these characters: .,/',
                        'abbreviation' => '3-7 uppercase letters without spaces',
                        'parent_id' => 'Optional, must be a number and an existing office'
                    ]
                ], 422);
            }

            // Create the office
            $office = Office::create([
                'code' => strtolower($request->code),
                'name' => strtoupper($request->name),
                'abbreviation' => strtoupper($request->abbreviation),
                'parent_id' => $request->parent_id ?: null
            ]);
            
            // Log activity
            $this->logActivity('offices', 'Office was created', $office, [
                'office_name' => $office->name,
                'office_code' => $office->code,
                'office_abbreviation' => $office->abbreviation,
                'parent_id' => $office->parent_id,
                'user_email' => auth()->check() ? auth()->user()->email : null
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Office created successfully',
                'office' => $office
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating office:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create office: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Display the specified office.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $office = Office::with('parentOffice')->findOrFail($id);
            return response()->json([
                'success' => true,
                'office' => $office
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Office not found'
            ], 404);
        }
    }

    public function update(Request $request, Office $office)
    {
        Log::info('Update office request received:', [
            'id' => $office->id,
            'request_data' => $request->all()
        ]);

        $validated = $request->validate([
            'code' => 'nullable|string|between:3,7|unique:offices,code,'.$office->id.'|regex:/^[a-z]+$/i',
            'name' => 'nullable|string|between:1,60|unique:offices,name,'.$office->id.'|regex:/^[A-Z\s\.,\/\']+$/i',
            'abbreviation' => [
                'nullable',
                'string',
                'between:3,7',
                'regex:/^[A-Z]+$/i',
                function($attribute, $value, $fail) use ($office, $request) {
                    // Only validate abbreviation if it's being changed
                    if ($value !== null && $value !== $office->abbreviation) {
                        // Check if abbreviation is already used by another office
                        $exists = Office::where('abbreviation', $value)
                            ->where('id', '!=', $office->id)
                            ->exists();
                        
                        if ($exists) {
                            $fail('This abbreviation is already in use');
                        }
                    }
                }
            ],
            'parent_id' => 'nullable|exists:offices,id'
        ]);

        Log::info('Validated data:', $validated);

        try {
            $office->update([
                'code' => empty($validated['code']) ? null : strtolower($validated['code']),
                'name' => empty($validated['name']) ? null : strtoupper($validated['name']),
                'abbreviation' => empty($validated['abbreviation']) ? null : strtoupper($validated['abbreviation']),
                'parent_id' => $validated['parent_id'] ?? null
            ]);

            Log::info('Office updated successfully:', [
                'id' => $office->id,
                'new_values' => $office->fresh()->toArray()
            ]);

            // Log the office update activity
            $this->logActivity('offices', 'Office was updated', $office, [
                'changes' => $validated,
                'user_email' => auth()->check() ? auth()->user()->email : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Office updated successfully',
                'office' => $office->fresh()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update office:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update office: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an existing office.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Office $office)
    {
        try {
            DB::beginTransaction();

            // Check if office has personnel assigned
            $personnelCount = $office->personnel()->count();
            if ($personnelCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete office. There are ' . $personnelCount . ' personnel assigned to this office.'
                ], 400);
            }

            // Check if office has sub-offices
            if ($office->hasSubOffices()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete office. This office has sub-offices.'
                ], 400);
            }

            // Check if office is referenced in positions table
            $positionCount = Position::where('office_id', $office->id)->count();
            if ($positionCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete office. There are ' . $positionCount . ' positions associated with this office.'
                ], 400);
            }

            // Log the deletion activity before deleting
            $this->logActivity('offices', 'Office was deleted', $office);
            
            // Delete the office
            $office->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Office deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete office', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'office_id' => $office->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete office: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if an office has personnel assigned to it.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function checkPersonnel($id)
    {
        try {
            $office = Office::findOrFail($id);
            $personnelCount = Personnel::where('office', $office->code)->count();
            
            return response()->json([
                'success' => true,
                'hasPersonnel' => $personnelCount > 0,
                'personnelCount' => $personnelCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check personnel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the mayor's office page.
     *
     * @return \Illuminate\Http\Response
     */
    public function mayor()
    {
        return view('Pages.plantilla.mayor');
    }

    /**
     * Check if an office code already exists in the database.
     *
     * @param  string  $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkCodeExists($code)
    {
        $exists = Office::where('code', strtolower($code))->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Check if office name or abbreviation already exists
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkDuplicate(Request $request)
    {
        $name = $request->input('name');
        $abbreviation = $request->input('abbreviation');
        $id = $request->input('id'); // Current office ID if editing

        $duplicateName = false;
        $duplicateAbbreviation = false;

        // Check for duplicate name
        if ($name) {
            $query = Office::where('name', $name);
            if ($id) {
                $query->where('id', '!=', $id);
            }
            $duplicateName = $query->exists();
        }

        // Check for duplicate abbreviation
        if ($abbreviation) {
            $query = Office::where('abbreviation', $abbreviation);
            if ($id) {
                $query->where('id', '!=', $id);
            }
            $duplicateAbbreviation = $query->exists();
        }

        return response()->json([
            'duplicateName' => $duplicateName,
            'duplicateAbbreviation' => $duplicateAbbreviation
        ]);
    }

    // abbreviation
    public function vacant()
    {
        $user = Auth::user();
        
        // Get vacant positions
        $vacantItems = Position::where('status', 'Vacant')
                             ->orWhereNull('status')
                             ->get();
        
        // Get all offices for the dropdown
        $offices = Office::select('id', 'code', 'name', 'abbreviation')
                        ->whereNotNull('code')
                        ->whereNotNull('name')
                        ->whereNotNull('abbreviation')
                        ->get();
        
        return view('Plantilla.Pages.vacant', [
            'user' => $user,
            'vacantItems' => $vacantItems,
            'offices' => $offices
        ]);
    }

    /**
     * Store a newly created plantilla item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeVacant(Request $request)
    {
        // Log the request data for debugging
        Log::info('Position store method called with data:', $request->all());
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'itemNo' => 'required|string|max:50|unique:positions,itemNo',
                'office_id' => 'required|exists:offices,id',
                'position' => 'required|string|max:255',
                'salaryGrade' => 'required|string|max:20',
                'monthlySalary' => 'required|numeric|min:0',
                'step' => 'nullable|integer|min:1|max:8',
                'status' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                // Create the position
                $position = Position::create([
                    'itemNo' => $request->itemNo,
                    'office_id' => $request->office_id,
                    'position' => $request->position,
                    'salaryGrade' => $request->salaryGrade,
                    'monthlySalary' => $request->monthlySalary,
                    'step' => $request->step ?? 1,
                    'type' => 'M',
                    'status' => $request->status ?? 'Vacant'
                ]);

                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Position created successfully',
                    'position' => $position
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Database error while creating position: ' . $e->getMessage(), [
                    'data' => $request->all(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save position. Please try again later.',
                    'error' => $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storeVacant: ' . $e->getMessage(), [
                'data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while saving the position.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing plantilla item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateVacant(Request $request)
    {
        // Log the request data for debugging
        Log::info('Position update method called with data:', $request->all());
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:positions,id',
                'itemNo' => 'required|string|max:50|unique:positions,itemNo,' . $request->id,
                'office_id' => 'required|exists:offices,id',
                'position' => 'required|string|max:255',
                'salaryGrade' => 'required|string|max:20',
                'monthlySalary' => 'required|numeric|min:0',
                'step' => 'nullable|integer|min:1|max:8',
                'status' => 'nullable|string|max:50',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check your inputs.');
            }

            // Find and update the position
            $position = Position::findOrFail($request->id);
            $position->update([
                'itemNo' => $request->itemNo,
                'office_id' => $request->office_id,
                'position' => $request->position,
                'salaryGrade' => $request->salaryGrade,
                'monthlySalary' => $request->monthlySalary,
                'step' => $request->step,
                'status' => $request->status
            ]);
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Position updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating position: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while updating the position: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete an existing plantilla item from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteVacant(Request $request)
    {
        // Log the request data for debugging
        Log::info('Position delete method called with data:', $request->all());
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:positions,id',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->with('error', 'Validation failed. Invalid position ID.');
            }

            // Find the position
            $position = Position::findOrFail($request->id);
            
            // Store item details for the success message
            $itemNo = $position->itemNo;
            
            // Delete the position
            $position->delete();
            
            DB::commit();
            
            return redirect()->back()->with('success', "Position #{$itemNo} deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting position: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the position: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified office.
     *
     * @param  \App\Models\Office  $office
     * @return \Illuminate\Http\Response
     */
    public function edit(Office $office)
    {
        try {
            return response()->json([
                'success' => true,
                'office' => $office
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load office details'
            ], 500);
        }
    }

    /**
     * Get position details for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPosition($id)
    {
        try {
            $position = Position::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'position' => [
                    'id' => $position->id,
                    'itemNo' => $position->itemNo,
                    'office_id' => $position->office_id,
                    'position' => $position->position,
                    'salaryGrade' => $position->salaryGrade,
                    'monthlySalary' => $position->monthlySalary,
                    'step' => $position->step
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load position details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of vacant positions (Vacant or Unfunded status).
     *
     * @return \Illuminate\Http\Response
     */
    public function getVacantPositions()
    {
        try {
            $positions = Position::where('status', 'Vacant')
                              ->orWhere('status', 'Unfunded')
                              ->orWhereNull('status')
                              ->with(['office'])
                              ->get()
                              ->map(function($position) {
                                  return [
                                      'id' => $position->id,
                                      'itemNo' => $position->itemNo,
                                      'position' => $position->position,
                                      'salaryGrade' => $position->salaryGrade,
                                      'monthlySalary' => $position->monthlySalary,
                                      'step' => $position->step,
                                      'status' => $position->status,
                                      'office' => $position->office ? [
                                          'id' => $position->office->id,
                                          'name' => $position->office->name,
                                          'code' => $position->office->code
                                      ] : null
                                  ];
                              });

            return response()->json([
                'success' => true,
                'positions' => $positions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch vacant positions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of all positions.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllPositions()
    {
        try {
            $positions = Position::with('office')->get();

            return response()->json([
                'success' => true,
                'positions' => $positions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load positions: ' . $e->getMessage()
            ], 500);
        }
    }
}
